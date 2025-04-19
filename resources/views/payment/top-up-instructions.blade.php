@extends('layouts.depan')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">Payment Instructions</h2>
        <p class="mt-2 text-gray-600">Please follow the instructions below to complete your payment</p>
        <a href="{{ route('ewallet.top-up') }}" class="mt-2 text-gray-600">← Back To TOP UP</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">Payment Details</h3>
                <span class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                    {{ ucfirst($topUp->payment_method) }}
                </span>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Amount</span>
                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <p class="text-gray-600 mb-2">Payment Code:</p>
                    <div class="bg-white border-2 border-dashed border-indigo-500 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-mono font-bold text-gray-900">{{ $topUp->payment_code }}</span>
                            <button onclick="copyPaymentCode('{{ $topUp->payment_code }}')" 
                                    class="text-indigo-600 hover:text-indigo-800 focus:outline-none"
                                    id="copyButton">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Instructions</h3>
            <ol class="space-y-4">
                @foreach($instructions as $instruction)
                    <li class="flex items-start">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-800 font-bold mr-3">
                            {{ $loop->iteration }}
                        </span>
                        <p class="text-gray-700 flex-1">{{ $instruction }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>

<div id="popup" class="popup hidden">
    <div class="popup-content">
        <div class="checkmark-container">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="24" fill="none" />
                <path class="checkmark-check" fill="none" d="M16 26l7 7 14-14" />
            </svg>
        </div>
        <p>Kode pembayaran berhasil disalin!</p>
    </div>
</div>

<style>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.popup.show {
    opacity: 1;
    pointer-events: auto;
}

.popup-content {
    background: #fff;
    padding: 30px 40px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    overflow: visible;
}

.checkmark-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    overflow: visible;
}

.checkmark {
    width: 60px;
    height: 60px;
    stroke: green;
    stroke-width: 3;
    transform-origin: center;
}

.checkmark-circle {
    stroke: green;
    stroke-width: 3;
    stroke-dasharray: 151;
    stroke-dashoffset: 150;
    animation: drawCircle 0.6s ease-out forwards, bounceIn 0.5s ease-out;
    transform-origin: center;
}

.checkmark-check {
    stroke: green;
    stroke-width: 3;
    stroke-dasharray: 30;
    stroke-dashoffset: 30;
    animation: drawCheck 0.4s ease-out 0.4s forwards;
    transform-origin: center;
}

@keyframes drawCircle {
    to { stroke-dashoffset: 0; }
}

@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}

@keyframes bounceIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    60% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
    }
}

.hidden { display: none; }
</style>

@push('scripts')
<script>
function copyPaymentCode() {
    const paymentCode = "{{ $topUp->payment_code }}";

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(paymentCode)
            .then(() => {
                console.log("Kode berhasil disalin.");
                showPopup();
            })
            .catch((err) => {
                console.error("Gagal menyalin:", err);
                fallbackCopy(paymentCode);
            });
    } else {
        fallbackCopy(paymentCode);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "-9999px";
    document.body.appendChild(textArea);
    textArea.select();

    try {
        document.execCommand('copy');
        console.log("Fallback: Kode berhasil disalin.");
        showPopup();
    } catch (err) {
        console.error("Fallback: Gagal menyalin:", err);
    }
    document.body.removeChild(textArea);
}

function showPopup() {
    const popup = document.getElementById('popup');
    popup.classList.remove('hidden');
    popup.classList.add('show');

    setTimeout(() => {
        popup.classList.remove('show');
        popup.classList.add('hidden');
    }, 1500);
}
</script>
@endpush
@endsection
