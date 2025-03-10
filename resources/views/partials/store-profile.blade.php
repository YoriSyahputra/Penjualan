<!-- resources/views/partials/store-profile.blade.php -->

<!-- Store Profile Section -->
<section class="bg-white shadow-md rounded-lg my-6 overflow-hidden container mx-auto px-4">
    <div class="flex flex-col md:flex-row">
        <!-- Store Logo and Banner -->
        <div class="w-full md:w-1/3 bg-gray-100 p-6 flex items-center justify-center">
            @if($store->logo)
                <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-lg">
            @else
                <div class="h-32 w-32 rounded-full bg-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                    {{ substr($store->name, 0, 1) }}
                </div>
            @endif
        </div>
        
        <!-- Store Details -->
        <div class="w-full md:w-2/3 p-6">
            <div class="flex items-center mb-3">
                <h2 class="text-2xl font-bold text-gray-800">{{ $store->name }}</h2>
                @if($store->status === 'official')
                <span class="ml-3 bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                    Official Store
                </span>
                @endif
            </div>
            
            <p class="text-gray-600 mb-4">{{ $store->description }}</p>
            
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <!-- Store Category -->
                @if($store->category)
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $store->category }}
                </div>
                @endif
                
                <!-- Store Address -->
                @if($store->address)
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $store->address }}
                </div>
                @endif
                
                <!-- Contact -->
                @if($store->phone_number)
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    {{ $store->phone_number }}
                </div>
                @endif
            </div>
            
            <!-- Store Stats -->
            <div class="mt-6 flex gap-6">
                <div class="text-center">
                <div class="text-xl font-bold text-indigo-600">
                        {{ \App\Models\Product::where('store_id', $store->id)->where('is_active', true)->count() }}
                    </div>
                    <div class="text-xs text-gray-500">Products</div>
                </div>
            </div>
            
            <!-- Store Products Button -->
            <div class="mt-6">
                <a href="{{ route('store.products', $store->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-8.268-2.943" />
                    </svg>
                    Lihat Product
                </a>
            </div>
        </div>
    </div>
</section>