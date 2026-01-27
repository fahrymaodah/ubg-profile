<?php

namespace App\Filament\Resources;

use App\Enums\ArticleStatus;
use App\Enums\UnitType;
use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    use HasUnitScope;
    
    protected static ?string $model = Article::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Article')
                    ->tabs([
                        Tab::make('Konten Artikel')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                if ($state) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),

                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Textarea::make('excerpt')
                                            ->label('Ringkasan')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Ringkasan singkat artikel'),

                                        RichEditor::make('content')
                                            ->label('Isi Artikel')
                                            ->required()
                                            ->toolbarButtons([
                                                // === TEXT FORMATTING ===
                                                'bold',              // Tebal
                                                'italic',            // Miring
                                                'underline',         // Garis bawah
                                                'strike',            // Coret
                                                'subscript',         // Subscript (H₂O)
                                                'superscript',       // Superscript (X²)
                                                'small',             // Teks kecil
                                                'lead',              // Teks lead (lebih besar)
                                                
                                                // === HEADINGS ===
                                                'h1',                // Heading 1
                                                'h2',                // Heading 2
                                                'h3',                // Heading 3
                                                
                                                // === TEXT COLOR & HIGHLIGHT ===
                                                'textColor',         // Warna teks
                                                'highlight',         // Highlight/stabilo
                                                
                                                // === ALIGNMENT ===
                                                'alignStart',        // Rata kiri
                                                'alignCenter',       // Rata tengah
                                                'alignEnd',          // Rata kanan
                                                'alignJustify',      // Rata kiri-kanan
                                                
                                                // === LISTS ===
                                                'bulletList',        // Bullet list
                                                'orderedList',       // Numbered list
                                                
                                                // === LINKS & MEDIA ===
                                                'link',              // Hyperlink
                                                'attachFiles',       // Sisipkan gambar
                                                
                                                // === STRUCTURE ===
                                                'blockquote',        // Kutipan
                                                'code',              // Inline code
                                                'codeBlock',         // Blok kode
                                                'horizontalRule',    // Garis horizontal
                                                'details',           // Collapsible/accordion
                                                
                                                // === TABLE ===
                                                'table',             // Sisipkan tabel
                                                
                                                // === GRID/KOLOM ===
                                                'grid',              // Sisipkan grid/kolom
                                                'gridDelete',        // Hapus grid
                                                
                                                // === UTILITIES ===
                                                'undo',              // Undo
                                                'redo',              // Redo
                                                'clearFormatting',   // Hapus format
                                            ])
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('article-images')
                                            ->fileAttachmentsVisibility('public')
                                            ->resizableImages()
                                            ->extraInputAttributes(['style' => 'min-height: 400px;'])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('SEO')
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Judul SEO')
                                            ->maxLength(60),

                                        Textarea::make('meta_description')
                                            ->label('Deskripsi SEO')
                                            ->maxLength(160)
                                            ->rows(3),
                                    ])
                                    ->columns(1),

                                Section::make('Publikasi')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(ArticleStatus::class)
                                            ->default(ArticleStatus::DRAFT)
                                            ->required(),

                                        DateTimePicker::make('published_at')
                                            ->label('Tanggal Publikasi')
                                            ->default(now()),

                                        Toggle::make('is_featured')
                                            ->label('Unggulan'),

                                        Toggle::make('is_pinned')
                                            ->label('Pin di Atas'),
                                    ])
                                    ->columns(2),

                                Section::make('Kategori & Media')
                                    ->schema([
                                        Select::make('category_id')
                                            ->label('Kategori')
                                            ->relationship(
                                                'category',
                                                'name',
                                                fn (Builder $query) => $query->when(
                                                    !auth()->user()?->isSuperAdmin(),
                                                    fn ($q) => $q->where(function ($q) {
                                                        $user = auth()->user();
                                                        if ($user->isUniversitas()) {
                                                            $q->where('unit_type', UnitType::UNIVERSITAS);
                                                        } elseif ($user->unit_type && $user->unit_id) {
                                                            $q->where('unit_type', $user->unit_type)
                                                              ->where('unit_id', $user->unit_id);
                                                        }
                                                    })
                                                )
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        FileUpload::make('featured_image')
                                            ->label('Gambar Utama')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('articles'),

                                        FileUpload::make('gallery')
                                            ->label('Galeri')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->multiple()
                                            ->directory('articles/gallery')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                static::getUnitFormSection(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(60)
                    ->width(80),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),

                TextColumn::make('published_at')
                    ->label('Publikasi')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('status')
                    ->options(ArticleStatus::class),

                TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Super Admin can see all
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Universitas admin sees only universitas articles
        if ($user->isUniversitas()) {
            return $query->where('unit_type', UnitType::UNIVERSITAS);
        }

        // Fakultas can see only their own articles
        if ($user->isFakultas() && $user->unit_id) {
            return $query->where('unit_type', UnitType::FAKULTAS)
                         ->where('unit_id', $user->unit_id);
        }

        // Prodi can see only their own articles
        if ($user->isProdi() && $user->unit_id) {
            return $query->where('unit_type', UnitType::PRODI)
                         ->where('unit_id', $user->unit_id);
        }

        return $query->whereRaw('1 = 0');
    }
}