{{-- 
    Komponen Menu Akademik Dinamis
    Render fakultas dan prodi berdasarkan data dari database
    
    Props:
    - $menu: Menu model untuk Akademik
    - $unitType: UnitType current website
    - $unitId: ID unit (fakultas_id jika web fakultas)
    - $isLast: Apakah menu terakhir (untuk positioning)
--}}

@props(['menu', 'unitType', 'unitId' => null, 'isLast' => false])

@php
    use App\Enums\UnitType;
    use App\Services\MenuService;
    
    $menuService = app(MenuService::class);
    $akademikItems = $menuService->getAkademikMenuStructure($unitType, $unitId);
    
    // Get static children from database (Kurikulum, Kalender Akademik, etc)
    $staticChildren = $menu->children ?? collect();
@endphp

<div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <button 
        class="flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition"
    >
        {{ $menu->title }}
        <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    {{-- Dropdown Content --}}
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute {{ $isLast ? 'right-0' : 'left-0' }} top-full pt-2 z-50"
        style="display: none;"
    >
        <div class="min-w-72 bg-white rounded-xl shadow-xl py-2 border border-gray-100">
            {{-- Dynamic Fakultas/Prodi Items --}}
            @foreach($akademikItems as $item)
                @if(!empty($item['children']))
                    {{-- Fakultas with Prodi (has children) - Click goes to fakultas, hover shows prodi --}}
                    <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                        <a 
                            href="{{ $item['url'] }}" 
                            target="{{ $item['target'] ?? '_blank' }}"
                            class="w-full flex items-center justify-between px-4 py-2.5 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition"
                        >
                            <span class="whitespace-nowrap">{{ $item['title'] }}</span>
                            <svg class="w-4 h-4 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        
                        {{-- Prodi Sub-dropdown (appears on hover) --}}
                        <div 
                            x-show="subOpen" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 translate-x-2"
                            class="absolute top-0 z-[70]"
                            style="display: none; left: calc(100% + 12px);"
                        >
                            <div class="min-w-64 bg-white rounded-xl shadow-xl py-2 border border-gray-100">
                                {{-- Prodi list --}}
                                @foreach($item['children'] as $prodi)
                                    <a 
                                        href="{{ $prodi['url'] }}" 
                                        target="{{ $prodi['target'] ?? '_blank' }}"
                                        class="block px-4 py-2.5 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition whitespace-nowrap"
                                    >
                                        {{ $prodi['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Single Item (Prodi only for Fakultas website, or item without children) --}}
                    <a 
                        href="{{ $item['url'] }}" 
                        target="{{ $item['target'] ?? '_blank' }}"
                        class="block px-4 py-2.5 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition whitespace-nowrap"
                    >
                        {{ $item['title'] }}
                    </a>
                @endif
            @endforeach
            
            {{-- Divider if there are both dynamic and static items --}}
            @if(count($akademikItems) > 0 && $staticChildren->count() > 0)
                <div class="border-t border-gray-100 my-2"></div>
            @endif
            
            {{-- Static Items from Database (Kurikulum, Kalender Akademik, etc) --}}
            @foreach($staticChildren as $child)
                <x-menu.dropdown-item :menu="$child" :level="1" />
            @endforeach
        </div>
    </div>
</div>
