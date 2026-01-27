<?php

namespace Database\Seeders;

use App\Enums\MenuType;
use App\Enums\UnitType;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * NOTE: Menu "Akademik" dengan daftar Fakultas dan Prodi tidak di-hardcode di sini.
     * Menu tersebut akan di-generate secara DINAMIS dari tabel fakultas dan prodi
     * melalui MenuService::getAkademikMenuStructure()
     * 
     * Logika:
     * - Web Universitas: Dropdown Fakultas -> Dropdown Prodi (multi-level)
     * - Web Fakultas: Dropdown Prodi saja (single level)
     * - Web Prodi: Tidak perlu menu fakultas/prodi
     */
    public function run(): void
    {
        $this->command->info('Seeding menus...');

        // Clear existing menus
        Menu::truncate();

        // Get pages for linking
        $visiMisiPage = Page::where('slug', 'visi-misi')->first();
        $sejarahPage = Page::where('slug', 'sejarah')->first();
        $strukturPage = Page::where('slug', 'struktur-organisasi')->first();

        // Main menu items for Universitas
        $menus = [
            // 1. Beranda
            [
                'title' => 'Beranda',
                'type' => MenuType::LINK,
                'url' => '/',
                'order' => 1,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
            ],
            
            // 2. Profil (Dropdown)
            [
                'title' => 'Profil',
                'type' => MenuType::DROPDOWN,
                'order' => 2,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
                'children' => [
                    [
                        'title' => 'Visi & Misi',
                        'type' => MenuType::PAGE,
                        'page_id' => $visiMisiPage?->id,
                        'order' => 1,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Sejarah',
                        'type' => MenuType::PAGE,
                        'page_id' => $sejarahPage?->id,
                        'order' => 2,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Struktur Organisasi',
                        'type' => MenuType::PAGE,
                        'page_id' => $strukturPage?->id,
                        'order' => 3,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Pimpinan',
                        'type' => MenuType::LINK,
                        'url' => '/pimpinan',
                        'order' => 4,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Dosen & Staf',
                        'type' => MenuType::LINK,
                        'url' => '/dosen',
                        'order' => 5,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                ],
            ],
            
            // 3. Akademik - DINAMIS dari tabel fakultas & prodi
            // Children akan di-generate oleh MenuService::getAkademikMenuStructure()
            // Ini hanya parent menu-nya saja
            [
                'title' => 'Akademik',
                'type' => MenuType::DROPDOWN,
                'order' => 3,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
                // Tidak ada children hardcoded - akan di-generate dinamis
                'children' => [
                    // Static items yang bukan fakultas/prodi
                    [
                        'title' => 'Kurikulum',
                        'type' => MenuType::LINK,
                        'url' => '/unduhan?kategori=kurikulum',
                        'order' => 98,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Kalender Akademik',
                        'type' => MenuType::LINK,
                        'url' => '/unduhan?kategori=kalender-akademik',
                        'order' => 99,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                ],
            ],
            
            // 4. Berita
            [
                'title' => 'Berita',
                'type' => MenuType::LINK,
                'url' => '/berita',
                'order' => 4,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
            ],
            
            // 5. Agenda
            [
                'title' => 'Agenda',
                'type' => MenuType::LINK,
                'url' => '/agenda',
                'order' => 5,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
            ],
            
            // 6. Informasi (Dropdown)
            [
                'title' => 'Informasi',
                'type' => MenuType::DROPDOWN,
                'order' => 6,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
                'children' => [
                    [
                        'title' => 'Prestasi',
                        'type' => MenuType::LINK,
                        'url' => '/prestasi',
                        'order' => 1,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Galeri',
                        'type' => MenuType::LINK,
                        'url' => '/galeri',
                        'order' => 2,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Unduhan',
                        'type' => MenuType::LINK,
                        'url' => '/unduhan',
                        'order' => 3,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                    [
                        'title' => 'Pengumuman',
                        'type' => MenuType::LINK,
                        'url' => '/pengumuman',
                        'order' => 4,
                        'is_active' => true,
                        'unit_type' => UnitType::UNIVERSITAS,
                    ],
                ],
            ],
            
            // 7. Kontak
            [
                'title' => 'Kontak',
                'type' => MenuType::LINK,
                'url' => '/kontak',
                'order' => 7,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
            ],
            
            // 8. Login
            [
                'title' => 'Login',
                'type' => MenuType::LOGIN,
                'order' => 8,
                'is_active' => true,
                'unit_type' => UnitType::UNIVERSITAS,
            ],
        ];

        foreach ($menus as $menuData) {
            $this->createMenuWithChildren($menuData);
        }

        $this->command->info('Menus seeded successfully!');
        $this->command->info('NOTE: Menu Akademik (Fakultas/Prodi) akan di-generate dinamis dari database.');
    }

    /**
     * Recursively create menu with children
     */
    private function createMenuWithChildren(array $menuData, ?int $parentId = null): void
    {
        $children = $menuData['children'] ?? [];
        unset($menuData['children']);
        
        if ($parentId) {
            $menuData['parent_id'] = $parentId;
        }
        
        $menu = Menu::create($menuData);
        
        foreach ($children as $childData) {
            $this->createMenuWithChildren($childData, $menu->id);
        }
    }
}
