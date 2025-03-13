<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->total, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Paid
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('dashboard.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
</div>