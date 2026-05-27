<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = ['nama_kategori'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }
}
