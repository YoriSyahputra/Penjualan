<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->text('alamat_lengkap');
            $table->text('provinsi');
            $table->text('kota');
            $table->text('kecamatan');
            $table->string('kode_pos');
            $table->boolean('is_primary')->default(false);
            $table->string('recipient_name');
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
