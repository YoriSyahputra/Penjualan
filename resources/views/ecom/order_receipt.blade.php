@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Order Receipt</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                <a href="/">Home</a> → <a href="{{ route('ecom.list_order_payment') }}">Orders</a> → <span class="font-medium">Receipt</span>
            </div>
        </div>

        <!-- Order Status -->
        <div class="mb-8 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                @if($order->status_order == 'pending')
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Payment Confirmed</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>This order has been paid successfully and wait the seller to processed.</p>
                        </div>
                    </div>
                @endif

                @if($order->status_order == 'processed')
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Payment Confirmed</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>This order has been paid successfully and is being processed.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 mb-6">
            <!-- Receipt Header -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                        <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Paid
                        </span>
                        <p class="text-gray-600 mt-1">Paid on {{ $order->paid_at ? $order->paid_at->format('F j, Y, g:i a') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Item List -->
                <div class="mb-8">
                    <h3 class="font-semibold text-gray-800 mb-3">Items Purchased</h3>
                    <div class="flex items-center space-x-4 p-4 bg-green-50 border border-green-400 rounded-md">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-700">
                            Status Order: <span class="font-bold text-green-700">{{ $order->status_package }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
                </h4>
                <div class="space-y-4">
                    @foreach($items as $item)
                    <div class="flex items-start border-b border-gray-100 pb-4">
                        <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-md overflow-hidden">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($item['variant_name'])
                                            Variant: {{ $item['variant_name'] }}
                                        @endif
                                        @if($item['package_name'])
                                            @if($item['variant_name']) | @endif
                                            Package: {{ $item['package_name'] }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Sold by: {{ $item['store_name'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</p>
                                    <p class="font-bold text-gray-900 mt-1">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Related Orders (if any) -->
            
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Order Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping Fee</span>
                        <span class="font-medium">Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Fee</span>
                        <span class="font-medium">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Payment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Method</h4>
                    <p class="text-gray-900">{{ $order->payment_method }}</p>
                    <p class="text-sm text-gray-600 mt-1">Transaction ID: {{ $order->transaction_id ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">Payment Code: {{ $order->payment_code }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Shipping Address</h4>
                    <p class="text-gray-900">{{ $order->alamat_lengkap }}</p>
                    <p class="text-gray-900">{{ $order->provinsi }}</p>
                    <p class="text-gray-900">{{ $order->kota }}</p>
                    <p class="text-gray-900">{{ $order->kecamatan }}</p>
                    <p class="text-gray-900">{{ $order->kode_pos }}</p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('ecom.list_order_payment') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali Belanja
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .breadcrumbs, button, .flex.flex-col.sm\\:flex-row {
            display: none !important;
        }
    }
</style>
@endpush
@endsection