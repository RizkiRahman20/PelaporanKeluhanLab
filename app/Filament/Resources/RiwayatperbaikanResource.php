<?php
namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPerbaikanResource\Pages;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RiwayatPerbaikanResource extends Resource
{
    protected static ?string $model = RiwayatPerbaikan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Perbaikan';
    protected static ?string $navigationLabel = 'Riwayat Perbaikan';
    protected static ?string $label = 'Riwayat Perbaikan';
    protected static ?string $pluralLabel = 'Riwayat Perbaikan';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user?->isAdminLab() || $user?->isSPV();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'perbaikan.laporan.lab',
            ]);

        $user = Auth::user();

        if ($user?->isAdminLab()) {
            $labIds = $user->penugasanUserLabs()
                ->where('status_aktif', 'aktif')
                ->pluck('id_lab');

            $query->whereHas('perbaikan.laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('perbaikan.id_laporan')
                    ->label('No. Laporan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('perbaikan.laporan.lab.nm_lab')
                    ->label('Lab')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_ubah')
                    ->label('Tanggal Ubah')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('perbaikan.status_perbaikan')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'antrean' => 'Antrean',
                        'dikerjakan' => 'Dikerjakan',
                        'menunggu_sparepart' => 'Menunggu Sparepart',
                        'selesai' => 'Selesai',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'antrean' => 'gray',
                        'dikerjakan' => 'warning',
                        'menunggu_sparepart' => 'info',
                        'selesai' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('catatan_rw')
                    ->label('Catatan')
                    ->limit(80)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),

                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_ubah', '>=', $date)
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_ubah', '<=', $date)
                            );
                    }),
            ])
            ->defaultSort('tgl_ubah', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatPerbaikans::route('/'),
        ];
    }
}