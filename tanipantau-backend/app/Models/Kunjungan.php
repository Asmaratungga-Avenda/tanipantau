<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * Model Kunjungan
 * 
 * Mewakili data kunjungan lapangan ke lahan.
 * Setiap kunjungan dilakukan oleh petugas ke lahan tertentu.
 */
class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';

    protected $fillable = [
        'lahan_id',
        'petugas_id',
        'tanggal_kunjungan',
        'kondisi_lahan',
        'catatan',
        'foto',
        'status_tindak_lanjut'
    ];

    protected $appends = ['foto_url'];

    protected $casts = [
        'tanggal_kunjungan' => 'datetime'
    ];

    /**
     * Relasi: Kunjungan belong to satu Lahan
     * 
     * @return BelongsTo
     */
    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }

    /**
     * Relasi: Kunjungan dilakukan oleh satu User (Petugas)
     * 
     * @return BelongsTo
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Accessor untuk URL foto
     * 
     * @return string|null
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }

        if (Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }

        return null;
    }
}
