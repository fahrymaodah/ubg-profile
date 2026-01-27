@extends('layouts.app')

@section('title', $fakultas->nama . ' - ' . ($settings['site_name'] ?? 'Universitas Bumigora'))

@section('content')
{{-- Announcement Ticker --}}
@php
    $showAnnouncement = $settings['show_announcement_bar'] ?? true;
    $showAnnouncement = $showAnnouncement === true || $showAnnouncement === 'true' || $showAnnouncement === '1';
@endphp
<x-announcement-ticker :announcements="$announcements" :show="$showAnnouncement" />

{{-- Hero Slider --}}
<x-slider :sliders="$sliders" />

{{-- About Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-blue-600 font-semibold mb-2 block">{{ $settings['site_name'] ?? 'Universitas Bumigora' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    {{ $fakultas->nama }}
                </h2>
                <div class="prose prose-lg text-gray-600 mb-8">
                    {!! $fakultas->deskripsi ?? 'Fakultas yang berkomitmen untuk menghasilkan lulusan berkualitas dan profesional di bidangnya.' !!}
                </div>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('page.show', 'visi-misi') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Visi & Misi
                    </a>
                    <a href="{{ route('dosen.index') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
                        Daftar Dosen
                    </a>
                </div>
            </div>
            <div class="relative">
                @if($fakultas->logo)
                <img src="{{ Storage::url($fakultas->logo) }}" 
                     alt="{{ $fakultas->nama }}" 
                     class="w-full max-w-md mx-auto">
                @elseif($fakultas->banner)
                <img src="{{ Storage::url($fakultas->banner) }}" 
                     alt="{{ $fakultas->nama }}" 
                     class="w-full rounded-xl shadow-lg">
                @else
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-12 text-center text-white max-w-md mx-auto">
                    <h3 class="text-4xl font-bold">{{ $fakultas->kode ?? substr($fakultas->nama, 0, 3) }}</h3>
                    <p class="text-lg mt-2">{{ $fakultas->nama }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-12 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['prodi'] ?? 0 }}</div>
                <p class="text-blue-200">Program Studi</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['dosen'] ?? 0 }}</div>
                <p class="text-blue-200">Dosen</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['artikel'] ?? 0 }}</div>
                <p class="text-blue-200">Artikel</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['prestasi'] ?? 0 }}</div>
                <p class="text-blue-200">Prestasi</p>
            </div>
        </div>
    </div>
</section>

{{-- Program Studi --}}
@if(isset($prodiList) && $prodiList->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Program Studi</h2>
            <p class="text-lg text-gray-600">Pilihan program studi yang tersedia di {{ $fakultas->nama }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($prodiList as $p)
            <a href="{{ url($p->subdomain ? '//' . $p->subdomain . '.' . config('app.domain') : '/prodi/' . $p->slug) }}" 
               class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center relative">
                    @if($p->logo)
                    <img src="{{ Storage::url($p->logo) }}" alt="{{ $p->nama }}" class="h-16 w-auto object-contain">
                    @else
                    <span class="text-3xl font-bold text-white/50">{{ $p->kode ?? substr($p->nama, 0, 2) }}</span>
                    @endif
                    <span class="absolute top-4 right-4 px-2 py-1 bg-white/20 text-white text-xs font-semibold rounded">
                        {{ $p->jenjang->value ?? 'S1' }}
                    </span>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition mb-2">
                        {{ $p->nama }}
                    </h3>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Akreditasi: <strong>{{ $p->akreditasi ?? '-' }}</strong></span>
                        <span class="text-blue-600 group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Dosen --}}
@if(isset($featuredDosen) && $featuredDosen->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Dosen</h2>
                <p class="text-lg text-gray-600">Tenaga pengajar profesional dan berpengalaman</p>
            </div>
            <a href="{{ route('dosen.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredDosen as $dosen)
            <x-dosen-card :dosen="$dosen" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Latest Articles --}}
@if($latestArticles->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Berita Terkini</h2>
                <p class="text-lg text-gray-600">Informasi dan berita terbaru dari {{ $fakultas->nama }}</p>
            </div>
            <a href="{{ route('article.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestArticles->take(3) as $article)
            <x-article-card :article="$article" />
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
                <p class="text-lg text-gray-600">Pencapaian mahasiswa dan dosen</p>
            </div>
            <a href="{{ route('prestasi.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
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

{{-- Contact CTA --}}
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ada Pertanyaan?</h2>
        <p class="text-lg text-blue-200 mb-8 max-w-2xl mx-auto">
            Hubungi kami untuk informasi lebih lanjut tentang {{ $fakultas->nama }}
        </p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition text-lg">
            Hubungi Kami
        </a>
    </div>
</section>
@endsection
