@props(['dosen'])

<article class="group h-full">
    <a href="{{ route('dosen.show', $dosen->nidn) }}" 
       class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden h-full flex flex-col">
        {{-- Photo --}}
        <div class="relative h-56 flex-shrink-0 overflow-hidden">
            @if($dosen->foto)
            <img src="{{ Storage::url($dosen->foto) }}" 
                 alt="{{ $dosen->nama_lengkap }}"
                 class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
            @else
            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            @endif
            
            {{-- Jabatan Badge --}}
            @if($dosen->jabatan_fungsional)
            <span class="absolute top-4 left-4 px-2.5 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                {{ $dosen->jabatan_fungsional }}
            </span>
            @endif
        </div>
        
        {{-- Content --}}
        <div class="p-4 text-center flex-1 flex flex-col">
            {{-- Name --}}
            <h3 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-1 line-clamp-2">
                {{ $dosen->nama_lengkap }}
            </h3>
            
            {{-- NIDN --}}
            <p class="text-xs text-gray-500 mb-2">NIDN: {{ $dosen->nidn }}</p>
            
            {{-- Prodi --}}
            @if($dosen->prodi)
            <p class="text-xs text-blue-600 font-medium mb-2 line-clamp-1">
                {{ $dosen->prodi->nama }}
            </p>
            @endif
            
            {{-- Keahlian --}}
            @if($dosen->bidang_keahlian)
            <div class="flex flex-wrap justify-center gap-1 mb-3">
                @php
                    $keahlianList = is_array($dosen->bidang_keahlian) 
                        ? $dosen->bidang_keahlian 
                        : explode(',', $dosen->bidang_keahlian);
                @endphp
                @foreach(array_slice($keahlianList, 0, 2) as $keahlian)
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">
                    {{ Str::limit(trim($keahlian), 15) }}
                </span>
                @endforeach
            </div>
            @endif
            
            {{-- Spacer to push social to bottom --}}
            <div class="flex-1"></div>
            
            {{-- Social Links --}}
            <div class="flex justify-center space-x-3 pt-3 border-t border-gray-100 mt-auto">
                @if($dosen->email)
                <span class="text-gray-400 hover:text-blue-600 transition"
                   onclick="event.stopPropagation(); window.location.href='mailto:{{ $dosen->email }}';">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                @endif
                @if($dosen->google_scholar_id)
                <span onclick="event.stopPropagation(); window.open('https://scholar.google.com/citations?user={{ $dosen->google_scholar_id }}', '_blank');"
                   class="text-gray-400 hover:text-blue-600 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 24a7 7 0 110-14 7 7 0 010 14zm0-24L0 9.5l4.838 3.94A8 8 0 0112 9a8 8 0 017.162 4.44L24 9.5z"/>
                    </svg>
                </span>
                @endif
                @if($dosen->sinta_id)
                <span onclick="event.stopPropagation(); window.open('https://sinta.kemdikbud.go.id/authors/detail?id={{ $dosen->sinta_id }}', '_blank');"
                   class="text-gray-400 hover:text-green-600 transition cursor-pointer">
                    <span class="text-xs font-bold">SINTA</span>
                </span>
                @endif
            </div>
        </div>
    </a>
</article>
