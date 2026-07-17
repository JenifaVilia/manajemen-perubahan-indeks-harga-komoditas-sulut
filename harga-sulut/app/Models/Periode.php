<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $fillable = [
        'bulan',
        'tahun',
        'tanggal_buka',
        'tanggal_tutup',
        'status',
        'created_by',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
    ];

    // Nama bulan dalam Bahasa Indonesia
    public static array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dataHargas(): HasMany
    {
        return $this->hasMany(DataHarga::class);
    }

    public function alasanPerubahans(): HasMany
    {
        return $this->hasMany(AlasanPerubahan::class);
    }

    public function getNamaAttribute(): string
    {
        return (self::$namaBulan[$this->bulan] ?? '-') . ' ' . $this->tahun;
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function isDeadlineNear(): bool
    {
        if (!$this->tanggal_tutup) return false;
        return now()->diffInDays($this->tanggal_tutup, false) <= 3 && $this->status === 'aktif';
    }
}
