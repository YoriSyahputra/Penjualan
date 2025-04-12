<!-- resources/views/store/products.blade.php -->
@extends('layouts.depan')

@section('content')
<div class="container mx-auto px-4 py-8" id="store-container">
    <!-- Store Profile Section -->
    @include('partials.store-profile', ['store' => $store, 'products' => $products])
    
    <!-- Search and Filter Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Find Products</h2>
        <form id="search-form" action="{{ route('store.products', ['storeId' => $store->id]) }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Products</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ $searchTerm }}" 
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                        placeholder="Search for products in this store...">
                    <button type="submit" class="absolute inset-y-0 right-0 px-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="md:w-1/4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategory && $selectedCategory->id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:w-1/4">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select id="sort" name="sort" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price-low" {{ $sortBy == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price-high" {{ $sortBy == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="popularity" {{ $sortBy == 'popularity' ? 'selected' : '' }}>Popularity</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="h-10 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200">
                    Filter
                </button>
                @if(!empty($searchTerm) || $selectedCategory)
                    <a href="{{ route('store.products', ['storeId' => $store->id]) }}" class="ml-2 h-10 px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Products Grid -->
    <div class="bg-white shadow-md rounded-lg p-6" id="products-container">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Store Products</h2>
            <p class="text-gray-500"><span id="product-count">{{ $products->total() }}</span> products found</p>
        </div>
        
        <div id="products-grid">
            @if($products->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-600 text-lg font-medium mb-2">No products found</p>
                    @if(!empty($searchTerm) || $selectedCategory)
                        <p class="text-gray-500 mb-4">Try adjusting your search or filter criteria</p>
                        <a href="{{ route('store.products', ['storeId' => $store->id]) }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            View All Products
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <a href="{{ route('product.show', $product->id) }}" class="block">
                                <div class="h-48 bg-gray-200 overflow-hidden">
                                    @if($product->productImages->isNotEmpty())
                                        <img src="{{ asset('storage/' . $product->productImages->first()->path_gambar) }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-lg font-semibold text-gray-800 line-clamp-1">{{ $product->name }}</h3>
                                </div>
                                
                                @if($product->category)
                                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">{{ $product->category->name }}</span>
                                @endif
                                
                                <div class="mt-3">
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                            <span class="ml-2 text-sm text-gray-500 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                                    <button 
                                        @auth
                                            onclick="openCartModal({{ $product->id }})"
                                        @else
                                            onclick="redirectToLogin()"
                                        @endauth
                                        @if($product->stock <= 0) disabled @endif
                                        class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ $product->stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <div class="mt-8" id="pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@include('partials.cart-modal')

<!-- Add a loading indicator -->
<div id="loading-indicator" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center space-x-3">
            <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-indigo-600 text-lg font-medium">Loading...</span>
        </div>
    </div>
</div>

<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300">
    Item added to cart successfully!
</div>

<script>
    function redirectToLogin() {
        window.location.href = '{{ route('login') }}';
    }

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
    // Add AJAX search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        const sortSelect = document.getElementById('sort');
        const productsContainer = document.getElementById('products-container');
        const loadingIndicator = document.getElementById('loading-indicator');
        
        // Function to fetch products via AJAX
        function fetchProducts() {
            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            
            // Build query string from form elements
            const formData = new FormData(searchForm);
            const queryString = new URLSearchParams(formData).toString();
            
            // Fetch the products using AJAX
            fetch(`{{ route('store.products.ajax', ['storeId' => $store->id]) }}?${queryString}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update the products grid
                document.getElementById('products-grid').innerHTML = data.html;
                
                // Update product count
                document.getElementById('product-count').textContent = data.total;
                
                // Update pagination
                document.getElementById('pagination-container').innerHTML = data.pagination;
                
                // Hide loading indicator
                loadingIndicator.classList.add('hidden');
                
                // Update browser URL without refreshing page
                const url = new URL(window.location);
                if (searchInput.value) url.searchParams.set('search', searchInput.value);
                else url.searchParams.delete('search');
                
                if (categorySelect.value) url.searchParams.set('category', categorySelect.value);
                else url.searchParams.delete('category');
                
                if (sortSelect.value) url.searchParams.set('sort', sortSelect.value);
                else url.searchParams.delete('sort');
                
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                loadingIndicator.classList.add('hidden');
                alert('Error loading products. Please try again.');
            });
        }
        
        // Handle form submission
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchProducts();
        });
        
        // Trigger search on input after a delay (for a smoother experience)
        let searchTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchProducts, 500); // 500ms delay
        });
        
        // Trigger search on category and sort changes
        categorySelect.addEventListener('change', fetchProducts);
        sortSelect.addEventListener('change', fetchProducts);
    });
</script>
@endsection