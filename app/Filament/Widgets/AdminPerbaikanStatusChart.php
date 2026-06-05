<?php

namespace App\Filament\Widgets;

use App\Models\Perbaikan;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminPerbaikanStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Tugas Perbaikan';

    protected static ?string $description = 'Ringkasan status tugas berdasarkan lab yang ditugaskan';

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
        $antrean = (clone $this->baseQuery())
            ->where('status_perbaikan', 'antrean')
            ->count();

        $dikerjakan = (clone $this->baseQuery())
            ->where('status_perbaikan', 'dikerjakan')
            ->count();

        $menungguSparepart = (clone $this->baseQuery())
            ->where('status_perbaikan', 'menunggu_sparepart')
            ->count();

        $selesai = (clone $this->baseQuery())
            ->where('status_perbaikan', 'selesai')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tugas',
                    'data' => [
                        $antrean,
                        $dikerjakan,
                        $menungguSparepart,
                        $selesai,
                    ],
                    'backgroundColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#ef4444',
                        '#22c55e',
                    ],
                    'borderColor' => '#111827',
                ],
            ],
            'labels' => [
                'Antrean',
                'Dikerjakan',
                'Menunggu Sparepart',
                'Selesai',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}