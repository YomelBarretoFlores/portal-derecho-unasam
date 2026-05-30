@props(['href' => null, 'variant' => 'primary', 'size' => null])

@php
    $classes = trim('btn '
        . ($size === 'sm' ? 'btn-sm ' : '')
        . match ($variant) {
            'ghost' => 'btn-ghost',
            'light' => 'btn-light',
            'ghost-light' => 'btn-ghost-light',
            default => 'btn-primary',
        });
@endphp

@if ($href)
    {{-- wire:navigate solo en enlaces internos (no externos ni descargas) --}}
    <a href="{{ $href }}" @if (! Str::startsWith($href, ['http', 'mailto', 'tel', '#'])) wire:navigate @endif {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
