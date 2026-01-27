@extends('layouts.app')

@section('title', 'Dosen - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Dosen']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Direktori Dosen</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Tenaga pengajar profesional dan berpengalaman di bidangnya
        </p>
    </div>

    {{-- Filters --}}
    <x-filter-panel 
        :action="route('dosen.index')" 
        :reset-url="route('dosen.index')"
        :has-active-filters="request()->hasAny(['q', 'prodi', 'jabatan'])"
        :show-search="true"
        search-placeholder="Nama dosen atau NIDN..."
        search-name="q"
        :search-value="request('q') ?? ''">
        
        @if(isset($prodiList) && $prodiList->count() > 0)
        <x-filter-select 
            name="prodi" 
            label="Program Studi"
            :selected="request('prodi') ?? ''"
            placeholder="Semua Prodi"
            :options="$prodiList->pluck('nama', 'id')->toArray()"
        />
        @endif
        
        <x-filter-select 
            name="jabatan" 
            label="Jabatan"
            :selected="request('jabatan') ?? ''"
            placeholder="Semua Jabatan"
            :options="[
                'Guru Besar' => 'Guru Besar',
                'Lektor Kepala' => 'Lektor Kepala',
                'Lektor' => 'Lektor',
                'Asisten Ahli' => 'Asisten Ahli',
                'Tenaga Pengajar' => 'Tenaga Pengajar',
            ]"
        />
    </x-filter-panel>

    {{-- Results Info --}}
    <div class="flex items-center justify-between mb-6">
        <span class="text-gray-600">{{ $dosen->total() }} dosen ditemukan</span>
    </div>

    {{-- Dosen Grid --}}
    @if($dosen->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($dosen as $d)
        <x-dosen-card :dosen="$d" />
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $dosen->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Dosen Ditemukan</h3>
        <p class="text-gray-500">Coba ubah filter pencarian Anda.</p>
    </div>
    @endif
</div>
@endsection
