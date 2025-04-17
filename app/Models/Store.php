<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
        'phone_number',
        'address',
        'logo',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sellerWallet()
    {
        return $this->hasOne(SellerWallet::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }
    protected static function booted()
    {
        static::created(function ($store) {
            SellerWallet::create([
                'user_id' => $store->user_id,
                'store_id' => $store->id,
                'balance' => 0,
            ]);
        });
    }

}