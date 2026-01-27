@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- 404 Illustration --}}
        <div class="my-8">
            <svg class="w-64 h-64 mx-auto text-blue-600" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Background Circle --}}
                <circle cx="100" cy="100" r="90" fill="currentColor" fill-opacity="0.1"/>
                
                {{-- 404 Text --}}
                <text x="100" y="110" text-anchor="middle" fill="currentColor" font-size="50" font-weight="bold">404</text>
                
                {{-- Magnifying Glass --}}
                <circle cx="70" cy="150" r="15" stroke="currentColor" stroke-width="3" fill="none"/>
                <line x1="81" y1="161" x2="95" y2="175" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                
                {{-- Question Mark --}}
                <text x="130" y="160" fill="currentColor" font-size="30" font-weight="bold">?</text>
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Halaman Tidak Ditemukan</h1>
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" 
                    class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-gray-400 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </button>
        </div>

        {{-- Quick Links --}}
        <div class="mt-12 mb-5 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-4">Atau kunjungi halaman populer:</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('article.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition text-sm">
                    Berita
                </a>
                <a href="{{ route('event.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition text-sm">
                    Agenda
                </a>
                <a href="{{ route('dosen.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition text-sm">
                    Dosen
                </a>
                <a href="{{ route('contact.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition text-sm">
                    Kontak
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
