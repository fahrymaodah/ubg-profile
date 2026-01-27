@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- 403 Illustration --}}
        <div class="my-8">
            <svg class="w-64 h-64 mx-auto" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Background Circle --}}
                <circle cx="100" cy="100" r="90" fill="#FEF3C7"/>
                
                {{-- Lock --}}
                <rect x="70" y="90" width="60" height="50" rx="5" fill="#F59E0B" stroke="#D97706" stroke-width="3"/>
                <path d="M80 90 V70 A20 20 0 0 1 120 70 V90" stroke="#D97706" stroke-width="6" fill="none" stroke-linecap="round"/>
                <circle cx="100" cy="115" r="8" fill="#FEF3C7"/>
                <rect x="97" y="115" width="6" height="15" rx="2" fill="#FEF3C7"/>
                
                {{-- 403 Text --}}
                <text x="100" y="170" text-anchor="middle" fill="#D97706" font-size="24" font-weight="bold">403</text>
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Akses Ditolak</h1>
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>

        {{-- Actions --}}
        <div class="mb-5 flex flex-col sm:flex-row items-center justify-center gap-4">
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
    </div>
</div>
@endsection
