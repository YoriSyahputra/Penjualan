@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Orders Saya</h1>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <form id="filterForm" method="GET" action="{{ route('dashboard.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Cari Order</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="searchInput" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nomor/nama..." 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition duration-200"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tampilkan Per Halaman</label>
                <select 
                    name="per_page" 
                    id="perPageSelect" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                >
                    @foreach([10, 20, 50, 100] as $number)
                        <option 
                            value="{{ $number }}" 
                            {{ request('per_page', 10) == $number ? 'selected' : '' }}
                        >
                            {{ $number }} Orders
                        </option>
                    @endforeach 
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input 
                    type="date" 
                    name="date_from" 
                    value="{{ request('date_from') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input 
                    type="date" 
                    name="date_to" 
                    value="{{ request('date_to') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                >
            </div>
        </form>
    </div>

    <!-- Notification Area -->
    <div id="notification" class="hidden mb-4"></div>

    <!-- Orders Table Container -->
    <div id="ordersTableContainer">
        @include('dashboard.orders.table', ['orders' => $orders])
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const ordersTableContainer = document.getElementById('ordersTableContainer');
    const notification = document.getElementById('notification');
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('perPageSelect');
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    
    function showNotification(message, type = 'success') {
        notification.innerHTML = `
            <div class="p-4 rounded ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${message}
            </div>
        `;
        notification.classList.remove('hidden');
        setTimeout(() => notification.classList.add('hidden'), 3000);
    }

    function loadOrders(page = 1) {
        const formData = new FormData(filterForm);
        formData.append('page', page);

        // Validasi tanggal
        if (dateFromInput.value && dateToInput.value) {
            const dateFrom = new Date(dateFromInput.value);
            const dateTo = new Date(dateToInput.value);
            
            if (dateFrom > dateTo) {
                showNotification('Tanggal dari harus kurang dari atau sama dengan tanggal sampai!', 'error');
                return;
            }
        }

        ordersTableContainer.innerHTML = `
            <div class="flex justify-center items-center p-10">
                <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-blue-500"></div>
            </div>
        `;

        fetch(`{{ route('dashboard.orders.index') }}?${new URLSearchParams(formData).toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            ordersTableContainer.innerHTML = html;
            setupPaginationLinks();
            history.pushState(null, '', window.location.pathname + '?' + new URLSearchParams(formData).toString());
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Gagal memuat data. Coba lagi!', 'error');
        });
    }

    function setupPaginationLinks() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page');
                loadOrders(page);
            });
        });
    }

    // Debounce search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadOrders(1), 500);
    });

    // Event Listeners untuk filter
    perPageSelect.addEventListener('change', () => loadOrders(1));
    
    dateFromInput.addEventListener('change', () => loadOrders(1));
    dateToInput.addEventListener('change', () => loadOrders(1));

    setupPaginationLinks();
});
</script>
@endpush
@endsection