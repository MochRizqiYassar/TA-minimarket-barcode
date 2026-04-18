<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {

            // 🔥 hapus foreign key lama
            $table->dropForeign(['id_barang']);

            // 🔥 ubah kolom jadi nullable
            $table->unsignedBigInteger('id_barang')->nullable()->change();

            // 🔥 buat ulang FK dengan SET NULL
            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {

            $table->dropForeign(['id_barang']);

            $table->unsignedBigInteger('id_barang')->nullable(false)->change();

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang')
                ->restrictOnDelete();
        });
    }
};
