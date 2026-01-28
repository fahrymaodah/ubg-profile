<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\Dosen;
use App\Models\Download;
use App\Models\Event;
use App\Models\Fakultas;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Get sliders
        $sliders = Slider::forUnit($unitType, $unitId)
            ->visible()
            ->ordered()
            ->get();

        // Get featured articles
        $featuredCount = $request->attributes->get('settings', [])['featured_articles_count'] ?? 4;
        $featuredArticles = Article::forUnit($unitType, $unitId)
            ->published()
            ->featured()
            ->latest()
            ->take((int) $featuredCount)
            ->get();

        // Get latest articles
        $latestArticles = Article::forUnit($unitType, $unitId)
            ->published()
            ->latest()
            ->take(6)
            ->get();

        // Get upcoming events
        $upcomingEvents = Event::forUnit($unitType, $unitId)
            ->active()
            ->upcoming()
            ->take(3)
            ->get();

        // Get visible announcements
        $announcements = Announcement::forUnit($unitType, $unitId)
            ->visible()
            ->ordered()
            ->take(5)
            ->get();

        // Get featured prestasi
        $prestasi = Prestasi::forUnit($unitType, $unitId)
            ->active()
            ->featured()
            ->latest()
            ->take(4)
            ->get();

        // Get unit data for fakultas/prodi views
        $unit = $request->attributes->get('unit');
        $fakultas = $unitType === UnitType::FAKULTAS ? $unit : null;
        $prodi = $unitType === UnitType::PRODI ? $unit : null;

        // Calculate stats based on unit type
        $stats = $this->calculateStats($unitType, $unit);

        // Get prodi list for fakultas view
        $prodiList = null;
        if ($unitType === UnitType::FAKULTAS && $fakultas) {
            $prodiList = $fakultas->prodi()->where('is_active', true)->get();
        }

        // Determine view based on unit type
        $view = match($unitType) {
            UnitType::FAKULTAS => 'home.fakultas',
            UnitType::PRODI => 'home.prodi',
            default => 'home.universitas',
        };

        return view($view, compact(
            'sliders',
            'featuredArticles',
            'latestArticles',
            'upcomingEvents',
            'announcements',
            'prestasi',
            'fakultas',
            'prodi',
            'stats',
            'prodiList'
        ));
    }

    /**
     * Search across content.
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        $articles = collect();
        $events = collect();
        $dosen = collect();
        $prestasi = collect();
        $galleries = collect();
        $downloads = collect();
        $announcements = collect();
        $pages = collect();

        if (strlen($query) >= 3) {
            // Search articles
            if ($type === 'all' || $type === 'article') {
                $articles = Article::forUnit($unitType, $unitId)
                    ->published()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%")
                          ->orWhere('excerpt', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(10)
                    ->get();
            }

            // Search events
            if ($type === 'all' || $type === 'event') {
                $events = Event::forUnit($unitType, $unitId)
                    ->active()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('location', 'like', "%{$query}%");
                    })
                    ->take(5)
                    ->get();
            }

            // Search dosen
            if ($type === 'all' || $type === 'dosen') {
                $dosenQuery = Dosen::where('is_active', true)
                    ->with('prodi')
                    ->where(function ($q) use ($query) {
                        $q->where('nama', 'like', "%{$query}%")
                          ->orWhere('nidn', 'like', "%{$query}%")
                          ->orWhere('bidang_keahlian', 'like', "%{$query}%")
                          ->orWhere('jabatan_fungsional', 'like', "%{$query}%");
                    });

                // Filter by unit if prodi or fakultas
                if ($unitType === UnitType::PRODI && $unitId) {
                    $dosenQuery->where('prodi_id', $unitId);
                } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
                    $dosenQuery->whereHas('prodi', function ($q) use ($unitId) {
                        $q->where('fakultas_id', $unitId);
                    });
                }

                $dosen = $dosenQuery->take(6)->get();
            }

            // Search prestasi
            if ($type === 'all' || $type === 'prestasi') {
                $prestasi = Prestasi::forUnit($unitType, $unitId)
                    ->active()
                    ->where(function ($q) use ($query) {
                        $q->where('judul', 'like', "%{$query}%")
                          ->orWhere('peserta', 'like', "%{$query}%")
                          ->orWhere('penyelenggara', 'like', "%{$query}%")
                          ->orWhere('deskripsi', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get();
            }

            // Search galleries
            if ($type === 'all' || $type === 'gallery') {
                $galleries = Gallery::forUnit($unitType, $unitId)
                    ->where('is_active', true)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(6)
                    ->get();
            }

            // Search downloads
            if ($type === 'all' || $type === 'download') {
                $downloads = Download::forUnit($unitType, $unitId)
                    ->where('is_active', true)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('category', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get();
            }

            // Search announcements
            if ($type === 'all' || $type === 'announcement') {
                $announcements = Announcement::forUnit($unitType, $unitId)
                    ->active()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get();
            }

            // Search pages
            if ($type === 'all' || $type === 'page') {
                $pages = Page::forUnit($unitType, $unitId)
                    ->active()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%");
                    })
                    ->take(5)
                    ->get();
            }
        }

        return view('search', compact(
            'query', 'type', 'articles', 'events', 'dosen', 
            'prestasi', 'galleries', 'downloads', 'announcements', 'pages'
        ));
    }

    /**
     * Calculate stats based on unit type.
     */
    private function calculateStats(UnitType $unitType, $unit): array
    {
        $stats = [];

        switch ($unitType) {
            case UnitType::UNIVERSITAS:
                $stats = [
                    'fakultas' => Fakultas::where('is_active', true)->count(),
                    'prodi' => Prodi::where('is_active', true)->count(),
                    'dosen' => Dosen::where('is_active', true)->count(),
                    'prestasi' => Prestasi::forUnit($unitType, null)
                        ->active()
                        ->whereIn('tingkat', [\App\Enums\PrestasiTingkat::NASIONAL, \App\Enums\PrestasiTingkat::INTERNASIONAL])
                        ->count(),
                ];
                break;

            case UnitType::FAKULTAS:
                if ($unit) {
                    $prodiIds = $unit->prodi()->where('is_active', true)->pluck('id');
                    $stats = [
                        'prodi' => $prodiIds->count(),
                        'dosen' => Dosen::whereIn('prodi_id', $prodiIds)->where('is_active', true)->count(),
                        'artikel' => $unit->articles()->published()->count(),
                        'prestasi' => $unit->prestasi()->active()->count(),
                    ];
                }
                break;

            case UnitType::PRODI:
                if ($unit) {
                    $stats = [
                        'dosen' => $unit->dosen()->where('is_active', true)->count(),
                        'akreditasi' => $unit->akreditasi ?? '-',
                        'artikel' => $unit->articles()->published()->count(),
                        'prestasi' => $unit->prestasi()->active()->count(),
                    ];
                }
                break;
        }

        return $stats;
    }
}
