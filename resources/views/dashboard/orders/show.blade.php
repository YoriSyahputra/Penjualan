@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Order #{{ $order->order_number }}</h1>
        <a href="{{ route('dashboard.orders.index') }}" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Back to Orders</a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold mb-2">Order Information</h2>
                <p><span class="font-medium">Date:</span> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                <p><span class="font-medium">Status Payment:</span> {{ ucfirst($order->status) }}</p>
                <p><span class="font-medium">Status:</span> {{ ucfirst($order->status_order) }}</p>
                <p><span class="font-medium">Payment Method:</span> {{ $order->payment_method }}</p>
                <p><span class="font-medium">Shipping Method:</span> {{ $order->shipping_method }}</p>
                <p><span class="font-medium">Shipping Kurir:</span> {{ $order->shipping_kurir }}</p>
                <p><span class="font-medium">Order ID:</span> {{ $order->order_number }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Customer Information</h2>
                <p><span class="font-medium">Name:</span> {{ $order->user->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $order->user->email }}</p>
                <p><span class="font-medium">Alamat:</span> {{ $order->alamat_lengkap }}</p>
                <p><span class="font-medium">Provinsi</span> {{ $order->provinsi }}</p>
                <p><span class="font-medium">Kota:</span> {{ $order->kota }}</p>
                <p><span class="font-medium">Kecamatan:</span> {{ $order->kecamatan }}</p>
                <p><span class="font-medium">Kode POS:</span> {{ $order->kode_pos }}</p>
            </div>
        </div>
                <!-- New Tracking Number Section -->
        @if($order->nomor_resi)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Tracking Information</h2>
                    <p>
                        <span class="font-medium">Nomor Resi:</span> 
                        <span class="text-blue-600">{{ $order->nomor_resi }}</span>
                    </p>
                </div>
        @endif


        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Order Items</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            {{ $item->product->name }}
                            @if($item->variant)
                            <span class="text-sm text-gray-500">({{ $item->variant->name }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ $item->quantity }}</td>
                        <td class="px-6 py-4">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-medium">Subtotal:</td>
                        <td class="px-6 py-3">{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-medium">Shipping Fee:</td>
                        <td class="px-6 py-3">{{ number_format($order->shipping_fee, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->service_fee)
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-medium">Service Fee:</td>
                        <td class="px-6 py-3">{{ number_format($order->service_fee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-medium">Total:</td>
                        <td class="px-6 py-3 font-bold">{{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Store Payments</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($storePayments as $payment)
                    <tr>
                        <td class="px-6 py-4">{{ $payment['store']->name }}</td>
                        <td class="px-6 py-4">{{ number_format($payment['subtotal'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ number_format($payment['shipping'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ number_format($payment['total'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Update Status Order</h2>
            <form action="{{ route('dashboard.orders.update-status', $order->id) }}" method="POST">
            @csrf
                @method('PUT')
                <select name="status_order" class="border-gray-300 rounded-md shadow-sm mr-2">
                    <option value="pending" {{ $order->status_order == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status_order == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="cancelled" {{ $order->status_order == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection