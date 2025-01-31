@extends('layouts.depan')

@section('content')
@include('partials.cart-modal')
<!-- Shop Header/Hero Section -->
<section class="bg-gradient-to-r from-indigo-500 to-purple-600 py-12">
    <div class="container mx-auto px-4">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Our Products</h1>
            <p class="text-lg mb-8">Discover amazing products at great prices</p>
            
            <!-- Filter & Search Form -->
            <form action="#" method="GET" class="flex flex-col md:flex-row justify-center items-center gap-4 max-w-2xl mx-auto">
                <div class="relative flex-1 w-full">
                    <input type="text" 
                           name="search"
                           value="{{ $searchTerm }}"
                           placeholder="Search products..." 
                           class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
                
                <select name="category" 
                        class="px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sort" 
                        class="px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price-low" {{ $sortBy == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price-high" {{ $sortBy == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="popularity" {{ $sortBy == 'popularity' ? 'selected' : '' }}>Most Popular</option>
                </select>

                <button type="submit" 
                        class="px-6 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors">
                    Apply Filters
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Shop Controls -->


        <!-- Product Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($products as $product)
    <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300">
        <!-- Product Image -->
        <div class="relative overflow-hidden" style="height: 280px;"> 
            @if($product->productImages->isNotEmpty())
                <img src="{{ asset('storage/' . $product->productImages->first()->path_gambar) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
            @else
                <img src="/api/placeholder/300/300" 
                     alt="No image"
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
            @endif
            
            <!-- Category Badge -->
            <div class="absolute top-2 right-2">
                <span class="bg-black bg-opacity-50 text-white text-xs font-medium px-2.5 py-1 rounded">
                    {{ $product->category->name }}
                </span>
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
            </div>
            
            <!-- Price -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    @if($product->discount_price)
                        <span class="text-gray-500 line-through text-sm">Rp.{{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="text-lg font-bold text-gray-900 ml-2">Rp.{{ number_format($product->discount_price, 0, ',', '.') }}</span>
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded ml-2">
                            {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                        </span>
                    @else
                        <span class="text-lg font-bold text-gray-900">Rp.{{ number_format($product->price, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>

            <!-- Stock Status -->
            @if($product->stock > 0)
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-sm text-green-600">In Stock ({{ $product->stock }})</span>
                </div>
            @else
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span class="text-sm text-red-600">Out of Stock</span>
                </div>
            @endif

            <!-- Add to Cart Button -->
            <button 
                onclick="openCartModal({{ $product->id }})"
                @if($product->stock <= 0) disabled @endif
                class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ $product->stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
            </button>
        </div>
    </div>
    @endforeach
</div>

        <!-- Empty State -->
        @if($products->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
        </div>
        @endif

        <!-- Pagination -->
        @if ($products->hasPages())
    <div class="mt-8">
        <div class="flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{-- Previous Page Link --}}
                @if ($products->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                @php
                    $start = max(1, $products->currentPage() - 3);
                    $end = min($start + 7, $products->lastPage());
                @endphp

                @if($start > 1)
                    <a href="{{ $products->url(1) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                    @if($start > 2)
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $products->currentPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600">{{ $i }}</span>
                    @else
                        <a href="{{ $products->url($i) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $i }}</a>
                    @endif
                @endfor

                @if($end < $products->lastPage())
                    @if($end < $products->lastPage() - 1)
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                    @endif
                    <a href="{{ $products->url($products->lastPage()) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $products->lastPage() }}</a>
                @endif

                {{-- Next Page Link --}}
                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
@include('partials.cart-modal')

    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300">
    Item added to cart successfully!
</div>

@push('scripts')
<script>
window.showToast = function(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.remove('translate-y-full', 'opacity-0');
    
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
    }, 3000);
}


function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update cart count
        const cartCount = document.getElementById('cartCount');
        cartCount.textContent = parseInt(cartCount.textContent) + 1;
        
        // Show success message
        showToast(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product to cart');
    });
}
</script>
@endpush
@endsection