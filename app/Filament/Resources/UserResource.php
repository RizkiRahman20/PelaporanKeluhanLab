<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Pengguna';
    protected static ?string $label = 'User';
    protected static ?string $pluralLabel = 'Data Pengguna';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPVKedisiplinan() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data User')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nm_user')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true
                            ),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),

                        Forms\Components\Select::make('role_user')
                            ->label('Role')
                            ->options([
                                'spv_kedisiplinan' => 'SPV Kedisiplinan',
                                'spv_jaringan' => 'SPV Jaringan',
                                'spv_inovasi_riset' => 'SPV Inovasi & Riset',
                                'spv_penjadwalan' => 'SPV Penjadwalan',
                                'spv_inventory' => 'SPV Inventory',
                                'spv_keuangan' => 'SPV Keuangan & Surat',
                                'admin_lab' => 'Admin Lab',
                                'asisten_lab' => 'Asisten Lab',
                                'calon_asisten' => 'Calon Asisten',
                            ])
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('status_aktif')
                            ->label('Status Aktif')
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
                Tables\Columns\TextColumn::make('nm_user')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->email),

                // Tables\Columns\TextColumn::make('email')
                //     ->label('Email')
                //     ->searchable()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('role_user')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'spv_kedisiplinan' => 'SPV Kedisiplinan',
                        'spv_jaringan' => 'SPV Jaringan',
                        'spv_inovasi_riset' => 'SPV Inovasi & Riset',
                        'spv_penjadwalan' => 'SPV Penjadwalan',
                        'spv_inventory' => 'SPV Inventory',
                        'spv_keuangan' => 'SPV Keuangan & Surat',
                        'admin_lab' => 'Admin Lab',
                        'asisten_lab' => 'Asisten Lab',
                        'calon_asisten' => 'Calon Asisten',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match (true) {
                        str_starts_with($state ?? '', 'spv_') => 'danger',
                        $state === 'admin_lab' => 'warning',
                        $state === 'asisten_lab' => 'success',
                        $state === 'calon_asisten' => 'gray',
                        default => 'gray',
                    }),

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
                Tables\Filters\SelectFilter::make('role_user')
                    ->label('Role')
                    ->options([
                        'spv_kedisiplinan' => 'SPV Kedisiplinan',
                        'spv_jaringan' => 'SPV Jaringan',
                        'spv_inovasi_riset' => 'SPV Inovasi & Riset',
                        'spv_penjadwalan' => 'SPV Penjadwalan',
                        'spv_inventory' => 'SPV Inventory',
                        'spv_keuangan' => 'SPV Keuangan & Surat',
                        'admin_lab' => 'Admin Lab',
                        'asisten_lab' => 'Asisten Lab',
                        'calon_asisten' => 'Calon Asisten',
                    ]),

                Tables\Filters\SelectFilter::make('status_aktif')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => $record->id_user !== Auth::id())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User berhasil dihapus.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}