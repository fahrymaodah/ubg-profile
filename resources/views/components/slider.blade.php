@props(['sliders'])

@if($sliders->count() > 0)
<section x-data="{ activeSlide: 0, totalSlides: {{ $sliders->count() }} }" 
         x-init="setInterval(() => activeSlide = (activeSlide + 1) % totalSlides, 5000)"
         class="relative overflow-hidden bg-gray-900">
    <div class="relative h-[400px] md:h-[500px] lg:h-[600px]">
        @foreach($sliders as $index => $slider)
        <div x-show="activeSlide === {{ $index }}"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-full"
             class="absolute inset-0">
            {{-- Background Image --}}
            <div class="absolute inset-0">
                @if($slider->image)
                <img src="{{ Storage::url($slider->image) }}" 
                     alt="{{ $slider->title }}" 
                     class="w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>
            </div>
            
            {{-- Content --}}
            <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
                <div class="max-w-2xl">
                    @if($slider->title)
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                        {{ $slider->title }}
                    </h2>
                    @endif
                    
                    @if($slider->subtitle)
                    <p class="text-lg md:text-xl text-gray-200 mb-8">
                        {{ $slider->subtitle }}
                    </p>
                    @endif
                    
                    @if($slider->link && $slider->button_text)
                    <a href="{{ $slider->link }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                        {{ $slider->button_text }}
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Navigation Arrows --}}
    @if($sliders->count() > 1)
    <button @click="activeSlide = (activeSlide - 1 + totalSlides) % totalSlides" 
            class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-white/20 hover:bg-white/40 text-white transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="activeSlide = (activeSlide + 1) % totalSlides" 
            class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-white/20 hover:bg-white/40 text-white transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    
    {{-- Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2">
        @foreach($sliders as $index => $slider)
        <button @click="activeSlide = {{ $index }}"
                :class="activeSlide === {{ $index }} ? 'bg-white w-8' : 'bg-white/50 w-3'"
                class="h-1 rounded-full transition-all duration-300">
        </button>
        @endforeach
    </div>
    @endif
</section>
@endif
