@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Product</h2>
                <a href="{{ route('dashboard.list_sale') }}" class="text-indigo-600 hover:text-indigo-800">
                    &larr; Back to Products
                </a>
            </div>

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="current-images mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($product->productImages as $image)
        <div class="relative group aspect-[4/3]">
            <img src="{{ Storage::url($image->path_gambar) }}" 
                alt="Product image" 
                class="w-full h-full object-cover rounded-lg shadow-sm">
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <form action="{{ route('dashboard.delete_product_image', $image->id) }}" 
                      method="POST" 
                      class="inline delete-image-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-gray-700 hover:bg-red-500 hover:text-white transition-all">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-4 text-gray-500">
            No images uploaded yet
        </div>
        @endforelse
    </div>
</div>
            <form id="editProductForm" action="{{ route('dashboard.update_product', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" name="name" id="name" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" name="sku" id="sku" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            value="{{ old('sku', $product->sku) }}" required>
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Discount Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="price" id="price" step="0.01" 
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('price', $product->price) }}" required>
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_price" class="block text-sm font-medium text-gray-700">Discount Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="discount_price" id="discount_price" step="0.01" 
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('discount_price', $product->discount_price) }}">
                            </div>
                            @error('discount_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock and Stock Alert -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                            <input type="number" name="stock" id="stock" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value="{{ old('stock', $product->stock) }}" required>
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock_alert" class="block text-sm font-medium text-gray-700">Stock Alert Level</label>
                            <input type="number" name="stock_alert" id="stock_alert" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value="{{ old('stock_alert', $product->stock_alert) }}" required>
                            @error('stock_alert')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700">Variants Options</label>
                        <div id="variantsContainer" class="space-y-2">
                            @foreach($product->variants as $index => $variant)
                            <div class="flex items-center space-x-2 variant-item">
                                <div class="flex-1 p-3 bg-white border rounded-lg flex justify-between items-center shadow-sm">
                                    <div class="flex-1 flex gap-2">
                                        <input type="text" name="variants[]" value="{{ $variant->name }}" 
                                            class="bg-transparent border-none focus:ring-0 w-full text-gray-700"
                                            placeholder="Enter variant name">
                                        <div class="relative shadow-sm rounded-md w-1/3">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="variant_prices[]" value="{{ $variant->price }}" 
                                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Price" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <button type="button" class="text-gray-400 hover:text-red-500 delete-variant transition-colors ml-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="addVariant" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Add Variant Option
                        </button>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700">Package Options</label>
                        <div id="packagesContainer" class="space-y-2">
                            @foreach($product->packages as $index => $package)
                            <div class="flex items-center space-x-2 package-item">
                                <div class="flex-1 p-3 bg-white border rounded-lg flex justify-between items-center shadow-sm">
                                    <div class="flex-1 flex gap-2">
                                        <input type="text" name="packages[]" value="{{ $package->name }}" 
                                            class="bg-transparent border-none focus:ring-0 w-full text-gray-700"
                                            placeholder="Enter package name">
                                        <div class="relative shadow-sm rounded-md w-1/3">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="package_prices[]" value="{{ $package->price }}" 
                                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Price" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <button type="button" class="text-gray-400 hover:text-red-500 delete-package transition-colors ml-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="addPackage" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Add Package Option
                        </button>
                    </div>
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload new images</span>
                                        <input id="images" name="new_images[]" type="file" class="sr-only" multiple accept="image/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                        <div id="imagePreviewContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>
                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('dashboard.list_sale') }}" 
                           class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Package Options Functionality
    document.getElementById('addVariant').addEventListener('click', function() {
        const container = document.getElementById('variantsContainer');
        const newVariant = document.createElement('div');
        newVariant.className = 'flex items-center space-x-2 variant-item';
        newVariant.innerHTML = `
            <div class="flex-1 p-3 bg-white border rounded-lg flex justify-between items-center shadow-sm">
                <div class="flex-1 flex gap-2">
                    <input type="text" name="variants[]" class="bg-transparent border-none focus:ring-0 w-full text-gray-700" placeholder="Enter variant name">
                    <div class="relative shadow-sm rounded-md w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="variant_prices[]" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Price" step="0.01" min="0">
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 delete-variant transition-colors ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(newVariant);
    });

    document.getElementById('addPackage').addEventListener('click', function() {
        const container = document.getElementById('packagesContainer');
        const newPackage = document.createElement('div');
        newPackage.className = 'flex items-center space-x-2 package-item';
        newPackage.innerHTML = `
            <div class="flex-1 p-3 bg-white border rounded-lg flex justify-between items-center shadow-sm">
                <div class="flex-1 flex gap-2">
                    <input type="text" name="packages[]" class="bg-transparent border-none focus:ring-0 w-full text-gray-700" placeholder="Enter package name">
                    <div class="relative shadow-sm rounded-md w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="package_prices[]" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Price" step="0.01" min="0">
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 delete-package transition-colors ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(newPackage);
    });
    // Delete variant functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-variant')) {
            e.target.closest('.variant-item').remove();
        }
    });

    // Delete package functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-package')) {
            e.target.closest('.package-item').remove();
        }
    });

    // Image Preview Functionality
    document.getElementById('images').addEventListener('change', function(event) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        
        for (const file of event.target.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative group aspect-[4/3]';
                preview.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg shadow-sm">
                    <button type="button" class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-gray-700 hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 remove-preview">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                container.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove preview image
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-preview')) {
            e.target.closest('.relative').remove();
        }
    });

    // Form submission with SweetAlert
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    const form = this;
    
    Swal.fire({
        title: 'Update Product',
        text: 'Are you sure you want to update this product?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#d1d5db',
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form programmatically without triggering the event listener again
            form.removeEventListener('submit', arguments.callee);
            form.submit();
        }
    });
});
</script>
@endsection