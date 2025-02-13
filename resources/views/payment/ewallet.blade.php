@extends('layouts.depan')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transfer-search/style-1.css') }}">
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header with Animation -->
            <div class="text-center mb-10 transform hover:scale-105 transition-transform duration-300">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">E-Wallet Payment</h1>
                <p class="text-gray-600 mt-2 font-medium">Secure & Fast Digital Payments</p>
            </div>

            <!-- Balance Card with Gradient -->
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

            <!-- Enhanced Tab Navigation -->
            <div class="mb-8 flex space-x-2 justify-center">
                <a href={{ route('ewallet.payment') }}
                    class="tab-btn w-20 h-20 flex items-center justify-center text-lg font-medium {{ request()->routeIs('ewallet.payment') ? 'bg-[#4f46e5] text-white' : 'bg-gray-100 text-gray-600' }} rounded-md shadow-md hover:bg-gray-200 focus:outline-none transition-all duration-200"
                    id="payment-tab">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </a>
                <a href={{ route('ewallet.search') }}
                    class="tab-btn w-20 h-20 flex items-center justify-center text-lg font-medium {{ request()->routeIs('ewallet.search') ? 'bg-[#4f46e5] text-white' : 'bg-gray-100 text-gray-600' }} rounded-md shadow-md hover:bg-gray-200 focus:outline-none transition-all duration-200"
                    id="transfer-tab">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </a>
            </div>
            <!-- Payment Content -->
            <div id="payment-section" class="tab-content">
                <div class="bg-gray-50 rounded-xl p-6 mb-8 transform hover:scale-102 transition-all duration-300">
                    <p class="text-gray-600 font-medium mb-2">Total Payment Amount</p>
                    <p class="text-4xl font-bold text-gray-800">Rp {{ number_format($total_amount, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Transaction fee included</span>
                    </div>
                </div>

                <form action="{{ route('ewallet.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="relative">

                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-102 transition-all duration-300 shadow-lg">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span>Complete Payment</span>
                            </div>
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentUrl = window.location.href;
        let tabs = document.querySelectorAll(".tab-btn");
        
        tabs.forEach(tab => {
            if (tab.href === currentUrl) {
                tab.classList.add("bg-indigo-500", "text-white");
                tab.classList.remove("bg-gray-100", "text-gray-600");
            }
        });
    });
</script>

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
@endpush
@endsection