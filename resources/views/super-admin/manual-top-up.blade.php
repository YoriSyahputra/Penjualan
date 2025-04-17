@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-semibold mb-6">Manual Top Up Confirmation</h2>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Payment Code</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="text" id="payment-code" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter payment code">
                <button type="button" onclick="searchPaymentCode()" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Search
                </button>
            </div>
        </div>

        <div id="top-up-details" class="hidden">
            <div class="border-t border-gray-200 pt-4">
                <dl class="divide-y divide-gray-200">
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">User Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2" id="user-name"></dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2" id="phone-number"></dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2" id="amount"></dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <form action="{{ route('super-admin.manual-top-up.confirm') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_code" id="confirm-payment-code">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Confirm Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div id="no-results" class="hidden">
            <p class="text-red-600 text-sm">No pending top-up request found with this payment code.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function searchPaymentCode() {
    const paymentCode = document.getElementById('payment-code').value;
    
    fetch(`{{ route('super-admin.search-payment-code') }}?payment_code=${paymentCode}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('user-name').textContent = data.user.name;
                document.getElementById('phone-number').textContent = data.user.phone_number;
                document.getElementById('amount').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.amount);
                document.getElementById('confirm-payment-code').value = data.payment_code;
                document.getElementById('top-up-details').classList.remove('hidden');
                document.getElementById('no-results').classList.add('hidden');
            } else {
                document.getElementById('top-up-details').classList.add('hidden');
                document.getElementById('no-results').classList.remove('hidden');
            }
        });
}
</script>
@endpush
@endsection
