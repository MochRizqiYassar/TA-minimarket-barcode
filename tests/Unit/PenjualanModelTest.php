<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenjualanModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_penjualan_memiliki_user()
    {
        $user = User::factory()->create([
            'status' => 'active',
            'role' => 'admin',
        ]);

        $penjualan = Penjualan::factory()->create([
            'id_user' => $user->id,
        ]);

        $this->assertInstanceOf(
            User::class,
            $penjualan->user
        );
    }

    public function test_penjualan_memiliki_detail_penjualan()
    {
        $barang = Barang::factory()->create();

        $penjualan = Penjualan::factory()->create();

        $penjualan->detailPenjualans()->create([
            'id_barang' => $barang->id_barang,
            'jumlah' => 2,
            'harga' => 2000,
            'harga_beli_saat_transaksi' => 1000,
            'subtotal' => 4000,
            'laba_satuan' => 1000,
            'total_laba' => 2000,
            'nama_barang' => $barang->nama_barang,
            'harga_snapshot' => 2000,
        ]);

        $this->assertCount(
            1,
            $penjualan->detailPenjualans
        );
    }

    public function test_status_pending()
    {
        $penjualan = Penjualan::factory()->create([
            'status' => 'pending',
        ]);

        $this->assertEquals(
            'pending',
            $penjualan->status
        );
    }

    public function test_status_approved()
    {
        $penjualan = Penjualan::factory()->create([
            'status' => 'approved',
        ]);

        $this->assertEquals(
            'approved',
            $penjualan->status
        );
    }

    public function test_total_harga_tersimpan()
    {
        $penjualan = Penjualan::factory()->create([
            'total_harga' => 15000,
        ]);

        $this->assertEquals(
            15000,
            $penjualan->total_harga
        );
    }
}
