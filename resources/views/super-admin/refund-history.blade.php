@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Refund History</h2>
                <form class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm"
                           placeholder="Search order number...">
                    <button type="submit" class="ml-2 bg-indigo-600 text-white px-4 py-2 rounded-md">Search</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cancelled By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Refund Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Refunded At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($refunds as $refund)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $refund->order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $refund->canceller->name }}</td>
                            <td class="px-6 py-4">{{ $refund->reason }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($refund->refunded_amount) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $refund->refunded_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
