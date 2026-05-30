@props(['href'])

<a href="{{ $href }}" wire:navigate {{ $attributes->class('rounded-lg px-3.5 py-2 text-[15px] font-medium text-navy-800 transition hover:bg-stone-100 hover:text-navy-900') }}>{{ $slot }}</a>
