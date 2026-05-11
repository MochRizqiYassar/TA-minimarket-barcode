<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {

            $table->integer('stok_minimum_etalase')
                  ->default(5)
                  ->after('stok');

            $table->integer('stok_minimum_gudang')
                  ->default(10)
                  ->after('stok_minimum_etalase');

        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {

            $table->dropColumn([
                'stok_minimum_etalase',
                'stok_minimum_gudang'
            ]);

        });
    }
};
