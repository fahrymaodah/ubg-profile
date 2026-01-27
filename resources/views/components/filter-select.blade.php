@props([
    'name',
    'label',
    'icon' => null,
    'options' => [],
    'selected' => '',
    'placeholder' => 'Pilih...',
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
    </label>
    <div class="relative">
        <select name="{{ $name }}" 
                id="{{ $name }}"
                class="custom-select w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-blue-500 focus:outline-none transition-all cursor-pointer">
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $value => $optionLabel)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
            </svg>
        </div>
    </div>
</div>
