<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;
use App\Models\Cart;  
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Support\Facades\Log;
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
    
            // Handle category filter - Modified this part
            $selectedCategory = null;
            if ($request->has('category')) {
                $selectedCategory = Category::find($request->category);
                if ($selectedCategory) {
                    $query->where('category_id', $selectedCategory->id);
                }
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
                'selectedCategory' => $selectedCategory, // Now passing the Category model object
                'searchTerm' => $request->search ?? '',
                'sortBy' => $request->get('sort', 'latest')
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
            // Periksa apakah product ada sebelum mengakses propertinya
            if ($item->product) {
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
            } else {
                // Optional: Hapus item keranjang yang tidak memiliki produk valid
                // $item->delete();
                
                // Log error untuk debugging
                \Log::warning('Cart item #' . $item->id . ' has null product');
            }
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

    public function remove($cartItemId)
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
    public function checkout()
    {
        try {
            // Ambil item cart dari user yang sedang login
            $cartItems = Cart::with(['product.productImages', 'variant', 'package'])
                ->where('user_id', auth()->id())
                ->get();

            $items = [];
            $subtotal = 0;
            $shippingFee = 10000; // Contoh default shipping fee
            $serviceFee = 1000;   // Contoh default service fee

            foreach ($cartItems as $item) {
                // Periksa apakah product ada sebelum mengakses propertinya
                if ($item->product) {
                    $price = $item->product->discount_price > 0 ? 
                            $item->product->discount_price : 
                            $item->product->price;
                    
                    if ($item->variant) {
                        $price += $item->variant->price_adjustment;
                    }
                    
                    if ($item->package) {
                        $price += $item->package->price_adjustment;
                    }

                    // Get main image
                    $image = $item->product->productImages()
                        ->where('gambar_utama', true)
                        ->first();

                    $items[] = [
                        'id' => $item->id,
                        'product_id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $price,
                        'quantity' => $item->quantity,
                        'image' => $image ? $image->path_gambar : null,
                        'variant_name' => $item->variant?->name,
                        'package_name' => $item->package?->name,
                        'subtotal' => $price * $item->quantity
                    ];

                    $subtotal += $price * $item->quantity;
                } else {
                    // Log warning untuk item cart yang tidak memiliki produk
                    \Log::warning('Checkout: Cart item #' . $item->id . ' has null product');
                    
                    // Optional: Hapus item cart yang tidak valid
                    // $item->delete();
                }
            }

            return view('ecom.checkout', [
                'items' => $items,
                'subtotal' => $subtotal,
                'shippingFee' => $shippingFee,
                'serviceFee' => $serviceFee
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Unable to process checkout. Please try again.');
        }
    }
    
    public function placeOrder(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'address' => 'required_without:selected_address|string|nullable',
                'selected_address' => 'required_without:address|exists:user_addresses,id|nullable',
                'shipping_method' => 'required|in:regular,express',
                'payment_method' => 'required|in:ludwig_payment,ewallet,cod',
            ]);

            // Get cart items
            $cartItems = Cart::with(['product', 'variant', 'package'])
                ->where('user_id', auth()->id())
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            // Calculate totals
            $subtotal = 0;
            $shippingFee = $request->shipping_method === 'regular' ? 15000 : 30000;
            $serviceFee = 1000;

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

                $subtotal += $price * $item->quantity;
            }

            $total = $subtotal + $shippingFee + $serviceFee;

            // Generate unique order number
            $orderNumber = 'ORD-' . time() . '-' . auth()->id();

            // Generate payment code for Ludwig Payment
            $paymentCode = null;
            if ($request->payment_method === 'ludwig_payment') {
                $paymentCode = 'LWP-' . strtoupper(substr(md5(time()), 0, 10));
            }

            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'payment_method' => $request->payment_method,
                'payment_code' => $paymentCode,
                'shipping_method' => $request->shipping_method,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'service_fee' => $serviceFee,
                'total' => $total,
                'address' => $request->address ?? auth()->user()->addresses()->find($request->selected_address)->address,
                'address_id' => $request->selected_address,
                'status' => 'pending',
            ]);

            // Create order items
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

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'variant_id' => $item->variant_id,
                    'package_id' => $item->package_id,
                ]);
            }

            // Clear the cart
            Cart::where('user_id', auth()->id())->delete();

            // Redirect to order confirmation page
            return redirect()->route('order.confirmation', $order->id);

        } catch (\Exception $e) {
            \Log::error('Place order error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Failed to place order. Please try again.');
        }
    }
    public function orderConfirmation(Order $order)
    {
        // Make sure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $items = [];
        foreach ($order->items as $item) {
            $image = $item->product->productImages()
                ->where('gambar_utama', true)
                ->first();

            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $image ? $image->path_gambar : null,
                'variant_name' => $item->variant?->name,
                'package_name' => $item->package?->name,
                'subtotal' => $item->price * $item->quantity
            ];
        }

        return view('ecom.process_checkout', [
            'items' => $items,
            'subtotal' => $order->subtotal,
            'shippingFee' => $order->shipping_fee,
            'serviceFee' => $order->service_fee,
            'paymentCode' => $order->payment_code,
            'paymentMethod' => $order->payment_method,
            'shippingMethod' => $order->shipping_method,
            'address' => $order->address,
            'selected_address_id' => $order->address_id,
            'order' => $order,
        ]);
    }
    public function unpaidOrders()
{
    // Ambil user yang sedang login
    $user = auth()->user();

    // Ambil semua orders untuk user tersebut (bukan hanya yang unpaid)
    $allOrders = Order::where('user_id', $user->id)
        ->with([
            'items.product',
            'items.variant',
            'items.package'
        ])
        ->latest()
        ->get();

    // Filter untuk pending orders
    $unpaidOrders = $allOrders->where('status', 'pending');

    // Logging untuk debugging (opsional)
    \Log::info('Unpaid Orders Debug:', [
        'user_id' => $user->id,
        'order_count' => $unpaidOrders->count(),
        'orders' => $unpaidOrders->map(function ($order) {
            return [
                'id'          => $order->id,
                'total'       => $order->total,
                'items_count' => $order->items->count(),
                'items'       => $order->items->map(function ($item) {
                    return [
                        'product_id'   => $item->product_id,
                        'product_name' => $item->product->name ?? 'N/A',
                        'quantity'     => $item->quantity,
                        'price'        => $item->price,
                        'variant_name' => $item->variant->name ?? null,
                        'package_name' => $item->package->name ?? null,
                    ];
                })->toArray(),
            ];
        })->toArray(),
    ]);

    // Kembalikan view dengan data order yang belum dibayar
    return view('ecom.list_order_payment', compact('allOrders', 'unpaidOrders'));
}
    public function cancel($id)
    {
        // Cari order berdasarkan id
        $order = Order::findOrFail($id);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah order dalam status pending sehingga dapat dibatalkan
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Order tidak dapat dibatalkan.');
        }

        // Update status order menjadi cancelled
        $order->update(['status' => 'cancelled']);

        return redirect()->route('payment.list_order_payment')->with('success', 'Order berhasil dibatalkan.');
    }
    public function listOrderPayment()
    {
        $user = auth()->user();
        
        // Get all orders for the user
        $allOrders = Order::where('user_id', $user->id)
                          ->with(['items.product', 'items.variant', 'items.package'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        // Get only unpaid orders for notification (assuming 'pending' status means unpaid)
        $unpaidOrders = $allOrders->where('status', 'pending');
        
        return view('ecom.list_order_payment', compact('allOrders', 'unpaidOrders'));
    }
}
