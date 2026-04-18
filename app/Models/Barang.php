<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'barcode',
        'nama_barang',
        'id_kategori',
        'id_tipe_barang',
        'stok',
        'harga_beli',
        'harga_jual',
        'foto'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function tipeBarang(): BelongsTo
    {
        return $this->belongsTo(TipeBarang::class, 'id_tipe_barang', 'id_tipe_barang');
    }

    public function detailKulakans(): HasMany
    {
        return $this->hasMany(DetailKulakan::class, 'id_barang', 'id_barang');
    }

    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang', 'id_barang');
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'id_barang', 'id_barang');
    }
}
