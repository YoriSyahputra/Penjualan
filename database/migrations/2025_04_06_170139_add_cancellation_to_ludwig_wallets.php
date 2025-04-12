<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ludwig_wallets', function (Blueprint $table) {
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->enum('cancellation_by', ['seller', 'user', 'system'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('ludwig_wallets', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_at', 'cancellation_by']);
        });
    }
};