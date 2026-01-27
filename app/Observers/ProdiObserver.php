<?php

namespace App\Observers;

use App\Enums\UnitType;
use App\Models\Prodi;
use App\Services\UnitBootstrapService;

class ProdiObserver
{
    public function __construct(
        protected UnitBootstrapService $bootstrapService
    ) {}

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
