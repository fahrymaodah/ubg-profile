@extends('layouts.app')

@section('title', $settings['site_name'] ?? 'Universitas Bumigora')

@section('content')
{{-- Announcement Ticker --}}
@php
    $showAnnouncement = $settings['show_announcement_bar'] ?? true;
    $showAnnouncement = $showAnnouncement === true || $showAnnouncement === 'true' || $showAnnouncement === '1';
@endphp
<x-announcement-ticker :announcements="$announcements" :show="$showAnnouncement" />

{{-- Hero Slider --}}
<x-slider :sliders="$sliders" />

{{-- Welcome Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Selamat Datang di<br>
                    <span class="text-blue-600">{{ $settings['site_name'] ?? 'Universitas Bumigora' }}</span>
                </h2>
                <div class="prose prose-lg text-gray-600 mb-8">
                    {!! $settings['site_description'] ?? 'Universitas Bumigora adalah institusi pendidikan tinggi yang berkomitmen untuk menghasilkan lulusan berkualitas dan berdaya saing tinggi.' !!}
                </div>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('page.show', 'visi-misi') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Visi & Misi
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('page.show', 'sejarah') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
                        Sejarah
                    </a>
                </div>
            </div>
            <div class="relative">
                @if(isset($settings['site_logo']))
                <img src="{{ Storage::url($settings['site_logo']) }}" 
                     alt="{{ $settings['site_name'] ?? 'UBG' }}" 
                     class="w-full max-w-md mx-auto">
                @else
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-12 text-center text-white max-w-md mx-auto">
                    <h3 class="text-6xl font-bold">UBG</h3>
                    <p class="text-xl mt-2">Universitas Bumigora</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['fakultas'] ?? 0 }}</div>
                <p class="text-blue-200">Fakultas</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['prodi'] ?? 0 }}</div>
                <p class="text-blue-200">Program Studi</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['dosen'] ?? 0 }}</div>
                <p class="text-blue-200">Dosen</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['prestasi'] ?? 0 }}</div>
                <p class="text-blue-200">Prestasi</p>
            </div>
        </div>
    </div>
</section>

{{-- Fakultas Showcase --}}
@if(isset($fakultas) && $fakultas->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fakultas</h2>
            <p class="text-lg text-gray-600">Jelajahi berbagai fakultas dan program studi kami</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($fakultas as $fak)
            <a href="{{ url($fak->subdomain ? '//' . $fak->subdomain . '.' . config('app.domain') : '/fakultas/' . $fak->slug) }}" 
               class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                    @if($fak->logo)
                    <img src="{{ Storage::url($fak->logo) }}" alt="{{ $fak->nama }}" class="h-20 w-auto object-contain">
                    @else
                    <span class="text-4xl font-bold text-white/50">{{ substr($fak->nama, 0, 2) }}</span>
                    @endif
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition mb-2">
                        {{ $fak->nama }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $fak->prodi_count ?? $fak->prodi->count() }} Program Studi
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Articles --}}
@if($featuredArticles->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Berita Terkini</h2>
                <p class="text-lg text-gray-600">Informasi dan berita terbaru dari kampus</p>
            </div>
            <a href="{{ route('article.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredArticles as $index => $article)
            <x-article-card :article="$article" :featured="$index === 0" />
            @endforeach
        </div>
        
        <div class="mt-8 text-center md:hidden">
            <a href="{{ route('article.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Lihat Semua Berita
            </a>
        </div>
    </div>
</section>
@endif

{{-- Upcoming Events --}}
@if($upcomingEvents->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Agenda Kegiatan</h2>
                <p class="text-lg text-gray-600">Kegiatan dan acara yang akan datang</p>
            </div>
            <a href="{{ route('event.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($upcomingEvents as $event)
            <x-event-card :event="$event" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Prestasi --}}
@if($prestasi->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Prestasi</h2>
                <p class="text-lg text-gray-600">Pencapaian dan prestasi terbaru</p>
            </div>
            <a href="{{ route('prestasi.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($prestasi as $item)
            <x-prestasi-card :prestasi="$item" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Tertarik Bergabung?</h2>
        <p class="text-lg text-blue-200 mb-8 max-w-2xl mx-auto">
            Mulai perjalanan akademis Anda bersama Universitas Bumigora. Daftar sekarang dan jadilah bagian dari komunitas kami.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="#" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition text-lg">
                Daftar Sekarang
            </a>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition text-lg">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>
@endsection
