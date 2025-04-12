<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\LudwigWallet;
use App\Models\Wallet;
use App\Models\OrderCancellation;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles; 

class OrderCancellationController extends Controller
{
    /**
     * Cancel an order and process refunds
     * 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder(Request $request, $orderId)
{
    // Perubahan 1: Gunakan $orderId daripada Route Model Binding untuk menghindari issue
    try {
        $order = Order::findOrFail($orderId);
    } catch (\Exception $e) {
        Log::error('Order tidak ditemukan', ['order_id' => $orderId]);
        return response()->json([
            'success' => false,
            'message' => 'Order tidak ditemukan',
            'error' => $e->getMessage()
        ], 404);
    }
    
    // Dalam konteksmu, auth()->user() adalah seller (admin)
    $currentUserId = auth()->id();
    
    // Debugging info awal
    Log::info('Mencoba membatalkan order', [
        'order_id' => $order->id,
        'seller_id' => $currentUserId,
        'order_properties' => [
            'status' => $order->status,
            'status_order' => $order->status_order,
            'store_id' => $order->store_id
        ]
    ]);
    
    // Validasi status order (harus paid dan pending)
    if ($order->status !== 'paid' || $order->status_order !== 'pending') {
        Log::warning('Order status invalid untuk pembatalan', [
            'order_id' => $order->id,
            'current_status' => $order->status,
            'current_status_order' => $order->status_order
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Order tidak dapat dibatalkan - status tidak sesuai',
            'details' => [
                'required_status' => 'paid',
                'required_status_order' => 'pending',
                'current_status' => $order->status,
                'current_status_order' => $order->status_order
            ]
        ], 400);
    }
    
    // Perubahan 2: Tambahkan debugging untuk `store_id`
    if (!isset($order->store_id)) {
        Log::error('Property store_id tidak ada pada model Order', [
            'order_id' => $order->id,
            'order_attributes' => array_keys($order->getAttributes())
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Struktur data order tidak valid - store_id tidak ditemukan',
            'order_attributes' => array_keys($order->getAttributes())
        ], 500);
    }
    
    // Pastikan seller adalah pemilik produk/store dari order ini
    $isOwner = $order->store->user_id == $currentUserId;

    
    Log::info('Seller mencoba membatalkan order', [
        'order_id' => $order->id,
        'seller_id' => $currentUserId,
        'is_owner' => $isOwner,
        'store_id' => $order->store_id
    ]);
    
    if (!$isOwner) {
        Log::warning('Unauthorized cancel attempt', [
            'order_id' => $order->id,
            'seller_id' => $currentUserId,
            'store_id' => $order->store_id,
            'role' => auth()->user()->getRoleNames() ?? 'undefined' // Jika menggunakan package role permission
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak memiliki akses untuk membatalkan order ini',
            'details' => [
                'required_store_id' => $order->store_id,
                'your_user_id' => $currentUserId,
                'is_match' => $isOwner
            ]
        ], 403);
    }
    
    // Validasi reason
    if (!$request->has('reason') || strlen(trim($request->reason)) < 5) {
        return response()->json([
            'success' => false,
            'message' => 'Alasan pembatalan harus diisi minimal 5 karakter',
            'received' => $request->reason ?? 'empty'
        ], 422);
    }
    
    try {
        DB::beginTransaction();
        
        // Debug pre-transaction
        Log::info('Memulai transaksi pembatalan order', [
            'order_id' => $order->id,
            'seller_id' => $currentUserId,
        ]);
        
        // 1. Update status order
        $order->update([
            'status_order' => 'cancelled',
            'cancelled_at' => now()
        ]);
        
        Log::info('Order status updated', [
            'order_id' => $order->id,
            'new_status_order' => 'cancelled'
        ]);
        
        // 2. Proses semua LudwigWallet entries terkait order ini
        $ludwigWallets = LudwigWallet::where('order_id', $order->id)
            ->where('status_payment', 'pending')
            ->get();
        
        Log::info('Ludwig wallets retrieved', [
            'order_id' => $order->id,
            'count' => $ludwigWallets->count(),
            'wallet_ids' => $ludwigWallets->pluck('id')->toArray()
        ]);
        
        if ($ludwigWallets->isEmpty()) {
            Log::warning('Tidak ada ludwig wallet untuk diproses', [
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data wallet yang perlu diproses untuk pembatalan',
                'order_id' => $order->id
            ], 400);
        }
        
        $totalRefunded = 0;
        $refundDetails = [];
        
        foreach ($ludwigWallets as $ludwigWallet) {
            try {
                $totalRefunded += $ludwigWallet->amount;
                
                // Update status di ludwig wallet
                $ludwigWallet->update([
                    'status_payment' => 'cancelled',
                    'status_package' => 'returned',
                    'cancellation_reason' => $request->reason,
                    'cancelled_at' => now(),
                    'cancellation_by' => $currentUserId
                ]);

                
                Log::info('Ludwig wallet updated', [
                    'wallet_id' => $ludwigWallet->id,
                    'order_id' => $ludwigWallet->order_id
                ]);
                
                // Process refund ke wallet customer
                $customerWallet = Wallet::firstOrCreate(
                    ['user_id' => $ludwigWallet->user_id],
                    ['balance' => 0]
                );
                
                // Tambahkan amount ke balance customer
                $oldBalance = $customerWallet->balance;
                $customerWallet->increment('balance', $ludwigWallet->amount);
                
                Log::info('Dana dikembalikan ke wallet customer', [
                    'wallet_id' => $ludwigWallet->id,
                    'order_id' => $ludwigWallet->order_id,
                    'customer_id' => $ludwigWallet->user_id,
                    'amount' => $ludwigWallet->amount,
                    'old_balance' => $oldBalance,
                    'new_balance' => $customerWallet->balance
                ]);
                
                $refundDetails[] = [
                    'wallet_id' => $ludwigWallet->id,
                    'customer_id' => $ludwigWallet->user_id,
                    'amount' => $ludwigWallet->amount
                ];
            } catch (\Exception $e) {
                Log::error('Error saat memproses ludwig wallet', [
                    'wallet_id' => $ludwigWallet->id,
                    'error' => $e->getMessage()
                ]);
                throw $e; // Re-throw untuk ditangkap di catch block utama
            }
        }
        
        // 3. Buat record cancellation dengan canceller_type 'seller'
        try {
            $cancellation = OrderCancellation::create([
                'order_id' => $order->id,
                'cancelled_by' => $currentUserId,
                'canceller_type' => 'seller', // Ini penting! Tetapkan sebagai 'seller'
                'reason' => $request->reason,
                'refunded_amount' => $totalRefunded,
                'refunded_at' => now()
            ]);
            
            Log::info('Order cancellation record created', [
                'cancellation_id' => $cancellation->id,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat membuat record cancellation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        DB::commit();
        
        Log::info('Order berhasil dibatalkan oleh seller', [
            'order_id' => $order->id,
            'seller_id' => $currentUserId,
            'refunded_amount' => $totalRefunded,
            'refund_details' => $refundDetails
        ]);
        
        // Return redirect untuk browser request
        if (!$request->expectsJson()) {
            return redirect()->route('dashboard.orders.index')
                ->with('success', 'Order berhasil dibatalkan dan dana telah dikembalikan ke pelanggan');
        }
        
        // Return JSON untuk API request
        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibatalkan dan dana telah dikembalikan ke pelanggan',
            'order_id' => $order->id,
            'refunded_amount' => $totalRefunded,
            'refund_details' => $refundDetails
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        // Enhanced error logging
        Log::error('Error saat membatalkan order: ' . $e->getMessage(), [
            'order_id' => $order->id ?? 'unknown',
            'seller_id' => $currentUserId,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString()
        ]);
        
        // Return detailed error untuk debugging
        return response()->json([
            'success' => false, 
            'message' => 'Gagal membatalkan order: ' . $e->getMessage(),
            'error_detail' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'class' => get_class($e)
            ],
            'hint' => $this->getProbableCause($e)
        ], 500);
    }
}
    
    /**
     * Helper untuk memberikan petunjuk potensial penyebab error
     */
    private function getProbableCause(\Exception $e)
    {
        $message = $e->getMessage();
        $file = $e->getFile();
        
        // Deteksi error umum Laravel
        if (strpos($message, 'Column not found') !== false) {
            return 'Ada kolom database yang tidak ditemukan. Pastikan semua kolom yang diupdate tersedia di tabel';
        }
        
        if (strpos($message, 'Call to undefined method') !== false) {
            return 'Ada pemanggilan method yang tidak tersedia pada class/object. Cek apakah object tersebut sudah valid';
        }
        
        if (strpos($message, 'Trying to get property') !== false) {
            return 'Mencoba mengakses property dari null/tidak valid. Pastikan semua object sudah benar diinisialisasi';
        }
        
        if (strpos($message, 'SQLSTATE[23000]') !== false) {
            return 'Error constraint database. Mungkin ada foreign key constraint atau unique constraint yang dilanggar';
        }
        
        if (strpos($file, 'Middleware') !== false || strpos($message, 'middleware') !== false) {
            return 'Error terjadi di middleware. Cek apakah route didefinisikan dengan benar dan middleware berfungsi';
        }
        
        return 'Periksa log Laravel untuk detail lengkap. Error dapat terjadi karena missing field, relasi DB, atau middleware';
    }
}