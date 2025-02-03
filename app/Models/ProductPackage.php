<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackage extends Model
{
    protected $fillable = [
        'product_id',
        'name'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}