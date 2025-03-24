@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Order Confirmation</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                <a href="/cart">Cart</a> → <a href="/checkout">Checkout</a> → <span class="font-medium">Confirmation</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <!-- Success Message -->
            <div class="mb-8 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Your order has been received!</h3>
    
                        @if($order->payment_method == 'ludwig_payment')
                            <div class="mt-2 text-sm text-green-700">
                                <p>Thank you for your purchase. Your order is being processed.</p>
                            </div>
                        @endif
                        @if($order->payment_method == 'wallet')
                            <div class="mt-2 text-sm text-green-700">
                                <p>Thank you for your purchase. Your order is being processed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ludwig Payment Information (show only if Ludwig Payment selected) -->
            @if($paymentMethod === 'ludwig_payment')
            <div class="mb-8 bg-indigo-50 border rounded-xl p-6">
                <h2 class="text-xl font-semibold text-indigo-900 mb-4">Ludwig Payment Information</h2>
                <div class="text-center mb-6">
                    <p class="text-sm text-indigo-700 mb-2">Your Payment Code:</p>
                    <p class="text-3xl font-bold tracking-wider text-indigo-900 mb-2 font-mono">{{ $paymentCode }}</p>
                    <p class="text-xs text-indigo-600">This code is valid for 24 hours</p>
                </div>
                
                <div class="bg-white p-4 rounded-lg mb-4">
                    <h3 class="font-medium text-gray-900 mb-2">Follow these steps to complete your payment:</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Open your Ludwig Payment app on your mobile device</li>
                        <li>Tap on "Pay" or "Scan" option</li>
                        <li>Enter the payment code shown above</li>
                        <li>Verify the amount and merchant information</li>
                        <li>Confirm your payment with your PIN or biometric</li>
                        <li>Keep the payment receipt for your records</li>
                    </ol>
                </div>
                
                <div class="flex justify-center">
                    <button onclick="copyPaymentCode('{{ $paymentCode }}')" 
                            class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z" />
                            <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z" />
                        </svg>
                        Copy Payment Code
                    </button>
                </div>
            </div>
            @endif

            <!-- Main Order -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h2>
                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status == 'completed') bg-green-100 text-green-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <div class="flex flex-col divide-y divide-gray-200">
                    @foreach($items as $item)
                    <div class="py-4 flex items-start">
                        <div class="flex-shrink-0 h-20 w-20">
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover rounded-md">
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if(isset($item['store_name']))
                                        Store: {{ $item['store_name'] }}
                                        @endif
                                        
                                        @if($item['variant_name'])
                                        @if(isset($item['store_name'])) · @endif
                                        Variant: {{ $item['variant_name'] }}
                                        @endif
                                        
                                        @if($item['package_name'])
                                        @if($item['variant_name'] || isset($item['store_name'])) · @endif
                                        Package: {{ $item['package_name'] }}
                                        @endif
                                    </p>
                                </div>
                                <p class="text-base font-medium text-gray-900">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Price Details -->
                <div class="mt-6 border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Shipping Fee ({{ ucfirst($shippingMethod) }})</span>
                        <span>Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Service Fee</span>
                        <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-medium text-base">
                        <span>Order Total</span>
                        <span>Rp {{ number_format($subtotal + $shippingFee + $serviceFee, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Related Orders (if any) -->
            @if(isset($relatedOrders) && !empty($relatedOrders))
                @foreach($relatedOrders as $relOrder)
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Order #{{ $relOrder['order']->order_number }}</h2>
                        <span class="px-3 py-1 text-xs font-medium rounded-full 
                            @if($relOrder['order']->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($relOrder['order']->status == 'processing') bg-blue-100 text-blue-800
                            @elseif($relOrder['order']->status == 'completed') bg-green-100 text-green-800
                            @elseif($relOrder['order']->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($relOrder['order']->status) }}
                        </span>
                    </div>
                    
                    <div class="flex flex-col divide-y divide-gray-200">
                        @foreach($relOrder['items'] as $item)
                        <div class="py-4 flex items-start">
                            <div class="flex-shrink-0 h-20 w-20">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover rounded-md">
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="text-base font-medium text-gray-900">{{ $item['name'] }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if(isset($item['store_name']))
                                            Store: {{ $item['store_name'] }}
                                            @endif
                                            
                                            @if($item['variant_name'])
                                            @if(isset($item['store_name'])) · @endif
                                            Variant: {{ $item['variant_name'] }}
                                            @endif
                                            
                                            @if($item['package_name'])
                                            @if($item['variant_name'] || isset($item['store_name'])) · @endif
                                            Package: {{ $item['package_name'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <p class="text-base font-medium text-gray-900">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Price Details for Related Order -->
                    <div class="mt-6 border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($relOrder['order']->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping Fee ({{ ucfirst($relOrder['order']->shipping_method) }})</span>
                            <span>Rp {{ number_format($relOrder['order']->shipping_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Service Fee</span>
                            <span>Rp {{ number_format($relOrder['order']->service_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 flex justify-between font-medium text-base">
                            <span>Order Total</span>
                            <span>Rp {{ number_format($relOrder['order']->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Total for All Orders -->
                <div class="mt-6 bg-indigo-50 p-4 rounded-lg">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Payment</span>
                        <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-indigo-600 mt-1">This is the total amount for all orders with the same payment code</p>
                </div>
            @endif
            
            <!-- Shipping & Payment Info -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">Shipping Information</h3>
                    <div class="bg-gray-50 rounded-md p-3 text-sm text-gray-700">
                        <p class="font-medium">{{ auth()->user()->name }}</p>
                        <p class="mt-1">{{ auth()->user()->phone_number }}</p>
                        <p class="mt-2">
                        @if($selected_address_id)
                            {{ auth()->user()->addresses()->find($selected_address_id)->alamat_lengkap }}
                        @else
                            {{ $fullAddress }}
                        @endif
                        </p>
                        <p class="mt-2">Shipping Method: {{ ucfirst($shippingMethod) }}</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-2">Payment Information</h3>
                    <div class="bg-gray-50 rounded-md p-3 text-sm text-gray-700">
                        <p class="font-medium">Payment Method:</p>
                        <p class="mt-1">
                            @if($paymentMethod === 'ludwig_payment')
                                Ludwig Payment
                            @elseif($paymentMethod === 'ewallet')
                                E-Wallet
                            @elseif($paymentMethod === 'cod')
                                Cash on Delivery
                            @endif
                        </p>
                        
                        @if($paymentMethod === 'cod')
                        <p class="mt-2 text-yellow-600">
                            <svg class="inline-block h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Have the exact amount ready when your package arrives
                        </p>
                        @endif
                        
                        @if($paymentMethod === 'ewallet')
                        <p class="mt-2 text-blue-600">
                            <svg class="inline-block h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            You'll be redirected to complete your payment
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-between gap-4">
                <a href="{{ route('home') }}" 
                   class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Bayar Nanti
                </a>

                @if($order->status == 'pending')
                    @if($paymentMethod === 'ludwig_payment')
                        <a href="{{ route('payment.search') }}" 
                        class="inline-flex justify-center items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Bayar Disini Mengguanakan LudwigPay
                        </a>
                    @endif

                @endif

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyPaymentCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Payment code copied to clipboard!');
    });
}
</script>
@endpush
@endsection