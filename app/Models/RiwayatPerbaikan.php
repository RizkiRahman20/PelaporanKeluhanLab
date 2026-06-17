<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerbaikan extends Model
{
    protected $table = 'riwayat_perbaikans';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'tgl_ubah',
        'id_user',
        'catatan_rw',
        'id_perbaikan',
    ];

    protected $casts = [
        'tgl_ubah' => 'date',
    ];

    public function perbaikan(): BelongsTo
    {
        return $this->belongsTo(Perbaikan::class, 'id_perbaikan', 'id_perbaikan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}