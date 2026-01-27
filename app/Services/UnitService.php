<?php

namespace App\Services;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UnitService
{
    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 3600;

    /**
     * Get unit by type and ID
     */
    public function getUnit(UnitType $unitType, ?int $unitId = null)
    {
        return match ($unitType) {
            UnitType::UNIVERSITAS => null, // No model for universitas
            UnitType::FAKULTAS => Fakultas::find($unitId),
            UnitType::PRODI => Prodi::find($unitId),
        };
    }

    /**
     * Get unit by subdomain
     */
    public function getUnitBySubdomain(string $subdomain): array
    {
        // Check if it's fakultas
        $fakultas = Fakultas::findBySubdomain($subdomain);
        if ($fakultas) {
            return [
                'type' => UnitType::FAKULTAS,
                'unit' => $fakultas,
            ];
        }

        // Check if it's prodi
        $prodi = Prodi::findBySubdomain($subdomain);
        if ($prodi) {
            return [
                'type' => UnitType::PRODI,
                'unit' => $prodi,
            ];
        }

        return [
            'type' => null,
            'unit' => null,
        ];
    }

    /**
     * Get all fakultas
     */
    public function getAllFakultas(): Collection
    {
        return Cache::remember('all_fakultas', $this->cacheTtl, function () {
            return Fakultas::active()->ordered()->get();
        });
    }

    /**
     * Get all prodi
     */
    public function getAllProdi(): Collection
    {
        return Cache::remember('all_prodi', $this->cacheTtl, function () {
            return Prodi::active()->ordered()->with('fakultas')->get();
        });
    }

    /**
     * Get prodi by fakultas
     */
    public function getProdiByFakultas(int $fakultasId): Collection
    {
        return Cache::remember("prodi_fakultas_{$fakultasId}", $this->cacheTtl, function () use ($fakultasId) {
            return Prodi::where('fakultas_id', $fakultasId)
                ->active()
                ->ordered()
                ->get();
        });
    }

    /**
     * Get unit hierarchy (for breadcrumbs, navigation)
     */
    public function getUnitHierarchy(UnitType $unitType, ?int $unitId = null): array
    {
        $hierarchy = [
            [
                'type' => UnitType::UNIVERSITAS,
                'name' => config('app.name', 'Universitas Bumigora'),
                'url' => config('app.url'),
            ],
        ];

        if ($unitType === UnitType::UNIVERSITAS) {
            return $hierarchy;
        }

        if ($unitType === UnitType::FAKULTAS) {
            $fakultas = Fakultas::find($unitId);
            if ($fakultas) {
                $hierarchy[] = [
                    'type' => UnitType::FAKULTAS,
                    'name' => $fakultas->nama,
                    'url' => $fakultas->url,
                ];
            }
            return $hierarchy;
        }

        if ($unitType === UnitType::PRODI) {
            $prodi = Prodi::with('fakultas')->find($unitId);
            if ($prodi) {
                if ($prodi->fakultas) {
                    $hierarchy[] = [
                        'type' => UnitType::FAKULTAS,
                        'name' => $prodi->fakultas->nama,
                        'url' => $prodi->fakultas->url,
                    ];
                }
                $hierarchy[] = [
                    'type' => UnitType::PRODI,
                    'name' => $prodi->nama,
                    'url' => $prodi->url,
                ];
            }
        }

        return $hierarchy;
    }

    /**
     * Get unit options for select (admin forms)
     */
    public function getUnitSelectOptions(User $user): array
    {
        $options = [];

        if ($user->isSuperAdmin()) {
            // Add universitas option
            $options[] = [
                'value' => 'universitas:',
                'label' => 'Universitas Bumigora',
            ];

            // Add all fakultas and prodi
            foreach ($this->getAllFakultas() as $fakultas) {
                $options[] = [
                    'value' => "fakultas:{$fakultas->id}",
                    'label' => $fakultas->nama,
                ];

                foreach ($fakultas->prodi as $prodi) {
                    $options[] = [
                        'value' => "prodi:{$prodi->id}",
                        'label' => "-- {$prodi->full_name}",
                    ];
                }
            }
        } elseif ($user->isUniversitas()) {
            $options[] = [
                'value' => 'universitas:',
                'label' => 'Universitas Bumigora',
            ];
        } elseif ($user->isFakultas()) {
            $fakultas = Fakultas::find($user->unit_id);
            if ($fakultas) {
                $options[] = [
                    'value' => "fakultas:{$fakultas->id}",
                    'label' => $fakultas->nama,
                ];

                foreach ($fakultas->prodi as $prodi) {
                    $options[] = [
                        'value' => "prodi:{$prodi->id}",
                        'label' => "-- {$prodi->full_name}",
                    ];
                }
            }
        } elseif ($user->isProdi()) {
            $prodi = Prodi::find($user->unit_id);
            if ($prodi) {
                $options[] = [
                    'value' => "prodi:{$prodi->id}",
                    'label' => $prodi->full_name,
                ];
            }
        }

        return $options;
    }

    /**
     * Parse unit select value
     */
    public function parseUnitSelectValue(string $value): array
    {
        $parts = explode(':', $value, 2);
        $type = UnitType::from($parts[0]);
        $id = $parts[1] !== '' ? (int) $parts[1] : null;

        return [
            'type' => $type,
            'id' => $id,
        ];
    }

    /**
     * Check if a unit is published
     */
    public function isUnitPublished(UnitType $unitType, ?int $unitId = null): bool
    {
        if ($unitType === UnitType::UNIVERSITAS) {
            return true; // Universitas is always published
        }

        $unit = $this->getUnit($unitType, $unitId);
        
        return $unit && $unit->is_published;
    }

    /**
     * Get coming soon message for unpublished unit
     */
    public function getComingSoonMessage(UnitType $unitType, ?int $unitId = null): ?string
    {
        $unit = $this->getUnit($unitType, $unitId);
        
        return $unit?->coming_soon_message ?? 'Website sedang dalam pengembangan. Silakan kunjungi kembali nanti.';
    }

    /**
     * Clear all unit caches
     */
    public function clearCache(): void
    {
        Cache::forget('all_fakultas');
        Cache::forget('all_prodi');
        
        // Clear fakultas-specific prodi caches
        foreach (Fakultas::all() as $fakultas) {
            Cache::forget("prodi_fakultas_{$fakultas->id}");
        }
    }
}
