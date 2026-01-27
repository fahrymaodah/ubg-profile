<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Dosen;
use App\Models\Event;
use App\Models\Fakultas;
use App\Models\Page;
use App\Models\Prestasi;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml for the current unit
     */
    public function index(Request $request): Response
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        $urls = collect();

        // Add homepage
        $urls->push([
            'loc' => url('/'),
            'lastmod' => now()->toDateString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        // Add static pages
        $pages = Page::forUnit($unitType, $unitId)
            ->active()
            ->get();

        foreach ($pages as $page) {
            $urls->push([
                'loc' => route('page.show', $page->slug),
                'lastmod' => $page->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ]);
        }

        // Add articles
        $articles = Article::forUnit($unitType, $unitId)
            ->published()
            ->latest('published_at')
            ->take(500)
            ->get();

        foreach ($articles as $article) {
            $urls->push([
                'loc' => route('article.show', $article->slug),
                'lastmod' => $article->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);
        }

        // Add article categories
        $categories = ArticleCategory::forUnit($unitType, $unitId)
            ->active()
            ->get();

        foreach ($categories as $category) {
            $urls->push([
                'loc' => route('article.category', $category->slug),
                'lastmod' => $category->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);
        }

        // Add dosen (for fakultas and prodi)
        if ($unitType !== UnitType::UNIVERSITAS) {
            $dosenQuery = Dosen::where('is_active', true);
            
            if ($unitType === UnitType::PRODI && $unitId) {
                $dosenQuery->where('prodi_id', $unitId);
            } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
                $dosenQuery->whereHas('prodi', fn($q) => $q->where('fakultas_id', $unitId));
            }

            $dosen = $dosenQuery->take(200)->get();

            // Dosen index page
            $urls->push([
                'loc' => route('dosen.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);

            foreach ($dosen as $d) {
                $urls->push([
                    'loc' => route('dosen.show', $d->slug),
                    'lastmod' => $d->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ]);
            }
        }

        // Add events
        $events = Event::forUnit($unitType, $unitId)
            ->active()
            ->take(100)
            ->get();

        if ($events->isNotEmpty()) {
            $urls->push([
                'loc' => route('event.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);

            foreach ($events as $event) {
                $urls->push([
                    'loc' => route('event.show', $event->id),
                    'lastmod' => $event->updated_at->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ]);
            }
        }

        // Add prestasi
        $prestasi = Prestasi::forUnit($unitType, $unitId)
            ->active()
            ->take(100)
            ->get();

        if ($prestasi->isNotEmpty()) {
            $urls->push([
                'loc' => route('prestasi.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);

            foreach ($prestasi as $p) {
                $urls->push([
                    'loc' => route('prestasi.show', $p->id),
                    'lastmod' => $p->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ]);
            }
        }

        // For universitas, add fakultas and prodi pages
        if ($unitType === UnitType::UNIVERSITAS) {
            $fakultasList = Fakultas::where('is_active', true)->get();
            
            foreach ($fakultasList as $fakultas) {
                // This would be subdomain, but for SEO we can reference them
                $urls->push([
                    'loc' => url('/fakultas/' . $fakultas->slug),
                    'lastmod' => $fakultas->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ]);
            }

            $prodiList = Prodi::where('is_active', true)->with('fakultas')->get();
            
            foreach ($prodiList as $prodi) {
                $urls->push([
                    'loc' => url('/prodi/' . $prodi->slug),
                    'lastmod' => $prodi->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ]);
            }
        }

        // Add common pages
        $commonPages = [
            ['loc' => route('article.index'), 'priority' => '0.7'],
            ['loc' => route('gallery.index'), 'priority' => '0.5'],
            ['loc' => route('download.index'), 'priority' => '0.5'],
            ['loc' => route('contact.index'), 'priority' => '0.6'],
        ];

        foreach ($commonPages as $page) {
            $urls->push([
                'loc' => $page['loc'],
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => $page['priority'],
            ]);
        }

        $xml = $this->generateXml($urls);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate XML from URLs collection
     */
    protected function generateXml($urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Generate robots.txt for the current unit
     */
    public function robots(Request $request): Response
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unit = $request->attributes->get('unit');

        // Check if unit is published/active
        $isPublished = true;
        if ($unit) {
            $isPublished = $unit->is_active ?? true;
        }

        $content = "User-agent: *\n";

        if ($isPublished) {
            $content .= "Allow: /\n";
            $content .= "Disallow: /admin\n";
            $content .= "Disallow: /admin/*\n";
            $content .= "Disallow: /storage/\n";
            $content .= "Disallow: /*?_unit=*\n";
            $content .= "\n";
            $content .= "# Sitemap\n";
            $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        } else {
            // If not published, disallow all
            $content .= "Disallow: /\n";
        }

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
