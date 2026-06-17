<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_kulakan', function (Blueprint $table) {
            $table->string('nama_barang')->nullable()->after('subtotal');
            $table->decimal('harga_satuan_snapshot', 15, 2)->nullable()->after('nama_barang');
        });
    }

    public function down(): void
    {
        Schema::table('detail_kulakan', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'harga_satuan_snapshot']);
        });
    }
};
