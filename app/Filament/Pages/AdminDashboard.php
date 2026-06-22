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

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.admin-dashboard';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role_user, ['admin_lab', 'asisten_lab'], true);
    }

    protected function getAssignedLabIds()
    {
        return Auth::user()
            ->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');
    }

    // Method baru untuk mengambil pesan selamat datang
    protected function getWelcomeMessage(): string
    {
        $user = Auth::user();

        // Mengambil nama lab dari tabel relasi
        // ASUMSI: penugasanUserLabs memiliki relasi ke 'lab' dan tabel lab memiliki kolom 'nama_lab'
        $labNames = $user->penugasanUserLabs()
            ->with('lab') // Eager load relasi lab
            ->where('status_aktif', 'aktif')
            ->get()
            ->pluck('lab.nm_lab') // Ubah 'nama_lab' sesuai dengan nama kolom di database Anda (misal: 'lab.name' atau 'lab.nama')
            ->filter()
            ->implode(' & '); // Jika user pegang >1 lab, akan jadi "lab 6 & lab 7"

        // Format role agar lebih rapi (opsional)
        $roleName = $user->role_user === 'admin_lab' ? 'Admin' : 'Asisten';

        if ($labNames) {
            // Output: "Selamat datang, Admin Lab 6"
            return "Selamat datang, {$roleName} {$labNames}";
        }

        // Fallback jika tidak ada lab yang aktif
        return "Selamat datang, {$user->name}";
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
            // Tambahkan pesan ini agar bisa diakses di Blade
            'welcomeMessage' => $this->getWelcomeMessage(),
            
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