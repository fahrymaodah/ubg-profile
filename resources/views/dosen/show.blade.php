@extends('layouts.app')

@section('title', $dosen->nama_lengkap . ' - Dosen - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Dosen', 'url' => route('dosen.index')],
    ['label' => $dosen->nama_lengkap]
]" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Profile Header --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 h-32"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end -mt-[80px]">
                {{-- Photo --}}
                <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                    @if($dosen->foto)
                    <img src="{{ Storage::url($dosen->foto) }}" 
                         alt="{{ $dosen->nama_lengkap }}"
                         class="w-32 h-32 rounded-xl border-4 border-white shadow-lg object-cover object-top">
                    @else
                    <div class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                
                {{-- Info --}}
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 md:text-white">{{ $dosen->nama_lengkap }}</h1>
                    <p class="text-gray-600">NIDN: {{ $dosen->nidn }}</p>
                    @if($dosen->prodi)
                    <p class="text-blue-600 font-medium">{{ $dosen->prodi->nama }}</p>
                    @endif
                </div>
                
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
                    @if($dosen->jabatan_fungsional)
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                        {{ $dosen->jabatan_fungsional }}
                    </span>
                    @endif
                    @if($dosen->golongan)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                        {{ $dosen->golongan }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Bio --}}
            @if($dosen->bio)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Biografi</h2>
                <div class="prose prose-gray max-w-none">
                    {!! nl2br(e($dosen->bio)) !!}
                </div>
            </div>
            @endif

            {{-- Pendidikan --}}
            @if($dosen->pendidikan && count($dosen->pendidikan) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Riwayat Pendidikan</h2>
                <div class="space-y-4">
                    @foreach($dosen->pendidikan as $edu)
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">{{ $edu['jenjang'] ?? '-' }}</h3>
                                <span class="text-sm text-gray-500">{{ $edu['tahun'] ?? '-' }}</span>
                            </div>
                            <p class="text-gray-600">{{ $edu['institusi'] ?? '-' }}</p>
                            @if(isset($edu['bidang']))
                            <p class="text-sm text-gray-500">{{ $edu['bidang'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Penelitian --}}
            @if($dosen->penelitian && count($dosen->penelitian) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Penelitian</h2>
                <div class="space-y-4">
                    @foreach($dosen->penelitian as $item)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $item['judul'] ?? '-' }}</h3>
                                <p class="text-sm text-gray-500">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                            </div>
                            @if(isset($item['link']) && $item['link'])
                            <a href="{{ $item['link'] }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Publikasi --}}
            @if($dosen->publikasi && count($dosen->publikasi) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Publikasi</h2>
                <div class="space-y-4">
                    @foreach($dosen->publikasi as $item)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $item['judul'] ?? '-' }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $item['jurnal'] ?? '-' }} • {{ $item['tahun'] ?? '-' }}
                                </p>
                            </div>
                            @if(isset($item['link']) && $item['link'])
                            <a href="{{ $item['link'] }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pengabdian --}}
            @if($dosen->pengabdian && count($dosen->pengabdian) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Pengabdian Masyarakat</h2>
                <div class="space-y-4">
                    @foreach($dosen->pengabdian as $item)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $item['judul'] ?? '-' }}</h3>
                                <p class="text-sm text-gray-500">Tahun: {{ $item['tahun'] ?? '-' }}</p>
                            </div>
                            @if(isset($item['link']) && $item['link'])
                            <a href="{{ $item['link'] }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Contact Info --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Kontak</h2>
                <div class="space-y-4">
                    @if($dosen->email)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $dosen->email }}" class="text-blue-600 hover:text-blue-700 break-all">
                            {{ $dosen->email }}
                        </a>
                    </div>
                    @endif
                    @if($dosen->telepon)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-gray-600">{{ $dosen->telepon }}</span>
                    </div>
                    @endif
                    @if($dosen->nip)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span class="text-gray-600">NIP: {{ $dosen->nip }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Keahlian --}}
            @php
                $keahlianList = is_array($dosen->bidang_keahlian) 
                    ? $dosen->bidang_keahlian 
                    : ($dosen->bidang_keahlian ? explode(',', $dosen->bidang_keahlian) : []);
            @endphp
            @if(count($keahlianList) > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Bidang Keahlian</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($keahlianList as $keahlian)
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                        {{ trim($keahlian) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Academic IDs --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Profil Akademik</h2>
                <div class="space-y-3">
                    @if($dosen->sinta_id)
                    <a href="https://sinta.kemdikbud.go.id/authors/detail?id={{ $dosen->sinta_id }}" 
                       target="_blank"
                       class="flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <span class="font-medium text-green-700">SINTA</span>
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                    @if($dosen->google_scholar_id)
                    <a href="https://scholar.google.com/citations?user={{ $dosen->google_scholar_id }}" 
                       target="_blank"
                       class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <span class="font-medium text-blue-700">Google Scholar</span>
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                    @if($dosen->scopus_id)
                    <a href="https://www.scopus.com/authid/detail.uri?authorId={{ $dosen->scopus_id }}" 
                       target="_blank"
                       class="flex items-center justify-between p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                        <span class="font-medium text-orange-700">Scopus</span>
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                    @if($dosen->orcid)
                    <a href="https://orcid.org/{{ $dosen->orcid }}" 
                       target="_blank"
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <span class="font-medium text-gray-700">ORCID</span>
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Jabatan --}}
            @if($dosen->jabatan_struktural)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Jabatan Struktural</h2>
                <p class="text-gray-600">{{ $dosen->jabatan_struktural }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
