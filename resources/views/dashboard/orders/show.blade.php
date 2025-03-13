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
                <p><span class="font-medium">Status:</span> {{ ucfirst($order->status) }}</p>
                <p><span class="font-medium">Payment Method:</span> {{ $order->payment_method }}</p>
                <p><span class="font-medium">Payment Code:</span> {{ $order->payment_code }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Customer Information</h2>
                <p><span class="font-medium">Name:</span> {{ $order->user->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $order->user->email }}</p>
                
            </div>
        </div>

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
            <h2 class="text-lg font-semibold mb-2">Update Status</h2>

                @csrf
                @method('PUT')
                <select name="status" class="border-gray-300 rounded-md shadow-sm mr-2">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection