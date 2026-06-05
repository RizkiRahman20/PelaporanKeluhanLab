<?php

namespace App\Filament\Widgets;

use App\Models\LaporanKeluhan;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class LaporanBulananChart extends ChartWidget
{
    protected static ?string $heading = 'Laporan Masuk Tahun Ini';

    protected static ?string $description = 'Jumlah laporan keluhan berdasarkan bulan';

    protected static ?string $maxHeight = '320px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $tahun = now()->year;

        $laporanPerBulan = LaporanKeluhan::query()
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels = collect(range(1, 12))
            ->map(fn ($bulan) => Carbon::create($tahun, $bulan, 1)->translatedFormat('M'))
            ->toArray();

        $data = collect(range(1, 12))
            ->map(fn ($bulan) => $laporanPerBulan[$bulan] ?? 0)
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Laporan Masuk',
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