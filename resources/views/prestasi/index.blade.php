@extends('layouts.app')

@section('title', 'Prestasi - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Prestasi']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Prestasi</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Pencapaian dan prestasi yang diraih oleh civitas akademika
        </p>
    </div>

    {{-- Filters --}}
    <x-filter-panel 
        :action="route('prestasi.index')" 
        :reset-url="route('prestasi.index')"
        :has-active-filters="request()->hasAny(['q', 'tingkat', 'kategori'])"
        :show-search="true"
        search-placeholder="Cari prestasi..."
        search-name="q"
        :search-value="request('q') ?? ''">
        
        <x-filter-select 
            name="tingkat" 
            label="Tingkat"
            :selected="request('tingkat') ?? ''"
            placeholder="Semua Tingkat"
            :options="[
                'universitas' => 'Universitas',
                'regional' => 'Regional',
                'nasional' => 'Nasional',
                'internasional' => 'Internasional',
            ]"
        />
        
        <x-filter-select 
            name="kategori" 
            label="Kategori"
            :selected="request('kategori') ?? ''"
            placeholder="Semua Kategori"
            :options="[
                'akademik' => 'Akademik',
                'non_akademik' => 'Non-Akademik',
                'olahraga' => 'Olahraga',
                'seni' => 'Seni',
                'penelitian' => 'Penelitian',
                'pengabdian' => 'Pengabdian',
            ]"
        />
    </x-filter-panel>

    {{-- Results Info --}}
    <div class="flex items-center justify-between mb-6">
        <span class="text-gray-600">{{ $prestasi->total() }} prestasi ditemukan</span>
    </div>

    {{-- Prestasi Grid --}}
    @if($prestasi->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($prestasi as $item)
        <x-prestasi-card :prestasi="$item" :currentUnitType="$unitType" :currentUnitId="$unitId" />
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $prestasi->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Prestasi</h3>
        <p class="text-gray-500">Prestasi akan segera ditampilkan.</p>
    </div>
    @endif
</div>
@endsection
