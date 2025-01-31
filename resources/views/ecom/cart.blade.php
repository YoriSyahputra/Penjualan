{{-- resources/views/ecom/cart.blade.php --}}
@extends('layouts.depan')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-8">Shopping Cart</h1>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Cart Items -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold">Cart Items ({{ count(session('cart', [])) }})</h2>
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAll" class="form-checkbox h-5 w-5 text-indigo-600">
                            <label for="selectAll" class="ml-2">Select All</label>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    @forelse(session('cart', []) as $key => $item)
                    <div class="border-b border-gray-200 py-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="selected_items[]" 
                                   value="{{ $key }}" 
                                   class="cart-item-checkbox form-checkbox h-5 w-5 text-indigo-600">
                            
                            <div class="flex-shrink-0 ml-4">
                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-24 h-24 object-cover rounded">
                            </div>
                            
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium">{{ $item['name'] }}</h3>
                                @if(isset($item['variant_id']) ?? false)
                                    <p class="text-sm text-gray-600">Variant: {{ $item['variant_name'] }}</p>
                                @endif

                                @if(isset($item['package_id']) ?? false)
                                    <p class="text-sm text-gray-600">Package: {{ $item['package_name'] }}</p>
                                @endif       
                                <div class="flex items-center mt-2">
                                    <span class="text-indigo-600 font-medium">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex items-center mt-2">
                                    <button onclick="updateQuantity('{{ $key }}', -1)" 
                                            class="text-gray-500 focus:outline-none focus:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    
                                    <input type="number" 
                                           value="{{ $item['quantity'] }}"
                                           data-key="{{ $key }}"
                                           class="mx-2 border text-center w-16"
                                           readonly>
                                    
                                    <button onclick="updateQuantity('{{ $key }}', 1)" 
                                            class="text-gray-500 focus:outline-none focus:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                    
                                    <button onclick="removeItem('{{ $key }}')" 
                                            class="ml-4 text-red-500 focus:outline-none">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="mt-2 text-gray-500">Your cart is empty</p>
                        <a href="/shop" class="mt-4 inline-block text-indigo-600 hover:text-indigo-500">
                            Continue Shopping
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Order Summary -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Shipping</span>
                        <span id="shipping">-</span>
                    </div>
                    <hr class="my-4">
                    <div class="flex justify-between mb-6">
                        <span class="font-semibold">Total</span>
                        <span class="font-semibold" id="total">Rp 0</span>
                    </div>
                    <button onclick="checkout()" 
                            class="bg-indigo-600 text-white py-2 px-4 w-full rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedItems = new Set();

// Select All functionality
document.getElementById('selectAll').addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = e.target.checked;
        if (e.target.checked) {
            selectedItems.add(checkbox.value);
        } else {
            selectedItems.delete(checkbox.value);
        }
    });
    updateSummary();
});

// Individual checkbox functionality
document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        if (e.target.checked) {
            selectedItems.add(e.target.value);
        } else {
            selectedItems.delete(e.target.value);
        }
        updateSummary();
    });
});

function updateQuantity(key, change) {
    fetch(`/cart/update/${key}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            change: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeItem(key) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch(`/cart/remove/${key}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function updateSummary() {
    // Calculate totals based on selected items
    let subtotal = 0;
    selectedItems.forEach(key => {
        const item = cartItems[key];
        subtotal += item.price * item.quantity;
    });

    document.getElementById('subtotal').textContent = `Rp ${numberFormat(subtotal)}`;
    // Update shipping and total here based on your logic
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function checkout() {
    if (selectedItems.size === 0) {
        alert('Please select items to checkout');
        return;
    }

    // Proceed to checkout with selected items
    window.location.href = '/checkout?items=' + Array.from(selectedItems).join(',');
}
</script>
@endpush
@endsection