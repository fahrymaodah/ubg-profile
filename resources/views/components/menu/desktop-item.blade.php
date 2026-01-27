@props(['menu', 'isLast' => false, 'unitType' => null, 'unitId' => null])

@php
    use App\Enums\MenuType;
    use App\Enums\UnitType;
    
    $hasChildren = $menu->children->count() > 0;
    $url = $menu->computed_url;
    $target = $menu->target ?? '_self';
    
    // Check if this is the "Akademik" menu - render with dynamic fakultas/prodi
    $isAkademikMenu = strtolower($menu->title) === 'akademik';
    
    // Get current unit type from props or global context
    $currentUnitType = $unitType ?? ($currentUnitType ?? UnitType::UNIVERSITAS);
    $currentUnitId = $unitId ?? ($currentUnitId ?? null);
    
    // Helper function to check if URL matches current path
    $checkUrlActive = function($checkUrl) {
        if (!$checkUrl || $checkUrl === '#') return false;
        $currentPath = request()->path();
        $currentUrl = url()->current();
        $menuPath = parse_url($checkUrl, PHP_URL_PATH) ?? '';
        $menuPath = ltrim($menuPath, '/');
        
        // Handle home page - both empty string and '/' should match
        if (($currentPath === '' || $currentPath === '/') && ($menuPath === '' || $menuPath === '/')) {
            return true;
        }
        
        // Check exact match or starts with for nested pages
        return ($currentPath === $menuPath) || 
               ($menuPath !== '' && $menuPath !== '/' && str_starts_with($currentPath . '/', $menuPath . '/')) ||
               ($currentUrl === $checkUrl);
    };
    
    // Recursive function to check if menu or any children is active
    $isMenuOrChildActive = function($menuItem) use (&$isMenuOrChildActive, $checkUrlActive) {
        // Check if this menu's URL is active
        if ($checkUrlActive($menuItem->computed_url)) {
            return true;
        }
        // Check children recursively
        foreach ($menuItem->children as $child) {
            if ($isMenuOrChildActive($child)) {
                return true;
            }
        }
        return false;
    };
    
    // Check if this menu item is directly active
    $isActive = $checkUrlActive($url);
    
    // Check if any child is active (for parent highlight)
    $hasActiveChild = false;
    if ($hasChildren) {
        foreach ($menu->children as $child) {
            if ($isMenuOrChildActive($child)) {
                $hasActiveChild = true;
                break;
            }
        }
    }
    
    // Parent is considered active if itself or any child is active
    $isParentActive = $isActive || $hasActiveChild;
@endphp

@if($isAkademikMenu && $currentUnitType !== UnitType::PRODI)
    {{-- Special Akademik Menu with Dynamic Fakultas/Prodi --}}
    <x-menu.akademik-item :menu="$menu" :unitType="$currentUnitType" :unitId="$currentUnitId" :isLast="$isLast" />
@elseif($hasChildren || $menu->type === MenuType::DROPDOWN)
    {{-- Dropdown Menu --}}
    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
        <button 
            class="flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition {{ $isParentActive ? 'nav-item-active' : '' }}"
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
            <div class="w-64 bg-white rounded-xl shadow-xl py-2 border border-gray-100">
                @foreach($menu->children as $child)
                    <x-menu.dropdown-item :menu="$child" :level="1" />
                @endforeach
            </div>
        </div>
    </div>
@elseif($menu->type === MenuType::BUTTON)
    {{-- Button Link (CTA) - Title dan URL dari database --}}
    <a 
        href="{{ $url ?? '#' }}" 
        target="{{ $target }}"
        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition flex items-center"
    >
        {{ $menu->title }}
    </a>
@elseif($menu->type === MenuType::LOGIN)
    {{-- Login Menu --}}
    @auth
        <a href="{{ url('/admin') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition">
            Dashboard
        </a>
    @else
        <a href="{{ url('/admin/login') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition">
            {{ $menu->title }}
        </a>
    @endauth
@else
    {{-- Regular Link --}}
    <a 
        href="{{ $url ?? '#' }}" 
        target="{{ $target }}"
        class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition {{ $isParentActive ? 'nav-item-active' : '' }}"
    >
        {{ $menu->title }}
    </a>
@endif
