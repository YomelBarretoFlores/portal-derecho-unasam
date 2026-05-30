@props(['href'])

@php
    $active = url()->current() === $href;
@endphp

<a href="{{ $href }}" wire:navigate
   {{ $attributes->class([
       'rounded-lg px-3.5 py-2 text-sm font-medium transition hover:bg-blue-50 hover:text-navy-900',
       'text-navy-900' => $active,
       'text-gray-600' => ! $active,
   ]) }}>
    {{ $slot }}
</a>
