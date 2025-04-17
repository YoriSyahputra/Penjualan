@extends('layouts.depan')

@section('content')
<div class="min-h-screen bg-gray-50 pt-16 py-8 sm:py-12">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-8 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900 text-center">Transfer Money</h1>
                <p class="text-gray-600 text-center mt-1">Send money instantly to {{ $recipient->name }}</p>
            </div>
            <!-- Transfer Form -->
            <div class="p-6">
                <!-- Recipient Card -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <img src="{{ $recipient->profile_photo_url }}" 
                                 alt="{{ $recipient->name }}" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $recipient->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $recipient->email }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('ewallet.transfer') }}" method="POST" id="transferForm">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">

                    <!-- Amount Input -->
                    <div class="mb-8">
                        <div class="text-center mb-4">
                        <input type="text" 
                                id="amount" 
                                class="w-full text-3xl font-bold text-center bg-transparent border-none focus:ring-0 focus:outline-none"
                                value="Rp 0"
                                readonly>

                            <!-- Hidden input that will actually be submitted -->
                            <input type="hidden" name="amount" id="amountValue" value="0">

                            <p class="text-sm text-gray-500 mt-1">
                                Available Balance: Rp {{ number_format(auth()->user()->wallet->balance ?? 0) }}
                            </p>
                        </div>

                        <!-- Numpad -->
                        <div class="grid grid-cols-3 gap-3">
                            @for($i = 1; $i <= 9; $i++)
                            <button type="button" 
                                    class="numpad-btn h-16 rounded-xl bg-gray-50 hover:bg-gray-100 active:bg-gray-200 font-semibold text-xl transition-colors"
                                    data-value="{{ $i }}">
                                {{ $i }}
                            </button>
                            @endfor
                            <button type="button" 
                                    class="numpad-btn h-16 rounded-xl bg-gray-50 hover:bg-gray-100 active:bg-gray-200 font-semibold text-xl transition-colors"
                                    data-value="000">
                                000
                            </button>
                            <button type="button" 
                                    class="numpad-btn h-16 rounded-xl bg-gray-50 hover:bg-gray-100 active:bg-gray-200 font-semibold text-xl transition-colors"
                                    data-value="0">
                                0
                            </button>
                            <button type="button" 
                                    id="backspace"
                                    class="h-16 rounded-xl bg-gray-50 hover:bg-gray-100 active:bg-gray-200 transition-colors flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Continue Button -->
                    <button type="button" 
                            id="continueBtn"
                            class="w-full h-14 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Continue to Transfer
                    </button>
                </form>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 text-center">
            <div class="flex items-center justify-center text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Secured by end-to-end encryption
            </div>
        </div>
    </div>
</div>

