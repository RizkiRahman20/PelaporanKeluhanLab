<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenugasanUserLabResource\Pages;
use App\Models\Lab;
use App\Models\PenugasanUserLab;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PenugasanUserLabResource extends Resource
{
    protected static ?string $model = PenugasanUserLab::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Penugasan User Lab';
    protected static ?string $label = 'Penugasan User Lab';
    protected static ?string $pluralLabel = 'Penugasan User Lab';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPVKedisiplinan() ?? false;
    }

    protected static function roleLabel(?string $role): string
    {
        return match ($role) {
            'spv_kedisiplinan' => 'SPV Kedisiplinan',
            'spv_jaringan' => 'SPV Jaringan',
            'spv_inovasi_riset' => 'SPV Inovasi & Riset',
            'spv_penjadwalan' => 'SPV Penjadwalan',
            'spv_inventory' => 'SPV Inventory',
            'spv_keuangan' => 'SPV Keuangan & Surat',
            'admin_lab' => 'Admin Lab',
            'asisten_lab' => 'Asisten Lab',
            'calon_asisten' => 'Calon Asisten',
            default => $role ?? '-',
        };
    }

    protected static function roleColor(?string $role): string
    {
        return match (true) {
            str_starts_with($role ?? '', 'spv_') => 'danger',
            $role === 'admin_lab' => 'warning',
            $role === 'asisten_lab' => 'success',
            $role === 'calon_asisten' => 'gray',
            default => 'gray',
        };
    }

    protected static function roleIcon(?string $role): string
    {
        return match (true) {
            str_starts_with($role ?? '', 'spv_') => 'heroicon-o-shield-check',
            $role === 'admin_lab' => 'heroicon-o-wrench-screwdriver',
            $role === 'asisten_lab' => 'heroicon-o-user-group',
            $role === 'calon_asisten' => 'heroicon-o-user',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected static function semesterLabel(?string $semester): string
    {
        return match ($semester) {
            'ganjil' => 'Ganjil',
            'genap' => 'Genap',
            default => $semester ?? '-',
        };
    }

    protected static function semesterColor(?string $semester): string
    {
        return match ($semester) {
            'ganjil' => 'info',
            'genap' => 'success',
            default => 'gray',
        };
    }

    protected static function statusLabel(?string $status): string
    {
        return match ($status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => $status ?? '-',
        };
    }

    protected static function statusColor(?string $status): string
    {
        return match ($status) {
            'aktif' => 'success',
            'nonaktif' => 'danger',
            default => 'gray',
        };
    }

    protected static function statusIcon(?string $status): string
    {
        return match ($status) {
            'aktif' => 'heroicon-o-check-circle',
            'nonaktif' => 'heroicon-o-x-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected static function userOptions(): array
    {
        return User::query()
            ->where('status_aktif', 'aktif')
            ->orderBy('nm_user')
            ->get()
            ->mapWithKeys(function (User $user) {
                return [
                    $user->id_user => $user->nm_user . ' - ' . self::roleLabel($user->role_user),
                ];
            })
            ->toArray();
    }

    protected static function labOptions(): array
    {
        return Lab::query()
            ->where('status_lab', 'aktif')
            ->orderBy('kd_lab')
            ->get()
            ->mapWithKeys(function (Lab $lab) {
                return [
                    $lab->id_lab => $lab->kd_lab . ' - ' . $lab->nm_lab,
                ];
            })
            ->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Penugasan')
                    ->description('Tempatkan SPV, admin lab, asisten lab, atau calon asisten ke laboratorium tertentu.')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('User')
                            ->helperText('Pilih user aktif yang akan diberikan penugasan.')
                            ->options(fn () => self::userOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('id_lab')
                            ->label('Laboratorium')
                            ->helperText('Pilih laboratorium aktif untuk penugasan user.')
                            ->options(fn () => self::labOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options([
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                            ])
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->label('Tahun Ajaran')
                            ->placeholder('Contoh: 2025/2026')
                            ->helperText('Gunakan format tahun ajaran, misalnya 2025/2026.')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Select::make('status_aktif')
                            ->label('Status Penugasan')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nm_user')
                    ->label('Nama User')
                    ->icon('heroicon-o-user')
                    ->weight('medium')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PenugasanUserLab $record): ?string =>
                        $record->user?->email
                            ? $record->user->email
                            : null
                    ),

                Tables\Columns\TextColumn::make('user.role_user')
                    ->label('Role')
                    ->badge()
                    ->icon(fn (?string $state): string => self::roleIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::roleLabel($state))
                    ->color(fn (?string $state): string => self::roleColor($state)),

                Tables\Columns\TextColumn::make('lab.nm_lab')
                    ->label('Laboratorium')
                    ->icon('heroicon-o-building-office-2')
                    ->weight('medium')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PenugasanUserLab $record): ?string =>
                        $record->lab?->kd_lab
                            ? 'Kode Lab: ' . $record->lab->kd_lab
                            : null
                    ),

                Tables\Columns\TextColumn::make('lab.kd_lab')
                    ->label('Kode Lab')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->icon('heroicon-o-academic-cap')
                    ->formatStateUsing(fn (?string $state): string => self::semesterLabel($state))
                    ->color(fn (?string $state): string => self::semesterColor($state)),

                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->icon('heroicon-o-calendar-days')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_aktif')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (?string $state): string => self::statusIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::statusLabel($state))
                    ->color(fn (?string $state): string => self::statusColor($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->icon('heroicon-o-clock')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->icon('heroicon-o-arrow-path')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_user')
                    ->label('User')
                    ->options(fn () => self::userOptions())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Laboratorium')
                    ->options(fn () => self::labOptions())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    ]),

                Tables\Filters\SelectFilter::make('status_aktif')
                    ->label('Status Penugasan')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->slideOver()
                    ->modalHeading('Detail Penugasan User Lab')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Infolists\Components\Section::make('Informasi User')
                            ->description('Data user yang mendapatkan penugasan.')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.nm_user')
                                    ->label('Nama User')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('user.role_user')
                                    ->label('Role')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::roleLabel($state))
                                    ->color(fn (?string $state): string => self::roleColor($state)),

                                Infolists\Components\TextEntry::make('user.status_aktif')
                                    ->label('Status User')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::statusLabel($state))
                                    ->color(fn (?string $state): string => self::statusColor($state)),
                            ]),

                        Infolists\Components\Section::make('Informasi Laboratorium')
                            ->description('Laboratorium yang ditugaskan kepada user.')
                            ->icon('heroicon-o-building-office-2')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('lab.kd_lab')
                                    ->label('Kode Lab')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('lab.nm_lab')
                                    ->label('Nama Lab')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('lab.status_lab')
                                    ->label('Status Lab')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::statusLabel($state))
                                    ->color(fn (?string $state): string => self::statusColor($state)),

                                Infolists\Components\TextEntry::make('lab.keterangan')
                                    ->label('Keterangan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Section::make('Periode Penugasan')
                            ->description('Semester, tahun ajaran, dan status penugasan.')
                            ->icon('heroicon-o-calendar-days')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('semester')
                                    ->label('Semester')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::semesterLabel($state))
                                    ->color(fn (?string $state): string => self::semesterColor($state)),

                                Infolists\Components\TextEntry::make('tahun_ajaran')
                                    ->label('Tahun Ajaran')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status_aktif')
                                    ->label('Status Penugasan')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::statusLabel($state))
                                    ->color(fn (?string $state): string => self::statusColor($state)),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y, H:i'),
                            ]),
                    ]),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),

                Tables\Actions\Action::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PenugasanUserLab $record): bool => $record->status_aktif === 'aktif')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-x-circle')
                    ->modalIconColor('danger')
                    ->modalHeading('Nonaktifkan Penugasan')
                    ->modalDescription('Penugasan ini akan dinonaktifkan, bukan dihapus dari database.')
                    ->modalSubmitActionLabel('Ya, nonaktifkan')
                    ->action(function (PenugasanUserLab $record): void {
                        $record->update([
                            'status_aktif' => 'nonaktif',
                        ]);

                        Notification::make()
                            ->title('Penugasan berhasil dinonaktifkan.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PenugasanUserLab $record): bool => $record->status_aktif === 'nonaktif')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalIconColor('success')
                    ->modalHeading('Aktifkan Penugasan')
                    ->modalDescription('Penugasan ini akan diaktifkan kembali.')
                    ->modalSubmitActionLabel('Ya, aktifkan')
                    ->action(function (PenugasanUserLab $record): void {
                        $record->update([
                            'status_aktif' => 'aktif',
                        ]);

                        Notification::make()
                            ->title('Penugasan berhasil diaktifkan.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(false),
            ])
            ->bulkActions([])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('Belum ada penugasan user lab')
            ->emptyStateDescription('Tambahkan penugasan untuk menghubungkan user dengan laboratorium tertentu.')
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenugasanUserLabs::route('/'),
            'create' => Pages\CreatePenugasanUserLab::route('/create'),
            'edit' => Pages\EditPenugasanUserLab::route('/{record}/edit'),
        ];
    }
}