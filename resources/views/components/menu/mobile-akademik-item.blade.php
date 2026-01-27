{{-- 
    Komponen Mobile Menu Akademik Dinamis
    Render fakultas dan prodi untuk mobile navbar
--}}

@props(['menu', 'unitType', 'unitId' => null])

@php
    use App\Enums\UnitType;
    use App\Services\MenuService;
    
    $menuService = app(MenuService::class);
    $akademikItems = $menuService->getAkademikMenuStructure($unitType, $unitId);
    
    // Get static children from database (Kurikulum, Kalender Akademik, etc)
    $staticChildren = $menu->children ?? collect();
@endphp

<div x-data="{ subOpen: false }">
    <button 
        @click="subOpen = !subOpen" 
        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg font-medium"
    >
        <span>{{ $menu->title }}</span>
        <svg 
            :class="{ 'rotate-180': subOpen }" 
            class="w-4 h-4 transition-transform" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div 
        x-show="subOpen" 
        x-collapse 
        class="pl-4 space-y-1 mt-1"
    >
        {{-- Dynamic Fakultas/Prodi Items --}}
        @foreach($akademikItems as $item)
            @if(!empty($item['children']))
                {{-- Fakultas with Prodi (has children) --}}
                <div x-data="{ prodiOpen: false }">
                    <button 
                        @click="prodiOpen = !prodiOpen" 
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg font-medium text-sm"
                    >
                        <span>{{ $item['title'] }}</span>
                        <svg 
                            :class="{ 'rotate-180': prodiOpen }" 
                            class="w-4 h-4 transition-transform" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="prodiOpen" x-collapse class="pl-4 space-y-1 mt-1">
                        {{-- Link to Fakultas --}}
                        <a 
                            href="{{ $item['url'] }}" 
                            target="{{ $item['target'] ?? '_blank' }}"
                            class="block px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg text-sm font-medium"
                        >
                            → Kunjungi {{ $item['title'] }}
                        </a>
                        
                        {{-- Prodi list --}}
                        @foreach($item['children'] as $prodi)
                            <a 
                                href="{{ $prodi['url'] }}" 
                                target="{{ $prodi['target'] ?? '_blank' }}"
                                class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg text-sm"
                            >
                                {{ $prodi['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Single Item (Prodi only for Fakultas website) --}}
                <a 
                    href="{{ $item['url'] }}" 
                    target="{{ $item['target'] ?? '_blank' }}"
                    class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium text-sm"
                >
                    {{ $item['title'] }}
                </a>
            @endif
        @endforeach
        
        {{-- Divider if there are both dynamic and static items --}}
        @if(count($akademikItems) > 0 && $staticChildren->count() > 0)
            <div class="border-t border-gray-100 my-2 mx-4"></div>
        @endif
        
        {{-- Static Items from Database (Kurikulum, Kalender Akademik, etc) --}}
        @foreach($staticChildren as $child)
            <x-menu.mobile-item :menu="$child" :level="1" />
        @endforeach
    </div>
</div>
