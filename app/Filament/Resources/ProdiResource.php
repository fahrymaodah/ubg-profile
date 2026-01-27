<?php

namespace App\Filament\Resources;

use App\Enums\Jenjang;
use App\Enums\UnitType;
use App\Filament\Resources\ProdiResource\Pages;
use App\Filament\Resources\ProdiResource\RelationManagers;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Services\UnitBootstrapService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProdiResource extends Resource
{
    protected static ?string $model = Prodi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Unit';

    protected static ?string $modelLabel = 'Program Studi';

    protected static ?string $pluralModelLabel = 'Program Studi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Program Studi')
                    ->tabs([
                        Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('fakultas_id')
                                            ->label('Fakultas')
                                            ->relationship('fakultas', 'nama')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('nama')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('kode')
                                                    ->required()
                                                    ->maxLength(20),
                                                TextInput::make('subdomain')
                                                    ->required()
                                                    ->maxLength(100),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('nama')
                                                    ->label('Nama Program Studi')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                                        if ($state) {
                                                            $set('slug', Str::slug($state));
                                                            $set('subdomain', Str::slug($state));
                                                        }
                                                    }),

                                                TextInput::make('kode')
                                                    ->label('Kode Prodi')
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Contoh: TI, SI, MN'),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true),

                                                TextInput::make('subdomain')
                                                    ->label('Subdomain')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->unique(ignoreRecord: true)
                                                    ->prefix('https://')
                                                    ->suffix('.' . config('app.domain', 'ubg.ac.id')),

                                                Select::make('jenjang')
                                                    ->label('Jenjang')
                                                    ->options(Jenjang::class)
                                                    ->required(),
                                            ]),

                                        Textarea::make('deskripsi')
                                            ->label('Deskripsi Singkat')
                                            ->rows(3)
                                            ->maxLength(500),
                                    ]),
                            ]),

                        Tab::make('Profil')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Visi & Misi')
                                    ->schema([
                                        RichEditor::make('visi')
                                            ->label('Visi')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),

                                        RichEditor::make('misi')
                                            ->label('Misi')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),

                                        RichEditor::make('tujuan')
                                            ->label('Tujuan')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),
                                    ]),

                                Section::make('Profil Lulusan')
                                    ->schema([
                                        RichEditor::make('profil_lulusan')
                                            ->label('Profil Lulusan')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),

                                        RichEditor::make('kompetensi')
                                            ->label('Kompetensi Lulusan')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Akreditasi')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Section::make('Informasi Akreditasi')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('akreditasi')
                                                    ->label('Peringkat Akreditasi')
                                                    ->placeholder('Unggul / A / B / C'),

                                                TextInput::make('no_sk_akreditasi')
                                                    ->label('No. SK Akreditasi'),

                                                TextInput::make('tanggal_akreditasi')
                                                    ->label('Tanggal Akreditasi')
                                                    ->type('date'),
                                            ]),

                                        FileUpload::make('sertifikat_akreditasi')
                                            ->label('Sertifikat Akreditasi')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('prodi/akreditasi')
                                            ->maxSize(10240)
                                            ->helperText('Max 10MB. Format: PDF'),
                                    ]),

                                Section::make('Kurikulum')
                                    ->schema([
                                        FileUpload::make('kurikulum_file')
                                            ->label('File Kurikulum')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('prodi/kurikulum')
                                            ->maxSize(20480)
                                            ->helperText('Max 20MB. Format: PDF'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Logo & Banner')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('logo')
                                                    ->label('Logo Program Studi')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('prodi/logos')
                                                    ->maxSize(2048),

                                                FileUpload::make('banner')
                                                    ->label('Banner')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('prodi/banners')
                                                    ->maxSize(5120),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Kontak')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Informasi Kontak')
                                    ->schema([
                                        Textarea::make('alamat')
                                            ->label('Alamat')
                                            ->rows(2),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('telepon')
                                                    ->label('Telepon')
                                                    ->tel(),

                                                TextInput::make('email')
                                                    ->label('Email')
                                                    ->email(),
                                            ]),
                                    ]),

                                Section::make('Media Sosial')
                                    ->schema([
                                        Repeater::make('social_media')
                                            ->label('Akun Media Sosial')
                                            ->schema([
                                                Select::make('platform')
                                                    ->options([
                                                        'facebook' => 'Facebook',
                                                        'twitter' => 'Twitter/X',
                                                        'instagram' => 'Instagram',
                                                        'youtube' => 'YouTube',
                                                        'linkedin' => 'LinkedIn',
                                                    ])
                                                    ->required(),

                                                TextInput::make('url')
                                                    ->url()
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Status & Publikasi')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->label('Aktif')
                                                    ->default(true),

                                                Toggle::make('is_published')
                                                    ->label('Dipublikasikan')
                                                    ->default(false),
                                            ]),

                                        TextInput::make('order')
                                            ->label('Urutan Tampil')
                                            ->numeric()
                                            ->default(0),

                                        Textarea::make('coming_soon_message')
                                            ->label('Pesan Coming Soon')
                                            ->rows(2)
                                            ->placeholder('Website sedang dalam pengembangan...'),
                                    ]),

                                Section::make('Tema')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('theme_primary_color')
                                                    ->label('Warna Primer')
                                                    ->type('color')
                                                    ->default('#1e40af'),

                                                TextInput::make('theme_secondary_color')
                                                    ->label('Warna Sekunder')
                                                    ->type('color')
                                                    ->default('#3b82f6'),
                                            ]),

                                        Toggle::make('use_parent_theme')
                                            ->label('Gunakan Tema Fakultas')
                                            ->default(true),
                                    ])
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&background=3b82f6&color=fff'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fakultas.nama')
                    ->label('Fakultas')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->badge(),

                Tables\Columns\TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->badge(),

                Tables\Columns\TextColumn::make('akreditasi')
                    ->label('Akreditasi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Unggul', 'A' => 'success',
                        'Baik Sekali', 'B' => 'info',
                        'Baik', 'C' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subdomain')
                    ->label('Subdomain')
                    ->formatStateUsing(fn ($state) => $state . '.' . config('app.domain', 'ubg.ac.id'))
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('dosen_count')
                    ->label('Dosen')
                    ->counts('dosen')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('fakultas_id')
                    ->label('Fakultas')
                    ->relationship('fakultas', 'nama'),

                Tables\Filters\SelectFilter::make('jenjang')
                    ->options(Jenjang::class),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('visit')
                        ->label('Kunjungi Website')
                        ->icon('heroicon-o-globe-alt')
                        ->url(fn (Prodi $record) => 'https://' . $record->subdomain . '.' . config('app.domain', 'ubg.ac.id'))
                        ->openUrlInNewTab()
                        ->visible(fn (Prodi $record) => $record->is_published),
                    Action::make('bootstrap')
                        ->label('Generate Data Default')
                        ->icon('heroicon-o-sparkles')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Data Default')
                        ->modalDescription('Ini akan membuat data default (Settings, Menu, Kategori Artikel, Halaman) untuk prodi ini. Data yang sudah ada tidak akan ditimpa.')
                        ->action(function (Prodi $record) {
                            $service = app(UnitBootstrapService::class);
                            $service->bootstrapUnit(UnitType::PRODI, $record->id);
                            
                            Notification::make()
                                ->title('Data default berhasil dibuat')
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => auth()->user()?->isSuperAdmin()),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DosenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProdis::route('/'),
            'create' => Pages\CreateProdi::route('/create'),
            'view' => Pages\ViewProdi::route('/{record}'),
            'edit' => Pages\EditProdi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Super admin and universitas can see all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return $query;
        }

        // Fakultas admin can see prodi in their fakultas
        if ($user->isFakultas() && $user->unit_id) {
            return $query->where('fakultas_id', $user->unit_id);
        }

        // Prodi admin can only see their own prodi
        if ($user->isProdi() && $user->unit_id) {
            return $query->where('id', $user->unit_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isUniversitas() || $user->isFakultas());
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isUniversitas());
    }
}
