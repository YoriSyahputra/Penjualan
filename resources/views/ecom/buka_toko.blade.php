@extends('layouts.depan')

@section('content')
<div class="min-h-screen pt-16 bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Store Creation Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-lg p-6 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold text-white text-center">Open Your Store</h1>
                <p class="text-indigo-100 text-center mt-2">Start selling your products today</p>
            </div>

            <!-- Store Creation Form -->
            <div class="bg-white shadow-md rounded-b-lg p-6 md:p-8">
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('store.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Store Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Store Category</label>
                            <select name="category" id="category" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="">Select a category</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Food">Food & Beverages</option>
                                <option value="Health">Health & Beauty</option>
                                <option value="Home">Home & Living</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Store Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" 
                                   value="{{ old('phone_number') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Store Logo -->
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
                            <input type="file" name="logo" id="logo" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   accept="image/*">
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Store Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Store Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Store Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Store Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Open Store
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection