<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add explicit indexes for performance optimization.
     */
    public function up(): void
    {
        Schema::table('lahan', function (Blueprint $table) {
            $table->index('fase_lahan', 'idx_lahan_fase');
            $table->index('status_aktif', 'idx_lahan_status_aktif');
        });

        Schema::table('kunjungan', function (Blueprint $table) {
            $table->index('tanggal_kunjungan', 'idx_kunjungan_tanggal');
            $table->index('status_tindak_lanjut', 'idx_kunjungan_status');
            $table->index(['petugas_id', 'tanggal_kunjungan'], 'idx_kunjungan_petugas_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lahan', function (Blueprint $table) {
            $table->dropIndex('idx_lahan_fase');
            $table->dropIndex('idx_lahan_status_aktif');
        });

        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropIndex('idx_kunjungan_tanggal');
            $table->dropIndex('idx_kunjungan_status');
            $table->dropIndex('idx_kunjungan_petugas_tanggal');
        });
    }
};
