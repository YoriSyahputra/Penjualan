@extends('layouts.depan')

@section('content')
<style>
    .slide-enter {
        transform: translateX(100%);
        opacity: 0;
    }
    .slide-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.5s ease-out;
    }
    .slide-exit {
        transform: translateX(0);
        opacity: 1;
    }
    .slide-exit-active {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.5s ease-in;
    }
</style>

<div class="min-h-screen bg-gray-50 pt-16 py-8 sm:py-12">
    <!-- Wallet Balance Notification -->
    <div id="walletNotification" class="fixed top-20 right-4 bg-white rounded-lg p-4 shadow-lg  border-green-100 slide-enter" style="z-index: 50;">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 3a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm8 0a1 1 0 011-1h.01a1 1 0 110 2H15a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Wallet Balance</p>
                <p class="text-lg font-bold text-green-600">Rp {{ number_format($walletBalance) }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Success Header -->
            <div class="px-6 py-8 border-b border-gray-100">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 text-center">Transfer Successful!</h1>
                <p class="text-gray-600 text-center mt-1">Your money has been sent successfully</p>
            </div>

            <!-- Transfer Details -->
            <div class="p-6">
                <!-- Amount Card -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-center">
                    <p class="text-sm text-gray-600 mb-1">Amount Sent</p>
                    <h2 class="text-3xl font-bold text-gray-900">Rp {{ number_format($transfer->amount) }}</h2>
                </div>

                <!-- Recipient Details -->
                <div class="border-t border-gray-100 py-4">
                    <p class="text-sm text-gray-600 mb-2">Recipient</p>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <img src="{{ $transfer->recipient->profile_photo_url }}" 
                                 alt="{{ $transfer->recipient->name }}" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $transfer->recipient->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $transfer->recipient->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="border-t border-gray-100 py-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID</span>
                        <span class="text-gray-900 font-medium">{{ $transfer->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date & Time</span>
                        <span class="text-gray-900">{{ $transfer->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($transfer->notes)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Notes</span>
                        <span class="text-gray-900">{{ $transfer->notes }}</span>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 mt-6">
                    <a href="{{ route('ewallet.payment') }}" 
                       class="block w-full h-14 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center">
                        Back to LudwigPay
                    </a>
                    <a href="{{ route('ewallet.search') }}" 
                       class="block w-full h-14 bg-gray-50 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                        Make Another Transfer
                    </a>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 text-center">
            <div class="flex items-center justify-center text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Transaction secured by end-to-end encryption
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('walletNotification');
    
    // Remove initial slide-enter class after a small delay to trigger animation
    setTimeout(() => {
        notification.classList.remove('slide-enter');
        notification.classList.add('slide-enter-active');
    }, 100);

    // Start exit animation after 7 seconds
    setTimeout(() => {
        notification.classList.remove('slide-enter-active');
        notification.classList.add('slide-exit-active');
        
        // Remove element after animation completes
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 7000);
});
</script>
@endsection