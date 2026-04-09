<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_kulakan', function (Blueprint $table) {
            $table->id('id_detail_kulakan');
            $table->foreignId('id_kulakan')->constrained('kulakan', 'id_kulakan')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
            $table->foreignId('id_tipe_barang')->constrained('tipe_barang', 'id_tipe_barang')->onDelete('restrict');
            $table->integer('banyak');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kulakan');
    }
};
