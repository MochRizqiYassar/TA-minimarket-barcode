<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BarangModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_etalase_habis()
    {
        $barang = Barang::factory()->create([
            'stok' => 0,
            'stok_minimum_etalase' => 5,
        ]);

        $this->assertEquals('habis', $barang->status_etalase);
    }

    public function test_status_etalase_menipis()
    {
        $barang = Barang::factory()->create([
            'stok' => 3,
            'stok_minimum_etalase' => 5,
        ]);

        $this->assertEquals('menipis', $barang->status_etalase);
    }

    public function test_status_etalase_aman()
    {
        $barang = Barang::factory()->create([
            'stok' => 10,
            'stok_minimum_etalase' => 5,
        ]);

        $this->assertEquals('aman', $barang->status_etalase);
    }
}
