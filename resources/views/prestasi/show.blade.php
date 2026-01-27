@extends('layouts.app')

@section('title', $prestasi->judul . ' - Prestasi - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prestasi', 'url' => route('prestasi.index')],
    ['label' => Str::limit($prestasi->judul, 30)]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                {{-- Image --}}
                @if($prestasi->foto)
                <div class="h-64 md:h-96 overflow-hidden">
                    <img src="{{ Storage::url($prestasi->foto) }}" 
                         alt="{{ $prestasi->judul }}"
                         class="w-full h-full object-cover">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                @endif
                
                <div class="p-6">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        @php
                            $tingkatColors = [
                                'universitas' => 'bg-gray-100 text-gray-700',
                                'regional' => 'bg-blue-100 text-blue-700',
                                'nasional' => 'bg-green-100 text-green-700',
                                'internasional' => 'bg-purple-100 text-purple-700',
                            ];
                        @endphp
                        <span class="px-3 py-1 {{ $tingkatColors[$prestasi->tingkat->value] ?? 'bg-gray-100 text-gray-700' }} text-sm font-medium rounded-full">
                            {{ $prestasi->tingkat->label() }}
                        </span>
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">
                            {{ $prestasi->kategori->label() }}
                        </span>
                    </div>
                    
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                        {{ $prestasi->judul }}
                    </h1>
                    
                    {{-- Meta --}}
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-6">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $prestasi->tanggal->format('d F Y') }}
                        </div>
                        @if($prestasi->lokasi)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            {{ $prestasi->lokasi }}
                        </div>
                        @endif
                    </div>
                    
                    {{-- Description --}}
                    @if($prestasi->deskripsi)
                    <div class="prose prose-lg max-w-none">
                        {!! $prestasi->deskripsi !!}
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Gallery --}}
            @if($prestasi->gallery && count($prestasi->gallery) > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Galeri</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($prestasi->gallery as $image)
                    <a href="{{ Storage::url($image) }}" 
                       target="_blank"
                       class="block rounded-lg overflow-hidden hover:opacity-90 transition">
                        <img src="{{ Storage::url($image) }}" 
                             alt="Gallery" 
                             class="w-full h-32 object-cover">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            {{-- Certificate --}}
            @if($prestasi->sertifikat)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Sertifikat</h2>
                <a href="{{ Storage::url($prestasi->sertifikat) }}" 
                   target="_blank"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lihat Sertifikat
                </a>
            </div>
            @endif
        </div>
        
        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Detail Info --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Prestasi</h2>
                <div class="space-y-4">
                    @if($prestasi->penyelenggara)
                    <div>
                        <p class="text-sm text-gray-500">Penyelenggara</p>
                        <p class="font-medium text-gray-900">{{ $prestasi->penyelenggara }}</p>
                    </div>
                    @endif
                    
                    @if($prestasi->peserta)
                    <div>
                        <p class="text-sm text-gray-500">Peserta/Peraih</p>
                        <p class="font-medium text-gray-900">{{ $prestasi->peserta }}</p>
                    </div>
                    @endif
                    
                    @if($prestasi->pembimbing)
                    <div>
                        <p class="text-sm text-gray-500">Pembimbing</p>
                        <p class="font-medium text-gray-900">{{ $prestasi->pembimbing }}</p>
                    </div>
                    @endif
                    
                    @if($prestasi->link)
                    <div>
                        <p class="text-sm text-gray-500">Link Terkait</p>
                        <a href="{{ $prestasi->link }}" 
                           target="_blank"
                           class="text-blue-600 hover:text-blue-700 font-medium break-all">
                            {{ $prestasi->link }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Share --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Bagikan</h2>
                <x-share-buttons 
                    :url="request()->url()" 
                    :title="$prestasi->judul" 
                    :description="$prestasi->deskripsi ?? ''"
                />
            </div>
            
            {{-- Related Prestasi --}}
            @if(isset($relatedPrestasi) && $relatedPrestasi->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Prestasi Lainnya</h2>
                <div class="space-y-4">
                    @foreach($relatedPrestasi as $related)
                    <a href="{{ route('prestasi.show', $related->id) }}" class="flex gap-3 group">
                        <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden">
                            @if($related->foto)
                            <img src="{{ Storage::url($related->foto) }}" 
                                 alt="{{ $related->judul }}"
                                 class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                {{ $related->judul }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $related->tanggal->format('d M Y') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
