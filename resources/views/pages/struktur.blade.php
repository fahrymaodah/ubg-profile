@extends('layouts.app')

@section('title', 'Struktur Organisasi - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<div class="bg-white">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Profil'], ['label' => 'Struktur Organisasi']]" :wrapper="false" />
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Struktur Organisasi</h1>
            <p class="text-xl text-indigo-200">{{ $settings['site_name'] ?? config('app.name') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- Struktur Image jika ada (prioritas utama) --}}
        @if(!empty($profil['struktur_image']))
        <div class="mb-12 text-center">
            <img src="{{ Storage::url($profil['struktur_image']) }}" 
                 alt="Struktur Organisasi" 
                 class="max-w-full h-auto mx-auto rounded-xl shadow-lg">
        </div>
        @else
            @php
                $struktur = $profil['struktur'] ?? [];
                $pejabat = $struktur['pejabat'] ?? [];
                $pendukung = $struktur['pendukung'] ?? [];
                $hasPejabat = !empty($pejabat);
                
                // Group pejabat by row
                $rows = [];
                foreach ($pejabat as $p) {
                    $row = (int) ($p['row'] ?? 1);
                    $col = (int) ($p['column'] ?? 1);
                    $rows[$row][$col] = $p;
                }
                ksort($rows);
                
                // Size configurations
                $sizeConfig = [
                    'xl' => ['card' => 'w-80 p-6', 'photo' => 'w-24 h-24', 'hasPhoto' => true, 'title' => 'text-xl', 'name' => 'text-base', 'gradient' => true],
                    'lg' => ['card' => 'w-72 p-5', 'photo' => 'w-20 h-20', 'hasPhoto' => true, 'title' => 'text-lg', 'name' => 'text-sm', 'gradient' => true],
                    'md' => ['card' => 'w-64 p-4', 'photo' => 'w-16 h-16', 'hasPhoto' => true, 'title' => 'text-base', 'name' => 'text-sm', 'gradient' => false],
                    'sm' => ['card' => 'w-56 p-4', 'photo' => 'w-14 h-14', 'hasPhoto' => false, 'title' => 'text-sm', 'name' => 'text-xs', 'gradient' => false],
                    'xs' => ['card' => 'w-48 p-3', 'photo' => 'w-12 h-12', 'hasPhoto' => false, 'title' => 'text-xs', 'name' => 'text-xs', 'gradient' => false],
                ];
                
                // Heroicons SVG paths
                $heroicons = [
                    'academic-cap' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
                    'book-open' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'building-library' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
                    'building-office' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
                    'computer-desktop' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
                    'currency-dollar' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'globe-alt' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418',
                    'shield-check' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                    'user-group' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z',
                    'cog' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z',
                ];
            @endphp

            @if($hasPejabat)
            {{-- Dynamic Grid-Based Struktur --}}
            <div class="mb-12">
                @foreach($rows as $rowNum => $cols)
                    @php ksort($cols); @endphp
                    
                    {{-- Connector between rows --}}
                    @if($rowNum > 1)
                    <div class="flex justify-center mb-6">
                        <div class="w-0.5 h-8 bg-indigo-300"></div>
                    </div>
                    @endif
                    
                    <div class="flex justify-center gap-6 mb-6 flex-wrap">
                        @foreach($cols as $colNum => $pejabat)
                            @php
                                $size = $pejabat['size'] ?? 'md';
                                $config = $sizeConfig[$size] ?? $sizeConfig['md'];
                                $hasImage = $config['hasPhoto'] && !empty($pejabat['image']);
                            @endphp
                            
                            @if($config['gradient'])
                            {{-- Gradient card for xl, lg --}}
                            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-xl {{ $config['card'] }} text-center shadow-xl">
                                @if($hasImage)
                                <img src="{{ Storage::url($pejabat['image']) }}" 
                                     alt="{{ $pejabat['nama'] ?? '' }}"
                                     class="{{ $config['photo'] }} rounded-full object-cover mx-auto mb-4 border-4 border-white/30">
                                @else
                                <div class="{{ $config['photo'] }} bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-1/2 h-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                @endif
                                <h3 class="{{ $config['title'] }} font-bold">{{ $pejabat['jabatan'] ?? '-' }}</h3>
                                @if(!empty($pejabat['nama']))
                                <p class="text-indigo-200 {{ $config['name'] }} mt-1">{{ $pejabat['nama'] }}</p>
                                @endif
                            </div>
                            @else
                            {{-- Normal card for md, sm, xs --}}
                            <div class="bg-white rounded-xl {{ $config['card'] }} text-center shadow-lg border-2 border-indigo-200 hover:border-indigo-400 transition">
                                @if($hasImage)
                                <img src="{{ Storage::url($pejabat['image']) }}" 
                                     alt="{{ $pejabat['nama'] ?? '' }}"
                                     class="{{ $config['photo'] }} rounded-full object-cover mx-auto mb-3 border-2 border-indigo-100">
                                @else
                                <div class="{{ $config['photo'] }} bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-1/2 h-1/2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                @endif
                                <h4 class="font-bold text-gray-900 {{ $config['title'] }}">{{ $pejabat['jabatan'] ?? '-' }}</h4>
                                @if(!empty($pejabat['nama']))
                                <p class="text-indigo-600 {{ $config['name'] }} mt-1 font-medium">{{ $pejabat['nama'] }}</p>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Unit Pendukung --}}
            @if(!empty($pendukung))
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Unit Pendukung</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($pendukung as $unit)
                        @php
                            $color = $unit['color'] ?? '#6366F1';
                            $icon = $unit['icon'] ?? 'building-office';
                            $iconPath = $heroicons[$icon] ?? $heroicons['building-office'];
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-4 text-center hover:shadow-md border border-transparent hover:border-gray-200 transition">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2" 
                                 style="background-color: {{ $color }}20;">
                                <svg class="w-5 h-5" style="color: {{ $color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                                </svg>
                            </div>
                            <h5 class="text-xs font-semibold text-gray-700">{{ $unit['nama'] ?? '-' }}</h5>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Default fallback jika tidak ada data sama sekali --}}
            @if(!$hasPejabat && empty($pendukung))
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Struktur Organisasi</h3>
                <p class="text-gray-500">Data struktur organisasi belum tersedia.</p>
            </div>
            @endif
        @endif

        {{-- Download Link - hanya tampil jika ada file struktur_image --}}
        @if(!empty($profil['struktur_image']))
        <div class="mt-12 text-center">
            <a href="{{ Storage::url($profil['struktur_image']) }}" download class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Struktur Organisasi
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
