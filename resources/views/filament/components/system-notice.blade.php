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
        // Fail silently - don't break the admin panel if license check fails
        \Log::warning('System notice component error: ' . $e->getMessage());
        $showInvalid = false;
        $showWarning = false;
    }
@endphp

@if($status && $showInvalid)
<div class="fi-banner bg-danger-600 px-4 py-3 text-center text-sm font-medium text-white">
    <div class="flex items-center justify-center gap-2">
        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
        <span>
            UNLICENSED - {{ $status['error'] ?? 'Lisensi tidak valid' }}. 
            Hubungi: <a href="mailto:{{ $status['developer']['email'] }}" class="underline">{{ $status['developer']['email'] }}</a>
        </span>
    </div>
</div>
@elseif($status && $showWarning)
<div class="fi-banner bg-warning-500 px-4 py-3 text-center text-sm font-medium text-white">
    <div class="flex items-center justify-center gap-2">
        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
        <span>
            Batas lisensi tercapai - 
            Fakultas: {{ $status['usage']['fakultas']['current'] }}/{{ $status['usage']['fakultas']['max'] }}, 
            Prodi: {{ $status['usage']['prodi']['current'] }}/{{ $status['usage']['prodi']['max'] }}. 
            Hubungi: <a href="mailto:{{ $status['developer']['email'] }}" class="underline">{{ $status['developer']['email'] }}</a>
        </span>
    </div>
</div>
@endif
