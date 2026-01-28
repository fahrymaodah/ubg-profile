@php
    $configService = app(\App\Services\AppConfigService::class);
    $status = $configService->getStatus();
    
    $showInvalid = $status['enabled'] && !$status['valid'];
    $showWarning = false;
    
    if ($status['enabled'] && $status['valid']) {
        $fakultasRemaining = $status['usage']['fakultas']['remaining'] ?? 0;
        $prodiRemaining = $status['usage']['prodi']['remaining'] ?? 0;
        $showWarning = $fakultasRemaining <= 0 || $prodiRemaining <= 0;
    }
@endphp

@if($showInvalid)
<div class="fi-banner bg-danger-600 px-4 py-3 text-center text-sm font-medium text-white">
    <div class="flex items-center justify-center gap-2">
        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
        <span>
            UNLICENSED - {{ $status['error'] ?? 'Lisensi tidak valid' }}. 
            Hubungi: <a href="mailto:{{ $status['developer']['email'] }}" class="underline">{{ $status['developer']['email'] }}</a>
        </span>
    </div>
</div>
@elseif($showWarning)
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
