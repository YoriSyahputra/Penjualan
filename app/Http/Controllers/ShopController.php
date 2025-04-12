<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;
use App\Models\Comment;
use App\Models\Cart;  
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCancellation;
use App\Models\DeliveryHistory;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['productImages', 'category', 'store'])
                ->where('is_active', true);    
    
            // Handle search
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }
        $store = null;
        if ($request->has('store_id')) {
            $store = \App\Models\Store::find($request->store_id);
        } else {
            // Fetch a random store instead of the first one
            $store = \App\Models\Store::inRandomOrder()->first();
        }

        // Handle category filter
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
            'selectedCategory' => $selectedCategory,
            'searchTerm' => $request->search ?? '',
            'sortBy' => $request->get('sort', 'latest'),
            'store' => $store ? $store : null
        ]);
    } catch (\Exception $e) {
        \Log::error('Shop index error: ' . $e->getMessage());
        return back()->with('error', 'Unable to load products. Please try again.');
    }
}
public function storeProducts(Request $request, $storeId)
{
    try {
        $store = \App\Models\Store::findOrFail($storeId);
        
        $query = Product::with(['productImages', 'category', 'store'])
                ->where('store_id', $store->id) // Filter berdasarkan store
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
        
        $products = $query->paginate(12);
        $categories = Category::all();
        
        // Ensure we're using the store.products view template
        return view('store.products', data: [
            'store' => $store,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'searchTerm' => $request->search ?? '',
            'sortBy' => $sortBy
        ]);
    } catch (\Exception $e) {
        \Log::error('Store products error: ' . $e->getMessage());
        return back()->with('error', 'Unable to load store products. Please try again.');
    }
}
public function storeProductsAjax(Request $request, $storeId)
{
    try {
        $store = \App\Models\Store::findOrFail($storeId);
        
        $query = Product::with(['productImages', 'category'])
            ->where('user_id', $store->user_id)
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
        $selectedCategory = null;
        if ($request->has('category') && !empty($request->category)) {
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
        
        $products = $query->paginate(12);
        
        if ($request->ajax()) {
            // Render products HTML
            $productsHtml = view('partials.product-grid', [
                'products' => $products,
                'store' => $store
            ])->render();
            
            // Render pagination HTML
            $paginationHtml = $products->appends(request()->query())->links()->toHtml();
            
            return response()->json([
                'html' => $productsHtml,
                'pagination' => $paginationHtml,
                'total' => $products->total()
            ]);
        }
        
        // This should not be reached if the request is AJAX
        return redirect()->route('store.products', ['storeId' => $storeId]);
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
        \Log::error('Store products error: ' . $e->getMessage());
        return back()->with('error', 'Unable to load store products. Please try again.');
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
                    $price += $item->variant->price;
                }
                
                if ($item->package) {
                    $price += $item->package->price;
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
    public function getProductDetail($id) 
{
    try {
        $product = Product::with(['variants', 'packages', 'productImages'])->findOrFail($id);

        // Ambil rekomendasi produk, misalnya produk lain dari kategori yang sama
        $recommended = Product::where('category_id', $product->category_id)
                              ->where('id', '!=', $product->id)
                              ->take(4)
                              ->get();

        return view('ecom.product_details', [
            'product'       => $product,
            'variants'      => $product->variants,
            'packages'      => $product->packages,
            'productImages' => $product->productImages,
            'recommended'   => $recommended // pastikan variabel ini dikirim ke view
        ]);
    } catch (\Exception $e) {
        \Log::error('Get product details error: ' . $e->getMessage());
        return redirect()->route('shop.index')->with('error', 'Product not found or an error occurred.');
    }
}
    public function getProductDetails($id)
    {
        $product = Product::with(['variants', 'packages', 'productImages'])->findOrFail($id);
        
        return response()->json([
            'product' => $product,
            'variants' => $product->variants,
            'packages' => $product->packages,
            'productImages' => $product->productImages
        ]);
    }

    public function checkout(Request $request)
    {
        try {
            // Ambil parameter 'items' dari URL, misalnya ?items=1,3
            $selectedItemIds = $request->query('items') ? explode(',', $request->query('items')) : [];

            if (empty($selectedItemIds)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Tidak ada item yang dipilih untuk checkout.');
            }

            // Ambil item cart dari user yang sedang login hanya untuk item yang dipilih
            $cartItems = Cart::with(['product.productImages', 'variant', 'package'])
                ->where('user_id', auth()->id())
                ->whereIn('id', $selectedItemIds)
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
                        $price += $item->variant->price;
                    }

                    if ($item->package) {
                        $price += $item->package->price;
                    }

                    // Ambil gambar utama
                    $image = $item->product->productImages()
                        ->where('gambar_utama', true)
                        ->first();

                    $items[] = [
                        'id'            => $item->id,
                        'product_id'    => $item->product->id,
                        'name'          => $item->product->name,
                        'price'         => $price,
                        'quantity'      => $item->quantity,
                        'image'         => $image ? $image->path_gambar : null,
                        'variant_name'  => $item->variant?->name,
                        'package_name'  => $item->package?->name,
                        'store_id'      => $item->product->store_id,  
                        'store_name'    => $item->product->store->name ?? 'Unknown Store',  
                        'subtotal'      => $price * $item->quantity
                    ];

                    $subtotal += $price * $item->quantity;
                } else {
                    \Log::warning('Checkout: Cart item #' . $item->id . ' has null product');
                    // Optional: $item->delete();
                }
            }

            return view('ecom.checkout', [
                'items'       => $items,
                'subtotal'    => $subtotal,
                'shippingFee' => $shippingFee,
                'serviceFee'  => $serviceFee
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->with('error', 'Unable to process checkout. Please try again.');
        }
    }
    
    public function placeOrder(Request $request)
{
    try {
        // Validate request
        $validated = $request->validate([
            'alamat_lengkap' => 'required_without:selected_address|string',
            'provinsi'       => 'required_without:selected_address|string',
            'kota'           => 'required_without:selected_address|string',
            'kecamatan'      => 'required_without:selected_address|string',
            'kode_pos'       => 'required_without:selected_address|string',
            'selected_address' => 'required_without:alamat_lengkap|exists:addresses,id',
            'shipping_method' => 'required|in:regular,express',
            'shipping_kurir' => 'required|in:jne,sicepat,j&t,lwexpress',
            'payment_method' => 'required|in:ludwig_payment,ewallet,cod',
            'selected_items' => 'required|string',
        ]);

        // Parse the comma-separated string into an array
        $selectedItemIds = explode(',', $request->input('selected_items'));
        
        if (empty($selectedItemIds)) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        // Get only the selected cart items
        $cartItems = Cart::with(['product', 'variant', 'package'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItemIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        // Generate payment code for Ludwig Payment (same for all orders)
        $paymentCode = null;
        if ($request->payment_method === 'ludwig_payment') {
            $paymentCode = 'LWP-' . strtoupper(substr(md5(time()), 0, 10));
        } elseif ($request->payment_method === 'ewallet') {
            $paymentCode = 'EWL-' . strtoupper(substr(md5(time()), 0, 10));
        }

        // Group cart items by store
        $itemsByStore = $cartItems->groupBy(function($item) {
            return $item->product->store_id;
        });

        $shippingFee = $request->shipping_method === 'regular' ? 15000 : 30000;
        $serviceFee = 1000;
        $orders = [];

        // Get address details if using a saved address
        $addressDetails = null;
        if ($request->selected_address) {
            $addressDetails = auth()->user()->addresses()->find($request->selected_address);
        }

        // Create separate orders for each store
        foreach ($itemsByStore as $storeId => $storeItems) {
            // Calculate subtotal for this store's items
            $subtotal = 0;
            foreach ($storeItems as $item) {
                $price = $item->product->discount_price > 0 ? 
                        $item->product->discount_price : 
                        $item->product->price;
                
                if ($item->variant) {
                    $price += $item->variant->price;
                }
                
                if ($item->package) {
                    $price += $item->package->price;
                }

                $subtotal += $price * $item->quantity;
            }

            $total = $subtotal + $shippingFee + $serviceFee;

            // Generate unique order number (different for each store)
            $orderNumber = 'ORD-' . time() . '-' . auth()->id() . '-' . $storeId;

            // Create the order for this store with the new address fields
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'payment_method' => $request->payment_method,
                'payment_code' => $paymentCode, // Same payment code for all orders
                'shipping_method' => $request->shipping_method,
                'shipping_kurir' => $request->shipping_kurir,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'service_fee' => $serviceFee,
                'total' => $total,
                'alamat_lengkap' => $addressDetails ? $addressDetails->alamat_lengkap : $request->alamat_lengkap,
                'provinsi' => $addressDetails ? $addressDetails->provinsi : $request->provinsi,
                'kota' => $addressDetails ? $addressDetails->kota : $request->kota,
                'kecamatan' => $addressDetails ? $addressDetails->kecamatan : $request->kecamatan,
                'kode_pos' => $addressDetails ? $addressDetails->kode_pos : $request->kode_pos,
                'address_id' => $request->selected_address,
                'status' => 'pending',
                'store_id' => $storeId,
            ]);

            $orders[] = $order;

            // Create order items for this store
            foreach ($storeItems as $item) {
                $price = $item->product->discount_price > 0 ? 
                        $item->product->discount_price : 
                        $item->product->price;
                
                if ($item->variant) {
                    $price += $item->variant->price;
                }
                
                if ($item->package) {
                    $price += $item->package->price;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'variant_id' => $item->variant_id,
                    'package_id' => $item->package_id,
                    'store_id' => $storeId, // Save the store ID in the order item
                ]);
            }

            // Delete cart items for this store
            $storeItemIds = $storeItems->pluck('id')->toArray();
            Cart::whereIn('id', $storeItemIds)->delete();
        }

        // Redirect to confirmation page for the first order
        // You may want to modify your confirmation page to show all related orders
        return redirect()->route('order.confirmation', $orders[0]->id)
            ->with('all_order_ids', collect($orders)->pluck('id')->toArray());

    } catch (\Exception $e) {
        Log::error('Place order error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
        return redirect()->route('checkout')->with('error', 'Failed to place order. Please try again.');
    }
}

public function orderConfirmation(Order $order)
{
    \Log::info('Order status: ' . $order->status);

    // Make sure the order belongs to the authenticated user
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    // Redirect based on order status
    if ($order->status === 'pending') {
        // Existing code for showing checkout process
        
        // Check if we have related orders (same payment code)
        $relatedOrders = [];
        if ($order->payment_code) {
            $relatedOrders = Order::where('payment_code', $order->payment_code)
                ->where('user_id', auth()->id())
                ->where('id', '!=', $order->id)
                ->get();
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
                'subtotal' => $item->price * $item->quantity,
                'store_name' => $item->product->store->name ?? 'Unknown Store'
            ];
        }

        // Prepare related orders data if any exist
        $relatedOrdersData = [];
        $totalAmount = $order->total;

        if (!$relatedOrders->isEmpty()) {
            foreach ($relatedOrders as $relOrder) {
                $relItems = [];
                
                foreach ($relOrder->items as $item) {
                    $image = $item->product->productImages()
                        ->where('gambar_utama', true)
                        ->first();
                        
                    $relItems[] = [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'image' => $image ? $image->path_gambar : null,
                        'variant_name' => $item->variant?->name,
                        'package_name' => $item->package?->name,
                        'subtotal' => $item->price * $item->quantity,
                        'store_name' => $item->product->store->name ?? 'Unknown Store'
                    ];
                }
                
                $relatedOrdersData[] = [
                    'order' => $relOrder,
                    'items' => $relItems
                ];
                
                $totalAmount += $relOrder->total;
            }
        }

        // Prepare full address for display
        $fullAddress = $this->formatFullAddress($order);

        return view('ecom.process_checkout', [
            'items' => $items,
            'subtotal' => $order->subtotal,
            'shippingFee' => $order->shipping_fee,
            'serviceFee' => $order->service_fee,
            'paymentCode' => $order->payment_code,
            'paymentMethod' => $order->payment_method,
            'shippingMethod' => $order->shipping_method,
            'shipping_kurir' => $order->shipping_kurir,
            'fullAddress' => $fullAddress,
            'alamat_lengkap' => $order->alamat_lengkap,
            'provinsi' => $order->provinsi,
            'kota' => $order->kota,
            'kecamatan' => $order->kecamatan,
            'kode_pos' => $order->kode_pos,
            'selected_address_id' => $order->address_id,
            'order' => $order,
            'relatedOrders' => $relatedOrdersData,
            'totalAmount' => $totalAmount
        ]);
    } 
    else if ($order->status === 'paid') {
        // For paid orders, display the receipt
        // Prepare order items
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
                'subtotal' => $item->price * $item->quantity,
                'store_name' => $item->product->store->name ?? 'Unknown Store'
            ];
        }

        // Check for related orders (same payment code)
        $relatedOrders = [];
        $relatedOrdersData = [];
        $totalAmount = $order->total;

        if ($order->payment_code) {
            $relatedOrders = Order::where('payment_code', $order->payment_code)
                ->where('user_id', auth()->id())
                ->where('id', '!=', $order->id)
                ->get();
                
            if (!$relatedOrders->isEmpty()) {
                foreach ($relatedOrders as $relOrder) {
                    $relItems = [];
                    
                    foreach ($relOrder->items as $item) {
                        $image = $item->product->productImages()
                            ->where('gambar_utama', true)
                            ->first();
                            
                        $relItems[] = [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'image' => $image ? $image->path_gambar : null,
                            'variant_name' => $item->variant?->name,
                            'package_name' => $item->package?->name,
                            'subtotal' => $item->price * $item->quantity,
                            'store_name' => $item->product->store->name ?? 'Unknown Store'
                        ];
                    }
                    
                    $relatedOrdersData[] = [
                        'order' => $relOrder,
                        'items' => $relItems
                    ];
                    
                    $totalAmount += $relOrder->total;
                }
            }
        }
        $cancellation = OrderCancellation::where('order_id', operator: $order->id)->first();
        $cancellationReason = $cancellation ? $cancellation->reason : null;
        
        // Prepare full address for display
        $fullAddress = $this->formatFullAddress($order);
        return view('ecom.order_receipt', [
            'order' => $order,
            'items' => $items,
            'subtotal' => $order->subtotal,
            'shippingFee' => $order->shipping_fee,
            'serviceFee' => $order->service_fee,
            'shippingMethod' => $order->shipping_method,
            'shippingKurir' => $order->shipping_kurir,
            'fullAddress' => $fullAddress,
            'alamat_lengkap' => $order->alamat_lengkap,
            'provinsi' => $order->provinsi,
            'kota' => $order->kota,
            'kecamatan' => $order->kecamatan,
            'kode_pos' => $order->kode_pos,
            'relatedOrders' => $relatedOrdersData,
            'totalAmount' => $totalAmount,
            'cancellationReason' => $cancellationReason,
        ]);
    }
    else {
        // For any other status, redirect to orders list with a message
        return redirect()->route('ecom.list_order_payment')
            ->with('message', 'Order status: ' . ucfirst($order->status));
    }
}

