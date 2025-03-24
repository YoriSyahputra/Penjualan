<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus kolom address lama
            $table->dropColumn('address');
            
            // Tambahkan kolom-kolom address baru
            $table->string('alamat_lengkap');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('kode_pos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus kolom-kolom baru
            $table->dropColumn(['alamat_lengkap', 'provinsi', 'kota', 'kecamatan', 'kode_pos']);
            
            // Kembalikan kolom address lama
            $table->text('address')->nullable();
        });
    }
};