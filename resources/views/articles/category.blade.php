@extends('layouts.app')

@section('title', $category->name . ' - Berita - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Berita', 'url' => route('article.index')],
    ['label' => $category->name]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 font-medium rounded-full mb-4">Kategori</span>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
        @if($category->description)
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            {{ $category->description }}
        </p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-3">
            {{-- Articles Count --}}
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200">
                <span class="text-gray-600">{{ $articles->total() }} artikel dalam kategori ini</span>
            </div>

            {{-- Articles Grid --}}
            @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($articles as $article)
                <x-article-card :article="$article" :currentUnitType="$unitType ?? null" :currentUnitId="$unitId ?? null" :fakultas="$fakultas ?? null" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Artikel</h3>
                <p class="text-gray-500">Belum ada artikel dalam kategori ini.</p>
                <a href="{{ route('article.index') }}" class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-700">
                    ← Kembali ke semua artikel
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <x-sidebar :categories="$categories" :recentArticles="$recentArticles" />
        </div>
    </div>
</div>
@endsection
