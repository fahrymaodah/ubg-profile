<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Announcement;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AnnouncementResource extends Resource
{
    use HasUnitScope;

    protected static ?string $model = Announcement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $modelLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        // Left column: Informasi Unit + Pengaturan (stacked)
                        Grid::make(1)
                            ->schema([
                                static::getUnitFormSection(),

                                Section::make('Pengaturan')
                                    ->schema([
                                        DateTimePicker::make('published_at')
                                            ->label('Tanggal Publish')
                                            ->native(false)
                                            ->displayFormat('d M Y H:i')
                                            ->default(now())
                                            ->helperText('Kosongkan untuk publish sekarang')
                                            ->columnSpanFull(),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),

                                        Toggle::make('is_urgent')
                                            ->label('Penting')
                                            ->helperText('Ditandai sebagai pengumuman penting'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(1),

                        // Right column: Konten Pengumuman
                        Section::make('Konten Pengumuman')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255),

                                RichEditor::make('content')
                                    ->label('Isi Pengumuman')
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
                                    ->fileAttachmentsDirectory('announcement-images')
                                    ->fileAttachmentsVisibility('public')
                                    ->resizableImages()
                                    ->extraInputAttributes(['style' => 'min-height: 300px;']),

                                Select::make('priority')
                                    ->label('Prioritas')
                                    ->options([
                                        'low' => 'Rendah',
                                        'normal' => 'Normal',
                                        'high' => 'Tinggi',
                                        'urgent' => 'Urgent',
                                    ])
                                    ->default('normal')
                                    ->required(),

                                FileUpload::make('attachment')
                                    ->label('Lampiran')
                                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->directory('announcements')
                                    ->maxSize(5120)
                                    ->helperText('Format: PDF, DOC, DOCX, atau gambar. Maksimal 5MB'),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Rendah',
                        'normal' => 'Normal',
                        'high' => 'Tinggi',
                        'urgent' => 'Urgent',
                        default => $state,
                    }),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Langsung'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Selamanya'),

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
                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'low' => 'Rendah',
                        'normal' => 'Normal',
                        'high' => 'Tinggi',
                        'urgent' => 'Urgent',
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
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
