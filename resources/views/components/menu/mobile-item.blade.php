@props(['menu', 'level' => 0])

@php
    use App\Enums\MenuType;
    
    $hasChildren = $menu->children->count() > 0;
    $url = $menu->computed_url;
    $target = $menu->target ?? '_self';
    $paddingLeft = $level > 0 ? 'pl-' . ($level * 4) : '';
    
    // Helper function to check if URL matches current path
    $checkUrlActive = function($checkUrl) {
        if (!$checkUrl || $checkUrl === '#') return false;
        $currentPath = request()->path();
        $currentUrl = url()->current();
        $menuPath = parse_url($checkUrl, PHP_URL_PATH) ?? '';
        $menuPath = ltrim($menuPath, '/');
        
        // Handle home page
        if (($currentPath === '' || $currentPath === '/') && ($menuPath === '' || $menuPath === '/')) {
            return true;
        }
        
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
    
    // Active classes
    $activeClass = $isActive ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50';
    $parentActiveClass = $isParentActive ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50';
@endphp

@if($hasChildren || $menu->type === MenuType::DROPDOWN)
    {{-- Mobile Dropdown --}}
    <div x-data="{ subOpen: {{ $isParentActive ? 'true' : 'false' }} }" class="{{ $paddingLeft }}">
        <button 
            @click="subOpen = !subOpen" 
            class="w-full flex items-center justify-between px-4 py-3 {{ $parentActiveClass }} rounded-lg font-medium"
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
            @foreach($menu->children as $child)
                <x-menu.mobile-item :menu="$child" :level="$level + 1" />
            @endforeach
        </div>
    </div>
@elseif($menu->type === MenuType::BUTTON)
    {{-- Mobile Button Link (CTA) - Title dan URL dari database --}}
    <div class="{{ $paddingLeft }}">
        <a 
            href="{{ $url ?? '#' }}" 
            target="{{ $target }}"
            class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
        >
            {{ $menu->title }}
        </a>
    </div>
@elseif($menu->type === MenuType::LOGIN)
    {{-- Mobile Login --}}
    <div class="{{ $paddingLeft }}">
        @auth
            <a href="{{ url('/admin') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">
                Dashboard
            </a>
        @else
            <a href="{{ url('/admin/login') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">
                {{ $menu->title }}
            </a>
        @endauth
    </div>
@else
    {{-- Mobile Regular Link --}}
    <a 
        href="{{ $url ?? '#' }}" 
        target="{{ $target }}"
        class="block px-4 py-3 {{ $activeClass }} hover:text-blue-600 rounded-lg font-medium {{ $paddingLeft }}"
    >
        {{ $menu->title }}
    </a>
@endif
