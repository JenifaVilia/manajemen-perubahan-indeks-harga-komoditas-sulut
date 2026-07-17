<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomoditasWilayah extends Model
{
    protected $table = 'komoditas_wilayah';

    protected $fillable = [
        'komoditas_id',
        'wilayah_id',
        'status',
        'requested_at',
        'requested_by',
        'alasan_pengajuan',
        'approved_at',
        'approved_by',
        'catatan_approval',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_tambah', 'pending_hapus']);
    }
}
