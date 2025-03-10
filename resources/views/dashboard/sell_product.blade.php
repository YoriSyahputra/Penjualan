@extends('layouts.admin')

@section('content')
<div class="py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Sell New Product</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.store_product') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               required>
                    </div>
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               required>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Product Category</label>
                        <select name="category_id" id="category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Dynamic Variant Options with Price -->
                    <div class="border p-4 rounded-lg">
                        <h3 class="font-medium mb-2">Product Variants</h3>
                        <div id="variants-container">
                            <div class="variant-group mb-3">
                                <div class="flex gap-2">
                                    <div class="w-3/5">
                                        <input type="text" name="variants[]" 
                                            class="w-full rounded-lg border-gray-300 mb-2" 
                                            placeholder="Enter variant (e.g. Color, Size, Storage)">
                                    </div>
                                    <div class="w-2/5 relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp.</span>
                                        <input type="number" name="variant_prices[]" 
                                            class="w-full pl-7 rounded-lg border-gray-300 mb-2" 
                                            placeholder="Price" step="0.01" min="0">
                                    </div>
                                    <button type="button" class="remove-variant px-2 py-1 text-red-600 hover:text-red-800">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-variant" class="mt-2 text-sm bg-indigo-50 text-indigo-600 px-3 py-1 rounded hover:bg-indigo-100">
                            + Add Variant
                        </button>
                    </div>
                    <div class="border p-4 rounded-lg">
                        <h3 class="font-medium mb-2">Package Options</h3>
                        <div id="packages-container">
                            <div class="package-group mb-3">
                                <div class="flex gap-2">
                                    <div class="w-3/5">
                                        <input type="text" name="packages[]" 
                                            class="w-full rounded-lg border-gray-300 mb-2" 
                                            placeholder="Enter package name">
                                    </div>
                                    <div class="w-2/5 relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp.</span>
                                        <input type="number" name="package_prices[]" 
                                            class="w-full pl-7 rounded-lg border-gray-300 mb-2" 
                                            placeholder="Price" step="0.01" min="0">
                                    </div>
                                    <button type="button" class="remove-package px-2 py-1 text-red-600 hover:text-red-800">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-package" class="mt-2 text-sm bg-indigo-50 text-indigo-600 px-3 py-1 rounded hover:bg-indigo-100">
                            + Add Package
                        </button>
                    </div>

                    <!-- Pricing -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Regular Price</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp.</span>
                                <input type="number" name="price" id="price" value="{{ old('price') }}" 
                                       class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div>
                            <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Discount Price (Optional)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp.</span>
                                <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price') }}" 
                                       class="w-full pl-7 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                       step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Inventory -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock') }}" 
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   min="0" required>
                        </div>

                        <div>
                            <label for="stock_alert" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert</label>
                            <input type="number" name="stock_alert" id="stock_alert" value="{{ old('stock_alert') }}" 
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   min="0" required>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                        <span>Upload files</span>
                                        <input id="images" name="images[]" type="file" class="hidden" multiple accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                        <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Create Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <script src="{{ asset('js/productVariants.js') }}"></script>
    <script src="{{ asset('js/packageOptions.js') }}"></script>
    <script src="{{ asset('js/imagePreview.js') }}"></script>
@endsection