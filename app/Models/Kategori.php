<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = ['nama_kategori'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }
}
