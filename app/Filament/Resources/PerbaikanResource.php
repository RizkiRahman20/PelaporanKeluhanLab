<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerbaikanResource\Pages;
use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PerbaikanResource extends Resource
{
    protected static ?string $model = Perbaikan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Perbaikan';
    protected static ?string $navigationLabel = 'Kelola Perbaikan';
    protected static ?string $label = 'Perbaikan';
    protected static ?string $pluralLabel = 'Kelola Perbaikan';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user?->isAdminLab() || $user?->isSPV();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'laporan.lab',
                'laporan.penugasan.user',
            ]);

        $user = Auth::user();

        if ($user?->isAdminLab()) {
            $labIds = $user->penugasanUserLabs()
                ->where('status_aktif', 'aktif')
                ->pluck('id_lab');

            $query->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status_perbaikan')
                ->label('Status Perbaikan')
                ->options([
                    'antrean' => 'Antrean',
                    'dikerjakan' => 'Dikerjakan',
                    'menunggu_sparepart' => 'Menunggu Sparepart',
                    'selesai' => 'Selesai',
                ])
                ->disabled(),

            Forms\Components\DatePicker::make('tgl_mulai')
                ->label('Tanggal Mulai')
                ->disabled(),

            Forms\Components\DatePicker::make('tgl_selesai')
                ->label('Tanggal Selesai')
                ->disabled(),

            Forms\Components\FileUpload::make('ft_perbaikan')
                ->label('Bukti Perbaikan')
                ->image()
                ->disk('public')
                ->directory('perbaikan')
                ->disabled(),

            Forms\Components\Textarea::make('catatan_pbk')
                ->label('Catatan Perbaikan')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_laporan')
                    ->label('No. Laporan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('laporan.lab.nm_lab')
                    ->label('Lab')
                    ->searchable(),

                Tables\Columns\TextColumn::make('laporan.nm_pelapor')
                    ->label('Pelapor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('laporan.kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'PC' => 'warning',
                        'non_PC' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status_perbaikan')
                    ->label('Status Perbaikan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'antrean' => 'Antrean',
                        'dikerjakan' => 'Dikerjakan',
                        'menunggu_sparepart' => 'Menunggu Sparepart',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'antrean' => 'gray',
                        'dikerjakan' => 'warning',
                        'menunggu_sparepart' => 'info',
                        'selesai' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('app_validasi')
                    ->label('Validasi SPV')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'divalidasi' => 'Divalidasi',
                        'dikembalikan' => 'Dikembalikan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'gray',
                        'divalidasi' => 'success',
                        'dikembalikan' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Mulai')
                    ->date('d/m/Y')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Selesai')
                    ->date('d/m/Y')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_perbaikan')
                    ->label('Status Perbaikan')
                    ->options([
                        'antrean' => 'Antrean',
                        'dikerjakan' => 'Dikerjakan',
                        'menunggu_sparepart' => 'Menunggu Sparepart',
                        'selesai' => 'Selesai',
                    ]),

                Tables\Filters\SelectFilter::make('app_validasi')
                    ->label('Validasi SPV')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'divalidasi' => 'Divalidasi',
                        'dikembalikan' => 'Dikembalikan',
                    ]),
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Detail Keluhan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->form([
                        Forms\Components\TextInput::make('no_laporan')
                            ->label('No. Laporan')
                            ->default(fn (Perbaikan $record) => $record->id_laporan)
                            ->disabled(),

                        Forms\Components\TextInput::make('lab')
                            ->label('Laboratorium')
                            ->default(fn (Perbaikan $record) => $record->laporan?->lab?->nm_lab ?? '-')
                            ->disabled(),

                        Forms\Components\TextInput::make('pelapor')
                            ->label('Pelapor')
                            ->default(fn (Perbaikan $record) => $record->laporan?->nm_pelapor ?? '-')
                            ->disabled(),

                        Forms\Components\TextInput::make('kategori')
                            ->label('Kategori')
                            ->default(fn (Perbaikan $record) => $record->laporan?->kategori ?? '-')
                            ->disabled(),

                        Forms\Components\Textarea::make('catatan_laporan')
                            ->label('Catatan Keluhan')
                            ->default(fn (Perbaikan $record) => $record->laporan?->catatan_lpr ?? '-')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Perbaikan $record): bool =>
                        Auth::user()?->isAdminLab()
                        && $record->status_perbaikan !== 'selesai'
                    )
                    ->form([
                        Forms\Components\Select::make('status_perbaikan')
                            ->label('Status Baru')
                            ->options([
                                'dikerjakan' => 'Dikerjakan',
                                'menunggu_sparepart' => 'Menunggu Sparepart',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('catatan_rw')
                            ->label('Catatan Perubahan')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Perbaikan $record, array $data): void {
                        $statusLama = $record->status_perbaikan;
                        $statusBaru = $data['status_perbaikan'];

                        $updateData = [
                            'status_perbaikan' => $statusBaru,
                        ];

                        if ($statusBaru === 'dikerjakan' && blank($record->tgl_mulai)) {
                            $updateData['tgl_mulai'] = now()->toDateString();
                        }

                        $record->update($updateData);

                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => "Status diubah dari {$statusLama} ke {$statusBaru}. Catatan: {$data['catatan_rw']}",
                            'id_perbaikan' => $record->id_perbaikan,
                        ]);

                        Notification::make()
                            ->title('Status perbaikan berhasil diperbarui.')
                            ->success()
                            ->send();
                    }),

                Action::make('selesaikan')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Perbaikan $record): bool =>
                        Auth::user()?->isAdminLab()
                        && $record->status_perbaikan !== 'selesai'
                    )
                    ->form([
                        Forms\Components\FileUpload::make('ft_perbaikan')
                            ->label('Upload Bukti Perbaikan')
                            ->image()
                            ->disk('public')
                            ->directory('perbaikan')
                            ->required(),

                        Forms\Components\Textarea::make('catatan_pbk')
                            ->label('Catatan Perbaikan')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Perbaikan $record, array $data): void {
                        $record->update([
                            'status_perbaikan' => 'selesai',
                            'tgl_selesai' => now()->toDateString(),
                            'ft_perbaikan' => $data['ft_perbaikan'],
                            'catatan_pbk' => $data['catatan_pbk'],
                            'app_validasi' => 'menunggu',
                        ]);

                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => 'Perbaikan diselesaikan oleh admin dan menunggu validasi SPV. Catatan: ' . $data['catatan_pbk'],
                            'id_perbaikan' => $record->id_perbaikan,
                        ]);

                        Notification::make()
                            ->title('Perbaikan selesai dan menunggu validasi SPV.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerbaikans::route('/'),
            'edit' => Pages\EditPerbaikan::route('/{record}/edit'),
        ];
    }
}