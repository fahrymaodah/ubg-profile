<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use App\Models\Prodi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';

    protected static string | \UnitEnum | null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Kategori Artikel';

    protected static ?string $pluralModelLabel = 'Kategori Artikel';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
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

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('articles_count')
                    ->label('Jumlah Artikel')
                    ->counts('articles')
                    ->sortable(),

                TextColumn::make('unit_type')
                    ->label('Unit')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
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

        // Universitas can see only universitas categories
        if ($user->isUniversitas()) {
            return $query->where('unit_type', UnitType::UNIVERSITAS);
        }

        // Fakultas can see only their own categories
        if ($user->isFakultas() && $user->unit_id) {
            return $query->where('unit_type', UnitType::FAKULTAS)
                         ->where('unit_id', $user->unit_id);
        }

        // Prodi can see only their own categories
        if ($user->isProdi() && $user->unit_id) {
            return $query->where('unit_type', UnitType::PRODI)
                         ->where('unit_id', $user->unit_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            $data['unit_type'] = UnitType::UNIVERSITAS;
            $data['unit_id'] = null;
        } else {
            $data['unit_type'] = $user->unit_type;
            $data['unit_id'] = $user->unit_id;
        }

        return $data;
    }
}
