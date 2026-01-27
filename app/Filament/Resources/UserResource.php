<?php

namespace App\Filament\Resources;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('Data akun pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->label('Password')
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : null),
                    ])
                    ->columns(2),

                Section::make('Hak Akses')
                    ->description('Pengaturan role dan unit akses')
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options(function ($record) {
                                $user = auth()->user();
                                
                                // Jika user edit akun sendiri yang bukan superadmin, tampilkan hanya rolenya
                                if ($record && $record->id === $user->id && !$user->isSuperAdmin()) {
                                    return [$user->role->value => $user->role->label()];
                                }
                                
                                // Super Admin bisa pilih semua role
                                if ($user->isSuperAdmin()) {
                                    return collect(UserRole::cases())
                                        ->mapWithKeys(fn($role) => [$role->value => $role->label()])
                                        ->toArray();
                                }
                                
                                // Universitas tidak bisa pilih Super Admin
                                if ($user->isUniversitas()) {
                                    return collect(UserRole::cases())
                                        ->filter(fn($role) => $role !== UserRole::SUPERADMIN)
                                        ->mapWithKeys(fn($role) => [$role->value => $role->label()])
                                        ->toArray();
                                }
                                
                                // Fakultas hanya bisa pilih Fakultas dan Prodi
                                if ($user->isFakultas()) {
                                    return collect(UserRole::cases())
                                        ->filter(fn($role) => in_array($role, [UserRole::FAKULTAS, UserRole::PRODI]))
                                        ->mapWithKeys(fn($role) => [$role->value => $role->label()])
                                        ->toArray();
                                }
                                
                                return [];
                            })
                            ->required()
                            ->live()
                            ->disabled(function ($record) {
                                $user = auth()->user();
                                return $record && $record->id === $user->id && !$user->isSuperAdmin();
                            }),

                        Select::make('fakultas_id')
                            ->label('Pilih Fakultas')
                            ->options(function () {
                                $user = auth()->user();
                                $query = Fakultas::where('is_active', true);
                                
                                // Jika user fakultas, hanya tampilkan fakultasnya sendiri
                                if ($user->isFakultas() && $user->unit_id) {
                                    $query->where('id', $user->unit_id);
                                }
                                
                                return $query->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get): bool {
                                $role = $get('role');
                                $roleValue = $role instanceof UserRole ? $role->value : $role;
                                return $roleValue === 'fakultas';
                            })
                            ->required(function (Get $get): bool {
                                $role = $get('role');
                                $roleValue = $role instanceof UserRole ? $role->value : $role;
                                return $roleValue === 'fakultas';
                            })
                            ->disabled(function ($record) {
                                $user = auth()->user();
                                return $record && $record->id === $user->id && !$user->isSuperAdmin();
                            })
                            ->helperText('Pengguna hanya dapat mengakses data fakultas yang dipilih'),

                        Select::make('prodi_id')
                            ->label('Pilih Program Studi')
                            ->options(function () {
                                $user = auth()->user();
                                $query = Prodi::with('fakultas')->where('is_active', true);
                                
                                // Jika user fakultas, hanya tampilkan prodi di fakultasnya
                                if ($user->isFakultas() && $user->unit_id) {
                                    $query->where('fakultas_id', $user->unit_id);
                                }
                                
                                return $query->get()->mapWithKeys(fn ($prodi) => [
                                    $prodi->id => $prodi->nama . ' (' . $prodi->fakultas->nama . ')'
                                ]);
                            })
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get): bool {
                                $role = $get('role');
                                $roleValue = $role instanceof UserRole ? $role->value : $role;
                                return $roleValue === 'prodi';
                            })
                            ->required(function (Get $get): bool {
                                $role = $get('role');
                                $roleValue = $role instanceof UserRole ? $role->value : $role;
                                return $roleValue === 'prodi';
                            })
                            ->disabled(function ($record) {
                                $user = auth()->user();
                                return $record && $record->id === $user->id && !$user->isSuperAdmin();
                            })
                            ->helperText('Pengguna hanya dapat mengakses data program studi yang dipilih'),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Pengguna yang tidak aktif tidak dapat login'),
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

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('unit_type')
                    ->label('Tipe Unit')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('unit_name')
                    ->label('Unit')
                    ->getStateUsing(function (User $record): ?string {
                        if ($record->unit_type === UnitType::FAKULTAS && $record->unit_id) {
                            return Fakultas::find($record->unit_id)?->nama;
                        }
                        if ($record->unit_type === UnitType::PRODI && $record->unit_id) {
                            return Prodi::find($record->unit_id)?->nama;
                        }
                        return '-';
                    })
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(UserRole::class),

                SelectFilter::make('unit_type')
                    ->label('Tipe Unit')
                    ->options(UnitType::class),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(function (User $record) {
                        $user = auth()->user();
                        // Prodi tidak bisa delete siapapun
                        if ($user->isProdi()) {
                            return false;
                        }
                        // User tidak bisa delete diri sendiri
                        if ($record->id === $user->id) {
                            return false;
                        }
                        return true;
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function () {
                            $user = auth()->user();
                            // Prodi tidak bisa bulk delete
                            return !$user->isProdi();
                        }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Super admin can see all users
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Admin universitas can see all except super admin
        if ($user->isUniversitas()) {
            return $query->where('role', '!=', UserRole::SUPERADMIN);
        }

        // Fakultas admin can see users in their fakultas and prodi under it
        if ($user->isFakultas() && $user->unit_id) {
            $prodiIds = Prodi::where('fakultas_id', $user->unit_id)->pluck('id');

            return $query->whereIn('role', [UserRole::FAKULTAS, UserRole::PRODI])
                ->where(function ($q) use ($user, $prodiIds) {
                    $q->where(function ($q) use ($user) {
                        $q->where('unit_type', UnitType::FAKULTAS)
                          ->where('unit_id', $user->unit_id);
                    })->orWhere(function ($q) use ($prodiIds) {
                        $q->where('unit_type', UnitType::PRODI)
                          ->whereIn('unit_id', $prodiIds);
                    });
                });
        }

        // Prodi admin can only see their own account
        return $query->where('id', $user->id);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isUniversitas() || $user->isFakultas());
    }
}
