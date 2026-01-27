@extends('layouts.app')

@section('title', 'Visi & Misi - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
@php
    // Get profil data from unit model or settings
    $visi = $unit?->visi ?? Setting::getValue('profil_visi') ?? '';
    $misiRaw = $unit?->misi ?? Setting::getValue('profil_misi') ?? '[]';
    $tujuanRaw = $unit?->tujuan ?? Setting::getValue('profil_tujuan') ?? '[]';
    
    // Parse repeater data if stored as JSON
    $misiItems = is_string($misiRaw) ? (json_decode($misiRaw, true) ?? []) : ($misiRaw ?? []);
    $tujuanItems = is_string($tujuanRaw) ? (json_decode($tujuanRaw, true) ?? []) : ($tujuanRaw ?? []);
@endphp
<div class="bg-white">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Beranda', 'url' => route('home')], ['label' => 'Profil'], ['label' => 'Visi & Misi']]" :wrapper="false" />
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Visi & Misi</h1>
            <p class="text-xl text-blue-200">{{ $settings['site_name'] ?? config('app.name') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Visi --}}
            <div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 h-full">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Visi</h2>
                    </div>
                    <div class="prose prose-lg prose-blue max-w-none">
                        @if(!empty($visi))
                            <div class="text-2xl text-gray-700 border-l-4 border-blue-500 pl-6 my-6">
                                {!! $visi !!}
                            </div>
                        @else
                            <p class="text-gray-500 italic">Visi belum diatur.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Misi --}}
            <div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 h-full">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Misi</h2>
                    </div>
                    @if(!empty($misiItems) && is_array($misiItems))
                        <div class="space-y-3">
                            @foreach($misiItems as $index => $misi)
                                <div class="flex gap-4 text-gray-700">
                                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-green-600 text-white rounded-full text-md font-bold">{{ $index + 1 }}</span>
                                    <span class="flex-1 text-xl">{{ $misi['item'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Misi belum diatur.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tujuan --}}
        @if(!empty($tujuanItems) && is_array($tujuanItems))
        <div class="mt-12">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Tujuan</h2>
                </div>
                <div class="space-y-3">
                    @foreach($tujuanItems as $index => $tujuan)
                        <div class="flex gap-4 text-gray-700">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-purple-600 text-white rounded-full text-sm font-bold">{{ $index + 1 }}</span>
                            <span class="flex-1">{{ $tujuan['item'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Nilai-nilai Inti --}}
        <div class="mt-12">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">Nilai-Nilai Inti</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Integritas</h3>
                    <p class="text-sm text-gray-600">Jujur dan bertanggung jawab</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Inovasi</h3>
                    <p class="text-sm text-gray-600">Kreatif dan adaptif</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Kolaborasi</h3>
                    <p class="text-sm text-gray-600">Sinergi dan kerjasama</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Keunggulan</h3>
                    <p class="text-sm text-gray-600">Berkualitas dan unggul</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
