@extends('layouts.app')

@section('title', 'Galeri Video - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Galeri', 'url' => route('gallery.index')], ['label' => 'Video']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Galeri Video</h1>
        <p class="text-lg text-gray-600">Kumpulan video kegiatan dan dokumentasi</p>
    </div>

    {{-- Gallery Navigation --}}
    <div class="flex gap-4 mb-8">
        <a href="{{ route('gallery.index') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            Semua
        </a>
        <a href="{{ route('gallery.photos') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            Foto
        </a>
        <a href="{{ route('gallery.videos') }}" class="px-4 py-2 rounded-lg bg-red-600 text-white">
            Video
        </a>
    </div>

    @if($galleries->count() > 0)
    {{-- Video Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($galleries as $gallery)
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
            <div class="relative aspect-video">
                @if($gallery->youtube_url)
                    @php
                        // Extract YouTube video ID
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $gallery->youtube_url, $matches);
                        $videoId = $matches[1] ?? '';
                    @endphp
                    @if($videoId)
                    <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" 
                         alt="{{ $gallery->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @endif
                @elseif($gallery->thumbnail)
                    <img src="{{ Storage::url($gallery->thumbnail) }}" 
                         alt="{{ $gallery->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                
                {{-- Play Button Overlay --}}
                <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center group-hover:scale-110 transition shadow-lg">
                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>

                {{-- Duration Badge --}}
                @if($gallery->duration)
                <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/80 text-white text-xs rounded">
                    {{ $gallery->duration }}
                </div>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-red-600 transition">
                    @if($gallery->youtube_url)
                    <a href="{{ $gallery->youtube_url }}" target="_blank" rel="noopener">
                        {{ $gallery->title }}
                    </a>
                    @else
                    {{ $gallery->title }}
                    @endif
                </h3>
                @if($gallery->description)
                <p class="text-sm text-gray-600 line-clamp-2">{{ $gallery->description }}</p>
                @endif
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $gallery->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $galleries->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Video</h3>
        <p class="text-gray-500">Galeri video akan segera tersedia.</p>
    </div>
    @endif
</div>
@endsection
