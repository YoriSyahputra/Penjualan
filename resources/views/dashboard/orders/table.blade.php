<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <form action="{{ route('dashboard.generate.tracking') }}" method="POST" id="orders-form">
    @csrf
    <div class="flex justify-between items-center px-4 py-3 bg-gray-50">
        <div>
            <input type="checkbox" id="select-all">
            <label for="select-all" class="ml-2 text-sm">Pilih Semua</label>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm hidden" id="generate-resi-btn">
                Generate Resi
            </button>
            <button type="button" class="bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded text-sm hidden ml-2" id="cetak-resi-btn">
                Cetak Resi Terpilih
            </button>
        </div>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Pilih
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cetak Resi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            @php
                $storeId = Auth::user()->store->id;
                $storeItems = $order->items->filter(function($item) use ($storeId) {
                    return $item->product->store_id == $storeId;
                });
                $storeTotal = $storeItems->sum(function($item) {
                    return $item->price * $item->quantity;
                });
                $itemCount = $storeItems->count();
            @endphp
            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                <td class="px-4 py-4">
                    <input type="checkbox" 
                           name="order_ids[]" 
                           value="{{ $order->id }}"
                           class="order-checkbox"
                           data-has-resi="{{ !empty($order->nomor_resi) ? 'true' : 'false' }}"
                           data-order-id="{{ $order->id }}"
                           {{ $order->status_order != 'processing' ? 'disabled' : '' }}>
                </td>

                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $order->created_at->format('d M Y H:i') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $itemCount }} Item
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    Rp {{ number_format($storeTotal, 0, ',', '.') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $order->status_order == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            ($order->status_order == 'cancelled' ? 'bg-red-100 text-red-800' :
                            ($order->status_order == 'processing' ? 'bg-blue-100 text-blue-800' :
                            'bg-green-100 text-green-800')) }}">
                        {{ ucfirst($order->status_order) }}
                    </span>
                </td>

                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('dashboard.orders.show', $order) }}" 
                       class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">
                        Lihat Detail
                    </a>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                    @if(!empty($order->nomor_resi))
                    <a href="{{ route('dashboard.orders.resi-sticker', $order) }}" target="_blank" class="text-purple-600 hover:text-purple-900">
                        Cetak Stiker Resi
                    </a>
                    @else
                    <span class="text-gray-400">Belum ada resi</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-4 text-center text-gray-500">
                    Belum ada order yang tersedia 🤷‍♂️
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </form>
</div>

<div class="mt-4 flex justify-between items-center">
    <div class="text-sm text-gray-600">
        Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} order
    </div>
    <div>
        {{ $orders->appends(request()->input())->links('components.custom-pagination') }}
    </div>
</div>  
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox:not(:disabled)');
    const generateResiBtn = document.getElementById('generate-resi-btn');
    const cetakResiBtn = document.getElementById('cetak-resi-btn');
    const ordersForm = document.getElementById('orders-form');

    // Fungsi untuk mengupdate tombol berdasarkan pilihan
    function updateButtons() {
        let anyChecked = false;
        let anyWithResi = false;
        
        orderCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                anyChecked = true;
                if (checkbox.dataset.hasResi === 'true') {
                    anyWithResi = true;
                }
            }
        });
        
        generateResiBtn.classList.toggle('hidden', !anyChecked);
        cetakResiBtn.classList.toggle('hidden', !anyWithResi);
    }

    // Event ketika "Pilih Semua" dicentang
    selectAllCheckbox.addEventListener('change', function() {
        orderCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateButtons();
    });

    // Event untuk setiap checkbox order
    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateButtons);
    });

    // Event untuk tombol cetak resi
    // Event untuk tombol cetak resi
    cetakResiBtn.addEventListener('click', function() {
    const selectedOrderIds = [];
    
    orderCheckboxes.forEach(checkbox => {
        if (checkbox.checked && checkbox.dataset.hasResi === 'true') {
            selectedOrderIds.push(checkbox.dataset.orderId);
        }
    });
    
    if (selectedOrderIds.length > 0) {
        const url = "{{ route('dashboard.orders.bulk-resi-sticker') }}?ids=" + selectedOrderIds.join(',');
        window.location.href = url;

    }
});
});
</script>