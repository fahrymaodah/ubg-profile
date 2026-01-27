<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\Jenjang;
use App\Enums\MenuType;
use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use App\Enums\UnitType;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Dosen;
use App\Models\Download;
use App\Models\Event;
use App\Models\Fakultas;
use App\Models\Gallery;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    protected $fakultasIHHP;
    protected $fakultasPendidikan;
    protected $prodiSastraInggris;
    protected $prodiHukum;
    protected $prodiPariwisata;
    protected $prodiPTI;
    protected $prodiPKO;
    protected $author;

    public function run(): void
    {
        $this->command->info('🚀 Starting Production Seeder...');
        $this->command->info('');

        // Clear all tables
        $this->clearAllData();

        $this->author = User::first();

        // Create Fakultas dan Prodi
        $this->seedFakultasProdi();

        // Seed settings for all units
        $this->seedSettings();

        // Seed pages for all units
        $this->seedPages();

        // Seed menus for all units
        $this->seedMenus();

        // Seed article categories for all units
        $this->seedArticleCategories();

        // Seed articles for all units
        $this->seedArticles();

        // Seed events for all units
        $this->seedEvents();

        // Seed announcements for all units
        $this->seedAnnouncements();

        // Seed galleries for all units
        $this->seedGalleries();

        // Seed downloads for all units
        $this->seedDownloads();

        // Seed prestasi for all units
        $this->seedPrestasi();

        // Seed dosen for all prodi
        $this->seedDosen();

        // Seed sliders for all units
        $this->seedSliders();

        $this->command->info('');
        $this->command->info('✅ Production Seeder completed!');
    }

    private function clearAllData(): void
    {
        $this->command->info('🗑️  Clearing all existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'articles', 'article_categories', 'announcements', 'events', 
            'galleries', 'downloads', 'pages', 'menus', 'prestasi', 
            'dosen', 'settings', 'sliders', 'prodi', 'fakultas'
        ];

        foreach ($tables as $table) {
            try {
                DB::table($table)->truncate();
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('   ✓ Data cleared');
    }

    private function seedFakultasProdi(): void
    {
        $this->command->info('📚 Seeding Fakultas & Prodi...');

        // Fakultas 1: Ilmu Humaniora, Hukum & Pariwisata
        $this->fakultasIHHP = Fakultas::create([
            'nama' => 'Fakultas Ilmu Humaniora, Hukum & Pariwisata',
            'slug' => 'humaniora-hukum-pariwisata',
            'subdomain' => 'fihhp',
            'kode' => 'FIHHP',
            'deskripsi' => 'Fakultas Ilmu Humaniora, Hukum & Pariwisata Universitas Bumigora menyelenggarakan pendidikan tinggi di bidang humaniora, ilmu hukum, dan pariwisata. Fakultas ini bertujuan menghasilkan lulusan yang kompeten, beretika, dan siap berkontribusi dalam pembangunan masyarakat.',
            'visi' => 'Menjadi fakultas unggul dalam pengembangan sumber daya manusia di bidang humaniora, hukum, dan pariwisata yang berdaya saing global.',
            'misi' => "1. Menyelenggarakan pendidikan berkualitas di bidang humaniora, hukum, dan pariwisata\n2. Melaksanakan penelitian inovatif untuk pengembangan ilmu pengetahuan\n3. Mengabdi kepada masyarakat melalui penerapan ilmu pengetahuan\n4. Membangun kerjasama dengan berbagai pihak di tingkat nasional dan internasional",
            'email' => 'fihhp@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 1,
        ]);

        // Fakultas 2: Pendidikan
        $this->fakultasPendidikan = Fakultas::create([
            'nama' => 'Fakultas Pendidikan',
            'slug' => 'pendidikan',
            'subdomain' => 'fp',
            'kode' => 'FP',
            'deskripsi' => 'Fakultas Pendidikan Universitas Bumigora berkomitmen menghasilkan tenaga pendidik profesional yang kompeten dan berkarakter. Dengan kurikulum berbasis kompetensi dan praktik, fakultas ini mempersiapkan mahasiswa untuk menjadi guru dan pelatih yang berkualitas.',
            'visi' => 'Menjadi fakultas pendidikan terkemuka yang menghasilkan pendidik profesional dan inovatif.',
            'misi' => "1. Menyelenggarakan pendidikan guru yang berkualitas dan relevan dengan kebutuhan masyarakat\n2. Mengembangkan penelitian pendidikan yang inovatif dan aplikatif\n3. Melakukan pengabdian kepada masyarakat di bidang pendidikan\n4. Membina kerjasama dengan lembaga pendidikan dan industri",
            'email' => 'fp@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 2,
        ]);

        // Prodi Fakultas Ilmu Humaniora, Hukum & Pariwisata
        $this->prodiSastraInggris = Prodi::create([
            'fakultas_id' => $this->fakultasIHHP->id,
            'nama' => 'Sastra Inggris',
            'slug' => 'sastra-inggris',
            'subdomain' => 'sasing',
            'kode' => 'SASING',
            'jenjang' => Jenjang::S1,
            'deskripsi' => 'Program Studi S1 Sastra Inggris Universitas Bumigora menyiapkan lulusan yang menguasai bahasa Inggris secara komprehensif dalam aspek linguistik, sastra, dan budaya. Lulusan siap berkarir di berbagai sektor seperti penerjemahan, pendidikan, media, dan industri pariwisata.',
            'visi' => 'Menjadi program studi sastra Inggris yang unggul dalam menghasilkan lulusan kompeten di bidang bahasa, sastra, dan budaya Inggris.',
            'misi' => "1. Menyelenggarakan pendidikan sastra Inggris yang berkualitas\n2. Mengembangkan kemampuan berbahasa Inggris mahasiswa secara aktif dan pasif\n3. Melakukan penelitian di bidang linguistik, sastra, dan budaya\n4. Membekali mahasiswa dengan keterampilan praktis untuk dunia kerja",
            'akreditasi' => 'Baik Sekali',
            'no_sk_akreditasi' => '1234/SK/BAN-PT/2024',
            'email' => 'sasing@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 1,
        ]);

        $this->prodiHukum = Prodi::create([
            'fakultas_id' => $this->fakultasIHHP->id,
            'nama' => 'Hukum',
            'slug' => 'hukum',
            'subdomain' => 'hukum',
            'kode' => 'HKM',
            'jenjang' => Jenjang::S1,
            'deskripsi' => 'Program Studi S1 Hukum Universitas Bumigora mendidik mahasiswa untuk memahami dan menerapkan ilmu hukum secara teoritis dan praktis. Kurikulum dirancang untuk menghasilkan sarjana hukum yang profesional, berintegritas, dan mampu berkontribusi dalam penegakan keadilan.',
            'visi' => 'Menjadi program studi hukum terkemuka yang menghasilkan sarjana hukum berintegritas dan profesional.',
            'misi' => "1. Menyelenggarakan pendidikan hukum yang berkualitas dan relevan\n2. Melaksanakan penelitian hukum yang bermanfaat bagi masyarakat\n3. Melakukan pengabdian dalam bentuk bantuan hukum kepada masyarakat\n4. Menjalin kerjasama dengan lembaga penegak hukum dan praktisi",
            'akreditasi' => 'Baik Sekali',
            'no_sk_akreditasi' => '1235/SK/BAN-PT/2024',
            'email' => 'hukum@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 2,
        ]);

        $this->prodiPariwisata = Prodi::create([
            'fakultas_id' => $this->fakultasIHHP->id,
            'nama' => 'Pariwisata',
            'slug' => 'pariwisata',
            'subdomain' => 'pariwisata',
            'kode' => 'PAR',
            'jenjang' => Jenjang::S1,
            'deskripsi' => 'Program Studi S1 Pariwisata Universitas Bumigora mempersiapkan mahasiswa untuk menjadi profesional di industri pariwisata dan hospitality. Dengan lokasi strategis di Lombok sebagai destinasi wisata internasional, mahasiswa mendapat pengalaman belajar langsung di industri.',
            'visi' => 'Menjadi program studi pariwisata unggulan yang menghasilkan profesional pariwisata berdaya saing global.',
            'misi' => "1. Menyelenggarakan pendidikan pariwisata berbasis praktik dan industri\n2. Mengembangkan penelitian pariwisata berkelanjutan\n3. Bermitra dengan industri pariwisata untuk pengembangan kurikulum dan magang\n4. Mengembangkan pariwisata berbasis masyarakat lokal",
            'akreditasi' => 'Baik',
            'no_sk_akreditasi' => '1236/SK/BAN-PT/2024',
            'email' => 'pariwisata@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 3,
        ]);

        // Prodi Fakultas Pendidikan
        $this->prodiPTI = Prodi::create([
            'fakultas_id' => $this->fakultasPendidikan->id,
            'nama' => 'Pendidikan Teknologi Informasi',
            'slug' => 'pendidikan-teknologi-informasi',
            'subdomain' => 'pti',
            'kode' => 'PTI',
            'jenjang' => Jenjang::S1,
            'deskripsi' => 'Program Studi S1 Pendidikan Teknologi Informasi Universitas Bumigora mencetak guru TI profesional yang menguasai teknologi informasi dan metodologi pengajaran modern. Lulusan siap mengajar di sekolah menengah dan lembaga pendidikan lainnya.',
            'visi' => 'Menjadi program studi pendidikan TI unggul yang menghasilkan pendidik profesional di bidang teknologi informasi.',
            'misi' => "1. Menyelenggarakan pendidikan calon guru TI yang berkualitas\n2. Mengembangkan media dan metode pembelajaran TI yang inovatif\n3. Melakukan penelitian di bidang pendidikan teknologi informasi\n4. Mengabdi kepada masyarakat melalui pelatihan dan pendampingan",
            'akreditasi' => 'Baik Sekali',
            'no_sk_akreditasi' => '1237/SK/BAN-PT/2024',
            'email' => 'pti@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 1,
        ]);

        $this->prodiPKO = Prodi::create([
            'fakultas_id' => $this->fakultasPendidikan->id,
            'nama' => 'Pendidikan Kepelatihan Olahraga',
            'slug' => 'pendidikan-kepelatihan-olahraga',
            'subdomain' => 'pko',
            'kode' => 'PKO',
            'jenjang' => Jenjang::S1,
            'deskripsi' => 'Program Studi S1 Pendidikan Kepelatihan Olahraga Universitas Bumigora mendidik mahasiswa untuk menjadi pelatih olahraga profesional dan guru pendidikan jasmani. Kurikulum memadukan teori kepelatihan dengan praktik langsung.',
            'visi' => 'Menjadi program studi kepelatihan olahraga terkemuka yang menghasilkan pelatih dan pendidik olahraga profesional.',
            'misi' => "1. Menyelenggarakan pendidikan kepelatihan olahraga berkualitas tinggi\n2. Mengembangkan metode kepelatihan berbasis sains\n3. Melakukan penelitian untuk pengembangan prestasi olahraga\n4. Bermitra dengan federasi olahraga untuk pengembangan atlet",
            'akreditasi' => 'Baik',
            'no_sk_akreditasi' => '1238/SK/BAN-PT/2024',
            'email' => 'pko@ubg.ac.id',
            'telepon' => '(0370) 633837',
            'is_active' => true,
            'is_published' => true,
            'order' => 2,
        ]);

        $this->command->info('   ✓ 2 Fakultas dan 5 Prodi created');
    }

    private function seedSettings(): void
    {
        $this->command->info('⚙️  Seeding Settings...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $unitName = $unit['name'];
            $siteName = $unit['unit_type'] === UnitType::UNIVERSITAS 
                ? 'Universitas Bumigora'
                : $unitName . ' - Universitas Bumigora';

            $settings = [
                // General
                ['key' => 'site_name', 'value' => $siteName, 'type' => 'text'],
                ['key' => 'site_description', 'value' => $unitName . ' - Kampus Unggulan di Nusa Tenggara Barat', 'type' => 'textarea'],
                ['key' => 'site_keywords', 'value' => strtolower($unitName) . ', universitas bumigora, ubg, kampus ntb', 'type' => 'text'],

                // Contact
                ['key' => 'address', 'value' => 'Jl. Ismail Marzuki No. 22, Cilinaya, Kec. Cakranegara, Kota Mataram, Nusa Tenggara Barat 83239', 'type' => 'textarea'],
                ['key' => 'phone', 'value' => '(0370) 633837', 'type' => 'text'],
                ['key' => 'email', 'value' => $unit['email'] ?? 'info@ubg.ac.id', 'type' => 'email'],
                ['key' => 'whatsapp', 'value' => '62817788899', 'type' => 'text'],

                // Social Media
                ['key' => 'facebook', 'value' => 'https://facebook.com/universitasbumigora', 'type' => 'url'],
                ['key' => 'instagram', 'value' => 'https://instagram.com/universitasbumigora', 'type' => 'url'],
                ['key' => 'youtube', 'value' => 'https://youtube.com/@universitasbumigora', 'type' => 'url'],
                ['key' => 'twitter', 'value' => '', 'type' => 'url'],
                ['key' => 'linkedin', 'value' => '', 'type' => 'url'],
                ['key' => 'tiktok', 'value' => '', 'type' => 'url'],

                // Theme - each unit can have different colors
                ['key' => 'theme_color_primary', 'value' => $unit['primary_color'] ?? '#0b5ed7', 'type' => 'color'],
                ['key' => 'theme_color_secondary', 'value' => '#64748b', 'type' => 'color'],
                ['key' => 'theme_color_accent', 'value' => '#f59e0b', 'type' => 'color'],
                ['key' => 'theme_font_heading', 'value' => 'Inter', 'type' => 'text'],
                ['key' => 'theme_font_body', 'value' => 'Inter', 'type' => 'text'],

                // Features
                ['key' => 'show_announcement_bar', 'value' => 'true', 'type' => 'boolean'],
                ['key' => 'show_floating_whatsapp', 'value' => 'true', 'type' => 'boolean'],
                ['key' => 'show_back_to_top', 'value' => 'true', 'type' => 'boolean'],
                ['key' => 'enable_dark_mode', 'value' => 'false', 'type' => 'boolean'],

                // Content
                ['key' => 'articles_per_page', 'value' => '12', 'type' => 'integer'],
                ['key' => 'featured_articles_count', 'value' => '4', 'type' => 'integer'],
                ['key' => 'sidebar_articles_count', 'value' => '5', 'type' => 'integer'],

                // Footer
                ['key' => 'footer_text_left', 'value' => '© 2026 ' . $unitName . '. All rights reserved.', 'type' => 'text'],
                ['key' => 'footer_text_right', 'value' => 'Developed with ❤️ by PUSTIK UBG', 'type' => 'text'],
            ];

            foreach ($settings as $setting) {
                Setting::create([
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]);
            }
        }

        $this->command->info('   ✓ Settings created for all units');
    }

    private function seedPages(): void
    {
        $this->command->info('📄 Seeding Pages...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
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
                    'content' => '<h2>Sejarah ' . $unitName . '</h2><p>' . $unitName . ' didirikan sebagai bagian dari Universitas Bumigora dengan komitmen untuk memberikan pendidikan berkualitas kepada masyarakat Nusa Tenggara Barat.</p><p>Seiring berjalannya waktu, ' . $unitName . ' terus berkembang dengan menambah fasilitas, meningkatkan kualitas pengajaran, dan memperluas jaringan kerjasama dengan berbagai institusi.</p><p>Saat ini, ' . $unitName . ' telah menjadi salah satu pilihan utama bagi calon mahasiswa yang ingin melanjutkan pendidikan tinggi di bidang yang relevan.</p>',
                ],
                [
                    'title' => 'Struktur Organisasi',
                    'slug' => 'struktur-organisasi',
                    'content' => '<h2>Struktur Organisasi ' . $unitName . '</h2><p>Struktur organisasi ' . $unitName . ' dirancang untuk mendukung penyelenggaraan Tri Dharma Perguruan Tinggi secara efektif dan efisien.</p><div class="bg-gray-100 p-4 rounded-lg my-4"><p class="text-center text-gray-500">[ Bagan Struktur Organisasi ]</p></div><p>Setiap unit kerja memiliki tugas dan fungsi yang jelas untuk mendukung pencapaian visi dan misi institusi.</p>',
                ],
                [
                    'title' => 'Fasilitas',
                    'slug' => 'fasilitas',
                    'content' => '<h2>Fasilitas ' . $unitName . '</h2><p>' . $unitName . ' dilengkapi dengan berbagai fasilitas modern untuk mendukung kegiatan belajar mengajar:</p><ul><li>Ruang Kuliah Ber-AC</li><li>Laboratorium Lengkap</li><li>Perpustakaan</li><li>Ruang Dosen</li><li>Ruang Rapat</li><li>Area Parkir</li><li>Kantin</li><li>Musholla</li></ul>',
                ],
                [
                    'title' => 'Kurikulum',
                    'slug' => 'kurikulum',
                    'content' => '<h2>Kurikulum ' . $unitName . '</h2><p>Kurikulum ' . $unitName . ' dirancang dengan mengacu pada standar nasional pendidikan tinggi dan kebutuhan industri. Kurikulum berbasis Outcome Based Education (OBE) untuk memastikan lulusan memiliki kompetensi yang dibutuhkan.</p><h3>Komponen Kurikulum:</h3><ul><li>Mata Kuliah Wajib Universitas</li><li>Mata Kuliah Wajib Program Studi</li><li>Mata Kuliah Pilihan</li><li>Praktik Kerja Lapangan</li><li>Tugas Akhir/Skripsi</li></ul>',
                ],
            ];

            foreach ($pages as $page) {
                Page::create([
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'content' => $page['content'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('   ✓ Pages created for all units');
    }

    private function seedMenus(): void
    {
        $this->command->info('🔗 Seeding Menus...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $visiMisiPage = Page::where('slug', 'visi-misi')
                ->where('unit_type', $unit['unit_type'])
                ->where('unit_id', $unit['unit_id'])
                ->first();

            $isProdi = $unit['unit_type'] === UnitType::PRODI;
            $isFakultas = $unit['unit_type'] === UnitType::FAKULTAS;

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

            // Add Akademik menu only for non-prodi
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
                $this->createMenuWithChildren($menuData, null, $unit['unit_type'], $unit['unit_id']);
            }
        }

        $this->command->info('   ✓ Menus created for all units');
    }

    private function createMenuWithChildren(array $menuData, ?int $parentId, UnitType $unitType, ?int $unitId): void
    {
        $children = $menuData['children'] ?? [];
        unset($menuData['children']);

        $menuData['unit_type'] = $unitType;
        $menuData['unit_id'] = $unitId;
        $menuData['is_active'] = true;

        if ($parentId) {
            $menuData['parent_id'] = $parentId;
        }

        $menu = Menu::create($menuData);

        foreach ($children as $childData) {
            $this->createMenuWithChildren($childData, $menu->id, $unitType, $unitId);
        }
    }

    private function seedArticleCategories(): void
    {
        $this->command->info('📂 Seeding Article Categories...');

        $units = $this->getAllUnits();

        $categories = [
            ['name' => 'Berita Kampus', 'slug' => 'berita-kampus'],
            ['name' => 'Akademik', 'slug' => 'akademik'],
            ['name' => 'Kemahasiswaan', 'slug' => 'kemahasiswaan'],
            ['name' => 'Prestasi', 'slug' => 'prestasi'],
            ['name' => 'Kegiatan', 'slug' => 'kegiatan'],
        ];

        foreach ($units as $unit) {
            foreach ($categories as $index => $cat) {
                ArticleCategory::create([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'description' => 'Kategori ' . $cat['name'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('   ✓ Article categories created for all units');
    }

    private function seedArticles(): void
    {
        $this->command->info('📰 Seeding Articles...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $categories = ArticleCategory::where('unit_type', $unit['unit_type'])
                ->where('unit_id', $unit['unit_id'])
                ->get();

            $unitName = $unit['name'];

            $articles = [
                [
                    'title' => $unitName . ' Membuka Pendaftaran Mahasiswa Baru 2026/2027',
                    'excerpt' => 'Pendaftaran mahasiswa baru tahun akademik 2026/2027 telah dibuka dengan berbagai jalur masuk.',
                    'content' => '<p>' . $unitName . ' dengan bangga mengumumkan pembukaan pendaftaran mahasiswa baru untuk tahun akademik 2026/2027.</p><p>Tersedia berbagai jalur masuk yang dapat dipilih oleh calon mahasiswa, antara lain jalur prestasi akademik, jalur rapor, dan jalur reguler.</p><p>Untuk informasi lebih lanjut dan pendaftaran online, silakan kunjungi website PMB Universitas Bumigora atau datang langsung ke kampus.</p>',
                    'is_featured' => true,
                ],
                [
                    'title' => 'Workshop Pengembangan Soft Skills untuk Mahasiswa ' . $unitName,
                    'excerpt' => 'Workshop intensif untuk mengembangkan kemampuan soft skills mahasiswa agar siap memasuki dunia kerja.',
                    'content' => '<p>' . $unitName . ' mengadakan workshop pengembangan soft skills yang diikuti oleh mahasiswa dari berbagai angkatan.</p><p>Materi workshop meliputi komunikasi efektif, kepemimpinan, manajemen waktu, dan kerja tim. Peserta juga mendapat kesempatan untuk praktik langsung melalui simulasi dan role play.</p><p>Kegiatan ini merupakan bagian dari program pengembangan karakter mahasiswa untuk menghasilkan lulusan yang tidak hanya kompeten secara akademik, tetapi juga memiliki soft skills yang baik.</p>',
                    'is_featured' => false,
                ],
                [
                    'title' => 'Kuliah Tamu: Perkembangan Industri dan Peluang Karir',
                    'excerpt' => 'Kuliah tamu menghadirkan praktisi industri untuk berbagi pengalaman dan wawasan tentang dunia kerja.',
                    'content' => '<p>' . $unitName . ' mengundang praktisi industri untuk memberikan kuliah tamu kepada mahasiswa.</p><p>Narasumber berbagi pengalaman tentang perkembangan industri terkini, keterampilan yang dibutuhkan, dan tips sukses berkarir. Mahasiswa juga berkesempatan berdiskusi langsung dengan narasumber.</p><p>Kegiatan ini diharapkan dapat memberikan gambaran nyata tentang dunia kerja dan memotivasi mahasiswa untuk mempersiapkan diri dengan baik.</p>',
                    'is_featured' => true,
                ],
                [
                    'title' => 'Mahasiswa ' . $unitName . ' Ikuti Program Magang di Perusahaan Ternama',
                    'excerpt' => 'Puluhan mahasiswa mengikuti program magang di berbagai perusahaan nasional dan multinasional.',
                    'content' => '<p>Sebanyak puluhan mahasiswa ' . $unitName . ' mengikuti program magang di berbagai perusahaan ternama.</p><p>Program magang ini merupakan bagian dari kurikulum yang wajib diikuti mahasiswa untuk mendapatkan pengalaman kerja nyata. Mahasiswa ditempatkan di berbagai departemen sesuai dengan bidang studi masing-masing.</p><p>Melalui program ini, mahasiswa dapat mengaplikasikan ilmu yang dipelajari di kampus dan membangun jaringan profesional sejak dini.</p>',
                    'is_featured' => false,
                ],
                [
                    'title' => $unitName . ' Gelar Seminar Nasional Dengan Tema Inovasi',
                    'excerpt' => 'Seminar nasional dengan tema inovasi menghadirkan pembicara ahli dari berbagai institusi.',
                    'content' => '<p>' . $unitName . ' menyelenggarakan seminar nasional dengan tema "Inovasi untuk Indonesia Maju" yang diikuti oleh ratusan peserta.</p><p>Seminar menghadirkan pembicara dari kalangan akademisi, praktisi, dan pemerintahan. Berbagai topik dibahas mulai dari inovasi teknologi, inovasi sosial, hingga inovasi dalam pendidikan.</p><p>Para peserta mendapat wawasan baru dan inspirasi untuk berkontribusi dalam pembangunan bangsa melalui inovasi di bidang masing-masing.</p>',
                    'is_featured' => true,
                ],
                [
                    'title' => 'Pengabdian Masyarakat: ' . $unitName . ' Berikan Pelatihan ke Desa Binaan',
                    'excerpt' => 'Tim pengabdian masyarakat memberikan pelatihan kepada warga desa binaan.',
                    'content' => '<p>Tim pengabdian masyarakat ' . $unitName . ' melaksanakan kegiatan pelatihan di desa binaan.</p><p>Pelatihan meliputi berbagai topik yang relevan dengan kebutuhan masyarakat setempat. Warga sangat antusias mengikuti kegiatan ini dan berharap dapat segera mengaplikasikan ilmu yang didapat.</p><p>Kegiatan pengabdian masyarakat ini merupakan wujud Tri Dharma Perguruan Tinggi dan komitmen ' . $unitName . ' untuk berkontribusi bagi masyarakat.</p>',
                    'is_featured' => false,
                ],
            ];

            foreach ($articles as $data) {
                Article::create([
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'category_id' => $categories->random()->id,
                    'author_id' => $this->author?->id,
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'status' => ArticleStatus::PUBLISHED,
                    'is_featured' => $data['is_featured'],
                    'published_at' => now()->subDays(rand(1, 30)),
                    'view_count' => rand(50, 500),
                ]);
            }
        }

        $this->command->info('   ✓ Articles created for all units');
    }

    private function seedEvents(): void
    {
        $this->command->info('📅 Seeding Events...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $unitName = $unit['name'];

            $events = [
                [
                    'title' => 'Seminar Nasional ' . $unitName,
                    'description' => 'Seminar nasional dengan tema inovasi dan pengembangan ilmu pengetahuan yang diselenggarakan oleh ' . $unitName . '.',
                    'location' => 'Auditorium Universitas Bumigora',
                    'start_date' => now()->addDays(14),
                    'end_date' => now()->addDays(14)->addHours(8),
                    'registration_link' => 'https://form.ubg.ac.id/seminar',
                ],
                [
                    'title' => 'Workshop Penulisan Karya Ilmiah',
                    'description' => 'Workshop untuk meningkatkan kemampuan menulis karya ilmiah bagi mahasiswa dan dosen.',
                    'location' => 'Ruang Seminar Lt. 3',
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addDays(7)->addHours(6),
                    'registration_link' => null,
                ],
                [
                    'title' => 'Kuliah Umum: Menghadapi Era Digital',
                    'description' => 'Kuliah umum dengan tema transformasi digital dan peluang di era industri 4.0.',
                    'location' => 'Aula Utama Kampus',
                    'start_date' => now()->addDays(21),
                    'end_date' => now()->addDays(21)->addHours(3),
                    'registration_link' => 'https://form.ubg.ac.id/kuliah-umum',
                ],
                [
                    'title' => 'Career Fair ' . $unitName . ' 2026',
                    'description' => 'Pameran karir yang menghadirkan berbagai perusahaan untuk memberikan informasi lowongan kerja.',
                    'location' => 'Gedung Serba Guna',
                    'start_date' => now()->addDays(30),
                    'end_date' => now()->addDays(31),
                    'registration_link' => 'https://form.ubg.ac.id/career-fair',
                ],
            ];

            foreach ($events as $event) {
                Event::create([
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'start_date' => $event['start_date'],
                    'end_date' => $event['end_date'],
                    'registration_link' => $event['registration_link'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('   ✓ Events created for all units');
    }

    private function seedAnnouncements(): void
    {
        $this->command->info('📢 Seeding Announcements...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $announcements = [
                [
                    'title' => 'Jadwal Ujian Akhir Semester Genap 2025/2026',
                    'content' => '<p>Ujian Akhir Semester Genap akan dilaksanakan pada tanggal 15-30 Juni 2026. Mahasiswa diharapkan mempersiapkan diri dengan baik.</p>',
                    'priority' => 'high',
                ],
                [
                    'title' => 'Pembayaran UKT Semester Genap',
                    'content' => '<p>Batas pembayaran UKT semester genap adalah tanggal 28 Februari 2026. Keterlambatan pembayaran akan dikenakan denda.</p>',
                    'priority' => 'urgent',
                ],
                [
                    'title' => 'Pendaftaran Wisuda Periode Maret 2026',
                    'content' => '<p>Pendaftaran wisuda periode Maret 2026 dibuka mulai tanggal 1-15 Februari 2026 melalui sistem akademik online.</p>',
                    'priority' => 'normal',
                ],
                [
                    'title' => 'Libur Akademik Hari Raya',
                    'content' => '<p>Universitas Bumigora akan libur dalam rangka hari raya. Kegiatan akademik akan kembali normal setelah masa libur.</p>',
                    'priority' => 'normal',
                ],
            ];

            foreach ($announcements as $ann) {
                Announcement::create([
                    'title' => $ann['title'],
                    'content' => $ann['content'],
                    'priority' => $ann['priority'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                ]);
            }
        }

        $this->command->info('   ✓ Announcements created for all units');
    }

    private function seedGalleries(): void
    {
        $this->command->info('🖼️  Seeding Galleries...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $unitName = $unit['name'];

            $galleries = [
                [
                    'title' => 'Kegiatan Akademik ' . $unitName,
                    'description' => 'Dokumentasi kegiatan perkuliahan dan praktikum di ' . $unitName . '.',
                    'type' => 'image',
                ],
                [
                    'title' => 'Wisuda ' . $unitName,
                    'description' => 'Momen wisuda mahasiswa ' . $unitName . '.',
                    'type' => 'image',
                ],
                [
                    'title' => 'Kegiatan Mahasiswa',
                    'description' => 'Berbagai kegiatan kemahasiswaan di ' . $unitName . '.',
                    'type' => 'image',
                ],
                [
                    'title' => 'Video Profil ' . $unitName,
                    'description' => 'Video profil resmi ' . $unitName . '.',
                    'type' => 'video',
                    'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ],
            ];

            foreach ($galleries as $index => $gallery) {
                Gallery::create([
                    'title' => $gallery['title'],
                    'description' => $gallery['description'],
                    'type' => $gallery['type'],
                    'youtube_url' => $gallery['youtube_url'] ?? null,
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('   ✓ Galleries created for all units');
    }

    private function seedDownloads(): void
    {
        $this->command->info('📥 Seeding Downloads...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $downloads = [
                [
                    'title' => 'Kalender Akademik 2025/2026',
                    'description' => 'Kalender akademik tahun ajaran 2025/2026.',
                    'category' => 'kalender-akademik',
                    'file' => 'downloads/kalender-akademik-2025-2026.pdf',
                    'file_size' => 1024000,
                ],
                [
                    'title' => 'Panduan Akademik',
                    'description' => 'Buku panduan akademik untuk mahasiswa.',
                    'category' => 'akademik',
                    'file' => 'downloads/panduan-akademik.pdf',
                    'file_size' => 2048000,
                ],
                [
                    'title' => 'Kurikulum Program Studi',
                    'description' => 'Struktur kurikulum program studi.',
                    'category' => 'kurikulum',
                    'file' => 'downloads/kurikulum.pdf',
                    'file_size' => 512000,
                ],
                [
                    'title' => 'Formulir Pengajuan Cuti',
                    'description' => 'Formulir untuk pengajuan cuti akademik.',
                    'category' => 'formulir',
                    'file' => 'downloads/form-cuti.docx',
                    'file_size' => 51200,
                ],
                [
                    'title' => 'Panduan Penulisan Tugas Akhir',
                    'description' => 'Panduan tata cara penulisan tugas akhir/skripsi.',
                    'category' => 'akademik',
                    'file' => 'downloads/panduan-ta.pdf',
                    'file_size' => 1536000,
                ],
            ];

            foreach ($downloads as $index => $download) {
                Download::create([
                    'title' => $download['title'],
                    'description' => $download['description'],
                    'category' => $download['category'],
                    'file' => $download['file'],
                    'file_size' => $download['file_size'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                    'download_count' => rand(10, 200),
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('   ✓ Downloads created for all units');
    }

    private function seedPrestasi(): void
    {
        $this->command->info('🏆 Seeding Prestasi...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $unitName = $unit['name'];

            $prestasiList = [
                [
                    'judul' => 'Juara 1 Lomba Karya Tulis Ilmiah Nasional',
                    'peserta' => 'Tim Mahasiswa ' . $unitName,
                    'tingkat' => PrestasiTingkat::NASIONAL,
                    'kategori' => PrestasiKategori::AKADEMIK,
                    'penyelenggara' => 'Kemenristekdikti',
                    'tanggal' => now()->subDays(30),
                    'deskripsi' => 'Prestasi gemilang diraih oleh tim mahasiswa dalam kompetisi karya tulis ilmiah tingkat nasional.',
                    'is_featured' => true,
                ],
                [
                    'judul' => 'Best Paper Award Conference Internasional',
                    'peserta' => 'Dosen ' . $unitName,
                    'tingkat' => PrestasiTingkat::INTERNASIONAL,
                    'kategori' => PrestasiKategori::PENELITIAN,
                    'penyelenggara' => 'IEEE',
                    'tanggal' => now()->subDays(60),
                    'deskripsi' => 'Paper penelitian mendapat penghargaan Best Paper dalam konferensi internasional.',
                    'is_featured' => true,
                ],
                [
                    'judul' => 'Juara 2 Debat Bahasa Inggris Regional',
                    'peserta' => 'Delegasi Mahasiswa',
                    'tingkat' => PrestasiTingkat::REGIONAL,
                    'kategori' => PrestasiKategori::NON_AKADEMIK,
                    'penyelenggara' => 'English Debate Society NTB',
                    'tanggal' => now()->subDays(45),
                    'deskripsi' => 'Tim debat berhasil meraih posisi runner-up dalam kompetisi debat bahasa Inggris tingkat regional.',
                    'is_featured' => false,
                ],
                [
                    'judul' => 'Medali Emas Kejuaraan Olahraga Mahasiswa',
                    'peserta' => 'Atlet Mahasiswa',
                    'tingkat' => PrestasiTingkat::NASIONAL,
                    'kategori' => PrestasiKategori::OLAHRAGA,
                    'penyelenggara' => 'BELMAWA',
                    'tanggal' => now()->subDays(90),
                    'deskripsi' => 'Atlet mahasiswa berhasil meraih medali emas dalam kejuaraan olahraga antar perguruan tinggi.',
                    'is_featured' => true,
                ],
            ];

            foreach ($prestasiList as $p) {
                Prestasi::create([
                    'judul' => $p['judul'],
                    'peserta' => $p['peserta'],
                    'tingkat' => $p['tingkat'],
                    'kategori' => $p['kategori'],
                    'penyelenggara' => $p['penyelenggara'],
                    'tanggal' => $p['tanggal'],
                    'deskripsi' => $p['deskripsi'],
                    'is_featured' => $p['is_featured'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('   ✓ Prestasi created for all units');
    }

    private function seedDosen(): void
    {
        $this->command->info('👨‍🏫 Seeding Dosen...');

        // Dosen untuk Prodi Sastra Inggris
        $dosenSastraInggris = [
            ['nidn' => '0801018501', 'nama' => 'Ahmad Rifai', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Hum.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'Linguistik Terapan, Pengajaran Bahasa Inggris'],
            ['nidn' => '0815029002', 'nama' => 'Siti Aminah', 'gelar_depan' => null, 'gelar_belakang' => 'S.S., M.A.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Sastra Inggris, Kajian Budaya'],
            ['nidn' => '0823038803', 'nama' => 'Budi Santoso', 'gelar_depan' => null, 'gelar_belakang' => 'S.Pd., M.Pd.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Asisten Ahli', 'bidang_keahlian' => 'Translation Studies, Interpreting'],
        ];

        // Dosen untuk Prodi Hukum
        $dosenHukum = [
            ['nidn' => '0805057504', 'nama' => 'Hendra Wijaya', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.H., M.H.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Guru Besar', 'bidang_keahlian' => 'Hukum Pidana, Kriminologi'],
            ['nidn' => '0812069105', 'nama' => 'Ratna Dewi', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.H., M.Kn.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'Hukum Perdata, Hukum Bisnis'],
            ['nidn' => '0828088906', 'nama' => 'Agus Salim', 'gelar_depan' => null, 'gelar_belakang' => 'S.H., M.H.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Hukum Tata Negara, Hukum Administrasi'],
        ];

        // Dosen untuk Prodi Pariwisata
        $dosenPariwisata = [
            ['nidn' => '0901018507', 'nama' => 'Ni Made Ayu', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Par.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'Manajemen Pariwisata, Hospitality'],
            ['nidn' => '0915029008', 'nama' => 'I Wayan Putra', 'gelar_depan' => null, 'gelar_belakang' => 'S.Par., M.M.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Destinasi Wisata, Tour Planning'],
            ['nidn' => '0923038809', 'nama' => 'Luh Ketut Sari', 'gelar_depan' => null, 'gelar_belakang' => 'S.S.T.Par., M.Par.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Asisten Ahli', 'bidang_keahlian' => 'Ekowisata, Pariwisata Berkelanjutan'],
        ];

        // Dosen untuk Prodi PTI
        $dosenPTI = [
            ['nidn' => '1001018510', 'nama' => 'Eko Prasetyo', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Pd., M.Kom.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'Pendidikan TI, E-Learning'],
            ['nidn' => '1015029011', 'nama' => 'Dewi Kartika', 'gelar_depan' => null, 'gelar_belakang' => 'S.Pd., M.Pd.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Media Pembelajaran, Kurikulum TI'],
            ['nidn' => '1023038812', 'nama' => 'Rudi Hermawan', 'gelar_depan' => null, 'gelar_belakang' => 'S.Kom., M.T.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Asisten Ahli', 'bidang_keahlian' => 'Pemrograman, Basis Data'],
        ];

        // Dosen untuk Prodi PKO
        $dosenPKO = [
            ['nidn' => '1101018513', 'nama' => 'Bambang Sudirman', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Pd.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'Kepelatihan Olahraga, Sport Science'],
            ['nidn' => '1115029014', 'nama' => 'Sri Wahyuni', 'gelar_depan' => null, 'gelar_belakang' => 'S.Pd., M.Or.', 'jenis_kelamin' => 'P', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Fisiologi Olahraga, Kebugaran'],
            ['nidn' => '1123038815', 'nama' => 'Dedi Kurniawan', 'gelar_depan' => null, 'gelar_belakang' => 'S.Pd., M.Pd.', 'jenis_kelamin' => 'L', 'jabatan_fungsional' => 'Asisten Ahli', 'bidang_keahlian' => 'Teknik Olahraga, Pedagogi'],
        ];

        $this->createDosen($dosenSastraInggris, $this->prodiSastraInggris->id);
        $this->createDosen($dosenHukum, $this->prodiHukum->id);
        $this->createDosen($dosenPariwisata, $this->prodiPariwisata->id);
        $this->createDosen($dosenPTI, $this->prodiPTI->id);
        $this->createDosen($dosenPKO, $this->prodiPKO->id);

        $this->command->info('   ✓ Dosen created for all Prodi');
    }

    private function createDosen(array $dosenList, int $prodiId): void
    {
        foreach ($dosenList as $index => $data) {
            $nama = $data['nama'];
            $email = Str::slug($nama, '.') . '@ubg.ac.id';

            Dosen::create([
                'nidn' => $data['nidn'],
                'nama' => $nama,
                'gelar_depan' => $data['gelar_depan'],
                'gelar_belakang' => $data['gelar_belakang'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'email' => $email,
                'jabatan_fungsional' => $data['jabatan_fungsional'],
                'bidang_keahlian' => $data['bidang_keahlian'],
                'bio' => 'Dosen tetap di Universitas Bumigora dengan keahlian di bidang ' . $data['bidang_keahlian'] . '.',
                'prodi_id' => $prodiId,
                'is_active' => true,
                'order' => $index + 1,
            ]);
        }
    }

    private function seedSliders(): void
    {
        $this->command->info('🎠 Seeding Sliders...');

        $units = $this->getAllUnits();

        foreach ($units as $unit) {
            $unitName = $unit['name'];
            
            $sliders = [
                [
                    'title' => 'Selamat Datang di ' . $unitName,
                    'subtitle' => 'Membangun Generasi Unggul dan Berdaya Saing Global',
                    'button_text' => 'Pelajari Lebih Lanjut',
                    'link' => '/profil/visi-misi',
                    'image' => 'sliders/slider-welcome.jpg',
                ],
                [
                    'title' => 'Pendaftaran Mahasiswa Baru 2026',
                    'subtitle' => 'Raih masa depanmu bersama Universitas Bumigora',
                    'button_text' => 'Daftar Sekarang',
                    'link' => 'https://pmb.ubg.ac.id',
                    'image' => 'sliders/slider-pmb.jpg',
                ],
                [
                    'title' => 'Fasilitas Modern',
                    'subtitle' => 'Dilengkapi fasilitas lengkap untuk mendukung pembelajaran',
                    'button_text' => 'Lihat Fasilitas',
                    'link' => '/halaman/fasilitas',
                    'image' => 'sliders/slider-fasilitas.jpg',
                ],
            ];

            foreach ($sliders as $index => $slider) {
                Slider::create([
                    'title' => $slider['title'],
                    'subtitle' => $slider['subtitle'],
                    'button_text' => $slider['button_text'],
                    'link' => $slider['link'],
                    'image' => $slider['image'],
                    'unit_type' => $unit['unit_type'],
                    'unit_id' => $unit['unit_id'],
                    'is_active' => true,
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('   ✓ Sliders created for all units');
    }

    private function getAllUnits(): array
    {
        return [
            // Fakultas 1
            [
                'unit_type' => UnitType::FAKULTAS,
                'unit_id' => $this->fakultasIHHP->id,
                'name' => $this->fakultasIHHP->nama,
                'email' => $this->fakultasIHHP->email,
                'visi' => $this->fakultasIHHP->visi,
                'misi' => $this->fakultasIHHP->misi,
                'primary_color' => '#0d6efd', // Blue
            ],
            // Fakultas 2: Pendidikan
            [
                'unit_type' => UnitType::FAKULTAS,
                'unit_id' => $this->fakultasPendidikan->id,
                'name' => $this->fakultasPendidikan->nama,
                'email' => $this->fakultasPendidikan->email,
                'visi' => $this->fakultasPendidikan->visi,
                'misi' => $this->fakultasPendidikan->misi,
                'primary_color' => '#198754', // Green
            ],
            // Prodi 1: Sastra Inggris
            [
                'unit_type' => UnitType::PRODI,
                'unit_id' => $this->prodiSastraInggris->id,
                'name' => $this->prodiSastraInggris->nama,
                'email' => $this->prodiSastraInggris->email,
                'visi' => $this->prodiSastraInggris->visi,
                'misi' => $this->prodiSastraInggris->misi,
                'primary_color' => '#6f42c1', // Purple
            ],
            // Prodi 2: Hukum
            [
                'unit_type' => UnitType::PRODI,
                'unit_id' => $this->prodiHukum->id,
                'name' => $this->prodiHukum->nama,
                'email' => $this->prodiHukum->email,
                'visi' => $this->prodiHukum->visi,
                'misi' => $this->prodiHukum->misi,
                'primary_color' => '#dc3545', // Red
            ],
            // Prodi 3: Pariwisata
            [
                'unit_type' => UnitType::PRODI,
                'unit_id' => $this->prodiPariwisata->id,
                'name' => $this->prodiPariwisata->nama,
                'email' => $this->prodiPariwisata->email,
                'visi' => $this->prodiPariwisata->visi,
                'misi' => $this->prodiPariwisata->misi,
                'primary_color' => '#fd7e14', // Orange
            ],
            // Prodi 4: PTI
            [
                'unit_type' => UnitType::PRODI,
                'unit_id' => $this->prodiPTI->id,
                'name' => $this->prodiPTI->nama,
                'email' => $this->prodiPTI->email,
                'visi' => $this->prodiPTI->visi,
                'misi' => $this->prodiPTI->misi,
                'primary_color' => '#0dcaf0', // Cyan
            ],
            // Prodi 5: PKO
            [
                'unit_type' => UnitType::PRODI,
                'unit_id' => $this->prodiPKO->id,
                'name' => $this->prodiPKO->nama,
                'email' => $this->prodiPKO->email,
                'visi' => $this->prodiPKO->visi,
                'misi' => $this->prodiPKO->misi,
                'primary_color' => '#20c997', // Teal
            ],
        ];
    }
}
