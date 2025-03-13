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

        <form method="POST" class="checkout-form" action="{{ route('checkout.process') }}" >
            @csrf
        
        <input type="hidden" name="selected_items" value="{{ implode(',', array_column($items, 'id')) }}"></div>
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
                                    <h3 class="font-medium text-lg text-gray-900">{{ $item['store_name'] }}</h3>
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
                                            <div class="text-sm text-gray-600">Rp {{ number_format(auth()->user()->wallet?->balance ?? 0, 0, ',', '.') }}</div>
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
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="ewallet" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">E-Wallet</div>
                                    <div class="text-sm text-gray-600">GoPay, OVO, Dana, etc</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors">
                                <input type="radio" name="payment_method" value="cod" class="h-4 w-4 text-indigo-600">
                                <div class="ml-4">
                                    <div class="font-medium">COD</div>
                                    <div class="text-sm text-gray-600">Bayar Di Rumah</div>
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
            // Update shipping fee and total (anda perlu mengimplementasikan logika ini)
        });
    });

    // Handle voucher code
    const voucherButton = document.querySelector('button[type="button"]');
    voucherButton?.addEventListener('click', function() {
        const voucherCode = document.querySelector('input[name="store_voucher"]').value;
        if (voucherCode) {
            // Validasi dan terapkan voucher (anda perlu mengimplementasikan logika ini)
        }
    });
});

function toggleAddressList() {
    const addressList = document.getElementById('addressList');
    if (addressList) {
        addressList.classList.toggle('hidden');
    }
}

function fillAddressForm(address) {
    // Pisahkan nama lengkap menjadi first name dan last name (asumsi dipisahkan dengan spasi)
    const names = address.recipient_name.split(' ');

    // Cek dan set first_name jika elemen ada
    const firstNameEl = document.getElementById('first_name');
    if (firstNameEl) {
        firstNameEl.value = names[0] || '';
    }

    // Cek dan set last_name jika elemen ada
    const lastNameEl = document.getElementById('last_name');
    if (lastNameEl) {
        lastNameEl.value = names.slice(1).join(' ') || '';
    }

    // Cek dan set address jika elemen ada
    const addressEl = document.getElementById('address');
    if (addressEl) {
        addressEl.value = address.address;
    }

    // Cek dan set phone jika elemen ada
    const phoneEl = document.getElementById('phone');
    if (phoneEl) {
        phoneEl.value = address.phone_number;
    }

    // Cek dan set postal_code jika elemen ada
    const postalCodeEl = document.getElementById('postal_code');
    if (postalCodeEl) {
        postalCodeEl.value = address.postal_code || '';
    }
    
    // Sembunyikan daftar alamat setelah pemilihan
    const addressListEl = document.getElementById('addressList');
    if (addressListEl) {
        addressListEl.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const instructionsDiv = document.getElementById('ludwigPaymentInstructions');

    // Tampilkan/sembunyikan instruksi Ludwig Payment berdasarkan pilihan payment_method
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'ludwig_payment') {
                instructionsDiv?.classList.remove('hidden');
            } else {
                instructionsDiv?.classList.add('hidden');
            }
        });
    });
});
</script>
@endpush
@endsection