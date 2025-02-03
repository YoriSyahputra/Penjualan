<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['productImages', 'category'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)  // Limiting to 8 featured products
            ->get();

        return view('ecom.home', compact('products'));
    }
}