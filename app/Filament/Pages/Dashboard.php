<?php

namespace App\Filament\Pages;

use App\Models\LaporanKeluhan;
use App\Models\Perbaikan;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('hapus_semua_foto')
                ->label('Hapus Semua Foto')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hapus semua foto?')
                ->modalDescription('Foto laporan mahasiswa dan foto bukti perbaikan akan dihapus. Data laporan dan data perbaikan tetap aman.')
                ->modalSubmitActionLabel('Ya, Hapus Foto')
                ->modalCancelActionLabel('Batal')
                ->visible(fn (): bool => Auth::user()?->isSPVKedisiplinan() ?? false)
                ->action(fn () => $this->hapusSemuaFoto()),
        ];
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

    protected function hapusSemuaFoto(): void
    {
        $fotoLaporan = (clone $this->laporanQuery())
            ->whereNotNull('file_foto')
            ->where('file_foto', '!=', '')
            ->pluck('file_foto');

        $fotoPerbaikan = (clone $this->perbaikanQuery())
            ->whereNotNull('ft_perbaikan')
            ->where('ft_perbaikan', '!=', '')
            ->pluck('ft_perbaikan');

        $semuaFoto = $fotoLaporan
            ->merge($fotoPerbaikan)
            ->map(fn ($path) => $this->normalisasiPathStorage($path))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! empty($semuaFoto)) {
            Storage::disk('public')->delete($semuaFoto);
        }

        DB::transaction(function () {
            (clone $this->laporanQuery())
                ->whereNotNull('file_foto')
                ->update([
                    'file_foto' => null,
                ]);

            (clone $this->perbaikanQuery())
                ->whereNotNull('ft_perbaikan')
                ->update([
                    'ft_perbaikan' => null,
                ]);
        });

        Notification::make()
            ->title('Foto berhasil dihapus')
            ->body('Semua foto laporan mahasiswa dan foto bukti perbaikan berhasil dihapus.')
            ->success()
            ->send();
    }

    protected function normalisasiPathStorage(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return $path ?: null;
    }
}