<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kulakan extends Model
{
    protected $table = 'kulakan';
    protected $primaryKey = 'id_kulakan';

    protected $fillable = ['id_supplier', 'tanggal_kulakan', 'status', 'total_harga'];

    protected $casts = ['tanggal_kulakan' => 'date'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function detailKulakans(): HasMany
    {
        return $this->hasMany(DetailKulakan::class, 'id_kulakan', 'id_kulakan');
    }

    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_kulakan', 'id_kulakan');
    }
}
