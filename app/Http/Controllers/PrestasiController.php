<?php

namespace App\Http\Controllers;

use App\Enums\PrestasiKategori;
use App\Enums\PrestasiTingkat;
use App\Enums\UnitType;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrestasiController extends Controller
{
    /**
     * Display a listing of prestasi.
     * Shows content from current unit + child units (cascade down)
     * 
     * Prodi: only prodi's achievements
     * Fakultas: fakultas + prodi under it
     * Universitas: all achievements
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $unit = $request->attributes->get('unit');
        
        $tingkat = $request->query('tingkat');
        $kategori = $request->query('kategori');
        $tahun = $request->query('tahun');

        // Use cascade down for prestasi
        $query = Prestasi::forUnitCascadeDown($unitType, $unitId)->active()->latest();

        if ($tingkat) {
            $query->byTingkat(PrestasiTingkat::from($tingkat));
        }

        if ($kategori) {
            $query->byKategori(PrestasiKategori::from($kategori));
        }

        if ($tahun) {
            $query->byYear((int) $tahun);
        }

        $prestasi = $query->paginate(12);

        // Get available years for filter (compatible with MySQL and SQLite)
        $yearFunction = config('database.default') === 'sqlite' 
            ? "strftime('%Y', tanggal)" 
            : 'YEAR(tanggal)';
            
        $years = Prestasi::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->selectRaw("{$yearFunction} as year")
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('prestasi.index', compact(
            'prestasi',
            'tingkat',
            'kategori',
            'tahun',
            'years',
            'unitType',
            'unitId'
        ));
    }

    /**
     * Display the specified prestasi.
     */
    public function show(Request $request, Prestasi $prestasi): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify prestasi is accessible in cascade down scope
        $isAccessible = Prestasi::forUnitCascadeDown($unitType, $unitId)
            ->where('id', $prestasi->id)
            ->active()
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        // Get related prestasi from cascade scope
        $relatedPrestasi = Prestasi::forUnitCascadeDown($unitType, $unitId)
            ->active()
            ->where('id', '!=', $prestasi->id)
            ->byKategori($prestasi->kategori)
            ->latest()
            ->take(4)
            ->get();

        return view('prestasi.show', compact('prestasi', 'relatedPrestasi'));
    }
}
