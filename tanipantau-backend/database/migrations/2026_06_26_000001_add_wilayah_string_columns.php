<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add string columns to petani
        Schema::table('petani', function (Blueprint $table) {
            if (!Schema::hasColumn('petani', 'kabupaten')) {
                $table->string('kabupaten', 255)->nullable()->after('provinsi');
            }
        });

        // Add string columns to lahan
        Schema::table('lahan', function (Blueprint $table) {
            if (!Schema::hasColumn('lahan', 'kabupaten')) {
                $table->string('kabupaten', 255)->nullable()->after('provinsi');
            }
            if (!Schema::hasColumn('lahan', 'kecamatan')) {
                $table->string('kecamatan', 255)->nullable()->after('kabupaten');
            }
            if (!Schema::hasColumn('lahan', 'desa')) {
                $table->string('desa', 255)->nullable()->after('kecamatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->dropColumn(['kabupaten']);
        });

        Schema::table('lahan', function (Blueprint $table) {
            $table->dropColumn(['kabupaten', 'kecamatan', 'desa']);
        });
    }
};
