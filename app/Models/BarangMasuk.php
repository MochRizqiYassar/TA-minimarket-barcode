<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_barang_masuk';

    protected $fillable = [
        'id_barang',
        'id_kulakan',
        'jumlah',
        'tanggal_masuk',
        'tanggal_expired',
        'nama_barang',
        'harga_beli',
    ];

    protected $casts = [
        'tanggal_masuk'    => 'date',
        'tanggal_expired'  => 'date',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function kulakan(): BelongsTo
    {
        return $this->belongsTo(Kulakan::class, 'id_kulakan', 'id_kulakan');
    }
}
