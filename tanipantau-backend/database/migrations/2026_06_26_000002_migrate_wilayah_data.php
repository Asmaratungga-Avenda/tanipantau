<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate petani: convert kabupaten_id/kecamatan_id/desa_id to string names
        DB::statement("
            UPDATE petani
            LEFT JOIN kabupatens ON petani.kabupaten_id = kabupatens.id
            SET petani.kabupaten = COALESCE(kabupatens.nama, petani.kabupaten, '')
            WHERE petani.kabupaten_id IS NOT NULL
        ");

        DB::statement("
            UPDATE petani
            LEFT JOIN kecamatans ON petani.kecamatan_id = kecamatans.id
            SET petani.kecamatan = COALESCE(kecamatans.nama, petani.kecamatan, '')
            WHERE petani.kecamatan_id IS NOT NULL
        ");

        DB::statement("
            UPDATE petani
            LEFT JOIN desas ON petani.desa_id = desas.id
            SET petani.desa = COALESCE(desas.nama, petani.desa, '')
            WHERE petani.desa_id IS NOT NULL
        ");

        // Migrate lahan: convert kabupaten_id/kecamatan_id/desa_id to string names
        DB::statement("
            UPDATE lahan
            LEFT JOIN kabupatens ON lahan.kabupaten_id = kabupatens.id
            SET lahan.kabupaten = COALESCE(kabupatens.nama, '')
            WHERE lahan.kabupaten_id IS NOT NULL
        ");

        DB::statement("
            UPDATE lahan
            LEFT JOIN kecamatans ON lahan.kecamatan_id = kecamatans.id
            SET lahan.kecamatan = COALESCE(kecamatans.nama, '')
            WHERE lahan.kecamatan_id IS NOT NULL
        ");

        DB::statement("
            UPDATE lahan
            LEFT JOIN desas ON lahan.desa_id = desas.id
            SET lahan.desa = COALESCE(desas.nama, '')
            WHERE lahan.desa_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        // No rollback for data migration
    }
};
