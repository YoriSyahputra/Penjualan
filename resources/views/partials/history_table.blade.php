<!-- Partial view untuk refresh ajax table -->
@forelse($histories as $index => $history)
    <tr class="border-t history-row" 
        data-date="{{ $history->created_at->format('Y-m-d') }}"
        data-courier="{{ $history->order->shipping_kurir ?? '' }}"
        data-search="{{ $history->order->nomor_resi ?? '' }} {{ $history->notes ?? '' }}">
        <td class="py-3 px-4">{{ $histories->firstItem() + $index }}</td>
        <td class="py-3 px-4">{{ $history->created_at->format('d M Y, H:i') }}</td>
        <td class="py-3 px-4">{{ $history->order->nomor_resi ?? 'N/A' }}</td>
        <td class="py-3 px-4">{{ $history->order->shipping_kurir ?? 'N/A' }}</td>
        <td class="py-3 px-4">
            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                @if($history->status == 'delivered') bg-green-100 text-green-800
                @elseif($history->status == 'failed') bg-red-100 text-red-800
                @elseif($history->status == 'picked_up') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucwords(str_replace('_', ' ', $history->status)) }}
            </span>
        </td>
        <td class="py-3 px-4">
            @if($history->order)
                {{ Str::limit($history->order->alamat_lengkap . ', ' . 
                $history->order->kecamatan . ', ' . 
                $history->order->kota, 50) }}
            @else
                Alamat tidak tersedia
            @endif
        </td>
        <td class="py-3 px-4">{{ Str::limit($history->notes, 30) }}</td>
        <td class="py-3 px-4">
            @if($history->photo_proof)
                <a href="{{ asset('storage/' . $history->photo_proof) }}" 
                   target="_blank" class="text-blue-600 hover:underline">
                    <i class="fas fa-image mr-1"></i> Lihat Bukti
                </a>
            @else
                <span class="text-gray-400">Tidak ada bukti</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="py-6 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center py-8">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="mt-4 text-lg font-medium">Belum ada riwayat pengiriman</p>
                <p class="mt-1 text-sm text-gray-500">Riwayat pengiriman kamu akan muncul di sini</p>
            </div>
        </td>
    </tr>
@endforelse