<?php

namespace App\Filament\Widgets;

use App\Models\Perbaikan;
use Filament\Widgets\ChartWidget;

class PerbaikanStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Perbaikan';

    protected static ?string $description = 'Perbandingan status pekerjaan maintenance';

    protected static ?string $maxHeight = '280px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $antrean = Perbaikan::query()
            ->where('status_perbaikan', 'antrean')
            ->count();

        $dikerjakan = Perbaikan::query()
            ->where('status_perbaikan', 'dikerjakan')
            ->count();

        $menungguSparepart = Perbaikan::query()
            ->where('status_perbaikan', 'menunggu_sparepart')
            ->count();

        $selesai = Perbaikan::query()
            ->where('status_perbaikan', 'selesai')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Perbaikan',
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
                    'borderColor' => '#1f2937',
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