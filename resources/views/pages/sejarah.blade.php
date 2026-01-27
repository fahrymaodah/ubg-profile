@extends('layouts.app')

@section('title', 'Sejarah - ' . ($settings['site_name'] ?? config('app.name')))

@php
    $sejarah = $profil['sejarah'] ?? [];
    $timeline = $sejarah['timeline'] ?? [];
    $pendirian = $sejarah['pendirian'] ?? '';
    $lokasi = $sejarah['lokasi'] ?? '';
    $akreditasi = $sejarah['akreditasi'] ?? '';
    $sejarahImage = $sejarah['image'] ?? null;
    $sejarahCaption = $sejarah['image_caption'] ?? '';
@endphp

@section('content')
<div class="bg-white">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-amber-600 to-amber-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Profil'], ['label' => 'Sejarah']]" :wrapper="false" />
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Sejarah</h1>
            <p class="text-xl text-amber-200">Perjalanan {{ $settings['site_name'] ?? config('app.name') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- Sejarah Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                {{-- Timeline --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Tonggak Sejarah</h2>
                    
                    @if(count($timeline) > 0)
                        <div class="relative">
                            {{-- Timeline Line --}}
                            <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-amber-200 transform md:-translate-x-1/2"></div>
                            
                            {{-- Timeline Items --}}
                            <div class="space-y-8">
                                @foreach($timeline as $index => $item)
                                    @if($index % 2 == 0)
                                        {{-- Left side (odd index for visual) --}}
                                        <div class="relative flex items-center md:justify-center">
                                            <div class="flex-1 md:w-1/2 md:pr-8 md:text-right pl-12 md:pl-0">
                                                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-amber-500 md:border-l-0 md:border-r-4">
                                                    @if(!empty($item['tahun']))
                                                        <span class="text-amber-600 font-bold">{{ $item['tahun'] }}</span>
                                                    @endif
                                                    <h3 class="font-bold text-gray-900 mt-1">{{ $item['judul'] ?? '' }}</h3>
                                                    @if(!empty($item['deskripsi']))
                                                        <p class="text-gray-600 text-sm mt-2">{{ $item['deskripsi'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="absolute left-4 md:left-1/2 w-4 h-4 bg-amber-500 rounded-full transform md:-translate-x-1/2 z-10 ring-4 ring-amber-100"></div>
                                            <div class="hidden md:block flex-1 w-1/2 pl-8"></div>
                                        </div>
                                    @else
                                        {{-- Right side --}}
                                        <div class="relative flex items-center md:justify-center">
                                            <div class="hidden md:block flex-1 w-1/2 pr-8"></div>
                                            <div class="absolute left-4 md:left-1/2 w-4 h-4 bg-amber-500 rounded-full transform md:-translate-x-1/2 z-10 ring-4 ring-amber-100"></div>
                                            <div class="flex-1 md:w-1/2 md:pl-8 pl-12">
                                                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-amber-500">
                                                    @if(!empty($item['tahun']))
                                                        <span class="text-amber-600 font-bold">{{ $item['tahun'] }}</span>
                                                    @endif
                                                    <h3 class="font-bold text-gray-900 mt-1">{{ $item['judul'] ?? '' }}</h3>
                                                    @if(!empty($item['deskripsi']))
                                                        <p class="text-gray-600 text-sm mt-2">{{ $item['deskripsi'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 italic">Timeline sejarah belum diatur.</p>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Quick Facts --}}
                @if(!empty($pendirian) || !empty($lokasi) || !empty($akreditasi))
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Fakta Singkat</h3>
                        <ul class="space-y-4">
                            @if(!empty($pendirian))
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <span class="font-semibold text-gray-900 block">Tahun Berdiri</span>
                                        <span class="text-gray-600">{{ $pendirian }}</span>
                                    </div>
                                </li>
                            @endif
                            @if(!empty($lokasi))
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div>
                                        <span class="font-semibold text-gray-900 block">Lokasi</span>
                                        <span class="text-gray-600">{{ $lokasi }}</span>
                                    </div>
                                </li>
                            @endif
                            @if(!empty($akreditasi))
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    <div>
                                        <span class="font-semibold text-gray-900 block">Akreditasi</span>
                                        <span class="text-gray-600">{{ $akreditasi }}</span>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                {{-- Gallery Sejarah --}}
                @if(!empty($sejarahImage))
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
                        <img src="{{ Storage::url($sejarahImage) }}" 
                             alt="{{ $sejarahCaption ?: 'Gedung Kampus' }}" 
                             class="w-full h-48 object-cover">
                        @if(!empty($sejarahCaption))
                            <div class="p-4">
                                <p class="text-sm text-gray-600 text-center">{{ $sejarahCaption }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- CTA --}}
                <div class="bg-amber-50 rounded-xl p-6 text-center">
                    <h4 class="font-bold text-gray-900 mb-2">Ingin Bergabung?</h4>
                    <p class="text-sm text-gray-600 mb-4">Jadilah bagian dari sejarah kami.</p>
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm font-medium">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
