<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with(['productImages', 'category'])
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            $categories = Category::all();

            return view('ecom.home', [
                'products' => $products,
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            \Log::error('Home page error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load products. Please try again.');
        }
    }
}