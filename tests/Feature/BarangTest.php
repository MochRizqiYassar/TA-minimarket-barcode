<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\TipeBarang;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    protected function admin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_store_barang_dengan_barcode_manual()
    {
        Storage::fake('public');

        $kategori = Kategori::factory()->create();
        $tipe = TipeBarang::factory()->create();

        $response = $this->actingAs($this->admin())
            ->post(route('barang.store'), [
                'barcode' => 'BRG001',
                'nama_barang' => 'Indomie',
                'id_kategori' => $kategori->id_kategori,
                'id_tipe_barang' => $tipe->id_tipe_barang,
                'harga_beli' => 1000,
                'harga_jual' => 2000,
                'stok_minimum_etalase' => 5,
                'stok_minimum_gudang' => 10,
            ]);

        $response->assertRedirect(route('barang.index'));

        $this->assertDatabaseHas('barang', [
            'barcode' => 'BRG001',
        ]);
    }

    public function test_store_barang_auto_generate_barcode()
    {
        $kategori = Kategori::factory()->create();
        $tipe = TipeBarang::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('barang.store'), [
                'barcode' => '',
                'nama_barang' => 'Sprite',
                'id_kategori' => $kategori->id_kategori,
                'id_tipe_barang' => $tipe->id_tipe_barang,
                'harga_beli' => 1000,
                'harga_jual' => 2000,
                'stok_minimum_etalase' => 5,
                'stok_minimum_gudang' => 10,
            ]);

        $barang = Barang::first();

        $this->assertStringStartsWith('BRG-', $barang->barcode);
    }

    public function test_store_barang_dengan_foto()
    {
        Storage::fake('public');

        $kategori = Kategori::factory()->create();
        $tipe = TipeBarang::factory()->create();

        $foto = UploadedFile::fake()->image('barang.jpg');

        $this->actingAs($this->admin())
            ->post(route('barang.store'), [
                'barcode' => 'BRG002',
                'nama_barang' => 'Teh',
                'id_kategori' => $kategori->id_kategori,
                'id_tipe_barang' => $tipe->id_tipe_barang,
                'harga_beli' => 1000,
                'harga_jual' => 2000,
                'stok_minimum_etalase' => 5,
                'stok_minimum_gudang' => 10,
                'foto' => $foto,
            ]);

        $barang = Barang::first();

        Storage::disk('public')->assertExists($barang->foto);
    }

    public function test_destroy_barang()
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete(route('barang.destroy', $barang));

        $response->assertRedirect();

        $this->assertDatabaseMissing('barang', [
            'id_barang' => $barang->id_barang,
        ]);
    }
}
