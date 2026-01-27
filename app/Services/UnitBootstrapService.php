<?php

namespace App\Services;

use App\Enums\MenuType;
use App\Enums\UnitType;
use App\Models\ArticleCategory;
use App\Models\Fakultas;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Prodi;
use App\Models\Setting;

/**
 * Service untuk membuat data default saat unit baru (Fakultas/Prodi) dibuat
 */
class UnitBootstrapService
{
    /**
     * List of mandatory menu titles that cannot be deleted (lowercase)
     */
    protected const MANDATORY_MENUS = ['beranda', 'profil', 'akademik', 'kontak'];

    /**
     * Bootstrap semua data default untuk unit baru
     */
    public function bootstrapUnit(UnitType $unitType, int $unitId): void
    {
        $unit = $this->getUnitInfo($unitType, $unitId);
        
        if (!$unit) {
            return;
        }

        $this->createDefaultSettings($unitType, $unitId, $unit);
        $this->createDefaultMenus($unitType, $unitId);
        $this->createDefaultArticleCategories($unitType, $unitId);
        $this->createDefaultPages($unitType, $unitId, $unit);
    }

    /**
     * Get unit information
     */
    protected function getUnitInfo(UnitType $unitType, int $unitId): ?array
    {
        if ($unitType === UnitType::FAKULTAS) {
            $fakultas = Fakultas::find($unitId);
            if (!$fakultas) return null;
            
            return [
                'name' => $fakultas->nama,
                'email' => $fakultas->email ?? 'info@ubg.ac.id',
                'visi' => $fakultas->visi,
                'misi' => $fakultas->misi,
            ];
        }
        
        if ($unitType === UnitType::PRODI) {
            $prodi = Prodi::with('fakultas')->find($unitId);
            if (!$prodi) return null;
            
            return [
                'name' => $prodi->nama,
                'email' => $prodi->email ?? $prodi->fakultas?->email ?? 'info@ubg.ac.id',
                'visi' => $prodi->visi,
                'misi' => $prodi->misi,
            ];
        }

        return null;
    }

