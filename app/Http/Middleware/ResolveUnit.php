<?php

namespace App\Http\Middleware;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Services\UnitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveUnit
{
    public function __construct(
        protected UnitService $unitService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Development mode: Allow unit override via query parameter
        if (app()->environment('local') && $request->has('_unit')) {
            $unitParam = $request->query('_unit');
            $result = $this->unitService->getUnitBySubdomain($unitParam);
            
            if ($result['unit']) {
                $this->setUnitContext($request, $result['type'], $result['unit']->id, $result['unit']);
                return $next($request);
            }
        }
        
        $host = $request->getHost();
        $mainDomain = config('app.domain', 'ubg.ac.id');
        
        // Remove www if present
        $host = preg_replace('/^www\./', '', $host);
        
        // Check if this is a subdomain
        if ($host === $mainDomain || $host === 'localhost' || str_starts_with($host, '127.0.0.1')) {
            // Main domain - universitas level
            $this->setUnitContext($request, UnitType::UNIVERSITAS, null, null);
        } elseif (str_ends_with($host, '.' . $mainDomain)) {
            // Subdomain
            $subdomain = str_replace('.' . $mainDomain, '', $host);
            
            // Skip admin subdomain
            if ($subdomain === 'admin') {
                return $next($request);
            }
            
            $result = $this->unitService->getUnitBySubdomain($subdomain);
            
            if ($result['unit']) {
                $this->setUnitContext($request, $result['type'], $result['unit']->id, $result['unit']);
            } else {
                // Unknown subdomain - return 404
                abort(404, 'Unit tidak ditemukan');
            }
        } else {
            // Custom domain - try to match
            $unit = $this->findUnitByCustomDomain($host);
            
            if ($unit) {
                $type = $unit instanceof Fakultas ? UnitType::FAKULTAS : UnitType::PRODI;
                $this->setUnitContext($request, $type, $unit->id, $unit);
            } else {
                // Default to universitas for development
                $this->setUnitContext($request, UnitType::UNIVERSITAS, null, null);
            }
        }

        return $next($request);
    }

    /**
     * Set unit context for the request
     */
    protected function setUnitContext(Request $request, UnitType $unitType, ?int $unitId, $unit): void
    {
        // Store in request for controllers
        $request->attributes->set('unit_type', $unitType);
        $request->attributes->set('unit_id', $unitId);
        $request->attributes->set('unit', $unit);
        
        // Share with views
        view()->share('currentUnitType', $unitType);
        view()->share('currentUnitId', $unitId);
        view()->share('currentUnit', $unit);
        
        // Also available via helper
        app()->instance('current_unit_type', $unitType);
        app()->instance('current_unit_id', $unitId);
        app()->instance('current_unit', $unit);
    }

    /**
     * Find unit by custom domain (if using custom domains feature)
     */
    protected function findUnitByCustomDomain(string $domain): Fakultas|Prodi|null
    {
        // Check fakultas
        $fakultas = Fakultas::where('website', 'like', '%' . $domain . '%')->first();
        if ($fakultas) {
            return $fakultas;
        }

        // Check prodi
        $prodi = Prodi::where('website', 'like', '%' . $domain . '%')->first();
        if ($prodi) {
            return $prodi;
        }

        return null;
    }
}
