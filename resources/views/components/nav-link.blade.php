@props(['href'])

@php $active = url()->current() === $href; @endphp

<a href="{{ $href }}" wire:navigate.hover {{ $attributes->class([
    'nav-link relative rounded-lg px-3.5 py-2 text-[15px] font-medium transition hover:bg-stone-100',
    'text-navy-900' => $active,
    'text-navy-800 hover:text-navy-900' => !$active,
]) }}>
    {{ $slot }}
    @if ($active)
        <span class="absolute -bottom-1 left-1/2 h-0.5 w-4 -translate-x-1/2 rounded-full bg-navy-700"></span>
    @endif
</a>
