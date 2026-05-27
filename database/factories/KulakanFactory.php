<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class KulakanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_supplier' => Supplier::factory(),
            'tanggal_kulakan' => now(),
            'status' => 'approved',
            'total_harga' => 10000,
        ];
    }
}
