<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

   // In your Order model
    protected $fillable = [
        'user_id',
        'order_number',
        'payment_method',
        'payment_code',
        'shipping_method',
        'subtotal',
        'shipping_fee',
        'service_fee',
        'total',
        'alamat_lengkap',
        'provinsi',
        'kota',
        'kecamatan',
        'kode_pos',
        'address_id',
        'status',
        'store_id',
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