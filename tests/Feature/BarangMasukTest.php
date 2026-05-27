<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kulakan;
use App\Models\BarangMasuk;
use App\Models\DetailKulakan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class BarangMasukTest extends TestCase
{
    use RefreshDatabase;

    protected function admin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_barang_masuk_berhasil()
    {
        $barang = Barang::factory()->create([
            'stok' => 0,
        ]);

        $kulakan = Kulakan::factory()->create();

        DetailKulakan::create([
    'id_barang' => $barang->id_barang,
    'id_kulakan' => $kulakan->id_kulakan,
    'id_tipe_barang' => 1,
    'banyak' => 20,
    'harga_satuan' => 1000,
    'subtotal' => 20000,
]);

        $response = $this->actingAs($this->admin())
            ->post(route('barang-masuk.store'), [
                'details_json' => json_encode([
                    [
                        'id' => $barang->id_barang,
                        'qty' => 5,
                    ]
                ])
            ]);

        $response->assertRedirect(route('barang-masuk.index'));

        $barang->refresh();

        $this->assertEquals(5, $barang->stok);
    }

    public function test_barang_masuk_gagal_jika_stok_tidak_cukup()
    {
        $barang = Barang::factory()->create();

        $kulakan = Kulakan::factory()->create();

        DetailKulakan::create([
    'id_barang' => $barang->id_barang,
    'id_kulakan' => $kulakan->id_kulakan,
    'id_tipe_barang' => 1,
    'banyak' => 2,
    'harga_satuan' => 1000,
    'subtotal' => 2000,
]);

        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->actingAs($this->admin())
            ->post(route('barang-masuk.store'), [
                'details_json' => json_encode([
                    [
                        'id' => $barang->id_barang,
                        'qty' => 10,
                    ]
                ])
            ]);
    }

    public function test_barang_masuk_gagal_jika_tidak_ada_item()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->actingAs($this->admin())
            ->post(route('barang-masuk.store'), [
                'details_json' => null,
            ]);
    }
}
