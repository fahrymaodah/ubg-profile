@props(['announcements', 'show' => true])

@if($show && $announcements->count() > 0)
<div x-data="{ current: 0, announcements: {{ $announcements->count() }} }" 
     x-init="setInterval(() => current = (current + 1) % announcements, 4000)"
     class="bg-blue-500 text-white py-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-4">
            {{-- Label --}}
            <div class="flex-shrink-0 flex items-center">
                <span class="bg-amber-500 px-3 py-1 rounded text-sm font-bold uppercase tracking-wide">
                    Pengumuman
                </span>
            </div>
            
            {{-- Announcements --}}
            <div class="flex-1 overflow-hidden relative h-6">
                @foreach($announcements as $index => $announcement)
                <div x-show="current === {{ $index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-full"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-full"
                     class="absolute inset-0 flex items-center">
                    <a href="#" class="text-sm hover:underline truncate">
                        @if($announcement->priority === 'urgent')
                        <span class="animate-pulse mr-2">🔴</span>
                        @elseif($announcement->priority === 'high')
                        <span class="mr-2">🟠</span>
                        @endif
                        {{ $announcement->title }}
                    </a>
                </div>
                @endforeach
            </div>
            
            {{-- Navigation --}}
            @if($announcements->count() > 1)
            <div class="flex-shrink-0 flex items-center space-x-2">
                <button @click="current = (current - 1 + announcements) % announcements"
                        class="p-1 hover:bg-amber-600 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <span class="text-xs">{{ $announcements->count() }}</span>
                <button @click="current = (current + 1) % announcements"
                        class="p-1 hover:bg-amber-600 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
