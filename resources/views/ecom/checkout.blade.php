@extends('layouts.depan')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-8">Checkout</h1>

        <form action="{{ route('place.order') }}" method="POST">
            @csrf
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column -->
                <div class="lg:w-2/3">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
                        <textarea 
                            name="address" 
                            rows="3" 
                            class="w-full border rounded-lg p-2"
                            required
                        >{{ auth()->user()->address ?? '' }}</textarea>
                    </div>
                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold mb-4">Order Items</h2>
                        @foreach($items as $key => $item)
                            <div class="flex items-center border-b py-4 last:border-b-0">
                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-20 h-20 object-cover rounded">
                                <div class="ml-4 flex-1">
                                    <h3 class="font-medium">{{ $item['name'] }}</h3>
                                    @if(isset($item['variant_name']))
                                        <p class="text-sm text-gray-600">Variant: {{ $item['variant_name'] }}</p>
                                    @endif
                                    @if(isset($item['package_name']))
                                        <p class="text-sm text-gray-600">Package: {{ $item['package_name'] }}</p>
                                    @endif
                                    <div class="flex justify-between mt-2">
                                        <span>Qty: {{ $item['quantity'] }}</span>
                                        <span class="font-medium">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold mb-4">Shipping Method</h2>
                        <select name="shipping_method" class="w-full border rounded-lg p-2" required>
                            <option value="">Select shipping method</option>
                            <option value="regular">Regular Shipping</option>
                            <option value="express">Express Shipping</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Payment Method</h2>
                        <select name="payment_method" class="w-full border rounded-lg p-2" required>
                            <option value="">Select payment method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="ewallet">E-Wallet</option>
                        </select>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                        
                        <!-- Store Voucher -->
                        <div class="mb-4">
                            <label class="block text-sm mb-2">Store Voucher</label>
                            <input type="text" 
                                   name="store_voucher" 
                                   class="w-full border rounded-lg p-2" 
                                   placeholder="Enter voucher code">
                        </div>

                        <!-- Price Details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span>Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service Fee</span>
                                <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                            </div>
                            @if(isset($discount))
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Total -->
                        <div class="border-t pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold">Total</span>
                                <span class="font-semibold text-lg">
                                    Rp {{ number_format($subtotal + $shippingFee + $serviceFee - ($discount ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection