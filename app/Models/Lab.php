<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    protected $table = 'labs';
    protected $primaryKey = 'id_lab';

    protected $fillable = [
        'kd_lab',
        'nm_lab',
        'status_lab',
    ];

    public function penugasanUserLabs(): HasMany
    {
        return $this->hasMany(PenugasanUserLab::class, 'id_lab', 'id_lab');
    }

    public function laporanKeluhans(): HasMany
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_lab', 'id_lab');
    }
}