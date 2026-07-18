<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlasanPerubahan extends Model
{
    protected $fillable = [
        'periode_id',
        'wilayah_id',
        'komoditas_id',
        'alasan',
        'faktor_pendorong',
        'rekomendasi',
        'status',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'catatan_provinsi',
    ];

    protected $casts = [
        'faktor_pendorong' => 'array',
        'submitted_at'     => 'datetime',
        'reviewed_at'      => 'datetime',
    ];

    public static array $faktors = [
        'cuaca_iklim'       => 'Cuaca / Iklim',
        'hari_raya'         => 'Hari Raya / Musiman',
        'pasokan_berkurang' => 'Pasokan Berkurang',
        'pasokan_melimpah'  => 'Pasokan Melimpah',
        'distribusi'        => 'Gangguan Distribusi',
        'harga_bbm'         => 'Kenaikan Harga BBM',
        'permintaan_tinggi' => 'Permintaan Tinggi',
        'permintaan_rendah' => 'Permintaan Rendah',
        'kebijakan_impor'   => 'Kebijakan Impor/Ekspor',
        'produksi_lokal'    => 'Faktor Produksi Lokal',
        'inflasi_global'    => 'Inflasi Global',
        'lainnya'           => 'Lainnya',
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

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFaktorsLabelAttribute(): string
    {
        if (!$this->faktor_pendorong) return '-';
        return collect($this->faktor_pendorong)
            ->map(fn($f) => self::$faktors[$f] ?? $f)
            ->implode(', ');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft'     => ['label' => 'Draft',         'color' => 'gray'],
            'submitted' => ['label' => 'Menunggu Review','color' => 'yellow'],
            'disetujui' => ['label' => 'Disetujui',     'color' => 'green'],
            'revisi'    => ['label' => 'Perlu Revisi',  'color' => 'red'],
            default     => ['label' => '-',              'color' => 'gray'],
        };
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopePerluRevisi($query)
    {
        return $query->where('status', 'revisi');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
