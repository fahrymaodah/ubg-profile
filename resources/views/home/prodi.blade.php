@extends('layouts.app')

@section('title', $prodi->nama . ' - ' . ($settings['site_name'] ?? 'Universitas Bumigora'))

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
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-blue-600 font-semibold">{{ $prodi->fakultas?->nama ?? 'Fakultas' }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded">{{ $prodi->jenjang->value ?? 'S1' }}</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    {{ $prodi->nama }}
                </h2>
                <div class="prose prose-lg text-gray-600 mb-8">
                    {!! $prodi->deskripsi ?? 'Program studi yang mempersiapkan mahasiswa dengan pengetahuan dan keterampilan yang dibutuhkan di dunia kerja.' !!}
                </div>
                
                {{-- Akreditasi Badge --}}
                @if($prodi->akreditasi)
                <div class="inline-flex items-center space-x-3 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-6">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ $prodi->akreditasi }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Terakreditasi</p>
                        <p class="text-xs text-green-500">{{ $prodi->akreditasi_lembaga ?? 'BAN-PT' }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('page.show', 'visi-misi') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Visi & Misi
                    </a>
                    <a href="{{ route('page.show', 'kurikulum') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
                        Kurikulum
                    </a>
                </div>
            </div>
            <div class="relative">
                @if($prodi->logo)
                <img src="{{ Storage::url($prodi->logo) }}" 
                     alt="{{ $prodi->nama }}" 
                     class="w-full max-w-md mx-auto">
                @elseif($prodi->banner)
                <img src="{{ Storage::url($prodi->banner) }}" 
                     alt="{{ $prodi->nama }}" 
                     class="w-full rounded-xl shadow-lg">
                @else
                <div class="bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl p-12 text-center text-white max-w-md mx-auto">
                    <h3 class="text-4xl font-bold">{{ $prodi->kode ?? substr($prodi->nama, 0, 3) }}</h3>
                    <p class="text-lg mt-2">{{ $prodi->nama }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-12 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['dosen'] ?? 0 }}</div>
                <p class="text-purple-200">Dosen</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['akreditasi'] ?? '-' }}</div>
                <p class="text-purple-200">Akreditasi</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['artikel'] ?? 0 }}</div>
                <p class="text-purple-200">Artikel</p>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['prestasi'] ?? 0 }}</div>
                <p class="text-purple-200">Prestasi</p>
            </div>
        </div>
    </div>
</section>

{{-- Profil Lulusan --}}
@if($prodi->profil_lulusan)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Profil Lulusan</h2>
            <p class="text-lg text-gray-600">Kompetensi yang akan dimiliki setelah lulus</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8 prose prose-lg max-w-none">
            {!! $prodi->profil_lulusan !!}
        </div>
    </div>
</section>
@endif

{{-- Kurikulum Overview --}}
@if($prodi->kompetensi)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Kompetensi Lulusan</h2>
                <div class="prose prose-lg text-gray-600">
                    {!! $prodi->kompetensi !!}
                </div>
            </div>
            <div>
                @if($prodi->kurikulum_file)
                <div class="bg-blue-50 rounded-xl p-8 text-center">
                    <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Dokumen Kurikulum</h3>
                    <p class="text-gray-600 mb-4">Download dokumen kurikulum lengkap</p>
                    <a href="{{ Storage::url($prodi->kurikulum_file) }}" 
                       download
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

{{-- Dosen --}}
@if(isset($dosen) && $dosen->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Dosen</h2>
                <p class="text-lg text-gray-600">Tenaga pengajar Program Studi {{ $prodi->nama }}</p>
            </div>
            <a href="{{ route('dosen.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($dosen->take(4) as $d)
            <x-dosen-card :dosen="$d" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Latest Articles --}}
@if($latestArticles->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Berita Terkini</h2>
                <p class="text-lg text-gray-600">Informasi dan berita terbaru</p>
            </div>
            <a href="{{ route('article.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
                Lihat Semua
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
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Prestasi</h2>
                <p class="text-lg text-gray-600">Pencapaian mahasiswa</p>
            </div>
            <a href="{{ route('prestasi.index') }}" class="hidden md:inline-flex items-center px-4 py-2 text-blue-600 font-medium hover:bg-blue-50 rounded-lg transition">
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
<section class="py-16 bg-gradient-to-r from-purple-600 to-blue-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Tertarik Bergabung?</h2>
        <p class="text-lg text-purple-200 mb-8 max-w-2xl mx-auto">
            Daftar sekarang dan mulai perjalanan akademis Anda di Program Studi {{ $prodi->nama }}
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="#" class="inline-flex items-center px-8 py-4 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition text-lg">
                Daftar Sekarang
            </a>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-purple-600 transition text-lg">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>
@endsection
