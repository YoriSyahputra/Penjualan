@extends('layouts.admin') 

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Store Orders</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" action="{{ url()->current() }}" class="flex items-center space-x-2">
        <!-- Input Pencarian -->
        <input 
            type="text" 
            name="search" 
            id="searchInput" 
            value="{{ request('search') }}" 
            placeholder="Search orders..." 
            class="border rounded px-3 py-2 w-64">
        
        <!-- Select Rows Per Page -->
        <select name="per_page" onchange="this.form.submit()" class="border rounded px-3 py-2">
            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
        
        <!-- Tombol Search -->
        <button type="submit" id="searchButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
            </svg>
        </button>
    </form>
</div>

    <div id="ordersTableContainer">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $itemCount }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($storeTotal, 0, ',', '.') }}</td>
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
                        <td colspan="7" class="px-6 py-4 text-center">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('perPage');
    const searchButton = document.getElementById('searchButton');
    const ordersTableContainer = document.getElementById('ordersTableContainer');
    
    // Get base URL from the current location
    const baseUrl = '{{ route('dashboard.orders.index') }}';
    
    // Function to load orders with AJAX
    function loadOrders(page = 1) {
        const searchTerm = searchInput.value;
        const perPage = perPageSelect.value;
        
        // Show loading indicator
        ordersTableContainer.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div><p class="mt-2">Loading...</p></div>';
        
        // Construct URL properly
        const params = new URLSearchParams();
        params.set('page', page);
        params.set('search', searchTerm);
        params.set('per_page', perPage);
        
        // Update URL in browser history
        const url = new URL(window.location);
        url.search = params.toString();
        window.history.pushState({}, '', url);
        
        // Make the AJAX request with properly constructed URL
        fetch(`${baseUrl}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            ordersTableContainer.innerHTML = html;
            setupPaginationLinks();
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            ordersTableContainer.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error loading orders. Please try again.</div>';
        });
    }
    
    // Setup pagination links after each table load
    function setupPaginationLinks() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page') || 1;
                loadOrders(page);
            });
        });
    }
    
    // Event listeners
    searchButton.addEventListener('click', function(e) {
        e.preventDefault();
        loadOrders(1); // Reset to page 1 when searching
    });
    
    // Allow Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loadOrders(1);
        }
    });
    
    // Add debounce for search input to prevent too many requests
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadOrders(1);
        }, 500); // Wait 500ms after typing stops
    });
    
    // Handle per page selection changes
    perPageSelect.addEventListener('change', function() {
        loadOrders(1); // Reset to page 1 when changing items per page
    });
    
    // Initialize pagination links
    setupPaginationLinks();
});
</script>
@endpush
@endsection