@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="relative">
        </div>
        
        <div class="pt-16 pb-6 px-6">
            <div class="flex items-center space-x-6">
                <div class="w-32 h-32 -mt-20 rounded-full overflow-hidden border-4 border-white bg-white shadow-xl">
                    @if(auth()->user()->store->logo)
                        <img src="{{ Storage::url(auth()->user()->store->logo) }}" 
                             alt="Store logo" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->store->name }}</h1>
                            <p class="text-sm text-gray-500">{{ auth()->user()->store->category }}</p>
                        </div>
                        <button onclick="document.getElementById('editForm').classList.remove('hidden')"
                                class="bg-white text-indigo-600 px-4 py-2 rounded-full shadow-md hover:bg-indigo-50 transition-colors border border-indigo-100 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Information -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Store Information</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Store Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->store->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Category</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->store->category }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->store->description }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->store->phone_number }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Address</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->store->address }}</dd>
            </div>
        </dl>
    </div>

    <!-- Edit Form (Hidden by default) -->
    <div id="editForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Store Profile</h3>
                <button onclick="document.getElementById('editForm').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('store.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Logo Upload -->
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0 h-24 w-24 rounded-full overflow-hidden border-2 border-gray-200 shadow-sm">
                        @if(auth()->user()->store->logo)
                            <img src="{{ Storage::url(auth()->user()->store->logo) }}" 
                                 alt="Current logo" 
                                 class="h-full w-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
                        <input type="file" name="logo" accept="image/*" 
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Recommended: Square image, at least 200x200px</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Store Name</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', auth()->user()->store->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" id="category"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->name }}" 
                                    {{ old('category', auth()->user()->store->category) == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number"
                               value="{{ old('phone_number', auth()->user()->store->phone_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', auth()->user()->store->description) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', auth()->user()->store->address) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('editForm').classList.add('hidden')"
                            class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" 
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)">
    {{ session('success') }}
</div>
@endif
@endsection