@include('components.pin-modal')
@include('components.create-pin-modal')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
        // Core elements
    const amountInput = document.getElementById('amount');
    const continueBtn = document.getElementById('continueBtn');
    const pinModal = document.getElementById('pinModal');
    const createPinModal = document.getElementById('createPinModal');
    const transferForm = document.getElementById('transferForm');

    // PinState class definition
    class PinState {
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

    // Initialize PIN instances
    window.pinInstances = {
        pin: new PinState(document.getElementById('pinInput')),
        newPin: document.getElementById('newPinInput') ? new PinState(document.getElementById('newPinInput')) : null
    };
    

    // Get user balance from meta tag
    const currentBalance = parseInt(document.querySelector('meta[name="user-balance"]')?.content) || 0;

    // Format amount to Indonesian Rupiah
    function formatAmount(amount) {
        if (!amount) return 'Rp 0';
        return `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`;
    }

    // Validate form and update continue button state
    function validateForm() {
        const amount = parseInt(amountInput.value.replace(/\D/g, '')) || 0;
        const isValid = amount >= 1000 && amount <= currentBalance;
        continueBtn.disabled = !isValid;
    }

    // Handle numpad input
    function handleNumpadInput(value) {
        const currentAmount = amountInput.value.replace(/[^\d]/g, '');
        const newAmount = currentAmount + value;
        
        if (parseInt(newAmount) <= currentBalance) {
            amountInput.value = formatAmount(newAmount);
            document.getElementById('amountValue').value = newAmount;
            validateForm();
        }
    }

    function handleBackspace() {
        const currentAmount = amountInput.value.replace(/[^\d]/g, '');
        const newAmount = currentAmount.slice(0, -1);
        amountInput.value = formatAmount(newAmount);
        document.getElementById('amountValue').value = newAmount;
        validateForm();
    }

    // Event Listeners for numpad
    document.querySelectorAll('.numpad-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            handleNumpadInput(btn.dataset.value);
        });
    });

    document.getElementById('backspace').addEventListener('click', handleBackspace);

    // PIN Input Handlers
    document.querySelectorAll('.pin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.pinInstances.pin.append(btn.getAttribute('data-val'));
        });
    });

    // Create PIN Input Handlers
    document.querySelectorAll('.create-pin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.pinInstances.newPin.append(btn.getAttribute('data-val'));
        });
    });

    // GLOBAL PIN MANAGEMENT FUNCTIONS
    window.clearPin = () => {
        window.pinInstances.pin.clear();
        document.querySelectorAll('.pin-dot').forEach(dot => {
            dot.classList.remove('bg-blue-600');
        });
    };
    
    window.deletePin = () => {
        window.pinInstances.pin.delete();
        const dots = document.querySelectorAll('.pin-dot');
        if (dots && window.pinInstances.pin.length < dots.length) {
            dots[window.pinInstances.pin.length].classList.remove('bg-blue-600');
        }
    };

    // Modal Management
    window.cancelPin = () => {
        if (pinModal) pinModal.classList.add('hidden');
        window.clearPin();
    };

    // Critical Handler for PIN Confirmation
    window.confirmPin = () => {
        if (window.pinInstances.pin.length !== 6) {
            showAlert('warning', 'PIN tidak valid', 'Masukkan PIN 6 digit.');
            return;
        }

        if (!transferForm) {
            showAlert('error', 'Error', 'Form transfer tidak ditemukan.');
            return;
        }

        const formData = new FormData(transferForm);
        formData.append('pin', window.pinInstances.pin.pin);
        
        const confirmBtn = document.querySelector('[onclick="confirmPin()"]');
        if (confirmBtn) {
            confirmBtn.textContent = 'Processing...';
            confirmBtn.disabled = true;
        }

        fetch(transferForm.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Transfer gagal'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (pinModal) pinModal.classList.add('hidden');
                window.clearPin();
                showAlert('success', 'Berhasil', 'Transfer berhasil dilakukan.');
                setTimeout(() => window.location.href = data.redirect || '/dashboard', 1500);
            } else {
                throw new Error(data.message || 'Transfer gagal');
            }
        })
        .catch(error => {
            showAlert('error', 'Error', error.message);
            window.clearPin();
            if (confirmBtn) {
                confirmBtn.textContent = 'Konfirmasi';
                confirmBtn.disabled = false;
            }
        });
    };

    // Continue Button Handler
    continueBtn.addEventListener('click', function() {
        const hasPin = document.querySelector('meta[name="user-has-pin"]')?.content === 'true';
        const amount = parseInt(amountInput.value.replace(/\D/g, '')) || 0;
        
        if (amount < 1000) {
            showAlert('warning', 'Validasi gagal', 'Minimum transfer amount is Rp 1.000');
            return;
        }

        const summaryAmount = document.getElementById('summaryAmount');
        if (summaryAmount) {
            summaryAmount.textContent = new Intl.NumberFormat('id-ID').format(amount);
        }
        
        if (!hasPin) {
            if (createPinModal) createPinModal.classList.remove('hidden');
        } else {
            if (pinModal) pinModal.classList.remove('hidden');
        }
    });

    // Alert helper function
    function showAlert(type, title, message) {
        if (window.Swal) {
            Swal.fire({
                icon: type,
                title: title,
                text: message
            });
        } else {
            alert(`${title}: ${message}`);
        }
    }

    // Keyboard Support
    document.addEventListener('keydown', function(e) {
        const isModalOpen = (pinModal && getComputedStyle(pinModal).display === 'flex') || 
                           (createPinModal && getComputedStyle(createPinModal).display === 'flex');
        
        if (isModalOpen) {
            return;
        }

        if (/^[0-9]$/.test(e.key)) {
            handleNumpadInput(e.key);
        } else if (e.key === 'Backspace') {
            handleBackspace();
        }
    });

    // Modal Click Outside
    window.addEventListener('click', function(e) {
        if (e.target === pinModal) {
            window.cancelPin();
        }
        if (e.target === createPinModal) {
            if (createPinModal) createPinModal.classList.add('hidden');
            window.clearNewPin();
        }
    });

    // Listen for pinCreated event from other components
    document.addEventListener('pinCreated', () => {
        setTimeout(() => {
            if (pinModal) pinModal.classList.remove('hidden');
        }, 1000);
    });
});
</script>

<style>
.numpad-btn:active, .pin-btn:active, .new-pin-btn:active {
    background-color: theme('colors.gray.200');
}
</style>
@endpush
@endsection