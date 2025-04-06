@extends('layouts.driver')

@section('content')
<div class="container px-2 sm:px-4 py-4 mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-lg sm:text-xl font-bold text-white">Riwayat Pengiriman Paket</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('driver.export.delivery.history', ['type' => 'pdf'] + request()->query()) }}" 
                   class="inline-flex items-center px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors duration-150 ease-in-out">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"></path>
                        <path d="M3 8a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('driver.export.delivery.history', ['type' => 'excel'] + request()->query()) }}" 
                   class="inline-flex items-center px-2 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors duration-150 ease-in-out">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                    Excel
                </a>
            </div>
        </div>

        <!-- Filter Section (Collapsible) -->
        <div class="p-4 border-b border-gray-200">
            <button id="toggleFilters" class="w-full flex justify-between items-center mb-2 text-blue-600 font-medium">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter Pengiriman
                </span>
                <svg id="filterArrow" class="w-4 h-4 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <form action="{{ route('driver.delivery.history') }}" method="GET" id="filterForm" class="space-y-3 hidden">
                <div>
                    <label for="searchInput" class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative rounded-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" 
                            value="{{ request('search') }}" 
                            placeholder="Nomor resi atau catatan..." 
                            class="pl-10 block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="startDate" class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="startDate" 
                            value="{{ request('start_date') }}" 
                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="endDate" class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="endDate" 
                            value="{{ request('end_date') }}" 
                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="courierFilter" class="block text-xs font-medium text-gray-700 mb-1">Kurir</label>
                        <select name="courier" id="courierFilter" 
                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kurir</option>
                            <option value="JNE" {{ request('courier') == 'JNE' ? 'selected' : '' }}>JNE</option>
                            <option value="J&T" {{ request('courier') == 'J&T' ? 'selected' : '' }}>J&T</option>
                            <option value="SiCepat" {{ request('courier') == 'SiCepat' ? 'selected' : '' }}>SiCepat</option>
                            <option value="LWExpress" {{ request('courier') == 'LWExpress' ? 'selected' : '' }}>LWExpress</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="statusFilter" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="statusFilter" 
                            class="block w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('driver.delivery.history') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Card-based List View (Mobile) -->
        <div class="block md:hidden">
            @forelse($histories as $index => $history)
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ $history->created_at->format('d M Y') }} • {{ $history->created_at->format('H:i') }}</div>
                            <div class="text-sm font-medium">{{ $history->order->nomor_resi ?? 'N/A' }}</div>
                        </div>
                        @if($history->status == 'delivered')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Delivered
                            </span>
                        @elseif($history->status == 'failed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Failed
                            </span>
                        @elseif($history->status == 'picked_up')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Picked Up
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </div>
                    
                    <div class="text-xs text-gray-500 mb-2">
                        <div class="flex items-start mb-1">
                            <span class="font-medium text-gray-700 w-16">Kurir:</span>
                            <span>{{ $history->order->shipping_kurir ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-start mb-1">
                            <span class="font-medium text-gray-700 w-16">Alamat:</span>
                            <span class="break-words">
                                @if($history->order)
                                    {{ Str::limit($history->order->alamat_lengkap . ', ' . 
                                    $history->order->kecamatan . ', ' . 
                                    $history->order->kota, 70) }}
                                @else
                                    <span class="italic">Alamat tidak tersedia</span>
                                @endif
                            </span>
                        </div>
                        @if($history->notes)
                        <div class="flex items-start">
                            <span class="font-medium text-gray-700 w-16">Catatan:</span>
                            <span>{{ Str::limit($history->notes, 50) }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex justify-between mt-3">
                    @if($history->photo_proof)
                        <button type="button" 
                            class="inline-flex items-center px-2 py-1 border border-blue-500 text-blue-500 rounded-md hover:bg-blue-50 text-xs font-medium focus:outline-none"
                            onclick="openImageModal('{{ asset('storage/' . $history->photo_proof) }}')">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Lihat Foto
                        </button>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Tidak ada bukti
                            </span>
                        @endif

                        
                        <a href="{{ route('driver.delivery.history.detail', $history->id) }}" 
                           class="inline-flex items-center px-2.5 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-base font-medium text-gray-900 mb-1">Belum ada riwayat pengiriman</h3>
                        <p class="text-sm text-gray-500">Riwayat pengiriman kamu akan muncul di sini</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Table Section (Desktop) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Resi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Foto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($histories as $index => $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $histories->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $history->created_at->format('d M Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $history->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $history->order->nomor_resi ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $history->order->shipping_kurir ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($history->status == 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Delivered
                                    </span>
                                @elseif($history->status == 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @elseif($history->status == 'picked_up')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Picked Up
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                @if($history->order)
                                    {{ Str::limit($history->order->alamat_lengkap . ', ' . 
                                    $history->order->kecamatan . ', ' . 
                                    $history->order->kota, 50) }}
                                @else
                                    <span class="text-gray-400 italic">Alamat tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ Str::limit($history->notes, 30) }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($history->photo_proof)
                                <button type="button" 
                                    class="inline-flex items-center px-3 py-1 border border-blue-500 text-blue-500 rounded-md hover:bg-blue-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    onclick="openImageModal('{{ asset('storage/' . $history->photo_proof) }}')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Lihat
                                </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak ada bukti
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('driver.delivery.history.detail', $history->id) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada riwayat pengiriman</h3>
                                    <p class="text-sm text-gray-500">Riwayat pengiriman kamu akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $histories->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeImageModal()"></div>

        <!-- Modal content -->
        <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4 z-10">
                <button type="button" onclick="closeImageModal()" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-1">
                <img id="modalImage" src="" class="w-full h-auto max-h-[70vh]" alt="Bukti Foto">
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log("JS started loading...");
        
        // Check filter elements
        const toggleFilters = document.getElementById('toggleFilters');
        const filterForm = document.getElementById('filterForm');
        const filterArrow = document.getElementById('filterArrow');
        
        if (!toggleFilters) {
            console.error("Element 'toggleFilters' tidak ditemukan!");
            return;
        }
        
        if (!filterForm) {
            console.error("Element 'filterForm' tidak ditemukan! Cek HTML/typo pada ID.");
            // Coba cari dengan ID lain jika ada double ID
            const altFilterForm = document.querySelector('form.space-y-3.hidden');
            if (altFilterForm) {
                console.log("Tapi form dengan class 'space-y-3 hidden' ditemukan!");
            }
            return;
        }
        
        if (!filterArrow) {
            console.error("Element 'filterArrow' tidak ditemukan!");
            return;
        }
        
        console.log("Semua element filter ditemukan ✓");
        
        // Log state awal filter
        console.log("Filter state awal:", {
            hidden: filterForm.classList.contains('hidden'),
            classes: filterForm.className
        });
        
        // Toggle filters
        toggleFilters.addEventListener('click', function() {
            console.log("Toggle filter diklik!");
            filterForm.classList.toggle('hidden');
            filterArrow.classList.toggle('rotate-180');
            console.log("Filter state setelah toggle:", {
                hidden: filterForm.classList.contains('hidden')
            });
        });
        
        // Cek filter values
        const searchInput = document.getElementById('searchInput');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const courierFilter = document.getElementById('courierFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        console.log("Filter values:", {
            search: searchInput ? searchInput.value : 'element tidak ditemukan',
            startDate: startDate ? startDate.value : 'element tidak ditemukan',
            endDate: endDate ? endDate.value : 'element tidak ditemukan',
            courier: courierFilter ? courierFilter.value : 'element tidak ditemukan',
            status: statusFilter ? statusFilter.value : 'element tidak ditemukan'
        });
        
        // Auto show filter if any filter is active
        if (
            (searchInput && searchInput.value) || 
            (startDate && startDate.value) || 
            (endDate && endDate.value) || 
            (courierFilter && courierFilter.value) || 
            (statusFilter && statusFilter.value)
        ) {
            console.log("Ada filter aktif, harusnya tampil form filter");
            filterForm.classList.remove('hidden');
            filterArrow.classList.add('rotate-180');
            
            // Double check
            setTimeout(function() {
                console.log("Filter state setelah auto-show:", {
                    hidden: filterForm.classList.contains('hidden'),
                    display: window.getComputedStyle(filterForm).display
                });
            }, 500);
        } else {
            console.log("Tidak ada filter aktif");
        }
        
        // Untuk cek masalah CSS jika ada
        console.log("CSS display value:", window.getComputedStyle(filterForm).display);
        
    } catch (error) {
        console.error("Error dalam script filter:", error);
    }
});
function openImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}
function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Enable scrolling again
}
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) {
        closeImageModal();
    }
});
</script>
@endsection