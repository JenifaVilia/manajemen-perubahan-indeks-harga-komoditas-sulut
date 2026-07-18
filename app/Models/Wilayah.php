<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $fillable = [
        'kode_wilayah',
        'nama_wilayah',
        'tipe',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

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

    public function getNamaLengkapAttribute(): string
    {
        return ucfirst($this->tipe) . ' ' . $this->nama_wilayah;
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKabupatenKota($query)
    {
        return $query->whereIn('tipe', ['kabupaten', 'kota']);
    }
}
