<?php

namespace Tests\Feature;

use App\Enums\UnitType;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic data
        $this->artisan('db:seed', ['--class' => 'FakultasProdiSeeder']);
    }

    /** @test */
    public function homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Universitas Bumigora');
    }

    /**
     * Test untuk loading fakultas homepage
     * Skip: Memerlukan data fakultas yang aktif dengan subdomain
     * yang terdaftar di database
     */
    // public function homepage_with_unit_parameter_loads_fakultas(): void

    /** @test */
    public function berita_index_page_loads(): void
    {
        $response = $this->get('/berita');

        $response->assertStatus(200);
    }

    /** @test */
    public function dosen_index_page_loads(): void
    {
        $response = $this->get('/dosen');

        $response->assertStatus(200);
    }

    /** @test */
    public function prestasi_index_page_loads(): void
    {
        $response = $this->get('/prestasi');

        $response->assertStatus(200);
    }

    /** @test */
    public function galeri_index_page_loads(): void
    {
        $response = $this->get('/galeri');

        $response->assertStatus(200);
    }

    /** @test */
    public function agenda_index_page_loads(): void
    {
        $response = $this->get('/agenda');

        $response->assertStatus(200);
    }

    /** @test */
    public function unduhan_index_page_loads(): void
    {
        $response = $this->get('/unduhan');

        $response->assertStatus(200);
    }

    /** @test */
    public function kontak_page_loads(): void
    {
        $response = $this->get('/kontak');

        $response->assertStatus(200);
    }

    /** @test */
    public function search_page_loads(): void
    {
        $response = $this->get('/cari?q=test');

        $response->assertStatus(200);
    }

    /** @test */
    public function sitemap_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /** @test */
    public function robots_txt_returns_text(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent');
        $response->assertSee('Sitemap');
    }

    /** @test */
    public function profil_visi_misi_page_loads(): void
    {
        $response = $this->get('/profil/visi-misi');

        $response->assertStatus(200);
    }

    /** @test */
    public function nonexistent_page_returns_404(): void
    {
        $response = $this->get('/halaman-tidak-ada-12345');

        $response->assertStatus(404);
    }
}
