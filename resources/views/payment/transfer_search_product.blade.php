@extends('layouts.depan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Pembayaran Pesanan</h1>
        <p class="text-gray-600 mt-2">Masukkan kode pembayaran Anda untuk melanjutkan proses pembayaran</p>
    </div>

    <!-- Payment Code Form -->
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <label for="paymentCode" class="block text-sm font-medium text-gray-700 mb-2">Kode Pembayaran</label>
                <div class="flex space-x-2">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            id="paymentCode"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center tracking-wider font-mono text-lg uppercase"
                            placeholder="LWP-XXXXXXXXXX"
                            maxlength="14"
                        >
                    </div>
                    <button 
                        onclick="searchPaymentCode()"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">Format: LWP-XXXXXXXXXX (Contoh: LWP-B31A3612B7)</p>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div id="orderDetails" class="max-w-md mx-auto mt-6 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Detail Pesanan</h2>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nomor Pesanan:</span>
                    <span id="orderNumber" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pembayaran:</span>
                    <span id="orderTotal" class="font-semibold text-lg"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span id="orderStatus" class="font-medium"></span>
                </div>
            </div>

            <!-- Products List -->
            <div id="productsList" class="mt-4 border-t pt-4">
                <h3 class="text-lg font-medium mb-3">Produk yang Dibeli</h3>
                <div id="productsContainer" class="space-y-3">
                    <!-- Products will be inserted here -->
                </div>
            </div>

   
            <!-- Payment Button -->
            <form id="paymentForm" action="{{ route('order.payment.process') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="order_id" id="orderId">
                <button type="button" onclick="showProductPinModal()" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-102 transition-all duration-300 shadow-lg">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Bayar Sekarang</span>
                    </div>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alert Component -->
<div id="alert" class="fixed top-4 right-4 max-w-sm w-full hidden">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold" id="alertTitle">Error!</strong>
        <span class="block sm:inline" id="alertMessage"></span>
    </div>
</div>

@include('components.pin-modal-2')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main elements
    const paymentCode = document.getElementById('paymentCode');
    const orderDetails = document.getElementById('orderDetails');
    const alert = document.getElementById('alert');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    const paymentForm = document.getElementById('paymentForm');
    const productPinModal = document.getElementById('productPinModal');

    // PIN state management class
    class ProductPinState {
        constructor(inputElement, maxLength = 6) {
            this.value = '';
            this.inputElement = inputElement;
            this.maxLength = maxLength;
        }

        append(digit) {
            if (this.value.length < this.maxLength) {
                this.value += digit;
                this.updateInput();
            }
        }

        clear() {
            this.value = '';
            this.updateInput();
        }

        delete() {
            this.value = this.value.slice(0, -1);
            this.updateInput();
        }

        updateInput() {
            this.inputElement.value = this.value;
        }

        get length() {
            return this.value.length;
        }

        get pin() {
            return this.value;
        }
    }

    // Initialize PIN state
    const productPin = new ProductPinState(document.getElementById('productPinInput'));

    // Format payment code input (LWP-XXXXXXXXXX)
    paymentCode.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        value = value.replace(/[^A-Z0-9-]/g, '');
        
        if (!value.startsWith('LWP-') && value.length > 0) {
            if (value.startsWith('LWP')) {
                value = 'LWP-' + value.substring(3);
            } else {
                value = 'LWP-' + value;
            }
        }
        
        if (value.length > 14) {
            value = value.substring(0, 14);
        }
        
        e.target.value = value;
    });

    // Search for payment code
    window.searchPaymentCode = function() {
        const code = paymentCode.value;
        if (code.length === 14) {
            fetchOrderDetails(code);
        } else {
            showAlert('error', 'Error', 'Masukkan kode pembayaran yang valid');
        }
    }

    function fetchOrderDetails(code) {
        console.log("Searching for code:", code);
        fetch(`/api/orders/payment-code/${code}`)
            .then(response => {
                console.log("Response status:", response.status);
                return response.json().then(data => {
                    if (!response.ok) {
                        // Pass along the error message from the API
                        throw new Error(data.message || 'Network response was not ok');
                    }
                    return data;
                });
            })
            .then(data => {
                console.log("API response:", data);
                if (data.success) {
                    displayOrderDetails(data.order);
                } else {
                    showAlert('error', 'Error', data.message || 'Kode pembayaran tidak valid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', error.message || 'Gagal memuat detail pesanan');
            });
    }
    // Display order details on the page
    function displayOrderDetails(order) {
        document.getElementById('orderNumber').textContent = order.order_number;
        document.getElementById('orderTotal').textContent = `Rp ${formatNumber(order.total)}`;
        document.getElementById('orderStatus').textContent = order.status;
        document.getElementById('orderId').value = order.id;

        // Display products
        const productsContainer = document.getElementById('productsContainer');
        productsContainer.innerHTML = '';
        
        order.items.forEach(item => {
            const productElement = document.createElement('div');
            productElement.className = 'flex justify-between items-center';
            productElement.innerHTML = `
                <span class="text-gray-800">${item.product_name} (${item.quantity}x)</span>
                <span class="font-medium">Rp ${formatNumber(item.price)}</span>
            `;
            productsContainer.appendChild(productElement);
        });

        orderDetails.classList.remove('hidden');
    }

    // Format number to Indonesian format
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Show alert message
    window.showAlert = function(type, title, message) {
        alertTitle.textContent = title;
        alertMessage.textContent = message;
        
        // Set alert color based on type
        const alertElement = document.querySelector('#alert > div');
        alertElement.className = 'px-4 py-3 rounded relative border';
        
        if (type === 'error') {
            alertElement.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
        } else if (type === 'success') {
            alertElement.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
        } else if (type === 'warning') {
            alertElement.classList.add('bg-yellow-100', 'border-yellow-400', 'text-yellow-700');
        }
        
        alert.classList.remove('hidden');
        
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 5000);
    }

    // PIN Management Functions
    window.clearProductPin = function() {
        productPin.clear();
    }
    
    window.deleteProductPin = function() {
        productPin.delete();
    }
    
    window.cancelProductPin = function() {
        productPinModal.classList.add('hidden');
        productPin.clear();
    }

    // Show PIN modal for product payment
    window.showProductPinModal = function() {
        const orderNumber = document.getElementById('orderNumber').textContent;
        const amount = document.getElementById('orderTotal').textContent.replace('Rp ', '');
        
        document.getElementById('paymentSummaryAmount').textContent = amount;
        document.getElementById('orderNumberSummary').textContent = orderNumber;
        productPinModal.classList.remove('hidden');
    }

    // PIN confirmation and payment processing
    window.confirmProductPin = function() {
        if (productPin.length !== 6) {
            showAlert('warning', 'PIN tidak valid', 'Masukkan PIN 6 digit.');
            return;
        }

        // Get order_id from form
        const orderId = document.getElementById('orderId').value;
        
        // Create FormData object
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('pin', productPin.pin);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Submit to the correct endpoint
        fetch('/order/payment/process', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productPinModal.classList.add('hidden');
                productPin.clear();
                showAlert('success', 'Berhasil', 'Pembayaran berhasil diproses.');
                setTimeout(() => window.location.href = data.redirect || '/dashboard', 1500);
            } else {
                throw new Error(data.message || 'Pembayaran gagal');
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            showAlert('error', 'Error', error.message);
            productPin.clear();
        });
    }

    // Set up PIN buttons
    document.querySelectorAll('.product-pin-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            productPin.append(btn.getAttribute('data-val'));
        });
    });
});
</script>
@endpush
@endsection