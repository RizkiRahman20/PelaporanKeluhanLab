<?php

namespace App\Filament\Pages;

use App\Models\LaporanKeluhan;
use App\Models\Perbaikan;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.monitoring-dashboard';

    protected static bool $isDiscovered = false;

    public function mount(): void
    {
        $user = Auth::user();

        if (in_array($user?->role_user, ['admin_lab', 'asisten_lab'], true)) {
            $this->redirect(AdminDashboard::getUrl());
        }
    }

    protected function assignedLabIds()
    {
        $user = Auth::user();

        if ($user?->isSPVKedisiplinan()) {
            return null;
        }

        return $user?->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');
    }

    protected function laporanQuery(): Builder
    {
        $query = LaporanKeluhan::query();

        $labIds = $this->assignedLabIds();

        if ($labIds !== null) {
            $query->whereIn('id_lab', $labIds);
        }

        return $query;
    }

    protected function perbaikanQuery(): Builder
    {
        $query = Perbaikan::query();

        $labIds = $this->assignedLabIds();

        if ($labIds !== null) {
            $query->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
        }

        return $query;
    }

    public function getViewData(): array
    {
        return [
            'totalLaporan' => (clone $this->laporanQuery())->count(),
            'laporanMenunggu' => (clone $this->laporanQuery())->where('approval', 'menunggu')->count(),
            'laporanDisetujui' => (clone $this->laporanQuery())->where('approval', 'disetujui')->count(),
            'laporanDitolak' => (clone $this->laporanQuery())->where('approval', 'ditolak')->count(),

            'perbaikanAntrean' => (clone $this->perbaikanQuery())->where('status_perbaikan', 'antrean')->count(),
            'perbaikanDikerjakan' => (clone $this->perbaikanQuery())->where('status_perbaikan', 'dikerjakan')->count(),
            'menungguSparepart' => (clone $this->perbaikanQuery())->where('status_perbaikan', 'menunggu_sparepart')->count(),
            'perbaikanSelesai' => (clone $this->perbaikanQuery())->where('status_perbaikan', 'selesai')->count(),

            'menungguValidasi' => (clone $this->perbaikanQuery())
                ->where('status_perbaikan', 'selesai')
                ->where('app_validasi', 'menunggu')
                ->count(),

            'perbaikanDivalidasi' => (clone $this->perbaikanQuery())
                ->where('app_validasi', 'divalidasi')
                ->count(),

            'perbaikanDikembalikan' => (clone $this->perbaikanQuery())
                ->where('app_validasi', 'dikembalikan')
                ->count(),
        ];
    }
}
