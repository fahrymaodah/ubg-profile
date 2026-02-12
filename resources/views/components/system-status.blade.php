@php
    $showInvalid = false;
    $showWarning = false;
    $status = null;
    
    try {
        $configService = app(\App\Services\AppConfigService::class);
        
        // Skip all processing if license system is disabled
        if (!$configService->isEnabled()) {
            // Early exit - don't even call getStatus() which queries the database
            $status = null;
        } else {
            $status = $configService->getStatus();
            
            $showInvalid = $status['enabled'] && !$status['valid'];
            
            if ($status['enabled'] && $status['valid']) {
                $fakultasRemaining = $status['usage']['fakultas']['remaining'] ?? 0;
                $prodiRemaining = $status['usage']['prodi']['remaining'] ?? 0;
                $showWarning = $fakultasRemaining <= 0 || $prodiRemaining <= 0;
            }
        }
    } catch (\Throwable $e) {
        // Fail silently - don't break the page if license check fails
        \Log::warning('System status component error: ' . $e->getMessage());
        $showInvalid = false;
        $showWarning = false;
    }
@endphp

@if($status && ($showInvalid || $showWarning))
<div id="system-notice" class="fixed top-0 left-0 right-0 z-[9999] {{ $showInvalid ? 'bg-red-600' : 'bg-amber-500' }} text-white text-center py-2 px-4 text-sm font-medium shadow-lg">
    <div class="flex items-center justify-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>
            @if($showInvalid)
                UNLICENSED - {{ $status['error'] ?? 'Lisensi tidak valid' }}. 
            @else
                Batas lisensi tercapai - 
                Fakultas: {{ $status['usage']['fakultas']['current'] }}/{{ $status['usage']['fakultas']['max'] }}, 
                Prodi: {{ $status['usage']['prodi']['current'] }}/{{ $status['usage']['prodi']['max'] }}. 
            @endif
            Hubungi: {{ $status['developer']['email'] }}
        </span>
    </div>
</div>
<div class="h-9"></div>
@endif
