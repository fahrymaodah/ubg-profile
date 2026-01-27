<footer class="bg-gray-900 text-white mt-auto">
    {{-- Main Footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            {{-- About --}}
            <div class="lg:col-span-1">
                <div class="flex items-center mb-4">
                    {{-- Logo dark (cascading: prodi → fakultas → universitas → default) --}}
                    <img src="{{ $logos['logo_dark'] ?? asset('images/logo-ubg-label-white.png') }}" alt="Logo" class="h-16 w-auto mr-3 rounded-full p-2">
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">
                    {{ $settings['site_description'] ?? 'Universitas Bumigora - Membangun generasi unggul dan berdaya saing global melalui pendidikan berkualitas.' }}
                </p>
                {{-- Social Media --}}
                <div class="flex space-x-3">
                    @if($settings['facebook'] ?? false)
                    <a href="{{ $settings['facebook'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.backgroundColor='#1877F2'" onmouseout="this.style.backgroundColor=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                    @if($settings['instagram'] ?? false)
                    <a href="{{ $settings['instagram'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.background='linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)'" onmouseout="this.style.background=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    @endif
                    @if($settings['youtube'] ?? false)
                    <a href="{{ $settings['youtube'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.backgroundColor='#FF0000'" onmouseout="this.style.backgroundColor=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if($settings['twitter'] ?? false)
                    <a href="{{ $settings['twitter'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.backgroundColor='#000000'" onmouseout="this.style.backgroundColor=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    @endif
                    @if($settings['tiktok'] ?? false)
                    <a href="{{ $settings['tiktok'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.backgroundColor='#000000'" onmouseout="this.style.backgroundColor=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.08 1.61 2.88 2.88 0 0 1 4.07-4.77v-3.4a6.32 6.32 0 0 0-6.32 6.32 6.46 6.46 0 0 0 6.32 6.32 6.37 6.37 0 0 0 6.32-6.32V9.74a8.13 8.13 0 0 0 5.23 1.91v-3.4a4.85 4.85 0 0 1-3.77-1.77Z"/></svg>
                    </a>
                    @endif
                    @if($settings['linkedin'] ?? false)
                    <a href="{{ $settings['linkedin'] }}" target="_blank" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center transition" onmouseover="this.style.backgroundColor='#0A66C2'" onmouseout="this.style.backgroundColor=''">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-1">
                <h5 class="text-md font-semibold mb-5 text-white">Menu Utama</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Beranda</a></li>
                    <li><a href="{{ route('article.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Berita</a></li>
                    <li><a href="{{ route('event.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Agenda</a></li>
                    <li><a href="{{ route('prestasi.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Prestasi</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Galeri</a></li>
                    <li><a href="{{ route('download.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Unduhan</a></li>
                </ul>
            </div>

            {{-- Profil --}}
            <div class="lg:col-span-1">
                <h5 class="text-md font-semibold mb-5 text-white">Profil</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('profil.visi-misi') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Visi & Misi</a></li>
                    <li><a href="{{ route('profil.sejarah') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Sejarah</a></li>
                    <li><a href="{{ route('profil.struktur') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Struktur Organisasi</a></li>
                    <li><a href="{{ route('dosen.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Dosen</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-gray-400 hover:text-white text-sm transition flex items-center"><span class="mr-2">→</span> Kontak</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="lg:col-span-1">
                <h5 class="text-md font-semibold mb-5 text-white">Hubungi Kami</h5>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $settings['address'] ?? 'Jl. Ismail Marzuki No.22, Cilinaya, Kec. Cakranegara, Kota Mataram, NTB 83239' }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $settings['email'] ?? 'info@ubg.ac.id' }}" class="hover:text-white transition">{{ $settings['email'] ?? 'info@ubg.ac.id' }}</a>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['phone'] ?? '0370638885') }}" class="hover:text-white transition">{{ $settings['phone'] ?? '(0370) 638885' }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom Footer --}}
    <div class="bg-gray-950 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between text-gray-500 text-sm">
                <p>{{ $settings['footer_text_left'] ?? '© ' . date('Y') . ' ' . ($settings['site_name'] ?? 'Universitas Bumigora') . '. All rights reserved.' }}</p>
                <p class="mt-2 md:mt-0">{{ $settings['footer_text_right'] ?? 'Developed with ❤️ by PUSTIK UBG' }}</p>
            </div>
        </div>
    </div>
</footer>
