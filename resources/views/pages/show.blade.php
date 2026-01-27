@extends('layouts.app')

@section('title', $page->judul . ' - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => $page->judul]]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Page Header --}}
    @if($page->gambar)
    <div class="mb-8 rounded-xl overflow-hidden shadow-lg h-64 md:h-80">
        <img src="{{ Storage::url($page->gambar) }}" 
             alt="{{ $page->judul }}"
             class="w-full h-full object-cover">
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        {{-- Header --}}
        <div class="p-6 md:p-8 border-b">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900">{{ $page->judul }}</h1>
            
            @if($page->updated_at)
            <p class="text-sm text-gray-500 mt-2">
                Terakhir diperbarui: {{ $page->updated_at->format('d F Y') }}
            </p>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-6 md:p-8">
            <div class="prose prose-lg max-w-none">
                {!! $page->konten !!}
            </div>
        </div>

        {{-- Gallery --}}
        @if($page->gallery && count($page->gallery) > 0)
        <div class="p-6 md:p-8 border-t bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Galeri</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($page->gallery as $image)
                <a href="{{ Storage::url($image) }}" 
                   target="_blank"
                   class="block rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                    <img src="{{ Storage::url($image) }}" 
                         alt="Gallery" 
                         class="w-full h-32 object-cover hover:scale-105 transition-transform duration-300">
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Attachments --}}
        @if($page->attachments && count($page->attachments) > 0)
        <div class="p-6 md:p-8 border-t">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Lampiran</h2>
            <div class="space-y-3">
                @foreach($page->attachments as $attachment)
                @php
                    $filename = basename($attachment);
                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                @endphp
                <a href="{{ Storage::url($attachment) }}" 
                   target="_blank"
                   class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium text-gray-900 group-hover:text-blue-600 transition">{{ $filename }}</p>
                        <p class="text-sm text-gray-500 uppercase">{{ $extension }}</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Related Pages / Sub-pages --}}
    @if(isset($relatedPages) && $relatedPages->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Halaman Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedPages as $related)
            <a href="{{ route('page.show', $related->slug) }}" 
               class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition group">
                <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition mb-2">
                    {{ $related->judul }}
                </h3>
                @if($related->excerpt)
                <p class="text-sm text-gray-600 line-clamp-2">{{ $related->excerpt }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
