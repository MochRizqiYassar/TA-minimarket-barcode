<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenjualanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tanggal_penjualan' => now(),
            'id_user' => User::factory(),
            'status' => 'pending',
            'total_harga' => 0,
        ];
    }
}
