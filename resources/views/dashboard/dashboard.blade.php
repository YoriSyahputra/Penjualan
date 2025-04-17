<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Title -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard Overview</h2>
        <div class="flex space-x-3">

        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Stock</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalStock) }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-50">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Today's Sales</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($dailySales['total_items_sold']) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($dailySales['total_revenue']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-50">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">This Month's Sales</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($monthlySales['total_items_sold']) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($monthlySales['total_revenue']) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-50">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Saldo Wallet</h3>
                    <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($walletBalance) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Daily Sales (Last 7 Days)</h3>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('dashboard.export.daily_sales', ['days' => 7]) }}"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Excel
                    </a>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Sales</h3>
                <div class="flex items-center space-x-2">
                        <a href="{{ route('dashboard.export.monthly_sales', ['month' => $selectedMonth]) }}"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Excel
                        </a>
                        <a href="{{ route('dashboard.export.yearly_sales', ['year' => $selectedYear ?? date('Y')]) }}"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-md text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Yearly
                        </a>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <select id="monthSelector" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($availableMonths as $month)
                                <option value="{{ $month->month_key }}" {{ $selectedMonth == $month->month_key ? 'selected' : '' }}>
                                    {{ $month->month_name }}
                                </option>
                            @endforeach
                        </select>
                        <select id="yearSelector" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 5; // 5 tahun ke belakang
                            @endphp
                            @for($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ ($selectedYear ?? $currentYear) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>            
                </div>
            <div style="height: 300px;">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topProducts as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img src="{{ Storage::url($product->productImages->first()->path_gambar) }}"
                                        alt="{{ $product->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($product->sold_count) }}</div>
                            <div class="text-sm text-gray-500">units</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($product->stock) }}</div>
                            <div class="text-sm text-gray-500">in stock</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Rp {{ number_format($product->price) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->stock > 50)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @elseif($product->stock > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk chart 7 hari terakhir
    const dailyLabels = @json(collect($last7DaysSales['daily_data'])->pluck('date'));
    const dailyData = @json(collect($last7DaysSales['daily_data'])->pluck('total_sold'));
    
    const monthlyLabels = @json(collect($selectedMonthSales)->pluck('date'));
    const monthlyData = @json(collect($selectedMonthSales)->pluck('total_sold'));
    
    // Daily Sales Chart dengan gradient
    const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
    const dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 400);
    dailyGradient.addColorStop(0, 'rgba(79, 70, 229, 0.6)');
    dailyGradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');

    const dailySalesChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Products Sold',
                data: dailyData,
                borderWidth: 3,
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: dailyGradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'white',
                pointBorderColor: 'rgb(79, 70, 229)',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Products Sold: ${context.formattedValue}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [3, 3],
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                }
            }
        }
    });

    // Monthly Sales Chart - Bar chart sesuai bulan dan tahun yang dipilih
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    let monthlySalesChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Products Sold',
                data: monthlyData,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgb(16, 185, 129)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Products Sold: ${context.formattedValue}`;
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [3, 3],
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                }
            }
        }
    });
    
    
    // Event listener for month selector
    document.getElementById('monthSelector').addEventListener('change', function() {
        const selectedYear = document.getElementById('yearSelector').value;
        window.location.href = `{{ route("dashboard.index") }}?month=${this.value}&year=${selectedYear}&time_range={{ $selectedTimeRange }}`;
    });
    document.getElementById('yearSelector').addEventListener('change', function() {
        const selectedMonth = document.getElementById('monthSelector').value;
        window.location.href = `{{ route("dashboard.index") }}?month=${selectedMonth}&year=${this.value}&time_range={{ $selectedTimeRange }}`;
    });
});
</script>
@endsection