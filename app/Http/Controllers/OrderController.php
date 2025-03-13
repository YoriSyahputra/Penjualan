<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
{
    // Ambil store ID dari user yang sedang login
    $storeId = Auth::user()->store->id;
    
    $query = Order::with(['user', 'items', 'items.product'])
        ->where('status', 'paid') // Hanya order dengan status paid
        ->whereHas('items.product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
        ->latest();
    
    // Handle pencarian
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('order_number', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('user', function($query) use ($searchTerm) {
                  $query->where('name', 'like', '%' . $searchTerm . '%');
              });
        });
    }
    
    // Ambil parameter per_page, default 10
    $perPage = $request->input('per_page', 10);
    $orders = $query->paginate($perPage);
    
    return view('dashboard.orders.index', compact('orders'));
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
            'status' => 'required|in:pending,processing,completed,cancelled,paid'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->route('dashboard.orders.show', $order)
            ->with('success', 'Order status updated successfully');
    }
}