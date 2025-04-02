<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $storeId = Auth::user()->store->id;
        
        \Log::info('Debug Params', [
            'Store ID' => $storeId,
            'Search' => $request->search,
            'Per Page' => $request->per_page,
            'Date From' => $request->date_from,
            'Date To' => $request->date_to
        ]);
        
        $query = Order::with(['user', 'items', 'items.product'])
            ->whereHas('items.product', function($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->latest();
        
        $query = $this->applyFilters($query, $request);
        
        $perPage = $request->input('per_page', 10);
        
        \Log::info('Final SQL: ' . $query->toSql());
        \Log::info('SQL Bindings: ' . json_encode($query->getBindings()));
        
        $orders = $query->paginate($perPage);
        
        \Log::info('Total Orders Found: ' . $orders->total());
        
        if ($request->ajax()) {
            return view('dashboard.orders.table', compact('orders'))->render();
        }
        
        return view('dashboard.orders.index', compact('orders'));
    }

    protected function applyFilters($query, $request)
    {
        // Kalo ada search, langsung filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', "%{$searchTerm}%")
                               ->orWhere('email', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Kalo ada filter tanggal, baru filter tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($request->date_from)->startOfDay(),
                \Carbon\Carbon::parse($request->date_to)->endOfDay()
            ]);
        }
        
        return $query;
    }

    public function show(Order $order)
    {
        // Get the current authenticated seller's store ID
        $storeId = Auth::user()->store->id;
        
        // Check if the order contains products from the seller's store
        if (!$order->items()->whereHas('product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->exists()) {
            abort(403, 'You are not authorized to view this order');
        }
        
        $order->load(['user', 'items' => function($query) use ($storeId) {
            $query->whereHas('product', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }, 'items.product', 'address']);
        
        $storePayments = $order->calculateStorePayments()->filter(function($payment, $id) use ($storeId) {
            return $id == $storeId;
        });
        
        return view('dashboard.orders.show', compact('order', 'storePayments'));
    }
       
    public function updateStatus(Request $request, Order $order)
    {
        // Get the current authenticated seller's store ID
        $storeId = Auth::user()->store->id;
        
        // Check if the order contains products from the seller's store
        if (!$order->items()->whereHas('product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->exists()) {
            abort(403, 'You are not authorized to update this order');
        }
        
        $request->validate([
            'status_order' => 'required|in:pending,processing,cancelled'
        ]);

        // Store the old status to check changes
        $oldStatus = $order->status_order;
        
        // Update the order status
        $order->update([
            'status_order' => $request->status_order
        ]);

        // Regenerate or clear tracking number based on status
        if ($request->status_order === 'processing') {
            $order->generateTrackingNumber();
        } elseif (in_array($request->status_order, ['pending', 'cancelled'])) {
            $order->clearTrackingNumber();
        }

        return redirect()->route('dashboard.orders.show', $order)
            ->with('success', 'Order status updated successfully');
    }
    public function generateMultipleTrackingNumbers(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        
        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);
            if ($order && $order->status_order === 'processing') {
                $order->generateTrackingNumber();
            }
        }

        return redirect()->back()->with('success', 'Resi berhasil di-generate! 🎉');
    }

    public function generateBulkResiSticker(Request $request)
{
    $orderIds = explode(',', $request->query('ids'));
    $storeId = Auth::user()->store->id;
    
    // Ambil order yang dimiliki oleh toko ini dan memiliki nomor resi
    $orders = Order::whereIn('id', $orderIds)
        ->whereNotNull('nomor_resi')
        ->whereHas('items.product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
        ->get();
        
    return view('dashboard.orders.resi-sticker', ['orders' => $orders]);
}
        public function generateResiSticker(Order $order)
        {
            return view('dashboard.orders.resi-sticker', ['orders' => [$order]]);
        }
        

}