<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Store;

class UpdateProductStoreIds extends Command
{
    protected $signature = 'products:update-store-ids';
    protected $description = 'Update store_id for products agar sesuai dengan store yang dibuat oleh user';

    public function handle()
    {
        // Ambil produk yang belum memiliki store_id, atau jika ingin mengecek semua produk:
        // $products = Product::all();
        $products = Product::whereNull('store_id')->get();
        $this->info("Found {$products->count()} products without store_id.");

        $updated = 0;
        foreach ($products as $product) {
            // Cari store berdasarkan user_id pembuat produk
            $store = Store::where('user_id', $product->user_id)->first();
            if ($store) {
                // Update hanya jika nilai store_id tidak sama (atau null)
                if ($product->store_id != $store->id) {
                    $product->update(['store_id' => $store->id]);
                    $updated++;
                    $this->info("Product ID {$product->id} diupdate ke store_id {$store->id}");
                }
            } else {
                $this->warn("Tidak ditemukan store untuk user ID: {$product->user_id} (product ID: {$product->id})");
            }
        }
        $this->info("Total produk yang diupdate: {$updated}");
    }
}
