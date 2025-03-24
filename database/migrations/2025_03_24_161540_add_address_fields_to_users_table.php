<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('alamat_lengkap')->nullable();
            $table->text('provinsi')->nullable();
            $table->text('kota')->nullable();
            $table->text('kecamatan')->nullable();
            $table->string('kode_pos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['alamat_lengkap', 'provinsi', 'kota', 'kecamatan', 'kode_pos']);
        });
    }
};
