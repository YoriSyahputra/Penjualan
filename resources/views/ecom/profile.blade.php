@extends('layouts.depan')

@section('content')
<div class="min-h-screen pt-16 bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-lg p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                    <div class="relative group">
                        <img src="{{ auth()->user()->profile_photo_url ?? '/api/placeholder/128/128' }}" 
                             alt="Profile photo" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white">
                        
                        <!-- Photo Upload Overlay -->
                        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" 
                              class="absolute inset-0 rounded-full bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            <input type="file" name="photo" id="photo" class="hidden" onchange="this.form.submit()">
                            <label for="photo" class="cursor-pointer text-white p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>
                        </form>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ auth()->user()->name }}</h1>
                        <p class="text-indigo-100">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="bg-white shadow-md rounded-b-lg p-6 md:p-8">
                @if (session('success'))
                    <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', auth()->user()->name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', auth()->user()->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" 
                                   value="{{ old('phone_number', auth()->user()->phone_number) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @php
                            \Log::info('Profile view store check', [
                                'has_store' => auth()->user()->store !== null,
                                'store_details' => auth()->user()->store ? auth()->user()->store->toArray() : 'No store'
                            ]);
                        @endphp        
                        @if(auth()->user()->store)
                            <!-- Store Information Display -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Store Information</h3>
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        @if(auth()->user()->admin_status === 'approved') bg-green-100 text-green-800
                                        @elseif(auth()->user()->admin_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst(auth()->user()->admin_status) }}
                                    </span>
                                </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Store Name</p>
                                <p class="font-medium">{{ auth()->user()->store->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Category</p>
                                <p class="font-medium">{{ auth()->user()->store->category }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Store Phone</p>
                                <p class="font-medium">{{ auth()->user()->store->phone_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Store Address</p>
                                <p class="font-medium">{{ auth()->user()->store->address }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600">Description</p>
                                <p class="font-medium">{{ auth()->user()->store->description }}</p>
                            </div>
                        </div>
                        @if(auth()->user()->admin_status === 'approved')
                            <div class="mt-4">
                                <a href="/dashboard" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Go to Dashboard
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @else
                    <div class="md:col-span-2 mt-4">
                        <a href="{{ route('store.create') }}" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Open Store
                        </a>
                    </div>
                @endif
                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var successMessage = document.getElementById('successMessage');
                if (successMessage) {
                    successMessage.style.transition = 'opacity 1s';
                    successMessage.style.opacity = 0;
                    setTimeout(function() {
                        successMessage.remove();
                    }, 1000);
                }
            }, 12000);
        });
</script>
@endsection