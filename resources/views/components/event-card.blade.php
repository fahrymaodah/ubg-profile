@props(['event', 'currentUnitType' => null, 'currentUnitId' => null])

@php
    use App\Enums\UnitType;
    
    // Determine if we should show unit source label
    // For cascade UP: show label when content is from a parent unit
    $showUnitLabel = false;
    $unitLabel = '';
    
    if ($currentUnitType && $event->unit_type) {
        // Viewing from Prodi: show label for fakultas and universitas items
        if ($currentUnitType === UnitType::PRODI) {
            if ($event->unit_type === UnitType::UNIVERSITAS) {
                $showUnitLabel = true;
                $unitLabel = 'Universitas';
            } elseif ($event->unit_type === UnitType::FAKULTAS) {
                $showUnitLabel = true;
                $unitLabel = $event->unit_source_label;
            }
        }
        // Viewing from Fakultas: show label for universitas items
        elseif ($currentUnitType === UnitType::FAKULTAS) {
            if ($event->unit_type === UnitType::UNIVERSITAS) {
                $showUnitLabel = true;
                $unitLabel = 'Universitas';
            }
        }
    }
@endphp

<article class="group">
    <a href="{{ route('event.show', $event->id) }}" 
       class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden h-full">
        {{-- Image --}}
        <div class="relative h-48 overflow-hidden">
            @if($event->image)
            <img src="{{ Storage::url($event->image) }}" 
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
            <div class="w-full h-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center">
                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif
            
            {{-- Status Badge --}}
            @php
                $isUpcoming = $event->start_date->isFuture();
                $isOngoing = $event->start_date->isPast() && ($event->end_date?->isFuture() ?? true);
                $isPast = $event->end_date?->isPast() ?? $event->start_date->isPast();
            @endphp
            <span class="absolute top-4 left-4 px-3 py-1 text-white text-xs font-semibold rounded-full
                {{ $isOngoing ? 'bg-green-500' : ($isUpcoming ? 'bg-blue-500' : 'bg-gray-500') }}">
                {{ $isOngoing ? 'Berlangsung' : ($isUpcoming ? 'Akan Datang' : 'Selesai') }}
            </span>
            
            {{-- Date Box --}}
            <div class="absolute bottom-4 right-4 bg-white rounded-lg p-2 text-center shadow-lg min-w-[60px]">
                <div class="text-2xl font-bold text-blue-600">{{ $event->start_date->format('d') }}</div>
                <div class="text-xs text-gray-600 uppercase">{{ $event->start_date->format('M') }}</div>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="p-5">
            {{-- Unit Source Label --}}
            @if($showUnitLabel)
            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded mb-2">
                {{ $unitLabel }}
            </span>
            @endif
            
            {{-- Title --}}
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-3 line-clamp-2">
                {{ $event->title }}
            </h3>
            
            {{-- Meta --}}
            <div class="space-y-2 text-sm text-gray-600">
                {{-- Date & Time --}}
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $event->start_date->format('d M Y, H:i') }}
                    @if($event->end_date)
                    - {{ $event->end_date->format('H:i') }}
                    @endif
                </div>
                
                {{-- Location --}}
                @if($event->location)
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="line-clamp-1">{{ $event->location }}</span>
                </div>
                @endif
            </div>
            
            {{-- Register Button --}}
            @if($event->registration_link && $isUpcoming)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="inline-flex items-center text-blue-600 font-medium text-sm">
                    Daftar Sekarang
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </div>
            @endif
        </div>
    </a>
</article>
