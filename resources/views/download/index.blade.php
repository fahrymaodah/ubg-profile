@extends('layouts.app')

@section('title', 'Unduhan - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Unduhan']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Pusat Unduhan</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Dokumen, formulir, dan berkas penting yang dapat diunduh
        </p>
    </div>

    {{-- Filters --}}
    <x-filter-panel 
        :action="route('download.index')" 
        :reset-url="route('download.index')"
        :has-active-filters="request()->hasAny(['q', 'kategori', 'tipe'])"
        :show-search="true"
        search-placeholder="Cari dokumen..."
        search-name="q"
        :search-value="request('q') ?? ''">
        
        @if(isset($categories) && count($categories) > 0)
        <x-filter-select 
            name="kategori" 
            label="Kategori"
            :selected="request('kategori') ?? ''"
            placeholder="Semua Kategori"
            :options="collect($categories)->mapWithKeys(fn($cat) => [$cat => $cat])->toArray()"
        />
        @endif
        
        <x-filter-select 
            name="tipe" 
            label="Tipe File"
            :selected="request('tipe') ?? ''"
            placeholder="Semua Tipe"
            :options="[
                'pdf' => 'PDF',
                'doc' => 'DOC/DOCX',
                'xls' => 'XLS/XLSX',
                'ppt' => 'PPT/PPTX',
                'zip' => 'ZIP/RAR',
            ]"
        />
    </x-filter-panel>

    {{-- Results Info --}}
    <div class="flex items-center justify-between mb-6">
        <span class="text-gray-600">{{ $downloads->total() }} dokumen ditemukan</span>
    </div>

    {{-- Downloads List --}}
    @if($downloads->count() > 0)
    <div class="space-y-4 mb-8">
        @foreach($downloads as $download)
        @php
            $extension = pathinfo($download->file, PATHINFO_EXTENSION);
            $iconColors = [
                'pdf' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'icon' => 'PDF'],
                'doc' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'DOC'],
                'docx' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'DOC'],
                'xls' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'XLS'],
                'xlsx' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'XLS'],
                'ppt' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'PPT'],
                'pptx' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'PPT'],
                'zip' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'ZIP'],
                'rar' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'RAR'],
            ];
            $iconStyle = $iconColors[$extension] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => strtoupper($extension)];
        @endphp
        <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- File Icon --}}
                    <div class="flex-shrink-0 w-14 h-14 {{ $iconStyle['bg'] }} rounded-lg flex items-center justify-center">
                        <span class="{{ $iconStyle['text'] }} font-bold text-sm">{{ $iconStyle['icon'] }}</span>
                    </div>
                    
                    {{-- File Info --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $download->title }}</h3>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                            @if($download->category)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded">
                                {{ $download->category }}
                            </span>
                            @endif
                            @if($download->formatted_size)
                            <span>{{ $download->formatted_size }}</span>
                            @endif
                            <span>{{ $download->created_at->format('d M Y') }}</span>
                            @if($download->download_count)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ number_format($download->download_count) }} unduhan
                            </span>
                            @endif
                            {{-- Unit Source Label --}}
                            @if(isset($unitType) && $unitType !== \App\Enums\UnitType::UNIVERSITAS)
                                @php
                                    $sourceLabel = match($download->unit_type) {
                                        \App\Enums\UnitType::UNIVERSITAS => 'Universitas',
                                        \App\Enums\UnitType::FAKULTAS => $download->unit_instance?->nama ?? 'Fakultas',
                                        \App\Enums\UnitType::PRODI => $download->unit_instance?->nama ?? 'Program Studi',
                                        default => null,
                                    };
                                    $isFromParent = $download->unit_type !== $unitType || $download->unit_id !== $unitId;
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
                        @if($download->description)
                        <p class="text-sm text-gray-600 mt-2">{{ Str::limit($download->description, 100) }}</p>
                        @endif
                    </div>
                </div>
                
                {{-- Download Button --}}
                <div class="flex-shrink-0 ml-4">
                    <a href="{{ route('download.file', $download) }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $downloads->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Dokumen</h3>
        <p class="text-gray-500">Dokumen unduhan akan segera ditampilkan.</p>
    </div>
    @endif

    {{-- Quick Categories --}}
    @if(isset($categories) && count($categories) > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Kategori Dokumen</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $cat)
            <a href="{{ route('download.index', ['kategori' => $cat]) }}" 
               class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition group">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">
                            {{ $cat }}
                        </h3>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
