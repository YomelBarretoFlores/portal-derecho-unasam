@props(['href'])

@php $active = url()->current() === $href; @endphp

<a href="{{ $href }}" wire:navigate.hover {{ $attributes->class([
    'nav-link relative px-3.5 py-2 text-[15px] font-medium transition hover:bg-stone-100',
    'text-navy-900' => $active,
    'text-navy-800 hover:text-navy-900' => !$active,
]) }}>
    {{ $slot }}
    @if ($active)
        <span class="absolute -bottom-1 left-1/2 h-px w-5 -translate-x-1/2 bg-gold-400"></span>
    @endif
</a>
