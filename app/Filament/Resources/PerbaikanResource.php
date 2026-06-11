<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerbaikanResource\Pages;
use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return $user?->isAdminLab() || $user?->isSPV() || $user?->isAsistenLab();
    }

    protected static function kategoriLabel(?string $state): string
    {
        return match ($state) {
            'PC' => 'PC',
            'non_PC' => 'Non-PC',
            default => $state ?? '-',
        };
    }

    protected static function kategoriColor(?string $state): string
    {
        return match ($state) {
            'PC' => 'warning',
            'non_PC' => 'info',
            default => 'gray',
        };
    }

    protected static function kategoriIcon(?string $state): string
    {
        return match ($state) {
            'PC' => 'heroicon-o-computer-desktop',
            'non_PC' => 'heroicon-o-wrench-screwdriver',
            default => 'heroicon-o-question-mark-circle',
        };
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
                'laporan.lab',
                'laporan.penugasan.user',
            ]);

        $user = Auth::user();

        if ($user?->isAdminLab() || $user->isAsistenLab()) {
            $labIds = $user->penugasanUserLabs()
                ->where('status_aktif', 'aktif')
                ->pluck('id_lab');

            $query->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
        }

        if ($user?->isSPV() && ! $user->isSPVKedisiplinan()) {
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
        return $form
            ->schema([
                Forms\Components\Section::make('Status Perbaikan')
                    ->description('Informasi status pengerjaan dan validasi perbaikan.')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status_perbaikan')
                            ->label('Status Perbaikan')
                            ->options([
                                'antrean' => 'Antrean',
                                'dikerjakan' => 'Dikerjakan',
                                'menunggu_sparepart' => 'Menunggu Sparepart',
                                'selesai' => 'Selesai',
                            ])
                            ->native(false)
                            ->disabled(),

                        Forms\Components\Select::make('app_validasi')
                            ->label('Validasi SPV')
                            ->options([
                                'menunggu' => 'Menunggu',
                                'divalidasi' => 'Divalidasi',
                                'dikembalikan' => 'Dikembalikan',
                            ])
                            ->native(false)
                            ->disabled(),

                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(),

                        Forms\Components\DatePicker::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Bukti dan Catatan')
                    ->description('Bukti hasil perbaikan dan catatan dari admin lab.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('ft_perbaikan')
                            ->label('Bukti Perbaikan')
                            ->image()
                            ->disk('public')
                            ->directory('perbaikan')
                            ->imagePreviewHeight('220')
                            ->openable()
                            ->downloadable()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_pbk')
                            ->label('Catatan Perbaikan')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Dikembalikan')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_laporan')
                    ->label('No. Laporan')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laporan.lab.nm_lab')
                    ->label('Laboratorium')
                    ->icon('heroicon-o-building-office-2')
                    ->weight('medium')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Perbaikan $record): ?string =>
                        $record->laporan?->nm_pelapor
                            ? 'Pelapor: ' . $record->laporan->nm_pelapor
                            : null
                    ),

                Tables\Columns\TextColumn::make('laporan.nm_pelapor')
                    ->label('Pelapor')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('laporan.file_foto')
                    ->label('Keluhan')
                    ->disk('public')
                    ->size(56)
                    ->square()
                    ->url(
                        fn (?string $state): ?string => $state
                            ? Storage::disk('public')->url($state)
                            : null,
                        true,
                    )
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('laporan.kategori')
                    ->label('Kategori')
                    ->badge()
                    ->icon(fn (?string $state): string => self::kategoriIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::kategoriLabel($state))
                    ->color(fn (?string $state): string => self::kategoriColor($state)),

                Tables\Columns\TextColumn::make('status_perbaikan')
                    ->label('Status Perbaikan')
                    ->badge()
                    ->icon(fn (?string $state): string => self::statusPerbaikanIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::statusPerbaikanLabel($state))
                    ->color(fn (?string $state): string => self::statusPerbaikanColor($state)),

                Tables\Columns\TextColumn::make('app_validasi')
                    ->label('Validasi SPV')
                    ->badge()
                    ->icon(fn (?string $state): string => self::validasiIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::validasiLabel($state))
                    ->color(fn (?string $state): string => self::validasiColor($state)),

                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Mulai')
                    ->icon('heroicon-o-play-circle')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('catatan_pbk')
                    ->label('Catatan')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->limit(45)
                    ->wrap()
                    ->placeholder('-')
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('ft_perbaikan')
                    ->label('Bukti')
                    ->disk('public')
                    ->size(56)
                    ->square()
                    ->url(
                        fn (?string $state): ?string => $state
                            ? Storage::disk('public')->url($state)
                            : null,
                        true,
                    )
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'PC' => 'PC',
                        'non_PC' => 'Non-PC',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('laporan', function (Builder $query) use ($data) {
                            $query->where('kategori', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('tanggal_perbaikan')
                    ->label('Tanggal Perbaikan')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->slideOver()
                    ->modalHeading('Detail Perbaikan')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Infolists\Components\Section::make('Informasi Laporan')
                            ->description('Ringkasan laporan keluhan yang sedang diperbaiki.')
                            ->icon('heroicon-o-document-text')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('id_laporan')
                                    ->label('No. Laporan')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('laporan.kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->icon(fn (?string $state): string => self::kategoriIcon($state))
                                    ->formatStateUsing(fn (?string $state): string => self::kategoriLabel($state))
                                    ->color(fn (?string $state): string => self::kategoriColor($state)),

                                Infolists\Components\TextEntry::make('status_perbaikan')
                                    ->label('Status Perbaikan')
                                    ->badge()
                                    ->icon(fn (?string $state): string => self::statusPerbaikanIcon($state))
                                    ->formatStateUsing(fn (?string $state): string => self::statusPerbaikanLabel($state))
                                    ->color(fn (?string $state): string => self::statusPerbaikanColor($state)),

                                Infolists\Components\TextEntry::make('app_validasi')
                                    ->label('Validasi SPV')
                                    ->badge()
                                    ->icon(fn (?string $state): string => self::validasiIcon($state))
                                    ->formatStateUsing(fn (?string $state): string => self::validasiLabel($state))
                                    ->color(fn (?string $state): string => self::validasiColor($state)),
                            ]),

                        Infolists\Components\Section::make('Pelapor dan Lokasi')
                            ->description('Data pelapor dan laboratorium tempat keluhan terjadi.')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('laporan.nm_pelapor')
                                    ->label('Pelapor')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('laporan.nim_pelapor')
                                    ->label('NIM')
                                    ->copyable()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('laporan.fakultas_pelapor')
                                    ->label('Fakultas / Program Studi')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('laporan.lab.nm_lab')
                                    ->label('Laboratorium')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('-'),
                            ]),

                        Infolists\Components\Section::make('Keluhan Pelapor')
                            ->description('Catatan awal dan bukti keluhan dari pelapor.')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Infolists\Components\TextEntry::make('laporan.catatan_lpr')
                                    ->label('Catatan Keluhan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\ImageEntry::make('laporan.file_foto')
                                    ->label('Gambar Keluhan Pelapor')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Tidak ada gambar')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null,
                                        true,
                                    )
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Section::make('Hasil Perbaikan')
                            ->description('Tanggal pengerjaan, catatan admin lab, dan bukti hasil perbaikan.')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('tgl_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d M Y')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('tgl_selesai')
                                    ->label('Tanggal Selesai')
                                    ->date('d M Y')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('catatan_pbk')
                                    ->label('Catatan Perbaikan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('alasan_penolakan')
                                    ->label('Alasan Dikembalikan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\ImageEntry::make('ft_perbaikan')
                                    ->label('Bukti Perbaikan')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Belum ada bukti perbaikan')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null,
                                        true,
                                    )
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Perbaikan $record): bool =>
                        Auth::user()?->isAdminLab() || Auth::user()?->isAsistenLab()
                        && $record->status_perbaikan !== 'selesai'
                    )
                    ->form([
                        Forms\Components\Select::make('status_perbaikan')
                            ->label('Status Baru')
                            ->helperText('Pilih status terbaru dari proses perbaikan.')
                            ->options([
                                'dikerjakan' => 'Dikerjakan',
                                'menunggu_sparepart' => 'Menunggu Sparepart',
                            ])
                            ->native(false)
                            ->required(),

                        Forms\Components\Textarea::make('catatan_rw')
                            ->label('Catatan Perubahan')
                            ->placeholder('Contoh: Perbaikan mulai dikerjakan, menunggu sparepart, atau kendala teknis lainnya.')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-arrow-path')
                    ->modalIconColor('warning')
                    ->modalHeading('Update Status Perbaikan')
                    ->modalDescription('Status perbaikan akan diperbarui dan otomatis masuk ke riwayat perbaikan.')
                    ->modalSubmitActionLabel('Ya, update status')
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
                        Auth::user()?->isAdminLab() || Auth::user()?->isAsistenLab()
                        && $record->status_perbaikan !== 'selesai'
                    )
                    ->form([
                        Forms\Components\FileUpload::make('ft_perbaikan')
                            ->label('Upload Bukti Perbaikan')
                            ->helperText('Upload gambar sebagai bukti bahwa perbaikan sudah selesai.')
                            ->image()
                            ->disk('public')
                            ->directory('perbaikan')
                            ->imagePreviewHeight('220')
                            ->openable()
                            ->downloadable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('catatan_pbk')
                            ->label('Catatan Perbaikan')
                            ->placeholder('Tuliskan ringkasan hasil perbaikan.')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalIconColor('success')
                    ->modalHeading('Selesaikan Perbaikan')
                    ->modalDescription('Perbaikan akan ditandai selesai dan menunggu validasi dari SPV.')
                    ->modalSubmitActionLabel('Ya, selesaikan')
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

                Action::make('validasi_spv')
                    ->label('Validasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Perbaikan $record): bool =>
                        Auth::user()?->isSPV()
                        && $record->status_perbaikan === 'selesai'
                        && $record->app_validasi === 'menunggu'
                    )
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-check-badge')
                    ->modalIconColor('success')
                    ->modalHeading('Validasi Perbaikan')
                    ->modalDescription('Pastikan bukti dan hasil perbaikan sudah benar sebelum divalidasi.')
                    ->modalSubmitActionLabel('Ya, validasi')
                    ->action(function (Perbaikan $record): void {
                        $record->update([
                            'app_validasi' => 'divalidasi',
                        ]);

                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => 'Perbaikan divalidasi oleh SPV.',
                            'id_perbaikan' => $record->id_perbaikan,
                        ]);

                        Notification::make()
                            ->title('Perbaikan berhasil divalidasi.')
                            ->success()
                            ->send();
                    }),

                Action::make('kembalikan_spv')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (Perbaikan $record): bool =>
                        Auth::user()?->isSPV()
                        && $record->status_perbaikan === 'selesai'
                        && $record->app_validasi === 'menunggu'
                    )
                    ->form([
                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Dikembalikan')
                            ->placeholder('Contoh: Bukti kurang jelas, hasil perbaikan belum sesuai, atau perlu pemeriksaan ulang.')
                            ->rows(4)
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-arrow-uturn-left')
                    ->modalIconColor('danger')
                    ->modalHeading('Kembalikan Perbaikan')
                    ->modalDescription('Status perbaikan akan dikembalikan ke dikerjakan agar admin lab dapat memperbaiki ulang.')
                    ->modalSubmitActionLabel('Ya, kembalikan')
                    ->action(function (Perbaikan $record, array $data): void {
                        $record->update([
                            'status_perbaikan' => 'dikerjakan',
                            'app_validasi' => 'dikembalikan',
                            'alasan_penolakan' => $data['alasan'],
                        ]);

                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => 'Perbaikan dikembalikan oleh SPV. Alasan: ' . $data['alasan'],
                            'id_perbaikan' => $record->id_perbaikan,
                        ]);

                        Notification::make()
                            ->title('Perbaikan dikembalikan ke admin.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->emptyStateIcon('heroicon-o-wrench-screwdriver')
            ->emptyStateHeading('Belum ada data perbaikan')
            ->emptyStateDescription('Data perbaikan akan muncul setelah laporan keluhan disetujui dan masuk antrean.')
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerbaikans::route('/'),
            'edit' => Pages\EditPerbaikan::route('/{record}/edit'),
        ];
    }
}