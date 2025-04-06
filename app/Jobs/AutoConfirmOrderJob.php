<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\LudwigWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoConfirmOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);
        
        if (!$order) {
            Log::warning('Order tidak ditemukan untuk konfirmasi otomatis', [
                'order_id' => $this->orderId
            ]);
            return;
        }
        
        // Cek apakah order masih perlu dikonfirmasi (status masih delivered)
        if ($order->status_order !== 'delivered') {
            Log::info('Order sudah dikonfirmasi manual atau status tidak valid untuk konfirmasi otomatis', [
                'order_id' => $this->orderId,
                'current_status' => $order->status_order
            ]);
            return;
        }
        
        try {
            // Update status order dan release funds
            DB::transaction(function () use ($order) {
                // Update status order
                $order->update([
                    'status_order' => 'completed',
                    'completed_at' => now(),
                    'auto_confirmed' => true // Untuk audit/tracking
                ]);
                
                // Ambil semua LudwigWallet entries untuk order ini
                $ludwigWallets = LudwigWallet::where('order_id', $order->id)
                    ->where('status_payment', 'pending')
                    ->get();
                
                foreach ($ludwigWallets as $ludwigWallet) {
                    $this->releaseFunds($ludwigWallet);
                }
            });
            
            Log::info('Order berhasil dikonfirmasi otomatis', [
                'order_id' => $this->orderId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saat konfirmasi otomatis pesanan: ' . $e->getMessage(), [
                'order_id' => $this->orderId,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function releaseFunds(LudwigWallet $ludwigWallet)
    {
        DB::transaction(function () use ($ludwigWallet) {
            // 1. Release subtotal ke seller wallet
            if ($ludwigWallet->seller_id) {
                $sellerWallet = \App\Models\SellerWallet::where('user_id', $ludwigWallet->seller_id)->first();
                
                if ($sellerWallet) {
                    // Transfer subtotal ke seller wallet
                    $sellerWallet->increment('balance', $ludwigWallet->subtotal);
                    
                    Log::info('Dana subtotal dirilis ke Seller (auto)', [
                        'order_id' => $ludwigWallet->order_id,
                        'seller_id' => $ludwigWallet->seller_id,
                        'amount' => $ludwigWallet->subtotal
                    ]);
                }
            }
            
            // 2. Release shipping fee ke driver wallet
            if ($ludwigWallet->driver_id && $ludwigWallet->shipping_fee > 0) {
                // Cek apakah driver sudah punya wallet, jika belum buat baru
                $driverWallet = \App\Models\DriverWallet::firstOrCreate(
                    ['driver_id' => $ludwigWallet->driver_id],
                    [
                        'user_id' => $ludwigWallet->driver_id,
                        'balance' => 0
                    ]
                );
                
                // Transfer shipping fee ke driver wallet
                $driverWallet->increment('balance', $ludwigWallet->shipping_fee);
                
                Log::info('Shipping fee dirilis ke Driver (auto)', [
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
}