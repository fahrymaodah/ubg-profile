@extends('layouts.app')

@section('title', 'Agenda - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Agenda']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Agenda Kegiatan</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Jadwal dan agenda kegiatan kampus
        </p>
    </div>

    {{-- Filters --}}
    @php
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp
    <x-filter-panel 
        :action="route('event.index')" 
        :reset-url="route('event.index')"
        :has-active-filters="request()->hasAny(['q', 'status', 'bulan'])"
        :show-search="true"
        search-placeholder="Cari agenda..."
        search-name="q"
        :search-value="$q ?? ''">
        
        <x-filter-select 
            name="status" 
            label="Status"
            :selected="$status ?? ''"
            placeholder="Semua Status"
            :options="[
                'upcoming' => 'Akan Datang',
                'ongoing' => 'Berlangsung',
                'past' => 'Selesai',
            ]"
        />
        
        <x-filter-select 
            name="bulan" 
            label="Bulan"
            :selected="$bulan ?? ''"
            placeholder="Semua Bulan"
            :options="$months"
        />
    </x-filter-panel>

    {{-- View Toggle & Results --}}
    <div class="flex items-center justify-between mb-6">
        <span class="text-gray-600">{{ $events->total() }} agenda ditemukan</span>
        <div class="flex space-x-2">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" 
               class="p-2 rounded-lg transition {{ request('view', 'grid') == 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
               class="p-2 rounded-lg transition {{ request('view') == 'list' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Events --}}
    @if($events->count() > 0)
        @if(request('view') == 'list')
        {{-- List View --}}
        <div class="space-y-4">
            @foreach($events as $event)
            @php
                $now = now();
                $isUpcoming = $event->start_date > $now;
                $isOngoing = $event->start_date <= $now && $event->end_date >= $now;
                $isPast = $event->end_date < $now;
                
                // Determine unit label for cascade UP
                $showUnitLabel = false;
                $eventUnitLabel = '';
                if ($unitType === \App\Enums\UnitType::PRODI) {
                    if ($event->unit_type === \App\Enums\UnitType::UNIVERSITAS) {
                        $showUnitLabel = true;
                        $eventUnitLabel = 'Universitas';
                    } elseif ($event->unit_type === \App\Enums\UnitType::FAKULTAS) {
                        $showUnitLabel = true;
                        $eventUnitLabel = $event->unit_source_label;
                    }
                } elseif ($unitType === \App\Enums\UnitType::FAKULTAS && $event->unit_type === \App\Enums\UnitType::UNIVERSITAS) {
                    $showUnitLabel = true;
                    $eventUnitLabel = 'Universitas';
                }
            @endphp
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('event.show', $event->id) }}" class="flex flex-col md:flex-row">
                    {{-- Date Box --}}
                    <div class="md:w-32 p-4 bg-gradient-to-br from-blue-600 to-blue-700 text-white text-center flex flex-col justify-center">
                        <span class="text-3xl font-bold">{{ $event->start_date->format('d') }}</span>
                        <span class="text-sm uppercase">{{ $event->start_date->format('M') }}</span>
                        <span class="text-xs opacity-75">{{ $event->start_date->format('Y') }}</span>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                @if($showUnitLabel)
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded mb-2">
                                    {{ $eventUnitLabel }}
                                </span>
                                @endif
                                <h3 class="text-lg font-bold text-gray-900 hover:text-blue-600 transition mb-2">
                                    {{ $event->title }}
                                </h3>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $event->start_date->format('H:i') }}
                                    </span>
                                    @if($event->location)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        {{ $event->location }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Status Badge --}}
                            @if($isUpcoming)
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                Akan Datang
                            </span>
                            @elseif($isOngoing)
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full animate-pulse">
                                Berlangsung
                            </span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                Selesai
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        {{-- Grid View --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
            <x-event-card :event="$event" :currentUnitType="$unitType" :currentUnitId="$unitId" />
            @endforeach
        </div>
        @endif

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $events->withQueryString()->links() }}
        </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Agenda</h3>
        <p class="text-gray-500">Agenda kegiatan akan segera ditampilkan.</p>
    </div>
    @endif
</div>
@endsection
