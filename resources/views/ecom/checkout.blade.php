@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                Cart → <span class="font-medium">Checkout</span> → Confirmation
            </div>
        </div>

        <form action="{{ route('order.place') }}" method="POST" class="checkout-form">
            @csrf
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column -->
                <div class="lg:w-2/3 space-y-6">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Shipping Address</h2>
                            <button type="button" class="text-indigo-600 text-sm hover:text-indigo-700">
                                Choose saved address
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" 
                                       name="first_name" 
                                       placeholder="First Name"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                <input type="text" 
                                       name="last_name" 
                                       placeholder="Last Name"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                            </div>
                            <textarea 
                                name="address" 
                                rows="3" 
                                placeholder="Enter your complete address"
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >{{ auth()->user()->address ?? '' }}</textarea>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" 
                                       name="phone" 
                                       placeholder="Phone Number"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                <input type="text" 
                                       name="postal_code" 
                                       placeholder="Postal Code"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>
                        <div class="divide-y divide-gray-200">
                            @foreach($items as $key => $item)
                                <div class="flex items-center py-4 gap-4">
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $item['image']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-24 h-24 object-cover rounded-lg">
                                        <span class="absolute -top-2 -right-2 bg-gray-900 text-white text-xs font-medium px-2 py-1 rounded-full">
                                            {{ $item['quantity'] }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-lg text-gray-900">{{ $item['name'] }}</h3>
                                        @if(isset($item['variant_name']))
                                            <p class="text-sm text-gray-600">Variant: {{ $item['variant_name'] }}</p>
                                        @endif
                                        @if(isset($item['package_name']))
                                            <p class="text-sm text-gray-600">Package: {{ $item['package_name'] }}</p>
                                        @endif
                                        <div class="mt-2 font-medium text-gray-900">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Shipping Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="shipping_method" value="regular" class="h-4 w-4 text-indigo-600" required>
                                <div class="ml-4">
                                    <div class="font-medium">Regular Shipping</div>
                                    <div class="text-sm text-gray-600">2-3 business days</div>
                                </div>
                                <div class="ml-auto font-medium">Rp 15.000</div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="shipping_method" value="express" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">Express Shipping</div>
                                    <div class="text-sm text-gray-600">1 business day</div>
                                </div>
                                <div class="ml-auto font-medium">Rp 30.000</div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="bank_transfer" class="h-4 w-4 text-indigo-600" required>
                                <div class="ml-4">
                                    <div class="font-medium">Bank Transfer</div>
                                    <div class="text-sm text-gray-600">Pay via bank transfer</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="credit_card" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">Credit Card</div>
                                    <div class="text-sm text-gray-600">Pay with Visa or Mastercard</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="ewallet" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">E-Wallet</div>
                                    <div class="text-sm text-gray-600">GoPay, OVO, Dana, etc</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 sticky top-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                        
                        <!-- Store Voucher -->
                        <div class="mb-6">
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="store_voucher" 
                                       class="flex-1 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       placeholder="Enter voucher code">
                                <button type="button" 
                                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <!-- Price Details -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping Fee</span>
                                <span>Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Service Fee</span>
                                <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                            </div>
                            @if(isset($discount))
                            <div class="flex justify-between text-green-600 font-medium">
                                <span>Discount</span>
                                <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Total -->
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-gray-900">
                                    Rp {{ number_format($subtotal + $shippingFee + $serviceFee - ($discount ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white py-4 px-6 rounded-lg text-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            Place Order
                        </button>
                        
                        <!-- Security Notice -->
                        <p class="text-center text-sm text-gray-600 mt-4">
                            <span class="inline-block align-middle">🔒</span> 
                            Secure checkout powered by SSL encryption
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle shipping method selection
    const shippingInputs = document.querySelectorAll('input[name="shipping_method"]');
    shippingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedMethod = this.value;
            // Update shipping fee and total (you'll need to implement this)
        });
    });

    // Handle voucher code
    const voucherButton = document.querySelector('button[type="button"]');
    voucherButton?.addEventListener('click', function() {
        const voucherCode = document.querySelector('input[name="store_voucher"]').value;
        if (voucherCode) {
            // Validate and apply voucher (you'll need to implement this)
        }
    });
});
</script>
@endpush
@endsection