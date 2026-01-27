@extends('layouts.app')

@section('title', $announcement->title . ' - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Pengumuman', 'url' => route('announcement.index')],
    ['label' => Str::limit($announcement->title, 50)]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                {{-- Header --}}
                <div class="p-8 border-b border-gray-100">
                    {{-- Priority Badge --}}
                    @php
                        $priorityConfig = [
                            'urgent' => ['icon' => '🔴', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Urgent'],
                            'high' => ['icon' => '🟠', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Tinggi'],
                            'normal' => ['icon' => '🟢', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Normal'],
                            'low' => ['icon' => '⚪', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Rendah'],
                        ];
                        $config = $priorityConfig[$announcement->priority] ?? $priorityConfig['normal'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config['bg'] }} {{ $config['text'] }} mb-4">
                        {{ $config['icon'] }} {{ $config['label'] }}
                    </span>
                    
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                        {{ $announcement->title }}
                    </h1>

                    {{-- Meta --}}
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Dipublikasikan {{ $announcement->created_at->format('d F Y') }}
                        </span>
                        @if($announcement->start_date)
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Berlaku sejak {{ $announcement->start_date->format('d M Y') }}
                        </span>
                        @endif
                        @if($announcement->end_date)
                        <span class="flex items-center gap-1 text-orange-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Berlaku hingga {{ $announcement->end_date->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $announcement->content !!}
                    </div>
                </div>

                {{-- Share --}}
                <div class="p-8 border-t border-gray-100">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Bagikan Pengumuman</h3>
                    <x-share-buttons 
                        :url="request()->url()" 
                        :title="$announcement->title" 
                        :description="strip_tags(Str::limit($announcement->content, 160))"
                    />
                </div>
            </article>

            {{-- Navigation --}}
            <div class="mt-6">
                <a href="{{ route('announcement.index') }}" 
                   class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Pengumuman
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Related Announcements --}}
            @if($relatedAnnouncements->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Pengumuman Lainnya</h2>
                <div class="space-y-4">
                    @foreach($relatedAnnouncements as $related)
                    <a href="{{ route('announcement.show', $related) }}" class="block group">
                        <div class="flex items-start gap-3">
                            @php
                                $relatedConfig = $priorityConfig[$related->priority] ?? $priorityConfig['normal'];
                            @endphp
                            <span class="flex-shrink-0 text-lg">{{ $relatedConfig['icon'] }}</span>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                    {{ $related->title }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $related->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Info Box --}}
            <div class="bg-blue-50 rounded-xl p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-800">Informasi Penting</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Pastikan untuk membaca pengumuman secara lengkap dan perhatikan tanggal berlaku yang tertera.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
