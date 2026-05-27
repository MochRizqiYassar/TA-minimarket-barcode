<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipeBarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_tipe' => fake()->word(),
        ];
    }
}
