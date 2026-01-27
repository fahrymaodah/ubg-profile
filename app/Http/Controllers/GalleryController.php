<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Gallery;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display all galleries.
     * Uses CASCADE DOWN - shows content from current unit + child units
     * 
     * Universitas: shows all galleries (univ + fakultas + prodi)
     * Fakultas: shows fakultas's + prodi's galleries
     * Prodi: shows prodi's galleries only
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $type = $request->query('type');
        $year = $request->query('tahun');

        $query = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->ordered();

        if ($type && in_array($type, ['image', 'video'])) {
            $query->where('type', $type);
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $galleries = $query->paginate(12);

        // Get years for filters (compatible with MySQL and SQLite)
        $yearFunction = config('database.default') === 'sqlite' 
            ? "strftime('%Y', created_at)" 
            : 'YEAR(created_at)';
            
        $years = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->selectRaw("{$yearFunction} as year")
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('gallery.index', compact('galleries', 'years', 'type', 'year', 'unitType', 'unitId'));
    }

    /**
     * Display photos only.
     */
    public function photos(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        $galleries = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->images()
            ->ordered()
            ->paginate(24);

        return view('gallery.photos', compact('galleries', 'unitType', 'unitId'));
    }

    /**
     * Display videos only.
     */
    public function videos(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        $galleries = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->videos()
            ->ordered()
            ->paginate(12);

        return view('gallery.videos', compact('galleries', 'unitType', 'unitId'));
    }

    /**
     * Display a single gallery album.
     */
    public function show(Request $request, Gallery $gallery): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify gallery is accessible via cascade DOWN
        $isAccessible = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->where('id', $gallery->id)
            ->active()
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        // Get related galleries (cascade DOWN)
        $relatedGalleries = Gallery::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->where('id', '!=', $gallery->id)
            ->when($gallery->category, function ($q) use ($gallery) {
                $q->where('category', $gallery->category);
            })
            ->ordered()
            ->take(4)
            ->get();

        return view('gallery.show', compact('gallery', 'relatedGalleries', 'unitType', 'unitId'));
    }
}
