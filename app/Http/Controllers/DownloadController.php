<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Display a listing of downloads.
     * Shows content from current unit + parent units (cascade up)
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $unit = $request->attributes->get('unit');
        $category = $request->query('kategori');

        // Use cascade up: prodi shows prodi+fakultas+universitas, fakultas shows fakultas+universitas
        $query = Download::forUnitCascadeUp($unitType, $unitId)->active()->ordered();

        if ($category) {
            $query->byCategory($category);
        }

        $downloads = $query->paginate(20);

        // Get categories from cascade scope
        $categories = Download::forUnitCascadeUp($unitType, $unitId)
            ->active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        // Get parent fakultas info for prodi level (for labels)
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unit) {
            $fakultas = $unit->fakultas;
        }

        return view('download.index', compact('downloads', 'categories', 'category', 'unitType', 'unitId', 'fakultas'));
    }

    /**
     * Download a file.
     */
    public function download(Request $request, Download $download): StreamedResponse
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify download is accessible in cascade scope
        $isAccessible = Download::forUnitCascadeUp($unitType, $unitId)
            ->where('id', $download->id)
            ->active()
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        $download->incrementDownloadCount();

        return Storage::download($download->file, $download->title . '.' . $download->extension);
    }
}
