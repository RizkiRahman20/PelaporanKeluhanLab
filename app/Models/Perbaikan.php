<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perbaikan extends Model
{
    protected $table = 'perbaikans';
    protected $primaryKey = 'id_perbaikan';

    protected $fillable = [
        'status_perbaikan',
        'tgl_mulai',
        'tgl_selesai',
        'ft_perbaikan',
        'catatan_pbk',
        'alasan_penolakan',
        'app_validasi',
        'id_laporan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanKeluhan::class, 'id_laporan', 'no_laporan');
    }

    public function riwayatPerbaikans(): HasMany
    {
        return $this->hasMany(RiwayatPerbaikan::class, 'id_perbaikan', 'id_perbaikan');
    }
}