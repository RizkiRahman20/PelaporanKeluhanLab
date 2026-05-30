<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabResource\Pages;
use App\Models\Lab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LabResource extends Resource
{
    protected static ?string $model = Lab::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Manajemen Lab';
    protected static ?string $label = 'Laboratorium';
    protected static ?string $pluralLabel = 'Manajemen Lab';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPVKedisiplinan() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Laboratorium')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('kd_lab')
                            ->label('Kode Lab')
                            ->placeholder('Contoh: LAB01')
                            ->required()
                            ->maxLength(10)
                            ->unique(
                                table: 'labs',
                                column: 'kd_lab',
                                ignoreRecord: true
                            ),

                        Forms\Components\TextInput::make('nm_lab')
                            ->label('Nama Lab')
                            ->placeholder('Contoh: Laboratorium 1')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('status_lab')
                            ->label('Status Lab')
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
                Tables\Columns\TextColumn::make('kd_lab')
                    ->label('Kode Lab')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_lab')
                    ->label('Nama Lab')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_lab')
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
                Tables\Filters\SelectFilter::make('status_lab')
                    ->label('Status Lab')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Saya tidak aktifkan DeleteAction untuk lab
                // karena lab biasanya sudah punya relasi ke laporan dan penugasan.
                // Kalau tidak dipakai lagi, ubah status_lab menjadi nonaktif saja.
            ])
            ->bulkActions([])
            ->defaultSort('kd_lab');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabs::route('/'),
            'create' => Pages\CreateLab::route('/create'),
            'edit' => Pages\EditLab::route('/{record}/edit'),
        ];
    }
}