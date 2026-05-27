<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';

    protected $fillable = [
    'tanggal_penjualan',
    'id_user',
    'total_harga',
    'status',
];

    protected $casts = ['tanggal_penjualan' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
