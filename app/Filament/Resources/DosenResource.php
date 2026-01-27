<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Filament\Resources\DosenResource\Pages;
use App\Models\Dosen;
use App\Models\Prodi;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DosenResource extends Resource
{
    protected static ?string $model = Dosen::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?string $modelLabel = 'Dosen';

    protected static ?string $pluralModelLabel = 'Dosen';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dosen')
                    ->tabs([
                        Tab::make('Data Diri')
                            ->schema([
                                Section::make('Informasi Dasar')
                                    ->schema([
                                        Select::make('prodi_id')
                                            ->label('Program Studi')
                                            ->options(function () {
                                                $user = auth()->user();
                                                $query = Prodi::with('fakultas')->where('is_active', true);
                                                
                                                if ($user->isProdi() && $user->unit_id) {
                                                    $query->where('id', $user->unit_id);
                                                } elseif ($user->isFakultas() && $user->unit_id) {
                                                    $query->where('fakultas_id', $user->unit_id);
                                                }
                                                
                                                return $query->get()->mapWithKeys(fn ($prodi) => [
                                                    $prodi->id => $prodi->nama . ' (' . $prodi->fakultas->nama . ')'
                                                ]);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('nidn')
                                                    ->label('NIDN')
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->unique(ignoreRecord: true),

                                                TextInput::make('nip')
                                                    ->label('NIP')
                                                    ->maxLength(30),

                                                Select::make('jenis_kelamin')
                                                    ->label('Jenis Kelamin')
                                                    ->options([
                                                        'L' => 'Laki-laki',
                                                        'P' => 'Perempuan',
                                                    ]),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('gelar_depan')
                                                    ->label('Gelar Depan')
                                                    ->maxLength(50)
                                                    ->placeholder('Dr., Prof.'),

                                                TextInput::make('nama')
                                                    ->label('Nama Lengkap')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('gelar_belakang')
                                                    ->label('Gelar Belakang')
                                                    ->maxLength(100)
                                                    ->placeholder('S.Kom., M.Cs.'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('tempat_lahir')
                                                    ->label('Tempat Lahir')
                                                    ->maxLength(100),

                                                DatePicker::make('tanggal_lahir')
                                                    ->label('Tanggal Lahir'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->maxLength(255),

                                                TextInput::make('telepon')
                                                    ->label('Telepon')
                                                    ->tel()
                                                    ->maxLength(20),
                                            ]),

                                        FileUpload::make('foto')
                                            ->label('Foto')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('dosen')
                                            ->maxSize(2048)
                                            ->imageEditor(),
                                    ]),
                            ]),

                        Tab::make('Kepegawaian')
                            ->schema([
                                Section::make('Jabatan & Pangkat')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('jabatan_fungsional')
                                                    ->label('Jabatan Fungsional')
                                                    ->options([
                                                        'Tenaga Pengajar' => 'Tenaga Pengajar',
                                                        'Asisten Ahli' => 'Asisten Ahli',
                                                        'Lektor' => 'Lektor',
                                                        'Lektor Kepala' => 'Lektor Kepala',
                                                        'Guru Besar' => 'Guru Besar',
                                                    ]),

                                                TextInput::make('jabatan_struktural')
                                                    ->label('Jabatan Struktural')
                                                    ->maxLength(100)
                                                    ->placeholder('Ketua Prodi, Dekan, dll'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                Select::make('golongan')
                                                    ->label('Golongan/Pangkat')
                                                    ->options([
                                                        'III/a' => 'III/a - Penata Muda',
                                                        'III/b' => 'III/b - Penata Muda Tk.I',
                                                        'III/c' => 'III/c - Penata',
                                                        'III/d' => 'III/d - Penata Tk.I',
                                                        'IV/a' => 'IV/a - Pembina',
                                                        'IV/b' => 'IV/b - Pembina Tk.I',
                                                        'IV/c' => 'IV/c - Pembina Utama Muda',
                                                        'IV/d' => 'IV/d - Pembina Utama Madya',
                                                        'IV/e' => 'IV/e - Pembina Utama',
                                                    ]),

                                                TextInput::make('bidang_keahlian')
                                                    ->label('Bidang Keahlian')
                                                    ->maxLength(255),
                                            ]),

                                        Textarea::make('bio')
                                            ->label('Biografi')
                                            ->rows(4),
                                    ]),
                            ]),

                        Tab::make('Pendidikan')
                            ->schema([
                                Repeater::make('pendidikan')
                                    ->label('Riwayat Pendidikan')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                Select::make('jenjang')
                                                    ->label('Jenjang')
                                                    ->options([
                                                        'S1' => 'S1',
                                                        'S2' => 'S2',
                                                        'S3' => 'S3',
                                                    ])
                                                    ->required(),

                                                TextInput::make('institusi')
                                                    ->label('Institusi')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('bidang')
                                                    ->label('Bidang Studi')
                                                    ->maxLength(255),

                                                TextInput::make('tahun')
                                                    ->label('Tahun Lulus')
                                                    ->numeric()
                                                    ->minValue(1950)
                                                    ->maxValue(date('Y')),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Tab::make('Penelitian & Publikasi')
                            ->schema([
                                Section::make('ID Peneliti')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('sinta_id')
                                                    ->label('SINTA ID')
                                                    ->maxLength(50),

                                                TextInput::make('google_scholar_id')
                                                    ->label('Google Scholar ID')
                                                    ->maxLength(50),

                                                TextInput::make('scopus_id')
                                                    ->label('Scopus ID')
                                                    ->maxLength(50),

                                                TextInput::make('orcid')
                                                    ->label('ORCID')
                                                    ->maxLength(50),
                                            ]),
                                    ]),

                                Repeater::make('penelitian')
                                    ->label('Penelitian')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('judul')
                                                    ->label('Judul')
                                                    ->required()
                                                    ->maxLength(500),

                                                TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->numeric(),

                                                TextInput::make('link')
                                                    ->label('Link')
                                                    ->url(),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible(),

                                Repeater::make('publikasi')
                                    ->label('Publikasi')
                                    ->schema([
                                        TextInput::make('judul')
                                            ->label('Judul')
                                            ->required()
                                            ->maxLength(500),
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('jurnal')
                                                    ->label('Jurnal/Prosiding')
                                                    ->maxLength(255),

                                                TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->numeric(),

                                                TextInput::make('link')
                                                    ->label('Link')
                                                    ->url(),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible(),

                                Repeater::make('pengabdian')
                                    ->label('Pengabdian Masyarakat')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('judul')
                                                    ->label('Judul')
                                                    ->required()
                                                    ->maxLength(500),

                                                TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->numeric(),

                                                TextInput::make('link')
                                                    ->label('Link')
                                                    ->url(),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible(),
                            ]),

                        Tab::make('Status')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),

                                        TextInput::make('order')
                                            ->label('Urutan')
                                            ->numeric()
                                            ->default(0),
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
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama) . '&background=3b82f6&color=fff'),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->description(fn ($record) => $record->full_name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable(),

                TextColumn::make('prodi.nama')
                    ->label('Program Studi')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jabatan_fungsional')
                    ->label('Jabatan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Guru Besar' => 'danger',
                        'Lektor Kepala' => 'warning',
                        'Lektor' => 'success',
                        'Asisten Ahli' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                SelectFilter::make('prodi_id')
                    ->label('Program Studi')
                    ->relationship('prodi', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('jabatan_fungsional')
                    ->label('Jabatan Fungsional')
                    ->options([
                        'Tenaga Pengajar' => 'Tenaga Pengajar',
                        'Asisten Ahli' => 'Asisten Ahli',
                        'Lektor' => 'Lektor',
                        'Lektor Kepala' => 'Lektor Kepala',
                        'Guru Besar' => 'Guru Besar',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDosen::route('/'),
            'create' => Pages\CreateDosen::route('/create'),
            'view' => Pages\ViewDosen::route('/{record}'),
            'edit' => Pages\EditDosen::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return $query;
        }

        if ($user->isFakultas() && $user->unit_id) {
            $prodiIds = Prodi::where('fakultas_id', $user->unit_id)->pluck('id');
            return $query->whereIn('prodi_id', $prodiIds);
        }

        if ($user->isProdi() && $user->unit_id) {
            return $query->where('prodi_id', $user->unit_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
