<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LudwigPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'transaction_reference'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}