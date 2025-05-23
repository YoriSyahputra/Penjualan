<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="user-balance" content="{{ auth()->check() ? auth()->user()->wallet->balance : 0 }}">
    <meta name="user-has-pin" content="{{ auth()->check() && auth()->user()->hasPin() ? 'true' : 'false' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Ludwig</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.js'])
    @php
    use App\Models\Cart;
    @endphp
    @include('partials.cart-modal')
</head>
<body class="antialiased">
    <header class="fixed w-full bg-white shadow-md z-50">
    <nav class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="relative" x-data="{ isOpen: false, categoryOpen: false }">
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-2xl font-bold text-indigo-600">LudWig</a>
                    <!-- Mobile menu button -->
                    <button @click="isOpen = !isOpen" 
                            class="text-gray-600 hover:text-indigo-600 transition-colors focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" 
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
                <div x-show="isOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    @click.away="isOpen = false"
                    class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">

                    <a href="/" class="block px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Home</a>
                    <a href="/shop" class="block px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Shop</a>
                    <!-- Categories Dropdown Trigger -->
                    <div class="relative" x-data="{ categoryOpen: false }">
                        <button @click="categoryOpen = !categoryOpen"
                                class="w-full text-left px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center justify-between">
                            Categories
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        
                        <!-- Categories Mega Menu -->
                        <div x-show="categoryOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-x-2"
                             x-transition:enter-end="opacity-100 transform translate-x-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-x-0"
                             x-transition:leave-end="opacity-0 transform -translate-x-2"
                             @click.away="categoryOpen = false"
                             class="absolute left-full top-0 w-64 bg-white rounded-md shadow-lg py-2 -mt-2 ml-0.5">
                            
                            <!-- Category Items -->
                            <div class="grid grid-cols-1 gap-2">
                                <a href="{{ route('shop.index') }}" 
                                class="block px-4 py-2 {{ !request('category') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600' }} hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    All Categories
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('shop.index', ['category' => $category->id]) }}" 
                                    class="block px-4 py-2 {{ request('category') == $category->id ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600' }} hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>      
            </div>

            <!-- Desktop Search Bar -->
            <div class="hidden md:block flex-1 max-w-xl mx-4">
                <form action="{{ route('shop.index') }}" method="GET" class="relative">
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search products..."
                                class="w-full px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <select name="category" 
                                class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500 text-sm">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" 
                                class="px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500 text-sm">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Mobile Search Icon and Popup -->
            <div class="md:hidden" x-data="{ isSearchOpen: false }">
                <button @click="isSearchOpen = true" class="text-gray-600 hover:text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                <!-- Mobile Search Popup -->
                <div x-show="isSearchOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    @click.away="isSearchOpen = false"
                    class="absolute left-0 right-0 top-full mt-2 mx-4 bg-white rounded-lg shadow-lg p-4">
                    <form action="{{ route('shop.index') }}" method="GET" class="space-y-3">
                        <div class="relative">
                            <input type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search products..."
                                class="w-full px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <select name="category" 
                                class="w-full px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" 
                                class="w-full px-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:border-indigo-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                        <button type="submit" 
                        class="px-6 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>
                <div class="flex items-center space-x-4">
                <a href="{{ route('ecom.list_order_payment') }}" 
                    class="bg-indigo-600 text-white p-3 rounded-full hover:bg-indigo-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-8.268-2.943" />
                    </svg>
                    </a>

                @auth
                    <div class="relative">
                        @auth
                            <a href="{{ route('cart.index') }}" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span id="cartCount">
                                {{ Cart::where('user_id', auth()->id())
                                    ->select('product_id')
                                    ->distinct()
                                    ->count('product_id') }}
                            </span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="ml-1">0</span>
                            </a>
                        @endauth
                    </div>
                    <div class="relative" x-data="{ isProfileOpen: false }">
                        <button @click="isProfileOpen = !isProfileOpen" 
                                class="flex items-center space-x-2 focus:outline-none">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" 
                                    alt="Profile photo" 
                                    class="h-8 w-8 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="isProfileOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            @click.away="isProfileOpen = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                            
                            @if(auth()->user()->is_super_admin)
                                <a href="{{ route('super-admin.dashboard') }}"
                                class="block px-4 py-2 text-red-600 hover:bg-red-50 transition-colors font-medium">
                                    Super Admin 
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @elseif(auth()->user()->is_admin)
                                <a href="{{ route('dashboard.index') }}"
                                class="block px-4 py-2 text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    Admin 
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @elseif(auth()->user()->is_driver)
                                <a href="{{ route('driver.dashboard') }}"
                                class="block px-4 py-2 text-blue-600 hover:bg-blue-50 transition-colors">
                                    Driver 
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @endif
                            
                            <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                Profile
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Register</a>
                    @endif

                    <!-- Guest Cart -->
                    <div class="relative">
                        <a href="{{ route('cart.index') }}" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span id="cartCount" class="ml-1">{{ Cart::where('user_id', auth()->id())->count() }}</span>
                        </a>
                    </div>

                    <!-- Guest Profile Icon -->
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endauth
            </div>
            </div>
        </div>
    </nav>
</header>
    <!-- Featured Products -->
    <main class="min-h-screen pt-16">
        @yield('content')
    </main>
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
                        <li><a href="/" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="/shop" class="text-gray-400 hover:text-white transition-colors">Shop</a></li>
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
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162 6.162 0 0-2.76-6.162-6.162-6.162zm0 10.162c-2.21 0-4-1.79-4-4 0-2.21 1.79-4 4-4 2.21 0 4 1.79 4 4 0 2.21-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-gray-400">&copy; 2025 LudWig. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @vite('resources/js/app.js')
    @stack('scripts')
</body>
</html>