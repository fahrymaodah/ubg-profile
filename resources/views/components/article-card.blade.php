@props([
    'article', 
    'featured' => false,
    'currentUnitType' => null,
    'currentUnitId' => null,
    'fakultas' => null,
])

@php
    use App\Enums\UnitType;
    
    // Determine if we need to show unit source label (CASCADE BOTH)
    // Show label for any article NOT from the current unit
    $showUnitLabel = false;
    $unitLabel = '';
    
    if ($currentUnitType && $article->unit_type) {
        $articleUnitType = $article->unit_type instanceof UnitType ? $article->unit_type : UnitType::tryFrom($article->unit_type);
        $viewUnitType = $currentUnitType instanceof UnitType ? $currentUnitType : UnitType::tryFrom($currentUnitType);
        
        // Check if article is NOT from current unit (show label for all external content)
        $isFromCurrentUnit = $articleUnitType === $viewUnitType && $article->unit_id == $currentUnitId;
        
        if (!$isFromCurrentUnit) {
            $showUnitLabel = true;
            // Always use unit_source_label from the article model - it's reliable
            $unitLabel = $article->unit_source_label;
        }
    }
@endphp

<article class="{{ $featured ? 'md:col-span-2 md:row-span-2' : '' }}">
    <a href="{{ route('article.show', $article->slug) }}" 
       class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden h-full">
        {{-- Image --}}
        <div class="relative {{ $featured ? 'h-64 md:h-80' : 'h-48' }} overflow-hidden">
            @if($article->featured_image)
            <img src="{{ Storage::url($article->featured_image) }}" 
                 alt="{{ $article->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            @endif
            
            {{-- Unit Source Label (top-left) --}}
            @if($showUnitLabel)
            <span class="absolute top-4 left-4 px-3 py-1 bg-sky-500 text-white text-xs font-semibold rounded-full">
                {{ $unitLabel }}
            </span>
            @endif
            
            {{-- Featured Badge (top-right) --}}
            @if($article->is_featured)
            <span class="absolute top-4 right-4 px-3 py-1 bg-amber-500 text-white text-xs font-semibold rounded-full">
                Featured
            </span>
            @endif
        </div>
        
        {{-- Content --}}
        <div class="p-5">
            {{-- Date & Category --}}
            <div class="flex items-center justify-between text-sm mb-3">
                <div class="flex items-center text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                </div>
                @if($article->category)
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                    {{ $article->category->name }}
                </span>
                @endif
            </div>
            
            {{-- Title --}}
            <h3 class="{{ $featured ? 'text-xl md:text-2xl' : 'text-lg' }} font-bold text-gray-900 mb-3 line-clamp-2">
                <span class="group-hover:text-blue-600 transition-colors">{{ $article->title }}</span>
            </h3>
            
            {{-- Excerpt --}}
            @if($article->excerpt)
            <p class="text-gray-600 text-sm line-clamp-{{ $featured ? '3' : '2' }} mb-4">
                {{ $article->excerpt }}
            </p>
            @endif
            
            {{-- Read More --}}
            <span class="inline-flex items-center text-blue-600 font-medium text-sm group-hover:text-blue-700">
                Baca Selengkapnya
                <svg class="ml-1 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </span>
        </div>
    </a>
</article>
