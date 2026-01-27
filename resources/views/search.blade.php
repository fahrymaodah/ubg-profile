@extends('layouts.app')

@section('title', 'Pencarian - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Pencarian']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Search Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Pencarian</h1>
        <p class="text-lg text-gray-600">Temukan informasi yang Anda cari</p>
    </div>

    {{-- Search Form --}}
    <div class="max-w-2xl mx-auto mb-8">
        <form action="{{ route('search') }}" method="GET" class="relative">
            <input type="text" 
                   name="q" 
                   value="{{ $query ?? '' }}"
                   placeholder="Ketik kata kunci pencarian..."
                   class="w-full px-6 py-4 pr-14 text-lg border-2 border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   autofocus>
            <input type="hidden" name="type" value="{{ $type ?? 'all' }}">
            <button type="submit" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Type Filters --}}
    @if(isset($query) && $query)
    <div class="flex justify-center gap-2 mb-8 flex-wrap">
        <a href="{{ route('search', ['q' => $query, 'type' => 'all']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Semua
        </a>
        <a href="{{ route('search', ['q' => $query, 'type' => 'article']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? '') === 'article' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Berita
        </a>
        <a href="{{ route('search', ['q' => $query, 'type' => 'event']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? '') === 'event' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Agenda
        </a>
        <a href="{{ route('search', ['q' => $query, 'type' => 'dosen']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? '') === 'dosen' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Dosen
        </a>
        <a href="{{ route('search', ['q' => $query, 'type' => 'prestasi']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? '') === 'prestasi' ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Prestasi
        </a>
        <a href="{{ route('search', ['q' => $query, 'type' => 'page']) }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ ($type ?? '') === 'page' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Halaman
        </a>
    </div>
    @endif

    {{-- Search Results --}}
    @if(isset($query) && $query)
    @php
        $totalResults = ($articles->count() ?? 0) + ($events->count() ?? 0) + ($dosen->count() ?? 0) + ($prestasi->count() ?? 0) + ($pages->count() ?? 0);
    @endphp

    <div class="mb-8">
        <p class="text-gray-600">
            Ditemukan <strong class="text-gray-900">{{ $totalResults }}</strong> hasil untuk 
            <strong class="text-gray-900">"{{ $query }}"</strong>
        </p>
    </div>

    @if($totalResults > 0)
    <div class="space-y-8">
        {{-- Articles Results --}}
        @if($articles->count() > 0)
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </span>
                Berita ({{ $articles->count() }})
            </h3>
            <div class="grid gap-4">
                @foreach($articles as $article)
                <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition flex items-start gap-4">
                    @if($article->thumbnail)
                    <img src="{{ Storage::url($article->thumbnail) }}" alt="{{ $article->title }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded mb-2">Berita</span>
                        <h4 class="font-semibold text-gray-900 mb-1">
                            <a href="{{ route('article.show', $article) }}" class="hover:text-blue-600">
                                {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e($article->title)) !!}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600 line-clamp-2">{!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e(Str::limit($article->excerpt ?? strip_tags($article->content), 150))) !!}</p>
                        <span class="text-xs text-gray-500 mt-1 block">{{ $article->published_at?->format('d M Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Events Results --}}
        @if($events->count() > 0)
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                Agenda ({{ $events->count() }})
            </h3>
            <div class="grid gap-4">
                @foreach($events as $event)
                <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition flex items-start gap-4">
                    @if($event->poster)
                    <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded mb-2">Agenda</span>
                        <h4 class="font-semibold text-gray-900 mb-1">
                            <a href="{{ route('event.show', $event) }}" class="hover:text-green-600">
                                {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e($event->title)) !!}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600 line-clamp-2">{!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e(Str::limit($event->description, 150))) !!}</p>
                        <span class="text-xs text-gray-500 mt-1 block">{{ $event->start_date?->format('d M Y') }} - {{ $event->location }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Dosen Results --}}
        @if($dosen->count() > 0)
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Dosen ({{ $dosen->count() }})
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($dosen as $d)
                <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition flex items-center gap-4">
                    @if($d->foto)
                    <img src="{{ Storage::url($d->foto) }}" alt="{{ $d->nama }}" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
                    @else
                    <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-purple-600 font-bold text-xl">{{ strtoupper(substr($d->nama, 0, 1)) }}</span>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded mb-1">Dosen</span>
                        <h4 class="font-semibold text-gray-900">
                            <a href="{{ route('dosen.show', $d) }}" class="hover:text-purple-600">
                                {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e($d->full_name)) !!}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600">{{ $d->jabatan_fungsional }} - {{ $d->prodi?->nama }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Prestasi Results --}}
        @if($prestasi->count() > 0)
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </span>
                Prestasi ({{ $prestasi->count() }})
            </h3>
            <div class="grid gap-4">
                @foreach($prestasi as $p)
                <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition flex items-start gap-4">
                    @if($p->foto)
                    <img src="{{ Storage::url($p->foto) }}" alt="{{ $p->nama_prestasi }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 rounded mb-2">Prestasi</span>
                        <h4 class="font-semibold text-gray-900 mb-1">
                            <a href="{{ route('prestasi.show', $p) }}" class="hover:text-amber-600">
                                {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e($p->nama_prestasi)) !!}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600">{{ $p->nama_peserta }} - {{ $p->tingkat?->label() }} {{ $p->peringkat }}</p>
                        <span class="text-xs text-gray-500">{{ $p->penyelenggara }} • {{ $p->tanggal?->format('Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Pages Results --}}
        @if($pages->count() > 0)
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                Halaman ({{ $pages->count() }})
            </h3>
            <div class="grid gap-4">
                @foreach($pages as $page)
                <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition">
                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded mb-2">Halaman</span>
                    <h4 class="font-semibold text-gray-900 mb-1">
                        <a href="{{ route('page.show', $page) }}" class="hover:text-blue-600">
                            {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e($page->title)) !!}
                        </a>
                    </h4>
                    <p class="text-sm text-gray-600 line-clamp-2">{!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', e(Str::limit(strip_tags($page->content), 150))) !!}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @else
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ditemukan</h3>
        <p class="text-gray-500 mb-6">
            Tidak ada hasil yang cocok dengan pencarian "{{ $query }}"
        </p>
        <div class="text-gray-600">
            <p class="mb-2">Saran:</p>
            <ul class="text-sm space-y-1">
                <li>• Periksa ejaan kata kunci</li>
                <li>• Gunakan kata kunci yang lebih umum</li>
                <li>• Coba kata kunci yang berbeda</li>
            </ul>
        </div>
    </div>
    @endif

    @else
    {{-- No Search Query --}}
    <div class="text-center py-16">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Mulai Pencarian</h3>
        <p class="text-gray-500">
            Ketik minimal 3 karakter di kotak pencarian untuk menemukan informasi.
        </p>
    </div>

    {{-- Quick Links --}}
    <div class="mt-8 max-w-2xl mx-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Jelajahi</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('article.index') }}" class="p-4 bg-blue-50 rounded-xl text-center hover:bg-blue-100 transition">
                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Berita</span>
            </a>
            <a href="{{ route('event.index') }}" class="p-4 bg-green-50 rounded-xl text-center hover:bg-green-100 transition">
                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Agenda</span>
            </a>
            <a href="{{ route('dosen.index') }}" class="p-4 bg-purple-50 rounded-xl text-center hover:bg-purple-100 transition">
                <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Dosen</span>
            </a>
            <a href="{{ route('prestasi.index') }}" class="p-4 bg-amber-50 rounded-xl text-center hover:bg-amber-100 transition">
                <svg class="w-8 h-8 text-amber-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Prestasi</span>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
