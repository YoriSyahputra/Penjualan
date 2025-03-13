<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index(Request $request)
{
    $query = Order::with(['user', 'items', 'items.product'])
        ->where('status', 'paid') // Filter to only show paid orders
        ->latest();
    
    // Handle search if provided
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('order_number', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('user', function($query) use ($searchTerm) {
                  $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%');
              });
        });
    }
    
    // Get per page value from request or default to 10
    $perPage = $request->input('per_page', 10);
    $orders = $query->paginate($perPage);
    
    if ($request->ajax()) {
        return view('dashboard.orders.table', compact('orders'))->render();
    }
    
    return view('dashboard.orders.index', compact('orders'));
}

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items', 'items.product', 'address']);
        $storePayments = $order->calculateStorePayments();
        
        return view('dashboard.orders.show', compact('order', 'storePayments'));
    }

    /**
     * Update the order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Order $order)
    {
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