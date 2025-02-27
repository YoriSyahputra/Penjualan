@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                <a href="/">Home</a> → <span class="font-medium">Your Orders</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <!-- Warning Message - Only show if there are unpaid orders -->
            @if($unpaidOrders->count() > 0)
            <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Payment Required</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>You have {{ $unpaidOrders->count() }} unpaid order(s). Please complete the payment within 24 hours to avoid automatic cancellation.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Orders List -->
            @forelse($allOrders as $order)
            <div class="border rounded-lg mb-4 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-600">Order ID:</span>
                            <span class="ml-2 font-medium">{{ $order->id }}</span>
                            <span class="ml-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div>
                            @if($order->status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Unpaid
                            </span>
                            @elseif($order->status == 'paid')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Paid
                            </span>
                            @elseif($order->status == 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Cancelled
                            </span>
                            @elseif($order->status == 'delivered')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Delivered
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-20 w-20">
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="h-full w-full object-cover rounded-md">
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="text-base font-medium text-gray-900">{{ $item->product->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                        @if($item->variant)
                                            Variant: {{ $item->variant->name }}
                                        @endif
                                        @if($item->package)
                                            @if($item->variant) · @endif
                                            Package: {{ $item->package->name }}
                                        @endif
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-base font-medium text-gray-900">
                            <p>Total Amount</p>
                            <p>Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="mt-4 flex justify-end space-x-4">
                            @if($order->status == 'pending')
                                <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Batalkan Order
                                    </button>
                                </form>
                                <a href="{{ route('order.confirmation', $order->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Detail Pembayaran
                                </a>
                            @else
                                <a href="{{ route('ecom.list_order_payment') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-8.268-2.943" />
                                    </svg>
                                    View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Orders Found</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't placed any orders yet.</p>
                <div class="mt-6">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Start Shopping
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection