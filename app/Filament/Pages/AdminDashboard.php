<?php
namespace App\Filament\Pages;

use App\Models\Perbaikan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Perbaikan';
    protected static ?string $navigationLabel = 'Dashboard Admin';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.admin-dashboard';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdminLab() ?? false;
    }

    protected function getAssignedLabIds()
    {
        return Auth::user()
            ->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');
    }

    protected function basePerbaikanQuery(): Builder
    {
        $labIds = $this->getAssignedLabIds();

        return Perbaikan::query()
            ->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
    }

    public function getViewData(): array
    {
        return [
            'totalTugas' => (clone $this->basePerbaikanQuery())->count(),
            'totalAntrean' => (clone $this->basePerbaikanQuery())->where('status_perbaikan', 'antrean')->count(),
            'totalDikerjakan' => (clone $this->basePerbaikanQuery())->where('status_perbaikan', 'dikerjakan')->count(),
            'totalMenungguSparepart' => (clone $this->basePerbaikanQuery())->where('status_perbaikan', 'menunggu_sparepart')->count(),
            'totalSelesai' => (clone $this->basePerbaikanQuery())->where('status_perbaikan', 'selesai')->count(),
            'totalMenungguValidasi' => (clone $this->basePerbaikanQuery())
                ->where('status_perbaikan', 'selesai')
                ->where('app_validasi', 'menunggu')
                ->count(),
        ];
    }
}
