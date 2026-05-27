<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Kategori;
use App\Models\TipeBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenjualanTest extends TestCase
{
    use RefreshDatabase;

    protected function admin()
{
    return User::factory()->create([
        'role' => 'kasir',
        'status' => 'active',
    ]);
}

    protected function barang($stok = 10)
    {
        return Barang::factory()->create([
            'stok' => $stok,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
        ]);
    }

    public function test_penjualan_berhasil()
    {
        $barang = $this->barang(10);

        $response = $this->actingAs($this->admin())
            ->post(route('penjualan.store'), [
                'tanggal_penjualan' => now()->format('Y-m-d'),
                'details_json' => json_encode([
                    [
                        'id_barang' => $barang->id_barang,
                        'jumlah' => 2,
                    ]
                ])
            ]);

        $response->assertRedirect(route('penjualan.index'));

        $this->assertDatabaseHas('penjualan', [
            'status' => 'pending',
        ]);
    }

    public function test_penjualan_gagal_jika_tidak_ada_barang()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->actingAs($this->admin())
            ->post(route('penjualan.store'), [
                'tanggal_penjualan' => now()->format('Y-m-d'),
                'details_json' => json_encode([]),
            ]);
    }

    public function test_penjualan_gagal_jika_stok_tidak_cukup()
    {
        $barang = $this->barang(1);

        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->actingAs($this->admin())
            ->post(route('penjualan.store'), [
                'tanggal_penjualan' => now()->format('Y-m-d'),
                'details_json' => json_encode([
                    [
                        'id_barang' => $barang->id_barang,
                        'jumlah' => 10,
                    ]
                ])
            ]);
    }

    public function test_approve_penjualan_berhasil()
    {
        $barang = $this->barang(10);

        $penjualan = Penjualan::factory()->create([
            'status' => 'pending',
        ]);

        $penjualan->detailPenjualans()->create([
            'id_barang' => $barang->id_barang,
            'jumlah' => 2,
            'harga' => 2000,
            'harga_beli_saat_transaksi' => 1000,
            'subtotal' => 4000,
            'laba_satuan' => 1000,
            'total_laba' => 2000,
        ]);

        $response = $this->actingAs($this->admin())
            ->post(route('penjualan.approve', $penjualan));

        $response->assertSessionHas('success');

        $barang->refresh();

        $this->assertEquals(8, $barang->stok);
    }

    public function test_approve_gagal_jika_sudah_approved()
    {
        $penjualan = Penjualan::factory()->create([
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin())
            ->post(route('penjualan.approve', $penjualan));

        $response->assertSessionHas('error');
    }

    public function test_destroy_penjualan_approved_mengembalikan_stok()
{
    $barang = $this->barang(10);

    $penjualan = Penjualan::factory()->create([
        'status' => 'pending',
    ]);

    $penjualan->detailPenjualans()->create([
        'id_barang' => $barang->id_barang,
        'jumlah' => 2,
        'harga' => 2000,
        'harga_beli_saat_transaksi' => 1000,
        'subtotal' => 4000,
        'laba_satuan' => 1000,
        'total_laba' => 2000,
    ]);

    // approve dulu
    $this->actingAs($this->admin())
        ->post(route('penjualan.approve', $penjualan));

    $barang->refresh();

    $this->assertEquals(8, $barang->stok);

    // lalu delete
    $this->actingAs($this->admin())
        ->delete(route('penjualan.destroy', $penjualan));

    $barang->refresh();

    // stok kembali normal
    $this->assertEquals(10, $barang->stok);
}
    public function test_penjualan_json_response()
{
    $barang = $this->barang(10);

    $response = $this->actingAs($this->admin())
        ->postJson(route('penjualan.store'), [
            'tanggal_penjualan' => now()->format('Y-m-d'),
            'details_json' => json_encode([
                [
                    'id_barang' => $barang->id_barang,
                    'jumlah' => 1,
                ]
            ])
        ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => true
        ]);
}
}
