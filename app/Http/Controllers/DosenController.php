<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenController extends Controller
{
    /**
     * Display a listing of dosen.
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $prodiId = $request->query('prodi');
        $search = $request->query('search');

        $query = Dosen::where('is_active', true)
            ->with('prodi.fakultas')
            ->orderBy('order')
            ->orderBy('nama');

        // Filter by unit type
        if ($unitType === UnitType::PRODI && $unitId) {
            $query->where('prodi_id', $unitId);
        } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
            $query->whereHas('prodi', function ($q) use ($unitId) {
                $q->where('fakultas_id', $unitId);
            });
        }

        // Filter by prodi
        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%")
                  ->orWhere('bidang_keahlian', 'like', "%{$search}%")
                  ->orWhere('jabatan_fungsional', 'like', "%{$search}%");
            });
        }

        $dosen = $query->paginate(12);

        // Get available prodis for filter
        $prodisQuery = Prodi::where('is_active', true)->orderBy('nama');
        
        if ($unitType === UnitType::FAKULTAS && $unitId) {
            $prodisQuery->where('fakultas_id', $unitId);
        }
        
        $prodis = $prodisQuery->get();

        return view('dosen.index', compact('dosen', 'prodis', 'prodiId', 'search'));
    }

    /**
     * Display the specified dosen.
     */
    public function show(Request $request, Dosen $dosen): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify dosen belongs to this unit
        if (!$dosen->is_active) {
            abort(404);
        }

        // Additional verification based on unit type
        if ($unitType === UnitType::PRODI && $unitId && $dosen->prodi_id !== $unitId) {
            abort(404);
        }

        if ($unitType === UnitType::FAKULTAS && $unitId && $dosen->prodi?->fakultas_id !== $unitId) {
            abort(404);
        }

        $dosen->load('prodi.fakultas');

        // Get related dosen from same prodi
        $relatedDosen = Dosen::where('is_active', true)
            ->where('id', '!=', $dosen->id)
            ->where('prodi_id', $dosen->prodi_id)
            ->orderBy('order')
            ->take(4)
            ->get();

        return view('dosen.show', compact('dosen', 'relatedDosen'));
    }
}
