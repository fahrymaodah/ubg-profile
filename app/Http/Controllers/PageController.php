<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show(Request $request, Page $page): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify page belongs to this unit
        if (!$page->belongsToUnit($unitType, $unitId) || !$page->is_active) {
            abort(404);
        }

        $template = $page->template ?? 'default';

        return view("pages.{$template}", compact('page'));
    }

    /**
     * Display visi & misi page.
     */
    public function visiMisi(Request $request): View
    {
        $unit = $request->attributes->get('unit');
        
        return view('pages.visi-misi', compact('unit'));
    }

    /**
     * Display sejarah page.
     */
    public function sejarah(Request $request): View
    {
        $unit = $request->attributes->get('unit');
        
        return view('pages.sejarah', compact('unit'));
    }

    /**
     * Display struktur organisasi page.
     */
    public function struktur(Request $request): View
    {
        $unit = $request->attributes->get('unit');
        
        return view('pages.struktur', compact('unit'));
    }
}
