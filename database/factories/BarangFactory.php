<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\TipeBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'barcode' => fake()->unique()->ean13(),
            'nama_barang' => fake()->word(),
            'id_kategori' => Kategori::factory(),
            'id_tipe_barang' => TipeBarang::factory(),
            'stok' => 10,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok_minimum_etalase' => 5,
            'stok_minimum_gudang' => 5,
        ];
    }
}
