<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Modern E-commerce</title>
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
    <!-- Header/Navigation -->
    <header class="fixed w-full bg-white shadow-md z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-indigo-600">LudWig</h1>
                    <div class="hidden md:flex space-x-6">
                        <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors">Home</a>
                        <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors">Shop</a>
                        <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors">Categories</a>
                        <a href="#" class="text-gray-600 hover:text-indigo-600 transition-colors">About</a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="md:hidden text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                    @auth
                        @if (auth()->user()->is_admin)
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Register</a>
                        @endif
                    @endauth
                    <a href="#" class="text-gray-600 hover:text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 bg-gradient-to-r from-indigo-500 to-purple-600">
        <div class="container mx-auto px-4 py-16">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-white mb-8 md:mb-0">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 animate-fade-in">Discover Amazing Products</h2>
                    <p class="text-lg mb-6">Find the best deals on trending items.</p>
                    <a href="#" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-indigo-100 transition-colors">
                        Shop Now
                    </a>
                </div>
                <div class="md:w-1/2">
                    <img src="/api/placeholder/600/400" alt="Hero Image" class="rounded-lg shadow-xl animate-float">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-8 md:py-12 lg:py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Responsive heading with different sizes -->
        <h3 class="text-xl md:text-2xl lg:text-3xl font-bold text-center mb-6 md:mb-8">Featured Categories</h3>

        <!-- Grid with responsive columns and spacing -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Shirts Category -->
            <a href="/category/shirts" class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
            <img src="{{ asset('storage/componen/baju.png') }}" alt="Shirts Category" 
                    class="w-full h-40 sm:h-44 md:h-48 object-cover">
                <div class="p-3 md:p-4">
                    <h4 class="font-semibold text-base md:text-lg mb-1 md:mb-2">Shirts</h4>
                    <p class="text-gray-600 text-xs md:text-sm">Browse our shirt collection</p>
                </div>
            </a>
            <!-- Pants Category -->
            <a href="/category/pants" class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
            <img src="{{ asset('storage/componen/celana.png') }}" alt="Pants Category" 
                     class="w-full h-40 sm:h-44 md:h-48 object-cover">
                <div class="p-3 md:p-4">
                    <h4 class="font-semibold text-base md:text-lg mb-1 md:mb-2">Pants</h4>
                    <p class="text-gray-600 text-xs md:text-sm">Browse our pants collection</p>
                </div>
            </a>

            <!-- Shoes Category -->
            <a href="/category/shoes" class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
            <img src="{{ asset('storage/componen/sepatu.png') }}" alt="Shoes Category" 
                     class="w-full h-40 sm:h-44 md:h-48 object-cover">
                <div class="p-3 md:p-4">
                    <h4 class="font-semibold text-base md:text-lg mb-1 md:mb-2">Shoes</h4>
                    <p class="text-gray-600 text-xs md:text-sm">Browse our shoes collection</p>
                </div>
            </a>

            <!-- Accessories Category -->
            <a href="/category/accessories" class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform">
            <img src="{{ asset('storage/componen/aksessoris.png') }}" alt="Accessories Category" 
                     class="w-full h-40 sm:h-44 md:h-48 object-cover">
                <div class="p-3 md:p-4">
                    <h4 class="font-semibold text-base md:text-lg mb-1 md:mb-2">Accessories</h4>
                    <p class="text-gray-600 text-xs md:text-sm">Browse our accessories collection</p>
                </div>
            </a>
        </div>
    </div>
</section>

    <!-- Featured Products -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl font-bold text-center mb-8">Featured Products</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach(range(1, 8) as $index)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <img src="/api/placeholder/300/300" alt="Product" class="w-full h-64 object-cover">
                    <div class="p-4">
                        <h4 class="font-semibold text-lg mb-2">Product {{ $index }}</h4>
                        <p class="text-gray-600 mb-2">$99.99</p>
                        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition-colors">
                            Add to Cart
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-lg mb-4">About Us</h4>
                    <p class="text-gray-400">Your trusted source for quality products and excellent service.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Shop</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Returns</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Size Guide</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.