<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';

    protected $fillable = ['nama_supplier', 'kontak', 'alamat'];

    public function kulakans(): HasMany
    {
        return $this->hasMany(Kulakan::class, 'id_supplier', 'id_supplier');
    }
}
