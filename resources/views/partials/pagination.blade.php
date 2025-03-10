@if($allOrders->hasPages())
<div class="mt-8">
    <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            @if($allOrders->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50">
                    Previous
                </span>
            @else
                <a href="{{ $allOrders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 pagination-link">
                    Previous
                </a>
            @endif
            
            @if($allOrders->hasMorePages())
                <a href="{{ $allOrders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 pagination-link">
                    Next
                </a>
            @else
                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50">
                    Next
                </span>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $allOrders->firstItem() ?: 0 }}</span>
                    to
                    <span class="font-medium">{{ $allOrders->lastItem() ?: 0 }}</span>
                    of
                    <span class="font-medium">{{ $allOrders->total() }}</span>
                    results
                </p>
            </div>
            <div>
                {{ $allOrders->appends(request()->query())->links() }}
            </div>
        </div>
    </nav>
</div>
@endif