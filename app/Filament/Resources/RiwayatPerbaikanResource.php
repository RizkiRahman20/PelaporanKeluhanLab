<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPerbaikanResource\Pages;
use App\Models\Lab;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RiwayatPerbaikanResource extends Resource
{
    protected static ?string $model = RiwayatPerbaikan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = 'Riwayat Perbaikan';
    protected static ?string $label = 'Riwayat Perbaikan';
    protected static ?string $pluralLabel = 'Riwayat Perbaikan';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user?->isAdminLab() || $user?->isSPV() || $user?->isAsistenLab();
    }

    protected static function statusPerbaikanLabel(?string $state): string
    {
        return match ($state) {
            'antrean' => 'Antrean',
            'dikerjakan' => 'Dikerjakan',
            'menunggu_sparepart' => 'Menunggu Sparepart',
            'selesai' => 'Selesai',
            default => $state ?? '-',
        };
    }

    protected static function statusPerbaikanColor(?string $state): string
    {
        return match ($state) {
            'antrean' => 'gray',
            'dikerjakan' => 'warning',
            'menunggu_sparepart' => 'info',
            'selesai' => 'success',
            default => 'gray',
        };
    }

    protected static function statusPerbaikanIcon(?string $state): string
    {
        return match ($state) {
            'antrean' => 'heroicon-o-clock',
            'dikerjakan' => 'heroicon-o-arrow-path',
            'menunggu_sparepart' => 'heroicon-o-wrench-screwdriver',
            'selesai' => 'heroicon-o-check-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected static function validasiLabel(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'Menunggu',
            'divalidasi' => 'Divalidasi',
            'dikembalikan' => 'Dikembalikan',
            default => $state ?? '-',
        };
    }

    protected static function validasiColor(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'gray',
            'divalidasi' => 'success',
            'dikembalikan' => 'danger',
            default => 'gray',
        };
    }

    protected static function validasiIcon(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'heroicon-o-clock',
            'divalidasi' => 'heroicon-o-check-badge',
            'dikembalikan' => 'heroicon-o-arrow-uturn-left',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'perbaikan.laporan.lab',
                'perbaikan.laporan.penugasan.user',
            ]);

        $user = Auth::user();

        if ($user?->isAdminLab() || $user->isAsistenLab()) {
            $labIds = $user->penugasanUserLabs()
                ->where('status_aktif', 'aktif')
                ->pluck('id_lab');

            $query->whereHas('perbaikan.laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
        }

        if ($user?->isSPV() && ! $user->isSPVKedisiplinan()) {
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
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('perbaikan.laporan.lab.nm_lab')
                    ->label('Laboratorium')
                    ->icon('heroicon-o-building-office-2')
                    ->weight('medium')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn (RiwayatPerbaikan $record): ?string =>
                            $record->perbaikan?->laporan?->nm_pelapor
                                ? 'Pelapor: ' . $record->perbaikan->laporan->nm_pelapor
                                : null
                    ),

                Tables\Columns\TextColumn::make('perbaikan.laporan.nm_pelapor')
                    ->label('Pelapor')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tgl_ubah')
                    ->label('Tanggal Ubah')
                    ->icon('heroicon-o-calendar-days')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('perbaikan.status_perbaikan')
                    ->label('Status Perbaikan')
                    ->badge()
                    ->icon(fn (?string $state): string => self::statusPerbaikanIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::statusPerbaikanLabel($state))
                    ->color(fn (?string $state): string => self::statusPerbaikanColor($state)),

                Tables\Columns\TextColumn::make('perbaikan.app_validasi')
                    ->label('Validasi SPV')
                    ->badge()
                    ->icon(fn (?string $state): string => self::validasiIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::validasiLabel($state))
                    ->color(fn (?string $state): string => self::validasiColor($state)),

                Tables\Columns\TextColumn::make('catatan_rw')
                    ->label('Catatan Riwayat')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->limit(70)
                    ->wrap()
                    ->placeholder('-')
                    ->tooltip(fn (?string $state): ?string => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Laboratorium')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Lab::where('status_lab', 'aktif')->pluck('nm_lab', 'id_lab'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('perbaikan.laporan', function (Builder $query) use ($data) {
                            $query->where('id_lab', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
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
            ->filtersFormColumns(2)
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Detail Riwayat Perbaikan')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Infolists\Components\Section::make('Informasi Laporan')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('perbaikan.id_laporan')
                                    ->label('No. Laporan')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('perbaikan.laporan.lab.nm_lab')
                                    ->label('Laboratorium')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('perbaikan.laporan.nm_pelapor')
                                    ->label('Pelapor')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('tgl_ubah')
                                    ->label('Tanggal Ubah')
                                    ->date('d M Y'),
                            ]),

                        Infolists\Components\Section::make('Status Perbaikan')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('perbaikan.status_perbaikan')
                                    ->label('Status Perbaikan')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::statusPerbaikanLabel($state))
                                    ->color(fn (?string $state): string => self::statusPerbaikanColor($state)),

                                Infolists\Components\TextEntry::make('perbaikan.app_validasi')
                                    ->label('Validasi SPV')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::validasiLabel($state))
                                    ->color(fn (?string $state): string => self::validasiColor($state)),
                            ]),

                        Infolists\Components\Section::make('Catatan')
                            ->schema([
                                Infolists\Components\TextEntry::make('catatan_rw')
                                    ->label('Catatan Riwayat')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('perbaikan.catatan_pbk')
                                    ->label('Catatan Perbaikan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Section::make('Bukti Perbaikan')
                            ->schema([
                                Infolists\Components\ImageEntry::make('perbaikan.ft_perbaikan')
                                    ->label('Foto Bukti')
                                    ->disk('public')
                                    ->height(220)
                                    ->placeholder('Belum ada bukti perbaikan')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('Belum ada riwayat perbaikan')
            ->emptyStateDescription('Riwayat akan muncul setelah status perbaikan diperbarui.')
            ->defaultSort('tgl_ubah', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatPerbaikans::route('/'),
        ];
    }
}