<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('barcode')->unique();
            $table->string('nama_barang');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('restrict');
            $table->foreignId('id_tipe_barang')->constrained('tipe_barang', 'id_tipe_barang')->onDelete('restrict');
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
