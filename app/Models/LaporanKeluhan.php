<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaporanKeluhan extends Model
{
    protected $table = 'laporan_keluhans';
    protected $primaryKey = 'no_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_laporan',
        'tgl_lapor',
        'approval',
        'nim_pelapor',
        'nm_pelapor',
        'fakultas_pelapor',
        'kategori',
        'catatan_lpr',
        'file_foto',
        'id_lab',
        'id_user',
        'id_penugasan',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tgl_lapor' => 'date',
    ];

    public static function generateNomorLaporan(): string
    {
        $prefix = 'LPR-' . date('Ymd') . '-';

        $last = self::where('no_laporan', 'like', $prefix . '%')
            ->orderBy('no_laporan', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_laporan, -4);
            return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'id_lab', 'id_lab');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function penugasan(): BelongsTo
    {
        return $this->belongsTo(PenugasanUserLab::class, 'id_penugasan', 'id_penugasan');
    }

    public function perbaikan(): HasOne
    {
        return $this->hasOne(Perbaikan::class, 'id_laporan', 'no_laporan');
    }
}