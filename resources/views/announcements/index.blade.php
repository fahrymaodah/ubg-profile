@extends('layouts.app')

@section('title', 'Pengumuman - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Pengumuman']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Pengumuman</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Informasi dan pengumuman terbaru dari kampus
        </p>
    </div>

    {{-- Filters --}}
    <x-filter-panel 
        :action="route('announcement.index')" 
        :reset-url="route('announcement.index')"
        :has-active-filters="(bool)$priority">
        
        <x-filter-select 
            name="priority" 
            label="Prioritas"
            :selected="$priority ?? ''"
            placeholder="Semua Prioritas"
            :options="[
                'urgent' => 'Urgent',
                'high' => 'Tinggi',
                'normal' => 'Normal',
                'low' => 'Rendah',
            ]"
        />
    </x-filter-panel>

    {{-- Announcements List --}}
    @if($announcements->count() > 0)
    <div class="space-y-4">
        @foreach($announcements as $announcement)
        <a href="{{ route('announcement.show', $announcement) }}" 
           class="block bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    {{-- Priority Badge --}}
                    <div class="flex-shrink-0">
                        @php
                            $priorityConfig = [
                                'urgent' => ['icon' => '🔴', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Urgent'],
                                'high' => ['icon' => '🟠', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Tinggi'],
                                'normal' => ['icon' => '🟢', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Normal'],
                                'low' => ['icon' => '⚪', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Rendah'],
                            ];
                            $config = $priorityConfig[$announcement->priority] ?? $priorityConfig['normal'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                            {{ $config['icon'] }} {{ $config['label'] }}
                        </span>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                            {{ $announcement->title }}
                        </h2>
                        @if($announcement->content)
                        <p class="mt-2 text-gray-600 line-clamp-2">
                            {!! strip_tags(Str::limit($announcement->content, 200)) !!}
                        </p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $announcement->created_at->format('d M Y') }}
                            </span>
                            @if($announcement->end_date)
                            <span class="flex items-center gap-1 text-orange-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Berlaku hingga {{ $announcement->end_date->format('d M Y') }}
                            </span>
                            @endif
                            {{-- Unit Source Label --}}
                            @if(isset($unitType) && $unitType !== \App\Enums\UnitType::UNIVERSITAS)
                                @php
                                    $sourceLabel = match($announcement->unit_type) {
                                        \App\Enums\UnitType::UNIVERSITAS => 'Universitas',
                                        \App\Enums\UnitType::FAKULTAS => $announcement->unit_instance?->nama ?? 'Fakultas',
                                        \App\Enums\UnitType::PRODI => $announcement->unit_instance?->nama ?? 'Program Studi',
                                        default => null,
                                    };
                                    $isFromParent = $announcement->unit_type !== $unitType || $announcement->unit_id !== $unitId;
                                @endphp
                                @if($sourceLabel && $isFromParent)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $sourceLabel }}
                                </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <div class="flex-shrink-0 self-center">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $announcements->withQueryString()->links() }}
    </div>
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengumuman</h3>
        <p class="text-gray-500">Pengumuman terbaru akan muncul di sini</p>
    </div>
    @endif
</div>
@endsection
