@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Driver History</h2>
                <form class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm"
                           placeholder="Search driver name...">
                    <button type="submit" class="ml-2 bg-indigo-600 text-white px-4 py-2 rounded-md">Search</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Deliveries</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wallet Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($drivers as $driver)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->delivery_histories_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($driver->driverWallet->balance ?? 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->phone_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
