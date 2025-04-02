<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


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
        'shipping_kurir',
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
        'status_order',
        'nomor_resi',
        'status_package',
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
    public function generateTrackingNumber()
    {
        if ($this->status_order === 'processing') {
            $shippingKurir = Str::upper($this->shipping_kurir);
            $randomPart = Str::upper(Str::random(15));
            $this->nomor_resi = "LW-{$shippingKurir}-{$randomPart}";
            $this->save();
        }
    }
    public function clearTrackingNumber()
    {
        if (in_array($this->status_order, ['pending', 'cancelled'])) {
            $this->nomor_resi = null;
            $this->save();
        }
    }

}