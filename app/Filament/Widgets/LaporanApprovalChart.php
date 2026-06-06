<?php

namespace App\Filament\Widgets;

use App\Models\LaporanKeluhan;
use Filament\Widgets\ChartWidget;

class LaporanApprovalChart extends ChartWidget
{
    protected static ?string $heading = 'Approval Laporan';

    protected static ?string $description = 'Perbandingan status approval laporan keluhan';

    protected static ?string $maxHeight = '280px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $menunggu = LaporanKeluhan::query()
            ->where('approval', 'menunggu')
            ->count();

        $disetujui = LaporanKeluhan::query()
            ->where('approval', 'disetujui')
            ->count();

        $ditolak = LaporanKeluhan::query()
            ->where('approval', 'ditolak')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Laporan',
                    'data' => [
                        $menunggu,
                        $disetujui,
                        $ditolak,
                    ],
                    'backgroundColor' => [
                        '#f59e0b',
                        '#22c55e',
                        '#ef4444',
                    ],
                    'borderColor' => '#111827',
                ],
            ],
            'labels' => [
                'Menunggu',
                'Disetujui',
                'Ditolak',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}