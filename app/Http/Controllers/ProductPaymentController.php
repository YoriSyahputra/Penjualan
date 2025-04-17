<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerWallet;
use App\Models\LudwigWallet;
use App\Models\DriverWallet;
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
    public function hasPin()
    {
        return response()->json([
            'hasPin' => auth()->user()->hasPin()
        ]);
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
        // Validasi input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|digits:6'
        ]);

        // Ambil user & product
        $user = auth()->user();
        $product = Product::with('store')->findOrFail($request->product_id);
        $amount = $request->amount;
        
        // Log attempt pembayaran
        Log::info('Proses Pembayaran Produk', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => $amount
        ]);

        // Cek PIN
        $pinRecord = Pin::where('user_id', $user->id)->first();
        
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Harap siapkan PIN Anda terlebih dahulu'
            ], 400);
        }

        // Cek PIN terkunci
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN dikunci. Silakan coba lagi nanti.'
            ], 400);
        }

        // Verifikasi PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            $pinRecord->increment('attempts');

            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes(30)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN dikunci. Coba lagi dalam 30 menit.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'PIN salah. Coba lagi.'
            ], 400);
        }

        // Reset percobaan PIN
        $pinRecord->update(['attempts' => 0]);

        // Cek saldo wallet
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // Proses pembayaran
        try {
            $order = null;
            
            DB::transaction(function () use ($user, $product, $amount, &$order) {
                // Kurangi saldo user
                $user->wallet->decrement('balance', $amount);

                // Buat order
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'payment_method' => 'wallet',
                    'subtotal' => $amount,
                    'total' => $amount,
                    'status' => 'paid',
                    'store_id' => $product->store_id,
                    'paid_at' => now()
                ]);

                // Buat order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $amount
                ]);

                // Simpan ke Ludwig Wallet
                LudwigWallet::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'seller_id' => $product->store->user_id,
                    'amount' => $amount,
                    'status_package' => 'pending',
                    'status_payment' => 'pending'
                ]);

                // Kurangi stok produk
                $product->decrement('stock', 1);

                // Log transaksi
                Log::info('Transaksi Berhasil', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'redirect' => route('order.receipt', ['order' => $order->id])
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Proses Pembayaran', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahan method untuk update status paket oleh driver
    public function updatePackageStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pickup,on_delivery,delivered,failed_delivery,returned'
        ]);

        $driver = auth()->user();
        $ludwigWallet = LudwigWallet::where('order_id', $request->order_id)->first();

        if (!$ludwigWallet) {
            return response()->json([
                'success' => false,
                'message' => 'Data transaksi tidak ditemukan'
            ], 404);
        }

        // Update status paket dan assign driver
        $ludwigWallet->update([
            'status_package' => $request->status,
            'driver_id' => $driver->id,
            'delivery_notes' => $request->notes ?? null
        ]);

        // Logika release dana ketika paket sudah delivered
        if ($request->status === 'delivered') {
            $this->releaseFunds($ludwigWallet);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status paket diperbarui'
        ]);
    }


    // Method internal buat release dana ke seller
    private function releaseFunds(LudwigWallet $ludwigWallet)
    {
        DB::transaction(function () use ($ludwigWallet) {
            // 1. Release subtotal ke seller wallet
            if ($ludwigWallet->seller_id) {
                $sellerWallet = SellerWallet::where('user_id', $ludwigWallet->seller_id)->first();
                
                if ($sellerWallet) {
                    // Transfer subtotal ke seller wallet
                    $sellerWallet->increment('balance', $ludwigWallet->subtotal);
                    
                    Log::info('Dana subtotal dirilis ke Seller', [
                        'order_id' => $ludwigWallet->order_id,
                        'seller_id' => $ludwigWallet->seller_id,
                        'amount' => $ludwigWallet->subtotal
                    ]);
                    Log::info('Debug driver wallet creation', [
                        'driver_id' => $ludwigWallet->driver_id,
                        'shipping_fee' => $ludwigWallet->shipping_fee,
                        'condition_met' => ($ludwigWallet->driver_id && $ludwigWallet->shipping_fee > 0)
                    ]);
                    
                }
            }
            
            // 2. Release shipping fee ke driver wallet
            
            if ($ludwigWallet->driver_id && $ludwigWallet->shipping_fee > 0) {
                // Cek apakah driver sudah punya wallet, jika belum buat baru
                $driverWallet = DriverWallet::firstOrCreate(
                    ['driver_id' => $ludwigWallet->driver_id],
                    [
                        'user_id' => $ludwigWallet->driver_id,
                        'balance' => 0
                    ]
                );
                
                // Transfer shipping fee ke driver wallet
                $driverWallet->increment('balance', $ludwigWallet->shipping_fee);
                
                Log::info('Shipping fee dirilis ke Driver', [
                    'order_id' => $ludwigWallet->order_id,
                    'driver_id' => $ludwigWallet->driver_id,
                    'amount' => $ludwigWallet->shipping_fee
                ]);
            }

            // 3. Update status di ludwig wallet
            $ludwigWallet->update([
                'status_payment' => 'released',
                'released_at' => now()
            ]);
        });
    }


    public function processOrderPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pin' => 'required|digits:6'
        ]);

        $user = auth()->user();
        
        // Ambil order utama
        $order = Order::with([
            'items', 
            'items.product', 
            'items.product.store'
        ])->findOrFail($request->order_id);

        // Cari semua order terkait dengan payment code yang sama
        $relatedOrders = collect([$order]);
        if ($order->payment_code) {
            $additionalOrders = Order::where('payment_code', $order->payment_code)
                ->where('id', '!=', $order->id)
                ->where('status', 'pending')
                ->with(['items', 'items.product', 'items.product.store'])
                ->get();
            
            $relatedOrders = $relatedOrders->merge($additionalOrders);
        }

        // Hitung total amount semua order
        $totalAmount = $relatedOrders->sum('total');
        
        Log::info('Proses pembayaran multiple order', [
            'order_utama_id' => $order->id,
            'payment_code' => $order->payment_code,
            'jumlah_order' => $relatedOrders->count(),
            'total_amount' => $totalAmount
        ]);

        // Validasi kepemilikan dan status order
        foreach ($relatedOrders as $relOrder) {
            if ($relOrder->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak sah ke salah satu order'
                ], 403);
            }

            if ($relOrder->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Salah satu order tidak bisa dibayar'
                ], 400);
            }
        }

        // Verifikasi PIN
        $pinRecord = Pin::where('user_id', $user->id)->first();
        if (!$pinRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Harap siapkan PIN terlebih dahulu'
            ], 400);
        }
        
        // Cek PIN terkunci
        if ($pinRecord->is_locked && now()->lessThan($pinRecord->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN dikunci. Silakan coba lagi nanti.'
            ], 400);
        }
        
        // Verifikasi PIN
        if (!Hash::check($request->pin, $pinRecord->pin)) {
            $pinRecord->increment('attempts');

            if ($pinRecord->attempts >= 3) {
                $pinRecord->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes(30)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN dikunci. Coba lagi dalam 30 menit.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'PIN salah. Coba lagi.'
            ], 400);
        }
        
        // Reset percobaan PIN
        $pinRecord->update(['attempts' => 0]);

        // Cek saldo wallet
        if ($user->wallet->balance < $totalAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // Proses pembayaran
        try {
            DB::beginTransaction();

            // Kurangi saldo user
            $user->wallet()->decrement('balance', $totalAmount);
            
            Log::info('Saldo dikurangi dari wallet user', [
                'user_id' => $user->id, 
                'amount' => $totalAmount
            ]);

            // Proses setiap order
            foreach ($relatedOrders as $relOrder) {
                // Kelompokkan item berdasarkan store
                $itemsByStore = $relOrder->items->groupBy(function($item) {
                    return $item->product->store_id;
                });
                
                // Proses pembayaran untuk setiap store
                foreach ($itemsByStore as $storeId => $items) {
                    // Ambil store berdasarkan ID
                    $store = \App\Models\Store::find($storeId);
                    
                    if (!$store) {
                        Log::warning('Store tidak ditemukan', ['store_id' => $storeId]);
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Store tidak tersedia'
                        ], 400);
                    }
                    
                    // Hitung subtotal untuk store ini
                    $storeSubtotal = $items->sum(function($item) {
                        return $item->price * $item->quantity;
                    });
                    
                    $storeShipping = $relOrder->shipping_fee ?? 0;
                    
                    // Distribusi ongkos kirim proporsional jika ada multiple store
                    if (count($itemsByStore) > 1 && $relOrder->subtotal > 0) {
                        $storeShipping = ($relOrder->shipping_fee * ($storeSubtotal / $relOrder->subtotal));
                    }

                    $totalAmount = round($storeSubtotal + $storeShipping, 2);
                    
                    // Buat Ludwig Wallet entry untuk dana yang ditahan
                    LudwigWallet::create([
                        'order_id' => $relOrder->id,
                        'user_id' => $user->id,
                        'seller_id' => $store->user_id,
                        'driver_id' => $relOrder->driver_id,
                        'amount' => $totalAmount,
                        'subtotal' => $storeSubtotal,
                        'shipping_fee' => $storeShipping,
                        'status_package' => 'pending',
                        'status_payment' => 'pending'
                    ]);
                    
                    Log::info('Dana ditahan di Ludwig Wallet', [
                        'order_id' => $relOrder->id,
                        'store_id' => $storeId,
                        'subtotal' => $storeSubtotal,
                        'shipping_fee' => $storeShipping,
                        'total_amount' => $totalAmount
                    ]);
                }

                // Update status order
                $relOrder->update([
                    'status' => 'paid',
                    'payment_method' => 'LudwigPayment',
                    'paid_at' => now()
                ]);
                
                Log::info('Order dikonfirmasi pembayaran', ['order_id' => $relOrder->id]);
            }

            DB::commit();

            // Tambahkan session flag untuk memunculkan notifikasi wallet
            session(['show_wallet_notification' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'redirect' => route('order.receipt', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pembayaran gagal: '.$e->getMessage(), [
                'order_utama_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal: ' . $e->getMessage()
            ], 500);
        }
    }

}