<?php

namespace App\Filament\Resources;

use App\Enums\MenuType;
use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\MenuResource\Pages;
use App\Models\Article;
use App\Models\Menu;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bars-3';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Menu';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Menu')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Menu')
                                    ->required()
                                    ->maxLength(100),

                                Select::make('type')
                                    ->label('Tipe Menu')
                                    ->options(collect(MenuType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($set) => $set('url', null)),
                            ]),

                        Select::make('parent_id')
                            ->label('Parent Menu')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn (Builder $query) => $query->whereNull('parent_id')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih sebagai root menu'),

                        TextInput::make('url')
                            ->label('URL')
                            ->placeholder('/halaman atau https://example.com')
                            ->helperText('Bisa relative (/berita) atau absolute (https://example.com)')
                            ->visible(fn ($get) => in_array($get('type'), [MenuType::LINK->value, MenuType::BUTTON->value]))
                            ->required(fn ($get) => in_array($get('type'), [MenuType::LINK->value, MenuType::BUTTON->value])),

                        Select::make('article_id')
                            ->label('Artikel')
                            ->options(fn () => Article::published()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('type') === MenuType::ARTICLE->value)
                            ->required(fn ($get) => $get('type') === MenuType::ARTICLE->value),

                        Select::make('page_id')
                            ->label('Halaman')
                            ->options(fn () => Page::active()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('type') === MenuType::PAGE->value)
                            ->required(fn ($get) => $get('type') === MenuType::PAGE->value),

                        Grid::make(3)
                            ->schema([
                                Select::make('target')
                                    ->label('Target Link')
                                    ->options([
                                        '_self' => 'Same Window',
                                        '_blank' => 'New Window',
                                    ])
                                    ->default('_self'),

                                TextInput::make('icon')
                                    ->label('Icon (CSS Class)')
                                    ->placeholder('heroicon-o-home'),

                                TextInput::make('css_class')
                                    ->label('CSS Class')
                                    ->placeholder('custom-class'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),
                    ]),

                Section::make('Informasi Unit')
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
                    ->visible(fn () => in_array(auth()->user()->role, [UserRole::SUPERADMIN, UserRole::UNIVERSITAS])),
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
                    ->description(fn (Menu $record) => $record->isMandatory() ? 'Menu wajib' : null)
                    ->icon(fn (Menu $record) => $record->isMandatory() ? 'heroicon-o-lock-closed' : null)
                    ->iconColor('warning'),

                TextColumn::make('parent.title')
                    ->label('Parent')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (MenuType $state) => $state->label())
                    ->color(fn (MenuType $state) => match ($state) {
                        MenuType::LINK => 'info',
                        MenuType::ARTICLE => 'success',
                        MenuType::PAGE => 'warning',
                        MenuType::DROPDOWN => 'gray',
                        MenuType::LOGIN => 'danger',
                    }),

                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('unit_type')
                    ->label('Pemilik')
                    ->formatStateUsing(function ($record) {
                        $unitType = $record->unit_type;
                        $unitId = $record->unit_id;
                        
                        if ($unitType === UnitType::UNIVERSITAS) {
                            return 'Universitas';
                        } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
                            $fakultas = \App\Models\Fakultas::find($unitId);
                            return $fakultas ? $fakultas->nama : 'Fakultas #' . $unitId;
                        } elseif ($unitType === UnitType::PRODI && $unitId) {
                            $prodi = \App\Models\Prodi::find($unitId);
                            return $prodi ? $prodi->nama : 'Prodi #' . $unitId;
                        }
                        return $unitType->label();
                    })
                    ->badge()
                    ->color(fn ($record) => match ($record->unit_type) {
                        UnitType::UNIVERSITAS => 'primary',
                        UnitType::FAKULTAS => 'success',
                        UnitType::PRODI => 'warning',
                        default => 'gray',
                    })
                    ->visible(fn () => auth()->user()->role === UserRole::SUPERADMIN),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(collect(MenuType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),

                SelectFilter::make('parent_id')
                    ->label('Level')
                    ->options([
                        'root' => 'Root Menu',
                        'child' => 'Sub Menu',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'root') {
                            $query->whereNull('parent_id');
                        } elseif ($data['value'] === 'child') {
                            $query->whereNotNull('parent_id');
                        }
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Menu $record) => $record->isMandatory())
                    ->tooltip(fn (Menu $record) => $record->isMandatory() ? 'Menu wajib tidak dapat dihapus' : null),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->deselectRecordsAfterCompletion()
                        ->before(function (DeleteBulkAction $action, $records) {
                            // Filter out mandatory menus
                            $deletableRecords = $records->filter(fn (Menu $record) => !$record->isMandatory());
                            if ($deletableRecords->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Tidak ada menu yang dapat dihapus')
                                    ->body('Menu wajib (Beranda, Profil, Akademik, Kontak) tidak dapat dihapus.')
                                    ->send();
                                $action->cancel();
                            }
                        }),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Menu hanya ditampilkan untuk unit yang login saja (tidak cascade ke child)
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
