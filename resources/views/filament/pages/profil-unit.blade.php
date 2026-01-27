@push('styles')
<style>
    /* === REPEATER LAYOUT: NOMOR | INPUT | BUTTON === */
    
    /* Counter reset pada UL parent */
    ul:has(.fi-fo-repeater-item) {
        counter-reset: misi-num !important;
    }
    
    /* Item: Grid 3 kolom dengan areas */
    .fi-fo-repeater-item {
        display: grid !important;
        grid-template-columns: 2rem 1fr auto !important;
        grid-template-areas: "number content actions" !important;
        gap: 1rem !important;
        align-items: center !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        border: none !important;
        border-top: none !important;
        box-shadow: none !important;
    }
    
    /* Nomor: pseudo-element di kolom 1 */
    .fi-fo-repeater-item::before {
        counter-increment: misi-num !important;
        content: counter(misi-num) !important;
        grid-area: number !important;
        font-weight: 700 !important;
        font-style: italic !important;
        font-size: 1.125rem !important;
        color: #374151 !important;
        text-align: center !important;
    }
    
    /* Content: di kolom 2 */
    .fi-fo-repeater-item-content {
        grid-area: content !important;
        width: 100% !important;
        min-width: 0 !important;
        padding: 4px !important;
    }
    
    /* Header (button): di kolom 3 */
    .fi-fo-repeater-item-header {
        grid-area: actions !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        border: none !important;
        border-top: none !important;
    }
    
    /* Button styling */
    .fi-fo-repeater-item-header-end-actions {
        display: flex !important;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .fi-fo-repeater-item-header-end-actions button {
        width: 2.375rem !important;
        height: 2.375rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .fi-fo-repeater-item-header-end-actions button svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
    }
    
    /* === INPUT STYLING === */
    
    /* Hide label */
    .fi-fo-repeater-item .fi-fo-field-label-col {
        display: none !important;
    }
    
    /* Content column full width */
    .fi-fo-repeater-item .fi-fo-field-content-col {
        width: 100% !important;
    }
    
    /* Input wrapper full width */
    .fi-fo-repeater-item .fi-input-wrp {
        width: 100% !important;
    }
    
    .fi-fo-repeater-item .fi-input-wrp-content-ctn {
        width: 100% !important;
    }
    
    /* Input field styling - border tipis */
    .fi-fo-repeater-item .fi-input {
        width: 100% !important;
        padding: 0.5rem 0.75rem !important;
        border: none !important;
        /* border-bottom: 1px solid #e5e7eb !important; */
        border-radius: 0 !important;
        background: transparent !important;
    }
    
    .fi-fo-repeater-item .fi-input:focus {
        outline: none !important;
        border-bottom: 1px solid #3b82f6 !important;
    }
    
    /* Hide collapse button dan border atas */
    .fi-fo-repeater-item-collapse-button {
        display: none !important;
    }
    
    /* Hilangkan semua border pada repeater items */
    .fi-fo-repeater-item,
    .fi-fo-repeater-item *,
    .fi-fo-repeater-item-header,
    .fi-fo-repeater-item-content {
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
    }
</style>
@endpush

<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex gap-x-3">
            <x-filament::button type="submit">
                Simpan Profil
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
