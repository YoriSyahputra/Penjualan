<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cancelled_by',
        'canceller_type',
        'reason',
        'refunded_amount',
        'refunded_at'
    ];

    protected $casts = [
        'refunded_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}