<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Kulakan;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangMasukFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_barang' => Barang::factory(),
            'id_kulakan' => Kulakan::factory(),
            'jumlah' => 5,
            'tanggal_masuk' => now(),
        ];
    }
}
