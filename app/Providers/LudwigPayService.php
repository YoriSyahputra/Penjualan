<?php

namespace App\Services;

use App\Models\LudwigPayment;
use Illuminate\Support\Str;

class LudwigPayService
{
    public function createPayment($order, $paymentMethod)
    {
        return LudwigPayment::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'payment_id' => 'LUD-' . Str::random(10),
            'amount' => $order->total_amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending'
        ]);
    }

    public function processPayment($payment)
    {
        // Simulate payment processing
        $success = rand(0, 1); // In real implementation, this would be actual payment processing

        if ($success) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'transaction_reference' => 'TRX-' . Str::random(8)
            ]);
            return true;
        }

        $payment->update(['status' => 'failed']);
        return false;
    }
}
