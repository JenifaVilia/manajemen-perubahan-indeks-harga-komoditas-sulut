<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataHarga extends Model
{
    protected $fillable = [
        'periode_id',
        'wilayah_id',
        'komoditas_id',
        'tipe_indeks',
        'harga_level',
        'inflasi_mtm',
        'inflasi_ytd',
        'inflasi_yoy',
        'andil_mtm',
        'andil_ytd',
        'andil_yoy',
        'uploaded_by',
        'sumber_file',
    ];

    protected $casts = [
        'harga_level' => 'decimal:4',
        'inflasi_mtm' => 'decimal:4',
        'inflasi_ytd' => 'decimal:4',
        'inflasi_yoy' => 'decimal:4',
        'andil_mtm'   => 'decimal:4',
        'andil_ytd'   => 'decimal:4',
        'andil_yoy'   => 'decimal:4',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function alasanPerubahan()
    {
        return $this->hasMany(AlasanPerubahan::class, 'komoditas_id', 'komoditas_id')
            ->whereColumn('wilayah_id', 'data_hargas.wilayah_id')
            ->whereColumn('periode_id', 'data_hargas.periode_id');
    }

    /**
     * Menentukan apakah perubahan harga dianggap signifikan (threshold: ±1%)
     */
    public function isSignifikan(float $threshold = 1.0): bool
    {
        return abs((float) $this->inflasi_mtm) >= $threshold;
    }

    public function getStatusPerubahanAttribute(): string
    {
        if ((float) $this->inflasi_mtm > 0) return 'naik';
        if ((float) $this->inflasi_mtm < 0) return 'turun';
        return 'stabil';
    }

    public function scopeSignifikan($query, float $threshold = 1.0)
    {
        return $query->where(function($q) use ($threshold) {
            $q->where('inflasi_mtm', '>=', $threshold)
              ->orWhere('inflasi_mtm', '<=', -$threshold);
        });
    }

    public function scopeNaik($query)
    {
        return $query->where('inflasi_mtm', '>', 0);
    }

    public function scopeTurun($query)
    {
        return $query->where('inflasi_mtm', '<', 0);
    }
}
