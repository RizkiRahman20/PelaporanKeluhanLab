<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeluhanResource\Pages;
use App\Models\LaporanKeluhan;
use App\Models\PenugasanUserLab;
use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
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

class LaporanKeluhanResource extends Resource
{
    protected static ?string $model = LaporanKeluhan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Validasi Laporan';
    protected static ?string $label = 'Laporan Keluhan';
    protected static ?string $pluralLabel = 'Validasi Laporan';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSPV() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'lab',
                'pic',
                'penugasan.user',
                'perbaikan',
            ]);

        $user = Auth::user();

        if ($user?->isSPV() && ! $user->isSPVKedisiplinan()) {
            $labIds = $user->penugasanUserLabs()
                ->where('status_aktif', 'aktif')
                ->pluck('id_lab');

            $query->whereIn('id_lab', $labIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('no_laporan')
                ->label('No. Laporan')
                ->disabled(),

            Forms\Components\DatePicker::make('tgl_lapor')
                ->label('Tanggal Lapor')
                ->disabled(),

            Forms\Components\TextInput::make('nim_pelapor')
                ->label('NIM')
                ->disabled(),

            Forms\Components\TextInput::make('nm_pelapor')
                ->label('Nama Pelapor')
                ->disabled(),

            Forms\Components\TextInput::make('fakultas_pelapor')
                ->label('Fakultas')
                ->disabled(),

            Forms\Components\TextInput::make('lab.nm_lab')
                ->label('Lab')
                ->disabled(),

            Forms\Components\TextInput::make('kategori')
                ->label('Kategori')
                ->disabled(),

            Forms\Components\Textarea::make('catatan_lpr')
                ->label('Catatan Keluhan')
                ->disabled()
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('file_foto')
                ->label('Gambar Keluhan')
                ->image()
                ->disk('public')
                ->directory('laporan')
                ->imagePreviewHeight('180')
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('approval')
                ->label('Status Approval')
                ->disabled(),

            Forms\Components\Textarea::make('alasan_penolakan')
                ->label('Alasan Penolakan')
                ->disabled()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_laporan')
                    ->label('No. Laporan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_lapor')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_pelapor')
                    ->label('Pelapor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lab.nm_lab')
                    ->label('Lab')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('file_foto')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(64)
                    ->square()
                    ->url(
                        fn (?string $state): ?string => $state
                            ? Storage::disk('public')->url($state)
                            : null,
                        true,
                    )
                    ->Placeholder('-'),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'PC' => 'warning',
                        'non_PC' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('approval')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('penugasan.user.nm_user')
                    ->label('Didelegasikan ke')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('pic.nm_user')
                    ->label('Divalidasi oleh')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->label('Status Approval')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Lab')
                    ->relationship('lab', 'nm_lab'),
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Detail Laporan Keluhan')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Infolists\Components\Section::make('Informasi Laporan')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('no_laporan')
                                    ->label('No. Laporan')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('tgl_lapor')
                                    ->label('Tanggal Lapor')
                                    ->date('d/m/Y'),

                                Infolists\Components\TextEntry::make('kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'PC' => 'warning',
                                        'non_PC' => 'info',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('approval')
                                    ->label('Status Approval')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'menunggu' => 'Menunggu',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => $state ?? '-',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'menunggu' => 'warning',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),

                        Infolists\Components\Section::make('Pelapor dan Lokasi')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nm_pelapor')
                                    ->label('Nama Pelapor')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('nim_pelapor')
                                    ->label('NIM')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('fakultas_pelapor')
                                    ->label('Fakultas')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('lab.nm_lab')
                                    ->label('Lab')
                                    ->placeholder('-'),
                            ]),

                        Infolists\Components\Section::make('Keluhan')
                            ->schema([
                                Infolists\Components\TextEntry::make('catatan_lpr')
                                    ->label('Catatan Keluhan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\ImageEntry::make('file_foto')
                                    ->label('Gambar Keluhan')
                                    ->disk('public')
                                    ->height(220)
                                    ->placeholder('Tidak ada gambar')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null,
                                        true,
                                    )
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\Section::make('Validasi')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('penugasan.user.nm_user')
                                    ->label('Didelegasikan ke')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('pic.nm_user')
                                    ->label('Divalidasi oleh')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('alasan_penolakan')
                                    ->label('Alasan Penolakan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LaporanKeluhan $record): bool =>
                        Auth::user()?->isSPV()
                        && $record->approval === 'menunggu'
                    )
                    ->form([
                        Forms\Components\Select::make('id_penugasan')
                            ->label('Delegasikan ke Admin / Asisten Lab')
                            ->options(function (LaporanKeluhan $record) {
                                return PenugasanUserLab::with(['user', 'lab'])
                                    ->where('status_aktif', 'aktif')
                                    ->where('id_lab', $record->id_lab)
                                    ->whereHas('user', function (Builder $query) {
                                        $query->whereIn('role_user', [
                                            'admin_lab',
                                            'asisten_lab',
                                        ])->where('status_aktif', 'aktif');
                                    })
                                    ->get()
                                    ->mapWithKeys(function (PenugasanUserLab $penugasan) {
                                        return [
                                            $penugasan->id_penugasan => $penugasan->user->nm_user . ' - ' . $penugasan->lab->nm_lab,
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Laporan')
                    ->modalDescription('Laporan akan disetujui dan otomatis masuk antrean perbaikan.')
                    ->action(function (LaporanKeluhan $record, array $data): void {
                        $record->update([
                            'approval' => 'disetujui',
                            'id_user' => Auth::id(),
                            'id_penugasan' => $data['id_penugasan'],
                            'alasan_penolakan' => null,
                        ]);

                        $perbaikan = Perbaikan::firstOrCreate(
                            [
                                'id_laporan' => $record->no_laporan,
                            ],
                            [
                                'status_perbaikan' => 'antrean',
                                'app_validasi' => 'menunggu',
                            ]
                        );

                        RiwayatPerbaikan::create([
                            'tgl_ubah' => now()->toDateString(),
                            'catatan_rw' => 'Laporan disetujui oleh SPV dan masuk antrean perbaikan.',
                            'id_perbaikan' => $perbaikan->id_perbaikan,
                        ]);

                        Notification::make()
                            ->title('Laporan berhasil disetujui dan didelegasikan.')
                            ->success()
                            ->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LaporanKeluhan $record): bool =>
                        Auth::user()?->isSPV()
                        && $record->approval === 'menunggu'
                    )
                    ->form([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Laporan')
                    ->modalDescription('Laporan yang ditolak tidak akan masuk ke antrean perbaikan.')
                    ->action(function (LaporanKeluhan $record, array $data): void {
                        $record->update([
                            'approval' => 'ditolak',
                            'id_user' => Auth::id(),
                            'alasan_penolakan' => $data['alasan_penolakan'],
                            'id_penugasan' => null,
                        ]);

                        Notification::make()
                            ->title('Laporan berhasil ditolak.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('tgl_lapor', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeluhans::route('/'),
        ];
    }
}
