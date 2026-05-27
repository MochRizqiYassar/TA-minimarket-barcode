<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangMasuk extends Model
{
    use HasFactory;
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
        'barcode',
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
    public function getSisaHariExpiredAttribute()
{
    if (!$this->tanggal_expired) {
        return null;
    }

    return now()->diffInDays(
        $this->tanggal_expired,
        false
    );
}
public function getStatusExpiredAttribute()
{
    if (!$this->tanggal_expired) {
        return 'aman';
    }

    $sisaHari = $this->sisa_hari_expired;

    if ($sisaHari <= 0) {
        return 'expired';
    }

    if ($sisaHari <= 14) {
        return 'kritis';
    }

    if ($sisaHari <= 30) {
        return 'warning';
    }

    return 'aman';
}
}
