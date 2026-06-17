<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailKulakan extends Model
{
    protected $table      = 'detail_kulakan';
    protected $primaryKey = 'id_detail_kulakan';

    // [FIX #9] Tambahkan kolom snapshot ke fillable agar mass assignment tidak diblokir
    protected $fillable = [
        'id_kulakan',
        'id_barang',
        'id_tipe_barang',
        'banyak',
        'harga_satuan',
        'subtotal',
        'nama_barang',
        'harga_satuan_snapshot',
    ];

    public function kulakan(): BelongsTo
    {
        return $this->belongsTo(Kulakan::class, 'id_kulakan', 'id_kulakan');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function tipeBarang(): BelongsTo
    {
        return $this->belongsTo(TipeBarang::class, 'id_tipe_barang', 'id_tipe_barang');
    }
}
