<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Filament\Resources\FakultasResource\Pages;
use App\Filament\Resources\FakultasResource\RelationManagers;
use App\Models\Fakultas;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FakultasResource extends Resource
{
    protected static ?string $model = Fakultas::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Unit';

    protected static ?string $modelLabel = 'Fakultas';

    protected static ?string $pluralModelLabel = 'Fakultas';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Fakultas')
                    ->tabs([
                        Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('nama')
                                                    ->label('Nama Fakultas')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                                        if ($state) {
                                                            $set('slug', Str::slug($state));
                                                            // Auto-generate subdomain from nama
                                                            $subdomain = Str::slug(
                                                                Str::of($state)->replace('Fakultas', '')->trim()
                                                            );
                                                            $set('subdomain', $subdomain);
                                                        }
                                                    }),

                                                TextInput::make('kode')
                                                    ->label('Kode Fakultas')
                                                    ->required()
                                                    ->maxLength(20)
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Contoh: FT, FEB, FIKES'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('URL-friendly name'),

                                                TextInput::make('subdomain')
                                                    ->label('Subdomain')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->unique(ignoreRecord: true)
                                                    ->prefix('https://')
                                                    ->suffix('.' . config('app.domain', 'ubg.ac.id'))
                                                    ->helperText('Contoh: teknik, feb, fikes'),
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
                                    ]),

                                Section::make('Sejarah')
                                    ->schema([
                                        RichEditor::make('sejarah')
                                            ->label('Sejarah Fakultas')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 300px;']),
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
                                                    ->label('Logo Fakultas')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('fakultas/logos')
                                                    ->maxSize(2048)
                                                    ->helperText('Max 2MB. Format: JPG, PNG'),

                                                FileUpload::make('banner')
                                                    ->label('Banner')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('fakultas/banners')
                                                    ->maxSize(5120)
                                                    ->helperText('Max 5MB. Ukuran optimal: 1920x400px'),
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
                                                    ->tel()
                                                    ->maxLength(20),

                                                TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->maxLength(255),
                                            ]),
                                    ]),

                                Section::make('Media Sosial')
                                    ->schema([
                                        Repeater::make('social_media')
                                            ->label('Akun Media Sosial')
                                            ->schema([
                                                Select::make('platform')
                                                    ->label('Platform')
                                                    ->options([
                                                        'facebook' => 'Facebook',
                                                        'twitter' => 'Twitter/X',
                                                        'instagram' => 'Instagram',
                                                        'youtube' => 'YouTube',
                                                        'linkedin' => 'LinkedIn',
                                                        'tiktok' => 'TikTok',
                                                    ])
                                                    ->required(),

                                                TextInput::make('url')
                                                    ->label('URL')
                                                    ->url()
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Tambah Media Sosial'),
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
                                                    ->default(true)
                                                    ->helperText('Fakultas tidak aktif tidak akan ditampilkan'),

                                                Toggle::make('is_published')
                                                    ->label('Dipublikasikan')
                                                    ->default(false)
                                                    ->helperText('Website fakultas bisa diakses publik'),
                                            ]),

                                        TextInput::make('order')
                                            ->label('Urutan Tampil')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Angka kecil tampil lebih dulu'),

                                        Textarea::make('coming_soon_message')
                                            ->label('Pesan Coming Soon')
                                            ->rows(2)
                                            ->placeholder('Website sedang dalam pengembangan...')
                                            ->helperText('Ditampilkan jika belum dipublikasikan'),
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
                                            ->label('Gunakan Tema Universitas')
                                            ->default(true)
                                            ->helperText('Jika aktif, akan mengikuti tema dari universitas'),
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
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=F&background=1e40af&color=fff'),

                TextColumn::make('nama')
                    ->label('Nama Fakultas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subdomain')
                    ->label('Subdomain')
                    ->formatStateUsing(fn ($state) => $state . '.' . config('app.domain', 'ubg.ac.id'))
                    ->copyable()
                    ->copyMessage('Subdomain disalin!')
                    ->toggleable(),

                TextColumn::make('prodi_count')
                    ->label('Prodi')
                    ->counts('prodi')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('visit')
                        ->label('Kunjungi Website')
                        ->icon('heroicon-o-globe-alt')
                        ->url(fn (Fakultas $record) => 'https://' . $record->subdomain . '.' . config('app.domain', 'ubg.ac.id'))
                        ->openUrlInNewTab()
                        ->visible(fn (Fakultas $record) => $record->is_published),
                    Action::make('bootstrap')
                        ->label('Generate Data Default')
                        ->icon('heroicon-o-sparkles')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Data Default')
                        ->modalDescription('Ini akan membuat data default (Settings, Menu, Kategori Artikel, Halaman) untuk fakultas ini. Data yang sudah ada tidak akan ditimpa.')
                        ->action(function (Fakultas $record) {
                            $service = app(UnitBootstrapService::class);
                            $service->bootstrapUnit(UnitType::FAKULTAS, $record->id);
                            
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
            RelationManagers\ProdiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFakultas::route('/'),
            'create' => Pages\CreateFakultas::route('/create'),
            'view' => Pages\ViewFakultas::route('/{record}'),
            'edit' => Pages\EditFakultas::route('/{record}/edit'),
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

        // Fakultas admin can only see their own fakultas
        if ($user->isFakultas() && $user->unit_id) {
            return $query->where('id', $user->unit_id);
        }

        // Prodi admin cannot manage fakultas
        return $query->whereRaw('1 = 0');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isUniversitas());
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isUniversitas());
    }
}
