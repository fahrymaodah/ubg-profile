{{-- Navbar dengan Multi-Level Menu Dinamis --}}
<nav class="bg-white shadow-lg sticky top-0 z-50" x-data="{ open: false, searchOpen: false }">
    {{-- Top Bar - uses primary color from theme --}}
    <div class="text-white py-2 hidden md:block" style="background-color: var(--color-primary, #1e40af);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center space-x-6">
                    @if($settings['email'] ?? false)
                    <a href="mailto:{{ $settings['email'] }}" class="flex items-center opacity-90 hover:opacity-100 hover:underline transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $settings['email'] }}
                    </a>
                    @endif
                    @if($settings['phone'] ?? false)
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['phone']) }}" class="flex items-center opacity-90 hover:opacity-100 hover:underline transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $settings['phone'] }}
                    </a>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    @if($settings['facebook'] ?? false)
                    <a href="{{ $settings['facebook'] }}" target="_blank" class="opacity-90 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                    @if($settings['instagram'] ?? false)
                    <a href="{{ $settings['instagram'] }}" target="_blank" class="opacity-90 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    @endif
                    @if($settings['youtube'] ?? false)
                    <a href="{{ $settings['youtube'] }}" target="_blank" class="opacity-90 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if($settings['linkedin'] ?? false)
                    <a href="{{ $settings['linkedin'] }}" target="_blank" class="opacity-90 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    @endif
                    <span class="ms-2 me-3 opacity-50">|</span>
                    {{-- Admin panel accessible via /admin on any subdomain --}}
                    @auth
                    <a href="{{ url('/admin') }}" class="opacity-90 hover:opacity-100 hover:underline transition">Dashboard</a>
                    @else
                    <a href="{{ url('/admin/login') }}" class="opacity-90 hover:opacity-100 hover:underline transition">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo (cascading: prodi → fakultas → universitas → default) --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ $logos['logo'] ?? asset('images/logo-ubg-label.png') }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" class="h-14 w-auto">
                </a>
            </div>

            {{-- Desktop Menu (Dinamis dari database) --}}
            <div class="hidden lg:flex items-center space-x-1">
                @if(isset($mainMenu) && $mainMenu->count() > 0)
                    @php
                        // Get unit context for dynamic akademik menu
                        $navUnitType = $currentUnitType ?? \App\Enums\UnitType::UNIVERSITAS;
                        $navUnitId = $currentUnitId ?? null;
                    @endphp
                    @foreach($mainMenu as $index => $menu)
                        <x-menu.desktop-item 
                            :menu="$menu" 
                            :isLast="$loop->last || $loop->index >= $mainMenu->count() - 2"
                            :unitType="$navUnitType"
                            :unitId="$navUnitId"
                        />
                    @endforeach
                @else
                    {{-- Fallback jika menu kosong --}}
                    <a href="{{ route('home') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : '' }}">Beranda</a>
                    <a href="{{ route('article.index') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition">Berita</a>
                    <a href="{{ route('event.index') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition">Agenda</a>
                    <a href="{{ route('contact.index') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition">Kontak</a>
                @endif
            </div>

            {{-- Search and Mobile Toggle --}}
            <div class="flex items-center space-x-2">
                <button @click="searchOpen = !searchOpen" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button @click="open = !open" class="lg:hidden p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Search Dropdown --}}
    <div x-show="searchOpen" x-transition @click.away="searchOpen = false" class="absolute inset-x-0 top-full bg-white shadow-lg border-t py-4" style="display: none;">
        <div class="max-w-2xl mx-auto px-4">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input type="text" name="q" placeholder="Cari berita, agenda, dosen..." class="w-full px-5 py-3 pr-12 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" autofocus>
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-gray-500 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Mobile Menu (Dinamis dari database) --}}
    <div x-show="open" x-transition class="lg:hidden bg-white border-t shadow-lg" style="display: none;">
        <div class="px-4 py-4 space-y-2 max-h-[70vh] overflow-y-auto">
            @if(isset($mainMenu) && $mainMenu->count() > 0)
                @php
                    $navUnitType = $currentUnitType ?? \App\Enums\UnitType::UNIVERSITAS;
                    $navUnitId = $currentUnitId ?? null;
                @endphp
                @foreach($mainMenu as $menu)
                    @if(strtolower($menu->title) === 'akademik' && $navUnitType !== \App\Enums\UnitType::PRODI)
                        {{-- Special Akademik Menu with Dynamic Fakultas/Prodi --}}
                        <x-menu.mobile-akademik-item :menu="$menu" :unitType="$navUnitType" :unitId="$navUnitId" />
                    @else
                        <x-menu.mobile-item :menu="$menu" />
                    @endif
                @endforeach
            @else
                {{-- Fallback jika menu kosong --}}
                <a href="{{ route('home') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">Beranda</a>
                <a href="{{ route('article.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">Berita</a>
                <a href="{{ route('event.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">Agenda</a>
                <a href="{{ route('contact.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium">Kontak</a>
                <div class="pt-4 border-t">
                    @php
                        $adminUrl = 'https://profil.' . config('app.domain', 'ubg.ac.id') . '/admin';
                    @endphp
                    @auth
                    <a href="{{ $adminUrl }}" class="block px-4 py-3 bg-blue-600 text-white text-center rounded-lg font-medium hover:bg-blue-700 transition">Dashboard</a>
                    @else
                    <a href="{{ $adminUrl }}/login" class="block px-4 py-3 bg-blue-600 text-white text-center rounded-lg font-medium hover:bg-blue-700 transition">Login</a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</nav>
