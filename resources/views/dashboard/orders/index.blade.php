@extends('layouts.admin') 

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Orders Management</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center">
            <div class="relative flex">
                <input type="text" 
                        id="searchInput" 
                        value="{{ request('search') }}" 
                        placeholder="Search orders..." 
                        class="border rounded-l px-3 py-2 w-64">
                <button id="searchButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <label for="perPage" class="text-sm font-medium text-gray-700">Show:</label>
            <select id="perPage" class="border rounded px-2 py-1">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    <div id="ordersTableContainer">
        @include('dashboard.orders.table')
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('perPage');
    const searchButton = document.getElementById('searchButton');
    const ordersTableContainer = document.getElementById('ordersTableContainer');
    
    // Function to load orders with AJAX
    function loadOrders(page = 1) {
        const searchTerm = searchInput.value;
        const perPage = perPageSelect.value;
        
        // Create loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'text-center py-4';
        loadingIndicator.innerHTML = '<div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>';
        
        // Show loading indicator
        ordersTableContainer.innerHTML = '';
        ordersTableContainer.appendChild(loadingIndicator);
        
        // Update URL without refreshing
        const url = new URL(window.location);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
        
        // Make the AJAX request
        fetch(`{{ route('dashboard.orders.index') }}?page=${page}&search=${encodeURIComponent(searchTerm)}&per_page=${perPage}`, {
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
                
                // Scroll to top of table
                ordersTableContainer.scrollIntoView({ behavior: 'smooth' });
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
    
    // Add debounce to search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadOrders(1);
        }, 500);
    });
    
    // Immediate update when per page selection changes
    perPageSelect.addEventListener('change', function() {
        loadOrders(1); // Reset to page 1 when changing items per page
    });
    
    // Initialize pagination links
    setupPaginationLinks();
});
</script>
@endpush
@endsection