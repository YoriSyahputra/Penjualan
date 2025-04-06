<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LudwigWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'user_id', 
        'seller_id', 
        'driver_id',
        'amount', 
        'shipping_fee', // Add this field to separate the shipping fee
        'subtotal',     // Add this field to store the product subtotal
        'status_package', 
        'status_payment',
        'pickup_at',
        'delivery_at',
        'released_at',
        'delivery_notes'
    ];

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke User (yg transfer)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Seller
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Relasi ke Driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Scope buat filter status package
    public function scopePending($query)
    {
        return $query->where('status_package', 'pending');
    }

    public function scopeOnDelivery($query)
    {
        return $query->where('status_package', 'on_delivery');
    }
}