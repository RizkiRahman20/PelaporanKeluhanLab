<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbaikans', function (Blueprint $table) {
            $table->id('id_perbaikan');
            $table->enum('status_perbaikan', [
                'antrean',
                'dikerjakan',
                'menunggu_sparepart',
                'selesai',
            ])->default('antrean');
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('ft_perbaikan')->nullable();
            $table->text('catatan_pbk')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->enum('app_validasi', ['menunggu', 'divalidasi', 'dikembalikan'])->default('menunggu');

            $table->string('id_laporan', 30);
            $table->foreign('id_laporan')
                ->references('no_laporan')
                ->on('laporan_keluhans')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index(['status_perbaikan', 'app_validasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};