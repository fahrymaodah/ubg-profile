<?php

namespace App\Observers;

use App\Enums\UnitType;
use App\Models\Prodi;
use App\Services\AppConfigService;
use App\Services\UnitBootstrapService;

class ProdiObserver
{
    public function __construct(
        protected UnitBootstrapService $bootstrapService,
        protected AppConfigService $configService
    ) {}

    /**
     * Handle the Prodi "creating" event.
     * Prevent creation if license limit reached.
     */
    public function creating(Prodi $prodi): bool
    {
        if (!$this->configService->canCreateProdi()) {
            $status = $this->configService->getStatus();
            $max = $status['usage']['prodi']['max'];
            $dev = $status['developer']['email'];
            
            throw new \Exception(
                "Batas lisensi tercapai. Maksimal {$max} program studi. " .
                "Hubungi developer ({$dev}) untuk upgrade lisensi."
            );
        }
        
        return true;
    }

    /**
     * Handle the Prodi "created" event.
     */
    public function created(Prodi $prodi): void
    {
        $this->bootstrapService->bootstrapUnit(UnitType::PRODI, $prodi->id);
    }

    /**
     * Handle the Prodi "deleted" event.
     */
    public function deleted(Prodi $prodi): void
    {
        $this->bootstrapService->cleanupUnit(UnitType::PRODI, $prodi->id);
    }
}
