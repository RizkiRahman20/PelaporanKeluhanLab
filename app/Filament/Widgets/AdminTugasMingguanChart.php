<?php

namespace App\Filament\Widgets;

use App\Models\Perbaikan;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminTugasMingguanChart extends ChartWidget
{
    protected static ?string $heading = 'Tugas Masuk 7 Hari Terakhir';

    protected static ?string $description = 'Jumlah tugas perbaikan yang masuk berdasarkan tanggal';

    protected static ?string $maxHeight = '320px';

    protected static ?string $pollingInterval = null;

    protected function getAssignedLabIds(): array
    {
        return Auth::user()
            ?->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab')
            ->toArray() ?? [];
    }

    protected function baseQuery(): Builder
    {
        $labIds = $this->getAssignedLabIds();

        return Perbaikan::query()
            ->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
    }

    protected function getData(): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $tugasPerHari = (clone $this->baseQuery())
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $dates = collect(range(0, 6))
            ->map(fn ($index) => now()->subDays(6 - $index));

        $labels = $dates
            ->map(fn (Carbon $date) => $date->translatedFormat('d M'))
            ->toArray();

        $data = $dates
            ->map(fn (Carbon $date) => $tugasPerHari[$date->toDateString()] ?? 0)
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tugas Masuk',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#60a5fa',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}