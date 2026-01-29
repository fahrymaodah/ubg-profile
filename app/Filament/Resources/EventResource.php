<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Event;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    use HasUnitScope;

    protected static ?string $model = Event::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Event';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Event';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        // Left column: Jadwal, Media & Pengaturan, Unit (stacked)
                        Grid::make(1)
                            ->schema([
                                Section::make('Jadwal Event')
                                    ->schema([
                                        DateTimePicker::make('start_date')
                                            ->label('Tanggal Mulai')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d M Y H:i'),

                                        DateTimePicker::make('end_date')
                                            ->label('Tanggal Selesai')
                                            ->native(false)
                                            ->displayFormat('d M Y H:i')
                                            ->after('start_date')
                                            ->helperText('Kosongkan jika event hanya 1 hari'),
                                    ])
                                    ->columns(2),

                                Section::make('Media & Pengaturan')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Gambar/Poster')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('events')
                                            ->imageEditor()
                                            ->maxSize(5120)
                                            ->helperText('Maksimal 5MB')
                                            ->columnSpanFull(),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->inline(false),

                                        Toggle::make('is_featured')
                                            ->label('Unggulan')
                                            ->helperText('Ditampilkan di halaman utama')
                                            ->inline(false),
                                    ])
                                    ->columns(2),

                                static::getUnitFormSection(),
                            ])
                            ->columnSpan(1),

                        // Right column: Informasi Event
                        Section::make('Informasi Event')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Event')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->label('Deskripsi')
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
                                    ->fileAttachmentsDirectory('event-images')
                                    ->fileAttachmentsVisibility('public')
                                    ->resizableImages()
                                    ->extraInputAttributes(['style' => 'min-height: 300px;'])
                                    ->columnSpanFull(),

                                TextInput::make('location')
                                    ->label('Lokasi')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-map-pin'),

                                TextInput::make('registration_link')
                                    ->label('Link Pendaftaran')
                                    ->url()
                                    ->suffixIcon('heroicon-o-link'),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Poster')
                    ->disk('public')
                    ->height(60)
                    ->width(80),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('-'),

                TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'upcoming' => 'info',
                        'ongoing' => 'success',
                        'past' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'upcoming' => 'Akan Datang',
                        'ongoing' => 'Berlangsung',
                        'past' => 'Selesai',
                        default => $state,
                    }),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'upcoming' => 'Akan Datang',
                        'ongoing' => 'Berlangsung',
                        'past' => 'Selesai',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'upcoming' => $query->upcoming(),
                        'ongoing' => $query->ongoing(),
                        'past' => $query->past(),
                        default => $query,
                    }),

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
            ->defaultSort('start_date', 'desc');
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
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
