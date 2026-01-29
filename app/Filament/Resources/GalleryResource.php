<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Gallery;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GalleryResource extends Resource
{
    use HasUnitScope;

    protected static ?string $model = Gallery::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'Gallery';

    protected static ?string $pluralModelLabel = 'Gallery';

    protected static ?int $navigationSort = 4;

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
                                        TextInput::make('order')
                                            ->label('Urutan')
                                            ->numeric()
                                            ->default(0)
                                            ->columnSpan(2),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),

                                        Toggle::make('is_featured')
                                            ->label('Unggulan')
                                            ->helperText('Ditampilkan di halaman utama')
                                            ->inline(false)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4),
                            ])
                            ->columnSpan(1),

                        // Right column: Konten Gallery
                        Section::make('Konten Gallery')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),

                                Select::make('type')
                                    ->label('Tipe')
                                    ->options([
                                        'image' => 'Gambar',
                                        'video' => 'Video YouTube',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('image'),

                                FileUpload::make('file')
                                    ->label('Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('gallery')
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->helperText('Maksimal 5MB')
                                    ->visible(fn (Get $get) => $get('type') === 'image')
                                    ->required(fn (Get $get) => $get('type') === 'image'),

                                TextInput::make('youtube_url')
                                    ->label('URL YouTube')
                                    ->url()
                                    ->suffixIcon('heroicon-o-play')
                                    ->helperText('Contoh: https://www.youtube.com/watch?v=xxxxx')
                                    ->visible(fn (Get $get) => $get('type') === 'video')
                                    ->required(fn (Get $get) => $get('type') === 'video'),
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
                ImageColumn::make('file')
                    ->label('Preview')
                    ->disk('public')
                    ->height(60)
                    ->width(80)
                    ->defaultImageUrl(fn ($record) => $record->youtube_thumbnail ?? 'https://via.placeholder.com/80x60?text=Video'),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => 'Gambar',
                        'video' => 'Video',
                        default => $state,
                    }),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),

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
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'image' => 'Gambar',
                        'video' => 'Video',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

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
            ->defaultSort('order')
            ->reorderable('order');
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
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
