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

    protected static function booted(): void
    {
        static::saving(function (DataHarga $dataHarga) {
            $dataHarga->autoKalkulasiInflasi();
        });
    }

    /**
     * Hitung nilai inflasi (MtM, YtD, YoY) secara otomatis berdasarkan harga level periode sebelumnya
     */
    public function autoKalkulasiInflasi(): void
    {
        if (!$this->harga_level || (float) $this->harga_level <= 0) {
            return;
        }

        $periode = $this->periode ?? Periode::find($this->periode_id);
        if (!$periode) {
            return;
        }

        // 1. Hitung Inflasi MtM (Month-to-Month) jika belum terisi
        if ($this->inflasi_mtm === null) {
            $prevBulan = $periode->bulan == 1 ? 12 : $periode->bulan - 1;
            $prevTahun = $periode->bulan == 1 ? $periode->tahun - 1 : $periode->tahun;

            $prevPeriode = Periode::where('bulan', $prevBulan)->where('tahun', $prevTahun)->first();
            if ($prevPeriode) {
                $prevHarga = DataHarga::where('periode_id', $prevPeriode->id)
                    ->where('wilayah_id', $this->wilayah_id)
                    ->where('komoditas_id', $this->komoditas_id)
                    ->where('tipe_indeks', $this->tipe_indeks)
                    ->value('harga_level');

                if ($prevHarga && (float) $prevHarga > 0) {
                    $mtm = (((float)$this->harga_level - (float)$prevHarga) / (float)$prevHarga) * 100;
                    $this->inflasi_mtm = round($mtm, 4);
                }
            }
        }

        // 2. Hitung Inflasi YtD (Year-to-Date) jika belum terisi
        if ($this->inflasi_ytd === null) {
            $desPeriode = Periode::where('bulan', 12)->where('tahun', $periode->tahun - 1)->first();
            if ($desPeriode) {
                $desHarga = DataHarga::where('periode_id', $desPeriode->id)
                    ->where('wilayah_id', $this->wilayah_id)
                    ->where('komoditas_id', $this->komoditas_id)
                    ->where('tipe_indeks', $this->tipe_indeks)
                    ->value('harga_level');

                if ($desHarga && (float) $desHarga > 0) {
                    $ytd = (((float)$this->harga_level - (float)$desHarga) / (float)$desHarga) * 100;
                    $this->inflasi_ytd = round($ytd, 4);
                }
            }
        }

        // 3. Hitung Inflasi YoY (Year-on-Year) jika belum terisi
        if ($this->inflasi_yoy === null) {
            $yoyPeriode = Periode::where('bulan', $periode->bulan)->where('tahun', $periode->tahun - 1)->first();
            if ($yoyPeriode) {
                $yoyHarga = DataHarga::where('periode_id', $yoyPeriode->id)
                    ->where('wilayah_id', $this->wilayah_id)
                    ->where('komoditas_id', $this->komoditas_id)
                    ->where('tipe_indeks', $this->tipe_indeks)
                    ->value('harga_level');

                if ($yoyHarga && (float) $yoyHarga > 0) {
                    $yoy = (((float)$this->harga_level - (float)$yoyHarga) / (float)$yoyHarga) * 100;
                    $this->inflasi_yoy = round($yoy, 4);
                }
            }
        }
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
