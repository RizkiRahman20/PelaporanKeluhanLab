<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeluhanResource\Pages;
use App\Models\LaporanKeluhan;
use App\Models\PenugasanUserLab;
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

    protected static function approvalLabel(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $state ?? '-',
        };
    }

    protected static function approvalColor(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    protected static function approvalIcon(?string $state): string
    {
        return match ($state) {
            'menunggu' => 'heroicon-o-clock',
            'disetujui' => 'heroicon-o-check-circle',
            'ditolak' => 'heroicon-o-x-circle',
            default => 'heroicon-o-question-mark-circle',
        };
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
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Laporan')
                    ->description('Data laporan keluhan yang dikirim oleh pelapor.')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('no_laporan')
                            ->label('No. Laporan')
                            ->disabled(),

                        Forms\Components\DatePicker::make('tgl_lapor')
                            ->label('Tanggal Lapor')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(),

                        Forms\Components\TextInput::make('kategori')
                            ->label('Kategori')
                            ->disabled(),

                        Forms\Components\TextInput::make('approval')
                            ->label('Status Approval')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Data Pelapor')
                    ->description('Identitas pelapor dan lokasi laboratorium.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nim_pelapor')
                            ->label('NIM')
                            ->disabled(),

                        Forms\Components\TextInput::make('nm_pelapor')
                            ->label('Nama Pelapor')
                            ->disabled(),

                        Forms\Components\TextInput::make('fakultas_pelapor')
                            ->label('Fakultas')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('prodi_pelapor')
                            ->label('Prodi')
                            ->disabled(),

                        Tables\Columns\TextColumn::make('prodi_pelapor')
                            ->label('Program Studi')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: false),

                        Forms\Components\TextInput::make('lab.nm_lab')
                            ->label('Laboratorium')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Keluhan')
                    ->description('Catatan dan bukti gambar keluhan.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_lpr')
                            ->label('Catatan Keluhan')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('file_foto')
                            ->label('Gambar Keluhan')
                            ->image()
                            ->disk('public')
                            ->directory('laporan')
                            ->imagePreviewHeight('220')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Validasi')
                    ->description('Informasi hasil validasi laporan.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
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
                Tables\Columns\TextColumn::make('no_laporan')
                    ->label('No. Laporan')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_lapor')
                    ->label('Tanggal')
                    ->icon('heroicon-o-calendar-days')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_pelapor')
                    ->label('Pelapor')
                    ->icon('heroicon-o-user')
                    ->weight('medium')
                    ->searchable()
                    ->description(fn (LaporanKeluhan $record): string =>
                        collect([
                            $record->nim_pelapor ? 'NIM: ' . $record->nim_pelapor : null,
                            $record->fakultas_pelapor,
                            $record->prodi_pelapor,
                        ])
                            ->filter()
                            ->implode(' • ')
                    ),

                Tables\Columns\TextColumn::make('lab.nm_lab')
                    ->label('Lab')
                    ->icon('heroicon-o-building-office-2')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\ImageColumn::make('file_foto')
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
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->icon(fn (?string $state): string => self::kategoriIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::kategoriLabel($state))
                    ->color(fn (?string $state): string => self::kategoriColor($state)),

                Tables\Columns\TextColumn::make('approval')
                    ->label('Approval')
                    ->badge()
                    ->icon(fn (?string $state): string => self::approvalIcon($state))
                    ->formatStateUsing(fn (?string $state): string => self::approvalLabel($state))
                    ->color(fn (?string $state): string => self::approvalColor($state)),

                Tables\Columns\TextColumn::make('penugasan.user.nm_user')
                    ->label('Delegasi')
                    ->icon('heroicon-o-user-plus')
                    ->placeholder('Belum didelegasikan')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pic.nm_user')
                    ->label('Validator')
                    ->icon('heroicon-o-shield-check')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('alasan_penolakan')
                    ->label('Alasan Ditolak')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-')
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->label('Status Approval')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'PC' => 'PC',
                        'non_PC' => 'Non-PC',
                    ]),

                Tables\Filters\SelectFilter::make('id_lab')
                    ->label('Laboratorium')
                    ->relationship('lab', 'nm_lab')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('tanggal_lapor')
                    ->label('Tanggal Lapor')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_lapor', '>=', $date)
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_lapor', '<=', $date)
                            );
                    }),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->slideOver()
                    ->modalHeading('Detail Laporan Keluhan')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Infolists\Components\Section::make('Informasi Laporan')
                            ->description('Ringkasan data laporan yang masuk dari pelapor.')
                            ->icon('heroicon-o-document-text')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('no_laporan')
                                    ->label('No. Laporan')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('tgl_lapor')
                                    ->label('Tanggal Lapor')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->icon(fn (?string $state): string => self::kategoriIcon($state))
                                    ->formatStateUsing(fn (?string $state): string => self::kategoriLabel($state))
                                    ->color(fn (?string $state): string => self::kategoriColor($state)),

                                Infolists\Components\TextEntry::make('approval')
                                    ->label('Status Approval')
                                    ->badge()
                                    ->icon(fn (?string $state): string => self::approvalIcon($state))
                                    ->formatStateUsing(fn (?string $state): string => self::approvalLabel($state))
                                    ->color(fn (?string $state): string => self::approvalColor($state)),
                            ]),

                        Infolists\Components\Section::make('Pelapor dan Lokasi')
                            ->description('Data mahasiswa atau pelapor beserta lokasi lab.')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nm_pelapor')
                                    ->label('Nama Pelapor')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('nim_pelapor')
                                    ->label('NIM')
                                    ->copyable()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('fakultas_pelapor')
                                    ->label('Fakultas')
                                    ->placeholder('-'),
                                
                                Infolists\Components\TextEntry::make('prodi_pelapor')
                                    ->label('Prodi')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('lab.nm_lab')
                                    ->label('Laboratorium')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('-'),
                            ]),

                        Infolists\Components\Section::make('Keluhan')
                            ->description('Catatan dan bukti gambar yang dikirim oleh pelapor.')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Infolists\Components\TextEntry::make('catatan_lpr')
                                    ->label('Catatan Keluhan')
                                    ->placeholder('-')
                                    ->prose()
                                    ->columnSpanFull(),

                                Infolists\Components\ImageEntry::make('file_foto')
                                    ->label('Gambar Keluhan')
                                    ->disk('public')
                                    ->height(260)
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
                            ->description('Informasi validasi dan delegasi laporan.')
                            ->icon('heroicon-o-shield-check')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('penugasan.user.nm_user')
                                    ->label('Didelegasikan ke')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Belum didelegasikan'),

                                Infolists\Components\TextEntry::make('pic.nm_user')
                                    ->label('Divalidasi oleh')
                                    ->badge()
                                    ->color('success')
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
                            ->helperText('Pilih admin atau asisten lab yang aktif pada laboratorium terkait.')
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
                    ->modalIcon('heroicon-o-check-circle')
                    ->modalIconColor('success')
                    ->modalHeading('Setujui Laporan')
                    ->modalDescription('Laporan akan disetujui dan otomatis masuk antrean perbaikan.')
                    ->modalSubmitActionLabel('Ya, setujui')
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
                            ->placeholder('Contoh: Bukti kurang jelas, laporan tidak sesuai, atau data pelapor tidak valid.')
                            ->rows(4)
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-x-circle')
                    ->modalIconColor('danger')
                    ->modalHeading('Tolak Laporan')
                    ->modalDescription('Laporan yang ditolak tidak akan masuk ke antrean perbaikan.')
                    ->modalSubmitActionLabel('Ya, tolak laporan')
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
            ->emptyStateIcon('heroicon-o-document-magnifying-glass')
            ->emptyStateHeading('Belum ada laporan keluhan')
            ->emptyStateDescription('Laporan yang masuk dari mahasiswa akan tampil di halaman ini untuk divalidasi.')
            ->defaultSort('tgl_lapor', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeluhans::route('/'),
        ];
    }
}