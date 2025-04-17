@extends('layouts.depan')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">Payment Instructions</h2>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Payment Details</h3>
            <p class="text-gray-600">Amount: Rp {{ number_format($topUp->amount, 0, ',', '.') }}</p>
            <p class="text-gray-600">Method: {{ ucfirst($topUp->payment_method) }}</p>
            <div class="mt-4">
                <p class="font-bold">Payment Code:</p>
                <div class="bg-gray-100 p-4 rounded-lg text-center text-2xl font-mono">
                    {{ $topUp->payment_code }}
                </div>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Instructions</h3>
            <ol class="list-decimal pl-4 space-y-2">
                @foreach($instructions as $instruction)
                    <li class="text-gray-600">{{ $instruction }}</li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
@endsection
