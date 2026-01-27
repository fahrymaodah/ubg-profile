@props([
    'src' => null,
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'placeholder' => '/images/placeholder.jpg',
    'eager' => false,
])

@php
    $loadingStrategy = $eager ? 'eager' : 'lazy';
    $imgSrc = $src ?: $placeholder;
    $showPlaceholder = !$src;
@endphp

<img 
    src="{{ $imgSrc }}"
    alt="{{ $alt }}"
    loading="{{ $loadingStrategy }}"
    decoding="async"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    {{ $attributes->merge(['class' => $class . ($showPlaceholder ? ' bg-gray-200' : '')]) }}
    onerror="this.onerror=null; this.src='{{ $placeholder }}'; this.classList.add('bg-gray-200');"
>
