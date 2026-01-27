<?php

namespace App\Filament\Resources\ProdiResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DosenRelationManager extends RelationManager
{
    protected static string $relationship = 'dosen';

    protected static ?string $title = 'Dosen';

    protected static ?string $modelLabel = 'Dosen';

    protected static ?string $pluralModelLabel = 'Dosen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Diri')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nidn')
                                    ->label('NIDN')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('nip')
                                    ->label('NIP')
                                    ->maxLength(30),

                                Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ]),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('gelar_depan')
                                    ->label('Gelar Depan')
                                    ->maxLength(50),

                                TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('gelar_belakang')
                                    ->label('Gelar Belakang')
                                    ->maxLength(50),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email(),

                                TextInput::make('telepon')
                                    ->label('Telepon')
                                    ->tel(),
                            ]),

                        FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('dosen')
                            ->maxSize(2048),
                    ]),

                Section::make('Jabatan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('jabatan_fungsional')
                                    ->label('Jabatan Fungsional')
                                    ->placeholder('Asisten Ahli, Lektor, dll'),

                                TextInput::make('jabatan_struktural')
                                    ->label('Jabatan Struktural')
                                    ->placeholder('Kaprodi, Dekan, dll'),
                            ]),

                        TextInput::make('bidang_keahlian')
                            ->label('Bidang Keahlian'),
                    ])
                    ->collapsible(),

                Section::make('Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                                TextInput::make('order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama) . '&background=1e40af&color=fff'),

                TextColumn::make('full_name')
                    ->label('Nama')
                    ->getStateUsing(fn ($record) => trim($record->gelar_depan . ' ' . $record->nama . ', ' . $record->gelar_belakang, ', '))
                    ->searchable(['nama', 'gelar_depan', 'gelar_belakang'])
                    ->sortable('nama'),

                TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable(),

                TextColumn::make('jabatan_fungsional')
                    ->label('Jabatan Fungsional')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
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
