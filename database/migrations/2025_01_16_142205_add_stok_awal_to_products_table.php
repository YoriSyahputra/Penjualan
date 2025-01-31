<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddStokAwalToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, add the column
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_awal')->after('stock')->nullable();
        });

        // Then, in a separate step, update the values
        DB::table('products')
            ->whereNull('stock_awal')
            ->update(['stock_awal' => DB::raw('stock')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_awal');
        });
    }
}