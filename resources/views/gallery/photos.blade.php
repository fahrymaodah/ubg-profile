@extends('layouts.app')

@section('title', 'Galeri Foto - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Galeri', 'url' => route('gallery.index')], ['label' => 'Foto']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Galeri Foto</h1>
        <p class="text-lg text-gray-600">Kumpulan foto kegiatan dan dokumentasi</p>
    </div>

    {{-- Gallery Navigation --}}
    <div class="flex gap-4 mb-8">
        <a href="{{ route('gallery.index') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            Semua
        </a>
        <a href="{{ route('gallery.photos') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white">
            Foto
        </a>
        <a href="{{ route('gallery.videos') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            Video
        </a>
    </div>

    @if($galleries->count() > 0)
    {{-- Photo Grid with Lightbox --}}
    <div x-data="{ lightbox: false, currentImage: '', currentTitle: '' }">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($galleries as $gallery)
            <div class="group cursor-pointer relative aspect-square overflow-hidden rounded-xl"
                 @click="lightbox = true; currentImage = '{{ Storage::url($gallery->file) }}'; currentTitle = '{{ addslashes($gallery->title) }}'">
                <img src="{{ Storage::url($gallery->file) }}" 
                     alt="{{ $gallery->title }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <h3 class="text-white font-semibold text-sm line-clamp-2">{{ $gallery->title }}</h3>
                    </div>
                </div>
                {{-- Zoom Icon --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Lightbox Modal --}}
        <div x-show="lightbox" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="lightbox = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4"
             style="display: none;">
            {{-- Close Button --}}
            <button @click="lightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300 transition z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Image --}}
            <div class="max-w-5xl max-h-[90vh] relative">
                <img :src="currentImage" :alt="currentTitle" class="max-w-full max-h-[80vh] object-contain">
                <div class="text-center mt-4">
                    <p x-text="currentTitle" class="text-white text-lg"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $galleries->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Foto</h3>
        <p class="text-gray-500">Galeri foto akan segera tersedia.</p>
    </div>
    @endif
</div>
@endsection
