@extends('layouts.depan')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Your Payment Codes</h2>
        <a href="{{ route('ewallet.top-up') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Top Up Again
        </a>
    </div>

    <div class="space-y-4">
        @forelse($paymentCodes as $code)
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-sm text-gray-500">{{ $code->created_at->format('d M Y, H:i') }}</span>
                        <p class="font-semibold text-lg">Rp {{ number_format($code->amount, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($code->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($code->status) }}
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Payment Code:</p>
                            <p class="font-mono font-bold text-lg">{{ $code->payment_code }}</p>
                        </div>
                        <button onclick="copyPaymentCode('{{ $code->payment_code }}')" 
                                class="text-indigo-600 hover:text-indigo-800 p-2 rounded-full hover:bg-indigo-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        Payment Method: <span class="font-medium">{{ ucfirst($code->payment_method) }}</span>
                    </p>
                </div>

                <a href="{{ route('ewallet.top-up.instructions', $code) }}" 
                   class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    View Instructions →
                </a>
            </div>
        @empty
            <div class="text-center py-8">
                <p class="text-gray-500">No payment codes found</p>
                <a href="{{ route('ewallet.top-up') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Create new top up →
                </a>
            </div>
        @endforelse
    </div>
</div>

<script>
function copyPaymentCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Payment code copied to clipboard');
    });
}
</script>
@endsection
