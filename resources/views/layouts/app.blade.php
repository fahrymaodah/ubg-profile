<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>@yield('title', ($settings['site_name'] ?? config('app.name', 'UBG Profile')))</title>
    <meta name="title" content="@yield('title', ($settings['site_name'] ?? config('app.name', 'UBG Profile')))">
    <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? 'Universitas Bumigora - Membangun generasi unggul dan berdaya saing global')">
    <meta name="keywords" content="@yield('meta_keywords', $settings['site_keywords'] ?? 'universitas bumigora, ubg, kampus lombok, perguruan tinggi ntb')">
    <meta name="author" content="{{ $settings['site_name'] ?? 'Universitas Bumigora' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook & Twitter Card - use section or default -->
    @hasSection('og_meta')
        @yield('og_meta')
    @else
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', ($settings['site_name'] ?? config('app.name', 'UBG Profile')))">
        <meta property="og:description" content="@yield('meta_description', $settings['site_description'] ?? 'Universitas Bumigora - Membangun generasi unggul dan berdaya saing global')">
        <meta property="og:image" content="{{ isset($settings['og_image']) && $settings['og_image'] ? Storage::url($settings['og_image']) : asset('images/og-default.jpg') }}">
        <meta property="og:site_name" content="{{ $settings['site_name'] ?? 'Universitas Bumigora' }}">
        <meta property="og:locale" content="id_ID">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="@yield('title', ($settings['site_name'] ?? config('app.name', 'UBG Profile')))">
        <meta name="twitter:description" content="@yield('meta_description', $settings['site_description'] ?? 'Universitas Bumigora - Membangun generasi unggul dan berdaya saing global')">
        <meta name="twitter:image" content="{{ isset($settings['og_image']) && $settings['og_image'] ? Storage::url($settings['og_image']) : asset('images/og-default.jpg') }}">
        @if(isset($settings['twitter_handle']))
        <meta name="twitter:site" content="{{ $settings['twitter_handle'] }}">
        @endif
    @endif

    <!-- Additional page-specific meta tags -->
    @stack('meta')

    <!-- Favicon (cascading: prodi → fakultas → universitas → default) -->
    <link rel="icon" type="image/png" href="{{ $logos['favicon'] ?? asset('images/logo-ubg.png') }}">
    <link rel="apple-touch-icon" href="{{ $logos['favicon'] ?? asset('images/logo-ubg.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if(isset($fontsLink) && $fontsLink)
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontsLink }}" rel="stylesheet">
    @endif

    <!-- Alpine.js with plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Theme Colors & Fonts -->
    @if(($settings['theme_color_primary'] ?? null) || ($settings['theme_color_secondary'] ?? null) || ($settings['theme_color_accent'] ?? null) || ($settings['theme_font_heading'] ?? null) || ($settings['theme_font_body'] ?? null))
    <style>
        :root {
            @if($settings['theme_color_primary'] ?? null)--color-primary: {{ $settings['theme_color_primary'] }};@endif
            @if($settings['theme_color_secondary'] ?? null)--color-secondary: {{ $settings['theme_color_secondary'] }};@endif
            @if($settings['theme_color_accent'] ?? null)--color-accent: {{ $settings['theme_color_accent'] }};@endif
        }
        @if($p = $settings['theme_color_primary'] ?? null)
        /* Primary: tombol, link, gradient */
        .bg-blue-500, .bg-blue-600, .bg-blue-700, .bg-blue-800,
        .bg-indigo-600, .bg-indigo-700 { background-color: {{ $p }} !important; }
        /* bg-blue-50/100 tidak di-override agar navbar hover tetap default */
        .text-blue-500, .text-blue-600, .text-blue-700, .text-blue-800,
        .text-indigo-600 { color: {{ $p }} !important; }
        .border-blue-500, .border-blue-600 { border-color: {{ $p }} !important; }
        .from-blue-500, .from-blue-600, .from-blue-700 { --tw-gradient-from: {{ $p }} !important; }
        .to-blue-600, .to-blue-700, .to-blue-800 { --tw-gradient-to: {{ $p }} !important; }
        .ring-blue-500 { --tw-ring-color: {{ $p }} !important; }
        *[class*="focus:ring-blue-"]:focus { --tw-ring-color: {{ $p }} !important; }
        *[class*="focus:border-blue-"]:focus { border-color: {{ $p }} !important; }
        /* Hover text */
        *[class*="hover:text-blue-"]:hover { color: {{ $p }} !important; }
        *[class*="hover:border-blue-"]:hover { border-color: {{ $p }} !important; }
        /* Group hover untuk card titles */
        .group:hover .group-hover\:text-blue-600 { color: {{ $p }} !important; }
        .group:hover .group-hover\:text-blue-700 { color: {{ $p }} !important; }
        /* Hover background - hanya untuk tombol dengan bg solid (bukan bg-blue-50) */
        a.bg-blue-600:hover, a.bg-blue-700:hover,
        button.bg-blue-600:hover, button.bg-blue-700:hover { background-color: {{ $p }} !important; filter: brightness(0.9); }
        /* Menu aktif navbar - warna primary tanpa garis bawah */
        .nav-item-active { color: {{ $p }} !important; font-weight: 600; }
        .nav-dropdown-active { color: {{ $p }} !important; background-color: {{ $p }}10 !important; }
        @endif
        @if($s = $settings['theme_color_secondary'] ?? null)
        /* Secondary: variasi, ikon */
        .bg-purple-500, .bg-purple-600, .bg-purple-700 { background-color: {{ $s }} !important; }
        .bg-purple-50, .bg-purple-100 { background-color: {{ $s }}15 !important; }
        .text-purple-600, .text-purple-700 { color: {{ $s }} !important; }
        @endif
        @if($a = $settings['theme_color_accent'] ?? null)
        /* Accent: badge, highlight */
        .bg-amber-500, .bg-amber-600, .bg-yellow-500 { background-color: {{ $a }} !important; }
        .bg-amber-50, .bg-amber-100, .bg-yellow-100 { background-color: {{ $a }}20 !important; }
        .text-amber-500, .text-amber-600, .text-yellow-500 { color: {{ $a }} !important; }
        .border-amber-500 { border-color: {{ $a }} !important; }
        @endif
        @if($fh = $settings['theme_font_heading'] ?? null)
        h1, h2, h3, h4, h5, h6 { font-family: "{{ $fh }}", sans-serif !important; }
        @endif
        @if($fb = $settings['theme_font_body'] ?? null)
        body { font-family: "{{ $fb }}", sans-serif !important; }
        @endif
    </style>
    @endif

    <!-- Custom CSS Injection -->
    @if(isset($customCss) && $customCss)
    <style>
        {!! $customCss !!}
    </style>
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 flex flex-col font-sans antialiased text-gray-900">
    @include('layouts.partials.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <!-- Floating WhatsApp Button -->
    @php
        $showWhatsApp = $settings['show_floating_whatsapp'] ?? true;
        $showWhatsApp = $showWhatsApp === true || $showWhatsApp === 'true' || $showWhatsApp === '1' || $showWhatsApp === 1;
    @endphp
    @if($showWhatsApp && ($settings['whatsapp'] ?? false))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text=Halo%2C%20saya%20ingin%20bertanya" 
       target="_blank" 
       rel="noopener noreferrer"
       class="fixed bottom-6 z-50 text-white rounded-full p-3 shadow-lg transition-transform duration-300 hover:scale-110 flex items-center justify-center"
       style="right: 1.5rem; background-color: #25D366;"
       onmouseover="this.style.backgroundColor='#128C7E'" 
       onmouseout="this.style.backgroundColor='#25D366'"
       title="Chat dengan WhatsApp">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 6.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    @endif

    <!-- Back to Top Button -->
    @php
        $showBackToTopSetting = $settings['show_back_to_top'] ?? true;
        $showBackToTopSetting = $showBackToTopSetting === true || $showBackToTopSetting === 'true' || $showBackToTopSetting === '1' || $showBackToTopSetting === 1;
    @endphp
    @if($showBackToTopSetting)
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            id="backToTopBtn"
            class="fixed z-50 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg transition-all duration-300 hover:scale-110 items-center justify-center"
            style="display: none; bottom: 5rem; right: 1.5rem;"
            title="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
    <script>
        (function() {
            var btn = document.getElementById('backToTopBtn');
            if (btn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        btn.style.display = 'flex';
                    } else {
                        btn.style.display = 'none';
                    }
                });
            }
        })();
    </script>
    @endif

    <!-- Custom JavaScript Injection -->
    @if(isset($customJs) && $customJs)
    <script>
        {!! $customJs !!}
    </script>
    @endif

    @stack('scripts')
</body>
</html>
