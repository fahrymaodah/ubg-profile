<?php

namespace App\Filament\Resources\FakultasResource\RelationManagers;

use App\Enums\Jenjang;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class ProdiRelationManager extends RelationManager
{
    protected static string $relationship = 'prodi';

    protected static ?string $title = 'Program Studi';

    protected static ?string $modelLabel = 'Program Studi';

    protected static ?string $pluralModelLabel = 'Program Studi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama Program Studi')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if ($state) {
                                            $set('slug', Str::slug($state));
                                            $set('subdomain', Str::slug($state));
                                        }
                                    }),

                                TextInput::make('kode')
                                    ->label('Kode Prodi')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Contoh: TI, SI, MN'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('subdomain')
                                    ->label('Subdomain')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),

                                Select::make('jenjang')
                                    ->label('Jenjang')
                                    ->options(Jenjang::class)
                                    ->required(),
                            ]),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(2),
                    ]),

                Section::make('Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                                Toggle::make('is_published')
                                    ->label('Dipublikasikan')
                                    ->default(false),
                            ]),

                        TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&background=3b82f6&color=fff'),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge(),

                TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->badge(),

                TextColumn::make('subdomain')
                    ->label('Subdomain')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                SelectFilter::make('jenjang')
                    ->options(Jenjang::class),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->headerActions([
                CreateAction::make(),
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
}
