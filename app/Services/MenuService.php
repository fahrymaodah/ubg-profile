<?php

namespace App\Services;

use App\Enums\MenuType;
use App\Enums\UnitType;
use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 3600;

    /**
     * Get menu tree for a unit
     * Ensures ordering: Regular menus → Kontak → Button(s)
     */
    public function getMenuTree(UnitType $unitType, ?int $unitId = null): Collection
    {
        $cacheKey = $this->getCacheKey($unitType, $unitId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId) {
            $dbMenus = Menu::forUnit($unitType, $unitId)
                ->root()
                ->active()
                ->ordered()
                ->with(['children' => function ($query) {
                    $query->active()->ordered()->with('descendants');
                }])
                ->get();

            return $this->ensureMenuOrdering($dbMenus);
        });
    }

    /**
     * Ensure proper ordering: Regular menus → Kontak → Button(s)
     */
    protected function ensureMenuOrdering(Collection $menus): Collection
    {
        $regularMenus = collect();
        $kontakMenu = null;
        $buttonMenus = collect();

        foreach ($menus as $menu) {
            $titleLower = strtolower($menu->title);
            
            if ($titleLower === 'kontak') {
                $kontakMenu = $menu;
            } elseif ($menu->type === MenuType::BUTTON) {
                $buttonMenus->push($menu);
            } else {
                $regularMenus->push($menu);
            }
        }

        // Build final menu: Regular → Kontak → Buttons
        $result = $regularMenus;
        
        if ($kontakMenu) {
            $result->push($kontakMenu);
        }

        foreach ($buttonMenus as $btn) {
            $result->push($btn);
        }

        return $result->values();
    }

    /**
     * Get flat list of menus with depth info (for admin)
     */
    public function getFlatMenuList(UnitType $unitType, ?int $unitId = null): Collection
    {
        $tree = Menu::forUnit($unitType, $unitId)
            ->root()
            ->ordered()
            ->with('descendants')
            ->get();

        return $this->flattenTree($tree);
    }

    /**
     * Flatten tree into list with depth info
     */
    protected function flattenTree(Collection $menus, int $depth = 0): Collection
    {
        $result = collect();

        foreach ($menus as $menu) {
            $menu->depth_level = $depth;
            $result->push($menu);

            if ($menu->children->isNotEmpty()) {
                $result = $result->merge($this->flattenTree($menu->children, $depth + 1));
            }
        }

        return $result;
    }

    /**
     * Reorder menus
     */
    public function reorder(array $menuIds): void
    {
        foreach ($menuIds as $order => $menuId) {
            Menu::where('id', $menuId)->update(['order' => $order]);
        }

        $this->clearAllCache();
    }

    /**
     * Move menu to new parent
     */
    public function moveToParent(Menu $menu, ?int $parentId): void
    {
        // Prevent circular reference
        if ($parentId && $this->wouldCreateCircularReference($menu, $parentId)) {
            throw new \InvalidArgumentException('Cannot move menu to its own descendant');
        }

        $menu->update(['parent_id' => $parentId]);
        $this->clearCache($menu->unit_type, $menu->unit_id);
    }

    /**
     * Check if moving would create circular reference
     */
    protected function wouldCreateCircularReference(Menu $menu, int $parentId): bool
    {
        if ($menu->id === $parentId) {
            return true;
        }

        $parent = Menu::find($parentId);
        while ($parent) {
            if ($parent->id === $menu->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Duplicate menu structure from one unit to another
     */
    public function duplicateMenuStructure(
        UnitType $sourceType,
        ?int $sourceId,
        UnitType $targetType,
        ?int $targetId
    ): void {
        $sourceMenus = Menu::forUnit($sourceType, $sourceId)
            ->root()
            ->ordered()
            ->with('descendants')
            ->get();

        $this->duplicateMenus($sourceMenus, $targetType, $targetId, null);
        $this->clearCache($targetType, $targetId);
    }

    /**
     * Recursively duplicate menus
     */
    protected function duplicateMenus(
        Collection $menus,
        UnitType $targetType,
        ?int $targetId,
        ?int $parentId
    ): void {
        foreach ($menus as $menu) {
            $newMenu = Menu::create([
                'parent_id' => $parentId,
                'unit_type' => $targetType,
                'unit_id' => $targetId,
                'title' => $menu->title,
                'type' => $menu->type,
                'url' => $menu->url,
                'target' => $menu->target,
                'icon' => $menu->icon,
                'css_class' => $menu->css_class,
                'order' => $menu->order,
                'is_active' => $menu->is_active,
            ]);

            if ($menu->children->isNotEmpty()) {
                $this->duplicateMenus($menu->children, $targetType, $targetId, $newMenu->id);
            }
        }
    }

    /**
     * List of mandatory menu titles (lowercase) - used for is_deletable flag
     */
    protected const MANDATORY_MENU_TITLES = ['beranda', 'profil', 'akademik', 'kontak'];

    /**
     * Create default menu structure for a unit
     */
    public function createDefaultMenus(UnitType $unitType, ?int $unitId = null): void
    {
        $defaultMenus = [
            ['title' => 'Beranda', 'url' => '/', 'order' => 1],
            ['title' => 'Profil', 'url' => null, 'order' => 2, 'children' => [
                ['title' => 'Visi & Misi', 'url' => '/profil/visi-misi', 'order' => 1],
                ['title' => 'Sejarah', 'url' => '/profil/sejarah', 'order' => 2],
                ['title' => 'Struktur Organisasi', 'url' => '/profil/struktur', 'order' => 3],
                ['title' => 'Dosen', 'url' => '/dosen', 'order' => 4],
            ]],
            ['title' => 'Akademik', 'url' => null, 'order' => 3, 'children' => [
                ['title' => 'Program Studi', 'url' => '/akademik/prodi', 'order' => 1],
                ['title' => 'Kurikulum', 'url' => '/akademik/kurikulum', 'order' => 2],
                ['title' => 'Kalender Akademik', 'url' => '/akademik/kalender', 'order' => 3],
            ]],
            ['title' => 'Berita', 'url' => '/berita', 'order' => 4],
            ['title' => 'Kontak', 'url' => '/kontak', 'order' => 5],
        ];

        $this->createMenusFromArray($defaultMenus, $unitType, $unitId, null);
        $this->clearCache($unitType, $unitId);
    }

    /**
     * Create menus from array structure
     */
    protected function createMenusFromArray(
        array $menus,
        UnitType $unitType,
        ?int $unitId,
        ?int $parentId
    ): void {
        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            // Set is_deletable based on whether it's a mandatory menu (root level only)
            $titleLower = strtolower($menuData['title']);
            $isDeletable = $parentId !== null || !in_array($titleLower, self::MANDATORY_MENU_TITLES);

            $menu = Menu::create(array_merge($menuData, [
                'parent_id' => $parentId,
                'unit_type' => $unitType,
                'unit_id' => $unitId,
                'type' => $menuData['url'] ? 'link' : 'dropdown',
                'is_active' => true,
                'is_deletable' => $isDeletable,
            ]));

            if (!empty($children)) {
                $this->createMenusFromArray($children, $unitType, $unitId, $menu->id);
            }
        }
    }

    /**
     * Get cache key for menu
     */
    protected function getCacheKey(UnitType $unitType, ?int $unitId): string
    {
        return "menus.{$unitType->value}.{$unitId}";
    }

    /**
     * Clear cache for specific unit
     */
    public function clearCache(UnitType $unitType, ?int $unitId = null): void
    {
        Cache::forget($this->getCacheKey($unitType, $unitId));
    }

    /**
     * Clear all menu caches
     */
    public function clearAllCache(): void
    {
        // This is a simplified approach - in production you might want to use cache tags
        Cache::flush();
    }

    /**
     * Get dynamic akademik menu based on unit type
     * 
     * @param UnitType $unitType Current website unit type
     * @param int|null $unitId Current unit ID (fakultas_id or prodi_id)
     * @return array Menu structure for akademik
     */
    public function getAkademikMenuStructure(UnitType $unitType, ?int $unitId = null): array
    {
        return match ($unitType) {
            UnitType::UNIVERSITAS => $this->getUniversitasAkademikMenu(),
            UnitType::FAKULTAS => $this->getFakultasAkademikMenu($unitId),
            UnitType::PRODI => [], // Prodi tidak perlu menu fakultas/prodi lain
        };
    }

    /**
     * Get akademik menu for universitas level
     * Shows all fakultas with their prodi as children
     */
    protected function getUniversitasAkademikMenu(): array
    {
        $fakultasList = \App\Models\Fakultas::where('is_active', true)
            ->orderBy('order')
            ->with(['prodi' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->get();

        $children = [];

        foreach ($fakultasList as $fakultas) {
            $prodiChildren = [];
            
            foreach ($fakultas->prodi as $prodi) {
                $prodiChildren[] = [
                    'title' => ($prodi->jenjang ? $prodi->jenjang->value . ' ' : '') . $prodi->nama,
                    'url' => $prodi->website ?: "https://{$prodi->subdomain}." . config('app.domain', 'ubg.ac.id'),
                    'target' => '_blank',
                ];
            }

            $children[] = [
                'title' => $fakultas->nama,
                'url' => $fakultas->website ?: "https://{$fakultas->subdomain}." . config('app.domain', 'ubg.ac.id'),
                'target' => '_blank',
                'has_children' => count($prodiChildren) > 0,
                'children' => $prodiChildren,
            ];
        }

        return $children;
    }

    /**
     * Get akademik menu for fakultas level
     * Shows prodi list under this fakultas only
     */
    protected function getFakultasAkademikMenu(?int $fakultasId): array
    {
        if (!$fakultasId) {
            return [];
        }

        $prodiList = \App\Models\Prodi::where('fakultas_id', $fakultasId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $children = [];

        foreach ($prodiList as $prodi) {
            $children[] = [
                'title' => ($prodi->jenjang ? $prodi->jenjang->value . ' ' : '') . $prodi->nama,
                'url' => $prodi->website ?: "https://{$prodi->subdomain}." . config('app.domain', 'ubg.ac.id'),
                'target' => '_blank',
            ];
        }

        return $children;
    }
}
