@extends('layouts.depan')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Top Up LudwigPay</h2>
        <a href="{{ route('ewallet.payment-codes') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            My Payment Codes
        </a>
    </div>

    <form action="{{ route('ewallet.top-up.process') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Preset Amounts -->
        <div class="grid grid-cols-2 gap-4">
            @foreach($presetAmounts as $amount)
                <button type="button" 
                        class="amount-btn border rounded-lg p-4 hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        data-amount="{{ $amount }}">
                    Rp {{ number_format($amount, 0, ',', '.') }}
                </button>
            @endforeach
        </div>

        <!-- Custom Amount -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Custom Amount</label>
            <input type="number" 
                   name="amount" 
                   id="amount"
                   min="5000"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Payment Method -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="indomaret">Indomaret</option>
                <option value="alfamart">Alfamart</option>
                <option value="bank">Bank Transfer</option>
                <option value="ludwigmart">LudwigMart</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Continue
        </button>
    </form>
</div>

<script>
document.querySelectorAll('.amount-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('amount').value = this.dataset.amount;
    });
});
</script>
@endsection
