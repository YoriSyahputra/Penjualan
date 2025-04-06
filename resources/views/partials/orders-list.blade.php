@forelse($allOrders as $order)
<div class="border rounded-lg mb-4 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-600">Order ID:</span>
                <span class="ml-2 font-medium">{{ $order->id }}</span>
                <span class="ml-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                @if($order->status == 'pending')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Unpaid
                </span>
                @elseif($order->status == 'paid')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Paid
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4">
        <div class="space-y-4">

            @foreach($order->items as $item)
            <div class="flex items-start">
                <div class="flex-shrink-0 h-20 w-20">
                @if($item->product->productImages->isNotEmpty())
                    <img src="{{ asset('storage/' . $item->product->productImages->first()->path_gambar) }}"
                        alt="{{ $item->product->name }}"
                        class="h-full w-full object-cover rounded-md">
                @else
                    <img src="/api/placeholder/300/300" alt="No image" class="h-full w-full object-cover rounded-md">
                @endif
                </div>
                <div class="ml-4 flex-1">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-base font-medium text-gray-900">{{ $item->product->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                            @if($item->variant)
                                Variant: {{ $item->variant->name }}
                            @endif
                            @if($item->package)
                                @if($item->variant) · @endif
                                Package: {{ $item->package->name }}
                            @endif
                            </p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                </div>
            </div>
            @endforeach

            @if($order->status == 'paid')
                <div class="mb-8">
                        <h3 class="font-semibold text-gray-800 mb-3">Items Purchased</h3>
                        <div class="flex items-center space-x-4 p-4 bg-green-50 border border-green-400 rounded-md">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            </div>
                            <div>
                            <h4 class="font-bold text-green-700">
                                Status Order: <span class="font-bold text-green-700">
                                    @if($order->deliveryHistory)
                                        @switch($order->deliveryHistory->status)
                                            @case('sedang_diantar')
                                                Sedang diantar ke lokasi kamu
                                                @break
                                            @case('menuju_alamat')
                                                Kurir sedang menuju alamat kamu
                                                @break
                                            @case('tiba_di_tujuan')
                                                Paket sudah sampai di tujuan
                                                @break
                                            @default
                                                {{ $order->deliveryHistory->status }}
                                        @endswitch
                                    @else
                                        Menunggu pengiriman
                                    @endif
                                </span>
                            </h4>
                        </div>
                    </div>
            @endif
        </div>

        <div class="mt-6 border-t border-gray-200 pt-4">
            <div class="flex justify-between text-base font-medium text-gray-900">
                <p>Total Amount</p>
                <p>Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
            
            <div class="mt-4 flex justify-end space-x-4">
                @if($order->status == 'pending') 
                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" class="cancel-order-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batalkan Order
                        </button>
                    </form>
                    <a href="{{ route('order.confirmation', $order->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Detail Pembayaran
                    </a>
                @endif

                @if($order->status == 'paid')
                    <a href="{{ route('order.receipt', $order->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-8.268-2.943" />
                        </svg>
                        View Details
                    </a>
                @endif
                
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No Orders Found</h3>
    <p class="mt-1 text-sm text-gray-500">
        @if(!empty($search))
            No orders match your search criteria.
            <a href="{{ route('ecom.list_order_payment') }}" class="text-indigo-600 hover:text-indigo-800">
                View all orders
            </a>
        @else
            You haven't placed any orders yet.
        @endif
    </p>
    <div class="mt-6">
        <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Start Shopping
        </a>
    </div>
</div>
@endforelse