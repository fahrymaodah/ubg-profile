<?php

namespace App\Filament\Traits;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Models\Fakultas;
use App\Models\Prodi;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Trait untuk menangani unit scope di Resource
 * 
 * - SuperAdmin & Universitas: Bisa memilih unit manapun
 * - Fakultas: Bisa memilih fakultasnya atau prodi di bawahnya
 * - Prodi: Unit otomatis terisi, section hidden
 */
trait HasUnitScope
{
    /**
     * Generate Section Informasi Unit dengan logic berdasarkan role user
     */
    public static function getUnitFormSection(int $columns = 2): Section
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->isSuperAdmin() ?? false;
        $isUniversitas = $user?->isUniversitas() ?? false;
        $isFakultas = $user?->isFakultas() ?? false;
        $isProdi = $user?->isProdi() ?? false;
        
        // Prodi user tidak perlu melihat section ini
        if ($isProdi) {
            return Section::make('Informasi Unit')
                ->schema([
                    Select::make('unit_type')
                        ->default(UnitType::PRODI->value)
                        ->dehydrated(true)
                        ->hidden(),
                    Select::make('unit_id')
                        ->default($user?->unit_id)
                        ->dehydrated(true)
                        ->hidden(),
                ])
                ->hidden();
        }
        
        // Tentukan options untuk unit_type berdasarkan role
        $unitTypeOptions = match (true) {
            $isFakultas => [
                UnitType::FAKULTAS->value => 'Fakultas',
                UnitType::PRODI->value => 'Program Studi',
            ],
            default => UnitType::toSelectOptions(),
        };
        
        return Section::make('Informasi Unit')
            ->schema([
                Select::make('unit_type')
                    ->label('Tipe Unit')
                    ->options($unitTypeOptions)
                    ->default(fn () => $user?->unit_type?->value)
                    ->required()
                    ->live()
                    ->dehydrated(true)
                    ->afterStateUpdated(function (Set $set) {
                        $set('unit_id', null);
                    })
                    ->helperText($isFakultas ? 'Pilih fakultas Anda atau prodi di bawahnya' : null),

                Select::make('unit_id')
                    ->label('Unit')
                    ->default(fn () => $user?->unit_id)
                    ->dehydrated(true)
                    ->options(function (Get $get) use ($user, $isFakultas) {
                        $unitType = $get('unit_type');
                        if (!$unitType) {
                            return [];
                        }

                        // Jika fakultas, batasi opsi
                        if ($isFakultas) {
                            return match ($unitType) {
                                UnitType::FAKULTAS->value => Fakultas::where('id', $user->unit_id)->pluck('nama', 'id')->toArray(),
                                UnitType::PRODI->value => Prodi::where('fakultas_id', $user->unit_id)->pluck('nama', 'id')->toArray(),
                                default => [],
                            };
                        }

                        // SuperAdmin/Universitas bisa pilih semua
                        return match (UnitType::tryFrom($unitType)) {
                            UnitType::UNIVERSITAS => ['1' => 'Universitas Bumigora'],
                            UnitType::FAKULTAS => Fakultas::pluck('nama', 'id')->toArray(),
                            UnitType::PRODI => Prodi::pluck('nama', 'id')->toArray(),
                            default => [],
                        };
                    })
                    ->required(fn (Get $get) => $get('unit_type') !== UnitType::UNIVERSITAS->value)
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('unit_type') !== UnitType::UNIVERSITAS->value),
            ])
            ->columns($columns);
    }

    /**
     * Mutate form data sebelum create - set unit dari user yang login jika prodi
     */
    public static function mutateDataWithUserUnit(array $data): array
    {
        $user = auth()->user();
        
        // Untuk prodi, selalu paksa unit mereka
        if ($user && $user->isProdi()) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        
        // Handle universitas case
        if (isset($data['unit_type']) && $data['unit_type'] === UnitType::UNIVERSITAS->value) {
            $data['unit_id'] = 1; // atau null, tergantung schema
        }
        
        return $data;
    }

    /**
     * Get default unit_type value berdasarkan user yang login
     */
    public static function getDefaultUnitType(): ?string
    {
        return auth()->user()?->unit_type?->value;
    }

    /**
     * Get default unit_id value berdasarkan user yang login
     */
    public static function getDefaultUnitId(): ?int
    {
        return auth()->user()?->unit_id;
    }
    
    /**
     * Check if current user needs unit selection form
     */
    public static function needsUnitSelection(): bool
    {
        $user = auth()->user();
        return !$user?->isProdi();
    }
}