// Helper function to format full address from components
private function formatFullAddress(Order $order)
{
    return $order->alamat_lengkap . ', ' . 
           $order->kecamatan . ', ' . 
           $order->kota . ', ' . 
           $order->provinsi . ' ' . 
           $order->kode_pos;
}

public function verifyPayment(Request $request)
{
    try {
        // Validate request
        $validated = $request->validate([
            'payment_code' => 'required|string',
            // Add any other verification fields you might need
        ]);

        $paymentCode = $request->payment_code;
        
        // Find all orders with this payment code that belong to the authenticated user
        $orders = Order::where('payment_code', $paymentCode)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->get();
            
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No pending orders found with this payment code.');
        }
        
        // Update the status of all related orders
        foreach ($orders as $order) {
            $order->status = 'paid';  // or whatever status you use for paid orders
            $order->save();
        }
        
        return redirect()->route('orders.index')->with('success', 'Payment verified successfully! All orders have been updated.');
        
    } catch (\Exception $e) {
        Log::error('Payment verification error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
        return redirect()->back()->with('error', 'Failed to verify payment. Please try again.');
    }
}
public function unpaidOrders(Request $request)
{
    $user = auth()->user();
    $search = $request->input('search');
    $statusFilter = $request->input('status');
    
    // Query builder for orders
    $ordersQuery = Order::where('user_id', $user->id)
                        ->with(['items.product', 'items.variant', 'items.package', 'cancellation']);
    
    // Apply status filter if provided
    if ($statusFilter) {
        $ordersQuery->where('status', $statusFilter);
    }
    
    // Apply search if provided
    if ($search) {
        $ordersQuery->where(function($query) use ($search) {
            $query->where('id', 'like', "%{$search}%")
                ->orWhereHas('items.product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }
    
    // Get paginated results
    $allOrders = $ordersQuery->latest()->paginate(10);
    
    // Get unpaid orders count for notification
    $unpaidOrders = Order::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->get();
                        
    // Define cancellation reasons
    $cancellationReasons = [
        'changed_mind' => 'Berubah pikiran',
        'found_better_price' => 'Menemukan harga lebih baik',
        'mistaken_order' => 'Salah pesan',
        'other' => 'Lainnya'
    ];
    
    if ($request->ajax()) {
        return response()->json([
            'html' => view('partials.orders-list', compact('allOrders', 'unpaidOrders', 'search', 'cancellationReasons'))->render(),
            'pagination' => view('partials.pagination', compact('allOrders'))->render(),
        ]);
    }
    
    return view('ecom.list_order_payment', compact(
        'allOrders', 
        'unpaidOrders', 
        'search', 
        'statusFilter',
        'cancellationReasons'  // Perhatikan "s" di akhir sekarang!
    ));
}
    public function cancel(Request $request, $id)
{
    // Cari order berdasarkan id
    $order = Order::findOrFail($id);

    // Pastikan order milik user yang sedang login
    if ($order->user_id !== auth()->id()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }
        abort(403, 'Unauthorized action.');
    }

    // Cek apakah order dalam status pending sehingga dapat dibatalkan
    if ($order->status !== 'pending') {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Order tidak dapat dibatalkan.']);
        }
        return redirect()->back()->with('error', 'Order tidak dapat dibatalkan.');
    }

    // Update status order menjadi cancelled
    $order->update(['status' => 'cancelled']);

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Order berhasil dibatalkan.']);
    }
    return redirect()->route('ecom.list_order_payment')->with('success', 'Order berhasil dibatalkan.');
}

public function listOrderPayment(Request $request)
{
    $user = auth()->user();
    $search = $request->input('search');
    
    // Query builder for orders
    $ordersQuery = Order::where('user_id', $user->id)
                        ->with(['items.product', 'items.variant', 'items.package', 'deliveryHistory'])
                        ->orderBy('created_at', 'desc');
    
    // Apply search if provided
    if ($search) {
        $ordersQuery->where(function($query) use ($search) {
            $query->where('id', 'like', "%{$search}%") // Ubah ke order_number
                ->orWhereHas('items.product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }
    
    // Get paginated results
    $allOrders = $ordersQuery->paginate(10);
    
    // Get unpaid orders count for notification
    $unpaidOrders = Order::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->get();
    
    return view('ecom.list_order_payment', compact('allOrders', 'unpaidOrders', 'search' ));
}

public function addComment(Request $request, $productId)
{
    $request->validate([
        'content' => 'required',
        'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'video'   => 'nullable|mimes:mp4,mov,avi|max:10000',
    ]);

    $comment = new Comment();
    $comment->product_id = $productId;
    $comment->user_id    = auth()->id();
    $comment->content    = $request->content;

    if ($request->hasFile('photo')) {
       $photoPath = $request->file('photo')->store('comments/photos', 'public');
       $comment->photo = $photoPath;
    }

    if ($request->hasFile('video')) {
       $videoPath = $request->file('video')->store('comments/videos', 'public');
       $comment->video = $videoPath;
    }

    $comment->save();

    // Misal: Notifikasi ke seller agar dapat melihat komentar yang masuk.
    $seller = $comment->product->store->seller; // Pastikan relasi sudah didefinisikan di model
    // Contoh notifikasi (gunakan Notification Laravel atau metode lain)
    // $seller->notify(new NewCommentNotification($comment));

    return redirect()->back()->with('success', 'Comment added successfully.');
}

}