<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_keluhans', function (Blueprint $table) {
            // Menambahkan kolom prodi persis setelah kolom fakultas
            $table->string('prodi_pelapor', 100)->after('fakultas_pelapor')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_keluhans', function (Blueprint $table) {
            $table->dropColumn('prodi_pelapor');
        });
    }
};