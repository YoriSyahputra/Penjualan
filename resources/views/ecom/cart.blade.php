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
                        <h2 class="text-lg font-semibold">Cart Items ({{ collect($cartItems)->unique('id')->count() }})</h2>
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAll" class="form-checkbox h-5 w-5 text-indigo-600">
                            <label for="selectAll" class="ml-2">Select All</label>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    @forelse($cartItems as $key => $item)
                    <div class="border-b border-gray-200 py-4">
                    <div class="flex items-center">
                    <input type="checkbox" 
                        name="selected_items[]" 
                        value="{{ $item['id'] }}" 
                        data-price="{{ $item['price'] }}"
                        data-quantity="{{ $item['quantity'] }}"a
                        class="cart-item-checkbox form-checkbox h-5 w-5 text-indigo-600">
                    
                    <div class="flex-shrink-0 ml-4">
                        @if(isset($item['image']) && Storage::disk('public')->exists($item['image']))
                            <img src="{{ Storage::url($item['image']) }}" 
                                alt="{{ $item['name'] }}" 
                                class="w-24 h-24 object-cover rounded">
                        @else
                            <img src="{{ asset('images/default-product.jpg') }}" 
                                alt="{{ $item['name'] }}" 
                                class="w-24 h-24 object-cover rounded">
                        @endif
                    </div>

                            
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium">{{ $item['name'] }}</h3>
                                @if(isset($item['variant_name']))
                                    <p class="text-sm text-gray-600">Variant: {{ $item['variant_name'] }}</p>
                                @endif

                                @if(isset($item['package_name']))
                                    <p class="text-sm text-gray-600">Package: {{ $item['package_name'] }}</p>
                                @endif       
                                    <div class="flex items-center mt-2">
                                        @if($item['has_discount'])
                                            <span class="text-gray-500 line-through mr-2">
                                                Rp {{ number_format($item['original_price'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-indigo-600 font-medium">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </span>
                                            <span class="ml-2 text-sm text-green-600">
                                                -{{ round((($item['original_price'] - $item['price']) / $item['original_price']) * 100) }}%
                                            </span>
                                        @else
                                            <span class="text-indigo-600 font-medium">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                <div class="flex items-center mt-2">
                                    <button onclick="updateQuantity('{{ $item['id'] }}', -1)" 
                                            class="text-gray-500 focus:outline-none focus:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    
                                    <input type="number" 
                                           value="{{ $item['quantity'] }}"
                                           data-key="{{ $item['id'] }}"
                                           class="mx-2 border text-center w-16"
                                           readonly>
                                    
                                    <button onclick="updateQuantity('{{ $item['id'] }}', 1)" 
                                            class="text-gray-500 focus:outline-none focus:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                    
                                    <button onclick="confirmDelete('{{ $item['id'] }}')" 
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

            <!-- Order Summary section remains the same -->
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
<!-- Add SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const cartItems = @json($cartItems);
let selectedItems = new Set();

// Utility functions
function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function showSweetAlert(title, text, icon = 'success') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

function updateQuantity(id, change) {
    const quantityInput = document.querySelector(`input[data-key="${id}"]`);
    const checkbox = document.querySelector(`input[value="${id}"]`);
    const currentQuantity = parseInt(quantityInput.value);
    const newQuantity = currentQuantity + change;
    
    // Don't proceed if trying to set quantity below 1
    if (newQuantity < 1) {
        showSweetAlert('Error', 'Quantity cannot be less than 1', 'error');
        return;
    }

    // Store old quantity for reverting if needed
    const oldQuantity = currentQuantity;
    
    // Optimistic update
    quantityInput.value = newQuantity;
    checkbox.setAttribute('data-quantity', newQuantity);
    
    if (checkbox.checked) {
        updateSummary();
    }

    fetch(`/cart/update/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ change: change })
    })
    .then(response => response.json())
.then(data => {
    if (data.success) {
        // Update all cart count elements on the page
        const cartCountElements = document.querySelectorAll('#cartCount');
        cartCountElements.forEach(element => {
            // Use the distinct count from the server response
            if (data.distinctCount !== undefined) {
                element.textContent = data.distinctCount;
            }
        });
        
        showSweetAlert('Success', 'Quantity updated successfully');

        } else {
            // Revert changes if failed
            quantityInput.value = oldQuantity;
            checkbox.setAttribute('data-quantity', oldQuantity);
            if (checkbox.checked) {
                updateSummary();
            }
            showSweetAlert('Error', data.message || 'Failed to update quantity', 'error');
        }
    })
    .catch(error => {
        console.error('Cart update error:', error);
        // Revert changes on error
        quantityInput.value = oldQuantity;
        checkbox.setAttribute('data-quantity', oldQuantity);
        if (checkbox.checked) {
            updateSummary();
        }
        showSweetAlert('Error', 'Error updating quantity', 'error');
    });
}
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            removeItem(id);
        }
    });
}

function removeItem(id) {
    const itemElement = document.querySelector(`input[value="${id}"]`).closest('.border-b');
    
    // Optimistic removal
    itemElement.style.opacity = '0.5';
    
    fetch(`/cart/remove/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            itemElement.remove();
            selectedItems.delete(id);
            updateSummary();
            showSweetAlert('Success', 'Item removed from cart');
            
            // Update cart count - modified to use the server's count
            const cartCount = document.getElementById('cartCount');
            if (cartCount && data.cartCount !== undefined) {
                cartCount.textContent = data.cartCount;
            }

            // If cart is empty, reload the page to show empty state
            if (document.querySelectorAll('.cart-item-checkbox').length === 0) {
                location.reload();
            }
        } else {
            itemElement.style.opacity = '1';
            showSweetAlert('Error', 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        itemElement.style.opacity = '1';
        showSweetAlert('Error', 'Error removing item', 'error');
    });
}


// Rest of the JavaScript code remains the same
function updateSummary() {
    let subtotal = 0;
    
    document.querySelectorAll('.cart-item-checkbox:checked').forEach(checkbox => {
        const price = parseFloat(checkbox.getAttribute('data-price'));
        const quantity = parseInt(checkbox.getAttribute('data-quantity'));
        subtotal += price * quantity;
    });

    document.getElementById('subtotal').textContent = `Rp ${numberFormat(subtotal)}`;
    document.getElementById('total').textContent = `Rp ${numberFormat(subtotal)}`;
}

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

document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        if (e.target.checked) {
            selectedItems.add(e.target.value);
        } else {
            selectedItems.delete(e.target.value);
        }
        
        const allCheckboxes = document.querySelectorAll('.cart-item-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = selectedItems.size === allCheckboxes.length;
        
        updateSummary();
    });
});

function checkout() {
    if (selectedItems.size === 0) {
        showSweetAlert('Error', 'Please select items to checkout', 'error');
        return;
    }

    window.location.href = '/checkout?items=' + Array.from(selectedItems).join(',');
}

updateSummary();
</script>
@endpush
@endsection