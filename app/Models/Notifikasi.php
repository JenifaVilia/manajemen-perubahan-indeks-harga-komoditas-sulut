<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'referensi_id',
        'referensi_tipe',
        'url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read'  => 'boolean',
        'read_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function getIconAttribute(): string
    {
        return match($this->tipe) {
            'input_alasan'       => 'pencil-square',
            'approval_komoditas' => 'check-circle',
            'periode_buka'       => 'calendar',
            'periode_tutup'      => 'calendar-x',
            'revisi_alasan'      => 'exclamation-triangle',
            'alasan_disetujui'   => 'check-badge',
            'reminder_deadline'  => 'clock',
            default              => 'bell',
        };
    }

    /**
     * Kirim notifikasi ke satu atau banyak user.
     */
    public static function kirim(
        int|array $userIds,
        string $judul,
        string $pesan,
        string $tipe = 'umum',
        ?int $referensiId = null,
        ?string $referensiTipe = null,
        ?string $url = null
    ): void {
        $ids = is_array($userIds) ? $userIds : [$userIds];
        $now = now();

        $records = array_map(fn($id) => [
            'user_id'        => $id,
            'judul'          => $judul,
            'pesan'          => $pesan,
            'tipe'           => $tipe,
            'referensi_id'   => $referensiId,
            'referensi_tipe' => $referensiTipe,
            'url'            => $url,
            'is_read'        => false,
            'created_at'     => $now,
            'updated_at'     => $now,
        ], $ids);

        self::insert($records);
    }
}
