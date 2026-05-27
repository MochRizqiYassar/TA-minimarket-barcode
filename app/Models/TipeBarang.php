<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipeBarang extends Model
{
    use HasFactory;
    protected $table = 'tipe_barang';
    protected $primaryKey = 'id_tipe_barang';

    protected $fillable = ['nama_tipe'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'id_tipe_barang', 'id_tipe_barang');
    }

    public function detailKulakans(): HasMany
    {
        return $this->hasMany(DetailKulakan::class, 'id_tipe_barang', 'id_tipe_barang');
    }
}
