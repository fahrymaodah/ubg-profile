<?php

namespace App\Observers;

use App\Enums\UnitType;
use App\Models\Fakultas;
use App\Services\UnitBootstrapService;

class FakultasObserver
{
    public function __construct(
        protected UnitBootstrapService $bootstrapService
    ) {}

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
