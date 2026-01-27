<?php

namespace App\Filament\Resources;

use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\PrestasiResource\Pages;
use App\Models\Fakultas;
use App\Models\Prestasi;
use App\Models\Prodi;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PrestasiResource extends Resource
{
    protected static ?string $model = Prestasi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-trophy';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Prestasi';

    protected static ?string $modelLabel = 'Prestasi';

    protected static ?string $pluralModelLabel = 'Prestasi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        $isFakultas = $user->role === UserRole::FAKULTAS;
        $isProdi = $user->role === UserRole::PRODI;

        $unitTypeOptions = $isFakultas
            ? [
                UnitType::FAKULTAS->value => 'Fakultas',
                UnitType::PRODI->value => 'Program Studi',
            ]
            : [
                UnitType::UNIVERSITAS->value => 'Universitas',
                UnitType::FAKULTAS->value => 'Fakultas',
                UnitType::PRODI->value => 'Program Studi',
            ];

        return $schema
            ->components([
                Tabs::make('Prestasi')
                    ->tabs([
                        Tabs\Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('unit_type')
                                            ->label('Tipe Unit')
                                            ->options($unitTypeOptions)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('unit_id', null))
                                            ->helperText($isFakultas ? 'Pilih fakultas Anda atau prodi di bawahnya' : null),

                                        Select::make('unit_id')
                                            ->label('Unit')
                                            ->options(function (Get $get) use ($user, $isFakultas) {
                                                $unitType = $get('unit_type');
                                                
                                                if ($isFakultas) {
                                                    return match ($unitType) {
                                                        UnitType::FAKULTAS->value => Fakultas::where('id', $user->unit_id)->pluck('nama', 'id'),
                                                        UnitType::PRODI->value => Prodi::where('fakultas_id', $user->unit_id)->pluck('nama', 'id'),
                                                        default => [],
                                                    };
                                                }
                                                
                                                return match ($unitType) {
                                                    UnitType::FAKULTAS->value => Fakultas::pluck('nama', 'id'),
                                                    UnitType::PRODI->value => Prodi::pluck('nama', 'id'),
                                                    default => [],
                                                };
                                            })
                                            ->required(fn (Get $get) => $get('unit_type') !== UnitType::UNIVERSITAS->value)
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn (Get $get) => $get('unit_type') !== UnitType::UNIVERSITAS->value),

                                        TextInput::make('judul')
                                            ->label('Judul Prestasi')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        RichEditor::make('deskripsi')
                                            ->label('Deskripsi')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'bulletList',
                                                'orderedList',
                                                'link',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;'])
                                            ->columnSpanFull(),

                                        DatePicker::make('tanggal')
                                            ->label('Tanggal')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d F Y'),

                                        Select::make('tingkat')
                                            ->label('Tingkat')
                                            ->options(PrestasiTingkat::class)
                                            ->required(),

                                        Select::make('kategori')
                                            ->label('Kategori')
                                            ->options(PrestasiKategori::class)
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Detail')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Informasi Kegiatan')
                                    ->schema([
                                        TextInput::make('penyelenggara')
                                            ->label('Penyelenggara')
                                            ->maxLength(255),

                                        TextInput::make('lokasi')
                                            ->label('Lokasi')
                                            ->maxLength(255),

                                        Textarea::make('peserta')
                                            ->label('Peserta/Penerima')
                                            ->helperText('Nama peserta atau penerima prestasi')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Textarea::make('pembimbing')
                                            ->label('Pembimbing')
                                            ->helperText('Nama dosen/pembimbing jika ada')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        TextInput::make('link')
                                            ->label('Link Terkait')
                                            ->url()
                                            ->suffixIcon('heroicon-o-link')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Foto & Dokumen')
                                    ->schema([
                                        FileUpload::make('foto')
                                            ->label('Foto Utama')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('prestasi')
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->helperText('Ukuran maksimal 2MB'),

                                        FileUpload::make('gallery')
                                            ->label('Gallery Foto')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->multiple()
                                            ->directory('prestasi/gallery')
                                            ->maxSize(2048)
                                            ->maxFiles(10)
                                            ->reorderable()
                                            ->helperText('Maksimal 10 foto, masing-masing 2MB'),

                                        FileUpload::make('sertifikat')
                                            ->label('Sertifikat/Piagam')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('prestasi/sertifikat')
                                            ->maxSize(5120)
                                            ->helperText('PDF atau gambar, maksimal 5MB'),
                                    ])
                                    ->columns(1),
                            ]),

                        Tabs\Tab::make('Status')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Pengaturan Tampilan')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->helperText('Prestasi akan ditampilkan di website')
                                            ->default(true),

                                        Toggle::make('is_featured')
                                            ->label('Unggulan')
                                            ->helperText('Ditampilkan di halaman utama'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&background=random'),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color(fn (PrestasiTingkat $state): string => $state->color()),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (PrestasiKategori $state): string => $state->color()),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options(PrestasiTingkat::class),

                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(PrestasiKategori::class),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestasi::route('/'),
            'create' => Pages\CreatePrestasi::route('/create'),
            'view' => Pages\ViewPrestasi::route('/{record}'),
            'edit' => Pages\EditPrestasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        return match ($user->role) {
            UserRole::SUPERADMIN => $query,
            UserRole::UNIVERSITAS => $query->where('unit_type', UnitType::UNIVERSITAS),
            UserRole::FAKULTAS => $query->where('unit_type', UnitType::FAKULTAS)
                                        ->where('unit_id', $user->unit_id),
            UserRole::PRODI => $query->where('unit_type', UnitType::PRODI)
                                     ->where('unit_id', $user->unit_id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
