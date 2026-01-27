@props(['items' => [], 'wrapper' => true])

@php
    // Jika wrapper=true, breadcrumb akan punya background sendiri (gradient biru)
    // Jika wrapper=false, breadcrumb transparan (untuk digunakan di dalam hero section)
    $bgClass = $wrapper ? 'bg-gradient-to-r from-blue-700 to-blue-900' : 'bg-transparent';
    $containerClass = $wrapper ? 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' : '';
    // Warna link: putih transparan untuk kontras di background gelap
    $linkColor = 'text-white/70 hover:text-white';
    $activeColor = 'text-white font-medium';
    $iconColor = 'text-white/50';
@endphp

<nav aria-label="Breadcrumb" class="{{ $bgClass }} text-white">
    <div class="{{ $containerClass }} py-3">
        <ol class="flex items-center flex-wrap gap-2 text-sm">
            {{-- Home --}}
            <li>
                <a href="{{ url('/') }}" class="{{ $linkColor }} transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </a>
            </li>
            
            @foreach($items as $item)
            <li class="flex items-center">
                <svg class="w-4 h-4 {{ $iconColor }} mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                
                @if(isset($item['url']) && !$loop->last)
                <a href="{{ $item['url'] }}" class="{{ $linkColor }} transition">
                    {{ $item['label'] }}
                </a>
                @else
                <span class="{{ $activeColor }}">{{ $item['label'] }}</span>
                @endif
            </li>
            @endforeach
        </ol>
    </div>
</nav>
