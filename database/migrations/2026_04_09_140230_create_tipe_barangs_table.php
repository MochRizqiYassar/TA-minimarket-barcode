<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipe_barang', function (Blueprint $table) {
            $table->id('id_tipe_barang');
            $table->string('nama_tipe'); // rentengan, box, satuan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_barang');
    }
};
