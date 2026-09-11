@props(['name'])

<svg {{ $attributes->merge(['class' => 'h-4 w-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('arrow-left')
            <path d="M19 12H5m6 6-6-6 6-6" />
            @break
        @case('external-link')
            <path d="M15 4h5v5M10 14 20 4M20 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('buscar')
            <circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" />
            @break
        @case('document')
            <path d="M6 3h8l4 4v14H6z" /><path d="M14 3v5h5M9 13h6M9 17h6" />
            @break
        @default
            <path d="M5 12h14m-6-6 6 6-6 6" />
    @endswitch
</svg>
