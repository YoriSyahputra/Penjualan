@extends('layouts.depan')

@section('content')

<link rel="stylesheet" href="{{ asset('css/transfer-search/style-1.css') }}">
<link rel="stylesheet" href="{{ asset('css/transfer-search/style-2.css') }}">

<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- New Header Section -->
            <div class="text-center mb-10 transform hover:scale-105 transition-transform duration-300">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Transfer Money</h1>
                <p class="text-gray-600 mt-2 font-medium">Secure & Fast Digital Transfers</p>
            </div>

            <!-- New Balance Card with Gradient -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-8 transform hover:scale-102 transition-all duration-300 shadow-lg mx-4">
                <div class="flex justify-between items-center">
                    <div class="text-white">
                        <p class="text-indigo-100 font-medium">Available Balance</p>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format(auth()->user()->wallet?->balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <!-- Search Section -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Find User to Transfer</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                    id="searchUser" 
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="Search by username or email..."
                                    autocomplete="off">
                            </div>
                        </div>
                        <div id="searchResults" class="absolute z-10 w-full bg-white mt-2 rounded-xl shadow-lg hidden divide-y divide-gray-100"></div>
                    </div>
                </div>

                <!-- Live Search Results -->
                <div id="userResults" class="mb-8 hidden">
                    <h2 class="text-lg font-semibold mb-4">Search Results</h2>
                    <div id="userResultsContent" class="space-y-3">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <!-- Recent Transfers -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Recent Transfers</h2>
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
                    </div>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        @forelse($recentTransfers as $transfer)
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
                            <img src="{{ $transfer->recipient->profile_photo_url }}" 
                                alt="{{ $transfer->recipient->name }}" 
                                class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">{{ $transfer->recipient->name }}</p>
                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $transfer->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <a href="{{ route('transfer.amount', $transfer->recipient_id) }}" 
                                class="opacity-0 group-hover:opacity-100 transition-opacity px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                Transfer Again
                            </a>
                        </div>
                        @empty
                        <div class="col-span-2 text-center py-8 text-gray-500">
                            No recent transfers
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/transfer-search/script-1.js') }}"></script>
<script src="{{ asset('js/transfer-search/script-2.js') }}"></script>
@endpush
<style>
.tab-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 8px;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endsection