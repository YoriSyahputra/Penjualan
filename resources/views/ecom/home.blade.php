@extends('layouts.depan')

@section('content')
@include('partials.cart-modal')

    <section class="pt-24 bg-gradient-to-r from-indigo-500 to-purple-600">
        <div class="container mx-auto px-4 py-16">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-white mb-8 md:mb-0">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Discover Amazing Products</h2>
                    <p class="text-lg mb-6">Find the best deals on trending items.</p>
                    <a href="/shop" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-indigo-100 transition-colors">
                        Shop Now
                    </a>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-8 md:py-12 lg:py-16 bg-gray-50 relative">
    <div class="container mx-auto px-4">
        <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-center mb-6 md:mb-8">Featured Categories</h3>

        <!-- Scroll Buttons -->
        <button onclick="scrollCategories('left')" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 focus:outline-none hidden md:block z-10" id="scrollLeftBtn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        
        <button onclick="scrollCategories('right')" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 focus:outline-none hidden md:block z-10" id="scrollRightBtn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Scrollable Container -->
        <div class="overflow-x-auto hide-scrollbar" id="categoriesContainer">
            <div class="flex space-x-4 md:space-x-6 py-2">
                <!-- Pakaian & Aksesoris -->
                <a href="/category/fashion" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/baju.png') }}" alt="Fashion Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Pakaian & Aksesoris</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Koleksi lengkap fashion</p>
                    </div>
                </a>

                <!-- Rumah Tangga -->
                <a href="/category/household" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/household.png') }}" alt="Household Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Rumah Tangga</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Perlengkapan rumah tangga</p>
                    </div>
                </a>

                <!-- Dekorasi -->
                <a href="/category/decoration" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/decoration.png') }}" alt="Decoration Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Dekorasi</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Hiasan rumah & dekorasi</p>
                    </div>
                </a>

                <!-- Kamar Mandi -->
                <a href="/category/bathroom" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/bathroom.png') }}" alt="Bathroom Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Kamar Mandi</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Peralatan kamar mandi</p>
                    </div>
                </a>

                <!-- Kebutuhan Rumah -->
                <a href="/category/home-needs" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/home-needs.png') }}" alt="Home Needs Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Kebutuhan Rumah</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Kebutuhan rumah sehari-hari</p>
                    </div>
                </a>

                <!-- Tempat Penyimpanan -->
                <a href="/category/storage" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/storage.png') }}" alt="Storage Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Tempat Penyimpanan</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Solusi penyimpanan</p>
                    </div>
                </a>

                <!-- Elektronik -->
                <a href="/category/electronics" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/electronics.png') }}" alt="Electronics Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Elektronik</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Peralatan elektronik</p>
                    </div>
                </a>

                <!-- Action Figure -->
                <a href="/category/action-figures" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/action-figures.png') }}" alt="Action Figures Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Action Figure</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Koleksi action figure</p>
                    </div>
                </a>

                <!-- Alat Olahraga -->
                <a href="/category/sports" class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
                    <img src="{{ asset('storage/componen/sports.png') }}" alt="Sports Category" class="w-full h-40 object-cover">
                    <div class="p-3 md:p-4">
                        <h4 class="font-semibold text-base md:text-lg mb-1">Alat Olahraga</h4>
                        <p class="text-gray-600 text-xs md:text-sm">Peralatan olahraga</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Featured Products -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h3 class="text-2xl font-bold text-center mb-8">Featured Products</h3>
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
    </div>
</section>

<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300">
    Item added to cart successfully!
</div>


    <style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>

    <script>
// Cart functionality
function redirectToLogin() {
    window.location.href = '/login';
}

// Toast notification functionality
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

// Categories scroll functionality
function scrollCategories(direction) {
    const container = document.getElementById('categoriesContainer');
    const scrollAmount = direction === 'left' ? -container.offsetWidth : container.offsetWidth;
    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

// Update scroll buttons visibility
document.getElementById('categoriesContainer').addEventListener('scroll', function() {
    const container = this;
    const leftBtn = document.getElementById('scrollLeftBtn');
    const rightBtn = document.getElementById('scrollRightBtn');

    if (container.scrollLeft > 0) {
        leftBtn.classList.remove('hidden');
    } else {
        leftBtn.classList.add('hidden');
    }

    if (container.scrollLeft + container.offsetWidth >= container.scrollWidth) {
        rightBtn.classList.add('hidden');
    } else {
        rightBtn.classList.remove('hidden');
    }
});
</script>

@endsection
