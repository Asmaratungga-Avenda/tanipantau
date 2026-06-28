<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->string('provinsi')->default('Jawa Timur')->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->dropColumn('provinsi');
        });
    }
};
