<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store->id;
        
        // Get unique customers who have purchased from this store
        $customers = User::whereHas('addresses')
            ->whereHas('orders', function($query) use ($storeId) {
                $query->where('store_id', $storeId)
                      ->where('status_order', '!=', 'cancelled');
            })
            ->with(['addresses', 'orders' => function($query) use ($storeId) {
                $query->where('store_id', $storeId)
                      ->where('status_order', '!=', 'cancelled')
                      ->with('items.product');
            }])
            ->get()
            ->map(function($user) {
                $totalProducts = $user->orders->sum(function($order) {
                    return $order->items->sum('quantity');
                });
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone_number,
                    'address' => $user->addresses->first()?->alamat_lengkap ?? 'No address',
                    'total_orders' => $user->orders->count(),
                    'total_products' => $totalProducts,
                ];
            });

        return view('dashboard.customers', compact('customers'));
    }
}
