<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Halaman Statis';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->components([
                Tabs::make('Page')
                    ->tabs([
                        Tab::make('Konten')
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
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->columnSpanFull(),

                                        RichEditor::make('content')
                                            ->label('Konten')
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
                                            ->fileAttachmentsDirectory('page-images')
                                            ->fileAttachmentsVisibility('public')
                                            ->resizableImages()
                                            ->extraInputAttributes(['style' => 'min-height: 400px;'])
                                            ->columnSpanFull(),
                                    ])
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Pengaturan Umum')
                                    ->schema([
                                        Select::make('template')
                                            ->label('Template')
                                            ->options(Page::templates())
                                            ->default('default')
                                            ->required(),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('Hanya halaman aktif yang bisa diakses publik'),
                                    ])
                                    ->columns(2),

                                Section::make('SEO')
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(70)
                                            ->helperText('Maksimal 70 karakter untuk hasil pencarian optimal'),

                                        TextInput::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(160)
                                            ->helperText('Maksimal 160 karakter untuk hasil pencarian optimal'),
                                    ])
                                    ->columns(2),

                                Section::make('Unit')
                                    ->schema([
                                        Select::make('unit_type')
                                            ->label('Tipe Unit')
                                            ->options(collect(UnitType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                                            ->default(fn () => auth()->user()->unit_type?->value ?? UnitType::UNIVERSITAS->value)
                                            ->disabled(fn () => !in_array(auth()->user()->role, [UserRole::SUPERADMIN, UserRole::UNIVERSITAS]))
                                            ->dehydrated(),

                                        Hidden::make('unit_id')
                                            ->default(fn () => auth()->user()->unit_id),
                                    ])
                                    ->columns(2)
                                    ->visible(fn () => in_array(auth()->user()->role, [UserRole::SUPERADMIN, UserRole::UNIVERSITAS])),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('template')
                    ->label('Template')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Page::templates()[$state] ?? $state),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('unit_type')
                    ->label('Unit')
                    ->formatStateUsing(fn (UnitType $state) => $state->label())
                    ->badge()
                    ->visible(fn () => in_array(auth()->user()->role, [UserRole::SUPERADMIN, UserRole::UNIVERSITAS])),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('template')
                    ->label('Template')
                    ->options(Page::templates()),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Page $record) => "/page/{$record->slug}")
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

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
