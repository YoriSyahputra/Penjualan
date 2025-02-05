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
    $cartItems = Cart::with(['product', 'variant', 'package'])
        ->where('user_id', auth()->id())
        ->get();

    return view('ecom.cart', ['cartItems' => $cartItems]);
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
            $user = auth()->user();
            $product = Product::findOrFail($id);
            
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'nullable|exists:product_variants,id',
                'package_id' => 'nullable|exists:product_packages,id'
            ]);
    
            // Check for existing cart item with same product, variant, and package
            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $id)
                ->where('variant_id', $request->variant_id)
                ->where('package_id', $request->package_id)
                ->first();
    
            if ($existingCartItem) {
                // Update quantity if item exists
                $existingCartItem->quantity += $request->quantity;
                $existingCartItem->save();
            } else {
                // Create new cart item
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $id,
                    'quantity' => $request->quantity,
                    'variant_id' => $request->variant_id,
                    'package_id' => $request->package_id
                ]);
            }
    
            return response()->json([
                'message' => 'Product added to cart successfully',
                'cartCount' => Cart::where('user_id', $user->id)->count()
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error adding product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    public function updateCartQuantity(Request $request, $cartItemId)
{
    $user = auth()->user();
    $cartItem = Cart::where('id', $cartItemId)
        ->where('user_id', $user->id)
        ->first();

    if (!$cartItem) {
        return response()->json([
            'success' => false,
            'message' => 'Cart item not found'
        ], 404);
    }

    $newQuantity = max(1, $cartItem->quantity + $request->change);
    $cartItem->quantity = $newQuantity;
    $cartItem->save();

    return response()->json([
        'success' => true,
        'newQuantity' => $newQuantity,
        'cartCount' => Cart::where('user_id', $user->id)->count()
    ]);
}
public function removeCartItem($cartItemId)
{
    $user = auth()->user();
    $cartItem = Cart::where('id', $cartItemId)
        ->where('user_id', $user->id)
        ->first();

    if (!$cartItem) {
        return response()->json([
            'success' => false,
            'message' => 'Cart item not found'
        ], 404);
    }

    $cartItem->delete();

    return response()->json([
        'success' => true,
        'cartCount' => Cart::where('user_id', $user->id)->count()
    ]);
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
public function checkout(Request $request)
{
    $selectedItems = explode(',', $request->items);
    $cart = session()->get('cart', []);
    
    $checkoutItems = [];
    $subtotal = 0;
    
    foreach ($selectedItems as $key) {
        if (isset($cart[$key])) {
            $checkoutItems[$key] = $cart[$key];
            $subtotal += $cart[$key]['price'] * $cart[$key]['quantity'];
        }
    }
    
    if (empty($checkoutItems)) {
        return redirect()->route('cart.index')->with('error', 'No items selected for checkout');
    }
    
    $serviceFee = 1000; // Example service fee
    $shippingFee = 0; // Will be calculated based on shipping option
    
    return view('ecom.checkout', [
        'items' => $checkoutItems,
        'subtotal' => $subtotal,
        'serviceFee' => $serviceFee,
        'shippingFee' => $shippingFee,
    ]);
}
public function placeOrder(Request $request)
{
    $request->validate([
        'address' => 'required|string',
        'shipping_method' => 'required|string',
        'payment_method' => 'required|string',
    ]);
    
    // Here you would:
    // 1. Create order record
    // 2. Create order items
    // 3. Clear cart
    // 4. Redirect to success page
    
    return redirect()->route('order.success')->with('success', 'Order placed successfully!');
}

}