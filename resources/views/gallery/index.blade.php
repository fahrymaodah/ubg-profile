@extends('layouts.app')

@section('title', 'Galeri - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Galeri']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Galeri</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Koleksi foto dan dokumentasi kegiatan kampus
        </p>
    </div>

    {{-- Filters --}}
    <x-filter-panel 
        :action="route('gallery.index')" 
        :reset-url="route('gallery.index')"
        :has-active-filters="request()->hasAny(['q', 'type', 'tahun'])"
        :show-search="true"
        search-placeholder="Cari album galeri..."
        search-name="q"
        :search-value="request('q') ?? ''">
        
        <x-filter-select 
            name="type" 
            label="Tipe"
            :selected="request('type') ?? ''"
            placeholder="Semua Tipe"
            :options="[
                'image' => 'Foto',
                'video' => 'Video',
            ]"
        />
        
        @php
            $yearOptions = [];
            for($y = date('Y'); $y >= 2015; $y--) {
                $yearOptions[$y] = (string) $y;
            }
        @endphp
        <x-filter-select 
            name="tahun" 
            label="Tahun"
            :selected="request('tahun') ?? ''"
            placeholder="Semua Tahun"
            :options="$yearOptions"
        />
    </x-filter-panel>

    {{-- Results Info --}}
    <div class="flex items-center justify-between mb-6">
        <span class="text-gray-600">{{ $galleries->total() }} album ditemukan</span>
    </div>

    {{-- Gallery Grid --}}
    @if($galleries->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($galleries as $gallery)
        <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition-all duration-300">
            {{-- Cover Image --}}
            <div class="relative h-56 overflow-hidden">
                @if($gallery->cover || ($gallery->images && count($gallery->images) > 0))
                <img src="{{ Storage::url($gallery->cover ?? $gallery->images[0]) }}" 
                     alt="{{ $gallery->judul }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @else
                <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif

                {{-- Image Count Badge --}}
                @if($gallery->images && count($gallery->images) > 0)
                <div class="absolute top-3 right-3 px-3 py-1 bg-black/70 text-white text-sm rounded-full flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ count($gallery->images) }}
                </div>
                @endif

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <a href="{{ route('gallery.show', $gallery->id) }}" 
                       class="px-6 py-2 bg-white text-gray-900 font-semibold rounded-lg hover:bg-blue-600 hover:text-white transition">
                        Lihat Album
                    </a>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-5">
                {{-- Unit Source Label (cascade DOWN) --}}
                @if(isset($unitType) && $unitType !== \App\Enums\UnitType::PRODI)
                    @php
                        $showGalleryLabel = false;
                        $galleryLabel = '';
                        
                        // Show label for child unit content
                        if ($unitType === \App\Enums\UnitType::UNIVERSITAS && $gallery->unit_type !== \App\Enums\UnitType::UNIVERSITAS) {
                            $showGalleryLabel = true;
                            $galleryLabel = $gallery->unit_source_label;
                        } elseif ($unitType === \App\Enums\UnitType::FAKULTAS && $gallery->unit_type === \App\Enums\UnitType::PRODI) {
                            $showGalleryLabel = true;
                            $galleryLabel = $gallery->unit_source_label;
                        }
                    @endphp
                    @if($showGalleryLabel)
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded mb-2">
                        {{ $galleryLabel }}
                    </span>
                    @endif
                @endif

                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">
                    <a href="{{ route('gallery.show', $gallery->id) }}">
                        {{ $gallery->judul }}
                    </a>
                </h3>

                @if($gallery->deskripsi)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ Str::limit(strip_tags($gallery->deskripsi), 100) }}
                </p>
                @endif

                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $gallery->tanggal ? $gallery->tanggal->format('d M Y') : $gallery->created_at->format('d M Y') }}
                    </div>
                    @if($gallery->category)
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                        {{ $gallery->category->nama }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $galleries->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Galeri</h3>
        <p class="text-gray-500">Album galeri akan segera ditampilkan.</p>
    </div>
    @endif
</div>
@endsection
