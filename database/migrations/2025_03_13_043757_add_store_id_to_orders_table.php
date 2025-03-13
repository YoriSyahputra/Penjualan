<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // First add the column as nullable
            $table->unsignedBigInteger('store_id')->nullable()->after('user_id');
        });
        
        // Use a separate call to add the foreign key
        // This gives you a chance to populate the data first
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });
    }
}