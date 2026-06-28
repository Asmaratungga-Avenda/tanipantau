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
        Schema::create('lahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petani_id')->constrained('petani')->onDelete('cascade');
            $table->string('nama_lahan');
            $table->string('komoditas'); // padi, jagung, kopi, teh, dll
            $table->float('luas_lahan'); // dalam hektar
            $table->float('garis_lintang')->nullable();
            $table->float('garis_bujur')->nullable();
            $table->date('tanggal_tanam')->nullable();
            $table->enum('fase_lahan', [
                'Persiapan',
                'Penanaman',
                'Pertumbuhan',
                'Panen',
                'Panen Selesai'
            ])->default('Persiapan');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lahan');
    }
};
