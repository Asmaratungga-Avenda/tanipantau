<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lahan_id')->constrained('lahan')->onDelete('cascade');
            $table->foreignId('petugas_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('tanggal_kunjungan');
            $table->enum('kondisi_lahan', [
                'Sangat Baik',
                'Baik',
                'Sedang',
                'Kurang Baik',
                'Sangat Kurang Baik'
            ])->default('Sedang');
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status_tindak_lanjut', [
                'Aman',
                'Perlu Pemantauan',
                'Perlu Tindakan'
            ])->default('Aman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
