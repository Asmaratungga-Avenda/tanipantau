<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Petani
 * 
 * Mewakili data petani yang terdaftar di sistem.
 * Setiap petani dapat memiliki satu atau lebih lahan.
 */
class Petani extends Model
{
    use HasFactory;

    protected $table = 'petani';

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'nomor_hp',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    /**
     * Relasi: Petani memiliki banyak Lahan
     * 
     * @return HasMany
     */
    public function lahan(): HasMany
    {
        return $this->hasMany(Lahan::class, 'petani_id');
    }
}
