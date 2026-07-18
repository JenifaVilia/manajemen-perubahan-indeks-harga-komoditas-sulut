<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'wilayah_id',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login'        => 'datetime',
        ];
    }

    // -------------------------
    // Relationships
    // -------------------------

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function alasanYangDiinput(): HasMany
    {
        return $this->hasMany(AlasanPerubahan::class, 'submitted_by');
    }

    public function alasanYangDireview(): HasMany
    {
        return $this->hasMany(AlasanPerubahan::class, 'reviewed_by');
    }

    // -------------------------
    // Helpers
    // -------------------------

    public function isProvinsi(): bool
    {
        return $this->hasRole('provinsi');
    }

    public function isKabupatenKota(): bool
    {
        return $this->hasRole('kabupaten_kota');
    }

    public function getUnreadNotifCountAttribute(): int
    {
        return $this->notifikasis()->where('is_read', false)->count();
    }

    public function getUnreadNotifsAttribute()
    {
        return $this->notifikasis()
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
    }
}
