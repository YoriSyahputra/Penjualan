<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'order_number',
        'payment_method',
        'payment_code',
        'shipping_method',
        'subtotal',
        'shipping_fee',
        'service_fee',
        'total',
        'address',
        'address_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function Address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function calculateStorePayments()
    {
        return $this->items->groupBy('product.store_id')->map(function($items, $storeId) {
            $store = $items->first()->product->store;
            $subtotal = $items->sum(fn($item) => $item->price * $item->quantity);
            $shipping = $this->shipping_fee * ($subtotal / $this->subtotal);
            
            return [
                'store' => $store,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping
            ];
        });
    }
}