<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nm_user',
        'email',
        'password',
        'role_user',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string 
    {
        return $this->nm_user ?? 'User';
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status_aktif === 'aktif';
    }

    public function isSPV(): bool
    {
        return str_starts_with($this->role_user, 'spv_');
    }

    public function isSPVKedisiplinan(): bool
    {
        return $this->role_user === 'spv_kedisiplinan';
    }

    public function isAdminLab(): bool
    {
        return $this->role_user === 'admin_lab';
    }

    public function isAsistenLab(): bool
    {
        return in_array($this->role_user, ['admin_lab', 'asisten_lab', 'calon_asisten'], true);
    }

    public function penugasanUserLabs(): HasMany
    {
        return $this->hasMany(PenugasanUserLab::class, 'id_user', 'id_user');
    }

    public function laporanDivalidasi(): HasMany
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_user', 'id_user');
    }

    public function labs()
    {
        return $this->belongsToMany(
            Lab::class,
            'penugasan_user_labs',
            'id_user',
            'id_lab',
            'id_user',
            'id_lab'
        )->withPivot(['id_penugasan', 'status_aktif', 'semester', 'tahun_ajaran']);
    }

    public function activeLabIds()
    {
        // Aman dari ambiguous column karena mengambil dari tabel penugasan_user_labs.
        return $this->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');
    }

    public function activePenugasanIds()
    {
        return $this->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_penugasan');
    }
}