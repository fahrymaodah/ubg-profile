@props(['menu', 'level' => 1])

@php
    use App\Enums\MenuType;
    
    $hasChildren = $menu->children->count() > 0;
    $url = $menu->computed_url;
    $target = $menu->target ?? '_self';
    
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
        if ($checkUrlActive($menuItem->computed_url)) {
            return true;
        }
        foreach ($menuItem->children as $child) {
            if ($isMenuOrChildActive($child)) {
                return true;
            }
        }
        return false;
    };
    
    $isActive = $checkUrlActive($url);
    $hasActiveChild = false;
    if ($hasChildren) {
        foreach ($menu->children as $child) {
            if ($isMenuOrChildActive($child)) {
                $hasActiveChild = true;
                break;
            }
        }
    }
    $isParentActive = $isActive || $hasActiveChild;
@endphp

@if($hasChildren || $menu->type === MenuType::DROPDOWN)
    {{-- Nested Dropdown (Sub-sub menu) --}}
    <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
        <button 
            class="w-full flex items-center justify-between px-4 py-2.5 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ $isParentActive ? 'nav-dropdown-active' : '' }}"
        >
            <span class="text-left whitespace-nowrap">{{ $menu->title }}</span>
            <svg class="w-4 h-4 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        
        {{-- Sub Dropdown (appears on right side with gap) --}}
        <div 
            x-show="subOpen" 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-x-2"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-2"
            class="absolute top-0 z-[70]"
            style="display: none; left: calc(100% + 5px);"
        >
            <div class="w-64 bg-white rounded-xl shadow-xl py-2 border border-gray-100">
                @foreach($menu->children as $child)
                    @if($child->children->count() > 0)
                        {{-- Recursive for deeper levels --}}
                        <x-menu.dropdown-item :menu="$child" :level="$level + 1" />
                    @else
                        {{-- Final level item --}}
                        @php
                            $childActive = $checkUrlActive($child->computed_url);
                        @endphp
                        <a 
                            href="{{ $child->computed_url ?? '#' }}" 
                            target="{{ $child->target ?? '_self' }}"
                            class="block px-4 py-2 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition whitespace-nowrap {{ $childActive ? 'nav-dropdown-active' : '' }}"
                        >
                            {{ $child->title }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@elseif($menu->type === MenuType::LOGIN)
    {{-- Login item in dropdown --}}
    @auth
        <a href="{{ url('/admin') }}" class="block px-4 py-2 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
            Dashboard
        </a>
    @else
        <a href="{{ url('/admin/login') }}" class="block px-4 py-2 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
            {{ $menu->title }}
        </a>
    @endauth
@else
    {{-- Regular link item --}}
    <a 
        href="{{ $url ?? '#' }}" 
        target="{{ $target }}"
        class="block px-4 py-2.5 text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition whitespace-nowrap {{ $isActive ? 'nav-dropdown-active' : '' }}"
    >
        {{ $menu->title }}
    </a>
@endif
