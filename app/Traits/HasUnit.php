<?php

namespace App\Traits;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Builder;

trait HasUnit
{
    /**
     * Boot the trait
     */
    protected static function bootHasUnit(): void
    {
        // Automatically set unit from authenticated user when creating
        static::creating(function ($model) {
            if (auth()->check() && !$model->unit_type) {
                $user = auth()->user();
                $model->unit_type = $user->unit_type;
                $model->unit_id = $user->unit_id;
            }
        });
    }

    /**
     * Get the unit relationship based on unit_type
     */
    public function unit()
    {
        if (!$this->unit_type || !$this->unit_id) {
            return null;
        }

        return match ($this->unit_type) {
            UnitType::FAKULTAS => $this->belongsTo(Fakultas::class, 'unit_id'),
            UnitType::PRODI => $this->belongsTo(Prodi::class, 'unit_id'),
            default => null,
        };
    }

    /**
     * Get the unit instance
     */
    public function getUnitInstanceAttribute()
    {
        if (!$this->unit_type || !$this->unit_id) {
            return null;
        }

        return match ($this->unit_type) {
            UnitType::UNIVERSITAS => null, // Universitas level
            UnitType::FAKULTAS => Fakultas::find($this->unit_id),
            UnitType::PRODI => Prodi::find($this->unit_id),
        };
    }

    /**
     * Get the unit name
     */
    public function getUnitNameAttribute(): string
    {
        if ($this->unit_type === UnitType::UNIVERSITAS || !$this->unit_id) {
            return 'Universitas';
        }

        return $this->unit_instance?->nama ?? 'Unknown';
    }

    /**
     * Scope for specific unit type and id
     */
    public function scopeForUnit(Builder $query, UnitType $unitType, ?int $unitId = null): Builder
    {
        return $query->where('unit_type', $unitType)
            ->where('unit_id', $unitId);
    }

    /**
     * Scope for universitas level
     */
    public function scopeForUniversitas(Builder $query): Builder
    {
        return $query->where('unit_type', UnitType::UNIVERSITAS)
            ->whereNull('unit_id');
    }

    /**
     * Scope for fakultas level
     */
    public function scopeForFakultas(Builder $query, int $fakultasId): Builder
    {
        return $query->where('unit_type', UnitType::FAKULTAS)
            ->where('unit_id', $fakultasId);
    }

    /**
     * Scope for prodi level
     */
    public function scopeForProdi(Builder $query, int $prodiId): Builder
    {
        return $query->where('unit_type', UnitType::PRODI)
            ->where('unit_id', $prodiId);
    }

    /**
     * Scope for accessible by user
     * Based on user's role and unit, filter what they can access
     */
    public function scopeAccessibleBy(Builder $query, $user): Builder
    {
        // Super admin can access everything
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Admin universitas can access universitas-level content
        if ($user->isUniversitas()) {
            return $query->forUniversitas();
        }

        // Admin fakultas can access their fakultas content
        if ($user->isFakultas()) {
            return $query->where(function ($q) use ($user) {
                $q->forFakultas($user->unit_id)
                  ->orWhere(function ($q2) use ($user) {
                      // Also can access prodi under their fakultas
                      $prodiIds = Prodi::where('fakultas_id', $user->unit_id)->pluck('id');
                      $q2->where('unit_type', UnitType::PRODI)
                         ->whereIn('unit_id', $prodiIds);
                  });
            });
        }

        // Admin prodi can only access their prodi content
        if ($user->isProdi()) {
            return $query->forProdi($user->unit_id);
        }

        // Default: return nothing
        return $query->whereRaw('1 = 0');
    }

    /**
     * Check if this resource belongs to a specific unit
     */
    public function belongsToUnit(UnitType $unitType, ?int $unitId = null): bool
    {
        return $this->unit_type === $unitType && $this->unit_id === $unitId;
    }

    /**
     * Scope for cascading content UP (from current unit to parent units)
     * Used for: Pengumuman, Unduhan - shows content from current unit + parents
     * 
     * Prodi: shows prodi's + fakultas's + universitas's content
     * Fakultas: shows fakultas's + universitas's content
     * Universitas: shows universitas's content only
     */
    public function scopeForUnitCascadeUp(Builder $query, UnitType $unitType, ?int $unitId = null): Builder
    {
        return $query->where(function ($q) use ($unitType, $unitId) {
            // Always include universitas level
            $q->where(function ($q2) {
                $q2->where('unit_type', UnitType::UNIVERSITAS)
                   ->whereNull('unit_id');
            });

            if ($unitType === UnitType::FAKULTAS && $unitId) {
                // Include fakultas content
                $q->orWhere(function ($q2) use ($unitId) {
                    $q2->where('unit_type', UnitType::FAKULTAS)
                       ->where('unit_id', $unitId);
                });
            }

            if ($unitType === UnitType::PRODI && $unitId) {
                // Include prodi content
                $q->orWhere(function ($q2) use ($unitId) {
                    $q2->where('unit_type', UnitType::PRODI)
                       ->where('unit_id', $unitId);
                });

                // Include parent fakultas content
                $prodi = Prodi::find($unitId);
                if ($prodi && $prodi->fakultas_id) {
                    $q->orWhere(function ($q2) use ($prodi) {
                        $q2->where('unit_type', UnitType::FAKULTAS)
                           ->where('unit_id', $prodi->fakultas_id);
                    });
                }
            }
        });
    }

    /**
     * Scope for cascading content DOWN (from current unit to child units)
     * Used for: Prestasi, Galeri - shows content from current unit + children
     * 
     * Universitas: shows universitas's + all fakultas + all prodi content
     * Fakultas: shows fakultas's + prodi under fakultas content
     * Prodi: shows prodi's content only
     */
    public function scopeForUnitCascadeDown(Builder $query, UnitType $unitType, ?int $unitId = null): Builder
    {
        return $query->where(function ($q) use ($unitType, $unitId) {
            if ($unitType === UnitType::UNIVERSITAS) {
                // Show all content (universitas + all fakultas + all prodi)
                $q->where(function ($q2) {
                    $q2->where('unit_type', UnitType::UNIVERSITAS)
                       ->whereNull('unit_id');
                })
                ->orWhere('unit_type', UnitType::FAKULTAS)
                ->orWhere('unit_type', UnitType::PRODI);
            } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
                // Show fakultas's content + prodi under this fakultas
                $q->where(function ($q2) use ($unitId) {
                    $q2->where('unit_type', UnitType::FAKULTAS)
                       ->where('unit_id', $unitId);
                });

                // Include prodi content under this fakultas
                $prodiIds = Prodi::where('fakultas_id', $unitId)->pluck('id');
                if ($prodiIds->isNotEmpty()) {
                    $q->orWhere(function ($q2) use ($prodiIds) {
                        $q2->where('unit_type', UnitType::PRODI)
                           ->whereIn('unit_id', $prodiIds);
                    });
                }
            } elseif ($unitType === UnitType::PRODI && $unitId) {
                // Show prodi's content only
                $q->where('unit_type', UnitType::PRODI)
                  ->where('unit_id', $unitId);
            }
        });
    }

    /**
     * Scope for cascading content BOTH directions (up and down)
     * Used for: Berita - shows content from ALL units with labels
     * 
     * Universitas: shows universitas's + all fakultas + all prodi content
     * Fakultas: shows fakultas's + universitas's + prodi under fakultas content
     * Prodi: shows prodi's + fakultas's + universitas's content
     */
    public function scopeForUnitCascadeBoth(Builder $query, UnitType $unitType, ?int $unitId = null): Builder
    {
        return $query->where(function ($q) use ($unitType, $unitId) {
            // Always include universitas level
            $q->where(function ($q2) {
                $q2->where('unit_type', UnitType::UNIVERSITAS)
                   ->whereNull('unit_id');
            });

            if ($unitType === UnitType::UNIVERSITAS) {
                // Universitas sees everything (cascade down)
                $q->orWhere('unit_type', UnitType::FAKULTAS)
                  ->orWhere('unit_type', UnitType::PRODI);
            } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
                // Fakultas sees: own content + prodi under it (cascade down)
                $q->orWhere(function ($q2) use ($unitId) {
                    $q2->where('unit_type', UnitType::FAKULTAS)
                       ->where('unit_id', $unitId);
                });

                // Include prodi content under this fakultas
                $prodiIds = Prodi::where('fakultas_id', $unitId)->pluck('id');
                if ($prodiIds->isNotEmpty()) {
                    $q->orWhere(function ($q2) use ($prodiIds) {
                        $q2->where('unit_type', UnitType::PRODI)
                           ->whereIn('unit_id', $prodiIds);
                    });
                }
            } elseif ($unitType === UnitType::PRODI && $unitId) {
                // Prodi sees: own content + parent fakultas (cascade up)
                $q->orWhere(function ($q2) use ($unitId) {
                    $q2->where('unit_type', UnitType::PRODI)
                       ->where('unit_id', $unitId);
                });

                // Include parent fakultas content
                $prodi = Prodi::find($unitId);
                if ($prodi && $prodi->fakultas_id) {
                    $q->orWhere(function ($q2) use ($prodi) {
                        $q2->where('unit_type', UnitType::FAKULTAS)
                           ->where('unit_id', $prodi->fakultas_id);
                    });
                }
            }
        });
    }

    /**
     * Get the unit source label for display
     * Returns label like "Universitas", "Fakultas X", "Prodi Y"
     */
    public function getUnitSourceLabelAttribute(): string
    {
        return match ($this->unit_type) {
            UnitType::UNIVERSITAS => 'Universitas',
            UnitType::FAKULTAS => $this->unit_instance?->nama ?? 'Fakultas',
            UnitType::PRODI => $this->unit_instance?->nama ?? 'Program Studi',
            default => 'Unknown',
        };
    }

    /**
     * Check if this resource is accessible by a user
     */
    public function isAccessibleBy($user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isUniversitas()) {
            return $this->unit_type === UnitType::UNIVERSITAS;
        }

        if ($user->isFakultas()) {
            // Can access fakultas content
            if ($this->unit_type === UnitType::FAKULTAS && $this->unit_id === $user->unit_id) {
                return true;
            }
            // Can access prodi content under their fakultas
            if ($this->unit_type === UnitType::PRODI) {
                $prodi = Prodi::find($this->unit_id);
                return $prodi && $prodi->fakultas_id === $user->unit_id;
            }
            return false;
        }

        if ($user->isProdi()) {
            return $this->unit_type === UnitType::PRODI && $this->unit_id === $user->unit_id;
        }

        return false;
    }
}
