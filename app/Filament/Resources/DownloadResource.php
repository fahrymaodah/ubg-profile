<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Filament\Resources\DownloadResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Download;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadResource extends Resource
{
    use HasUnitScope;

    protected static ?string $model = Download::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Download';

    protected static ?string $modelLabel = 'Download';

    protected static ?string $pluralModelLabel = 'Download';

    protected static ?int $navigationSort = 5;

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
                                            ->helperText('Urutan tampil file (kecil = lebih dulu)'),

                                        TextInput::make('file_size')
                                            ->label('Ukuran File')
                                            ->helperText('Akan otomatis terdeteksi saat upload')
                                            ->disabled(),

                                        TextInput::make('download_count')
                                            ->label('Jumlah Download')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Jumlah kali file didownload')
                                            ->disabled(),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('File akan ditampilkan di website'),

                                        Placeholder::make('download_count_display')
                                            ->label('Total Download')
                                            ->content(fn ($record) => $record?->download_count ?? 0)
                                            ->visible(fn ($record) => $record !== null),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(1),

                        // Right column: Informasi File
                        Section::make('Informasi File')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('category')
                                    ->label('Kategori')
                                    ->maxLength(100)
                                    ->helperText('Contoh: Akademik, Keuangan, Regulasi'),

                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),

                                FileUpload::make('file')
                                    ->label('File')
                                    ->required()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('downloads')
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-powerpoint',
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        'application/zip',
                                        'application/x-rar-compressed',
                                    ])
                                    ->maxSize(51200) // 50MB
                                    ->helperText('PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR - Maks 50MB'),

                                Hidden::make('file_size'),
                            ])
                            ->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('extension')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pdf' => 'danger',
                        'doc', 'docx' => 'primary',
                        'xls', 'xlsx' => 'success',
                        'ppt', 'pptx' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('formatted_size')
                    ->label('Ukuran'),

                TextColumn::make('download_count')
                    ->label('Download')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn () => Download::whereNotNull('category')
                        ->distinct()
                        ->pluck('category', 'category')
                        ->toArray()
                    ),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Download $record): string => Storage::url($record->file))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
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
            'index' => Pages\ListDownloads::route('/'),
            'create' => Pages\CreateDownload::route('/create'),
            'edit' => Pages\EditDownload::route('/{record}/edit'),
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
