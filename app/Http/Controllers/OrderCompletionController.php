<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\LudwigWallet;
use App\Models\SellerWallet;
use App\Models\DriverWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCompletionController extends Controller
{
    public function confirmOrderCompletion(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Validasi kepemilikan order
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak punya akses ke order ini'
            ], 403);
        }
        
        // Validasi status order (harus delivered)
        if ($order->status_order !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Order ini belum selesai diantar'
            ], 400);
        }
        
        try {
            // Update status order dan release funds
            DB::transaction(function () use ($order) {
                // Update status order
                $order->update([
                    'status_order' => 'completed',
                    'completed_at' => now()
                ]);
                
                // Ambil semua LudwigWallet entries untuk order ini
                $ludwigWallets = LudwigWallet::where('order_id', $order->id)
                    ->where('status_payment', 'pending')
                    ->get();
                
                foreach ($ludwigWallets as $ludwigWallet) {
                    $this->releaseFunds($ludwigWallet);
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dikonfirmasi dan dana telah dirilis'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saat konfirmasi pesanan: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => auth()->id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
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
    
    // Method untuk konfirmasi otomatis setelah 1 jam
    public function scheduleAutomaticCompletion($orderId)
    {
        // Membuat job untuk dijalankan setelah 1 jam
        \App\Jobs\AutoConfirmOrderJob::dispatch($orderId)
            ->delay(now()->addHour());
            
        return response()->json([
            'success' => true,
            'message' => 'Pesanan akan dikonfirmasi otomatis dalam 1 jam'
        ]);
    }
}