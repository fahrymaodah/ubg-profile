<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Traits\HasUnitScope;
use App\Models\Slider;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SliderResource extends Resource
{
    use HasUnitScope;

    protected static ?string $model = Slider::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Slider';

    protected static ?string $modelLabel = 'Slider';

    protected static ?string $pluralModelLabel = 'Slider';

    protected static ?int $navigationSort = 3;

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
                                        DateTimePicker::make('start_date')
                                            ->label('Mulai Tayang')
                                            ->native(false)
                                            ->displayFormat('d M Y H:i')
                                            ->helperText('Kosongkan untuk langsung tayang'),

                                        DateTimePicker::make('end_date')
                                            ->label('Selesai Tayang')
                                            ->native(false)
                                            ->displayFormat('d M Y H:i')
                                            ->helperText('Kosongkan untuk tayang selamanya'),

                                        TextInput::make('order')
                                            ->label('Urutan')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Urutan tampil slider (kecil = lebih dulu)'),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('Slider akan ditampilkan di website'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(1),

                        // Right column: Konten Slider
                        Section::make('Konten Slider')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->maxLength(255),

                                TextInput::make('subtitle')
                                    ->label('Subtitle')
                                    ->maxLength(255),

                                TextInput::make('link')
                                    ->label('Link')
                                    ->url()
                                    ->suffixIcon('heroicon-o-link'),

                                TextInput::make('button_text')
                                    ->label('Teks Tombol')
                                    ->maxLength(50)
                                    ->helperText('Kosongkan jika tidak ingin menampilkan tombol'),

                                FileUpload::make('image')
                                    ->label('Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('sliders')
                                    ->imageEditor()
                                    ->required()
                                    ->maxSize(5120)
                                    ->helperText('Ukuran rekomendasi: 1920x600 piksel, maksimal 5MB'),
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
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(60)
                    ->width(120),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),

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
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
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
