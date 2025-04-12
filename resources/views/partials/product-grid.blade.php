@if($products->isEmpty())
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <p class="text-gray-600 text-lg font-medium mb-2">No products found</p>
        <p class="text-gray-500 mb-4">Try adjusting your search or filter criteria</p>
        <a href="{{ route('store.products', ['storeId' => $store->id]) }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            View All Products
        </a>
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
                            class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-1 disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ $product->stock > 0 ? 'Add' : 'Out of Stock' }}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif