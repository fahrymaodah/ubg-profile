<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unit_type',
        'unit_id',
        'is_active',
    ];

    protected $appends = [
        'fakultas_id',
        'prodi_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'unit_type' => UnitType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    /**
     * Get the unit (Fakultas or Prodi) that this user belongs to.
     */
    public function unit(): BelongsTo
    {
        return match ($this->unit_type) {
            UnitType::FAKULTAS => $this->belongsTo(Fakultas::class, 'unit_id'),
            UnitType::PRODI => $this->belongsTo(Prodi::class, 'unit_id'),
            default => $this->belongsTo(Fakultas::class, 'unit_id')->whereNull('unit_id'),
        };
    }

    /**
     * Get fakultas for this user
     */
    public function fakultas(): ?Fakultas
    {
        return match ($this->unit_type) {
            UnitType::FAKULTAS => Fakultas::find($this->unit_id),
            UnitType::PRODI => Prodi::find($this->unit_id)?->fakultas,
            default => null,
        };
    }

    /**
     * Get prodi for this user
     */
    public function prodi(): ?Prodi
    {
        return $this->unit_type === UnitType::PRODI
            ? Prodi::find($this->unit_id)
            : null;
    }

    /**
     * Get articles created by this user
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPERADMIN;
    }

    /**
     * Check if user is universitas admin
     */
    public function isUniversitas(): bool
    {
        return $this->role === UserRole::UNIVERSITAS;
    }

    /**
     * Check if user is fakultas admin
     */
    public function isFakultas(): bool
    {
        return $this->role === UserRole::FAKULTAS;
    }

    /**
     * Check if user is prodi admin
     */
    public function isProdi(): bool
    {
        return $this->role === UserRole::PRODI;
    }

    /**
     * Check if user can manage a specific unit
     */
    public function canManageUnit(?UnitType $unitType, ?int $unitId = null): bool
    {
        // Superadmin can manage everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Universitas admin can manage all fakultas and prodi
        if ($this->isUniversitas()) {
            return true;
        }
        
        // If target has no unit type, only superadmin/universitas can manage (already handled above)
        if ($unitType === null) {
            return false;
        }

        // Fakultas admin can manage their fakultas and its prodi
        if ($this->isFakultas()) {
            if ($unitType === UnitType::FAKULTAS && $unitId === $this->unit_id) {
                return true;
            }
            if ($unitType === UnitType::PRODI) {
                $prodi = Prodi::find($unitId);
                return $prodi && $prodi->fakultas_id === $this->unit_id;
            }
        }

        // Prodi admin can only manage their own prodi
        if ($this->isProdi()) {
            return $unitType === UnitType::PRODI && $unitId === $this->unit_id;
        }

        return false;
    }

    /**
     * Get unit name for display
     */
    public function getUnitNameAttribute(): string
    {
        return match ($this->unit_type) {
            UnitType::FAKULTAS => Fakultas::find($this->unit_id)?->nama ?? '-',
            UnitType::PRODI => Prodi::find($this->unit_id)?->nama ?? '-',
            default => 'Universitas',
        };
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Accessor for fakultas_id (virtual field mapped from unit_id)
     */
    public function getFakultasIdAttribute(): ?string
    {
        if ($this->role === UserRole::FAKULTAS && $this->unit_type === UnitType::FAKULTAS) {
            return (string) $this->unit_id;
        }
        return null;
    }

    /**
     * Accessor for prodi_id (virtual field mapped from unit_id)
     */
    public function getProdiIdAttribute(): ?string
    {
        if ($this->role === UserRole::PRODI && $this->unit_type === UnitType::PRODI) {
            return (string) $this->unit_id;
        }
        return null;
    }
}
