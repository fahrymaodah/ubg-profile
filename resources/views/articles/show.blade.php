@extends('layouts.app')

@section('title', $article->title . ' - ' . ($settings['site_name'] ?? config('app.name')))
@section('meta_description', $article->meta_description ?? $article->excerpt ?? Str::limit(strip_tags($article->content), 160))

@section('og_meta')
{{-- Open Graph / Facebook --}}
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $article->meta_title ?? $article->title }}">
<meta property="og:description" content="{{ $article->meta_description ?? $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}">
@if($article->featured_image)
<meta property="og:image" content="{{ url(Storage::url($article->featured_image)) }}">
<meta property="og:image:alt" content="{{ $article->title }}">
@else
<meta property="og:image" content="{{ asset('images/og-default.jpg') }}">
@endif
<meta property="og:site_name" content="{{ $settings['site_name'] ?? 'Universitas Bumigora' }}">
<meta property="og:locale" content="id_ID">
<meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $article->updated_at?->toIso8601String() }}">
@if($article->author)
<meta property="article:author" content="{{ $article->author->name }}">
@endif
@if($article->category)
<meta property="article:section" content="{{ $article->category->name }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="{{ $article->meta_title ?? $article->title }}">
<meta name="twitter:description" content="{{ $article->meta_description ?? $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}">
@if($article->featured_image)
<meta name="twitter:image" content="{{ url(Storage::url($article->featured_image)) }}">
@else
<meta name="twitter:image" content="{{ asset('images/og-default.jpg') }}">
@endif
@if(isset($settings['twitter_handle']))
<meta name="twitter:site" content="{{ $settings['twitter_handle'] }}">
@endif
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Berita', 'url' => route('article.index')],
    ['label' => $article->category?->name ?? 'Artikel', 'url' => $article->category ? route('article.category', $article->category->slug) : null],
    ['label' => Str::limit($article->title, 30)]
]" />

<article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-3">
            {{-- Article Header --}}
            <header class="mb-8">
                @if($article->category)
                <a href="{{ route('article.category', $article->category->slug) }}" 
                   class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full mb-4 hover:bg-blue-200 transition">
                    {{ $article->category->name }}
                </a>
                @endif
                
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    {{ $article->title }}
                </h1>
                
                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    @if($article->author)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $article->author->name }}
                    </div>
                    @endif
                    
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $article->published_at?->format('d F Y') ?? $article->created_at->format('d F Y') }}
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ number_format($article->views ?? 0) }} views
                    </div>
                </div>
            </header>
            
            {{-- Featured Image --}}
            @if($article->featured_image)
            <figure class="mb-8">
                <img src="{{ Storage::url($article->featured_image) }}" 
                     alt="{{ $article->title }}"
                     class="w-full rounded-xl shadow-lg">
            </figure>
            @endif
            
            {{-- Excerpt --}}
            @if($article->excerpt)
            <div class="text-xl text-gray-600 font-medium mb-8 border-l-4 border-blue-500 pl-4 italic">
                {{ $article->excerpt }}
            </div>
            @endif
            
            {{-- Content --}}
            <div class="prose prose-lg max-w-none mb-8">
                {!! $article->content !!}
            </div>
            
            {{-- Gallery --}}
            @if($article->gallery && count($article->gallery) > 0)
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Galeri</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($article->gallery as $image)
                    <a href="{{ Storage::url($image) }}" 
                       target="_blank"
                       class="block rounded-lg overflow-hidden hover:opacity-90 transition">
                        <img src="{{ Storage::url($image) }}" 
                             alt="Gallery image" 
                             class="w-full h-40 object-cover">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            {{-- Share --}}
            <div class="border-t border-b border-gray-200 py-6 mb-8">
                <x-share-buttons 
                    :url="url()->current()" 
                    :title="$article->title" 
                    :description="$article->excerpt ?? ''"
                    :image="$article->featured_image ? Storage::url($article->featured_image) : null"
                />
            </div>
            
            {{-- Related Articles --}}
            @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($relatedArticles as $related)
                    <x-article-card :article="$related" />
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <x-sidebar :categories="$categories" :recentArticles="$recentArticles" />
        </div>
    </div>
</article>
@endsection
