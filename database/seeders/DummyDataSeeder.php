<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use App\Enums\UnitType;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Announcement;
use App\Models\Dosen;
use App\Models\Download;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding dummy data for frontend...');

        $this->seedArticleCategories();
        $this->seedArticles();
        $this->seedEvents();
        $this->seedPrestasi();
        $this->seedDosen();
        $this->seedGalleries();
        $this->seedDownloads();
        $this->seedSliders();
        $this->seedAnnouncements();
        $this->seedPages();

        $this->command->info('Dummy data seeding completed!');
    }

    private function seedArticleCategories(): void
    {
        $categories = [
            ['name' => 'Berita Kampus', 'slug' => 'berita-kampus'],
            ['name' => 'Akademik', 'slug' => 'akademik'],
            ['name' => 'Kemahasiswaan', 'slug' => 'kemahasiswaan'],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman'],
            ['name' => 'Penelitian', 'slug' => 'penelitian'],
        ];

        foreach ($categories as $index => $cat) {
            ArticleCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => 'Kategori ' . $cat['name'],
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                    'order' => $index + 1,
                ]
            );
        }

        $this->command->info('Article categories seeded: ' . ArticleCategory::count());
    }

    private function seedArticles(): void
    {
        $author = User::first();
        $categories = ArticleCategory::all();

        $articles = [
            [
                'title' => 'Universitas Bumigora Raih Akreditasi Unggul dari BAN-PT',
                'excerpt' => 'Pencapaian gemilang bagi Universitas Bumigora dengan meraih akreditasi Unggul dari Badan Akreditasi Nasional Perguruan Tinggi.',
                'content' => '<p>Universitas Bumigora dengan bangga mengumumkan pencapaian luar biasa dalam perjalanan akademiknya. Badan Akreditasi Nasional Perguruan Tinggi (BAN-PT) telah memberikan status akreditasi "Unggul" kepada institusi kita.</p><p>Keberhasilan ini merupakan hasil kerja keras seluruh civitas akademika, mulai dari pimpinan universitas, dosen, tenaga kependidikan, hingga mahasiswa yang terus berkontribusi dalam peningkatan mutu pendidikan.</p><p>Dengan akreditasi Unggul ini, Universitas Bumigora semakin mantap dalam posisinya sebagai perguruan tinggi berkualitas di Nusa Tenggara Barat.</p>',
                'is_featured' => true,
            ],
            [
                'title' => 'Pendaftaran Mahasiswa Baru Tahun Akademik 2026/2027 Dibuka',
                'excerpt' => 'Universitas Bumigora membuka pendaftaran mahasiswa baru untuk tahun akademik 2026/2027 dengan berbagai program studi unggulan.',
                'content' => '<p>Pendaftaran mahasiswa baru Universitas Bumigora tahun akademik 2026/2027 resmi dibuka mulai tanggal 1 Februari 2026.</p><p>Tersedia berbagai program studi unggulan dari 4 fakultas yang siap menyambut calon mahasiswa baru yang ingin mengembangkan potensi dan meraih masa depan cerah bersama Universitas Bumigora.</p><p>Pendaftaran dapat dilakukan secara online melalui website pmb.ubg.ac.id atau secara langsung di kampus.</p>',
                'is_featured' => true,
            ],
            [
                'title' => 'Kerjasama Internasional dengan Universitas di Malaysia',
                'excerpt' => 'Universitas Bumigora menandatangani MoU dengan Universiti Teknologi Malaysia untuk program pertukaran mahasiswa dan dosen.',
                'content' => '<p>Dalam rangka meningkatkan kualitas pendidikan dan memperluas jaringan internasional, Universitas Bumigora telah menandatangani Memorandum of Understanding (MoU) dengan Universiti Teknologi Malaysia (UTM).</p><p>Kerjasama ini meliputi program pertukaran mahasiswa, pertukaran dosen, penelitian kolaboratif, dan pengembangan kurikulum bersama.</p>',
                'is_featured' => false,
            ],
            [
                'title' => 'Workshop Digital Marketing untuk Mahasiswa',
                'excerpt' => 'Pusat Karir UBG mengadakan workshop digital marketing untuk mempersiapkan mahasiswa menghadapi dunia kerja.',
                'content' => '<p>Pusat Karir Universitas Bumigora mengadakan workshop intensif digital marketing selama 3 hari yang diikuti oleh lebih dari 200 mahasiswa dari berbagai program studi.</p><p>Workshop ini menghadirkan praktisi digital marketing berpengalaman yang berbagi ilmu tentang SEO, Social Media Marketing, dan Content Marketing.</p>',
                'is_featured' => false,
            ],
            [
                'title' => 'Tim Robotika UBG Juara Nasional Kontes Robot Indonesia',
                'excerpt' => 'Tim Robotika Universitas Bumigora berhasil meraih juara pertama dalam Kontes Robot Indonesia 2026.',
                'content' => '<p>Kebanggaan besar bagi Universitas Bumigora! Tim Robotika yang beranggotakan mahasiswa Teknik Informatika berhasil meraih juara pertama dalam Kontes Robot Indonesia (KRI) 2026 yang diselenggarakan di Jakarta.</p><p>Tim yang dipimpin oleh Ahmad Fauzi ini berhasil mengalahkan lebih dari 50 tim dari berbagai perguruan tinggi ternama di Indonesia.</p>',
                'is_featured' => true,
            ],
            [
                'title' => 'Wisuda Periode Januari 2026',
                'excerpt' => 'Universitas Bumigora menggelar wisuda periode Januari 2026 dengan meluluskan 500 wisudawan.',
                'content' => '<p>Universitas Bumigora dengan khidmat menggelar upacara wisuda periode Januari 2026 yang meluluskan 500 wisudawan dari berbagai program studi.</p><p>Acara wisuda dipimpin langsung oleh Rektor Universitas Bumigora dan dihadiri oleh para wakil rektor, dekan, serta orang tua wisudawan.</p>',
                'is_featured' => false,
            ],
        ];

        foreach ($articles as $index => $data) {
            $category = $categories->random();
            
            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'category_id' => $category->id,
                    'author_id' => $author?->id,
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'status' => ArticleStatus::PUBLISHED,
                    'is_featured' => $data['is_featured'],
                    'published_at' => now()->subDays(rand(1, 30)),
                    'view_count' => rand(50, 500),
                ]
            );
        }

        $this->command->info('Articles seeded: ' . Article::count());
    }

    private function seedEvents(): void
    {
        $events = [
            [
                'title' => 'Seminar Nasional Teknologi Informasi 2026',
                'description' => 'Seminar nasional dengan tema "Artificial Intelligence dan Masa Depan Pendidikan" yang menghadirkan pembicara dari dalam dan luar negeri.',
                'location' => 'Auditorium Universitas Bumigora',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(14)->addHours(8),
                'registration_link' => 'https://form.ubg.ac.id/seminar-ti-2026',
            ],
            [
                'title' => 'Career Fair 2026',
                'description' => 'Pameran karir yang menghadirkan lebih dari 50 perusahaan nasional dan multinasional untuk memberikan kesempatan kerja bagi mahasiswa dan alumni.',
                'location' => 'Gedung Serba Guna UBG',
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(22),
                'registration_link' => 'https://form.ubg.ac.id/career-fair-2026',
            ],
            [
                'title' => 'Workshop Penulisan Artikel Ilmiah',
                'description' => 'Workshop intensif untuk meningkatkan kemampuan menulis artikel ilmiah bagi mahasiswa dan dosen.',
                'location' => 'Ruang Seminar Lt. 3 Gedung A',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(7)->addHours(6),
                'registration_link' => null,
            ],
            [
                'title' => 'Dies Natalis ke-30 Universitas Bumigora',
                'description' => 'Perayaan ulang tahun ke-30 Universitas Bumigora dengan berbagai rangkaian acara menarik.',
                'location' => 'Seluruh Area Kampus UBG',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(32),
                'registration_link' => null,
            ],
            [
                'title' => 'Webinar: Peluang Beasiswa Luar Negeri',
                'description' => 'Webinar informatif tentang berbagai peluang beasiswa untuk melanjutkan studi di luar negeri.',
                'location' => 'Online via Zoom',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addDays(3)->addHours(2),
                'registration_link' => 'https://form.ubg.ac.id/webinar-beasiswa',
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['title' => $event['title'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($event, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Events seeded: ' . Event::count());
    }

    private function seedPrestasi(): void
    {
        $prestasi = [
            [
                'judul' => 'Juara 1 Kontes Robot Indonesia 2026',
                'peserta' => 'Tim Robotika UBG',
                'tingkat' => PrestasiTingkat::NASIONAL,
                'kategori' => PrestasiKategori::AKADEMIK,
                'penyelenggara' => 'Kemenristekdikti',
                'tanggal' => now()->subDays(10),
                'deskripsi' => 'Tim Robotika UBG berhasil meraih juara pertama dalam Kontes Robot Indonesia 2026 kategori robot sepak bola.',
                'is_featured' => true,
            ],
            [
                'judul' => 'Best Paper Award - ICAICTA 2025',
                'peserta' => 'Dr. Budi Santoso, M.Kom',
                'tingkat' => PrestasiTingkat::INTERNASIONAL,
                'kategori' => PrestasiKategori::PENELITIAN,
                'penyelenggara' => 'IEEE Indonesia Section',
                'tanggal' => now()->subDays(45),
                'deskripsi' => 'Paper tentang Machine Learning untuk Deteksi Dini Penyakit meraih Best Paper Award.',
                'is_featured' => true,
            ],
            [
                'judul' => 'Juara 2 Debat Bahasa Inggris Tingkat Provinsi',
                'peserta' => 'Siti Nurhaliza',
                'tingkat' => PrestasiTingkat::REGIONAL,
                'kategori' => PrestasiKategori::NON_AKADEMIK,
                'penyelenggara' => 'Dinas Pendidikan NTB',
                'tanggal' => now()->subDays(20),
                'deskripsi' => 'Mahasiswa Ilmu Komunikasi meraih prestasi dalam kompetisi debat bahasa Inggris.',
                'is_featured' => false,
            ],
            [
                'judul' => 'Medali Emas PON XXI - Cabor Badminton',
                'peserta' => 'Ahmad Rizki',
                'tingkat' => PrestasiTingkat::NASIONAL,
                'kategori' => PrestasiKategori::OLAHRAGA,
                'penyelenggara' => 'KONI',
                'tanggal' => now()->subDays(60),
                'deskripsi' => 'Mahasiswa UBG meraih medali emas dalam PON XXI mewakili kontingen NTB.',
                'is_featured' => true,
            ],
            [
                'judul' => 'Finalis Lomba Karya Tulis Ilmiah Nasional',
                'peserta' => 'Kelompok Studi Ekonomi UBG',
                'tingkat' => PrestasiTingkat::NASIONAL,
                'kategori' => PrestasiKategori::AKADEMIK,
                'penyelenggara' => 'Kemenristekdikti',
                'tanggal' => now()->subDays(30),
                'deskripsi' => 'Tim mahasiswa Fakultas Ekonomi dan Bisnis masuk sebagai finalis LKTI Nasional.',
                'is_featured' => false,
            ],
        ];

        foreach ($prestasi as $p) {
            Prestasi::updateOrCreate(
                ['judul' => $p['judul'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($p, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Prestasi seeded: ' . Prestasi::count());
    }

    private function seedDosen(): void
    {
        $prodis = Prodi::all();
        
        if ($prodis->isEmpty()) {
            $this->command->warn('No Prodi found. Skipping dosen seeding.');
            return;
        }

        $dosenData = [
            [
                'nidn' => '0801018501',
                'nama' => 'Budi',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.Kom',
                'jenis_kelamin' => 'L',
                'email' => 'budi.santoso@ubg.ac.id',
                'jabatan_fungsional' => 'Lektor Kepala',
                'bidang_keahlian' => 'Artificial Intelligence, Machine Learning',
                'bio' => 'Dosen senior dengan pengalaman lebih dari 15 tahun di bidang kecerdasan buatan.',
            ],
            [
                'nidn' => '0815029002',
                'nama' => 'Dewi Lestari',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kom., M.T.',
                'jenis_kelamin' => 'P',
                'email' => 'dewi.lestari@ubg.ac.id',
                'jabatan_fungsional' => 'Lektor',
                'bidang_keahlian' => 'Web Development, Database',
                'bio' => 'Dosen muda yang aktif dalam pengembangan sistem informasi.',
            ],
            [
                'nidn' => '0823038803',
                'nama' => 'Ahmad Fauzi',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.T., M.Eng.',
                'jenis_kelamin' => 'L',
                'email' => 'ahmad.fauzi@ubg.ac.id',
                'jabatan_fungsional' => 'Asisten Ahli',
                'bidang_keahlian' => 'Robotika, IoT',
                'bio' => 'Penggerak Tim Robotika UBG yang telah meraih berbagai prestasi nasional.',
            ],
            [
                'nidn' => '0805057504',
                'nama' => 'Siti Rahayu',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'M.Si.',
                'jenis_kelamin' => 'P',
                'email' => 'siti.rahayu@ubg.ac.id',
                'jabatan_fungsional' => 'Guru Besar',
                'bidang_keahlian' => 'Ekonomi Digital, E-Commerce',
                'bio' => 'Guru Besar di bidang Ekonomi Digital dengan berbagai publikasi internasional.',
            ],
            [
                'nidn' => '0812069105',
                'nama' => 'Andi Wijaya',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kep., Ns., M.Kep.',
                'jenis_kelamin' => 'L',
                'email' => 'andi.wijaya@ubg.ac.id',
                'jabatan_fungsional' => 'Lektor',
                'bidang_keahlian' => 'Keperawatan Medikal Bedah',
                'bio' => 'Praktisi keperawatan dengan pengalaman klinis lebih dari 10 tahun.',
            ],
            [
                'nidn' => '0828088906',
                'nama' => 'Ratna Kusuma',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.I.Kom.',
                'jenis_kelamin' => 'P',
                'email' => 'ratna.kusuma@ubg.ac.id',
                'jabatan_fungsional' => 'Lektor Kepala',
                'bidang_keahlian' => 'Komunikasi Digital, Media Studies',
                'bio' => 'Pakar komunikasi dengan fokus penelitian pada media digital dan masyarakat.',
            ],
        ];

        foreach ($dosenData as $index => $data) {
            $prodi = $prodis->random();
            
            Dosen::updateOrCreate(
                ['nidn' => $data['nidn']],
                array_merge($data, [
                    'prodi_id' => $prodi->id,
                    'is_active' => true,
                    'order' => $index + 1,
                ])
            );
        }

        $this->command->info('Dosen seeded: ' . Dosen::count());
    }

    private function seedGalleries(): void
    {
        $galleries = [
            [
                'title' => 'Wisuda Periode Januari 2026',
                'description' => 'Dokumentasi upacara wisuda periode Januari 2026 Universitas Bumigora.',
                'type' => 'image',
            ],
            [
                'title' => 'Seminar Nasional TI 2025',
                'description' => 'Dokumentasi Seminar Nasional Teknologi Informasi tahun 2025.',
                'type' => 'image',
            ],
            [
                'title' => 'Kegiatan Mahasiswa Baru',
                'description' => 'Dokumentasi kegiatan orientasi mahasiswa baru tahun akademik 2025/2026.',
                'type' => 'image',
            ],
            [
                'title' => 'Video Profile Universitas Bumigora',
                'description' => 'Video profil resmi Universitas Bumigora.',
                'type' => 'video',
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'title' => 'Dokumentasi Dies Natalis ke-29',
                'description' => 'Rangkaian kegiatan perayaan Dies Natalis ke-29 Universitas Bumigora.',
                'type' => 'image',
            ],
        ];

        foreach ($galleries as $index => $gallery) {
            Gallery::updateOrCreate(
                ['title' => $gallery['title'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($gallery, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                    'order' => $index + 1,
                ])
            );
        }

        $this->command->info('Galleries seeded: ' . Gallery::count());
    }

    private function seedDownloads(): void
    {
        $downloads = [
            [
                'title' => 'Kalender Akademik 2025/2026',
                'description' => 'Kalender akademik Universitas Bumigora tahun akademik 2025/2026.',
                'category' => 'Akademik',
                'file' => 'downloads/kalender-akademik-2025-2026.pdf',
                'file_size' => 1024000,
            ],
            [
                'title' => 'Panduan Penulisan Skripsi',
                'description' => 'Panduan tata cara penulisan skripsi untuk mahasiswa S1.',
                'category' => 'Akademik',
                'file' => 'downloads/panduan-skripsi.pdf',
                'file_size' => 2048000,
            ],
            [
                'title' => 'Formulir Cuti Akademik',
                'description' => 'Formulir pengajuan cuti akademik mahasiswa.',
                'category' => 'Formulir',
                'file' => 'downloads/form-cuti-akademik.docx',
                'file_size' => 51200,
            ],
            [
                'title' => 'Brosur PMB 2026',
                'description' => 'Brosur informasi penerimaan mahasiswa baru tahun 2026.',
                'category' => 'Promosi',
                'file' => 'downloads/brosur-pmb-2026.pdf',
                'file_size' => 5120000,
            ],
            [
                'title' => 'Template Laporan Praktikum',
                'description' => 'Template standar untuk laporan praktikum.',
                'category' => 'Template',
                'file' => 'downloads/template-laporan-praktikum.docx',
                'file_size' => 102400,
            ],
        ];

        foreach ($downloads as $index => $download) {
            Download::updateOrCreate(
                ['title' => $download['title'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($download, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                    'download_count' => rand(10, 200),
                    'order' => $index + 1,
                ])
            );
        }

        $this->command->info('Downloads seeded: ' . Download::count());
    }

    private function seedSliders(): void
    {
        $sliders = [
            [
                'title' => 'Selamat Datang di Universitas Bumigora',
                'subtitle' => 'Membangun Generasi Unggul dan Berdaya Saing Global',
                'button_text' => 'Daftar Sekarang',
                'link' => '/pmb',
                'image' => 'sliders/slider-1.jpg',
            ],
            [
                'title' => 'Akreditasi Unggul',
                'subtitle' => 'Universitas Bumigora meraih akreditasi Unggul dari BAN-PT',
                'button_text' => 'Pelajari Lebih Lanjut',
                'link' => '/profil/akreditasi',
                'image' => 'sliders/slider-2.jpg',
            ],
            [
                'title' => 'Pendaftaran Mahasiswa Baru 2026',
                'subtitle' => 'Raih masa depanmu bersama Universitas Bumigora',
                'button_text' => 'Daftar Online',
                'link' => 'https://pmb.ubg.ac.id',
                'image' => 'sliders/slider-3.jpg',
            ],
        ];

        foreach ($sliders as $index => $slider) {
            Slider::updateOrCreate(
                ['title' => $slider['title'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($slider, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                    'order' => $index + 1,
                ])
            );
        }

        $this->command->info('Sliders seeded: ' . Slider::count());
    }

    private function seedAnnouncements(): void
    {
        $announcements = [
            [
                'title' => 'Pendaftaran Wisuda Periode Maret 2026 Dibuka',
                'content' => 'Pendaftaran wisuda periode Maret 2026 dibuka mulai tanggal 1-15 Februari 2026.',
                'priority' => 'normal',
            ],
            [
                'title' => 'Pembayaran UKT Semester Genap',
                'content' => 'Batas pembayaran UKT semester genap 2025/2026 adalah tanggal 28 Februari 2026.',
                'priority' => 'high',
            ],
            [
                'title' => 'Libur Tahun Baru Imlek',
                'content' => 'Universitas Bumigora libur dalam rangka Tahun Baru Imlek pada tanggal 29 Januari 2026.',
                'priority' => 'normal',
            ],
        ];

        foreach ($announcements as $index => $ann) {
            Announcement::updateOrCreate(
                ['title' => $ann['title'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($ann, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                ])
            );
        }

        $this->command->info('Announcements seeded: ' . Announcement::count());
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => '<p>Universitas Bumigora adalah perguruan tinggi swasta yang berlokasi di Kota Mataram, Nusa Tenggara Barat. Didirikan dengan visi menjadi perguruan tinggi unggul dan berdaya saing global.</p><p>Dengan dukungan tenaga pengajar yang kompeten dan fasilitas yang memadai, Universitas Bumigora terus berkomitmen untuk menghasilkan lulusan yang berkualitas dan siap bersaing di dunia kerja.</p>',
            ],
            [
                'title' => 'Fasilitas',
                'slug' => 'fasilitas',
                'content' => '<p>Universitas Bumigora dilengkapi dengan berbagai fasilitas modern untuk mendukung kegiatan belajar mengajar:</p><ul><li>Laboratorium Komputer</li><li>Perpustakaan Digital</li><li>Ruang Kuliah ber-AC</li><li>Auditorium</li><li>Gedung Serba Guna</li><li>Masjid Kampus</li><li>Kantin</li><li>Area Parkir Luas</li></ul>',
            ],
            [
                'title' => 'Hubungi Kami',
                'slug' => 'hubungi-kami',
                'content' => '<p>Silakan hubungi kami untuk informasi lebih lanjut:</p><p><strong>Alamat:</strong> Jl. Ismail Marzuki No.22, Cilinaya, Kec. Cakranegara, Kota Mataram, NTB 83239</p><p><strong>Telepon:</strong> (0370) 638885</p><p><strong>Email:</strong> info@ubg.ac.id</p>',
            ],
        ];

        foreach ($pages as $index => $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug'], 'unit_type' => UnitType::UNIVERSITAS],
                array_merge($page, [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Pages seeded: ' . Page::count());
    }
}
