<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kulakan', function (Blueprint $table) {
            $table->id('id_kulakan');
            $table->foreignId('id_supplier')->constrained('supplier', 'id_supplier')->onDelete('restrict');
            $table->date('tanggal_kulakan');
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kulakan');
    }
};
