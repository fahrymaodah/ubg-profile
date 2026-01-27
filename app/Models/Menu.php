<?php

namespace App\Models;

use App\Enums\MenuType;
use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'parent_id',
        'unit_type',
        'unit_id',
        'title',
        'type',
        'url',
        'article_id',
        'page_id',
        'target',
        'icon',
        'css_class',
        'order',
        'is_active',
        'is_deletable',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'type' => MenuType::class,
            'is_active' => 'boolean',
            'is_deletable' => 'boolean',
        ];
    }

    /**
     * List of mandatory menu titles (lowercase) that cannot be deleted
     */
    public const MANDATORY_MENUS = ['beranda', 'profil', 'akademik', 'kontak'];

    /**
     * List of mandatory submenu titles under Profil (lowercase)
     */
    public const MANDATORY_PROFIL_SUBMENUS = ['visi & misi', 'sejarah', 'struktur organisasi', 'dosen'];

    /**
     * Check if this menu is mandatory (cannot be deleted)
     * - Root menus: Beranda, Profil, Akademik, Kontak
     * - Profil submenus: Visi & Misi, Sejarah, Struktur Organisasi, Dosen
     */
    public function isMandatory(): bool
    {
        // Explicitly check is_deletable = false
        if ($this->is_deletable === false) {
            return true;
        }

        $titleLower = strtolower($this->title);

        // Root level menus with mandatory titles
        if ($this->parent_id === null && in_array($titleLower, self::MANDATORY_MENUS)) {
            return true;
        }

        // Submenu under Profil with mandatory titles
        if ($this->parent_id !== null) {
            $parent = $this->parent;
            if ($parent && strtolower($parent->title) === 'profil' && in_array($titleLower, self::MANDATORY_PROFIL_SUBMENUS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the parent menu
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get child menus
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('order')
            ->where('is_active', true);
    }

    /**
     * Get all descendants (recursive)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the linked article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the linked page
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the computed URL based on menu type
     */
    public function getComputedUrlAttribute(): ?string
    {
        return match ($this->type) {
            MenuType::LINK => $this->url,
            MenuType::BUTTON => $this->url,
            MenuType::ARTICLE => $this->article ? route('article.show', $this->article->slug) : null,
            MenuType::PAGE => $this->page ? route('page.show', $this->page->slug) : null,
            MenuType::LOGIN => route('filament.admin.auth.login'),
            MenuType::DROPDOWN => null,
        };
    }

    /**
     * Check if this menu has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get depth level of this menu
     */
    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }

    /**
     * Scope for root menus (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get menu tree for a unit
     */
    public static function getTree(UnitType $unitType, ?int $unitId = null): \Illuminate\Support\Collection
    {
        return static::forUnit($unitType, $unitId)
            ->root()
            ->active()
            ->ordered()
            ->with('descendants')
            ->get();
    }
}
