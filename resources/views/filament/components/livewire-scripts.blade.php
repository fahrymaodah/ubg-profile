{{-- 
    Livewire Scripts - Manual load for Cloudflare bypass
    
    Problem: Cloudflare blocks /livewire/livewire.min.js?id=xxx (query string triggers challenge)
    Solution: Load from /vendor/livewire/ WITHOUT query string
    
    NOTE: This file is loaded in panels::scripts.before to make Alpine available
    Livewire.start() is called separately in panels::scripts.after (after Filament registers Alpine components)
--}}
@livewireScriptConfig
<script src="{{ asset('vendor/livewire/livewire.min.js') }}" data-csrf="{{ csrf_token() }}" data-update-uri="{{ route('livewire.update') }}"></script>
