<?php

namespace Database\Seeders;

use App\Enums\Jenjang;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FakultasProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Struktur Subdomain:
     * - fihhp.ubg.ac.id → Fakultas Ilmu Hukum, Humaniora, dan Pariwisata
     *   - sasing.ubg.ac.id → Prodi Sastra Inggris
     *   - hukum.ubg.ac.id → Prodi Ilmu Hukum
     *   - pariwisata.ubg.ac.id → Prodi Pariwisata
     * 
     * - fp.ubg.ac.id → Fakultas Pendidikan
     *   - pti.ubg.ac.id → Prodi Pendidikan Teknologi Informasi
     *   - pko.ubg.ac.id → Prodi Pendidikan Kepelatihan Olahraga
     */
    public function run(): void
    {
        $fakultasData = [
            // Fakultas yang aktif di sistem baru
            [
                'nama' => 'Fakultas Ilmu Hukum, Humaniora, dan Pariwisata',
                'subdomain' => 'fihhp',
                'kode' => 'FIHHP',
                'deskripsi' => 'Fakultas Ilmu Hukum, Humaniora, dan Pariwisata Universitas Bumigora',
                'is_published' => true,
                'prodi' => [
                    ['nama' => 'Sastra Inggris', 'subdomain' => 'sasing', 'kode' => 'SASING', 'jenjang' => Jenjang::S1, 'is_published' => true],
                    ['nama' => 'Ilmu Hukum', 'subdomain' => 'hukum', 'kode' => 'HKM', 'jenjang' => Jenjang::S1, 'is_published' => true],
                    ['nama' => 'Pariwisata', 'subdomain' => 'pariwisata', 'kode' => 'PAR', 'jenjang' => Jenjang::S1, 'is_published' => true],
                ],
            ],
            [
                'nama' => 'Fakultas Pendidikan',
                'subdomain' => 'fp',
                'kode' => 'FP',
                'deskripsi' => 'Fakultas Pendidikan Universitas Bumigora',
                'is_published' => true,
                'prodi' => [
                    ['nama' => 'Pendidikan Teknologi Informasi', 'subdomain' => 'pti', 'kode' => 'PTI', 'jenjang' => Jenjang::S1, 'is_published' => true],
                    ['nama' => 'Pendidikan Kepelatihan Olahraga', 'subdomain' => 'pko', 'kode' => 'PKO', 'jenjang' => Jenjang::S1, 'is_published' => true],
                ],
            ],

            // Fakultas lain (belum aktif di sistem baru - masih di website lama)
            [
                'nama' => 'Fakultas Teknik',
                'subdomain' => 'ft',
                'kode' => 'FT',
                'deskripsi' => 'Fakultas Teknik Universitas Bumigora',
                'is_published' => false,
                'prodi' => [
                    ['nama' => 'Ilmu Komputer', 'subdomain' => 'ilkom', 'kode' => 'ILKOM', 'jenjang' => Jenjang::S1, 'is_published' => false],
                    ['nama' => 'Teknologi Informasi', 'subdomain' => 'ti', 'kode' => 'TI', 'jenjang' => Jenjang::S1, 'is_published' => false],
                    ['nama' => 'Rekayasa Perangkat Lunak', 'subdomain' => 'rpl', 'kode' => 'RPL', 'jenjang' => Jenjang::S1, 'is_published' => false],
                ],
            ],
            [
                'nama' => 'Fakultas Ekonomi dan Bisnis',
                'subdomain' => 'feb',
                'kode' => 'FEB',
                'deskripsi' => 'Fakultas Ekonomi dan Bisnis Universitas Bumigora',
                'is_published' => false,
                'prodi' => [
                    ['nama' => 'Manajemen', 'subdomain' => 'manajemen', 'kode' => 'MM', 'jenjang' => Jenjang::S1, 'is_published' => false],
                    ['nama' => 'Akuntansi', 'subdomain' => 'akuntansi', 'kode' => 'AK', 'jenjang' => Jenjang::S1, 'is_published' => false],
                    ['nama' => 'Bisnis Digital', 'subdomain' => 'bd', 'kode' => 'BD', 'jenjang' => Jenjang::S1, 'is_published' => false],
                ],
            ],
        ];

        foreach ($fakultasData as $data) {
            $prodiData = $data['prodi'];
            $fakultasPublished = $data['is_published'] ?? false;
            unset($data['prodi'], $data['is_published']);

            $fakultas = Fakultas::updateOrCreate(
                ['kode' => $data['kode']],
                array_merge($data, [
                    'slug' => Str::slug($data['nama']),
                    'is_active' => true,
                    'is_published' => $fakultasPublished,
                    'order' => 0,
                ])
            );

            $order = 1;
            foreach ($prodiData as $prodi) {
                $prodiPublished = $prodi['is_published'] ?? false;
                unset($prodi['is_published']);
                
                Prodi::updateOrCreate(
                    ['kode' => $prodi['kode']],
                    array_merge($prodi, [
                        'fakultas_id' => $fakultas->id,
                        'slug' => Str::slug($prodi['nama']),
                        'deskripsi' => 'Program Studi ' . $prodi['nama'],
                        'is_active' => true,
                        'is_published' => $prodiPublished,
                        'coming_soon_message' => $prodiPublished ? null : 'Website Program Studi ' . $prodi['nama'] . ' sedang dalam pengembangan.',
                        'order' => $order++,
                    ])
                );
            }
        }

        $this->command->info('Fakultas and Prodi seeded successfully!');
        $this->command->info('Total Fakultas: ' . Fakultas::count());
        $this->command->info('Total Prodi: ' . Prodi::count());
        $this->command->info('Published Fakultas: ' . Fakultas::where('is_published', true)->count());
        $this->command->info('Published Prodi: ' . Prodi::where('is_published', true)->count());
    }
}
