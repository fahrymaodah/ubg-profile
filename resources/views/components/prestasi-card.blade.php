@props(['prestasi', 'currentUnitType' => null, 'currentUnitId' => null])

@php
    use App\Enums\UnitType;
    
    // Determine if we should show unit source label
    // For cascade DOWN: show label when content is from a child unit
    $showUnitLabel = false;
    $unitLabel = '';
    
    if ($currentUnitType && $prestasi->unit_type) {
        // Viewing from Universitas: show label for fakultas and prodi items
        if ($currentUnitType === UnitType::UNIVERSITAS) {
            if ($prestasi->unit_type !== UnitType::UNIVERSITAS) {
                $showUnitLabel = true;
                $unitLabel = $prestasi->unit_source_label;
            }
        }
        // Viewing from Fakultas: show label for prodi items
        elseif ($currentUnitType === UnitType::FAKULTAS) {
            if ($prestasi->unit_type === UnitType::PRODI) {
                $showUnitLabel = true;
                $unitLabel = $prestasi->unit_source_label;
            }
        }
        // Viewing from Prodi: no label needed (it's their own content)
    }
@endphp

<article class="group">
    <a href="{{ route('prestasi.show', $prestasi->id) }}" 
       class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden h-full">
        {{-- Image --}}
        <div class="relative h-48 overflow-hidden">
            @if($prestasi->foto)
            <img src="{{ Storage::url($prestasi->foto) }}" 
                 alt="{{ $prestasi->judul }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
            <div class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            @endif
            
            {{-- Tingkat Badge --}}
            @php
                $tingkatColors = [
                    'universitas' => 'bg-gray-500',
                    'regional' => 'bg-blue-500',
                    'nasional' => 'bg-green-500',
                    'internasional' => 'bg-purple-500',
                ];
            @endphp
            <span class="absolute top-4 left-4 px-3 py-1 {{ $tingkatColors[$prestasi->tingkat->value] ?? 'bg-gray-500' }} text-white text-xs font-semibold rounded-full">
                {{ $prestasi->tingkat->label() }}
            </span>
            
            {{-- Trophy Icon --}}
            <div class="absolute top-4 right-4 w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 3h14l-1.5 15h-11L5 3zm-3 0h2v2h1l1 10H3V3zm17 0h2v12h-3l1-10h1V3h-1z"/>
                    <path d="M8 21h8v-2H8v2z"/>
                </svg>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="p-5">
            {{-- Category --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded">
                    {{ $prestasi->kategori->label() }}
                </span>
                
                {{-- Unit Source Label --}}
                @if($showUnitLabel)
                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                    {{ $unitLabel }}
                </span>
                @endif
            </div>
            
            {{-- Title --}}
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-3 line-clamp-2">
                {{ $prestasi->judul }}
            </h3>
            
            {{-- Meta --}}
            <div class="space-y-2 text-sm text-gray-600">
                {{-- Date --}}
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $prestasi->tanggal->format('d M Y') }}
                </div>
                
                {{-- Penyelenggara --}}
                @if($prestasi->penyelenggara)
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="line-clamp-1">{{ $prestasi->penyelenggara }}</span>
                </div>
                @endif
                
                {{-- Peserta --}}
                @if($prestasi->peserta)
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="line-clamp-1">{{ $prestasi->peserta }}</span>
                </div>
                @endif
            </div>
        </div>
    </a>
</article>
