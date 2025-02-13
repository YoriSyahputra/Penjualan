@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                <a href="/cart">Cart </a>→ <span class="font-medium">Checkout</span> → Confirmation
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
                        </div>

                        <!-- User Info Summary -->
                        <div class="mt-3 space-y-2 mb-6">
                            <p class="text-gray-600">Nama: <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span></p>
                            <p class="text-gray-600">Nomor Telepon: <span class="font-medium text-gray-800">{{ auth()->user()->phone_number }}</span></p>
                            <p class="text-gray-600">Alamat: <span class="font-medium text-gray-800">{{ auth()->user()->address }}</span></p>
                        </div>

                        @if(auth()->user()->addresses()->count() > 0)
                            <button type="button" 
                                    onclick="toggleAddressList()"
                                    class="w-full mb-4 py-3 px-6 bg-gray-100 text-gray-800 rounded-lg text-lg font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                Choose saved address
                            </button>
                        @endif

                        <!-- Saved Addresses List (Initially Hidden) -->
                        <div id="addressList" class="hidden space-y-4">
                            @forelse(auth()->user()->addresses as $address)
                                <label class="block">
                                    <div class="bg-white border-l-4 {{ $address->is_primary ? 'border-indigo-500' : 'border-gray-300' }} p-4 rounded-lg shadow-md transform transition-all duration-300 hover:scale-[1.02]">
                                        <div class="flex items-start">
                                            <input type="radio" 
                                                name="selected_address" 
                                                value="{{ $address->id }}"
                                                {{ $address->is_primary ? 'checked' : '' }}
                                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                onchange="fillAddressForm({{ json_encode($address) }})">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-gray-800">Default Address</h4>
                                                    @if($address->is_primary)
                                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                                                            Primary
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-600 mt-1">{{ $address->recipient_name }}</p>
                                                <p class="text-gray-600">{{ $address->phone_number }}</p>
                                                <p class="text-gray-600 mt-2">{{ $address->address }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                @if($loop->last)
                                    <!-- New Address Form inside addressList -->
                                    <div class="mt-6 space-y-4 border-t pt-6">
                                        <h3 class="font-semibold text-gray-900">Or enter a new address:</h3>
                                        <textarea 
                                            name="address" 
                                            id="address"
                                            rows="3" 
                                            placeholder="Enter your complete address"
                                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            required
                                        >{{ auth()->user()->address ?? '' }}</textarea>
                                    </div>
                                @endif
                            @empty
                                <p class="text-gray-500 text-center py-4">No saved addresses found</p>
                                <!-- New Address Form when no saved addresses -->
                                <div class="mt-6 space-y-4">
                                    <textarea 
                                        name="address" 
                                        id="address"
                                        rows="3" 
                                        placeholder="Enter your complete address"
                                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                    >{{ auth()->user()->address ?? '' }}</textarea>
                                </div>
                            @endforelse
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
                                            <p class="text-sm text-gray-600">Harga: Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                            <p class="text-sm text-gray-600">Total: {{ $item['quantity'] }}</p>
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
                    <!-- Payment Method Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            <!-- Ludwig Payment -->
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="ludwig_payment" class="h-4 w-4 text-indigo-600" required>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium">Ludwig Payment</div>
                                            <div class="text-sm text-gray-600">Pay securely with Ludwig Payment</div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">Recommended</span>
                                            <img src="/images/ludwig-payment-logo.png" alt="Ludwig Payment" class="h-8 w-auto" onerror="this.src='/images/payment-default.png'">
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Instant confirmation</li>
                                            <li>Secure transaction</li>
                                            <li>24/7 support</li>
                                        </ul>
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="bank_transfer" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">Bank Transfer</div>
                                    <div class="text-sm text-gray-600">Pay via bank transfer</div>
                                </div>
                            </label>

                            <!-- Credit Card -->
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="credit_card" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">Credit Card</div>
                                    <div class="text-sm text-gray-600">Pay with Visa or Mastercard</div>
                                </div>
                            </label>

                            <!-- E-Wallet -->
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="ewallet" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">E-Wallet</div>
                                    <div class="text-sm text-gray-600">GoPay, OVO, Dana, etc</div>
                                </div>
                            </label>
                        </div>
                        <div id="ludwigPaymentInstructions" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                            <h3 class="font-medium text-gray-900 mb-2">How to pay with Ludwig Payment:</h3>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                                <li>Open your Ludwig Payment app</li>
                                <li>Select "Pay" or "Scan"</li>
                                <li>Enter the payment code that will be shown after clicking "Place Order"</li>
                                <li>Confirm your payment with your 6-digit PIN</li>
                                <li>Keep the payment proof until order is confirmed</li>
                            </ol>
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

function toggleAddressList() {
    const addressList = document.getElementById('addressList');
    addressList.classList.toggle('hidden');
}

function fillAddressForm(address) {
    // Split full name into first and last name (assuming space separation)
    const names = address.recipient_name.split(' ');
    document.getElementById('first_name').value = names[0] || '';
    document.getElementById('last_name').value = names.slice(1).join(' ') || '';
    
    document.getElementById('address').value = address.address;
    document.getElementById('phone').value = address.phone_number;
    document.getElementById('postal_code').value = address.postal_code || '';
    
    // Hide the address list after selection
    document.getElementById('addressList').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const ludwigPaymentRadio = document.querySelector('input[value="ludwig_payment"]');
    const instructionsDiv = document.getElementById('ludwigPaymentInstructions');

    // Show/hide Ludwig Payment instructions based on selection
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'ludwig_payment') {
                instructionsDiv.classList.remove('hidden');
            } else {
                instructionsDiv.classList.add('hidden');
            }
        });
    });

    // Modify the existing payment processing for Ludwig Payment
    const checkoutForm = document.querySelector('.checkout-form');
    checkoutForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (selectedPaymentMethod === 'ludwig_payment') {
            // Generate a unique payment code for Ludwig Payment
            const paymentCode = generateLudwigPaymentCode();
            
            // Show the Ludwig Payment modal with the code
            showLudwigPaymentModal(paymentCode);
        } else {
            // Handle other payment methods as before
            // Your existing payment processing code
        }
    });
});

function generateLudwigPaymentCode() {
    // Generate a random 12-digit payment code
    return Math.random().toString().slice(2, 14);
}

function showLudwigPaymentModal(paymentCode) {
    const modalHTML = `
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 flex items-center justify-center ludwig-payment-modal">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-xl font-semibold mb-4">Ludwig Payment</h3>
                
                <div class="text-center mb-6">
                    <div class="text-sm text-gray-600 mb-2">Your Payment Code:</div>
                    <div class="text-3xl font-bold tracking-wide text-gray-900 mb-2">${paymentCode}</div>
                    <div class="text-xs text-gray-500">Code valid for 15 minutes</div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h4 class="font-medium text-blue-900 mb-2">Next Steps:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                        <li>Open your Ludwig Payment app</li>
                        <li>Enter this payment code</li>
                        <li>Confirm payment with your PIN</li>
                    </ol>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="copyPaymentCode('${paymentCode}')"
                            class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Copy Code
                    </button>
                    <button type="button" 
                            onclick="closeLudwigPaymentModal()"
                            class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function copyPaymentCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Payment code copied to clipboard!');
    });
}

function closeLudwigPaymentModal() {
    const modal = document.querySelector('.ludwig-payment-modal');
    modal.remove();
}

</script>
@endpush
@endsection