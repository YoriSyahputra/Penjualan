@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
            <div class="text-sm breadcrumbs text-gray-600">
                <a href="/">Home</a> → <span class="font-medium">Your Orders</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <!-- Search and Filter Form -->
            <div class="mb-6">
                <div class="flex flex-col space-y-3 md:flex-row md:space-y-0 md:space-x-4">
                    <div class="relative flex-grow">
                        <input type="text" 
                               id="searchInput" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Search by Order ID or Product Name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        @if(!empty($search))
                            <button id="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" data-status="all" class="status-filter px-4 py-2 rounded-md border {{ !isset($statusFilter) || $statusFilter == '' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            All
                        </button>
                        <button type="button" data-status="pending" class="status-filter px-4 py-2 rounded-md border {{ isset($statusFilter) && $statusFilter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            Unpaid
                        </button>
                        <button type="button" data-status="paid" class="status-filter px-4 py-2 rounded-md border {{ isset($statusFilter) && $statusFilter == 'paid' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            Paid
                        </button>
                        <button type="button" data-status="cancelled" class="status-filter px-4 py-2 rounded-md border {{ isset($statusFilter) && $statusFilter == 'cancelled' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            Cancelled
                        </button>
                    </div>
                </div>
            </div>

            <!-- Warning Message - Only show if there are unpaid orders -->
            @if($unpaidOrders->count() > 0)
            <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Payment Required</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>You have {{ $unpaidOrders->count() }} unpaid order(s). Please complete the payment within 24 hours to avoid automatic cancellation.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Search Results Info -->
            <div id="searchInfo" class="mb-4 text-sm text-gray-600 {{ empty($search) ? 'hidden' : '' }}">
                Showing results for "<span id="searchTermDisplay">{{ $search }}</span>" (<span id="resultCount">{{ $allOrders->total() }}</span> found)
                <button id="clearResults" class="text-indigo-600 hover:text-indigo-800 ml-2">
                    Clear search
                </button>
            </div>

            <!-- Orders List - This will be replaced via AJAX -->
            <div id="ordersList">
                @include('partials.orders-list')
            </div>

            <!-- Pagination - This will be replaced via AJAX -->
            <div id="pagination">
                @include('partials.pagination')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const clearResults = document.getElementById('clearResults');
        const ordersList = document.getElementById('ordersList');
        const pagination = document.getElementById('pagination');
        const searchInfo = document.getElementById('searchInfo');
        const searchTermDisplay = document.getElementById('searchTermDisplay');
        const resultCount = document.getElementById('resultCount');
        const statusFilters = document.querySelectorAll('.status-filter');
        
        let currentStatus = "{{ $statusFilter ?? '' }}";
        let timer;
        
        // Function to fetch and update orders
        function fetchOrders(search = '', status = '', page = null) {
            const url = new URL("{{ route('ecom.list_order_payment') }}");
            
            if (search) url.searchParams.append('search', search);
            if (status) url.searchParams.append('status', status);
            if (page) url.searchParams.append('page', page);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                ordersList.innerHTML = data.html;
                pagination.innerHTML = data.pagination;
                
                // Update search info
                if (search) {
                    searchInfo.classList.remove('hidden');
                    searchTermDisplay.textContent = search;
                    resultCount.textContent = document.querySelectorAll('#ordersList > div').length - 1; // subtract the empty state div
                } else {
                    searchInfo.classList.add('hidden');
                }
                
                // Reinitialize the event listeners for pagination
                initPaginationListeners();
                
                // Reinitialize the event listeners for cancel forms
                initCancelFormListeners();
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Initialize event listeners for pagination
        function initPaginationListeners() {
            document.querySelectorAll('.pagination-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.href.split('page=')[1];
                    fetchOrders(searchInput.value, currentStatus, page);
                });
            });
        }
        
        // Initialize event listeners for cancel forms
        function initCancelFormListeners() {
            document.querySelectorAll('.cancel-order-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (confirm('Are you sure you want to cancel this order?')) {
                        const formData = new FormData(this);
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Show success message
                            alert(data.message || 'Order cancelled successfully');
                            
                            // Refresh the orders list
                            fetchOrders(searchInput.value, currentStatus);
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            });
        }
        
        // Search input event
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                fetchOrders(this.value, currentStatus);
            }, 500);
        });
        
        // Clear search button
        if (clearSearch) {
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                fetchOrders('', currentStatus);
            });
        }
        
        // Clear results button
        clearResults.addEventListener('click', function() {
            searchInput.value = '';
            fetchOrders('', currentStatus);
        });
        
        // Status filter buttons
        statusFilters.forEach(button => {
            button.addEventListener('click', function() {
                const status = this.dataset.status === 'all' ? '' : this.dataset.status;
                
                // Update visual state
                statusFilters.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'bg-yellow-500', 'bg-green-600', 'bg-red-600', 'text-white');
                    btn.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                });
                
                if (status === 'pending') {
                    this.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                    this.classList.add('bg-yellow-500', 'text-white');
                } else if (status === 'paid') {
                    this.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                    this.classList.add('bg-green-600', 'text-white');
                } else if (status === 'cancelled') {
                    this.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                    this.classList.add('bg-red-600', 'text-white');
                } else {
                    this.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                    this.classList.add('bg-indigo-600', 'text-white');
                }
                
                currentStatus = status;
                fetchOrders(searchInput.value, status);
            });
        });
        
        // Initialize pagination and cancel form listeners
        initPaginationListeners();
        initCancelFormListeners();
    });
</script>
@endpush
@endsection