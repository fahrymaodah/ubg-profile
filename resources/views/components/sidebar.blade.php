@props([
    'categories' => collect(),
    'recentArticles' => collect(),
    'tags' => [],
    'showSearch' => true
])

<aside {{ $attributes->merge(['class' => 'space-y-6']) }}>
    {{-- Search --}}
    @if($showSearch)
    <div class="bg-white rounded-xl shadow-md p-5">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Pencarian</h4>
        <form action="{{ route('search') }}" method="GET">
            <div class="relative">
                <input type="text" 
                       name="q" 
                       placeholder="Cari artikel..."
                       value="{{ request('q') }}"
                       class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
    @endif
    
    {{-- Categories --}}
    @if($categories->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-5">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Kategori</h4>
        <ul class="space-y-2">
            @foreach($categories as $category)
            <li>
                <a href="{{ route('article.category', $category->slug) }}" 
                   class="flex items-center justify-between text-gray-600 hover:text-blue-600 transition py-1">
                    <span>{{ $category->name }}</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                        {{ $category->articles_count ?? $category->articles->count() }}
                    </span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    {{-- Recent Articles --}}
    @if($recentArticles->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-5">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Artikel Terbaru</h4>
        <div class="space-y-4">
            @foreach($recentArticles as $article)
            <a href="{{ route('article.show', $article->slug) }}" class="flex gap-3 group">
                <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" 
                         alt="{{ $article->title }}"
                         class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                        {{ $article->title }}
                    </h5>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Tags --}}
    @if(count($tags) > 0)
    <div class="bg-white rounded-xl shadow-md p-5">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Tags</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
            <a href="{{ route('search', ['tag' => $tag]) }}" 
               class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full hover:bg-blue-100 hover:text-blue-600 transition">
                {{ $tag }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Slot for additional content --}}
    {{ $slot }}
</aside>
