@extends('layouts.app')

@section('title', '500 - Terjadi Kesalahan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- Error Illustration --}}
        <div class="my-8">
            <svg class="w-64 h-64 mx-auto" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Background Circle --}}
                <circle cx="100" cy="100" r="90" fill="#FEE2E2"/>
                
                {{-- Gear 1 --}}
                <g transform="translate(60, 80)">
                    <circle cx="20" cy="20" r="15" stroke="#EF4444" stroke-width="3" fill="none"/>
                    <circle cx="20" cy="20" r="5" fill="#EF4444"/>
                    {{-- Teeth --}}
                    <rect x="17" y="0" width="6" height="8" fill="#EF4444"/>
                    <rect x="17" y="32" width="6" height="8" fill="#EF4444"/>
                    <rect x="0" y="17" width="8" height="6" fill="#EF4444"/>
                    <rect x="32" y="17" width="8" height="6" fill="#EF4444"/>
                </g>
                
                {{-- Gear 2 --}}
                <g transform="translate(110, 90)">
                    <circle cx="15" cy="15" r="12" stroke="#EF4444" stroke-width="3" fill="none"/>
                    <circle cx="15" cy="15" r="4" fill="#EF4444"/>
                    {{-- Teeth --}}
                    <rect x="12" y="0" width="6" height="6" fill="#EF4444"/>
                    <rect x="12" y="24" width="6" height="6" fill="#EF4444"/>
                    <rect x="0" y="12" width="6" height="6" fill="#EF4444"/>
                    <rect x="24" y="12" width="6" height="6" fill="#EF4444"/>
                </g>
                
                {{-- Lightning bolt --}}
                <path d="M95 50 L85 85 L95 85 L85 110 L115 70 L100 70 L110 50 Z" fill="#EF4444"/>
                
                {{-- 500 Text --}}
                <text x="100" y="160" text-anchor="middle" fill="#EF4444" font-size="24" font-weight="bold">500</text>
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Terjadi Kesalahan</h1>
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            Maaf, terjadi kesalahan pada server kami. Tim teknis kami sedang menangani masalah ini.
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
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-gray-400 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Muat Ulang
            </button>
        </div>

        {{-- Contact Support --}}
        <div class="mt-12 mb-5 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-4">Jika masalah terus berlanjut, silakan hubungi:</p>
            <a href="mailto:{{ $settings['email'] ?? 'support@ubg.ac.id' }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ $settings['email'] ?? 'support@ubg.ac.id' }}
            </a>
        </div>
    </div>
</div>
@endsection
