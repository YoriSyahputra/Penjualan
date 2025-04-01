<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLudwigWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('ludwig_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('driver_id')->nullable(); // Buat track driver
            $table->decimal('amount', 15, 2);
            
            // Status paket dari driver
            $table->enum('status_package', [
                'pending',         // Belum diproses
                'pickup',          // Driver sudah pickup barang
                'on_delivery',     // Dalam perjalanan
                'delivered',       // Barang sudah sampai
                'failed_delivery', // Gagal antar
                'returned'         // Barang dikembalikan
            ])->default('pending');
            
            // Status pembayaran
            $table->enum('status_payment', [
                'pending',     // Dana di ludwig_wallet
                'hold',        // Dana ditahan 
                'released',    // Dana sudah di-transfer ke seller
                'cancelled'    // Transaksi dibatalkan
            ])->default('pending');
            
            $table->timestamp('pickup_at')->nullable();
            $table->timestamp('delivery_at')->nullable();
            $table->timestamp('released_at')->nullable();
            
            $table->text('delivery_notes')->nullable(); // Catatan pengiriman
            
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('driver_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ludwig_wallets');
    }
}