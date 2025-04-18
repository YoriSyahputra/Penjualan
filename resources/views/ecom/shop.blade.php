@extends('layouts.depan')

@section('content')
<!-- Shop Header/Hero Section -->
<section class="bg-gradient-to-r from-indigo-500 to-purple-600 py-12">
    <div class="container mx-auto px-4">
<div class="text-center text-white">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">
    @if($selectedCategory && $selectedCategory instanceof \App\Models\Category)
        {{ $selectedCategory->name }}
    @else
        All Products
    @endif
    </h1>
</div>
    </div>
</section>

@if(isset($store) && $store)
    @include('partials.store-profile', ['store' => $store])
@else
    <div class="bg-yellow-100 p-4 text-yellow-800">
        Debug: Store tidak tersedia. 
        @if(isset($store))
            Variabel store ada tetapi nilainya kosong
        @else
            Variabel store tidak didefinisikan
        @endif
    </div>
@endif

<!-- Main Content -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300">
                <a href="{{ route('product.show', ['id' => $product->id]) }}">
                    <!-- Product Image -->
                    <div class="relative aspect-square overflow-hidden">
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
                            <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    </div>

            <!-- Product Info -->
            <div class="p-2">
                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
        </a>
                
                <!-- Price -->
                <div class="flex flex-col mt-1">
                    @if($product->discount_price)
                        <div class="flex items-center">
                            <span class="text-gray-500 line-through text-xs">Rp.{{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-green-600 text-xs font-semibold ml-1">
                                {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                            </span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">Rp.{{ number_format($product->discount_price, 0, ',', '.') }}</span>
                    @else
                        <span class="text-sm font-bold text-gray-900">Rp.{{ number_format($product->price, 0, ',', '.') }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="flex items-center gap-1 my-1">
                    @if($product->stock > 0)
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        <span class="text-xs text-green-600">Stock: {{ $product->stock }}</span>
                    @else
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        <span class="text-xs text-red-600">Out of Stock</span>
                    @endif
                </div>

                <!-- Add to Cart Button -->
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
                    <a href="{{ $products->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-3 bg-white hover:bg-gray-50 text-indigo-600 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center px-4 py-3 bg-gray-100 text-gray-400 text-sm font-medium">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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

<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300">
    Item added to cart successfully!
</div>

@push('scripts')
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
</script>
@endpush
@endsection