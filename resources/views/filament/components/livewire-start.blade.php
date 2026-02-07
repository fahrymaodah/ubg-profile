{{-- 
    Initialize Livewire AFTER Filament's app.js has registered Alpine components
    This must be loaded via panels::scripts.after
--}}
<script>
    if (window.Livewire && typeof Livewire.start === 'function') {
        Livewire.start();
    }
</script>
