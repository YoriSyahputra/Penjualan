<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    protected $fillable = [
        'category_id',
        'user_id',
        'store_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'discount_price',
        'stock',
        'stock_awal',
        'stock_alert',
        'sold_count',
        'is_active'
    ];

    protected $with = ['productImages'];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function packages()
    {
        return $this->hasMany(ProductPackage::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
