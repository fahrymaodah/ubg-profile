@extends('layouts.app')

@section('title', $event->title . ' - Agenda - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Agenda', 'url' => route('event.index')],
    ['label' => Str::limit($event->title, 30)]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            {{-- Header Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                {{-- Image --}}
                @if($event->image)
                <div class="h-64 md:h-80 overflow-hidden">
                    <img src="{{ Storage::url($event->image) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-full object-cover">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
                
                <div class="p-6">
                    {{-- Status Badge --}}
                    @php
                        $now = now();
                        $isUpcoming = $event->start_date > $now;
                        $isOngoing = $event->start_date <= $now && $event->end_date >= $now;
                        $isPast = $event->end_date < $now;
                    @endphp
                    <div class="mb-4">
                        @if($isUpcoming)
                        <span class="px-4 py-1.5 bg-blue-100 text-blue-700 font-semibold rounded-full">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Akan Datang
                            </span>
                        </span>
                        @elseif($isOngoing)
                        <span class="px-4 py-1.5 bg-green-100 text-green-700 font-semibold rounded-full animate-pulse">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Sedang Berlangsung
                            </span>
                        </span>
                        @else
                        <span class="px-4 py-1.5 bg-gray-100 text-gray-700 font-semibold rounded-full">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Selesai
                            </span>
                        </span>
                        @endif
                    </div>
                    
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                        {{ $event->title }}
                    </h1>
                    
                    {{-- Description --}}
                    @if($event->description)
                    <div class="prose prose-lg max-w-none">
                        {!! $event->description !!}
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Documents/Attachments --}}
            @if($event->dokumen)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Dokumen Terkait</h2>
                <a href="{{ Storage::url($event->dokumen) }}" 
                   target="_blank"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Unduh Dokumen
                </a>
            </div>
            @endif
        </div>
        
        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Event Details Card --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Acara</h2>
                <div class="space-y-4">
                    {{-- Date --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="font-medium text-gray-900">
                                {{ $event->start_date->format('d F Y') }}
                                @if($event->end_date->format('d F Y') !== $event->start_date->format('d F Y'))
                                - {{ $event->end_date->format('d F Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    {{-- Time --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Waktu</p>
                            <p class="font-medium text-gray-900">
                                {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} WITA
                            </p>
                        </div>
                    </div>
                    
                    {{-- Location --}}
                    @if($event->location)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lokasi</p>
                            <p class="font-medium text-gray-900">{{ $event->location }}</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Organizer --}}
                    @if($event->penyelenggara)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Penyelenggara</p>
                            <p class="font-medium text-gray-900">{{ $event->penyelenggara }}</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Contact --}}
                    @if($event->kontak)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kontak</p>
                            <p class="font-medium text-gray-900">{{ $event->kontak }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Registration Link --}}
                    @if($event->link_registrasi && $isUpcoming)
                    <div class="pt-4 border-t">
                        <a href="{{ $event->link_registrasi }}" 
                           target="_blank"
                           class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Daftar Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Countdown --}}
            @if($isUpcoming)
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white"
                 x-data="{
                     targetDate: new Date('{{ $event->start_date->toISOString() }}'),
                     days: 0,
                     hours: 0,
                     minutes: 0,
                     seconds: 0,
                     init() {
                         this.updateCountdown();
                         setInterval(() => this.updateCountdown(), 1000);
                     },
                     updateCountdown() {
                         const now = new Date();
                         const diff = this.targetDate - now;
                         if (diff > 0) {
                             this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                             this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                             this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                             this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                         }
                     }
                 }">
                <h2 class="text-lg font-bold mb-4 text-center">Hitung Mundur</h2>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <div class="text-3xl font-bold" x-text="days">0</div>
                        <div class="text-xs opacity-75">Hari</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" x-text="hours">0</div>
                        <div class="text-xs opacity-75">Jam</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" x-text="minutes">0</div>
                        <div class="text-xs opacity-75">Menit</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" x-text="seconds">0</div>
                        <div class="text-xs opacity-75">Detik</div>
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Share --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Bagikan</h2>
                <div class="flex space-x-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                       target="_blank"
                       class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($event->title) }}" 
                       target="_blank"
                       class="p-2 bg-sky-500 text-white rounded-full hover:bg-sky-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($event->title . ' ' . request()->url()) }}" 
                       target="_blank"
                       class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
                
                {{-- Add to Calendar --}}
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500 mb-2">Tambahkan ke kalender</p>
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($event->title) }}&dates={{ $event->start_date->format('Ymd\THis') }}/{{ $event->end_date->format('Ymd\THis') }}&details={{ urlencode(strip_tags($event->description ?? '')) }}&location={{ urlencode($event->location ?? '') }}" 
                       target="_blank"
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Google Calendar
                    </a>
                </div>
            </div>
            
            {{-- Related Events --}}
            @if(isset($relatedEvents) && $relatedEvents->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Agenda Lainnya</h2>
                <div class="space-y-4">
                    @foreach($relatedEvents as $related)
                    <a href="{{ route('event.show', $related) }}" class="flex gap-3 group">
                        <div class="flex-shrink-0 w-14 h-14 rounded-lg bg-blue-100 flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-blue-600">{{ $related->start_date->format('d') }}</span>
                            <span class="text-xs text-blue-500">{{ $related->start_date->format('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                {{ $related->title }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $related->start_date->format('H:i') }}</p>
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
