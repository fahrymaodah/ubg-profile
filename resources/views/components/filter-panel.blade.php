@props([
    'action' => '',
    'method' => 'GET',
    'resetUrl' => '',
    'hasActiveFilters' => false,
    'showSearch' => false,
    'searchPlaceholder' => 'Cari...',
    'searchName' => 'q',
    'searchValue' => '',
])

<div x-data="{ open: true }" class="mb-8">
    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center size-9 bg-blue-100 rounded-lg">
                        <svg class="size-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Filter & Pencarian</h3>
                        <p class="text-xs text-gray-500">Temukan data yang Anda cari</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    @if($hasActiveFilters)
                    <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        <svg class="size-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        Filter Aktif
                    </span>
                    @endif
                    
                    {{-- Mobile Toggle --}}
                    <button type="button" 
                            @click="open = !open" 
                            class="lg:hidden flex items-center justify-center size-9 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg x-show="!open" x-cloak class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
                        </svg>
                        <svg x-show="open" x-cloak class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 15 7-7 7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Filter Form --}}
        <form action="{{ $action }}" method="{{ $method }}">
            <div x-show="open" x-cloak class="p-5" x-transition>
                <div class="space-y-4">
                    {{-- Search Field (if enabled) --}}
                    @if($showSearch)
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="size-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="{{ $searchName }}" 
                               value="{{ $searchValue }}"
                               placeholder="{{ $searchPlaceholder }}"
                               class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder:text-gray-500 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                    </div>
                    @endif

                    {{-- Filter Fields Slot --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{ $slot }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    @if($hasActiveFilters && $resetUrl)
                    <a href="{{ $resetUrl }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                        Reset Filter
                    </a>
                    @endif
                    
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
