<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductPackage;
use App\Models\ProductVariant;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
   private $imageSizes = [
       'thumbnail' => [150, 150],
       'galeri' => [400, 400],
       'original' => [800, 800]
   ];
public function index(Request $request)
{
    try {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();
        $query = Product::query()
            ->with(['productImages' => function($query) {
                $query->orderBy('urutan', 'asc');
            }, 'category'])
            ->where('user_id', $userId);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Handle category filter
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')
                         ->paginate(8)
                         ->withQueryString();

        $categories = Category::all();

        return view('dashboard.list_sale', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->category ?? 'all',
            'searchTerm' => $request->search ?? ''
        ]);

    } catch (\Exception $e) {
        \Log::error('Product index error: ' . $e->getMessage());
        return redirect()
            ->route('dashboard.list_sale')
            ->with('error', 'Unable to load products: ' . $e->getMessage());
    }
}
public function create()
{
    $categories = Category::pluck('name', 'id');
    return view('dashboard.sell_product', compact('categories'));
}
public function store(Request $request)
{
    // Get the user's store ID
    $userStore = \App\Models\Store::where('user_id', auth()->id())->first();
    
    if (!$userStore) {
        return back()->with('error', 'You need to create a store first')->withInput();
    }

    // Convert valid categories array to string for validation rule
    $validCategoryIds = Category::pluck('id')->toArray();
    
        
        // Validate request
        $validated = $request->validate([
            'name' => 'required|max:255',
            'sku' => 'required|unique:products',
            'description' => 'required',    
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'required|integer|min:0',
            'category_id' => 'required|numeric|in:' . implode(',', $validCategoryIds),
            'images' => 'required|array|min:1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'variants' => 'nullable|array',
            'variants.*' => 'nullable|string|max:255',
            'variant_prices' => 'nullable|array',
            'variant_prices.*' => 'nullable|numeric|min:0',
            'packages' => 'nullable|array',
            'packages.*' => 'nullable|string|max:255',
            'package_prices' => 'nullable|array',
            'package_prices.*' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Product name is required',
            'name.max' => 'Product name cannot exceed 255 characters',  
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU has already been taken',
            'category_id.required' => 'Product category is required',
            'category_id.in' => 'Selected category is invalid',
            'description.required' => 'Product description is required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',
            'discount_price.numeric' => 'Discount price must be a number',
            'discount_price.min' => 'Discount price cannot be negative',
            'stock.required' => 'Stock quantity is required',
            'stock.integer' => 'Stock must be a whole number',
            'stock.min' => 'Stock cannot be negative',
            'stock_alert.required' => 'Low stock alert is required',
            'stock_alert.integer' => 'Low stock alert must be a whole number',
            'stock_alert.min' => 'Low stock alert cannot be negative',
            'images.required' => 'At least one product image is required',
            'images.array' => 'Invalid image format',
            'images.min' => 'At least one product image is required',
            'images.*.required' => 'Each uploaded file must be an image',
            'images.*.image' => 'File must be an image',
            'images.*.mimes' => 'Image must be a JPEG, PNG, or JPG',
            'images.*.max' => 'Image size cannot exceed 2MB'
        ]);

        DB::beginTransaction();
    try {
        // Create product with store_id
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sku' => $validated['sku'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock' => $validated['stock'],
            'stock_awal' => $validated['stock'],
            'stock_alert' => $validated['stock_alert'],
            'category_id' => $validated['category_id'],
            'is_active' => true,
            'user_id' => auth()->id(),
            'store_id' => $userStore->id, // Add this line to set the store_id
        ]);

            
            // Handle variants
            if ($request->has('variants')) {
                $variants = array_filter($request->variants, function($variant) {
                    return !empty(trim($variant));
                });
                
                foreach ($variants as $key => $variantName) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variantName,
                        'price' => $request->variant_prices[$key] ?? 0
                    ]);
                }
            }
            
            if ($request->has('packages')) {
                $packages = array_filter($request->packages, function($package) {
                    return !empty(trim($package));
                });
                
                foreach ($packages as $key => $packageName) {
                    ProductPackage::create([
                        'product_id' => $product->id,
                        'name' => $packageName,
                        'price' => $request->package_prices[$key] ?? 0
                    ]);
                }
            }
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                $lastOrder = $product->productImages()->max('urutan') ?? 0;
            
                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    
                    // Store original image
                    $path = $image->storeAs('produk/original', $filename, 'public');
                    
                    // Create database record
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path_gambar' => 'produk/original/' . $filename,
                        'gambar_utama' => $index === 0,
                        'urutan' => $index + 1
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('dashboard.list_sale')
                           ->with('success', 'Product created successfully');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation error: ' . $e->getMessage());
            return back()->with('error', 'Error creating product: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function edit(Product $product)
{
    if ($product->user_id !== auth()->id()) {
        abort(403);
    }

    $product->load(['productImages', 'packages']);
    $categories = Category::pluck('name', 'id');

    return view('dashboard.edit_product', [
        'product' => $product,
        'categories' => $categories
    ]);
}

public function update(Request $request, Product $product)
{
    if ($product->user_id !== auth()->id()) {
        abort(403);
    }

    $validCategoryIds = Category::pluck('id')->toArray();
    
    $validated = $request->validate([
        'name' => 'required|max:255',
        'sku' => 'required|unique:products,sku,'.$product->id,
        'description' => 'required',    
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'stock_alert' => 'required|integer|min:0',
        'new_images' => 'nullable|array',
        'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'variants' => 'nullable|array',
        'variants.*' => 'nullable|string|max:255',
        'variant_prices' => 'nullable|array',
        'variant_prices.*' => 'nullable|numeric|min:0',
        'packages' => 'nullable|array',
        'packages.*' => 'nullable|string|max:255',
        'package_prices' => 'nullable|array',
        'package_prices.*' => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sku' => $validated['sku'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock' => $validated['stock'],
            'stock_alert' => $validated['stock_alert'],
        ]);
        

        // Update variants
        $product->variants()->delete();
        if ($request->has('variants')) {
            $variants = array_filter($request->variants, function($variant) {
                return !empty(trim($variant));
            });
            
            foreach ($variants as $key => $variantName) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantName,
                    'price' => $request->variant_prices[$key] ?? 0
                ]);
            }
        }
        // Update packages
        $product->packages()->delete();
        if ($request->has('packages')) {
            $packages = array_filter($request->packages, function($package) {
                return !empty(trim($package));
            });
            
            foreach ($packages as $key => $packageName) {
                ProductPackage::create([
                    'product_id' => $product->id,
                    'name' => $packageName,
                    'price' => $request->package_prices[$key] ?? 0
                ]);
            }
        }
        // Handle new images
        if ($request->hasFile('new_images')) {
            $lastOrder = $product->productImages()->max('urutan') ?? 0;
        
            foreach ($request->file('new_images') as $index => $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('produk/original', $filename, 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'path_gambar' => 'produk/original/' . $filename,
                    'gambar_utama' => $product->productImages()->count() === 0,
                    'urutan' => $lastOrder + $index + 1
                ]);
            }
        }
        
        DB::commit();
        return redirect()->route('dashboard.list_sale')
                       ->with('success', 'Product updated successfully');
                        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Product update error: ' . $e->getMessage());
        return back()->with('error', 'Error updating product: ' . $e->getMessage())
                    ->withInput();
    }
}
public function destroy(Product $product)
{
    if ($product->user_id !== auth()->id()) {
        abort(403);
    }

    DB::beginTransaction();

    try {
        // Hapus order_items yang terkait dengan product ini
        DB::table('order_items')->where('product_id', $product->id)->delete();
        
        // Hapus order_items yang terkait dengan variants product ini
        $variantIds = $product->variants()->pluck('id')->toArray();
        if (!empty($variantIds)) {
            DB::table('order_items')->whereIn('variant_id', $variantIds)->delete();
        }
        
        // Hapus gambar produk
        foreach ($product->productImages as $image) {
            $this->deleteProductImages($image->path_gambar);
            $image->delete();
        }

        // Hapus variants, packages, dan comments
        $product->variants()->delete();
        $product->packages()->delete();
        $product->comments()->delete();

        // Terakhir hapus produknya
        $product->delete();

        DB::commit();
        return redirect()->route('dashboard.list_sale')
                      ->with('success', 'Product deleted successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Product deletion error: ' . $e->getMessage());
        return back()->with('error', 'Error deleting product: ' . $e->getMessage());
    }
}


private function deleteProductImages($path)
{
    if (!$path) return;
    
    $filename = basename($path);
    
    // Hapus file dari semua ukuran
    foreach (array_keys($this->imageSizes) as $type) {
        $filePath = "produk/{$type}/{$filename}";
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
    
    // Hapus file original juga kalau ada
    $originalPath = "produk/original/{$filename}";
    if (Storage::disk('public')->exists($originalPath)) {
        Storage::disk('public')->delete($originalPath);
    }
}
   public function deleteImage(ProductImage $image)
    {
        // Check if the associated product exists
        if (!$image->produk) {
            // Optionally delete the orphaned image or abort
            try {
                $this->deleteProductImages($image->path_gambar);
                $image->delete();
                return back()->with('success', 'Image deleted successfully');
            } catch (\Exception $e) {
                return back()->with('error', 'Error deleting image: ' . $e->getMessage());
            }
        }

        // Proceed with ownership check
        if ($image->produk->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $this->deleteProductImages($image->path_gambar);
            $image->delete();
            return back()->with('success', 'Image deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }
}