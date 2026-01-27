<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Announcement;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     * Shows content from current unit + parent units (cascade up)
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $unit = $request->attributes->get('unit');
        $priority = $request->query('priority');

        // Use cascade up: prodi shows prodi+fakultas+universitas, fakultas shows fakultas+universitas
        $query = Announcement::forUnitCascadeUp($unitType, $unitId)->visible();

        if ($priority && in_array($priority, ['urgent', 'high', 'normal', 'low'])) {
            $query->byPriority($priority);
        }

        $announcements = $query->ordered()->paginate(12);

        // Get parent fakultas info for prodi level (for labels)
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unit) {
            $fakultas = $unit->fakultas;
        }

        return view('announcements.index', compact('announcements', 'priority', 'unitType', 'unitId', 'fakultas'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Request $request, Announcement $announcement): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Verify announcement is visible in cascade up scope
        $isAccessible = Announcement::forUnitCascadeUp($unitType, $unitId)
            ->where('id', $announcement->id)
            ->visible()
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        // Get related announcements from cascade scope
        $relatedAnnouncements = Announcement::forUnitCascadeUp($unitType, $unitId)
            ->visible()
            ->where('id', '!=', $announcement->id)
            ->ordered()
            ->take(5)
            ->get();

        return view('announcements.show', compact('announcement', 'relatedAnnouncements'));
    }
}
