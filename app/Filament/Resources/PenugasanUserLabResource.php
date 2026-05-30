<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenugasanUserLabResource\Pages;
use App\Models\Lab;
use App\Models\PenugasanUserLab;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
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
                    ->description('Gunakan form ini untuk menempatkan SPV, admin lab, asisten lab, atau calon asisten ke laboratorium tertentu.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('id_user')
                            ->label('User')
                            ->options(fn () => self::userOptions())
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('id_lab')
                            ->label('Laboratorium')
                            ->options(fn () => self::labOptions())
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options([
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->label('Tahun Ajaran')
                            ->placeholder('Contoh: 2025/2026')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\Select::make('status_aktif')
                            ->label('Status Penugasan')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nm_user')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.role_user')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::roleLabel($state))
                    ->color(fn (?string $state): string => match (true) {
                        str_starts_with($state ?? '', 'spv_') => 'danger',
                        $state === 'admin_lab' => 'warning',
                        $state === 'asisten_lab' => 'success',
                        $state === 'calon_asisten' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('lab.kd_lab')
                    ->label('Kode Lab')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lab.nm_lab')
                    ->label('Nama Lab')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_aktif')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_user')
                    ->label('User')
                    ->options(fn () => self::userOptions())
                    ->searchable(),

                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Laboratorium')
                    ->options(fn () => self::labOptions())
                    ->searchable(),

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
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PenugasanUserLab $record): bool => $record->status_aktif === 'aktif')
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan Penugasan')
                    ->modalDescription('Penugasan ini akan dinonaktifkan, bukan dihapus.')
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
                    ->modalHeading('Aktifkan Penugasan')
                    ->modalDescription('Penugasan ini akan diaktifkan kembali.')
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
            ->defaultSort('created_at', 'desc');
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