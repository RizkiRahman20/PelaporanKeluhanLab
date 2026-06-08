<?php

namespace App\Filament\Resources\RiwayatPerbaikanResource\Pages;

use App\Filament\Resources\RiwayatPerbaikanResource;
use App\Models\Lab;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListRiwayatPerbaikans extends ListRecords
{
    protected static string $resource = RiwayatPerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn (): bool => Auth::user()?->isSPV() ?? false)
                ->modalHeading('Cetak PDF Riwayat Perbaikan')
                ->modalSubmitActionLabel('Cetak')
                ->form([
                    Forms\Components\Select::make('id_lab')
                        ->label('Laboratorium')
                        ->options(fn () => $this->getLabOptions())
                        ->placeholder('Semua Lab')
                        ->searchable(),

                    Forms\Components\DatePicker::make('dari')
                        ->label('Dari Tanggal')
                        ->native(false),

                    Forms\Components\DatePicker::make('sampai')
                        ->label('Sampai Tanggal')
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $params = collect($data)
                        ->filter(fn ($value) => filled($value))
                        ->toArray();

                    return redirect()->route('pdf.riwayat-perbaikan', $params);
                }),
        ];
    }

    protected function getLabOptions(): array
{
    $user = Auth::user();

    $query = Lab::query()
        ->where('status_lab', 'aktif')
        ->orderBy('id_lab');

    if ($user?->isSPV() && ! $user->isSPVKedisiplinan()) {
        $labIds = $user->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');

        $query->whereIn('id_lab', $labIds);
    }

    return $query
        ->get()
        ->mapWithKeys(function (Lab $lab) {
            return [
                $lab->id_lab => $lab->kd_lab . ' - ' . $lab->nm_lab,
            ];
        })
        ->toArray();
}
}