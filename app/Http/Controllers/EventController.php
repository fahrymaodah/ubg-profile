<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\Event;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     * Shows content from current unit + parent units (cascade up)
     * 
     * Prodi: shows prodi's + fakultas's + universitas's events
     * Fakultas: shows fakultas's + universitas's events  
     * Universitas: shows universitas's events only
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $status = $request->query('status');
        $bulan = $request->query('bulan');
        $q = $request->query('q');

        // Use cascade UP for events (like announcements)
        $query = Event::forUnitCascadeUp($unitType, $unitId)->active();

        // Search
        if ($q) {
            $query->where(function($qr) use ($q) {
                $qr->where('title', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%")
                   ->orWhere('location', 'like', "%{$q}%");
            });
        }

        // Status filter
        if ($status === 'upcoming') {
            $query->upcoming();
        } elseif ($status === 'ongoing') {
            $query->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        } elseif ($status === 'past') {
            $query->past();
        }

        // Month filter
        if ($bulan) {
            $query->whereMonth('start_date', $bulan);
        }

        $events = $query->orderByDesc('start_date')->paginate(12);

        // Get fakultas for label display (if prodi level)
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = Prodi::find($unitId);
            $fakultas = $prodi?->fakultas;
        }

        return view('events.index', compact('events', 'status', 'bulan', 'q', 'unitType', 'unitId', 'fakultas'));
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, Event $event): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Check if event is accessible (cascade UP logic)
        $isAccessible = false;
        
        if ($event->is_active) {
            // Universitas content is always accessible
            if ($event->unit_type === UnitType::UNIVERSITAS) {
                $isAccessible = true;
            }
            // Same unit
            elseif ($event->belongsToUnit($unitType, $unitId)) {
                $isAccessible = true;
            }
            // Fakultas content accessible from prodi under that fakultas
            elseif ($unitType === UnitType::PRODI && $event->unit_type === UnitType::FAKULTAS) {
                $prodi = Prodi::find($unitId);
                if ($prodi && $prodi->fakultas_id === $event->unit_id) {
                    $isAccessible = true;
                }
            }
        }

        if (!$isAccessible) {
            abort(404);
        }

        // Get related upcoming events
        $relatedEvents = Event::forUnitCascadeUp($unitType, $unitId)
            ->active()
            ->upcoming()
            ->where('id', '!=', $event->id)
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }
}
