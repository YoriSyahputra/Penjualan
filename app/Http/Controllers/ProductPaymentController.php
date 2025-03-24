<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerWallet;

use App\Models\Pin;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
class ProductPaymentController extends Controller
{
    public function showSearch()
    {
        return view('payment.transfer_search_product');
    }

    public function getOrderByPaymentCode($code)
    {
        // Validate payment code format
        if (!preg_match('/^LWP-[A-Z0-9]{10,15}$/', $code)) {
            return response()->json([
                'success' => false,
                'message' => 'Format kode pembayaran tidak valid'
            ], 400);
        }

        // Find all orders by payment code
        $orders = Order::where('payment_code', $code)
            ->where('status', 'pending')
            ->with(['items.product'])
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode pembayaran tidak ditemukan atau pesanan sudah dibayar'
            ], 404);
        }

        // Calculate total amount for all orders
        $totalAmount = $orders->sum('total');
        
        // Get first order to display (we'll process all orders during payment)
        $firstOrder = $orders->first();
        
        // Collect all items from all orders
        $allItems = collect();
        foreach ($orders as $order) {
            $allItems = $allItems->merge($order->items->map(function($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ];
            }));
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $firstOrder->id,
                'order_number' => $firstOrder->order_number,
                'total' => $totalAmount, // Total of all related orders
                'status' => $firstOrder->status,
                'payment_code' => $code,
                'related_order_count' => $orders->count(),
                'items' => $allItems
            ],
            'related_order_ids' => $orders->pluck('id')->toArray()
        ]);
    }
    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select([
                'id', 
                'name', 
                'price', 
                'description', 
                'stock',
                'thumbnail'
            ])
            ->limit(5)
            ->get();

        return response()->json($products);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|digits:6'
        ]);

        $user = auth()->user();
        $product = Product::with('store')->findOrFail($request->product_id);
        $amount = $request->amount;
        
        // Log payment attempt
        Log::info('Mencoba proses pembayaran produk', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => $amount
        ]);
        
        // Get the seller's wallet
        $sellerWallet = SellerWallet::where('store_id', $product->store_id)->first();
        
        // Log seller wallet status
        Log::info('Status seller wallet', [
            'store_id' => $product->store_id,
            'wallet_exists' => $sellerWallet ? 'Ya' : 'Tidak',
            'wallet_id' => $sellerWallet ? $sellerWallet->id : null,
            'current_balance' => $sellerWallet ? $sellerWallet->balance : null
        ]);
        
        if (!$sellerWallet && $product->store) {
            // Buat wallet baru jika tidak ada
            Log::info('Membuat wallet baru untuk seller', [
                'store_id' => $product->store_id,
                'store_user_id' => $product->store->user_id
            ]);
            
            $sellerWallet = SellerWallet::create([
                'store_id' => $product->store_id,
                'user_id' => $product->store->user_id,
                'balance' => 0
            ]);
        }
        
        if (!$sellerWallet) {
            return response()->json([
                'success' => false,
                'message' => 'Seller wallet tidak ditemukan'
            ], 400);
        }

        // Verify PIN
        $pinRecord = Pin::where('user_id', $user->id)->first();
        
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Harap siapkan PIN Anda terlebih dahulu'
            ], 400);
        }

        // Check if PIN is locked
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN telah dikunci karena terlalu banyak percobaan. Silakan coba lagi nanti.'
            ], 400);
        }

        // Verify PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            $pinRecord->increment('attempts');

            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes(30)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN telah dikunci karena terlalu banyak percobaan. Silakan coba lagi dalam 30 menit.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'PIN tidak valid. Silakan coba lagi.'
            ], 400);
        }

        // Reset PIN attempts on successful verification
        $pinRecord->update(['attempts' => 0]);

        // Check wallet balance
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // Process payment and create order
        try {
            $order = null; // Declare outside the transaction to access it later
            
            DB::transaction(function () use ($user, $product, $amount, &$order, $sellerWallet) {
                // Log wallet balances before transaction
                Log::info('Saldo sebelum transaksi', [
                    'user_wallet' => $user->wallet->balance,
                    'seller_wallet' => $sellerWallet->balance
                ]);
                
                // Deduct from user's wallet
                $user->wallet->decrement('balance', $amount);
                
                // PENTING: Refresh seller wallet sebelum increment
                $sellerWallet = $sellerWallet->fresh();
                
                // Add to seller's wallet with explicit logging
                $oldBalance = $sellerWallet->balance;
                $sellerWallet->increment('balance', $amount);
                $newBalance = $sellerWallet->fresh()->balance;
                
                Log::info('Penambahan saldo seller wallet', [
                    'wallet_id' => $sellerWallet->id,
                    'store_id' => $product->store_id,
                    'old_balance' => $oldBalance,
                    'added_amount' => $amount,
                    'new_balance' => $newBalance,
                    'difference' => $newBalance - $oldBalance
                ]);

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'payment_method' => 'wallet',
                    'subtotal' => $amount,
                    'total' => $amount,
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $amount
                ]);

                // Update product stock
                $product->decrement('stock', 1);
                
                // Log wallet balances after transaction
                Log::info('Saldo setelah transaksi', [
                    'user_wallet' => $user->wallet->fresh()->balance,
                    'seller_wallet' => $sellerWallet->fresh()->balance
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'redirect' => route('order.receipt', ['order' => $order->id])
            ]);
        } catch (\Exception $e) {
            Log::error('Proses pembayaran gagal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Proses pembayaran gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processOrderPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pin' => 'required|digits:6'
        ]);

        $user = auth()->user();
        
        // Get the initial order
        $order = Order::with([
            'items', 
            'items.product', 
            'items.product.store'
        ])->findOrFail($request->order_id);

        // Find all related orders with the same payment code
        $relatedOrders = collect([$order]);
        if ($order->payment_code) {
            $additionalOrders = Order::where('payment_code', $order->payment_code)
                ->where('id', '!=', $order->id)
                ->where('status', 'pending')
                ->with(['items', 'items.product', 'items.product.store'])
                ->get();
            
            $relatedOrders = $relatedOrders->merge($additionalOrders);
        }

        // Calculate the total amount across all orders
        $totalAmount = $relatedOrders->sum('total');
        
        Log::info('Processing payment for multiple orders', [
            'primary_order_id' => $order->id,
            'payment_code' => $order->payment_code,
            'order_count' => $relatedOrders->count(),
            'total_amount' => $totalAmount
        ]);

        // Validate ownership and status for all orders
        foreach ($relatedOrders as $relOrder) {
            if ($relOrder->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to one or more orders'
                ], 403);
            }

            if ($relOrder->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more orders are not eligible for payment'
                ], 400);
            }
        }

        // PIN verification
        $pinRecord = Pin::where('user_id', $user->id)->first();
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Please set up your PIN first'
            ], 400);
        }
        
        // Check if PIN is locked
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN has been locked due to too many attempts. Please try again later.'
            ], 400);
        }
        
        // Verify PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            $pinRecord->increment('attempts');

            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes(30)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN has been locked due to too many attempts. Please try again in 30 minutes.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN. Please try again.'
            ], 400);
        }
        
        // Reset PIN attempts on successful verification
        $pinRecord->update(['attempts' => 0]);

        // Check user balance against total amount
        if ($user->wallet->balance < $totalAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // Verify all orders have items before proceeding
        foreach ($relatedOrders as $relOrder) {
            if ($relOrder->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items found in one or more orders'
                ], 400);
            }
            
            // Verify each item's product and store
            foreach ($relOrder->items as $item) {
                if (!$item->product) {
                    Log::warning('Product not found for item', ['item_id' => $item->id, 'product_id' => $item->product_id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more products in your orders are no longer available'
                    ], 400);
                }
                
                if (!$item->product->store_id) {
                    Log::warning('Product has no store', ['product_id' => $item->product_id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more products in your orders have invalid store information'
                    ], 400);
                }
            }
        }

        // Process the payment for all orders
        try {
            DB::beginTransaction();

            // Deduct total amount from buyer's wallet
            $user->wallet()->decrement('balance', $totalAmount);
            
            Log::info('Deducted from user wallet', [
                'user_id' => $user->id, 
                'amount' => $totalAmount
            ]);

            // Process each order
            foreach ($relatedOrders as $relOrder) {
                // Group items by store for this order
                $itemsByStore = $relOrder->items->groupBy(function($item) {
                    return $item->product->store_id;
                });
                
                // Process seller payments for each store
                foreach ($itemsByStore as $storeId => $items) {
                    // Get store object by ID
                    $store = \App\Models\Store::find($storeId);
                    
                    if (!$store) {
                        Log::warning('Store not found', ['store_id' => $storeId]);
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'One or more stores in your orders are not available'
                        ], 400);
                    }
                    
                    // Calculate store's share
                    $storeSubtotal = $items->sum(function($item) {
                        return $item->price * $item->quantity;
                    });
                    
                    $storeShipping = $relOrder->shipping_fee ?? 0;
                    
                    // If there are multiple stores, distribute shipping fee proportionally
                    if (count($itemsByStore) > 1 && $relOrder->subtotal > 0) {
                        $storeShipping = ($relOrder->shipping_fee * ($storeSubtotal / $relOrder->subtotal));
                    }

                    $totalToAdd = round($storeSubtotal + $storeShipping, 2);
                    
                    // Get or create seller wallet
                    $sellerWallet = SellerWallet::firstOrCreate(
                        ['store_id' => $storeId],
                        ['user_id' => $store->user_id, 'balance' => 0]
                    );
                    
                    // Update seller wallet balance
                    $sellerWallet->increment('balance', $totalToAdd);
                    
                    Log::info('Processed payment for store', [
                        'order_id' => $relOrder->id,
                        'store_id' => $storeId,
                        'amount' => $totalToAdd
                    ]);
                }

                // Update order status
                $relOrder->update([
                    'status' => 'paid',
                    'payment_method' => 'LudwigPayment',
                    'paid_at' => now()
                ]);
                
                Log::info('Order marked as paid', ['order_id' => $relOrder->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'redirect' => route('order.receipt', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: '.$e->getMessage(), [
                'primary_order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}