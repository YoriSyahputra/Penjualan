<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cancelled_by')->references('id')->on('users');
            $table->enum('canceller_type', ['seller', 'user', 'admin', 'system']);
            $table->text('reason');
            $table->decimal('refunded_amount', 12, 2);
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_cancellations');
    }
};