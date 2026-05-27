<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BarangMasukModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_expired()
    {
        $barang = BarangMasuk::factory()->create([
            'tanggal_expired' => now()->subDay(),
        ]);

        $this->assertEquals('expired', $barang->status_expired);
    }

    public function test_status_kritis()
    {
        $barang = BarangMasuk::factory()->create([
            'tanggal_expired' => now()->addDays(10),
        ]);

        $this->assertEquals('kritis', $barang->status_expired);
    }

    public function test_status_warning()
    {
        $barang = BarangMasuk::factory()->create([
            'tanggal_expired' => now()->addDays(20),
        ]);

        $this->assertEquals('warning', $barang->status_expired);
    }

    public function test_status_aman()
    {
        $barang = BarangMasuk::factory()->create([
            'tanggal_expired' => now()->addDays(60),
        ]);

        $this->assertEquals('aman', $barang->status_expired);
    }
}
