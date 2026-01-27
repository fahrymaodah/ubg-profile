<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex gap-x-3">
            <x-filament::button type="submit">
                Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
