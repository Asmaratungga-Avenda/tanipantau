<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Lahan
 * 
 * Mewakili data lahan/sawah yang dimiliki petani.
 * Setiap petani dapat memiliki satu atau lebih lahan.
 */
class Lahan extends Model
{
    use HasFactory;

    protected $table = 'lahan';

    protected $fillable = [
        'petani_id',
        'petugas_id',
        'nama_lahan',
        'komoditas',
        'luas_lahan',
        'garis_lintang',
        'garis_bujur',
        'tanggal_tanam',
        'fase_lahan',
        'status_aktif',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa'
    ];

    protected $casts = [
        'tanggal_tanam' => 'date',
        'garis_lintang' => 'float',
        'garis_bujur' => 'float',
        'luas_lahan' => 'float',
        'status_aktif' => 'boolean'
    ];

    /**
     * Mapping komoditas ke ikon (emoji atau marker icon)
     */
    public static function getCommodityIcon(string $komoditas): string
    {
        $icons = [
            'Padi' => '🌾',
            'Jagung' => '🌽',
            'Hortikultura' => '🍅'
        ];

        return $icons[$komoditas] ?? '📍';
    }

    /**
     * Relasi: Lahan dimiliki oleh satu Petani
     * 
     * @return BelongsTo
     */
    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'petani_id');
    }

    /**
     * Relasi: Lahan memiliki banyak Kunjungan
     * 
     * @return HasMany
     */
    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'lahan_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function penugasanLahan(): HasMany
    {
        return $this->hasMany(PenugasanLahan::class, 'lahan_id');
    }
}
