<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_keluhans', function (Blueprint $table) {
            $table->string('no_laporan', 30)->primary();
            $table->date('tgl_lapor');
            $table->enum('approval', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->string('nim_pelapor', 20);
            $table->string('nm_pelapor', 100);
            $table->string('fakultas_pelapor', 100);
            $table->enum('kategori', ['PC', 'non_PC']);
            $table->text('catatan_lpr');
            $table->string('file_foto')->nullable();

            // Tambahan sesuai LRS terbaru.
            $table->foreignId('id_lab')
                ->constrained('labs', 'id_lab')
                ->cascadeOnDelete();

            // PIC/SPV yang memvalidasi laporan.
            $table->foreignId('id_user')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            // Admin/asisten lab yang menerima delegasi.
            $table->foreignId('id_penugasan')
                ->nullable()
                ->constrained('penugasan_user_labs', 'id_penugasan')
                ->nullOnDelete();

            // Alasan kalau laporan ditolak oleh PIC/SPV.
            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();

            $table->index(['id_lab', 'approval']);
            $table->index(['id_penugasan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_keluhans');
    }
};