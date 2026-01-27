@extends('layouts.app')

@section('title', $gallery->judul . ' - Galeri - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Galeri', 'url' => route('gallery.index')],
    ['label' => Str::limit($gallery->judul, 30)]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $gallery->judul }}</h1>
        
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $gallery->tanggal ? $gallery->tanggal->format('d F Y') : $gallery->created_at->format('d F Y') }}
            </div>
            @if($gallery->images && count($gallery->images) > 0)
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ count($gallery->images) }} foto
            </div>
            @endif
            @if($gallery->category)
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                {{ $gallery->category->nama }}
            </span>
            @endif
        </div>

        @if($gallery->deskripsi)
        <div class="mt-4 text-gray-600 prose max-w-none">
            {!! $gallery->deskripsi !!}
        </div>
        @endif
    </div>

    {{-- Gallery Grid with Lightbox --}}
    @if($gallery->images && count($gallery->images) > 0)
    <div x-data="{ 
        lightbox: false, 
        currentIndex: 0,
        images: {{ json_encode(array_map(fn($img) => Storage::url($img), $gallery->images)) }},
        open(index) {
            this.currentIndex = index;
            this.lightbox = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.lightbox = false;
            document.body.style.overflow = '';
        },
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        }
    }" 
    @keydown.escape.window="close()"
    @keydown.right.window="lightbox && next()"
    @keydown.left.window="lightbox && prev()">
        
        {{-- Gallery Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($gallery->images as $index => $image)
            <div class="relative aspect-square rounded-xl overflow-hidden cursor-pointer group"
                 @click="open({{ $index }})">
                <img src="{{ Storage::url($image) }}" 
                     alt="{{ $gallery->judul }} - Foto {{ $index + 1 }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
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
             class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
             style="display: none;">
            
            {{-- Close Button --}}
            <button @click="close()" 
                    class="absolute top-4 right-4 p-2 text-white/70 hover:text-white transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Previous Button --}}
            <button @click="prev()" 
                    class="absolute left-4 p-3 text-white/70 hover:text-white bg-black/30 rounded-full transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Image --}}
            <div class="max-w-4xl max-h-[85vh] mx-auto px-16">
                <img :src="images[currentIndex]" 
                     class="max-w-full max-h-[85vh] object-contain rounded-lg">
            </div>

            {{-- Next Button --}}
            <button @click="next()" 
                    class="absolute right-4 p-3 text-white/70 hover:text-white bg-black/30 rounded-full transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Counter --}}
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white/70 text-sm">
                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            </div>

            {{-- Thumbnails --}}
            <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 flex space-x-2 overflow-x-auto max-w-full px-4">
                <template x-for="(img, index) in images" :key="index">
                    <button @click="currentIndex = index"
                            :class="currentIndex === index ? 'ring-2 ring-white opacity-100' : 'opacity-50 hover:opacity-75'"
                            class="flex-shrink-0 w-16 h-16 rounded overflow-hidden transition">
                        <img :src="img" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Foto</h3>
        <p class="text-gray-500">Album ini belum memiliki foto.</p>
    </div>
    @endif

    {{-- Related Galleries --}}
    @if(isset($relatedGalleries) && $relatedGalleries->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Album Lainnya</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedGalleries as $related)
            <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-lg transition">
                <a href="{{ route('gallery.show', $related->id) }}" class="block">
                    <div class="relative h-40 overflow-hidden">
                        @if($related->cover || ($related->images && count($related->images) > 0))
                        <img src="{{ Storage::url($related->cover ?? $related->images[0]) }}" 
                             alt="{{ $related->judul }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                        @if($related->images && count($related->images) > 0)
                        <span class="absolute top-2 right-2 px-2 py-1 bg-black/70 text-white text-xs rounded-full">
                            {{ count($related->images) }} foto
                        </span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition truncate">
                            {{ $related->judul }}
                        </h3>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
