@extends('layouts.driver')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <div class="mb-6">
        <a href="{{ route('driver.delivery.history') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Detail Pengiriman</h1>
                    <p class="mt-1 text-sm text-gray-600">Nomor Resi: {{ $history->order->nomor_resi ?? 'N/A' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                    @if($history->status == 'delivered') bg-green-100 text-green-800
                    @elseif($history->status == 'failed') bg-red-100 text-red-800
                    @elseif($history->status == 'picked_up') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($history->status) }}
                </span>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 sm:p-6">
            <!-- Customer Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Penerima</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama</label>
                        <p class="text-gray-900">{{ $history->order->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Alamat Lengkap</label>
                        <p class="text-gray-900">
                            {{ $history->order->alamat_lengkap ?? 'N/A' }},
                            {{ $history->order->kecamatan }},
                            {{ $history->order->kota }},
                            {{ $history->order->provinsi }}
                            {{ $history->order->kode_pos }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengiriman</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Kurir</label>
                        <p class="text-gray-900">{{ $history->order->shipping_kurir ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tanggal Pengiriman</label>
                        <p class="text-gray-900">{{ $history->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($history->delivered_at)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tanggal Diterima</label>
                        <p class="text-gray-900">{{ $history->delivered_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Delivery Proof -->
            @if($history->photo_proof)
            <div class="md:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bukti Pengiriman</h2>
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $history->photo_proof) }}" 
                         alt="Bukti Pengiriman" 
                         class="max-w-lg rounded-lg shadow-md cursor-pointer"
                         onclick="openImageModal('{{ asset('storage/' . $history->photo_proof) }}')" />
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImageModal()"></div>
        <div class="relative bg-white rounded-lg overflow-hidden shadow-xl max-w-3xl w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="closeImageModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-1">
                <img id="modalImage" src="" alt="Bukti Pengiriman" class="w-full h-auto">
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection