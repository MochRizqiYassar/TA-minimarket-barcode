<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {

            $table->dropForeign(['id_barang']);

            $table->unsignedBigInteger('id_barang')->nullable()->change();

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
