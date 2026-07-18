<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komoditas extends Model
{
    protected $table = 'komoditas';

    protected $fillable = [
        'kode_komoditas',
        'nama_komoditas',
        'satuan',
        'kelompok',
        'subkelompok',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function dataHargas(): HasMany
    {
        return $this->hasMany(DataHarga::class);
    }

    public function alasanPerubahans(): HasMany
    {
        return $this->hasMany(AlasanPerubahan::class);
    }

    public function komoditasWilayah(): HasMany
    {
        return $this->hasMany(KomoditasWilayah::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
