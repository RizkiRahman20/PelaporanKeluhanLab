<?php

namespace App\Filament\Pages;

use App\Models\Lab;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CetakPdf extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Cetak PDF';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.cetak-pdf';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_lab')
                    ->label('Pilih Lab')
                    ->options(
                        Lab::where('status_lab', 'aktif')
                            ->orderBy('nm_lab')
                            ->pluck('nm_lab', 'id_lab')
                    )
                    ->placeholder('Semua Lab')
                    ->searchable(),

                DatePicker::make('dari')
                    ->label('Dari Tanggal'),

                DatePicker::make('sampai')
                    ->label('Sampai Tanggal'),
            ])
            ->statePath('data');
    }

    public function cetak(): void
    {
        $params = array_filter($this->data ?? []);

        $params['mode'] = 'preview';

        $this->redirectRoute('pdf.riwayat', $params);
    }

    public static function canAccess(): bool
    {
        return (Auth::user()?->isSPV() || Auth::user()?->isAdminLab()) ?? false;
    }
}