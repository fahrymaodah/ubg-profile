<?php

namespace App\Observers;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Services\AppConfigService;
use App\Services\UnitBootstrapService;

class FakultasObserver
{
    public function __construct(
        protected UnitBootstrapService $bootstrapService,
        protected AppConfigService $configService
    ) {}

    /**
     * Handle the Fakultas "creating" event.
     * Prevent creation if license limit reached.
     */
    public function creating(Fakultas $fakultas): bool
    {
        if (!$this->configService->canCreateFakultas()) {
            $status = $this->configService->getStatus();
            $max = $status['usage']['fakultas']['max'];
            $dev = $status['developer']['email'];
            
            throw new \Exception(
                "Batas lisensi tercapai. Maksimal {$max} fakultas. " .
                "Hubungi developer ({$dev}) untuk upgrade lisensi."
            );
        }
        
        return true;
    }

    /**
     * Handle the Fakultas "created" event.
     */
    public function created(Fakultas $fakultas): void
    {
        $this->bootstrapService->bootstrapUnit(UnitType::FAKULTAS, $fakultas->id);
    }

    /**
     * Handle the Fakultas "deleted" event.
     */
    public function deleted(Fakultas $fakultas): void
    {
        $this->bootstrapService->cleanupUnit(UnitType::FAKULTAS, $fakultas->id);
    }
}
