<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenugasanUserLab extends Model
{
    protected $table = 'penugasan_user_labs';
    protected $primaryKey = 'id_penugasan';

    protected $fillable = [
        'status_aktif',
        'semester',
        'tahun_ajaran',
        'id_user',
        'id_lab',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'id_lab', 'id_lab');
    }

    public function laporanKeluhans(): HasMany
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_penugasan', 'id_penugasan');
    }
}