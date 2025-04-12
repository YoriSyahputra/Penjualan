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


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Core elements
    const amountInput = document.getElementById('amount');
    const continueBtn = document.getElementById('continueBtn');
    const pinModal = document.getElementById('pinModal');
    const createPinModal = document.getElementById('createPinModal');
    const transferForm = document.getElementById('transferForm');
    
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
        continueBtn.disabled = amount < 1000 || amount > currentBalance;
    }

    // Handle numpad input
    function handleNumpadInput(value) {
        const currentAmount = amountInput.value.replace(/[^\d]/g, '');
        const newAmount = currentAmount + value;
        
        if (parseInt(newAmount) <= currentBalance) {
            // Update the visible formatted field
            amountInput.value = formatAmount(newAmount);
            // And update the hidden input with the raw number
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

    // Event Listeners
    document.querySelectorAll('.numpad-btn').forEach(btn => {
        btn.addEventListener('click', () => handleNumpadInput(btn.dataset.value));
    });

    document.getElementById('backspace').addEventListener('click', handleBackspace);

    // PIN Input Handlers
    document.querySelectorAll('.pin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const pinInput = document.getElementById('pinInput');
            if (pinInput.value.length < 6) {
                pinInput.value += this.dataset.value;
            }
        });
    });

    // Create PIN Input Handlers
    document.querySelectorAll('.create-pin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const newPinInput = document.getElementById('newPin');
            if (newPinInput.value.length < 6) {
                newPinInput.value += this.dataset.value;
                document.getElementById('savePinBtn').disabled = newPinInput.value.length !== 6;
            }
        });
    });

    // PIN Management Functions
    window.clearPin = () => document.getElementById('pinInput').value = '';
    window.deletePin = () => document.getElementById('pinInput').value = document.getElementById('pinInput').value.slice(0, -1);
    window.clearNewPin = () => {
        document.getElementById('newPin').value = '';
        document.getElementById('savePinBtn').disabled = true;
    };
    window.deleteNewPin = () => {
        const newPinInput = document.getElementById('newPin');
        newPinInput.value = newPinInput.value.slice(0, -1);
        document.getElementById('savePinBtn').disabled = true;
    };

    // Modal Management
    window.cancelPin = () => {
        pinModal.style.display = 'none';
        clearPin();
    };

    // Continue Button Handler - Updated to include amount in PIN modal
    continueBtn.addEventListener('click', function() {
        const hasPin = document.querySelector('meta[name="user-has-pin"]')?.content === 'true';
        const amount = parseInt(amountInput.value.replace(/\D/g, '')) || 0;
        
        if (amount < 1000) {
            alert('Minimum transfer amount is Rp 1.000');
            return;
        }

        // Update amount in PIN modal summary
        const summaryAmount = document.getElementById('summaryAmount');
        if (summaryAmount) {
            summaryAmount.textContent = new Intl.NumberFormat('id-ID').format(amount);
        }
        
        if (!hasPin) {
            createPinModal.style.display = 'flex';
        } else {
            pinModal.style.display = 'flex';
        }
    });

    // Keyboard Support
    document.addEventListener('keydown', function(e) {
        if (pinModal.style.display === 'flex' || createPinModal.style.display === 'flex') return;

        if (/^[0-9]$/.test(e.key)) {
            handleNumpadInput(e.key);
        } else if (e.key === 'Backspace') {
            handleBackspace();
        }
    });

    // Modal Click Outside
    window.addEventListener('click', function(e) {
        if (e.target === pinModal) cancelPin();
        if (e.target === createPinModal) {
            createPinModal.style.display = 'none';
            clearNewPin();
        }
    });
});
</script>

<style>
.numpad-btn:active, .pin-btn:active, .create-pin-btn:active {
    background-color: theme('colors.gray.200');
}
</style>
@endpush
@endsection