<?php

namespace App\Filament\Widgets;

use App\Models\Perbaikan;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminValidasiStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Validasi Hasil Perbaikan';

    protected static ?string $description = 'Status validasi SPV terhadap hasil perbaikan';

    protected static ?string $maxHeight = '280px';

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
        $menungguValidasi = (clone $this->baseQuery())
            ->where('status_perbaikan', 'selesai')
            ->where('app_validasi', 'menunggu')
            ->count();

        $divalidasi = (clone $this->baseQuery())
            ->where('app_validasi', 'divalidasi')
            ->count();

        $dikembalikan = (clone $this->baseQuery())
            ->where('app_validasi', 'dikembalikan')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Perbaikan',
                    'data' => [
                        $menungguValidasi,
                        $divalidasi,
                        $dikembalikan,
                    ],
                    'backgroundColor' => [
                        '#8b5cf6',
                        '#14b8a6',
                        '#ef4444',
                    ],
                    'borderColor' => '#111827',
                ],
            ],
            'labels' => [
                'Menunggu Validasi',
                'Divalidasi',
                'Dikembalikan',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}