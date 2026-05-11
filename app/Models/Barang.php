<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DetailKulakan;

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
        'foto',
        'stok_minimum_etalase',
        'stok_minimum_gudang',
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
    public function getStokGudangAttribute()
{
    return $this->detailKulakans()->sum('banyak');
}
public function getIsStokMenipisAttribute()
{
    return
        $this->stok <= $this->stok_minimum_etalase
        ||
        $this->stok_gudang <= $this->stok_minimum_gudang;
}
public function getStatusEtalaseAttribute()
{
    if ($this->stok == 0) {
        return 'habis';
    }

    if ($this->stok <= $this->stok_minimum_etalase) {
        return 'menipis';
    }

    return 'aman';
}
public function getStatusGudangAttribute()
{
    if ($this->stok_gudang == 0) {
        return 'habis';
    }

    if ($this->stok_gudang <= $this->stok_minimum_gudang) {
        return 'menipis';
    }

    return 'aman';
}
}
