<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;

use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function cart()
{
    return view('ecom.cart');
}
    public function index(Request $request)
    {
        try {
            $query = Product::with(['productImages', 'category'])
                ->where('is_active', true);

            // Handle search
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            // Handle category filter
            if ($request->has('category') && $request->category != 'all') {
                $query->where('category_id', $request->category);
            }

            // Sort products
            $sortBy = $request->get('sort', 'latest');
            switch ($sortBy) {
                case 'price-low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popularity':
                    $query->orderBy('views', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $products = $query->paginate(40); // 4 columns × 10 rows = 40 items per page
            $categories = Category::all();

            return view('ecom.shop', [
                'products' => $products,
                'categories' => $categories,
                'selectedCategory' => $request->category ?? 'all',
                'searchTerm' => $request->search ?? '',
                'sortBy' => $sortBy
            ]);

        } catch (\Exception $e) {
            \Log::error('Shop index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load products. Please try again.');
        }
    }

    // ShopController.php
    public function getProductDetails($id)
    {
        $product = Product::with(['variants', 'packages', 'productImages'])
            ->findOrFail($id);
        
        return response()->json([
            'product' => $product,
            'variants' => $product->variants,
            'packages' => $product->packages,
            'productImages' => $product->productImages
        ]);
    }
    

public function addToCart(Request $request, $id)
{
    try {
        $product = Product::findOrFail($id);
        
        // Validate inputs
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
            'package_id' => 'nullable|exists:product_packages,id'
        ]);

        // Prepare cart item details
        $cartItem = [
            'id' => $id,
            'name' => $product->name,
            'price' => $product->discount_price ?? $product->price,
            'quantity' => $request->quantity,
            'image' => $product->productImages->first()->path_gambar ?? null,
            'variant_id' => $request->variant_id ?? null,
            'variant_name' => null,
            'package_id' => $request->package_id ?? null,
            'package_name' => null
        ];    

        // Add variant details if selected
        if ($request->variant_id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            $cartItem['variant_id'] = $variant->id;
            $cartItem['variant_name'] = $variant->name;
        }

        // Add package details if selected
        if ($request->package_id) {
            $package = ProductPackage::findOrFail($request->package_id);
            $cartItem['package_id'] = $package->id;
            $cartItem['package_name'] = $package->name;
        }

        // Generate a unique key for the cart item
        $cartKey = md5(json_encode($cartItem));

        // Get existing cart or initialize
        $cart = session()->get('cart', []);

        // Check if item exists and update quantity
        $existingItemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id && 
                ($item['variant_id'] ?? null) == ($cartItem['variant_id'] ?? null) && 
                ($item['package_id'] ?? null) == ($cartItem['package_id'] ?? null)) {
                $existingItemKey = $key;
                break;
            }
        }

        if ($existingItemKey !== null) {
            $cart[$existingItemKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = $cartItem;
        }

        // Update session
        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cartCount' => count($cart)
        ]);

    } catch (\Exception $e) {
        \Log::error('Add to cart error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error adding product to cart',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function update(Request $request, $key)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$key])) {
        // Ensure quantity doesn't go below 1
        $cart[$key]['quantity'] = max(1, $cart[$key]['quantity'] + $request->change);
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'newQuantity' => $cart[$key]['quantity']
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Item not found in cart'
    ]);
}

public function remove($key)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$key])) {
        unset($cart[$key]);
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => count($cart)
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Item not found in cart'
    ]);
}

}