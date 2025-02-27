<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('order_number')->unique();
        $table->string('payment_method');
        $table->string('payment_code')->nullable();
        $table->string('shipping_method');
        $table->decimal('subtotal', 12, 2);
        $table->decimal('shipping_fee', 8, 2);
        $table->decimal('service_fee', 8, 2);
        $table->decimal('total', 12, 2);
        $table->string('address');
        $table->foreignId('address_id')->nullable()->constrained('addresses');
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
