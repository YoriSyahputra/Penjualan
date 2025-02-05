<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\GambarProduk;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

    class DashboardController extends Controller
    {
        public function index()
        {
            // Mengambil total stock dari tabel Produks
            $totalStock = Product::sum('stock');
            
            // Mengambil data penjualan harian (7 hari terakhir)
            $dailySales = $this->getDailySales();
            
            // Mengambil data penjualan bulanan
            $monthlySales = $this->getMonthlySales();
            
            // Mengambil produk terlaris
            $topProducts = $this->getTopSellingProducts();

            return view('dashboard.dashboard', compact(
                'totalStock',
                'dailySales',
                'monthlySales',
                'topProducts'
            ));
        }

        private function getDailySales()
        {
            return Product::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('SUM(CASE WHEN stock_awal > stock THEN stock_awal - stock ELSE 0 END) as total_sold')
            )
                ->whereDate('updated_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        private function getMonthlySales()
        {
            return Product::select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN stock_awal > stock THEN stock_awal - stock ELSE 0 END) as total_sold')
            )
                ->whereDate('updated_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        private function getTopSellingProducts($limit = 5)
        {
            return Product::select(
                'id',
                'name',
                'stock',
                'price',
                DB::raw('CASE WHEN stock_awal > stock THEN stock_awal - stock ELSE 0 END as total_sold')
            )
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->get();
        }

        // Method untuk mengupdate stock produk
        public function updateStock($productId, $newStock)
        {
            $product = Product::findOrFail($productId);
            
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
    }