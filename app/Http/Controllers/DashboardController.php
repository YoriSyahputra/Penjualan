<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\GambarProduk;
use App\Models\Cart;
use App\Models\Store;
use App\Models\SellerWallet;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil ID seller yang lagi login
        $userId = Auth::id();
        $store = Store::where('user_id', $userId)->first();

        if (!$store) {
            return redirect()->route('store.create')->with('error', 'Kamu belum punya toko, yuk bikin dulu!');
        }
        
        $storeId = $store->id;
        
        // Get selected month or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedTimeRange = $request->get('time_range', 7); // Default 7 days
        $wallet = SellerWallet::where('store_id', $storeId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;
        
        // Mengambil total stock dari tabel Products yang punya seller ini aja
        $totalStock = Product::where('store_id', $storeId)
                ->where('is_active', true)
                ->sum('stock');

        // Mengambil data metrics dengan parameter dinamis
        $dailySales = $this->getDailySales($storeId);
        $monthlySales = $this->getMonthlySales($storeId);
        $last7DaysSales = $this->getLast7DaysSales($storeId);
        $selectedMonthSales = $this->getSelectedMonthSales($storeId, $selectedMonth);
        $topProducts = $this->getTopSellingProducts($storeId);
        $selectedYear = $request->get('year', Carbon::now()->format('Y'));
        
        // Dapatkan list bulan untuk dropdown selector
        $availableMonths = $this->getAvailableMonths($storeId);

        $stockAlerts = Product::where('store_id', $storeId)
            ->where('is_active', true)
            ->whereNotNull('stock_alert')
            ->whereRaw('stock <= stock_alert')
            ->select('id', 'name', 'stock', 'stock_alert')
            ->get();

        $stockAlertsCount = $stockAlerts->count();
        if ($stockAlertsCount > 0) {
            $firstAlert = $stockAlerts->first();
            $remainingCount = $stockAlertsCount - 1;
            $stockAlerts = [
                [
                    'type' => 'warning',
                    'id' => 'stock-summary',
                    'name' => $firstAlert->name,
                    'count' => $remainingCount,
                    'stock' => $firstAlert->stock,
                    'stock_alert' => $firstAlert->stock_alert
                ]
            ];
        } else {
            $stockAlerts = [];
        }

        $newOrders = Order::where('store_id', $storeId)
            ->where('status_order', 'pending')
            ->whereNotNull('paid_at')
            ->select('id', 'order_number', 'total', 'paid_at')
            ->orderBy('paid_at', 'desc')
            ->get();

        $newOrdersCount = $newOrders->count();
        if ($newOrdersCount > 0) {
            $firstOrder = $newOrders->first();
            $remainingCount = $newOrdersCount - 1;
            $newOrders = [
                [
                    'type' => 'success',
                    'id' => 'order-summary',
                    'order_number' => $firstOrder->order_number,
                    'total' => $firstOrder->total,
                    'count' => $remainingCount
                ]
            ];
        } else {
            $newOrders = [];
        }

        return view('dashboard.dashboard', compact(
            'totalStock',
            'dailySales',
            'monthlySales',
            'last7DaysSales',
            'selectedMonthSales',
            'topProducts',
            'availableMonths',
            'selectedMonth',
            'selectedTimeRange',
            'walletBalance',
            'selectedYear',
            'stockAlerts',  
            'newOrders'      
        ));  
    }

    /**
     * Mendapatkan data barang yang dijual hari ini (daily sales) berdasarkan sold_count
     * 
     * @param int $storeId
     * @return array
     */
    private function getDailySales($storeId)
    {
        // Ambil data produk untuk toko ini
        $productData = Product::where('store_id', $storeId)
            ->where('is_active', true)
            ->get();
        
        // Hitung total sold_count untuk semua produk
        $totalSoldCount = $productData->sum('sold_count');
        
        // Hitung total revenue (harga produk * sold_count)
        $totalRevenue = $productData->sum(function($product) {
            return $product->price * $product->sold_count;
        });
        
        // Jumlah produk unik yang terjual (yang punya sold_count > 0)
        $uniqueProductsSold = $productData->where('sold_count', '>', 0)->count();
        
        return [
            'products' => $productData,
            'total_items_sold' => $totalSoldCount, // Ini yang bakal dipake di view
            'total_revenue' => $totalRevenue,
            'unique_products_sold' => $uniqueProductsSold
        ];
    }

    /**
     * Mendapatkan data barang yang dijual bulan ini (monthly sales) berdasarkan sold_count
     * 
     * @param int $storeId
     * @return array
     */
    private function getMonthlySales($storeId)
    {
        // Ambil data produk untuk toko ini
        $productData = Product::where('store_id', $storeId)
            ->where('is_active', true)
            ->get();
        
        // Hitung total sold_count untuk semua produk
        $totalSoldCount = $productData->sum('sold_count');
        
        // Hitung total revenue (harga produk * sold_count)
        $totalRevenue = $productData->sum(function($product) {
            return $product->price * $product->sold_count;
        });
        
        // Menghitung jumlah produk unik yang terjual (dengan sold_count > 0)
        $uniqueProductsSold = $productData->where('sold_count', '>', 0)->count();
        
        // Menghitung rata-rata penjualan per hari dalam bulan ini
        $daysInMonth = Carbon::now()->daysInMonth;
        $avgSalesPerDay = $daysInMonth > 0 ? $totalSoldCount / $daysInMonth : 0;
        
        return [
            'products' => $productData,
            'total_items_sold' => $totalSoldCount, // Ini yang bakal dipake di view
            'total_revenue' => $totalRevenue,
            'unique_products_sold' => $uniqueProductsSold,
            'avg_sales_per_day' => $avgSalesPerDay
        ];
    }
    private function getYearlySales($storeId, $year)
{
    return OrderItem::whereHas('product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.status_order', '!=', 'cancelled')
        ->whereYear('orders.paid_at', $year)
        ->select(
            DB::raw('MONTH(orders.paid_at) as month'),
            DB::raw('DATE_FORMAT(orders.paid_at, "%b") as month_name'),
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
        )
        ->groupBy('month', 'month_name')
        ->orderBy('month')
        ->get();
}

    /**
     * Mendapatkan data barang yang dijual 7 hari terakhir berdasarkan produk
     * 
     * @param int $storeId
     * @return array
     */
    private function getLast7DaysSales($storeId)
{
    // Ambil data harian dari completed_at
    $dailyData = OrderItem::whereHas('product', function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.status_order', '!=', 'cancelled')
        ->whereDate('orders.paid_at', '>=', Carbon::now()->subDays(7))
        ->select(
            DB::raw('DATE(orders.paid_at) as date'),
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'),
            DB::raw('COUNT(DISTINCT order_items.product_id) as unique_products')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Ambil data produk yang terjual selama 7 hari terakhir berdasarkan completed_at
    $productData = Product::where('products.store_id', $storeId)
        ->select(
            'products.id',
            'products.name',
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as week_sold'),
            DB::raw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as total_revenue')
        )
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('orders', function($join) {
            $join->on('order_items.order_id', '=', 'orders.id')
                ->where('orders.status_order', '!=', 'cancelled')
                ->whereDate('orders.paid_at', '>=', Carbon::now()->subDays(7));
        })
        ->groupBy('products.id', 'products.name')
        ->get();

    // Urutkan produk berdasarkan penjualan tertinggi
    $sortedProductData = $productData->sortByDesc('week_sold')->values();

    // Hitung total keseluruhan
    $totalWeekSold = $dailyData->sum('total_sold');
    $totalRevenue = $dailyData->sum('total_revenue');

    // Isi data harian yang kosong
    $completeData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $dayData = $dailyData->firstWhere('date', $date);

        $completeData[] = [
            'date' => $date,
            'day_name' => Carbon::parse($date)->format('D'),
            'total_sold' => $dayData ? $dayData->total_sold : 0,
            'total_revenue' => $dayData ? $dayData->total_revenue : 0,
            'unique_products' => $dayData ? $dayData->unique_products : 0
        ];
    }

    return [
        'daily_data' => $completeData,
        'product_data' => $sortedProductData,
        'week_items_sold' => $totalWeekSold,
        'total_revenue' => $totalRevenue,
        'avg_daily_sales' => $totalWeekSold / 7
    ];
}

    
    /**
     * Mendapatkan data penjualan untuk bulan tertentu dalam format YYYY-MM
     * 
     * @param int $storeId
     * @param string $yearMonth Format: YYYY-MM
     * @return \Illuminate\Support\Collection
     */
    private function getSelectedMonthSales($storeId, $yearMonth)
    {
        list($year, $month) = explode('-', $yearMonth);
    
        return OrderItem::whereHas('product', function($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status_order', '!=', 'cancelled')
            ->whereYear('orders.paid_at', $year)
            ->whereMonth('orders.paid_at', $month)
            ->select(
                DB::raw('DAY(orders.paid_at) as day'),
                DB::raw('DATE(orders.paid_at) as date'),
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('day', 'date')
            ->orderBy('day')
            ->get();
    }
    
    /**
     * Mendapatkan list bulan yang tersedia untuk data penjualan
     * 
     * @param int $storeId
     * @return \Illuminate\Support\Collection
     */
    private function getAvailableMonths($storeId)
    {
        return OrderItem::whereHas('product', function($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('DATE_FORMAT(created_at, "%b %Y") as month_name')
            )
            ->groupBy('month_key', 'month_name')
            ->orderBy('month_key', 'desc')
            ->get();
    }

    /**
     * Mendapatkan top selling products berdasarkan sold_count di model Product
     * dan jumlah terjual di OrderItems
     * 
     * @param int $storeId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopSellingProducts($storeId, $limit = 5)
{
    return Product::where('store_id', $storeId)
        ->select(
            'products.id',
            'products.name',
            'products.stock',
            'products.price',
            'products.sold_count',
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as items_sold')
        )
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->groupBy('products.id', 'products.name', 'products.stock', 'products.price', 'products.sold_count')
        ->orderBy('products.sold_count', 'desc') // Urutkan berdasarkan sold_count
        ->limit($limit)
        ->get();
}


    // Method untuk mengupdate stock produk (pastikan cuma bisa update stock produk sendiri)
    public function updateStock($productId, $newStock)
    {
        $storeId = Auth::id();
        $product = Product::where('id', $productId)
                         ->where('store_id', $storeId)
                         ->firstOrFail();
        
        // Simpan stock awal sebelum update
        if (!$product->stock_awal) {
            $product->stock_awal = $product->stock;
        }
        
        $product->stock = $newStock;
        $product->save();

        return response()->json([
            'success' => true,
            'new_stock' => $product->stock
        ]);
    }
    /**
     * Export daily sales data to Excel
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportDailySales(Request $request)
    {
        $userId = Auth::id();
        $store = Store::where('user_id', $userId)->first();
        
        if (!$store) {
            return redirect()->route('dashboard.index')->with('error', 'Toko tidak ditemukan');
        }
        
        $days = $request->get('days', 7);
        
        return \Excel::download(new \App\Exports\DailySalesExport($store->id, $days), 'daily-sales-last-' . $days . '-days.xlsx');
    }

    /**
     * Export monthly sales data to Excel
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportMonthlySales(Request $request)
    {
        $userId = Auth::id();
        $store = Store::where('user_id', $userId)->first();
        
        if (!$store) {
            return redirect()->route('dashboard.index')->with('error', 'Toko tidak ditemukan');
        }
        
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        
        return \Excel::download(
            new \App\Exports\MonthlySalesExport($store->id, $selectedMonth, $store->store_name), 
            'monthly-sales-' . $selectedMonth . '.xlsx'
        );
    }
    public function exportYearlySales(Request $request)
{
    $userId = Auth::id();
    $store = Store::where('user_id', $userId)->first();
    
    if (!$store) {
        return redirect()->route('dashboard.index')->with('error', 'Toko tidak ditemukan');
    }
    
    $selectedYear = $request->get('year', Carbon::now()->format('Y'));
    
    return \Excel::download(
        new \App\Exports\YearlySalesExport($store->id, $selectedYear, $store->store_name), 
        'yearly-sales-' . $selectedYear . '.xlsx'
    );
}

}