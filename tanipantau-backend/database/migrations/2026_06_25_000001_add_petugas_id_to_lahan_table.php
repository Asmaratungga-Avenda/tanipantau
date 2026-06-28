<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('lahan', 'petugas_id')) {
            Schema::table('lahan', function (Blueprint $table) {
                $table->foreignId('petugas_id')
                    ->nullable()
                    ->after('petani_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lahan', 'petugas_id')) {
            Schema::table('lahan', function (Blueprint $table) {
                $table->dropForeign(['petugas_id']);
                $table->dropColumn('petugas_id');
            });
        }
    }
};
