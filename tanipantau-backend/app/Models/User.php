<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
/**
 * Model User
 * 
 * Mewakili pengguna sistem (Admin, Petugas, Manajer).
 * User dapat melakukan kunjungan lapangan sebagai Petugas.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: User (Petugas) memiliki banyak Kunjungan
     * 
     * @return HasMany
     */
    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'petugas_id');
    }

    /**
     * Relasi: User (Petugas) memiliki banyak Lahan
     * 
     * @return HasMany
     */
    public function lahan(): HasMany
    {
        return $this->hasMany(Lahan::class, 'petugas_id');
    }

    /**
     * Relasi: Riwayat penugasan lahan
     * 
     * @return HasMany
     */
    public function penugasanLahan(): HasMany
    {
        return $this->hasMany(PenugasanLahan::class, 'petugas_id');
    }
}