    /**
     * Create default settings for unit
     */
    protected function createDefaultSettings(UnitType $unitType, int $unitId, array $unit): void
    {
        // Check if settings already exist
        $exists = Setting::where('unit_type', $unitType)
            ->where('unit_id', $unitId)
            ->exists();
            
        if ($exists) {
            return;
        }

        $siteName = $unit['name'] . ' - Universitas Bumigora';

        $settings = [
            // General
            ['key' => 'site_name', 'value' => $siteName, 'type' => 'text'],
            ['key' => 'site_description', 'value' => $unit['name'] . ' - Kampus Unggulan di Nusa Tenggara Barat', 'type' => 'textarea'],
            ['key' => 'site_keywords', 'value' => strtolower($unit['name']) . ', universitas bumigora, ubg, kampus ntb', 'type' => 'text'],

            // Contact
            ['key' => 'address', 'value' => 'Jl. Ismail Marzuki No. 22, Cilinaya, Kec. Cakranegara, Kota Mataram, Nusa Tenggara Barat 83239', 'type' => 'textarea'],
            ['key' => 'phone', 'value' => '(0370) 633837', 'type' => 'text'],
            ['key' => 'email', 'value' => $unit['email'], 'type' => 'email'],
            ['key' => 'whatsapp', 'value' => '', 'type' => 'text'],

            // Social Media
            ['key' => 'facebook', 'value' => '', 'type' => 'url'],
            ['key' => 'instagram', 'value' => '', 'type' => 'url'],
            ['key' => 'youtube', 'value' => '', 'type' => 'url'],
            ['key' => 'twitter', 'value' => '', 'type' => 'url'],
            ['key' => 'linkedin', 'value' => '', 'type' => 'url'],
            ['key' => 'tiktok', 'value' => '', 'type' => 'url'],

            // Theme
            ['key' => 'theme_color_primary', 'value' => '#0b5ed7', 'type' => 'color'],
            ['key' => 'theme_color_secondary', 'value' => '#64748b', 'type' => 'color'],
            ['key' => 'theme_color_accent', 'value' => '#f59e0b', 'type' => 'color'],

            // Features
            ['key' => 'show_announcement_bar', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'show_floating_whatsapp', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'show_back_to_top', 'value' => 'true', 'type' => 'boolean'],

            // Content
            ['key' => 'articles_per_page', 'value' => '12', 'type' => 'integer'],
            ['key' => 'featured_articles_count', 'value' => '4', 'type' => 'integer'],
            ['key' => 'sidebar_articles_count', 'value' => '5', 'type' => 'integer'],

            // Footer
            ['key' => 'footer_text_left', 'value' => '© ' . date('Y') . ' ' . $unit['name'] . '. All rights reserved.', 'type' => 'text'],
            ['key' => 'footer_text_right', 'value' => 'Developed with ❤️ by PUSTIK UBG', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::create([
                'unit_type' => $unitType,
                'unit_id' => $unitId,
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'],
            ]);
        }
    }

    /**
     * Create default menus for unit
     */
    protected function createDefaultMenus(UnitType $unitType, int $unitId): void
    {
        // Check if menus already exist
        $exists = Menu::where('unit_type', $unitType)
            ->where('unit_id', $unitId)
            ->exists();
            
        if ($exists) {
            return;
        }

        $isProdi = $unitType === UnitType::PRODI;

        $menus = [
            ['title' => 'Beranda', 'type' => MenuType::LINK, 'url' => '/', 'order' => 1],
            [
                'title' => 'Profil',
                'type' => MenuType::DROPDOWN,
                'order' => 2,
                'children' => [
                    ['title' => 'Visi & Misi', 'type' => MenuType::LINK, 'url' => '/profil/visi-misi', 'order' => 1],
                    ['title' => 'Sejarah', 'type' => MenuType::LINK, 'url' => '/profil/sejarah', 'order' => 2],
                    ['title' => 'Struktur Organisasi', 'type' => MenuType::LINK, 'url' => '/profil/struktur', 'order' => 3],
                    ['title' => 'Dosen', 'type' => MenuType::LINK, 'url' => '/dosen', 'order' => 4],
                ],
            ],
        ];

        // Add Akademik menu
        if (!$isProdi) {
            $menus[] = [
                'title' => 'Akademik',
                'type' => MenuType::DROPDOWN,
                'order' => 3,
                'children' => [
                    ['title' => 'Kurikulum', 'type' => MenuType::LINK, 'url' => '/unduhan?kategori=kurikulum', 'order' => 98],
                    ['title' => 'Kalender Akademik', 'type' => MenuType::LINK, 'url' => '/unduhan?kategori=kalender-akademik', 'order' => 99],
                ],
            ];
        } else {
            $menus[] = [
                'title' => 'Akademik',
                'type' => MenuType::DROPDOWN,
                'order' => 3,
                'children' => [
                    ['title' => 'Kurikulum', 'type' => MenuType::LINK, 'url' => '/halaman/kurikulum', 'order' => 1],
                    ['title' => 'Kalender Akademik', 'type' => MenuType::LINK, 'url' => '/unduhan?kategori=kalender-akademik', 'order' => 2],
                ],
            ];
        }

        $menus = array_merge($menus, [
            ['title' => 'Berita', 'type' => MenuType::LINK, 'url' => '/berita', 'order' => 4],
            ['title' => 'Agenda', 'type' => MenuType::LINK, 'url' => '/agenda', 'order' => 5],
            [
                'title' => 'Informasi',
                'type' => MenuType::DROPDOWN,
                'order' => 6,
                'children' => [
                    ['title' => 'Prestasi', 'type' => MenuType::LINK, 'url' => '/prestasi', 'order' => 1],
                    ['title' => 'Galeri', 'type' => MenuType::LINK, 'url' => '/galeri', 'order' => 2],
                    ['title' => 'Unduhan', 'type' => MenuType::LINK, 'url' => '/unduhan', 'order' => 3],
                    ['title' => 'Pengumuman', 'type' => MenuType::LINK, 'url' => '/pengumuman', 'order' => 4],
                ],
            ],
            ['title' => 'Kontak', 'type' => MenuType::LINK, 'url' => '/kontak', 'order' => 7],
        ]);

        foreach ($menus as $menuData) {
            $this->createMenuWithChildren($menuData, null, $unitType, $unitId);
        }
    }

    /**
     * Create menu with its children
     */
    protected function createMenuWithChildren(array $menuData, ?int $parentId, UnitType $unitType, int $unitId): void
    {
        $children = $menuData['children'] ?? [];
        unset($menuData['children']);

        $menuData['unit_type'] = $unitType;
        $menuData['unit_id'] = $unitId;
        $menuData['is_active'] = true;

        // Set is_deletable to false for mandatory menus (root level only)
        $titleLower = strtolower($menuData['title']);
        if (!$parentId && in_array($titleLower, self::MANDATORY_MENUS)) {
            $menuData['is_deletable'] = false;
        } else {
            $menuData['is_deletable'] = true;
        }

        if ($parentId) {
            $menuData['parent_id'] = $parentId;
        }

        $menu = Menu::create($menuData);

        foreach ($children as $childData) {
            $this->createMenuWithChildren($childData, $menu->id, $unitType, $unitId);
        }
    }

    /**
     * Create default article categories for unit
     */
    protected function createDefaultArticleCategories(UnitType $unitType, int $unitId): void
    {
        // Check if categories already exist
        $exists = ArticleCategory::where('unit_type', $unitType)
            ->where('unit_id', $unitId)
            ->exists();
            
        if ($exists) {
            return;
        }

        $categories = [
            ['name' => 'Berita Kampus', 'slug' => 'berita-kampus'],
            ['name' => 'Akademik', 'slug' => 'akademik'],
            ['name' => 'Kemahasiswaan', 'slug' => 'kemahasiswaan'],
            ['name' => 'Prestasi', 'slug' => 'prestasi'],
            ['name' => 'Kegiatan', 'slug' => 'kegiatan'],
        ];

        foreach ($categories as $index => $cat) {
            ArticleCategory::create([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => 'Kategori ' . $cat['name'],
                'unit_type' => $unitType,
                'unit_id' => $unitId,
                'is_active' => true,
                'order' => $index + 1,
            ]);
        }
    }

    /**
     * Create default pages for unit
     */
    protected function createDefaultPages(UnitType $unitType, int $unitId, array $unit): void
    {
        // Check if pages already exist
        $exists = Page::where('unit_type', $unitType)
            ->where('unit_id', $unitId)
            ->exists();
            
        if ($exists) {
            return;
        }

        $unitName = $unit['name'];

        $pages = [
            [
                'title' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'content' => '<h2>Visi</h2><p>' . ($unit['visi'] ?? 'Menjadi institusi pendidikan tinggi yang unggul dan berdaya saing global.') . '</p><h2>Misi</h2><p>' . ($unit['misi'] ?? '1. Menyelenggarakan pendidikan berkualitas<br>2. Melaksanakan penelitian inovatif<br>3. Mengabdi kepada masyarakat<br>4. Membangun kerjasama strategis') . '</p>',
            ],
            [
                'title' => 'Sejarah',
                'slug' => 'sejarah',
                'content' => '<h2>Sejarah ' . $unitName . '</h2><p>' . $unitName . ' didirikan sebagai bagian dari Universitas Bumigora dengan komitmen untuk memberikan pendidikan berkualitas kepada masyarakat Nusa Tenggara Barat.</p>',
            ],
            [
                'title' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'content' => '<h2>Struktur Organisasi ' . $unitName . '</h2><p>Struktur organisasi ' . $unitName . ' dirancang untuk mendukung penyelenggaraan Tri Dharma Perguruan Tinggi secara efektif dan efisien.</p>',
            ],
            [
                'title' => 'Kurikulum',
                'slug' => 'kurikulum',
                'content' => '<h2>Kurikulum ' . $unitName . '</h2><p>Kurikulum ' . $unitName . ' dirancang dengan mengacu pada standar nasional pendidikan tinggi dan kebutuhan industri.</p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::create([
                'title' => $page['title'],
                'slug' => $page['slug'],
                'content' => $page['content'],
                'unit_type' => $unitType,
                'unit_id' => $unitId,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Delete all data associated with a unit
     */
    public function cleanupUnit(UnitType $unitType, int $unitId): void
    {
        Setting::where('unit_type', $unitType)->where('unit_id', $unitId)->delete();
        Menu::where('unit_type', $unitType)->where('unit_id', $unitId)->delete();
        ArticleCategory::where('unit_type', $unitType)->where('unit_id', $unitId)->delete();
        Page::where('unit_type', $unitType)->where('unit_id', $unitId)->delete();
    }
}
