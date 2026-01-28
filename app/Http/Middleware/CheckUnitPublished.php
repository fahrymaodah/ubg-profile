<?php

namespace App\Http\Middleware;

use App\Enums\UnitType;
use App\Services\UnitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUnitPublished
{
    public function __construct(
        protected UnitService $unitService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $unitType = $request->attributes->get('unit_type');
        $unitId = $request->attributes->get('unit_id');
        
        // Skip if unit_type is not set (e.g., profil subdomain handled by Filament)
        if ($unitType === null) {
            return $next($request);
        }
        
        // Universitas is always published
        if ($unitType === UnitType::UNIVERSITAS) {
            return $next($request);
        }

        // Check if unit exists and is published
        if (!$this->unitService->isUnitPublished($unitType, $unitId)) {
            $message = $this->unitService->getComingSoonMessage($unitType, $unitId);
            
            return response()->view('coming-soon', [
                'message' => $message,
                'unitType' => $unitType,
                'unitId' => $unitId,
            ], 503);
        }

        return $next($request);
    }
}
