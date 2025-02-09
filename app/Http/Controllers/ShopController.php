<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;
use App\Models\Cart;  
use Illuminate\Http\Request;

class ShopController extends Controller
{
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

            $products = $query->paginate(40);
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

    public function cart()
    {
        $cartItems = Cart::with(['product.productImages', 'variant', 'package'])
            ->where('user_id', auth()->id())
            ->get();

        $cartData = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $price = $item->product->discount_price > 0 ? 
                    $item->product->discount_price : 
                    $item->product->price;
            
            if ($item->variant) {
                $price += $item->variant->price_adjustment;
            }
            
            if ($item->package) {
                $price += $item->package->price_adjustment;
            }

            // Update image handling
            $image = $item->product->productImages()
                ->where('gambar_utama', true)
                ->first();

            $cartData[] = [
                'id' => $item->id,
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'original_price' => $item->product->price,
                'price' => $price,
                'has_discount' => $item->product->discount_price > 0,
                'quantity' => $item->quantity,
                'image' => $image ? $image->path_gambar : null, // Use path_gambar field
                'variant_id' => $item->variant?->id,
                'variant_name' => $item->variant?->name,
                'package_id' => $item->package?->id,
                'package_name' => $item->package?->name,
                'subtotal' => $price * $item->quantity
            ];

            $subtotal += $price * $item->quantity;
        }

        return view('ecom.cart', [
            'cartItems' => $cartData,
            'subtotal' => $subtotal
        ]);
    }


    public function addToCart(Request $request, $id)
{
    try {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
            'package_id' => 'nullable|exists:product_packages,id'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first'
            ], 401);
        }

        // Check if product exists in cart with same variant and package
        $cartItem = Cart::where([
            'user_id' => auth()->id(),
            'product_id' => $id,
            'variant_id' => $request->variant_id,
            'package_id' => $request->package_id,
        ])->first();

        if ($cartItem) {
            // Update quantity if product already exists
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart item if product doesn't exist
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $id,
                'quantity' => $request->quantity,
                'variant_id' => $request->variant_id,
                'package_id' => $request->package_id
            ]);
        }

        // Get distinct product count
        $uniqueProductCount = Cart::where('user_id', auth()->id())
            ->distinct()
            ->count('product_id');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cartCount' => $uniqueProductCount
        ]);

    } catch (\Exception $e) {
        \Log::error('Add to cart error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error adding product to cart: ' . $e->getMessage()
        ], 500);
    }
}

    public function updateCartQuantity(Request $request, $cartItemId)
    {
        try {
            $cartItem = Cart::where('id', $cartItemId)
                        ->where('user_id', auth()->id())
                        ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $newQuantity = max(1, $cartItem->quantity + $request->change);
            
            if ($newQuantity > $cartItem->product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available'
                ], 400);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            $uniqueProductCount = Cart::where('user_id', auth()->id())
                                    ->distinct()
                                    ->count('product_id');

            return response()->json([
                'success' => true,
                'newQuantity' => $newQuantity,
                'cartCount' => $uniqueProductCount  
            ]);

        } catch (\Exception $e) {
            \Log::error('Update cart quantity error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart quantity'
            ], 500);
        }
    }
    public function update(Request $request, $key)
{
    try {
        $cart = Cart::where('id', $key)
                   ->where('user_id', auth()->id())
                   ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $product = $cart->product;
        $newQuantity = $cart->quantity + $request->input('change');

        // Validate quantity
        if ($newQuantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Quantity cannot be less than 1'
            ], 400);
        }

        if ($newQuantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        // Update the quantity
        $cart->quantity = $newQuantity;
        $cart->save();

        // Get the updated cart count
        $cartCount = Cart::where('user_id', auth()->id())
            ->distinct()
            ->count('product_id');

            return response()->json([
                'success' => true,
                'distinctCount' => Cart::where('user_id', auth()->id())
                                     ->select('product_id')
                                     ->distinct()
                                     ->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart'
            ]);
        }    
    }

    public function removeCartItem($cartItemId)
{
    try {
        $cartItem = Cart::where('id', $cartItemId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->delete();
        
        // Get distinct product count
        $uniqueProductCount = Cart::where('user_id', auth()->id())
            ->distinct()
            ->count('product_id');

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully',
            'cartCount' => $uniqueProductCount
        ]);

    } catch (\Exception $e) {
        \Log::error('Remove cart item error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error removing cart item'
        ], 500);
    }
}
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
}