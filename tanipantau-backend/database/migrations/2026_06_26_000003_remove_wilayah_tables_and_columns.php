<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove FK columns from petani
        Schema::table('petani', function (Blueprint $table) {
            if (Schema::hasColumn('petani', 'kabupaten_id')) {
                $table->dropForeign(['kabupaten_id']);
                $table->dropColumn('kabupaten_id');
            }
            if (Schema::hasColumn('petani', 'kecamatan_id')) {
                $table->dropForeign(['kecamatan_id']);
                $table->dropColumn('kecamatan_id');
            }
            if (Schema::hasColumn('petani', 'desa_id')) {
                $table->dropForeign(['desa_id']);
                $table->dropColumn('desa_id');
            }
        });

        // Remove FK columns from lahan
        Schema::table('lahan', function (Blueprint $table) {
            if (Schema::hasColumn('lahan', 'kabupaten_id')) {
                $table->dropForeign(['kabupaten_id']);
                $table->dropColumn('kabupaten_id');
            }
            if (Schema::hasColumn('lahan', 'kecamatan_id')) {
                $table->dropForeign(['kecamatan_id']);
                $table->dropColumn('kecamatan_id');
            }
            if (Schema::hasColumn('lahan', 'desa_id')) {
                $table->dropForeign(['desa_id']);
                $table->dropColumn('desa_id');
            }
        });

        // Drop wilayah tables
        Schema::dropIfExists('desas');
        Schema::dropIfExists('kecamatans');
        Schema::dropIfExists('kabupatens');
    }

    public function down(): void
    {
        // Cannot restore dropped tables automatically
    }
};
