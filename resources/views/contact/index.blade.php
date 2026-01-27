@extends('layouts.app')

@section('title', 'Kontak - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[['label' => 'Kontak']]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Hubungi Kami</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Silakan hubungi kami untuk informasi lebih lanjut
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Contact Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name --}}
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nama"
                                   name="nama" 
                                   value="{{ old('nama') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nama lengkap Anda">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email"
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="email@example.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Phone --}}
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
                                No. Telepon
                            </label>
                            <input type="tel" 
                                   id="telepon"
                                   name="telepon" 
                                   value="{{ old('telepon') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="08123456789">
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subjek" class="block text-sm font-medium text-gray-700 mb-2">
                                Subjek <span class="text-red-500">*</span>
                            </label>
                            <select id="subjek"
                                    name="subjek" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih Subjek</option>
                                <option value="informasi_umum" {{ old('subjek') == 'informasi_umum' ? 'selected' : '' }}>Informasi Umum</option>
                                <option value="pendaftaran" {{ old('subjek') == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran Mahasiswa Baru</option>
                                <option value="akademik" {{ old('subjek') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                <option value="kerjasama" {{ old('subjek') == 'kerjasama' ? 'selected' : '' }}>Kerjasama</option>
                                <option value="lainnya" {{ old('subjek') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="pesan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="pesan"
                                  name="pesan" 
                                  rows="5"
                                  required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                                  placeholder="Tulis pesan Anda...">{{ old('pesan') }}</textarea>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" 
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Address Card --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Kontak</h3>
                <div class="space-y-4">
                    {{-- Address --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Alamat</p>
                            <p class="font-medium text-gray-900">{{ $settings['address'] ?? 'Jl. Ismail Marzuki No.22, Cilinaya, Kec. Cakranegara, Kota Mataram, NTB 83239' }}</p>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telepon</p>
                            <a href="tel:{{ $settings['phone'] ?? '0370638885' }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $settings['phone'] ?? '(0370) 638885' }}
                            </a>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <a href="mailto:{{ $settings['email'] ?? 'info@ubg.ac.id' }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $settings['email'] ?? 'info@ubg.ac.id' }}
                            </a>
                        </div>
                    </div>

                    {{-- Working Hours --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jam Operasional</p>
                            <p class="font-medium text-gray-900">Senin - Jumat: 08:00 - 16:00</p>
                            <p class="font-medium text-gray-900">Sabtu: 08:00 - 12:00</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Media Sosial</h3>
                <div class="flex space-x-3">
                    @if($settings['facebook'] ?? false)
                    <a href="{{ $settings['facebook'] }}" target="_blank" 
                       class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    @endif
                    @if($settings['instagram'] ?? false)
                    <a href="{{ $settings['instagram'] }}" target="_blank"
                       class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 text-white rounded-lg flex items-center justify-center hover:opacity-90 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    @endif
                    @if($settings['twitter'] ?? false)
                    <a href="{{ $settings['twitter'] }}" target="_blank"
                       class="w-10 h-10 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    @endif
                    @if($settings['youtube'] ?? false)
                    <a href="{{ $settings['youtube'] }}" target="_blank"
                       class="w-10 h-10 bg-red-600 text-white rounded-lg flex items-center justify-center hover:bg-red-700 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    @endif
                    @if($settings['linkedin'] ?? false)
                    <a href="{{ $settings['linkedin'] }}" target="_blank"
                       class="w-10 h-10 bg-blue-700 text-white rounded-lg flex items-center justify-center hover:bg-blue-800 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    @endif
                    @if($settings['tiktok'] ?? false)
                    <a href="{{ $settings['tiktok'] }}" target="_blank"
                       class="w-10 h-10 bg-black text-white rounded-lg flex items-center justify-center hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.08 1.61 2.88 2.88 0 0 1 4.07-4.77v-3.4a6.32 6.32 0 0 0-6.32 6.32 6.46 6.46 0 0 0 6.32 6.32 6.37 6.37 0 0 0 6.32-6.32V9.74a8.13 8.13 0 0 0 5.23 1.91v-3.4a4.85 4.85 0 0 1-3.77-1.77Z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick Contact --}}
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-bold mb-3">Butuh Bantuan Cepat?</h3>
                <p class="text-sm opacity-90 mb-4">
                    Hubungi kami langsung melalui WhatsApp untuk respon yang lebih cepat.
                </p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '6281234567890') }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 text-white font-semibold rounded-lg transition"
                   style="background-color: #25D366;"
                   onmouseover="this.style.backgroundColor='#128C7E'" 
                   onmouseout="this.style.backgroundColor='#25D366'">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Chat WhatsApp
                </a>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="mt-12">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">Lokasi Kami</h2>
                @if($settings['maps_link'] ?? false)
                <a href="{{ $settings['maps_link'] }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Buka di Google Maps
                </a>
                @endif
            </div>
            <div class="h-96">
                <iframe 
                    src="{{ $settings['maps_embed'] ?? ($settings['google_maps_embed'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.7392!2d116.1306!3d-8.5833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOMKwMzUnMDAuMCJTIDExNsKwMDcnNTAuMiJF!5e0!3m2!1sen!2sid!4v1234567890') }}"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endsection
