@push('styles')
<style>
    /* Simplify repeater styling */
    .filament-forms-field-wrapper-component-repeater .divide-y > .grid > div {
        border: none !important;
        padding: 0.75rem 0 !important;
        margin: 0.25rem 0 !important;
        background: #f9fafb !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem !important;
    }

    .filament-forms-field-wrapper-component-repeater .divide-y > .grid > div:last-child {
        border-bottom: none !important;
    }

    /* Make repeater item compact */
    .filament-forms-field-wrapper-component-repeater .grid-cols-1 {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 0.5rem;
        align-items: center;
    }

    /* Button styling */
    .filament-forms-field-wrapper-component-repeater button[type="button"] {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Textarea compact */
    .filament-forms-field-wrapper-component-repeater textarea {
        min-height: 2.5rem !important;
        padding: 0.375rem !important;
    }
</style>
@endpush
