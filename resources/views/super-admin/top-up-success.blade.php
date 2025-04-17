@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden mt-16">
        <div class="p-6">
            <div class="flex items-center justify-center">
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h2 class="mt-4 text-center text-2xl font-bold text-gray-900">Top Up Successful</h2>
            
            <dl class="mt-6 divide-y divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $topUp->user->name }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Payment Code</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900 sm:col-span-2">{{ $topUp->payment_code }}</dd>
                </div>
            </dl>

            <div class="mt-6">
                <a href="{{ route('super-admin.manual-top-up') }}" 
                   class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Back to Manual Top Up
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
