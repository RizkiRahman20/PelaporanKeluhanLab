<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_perbaikans', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->date('tgl_ubah');
            $table->text('catatan_rw');
            $table->foreignId('id_perbaikan')
                ->constrained('perbaikans', 'id_perbaikan')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_perbaikans');
    }
};