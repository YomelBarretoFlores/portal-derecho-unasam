@php
    /** @var \App\Models\Docente $record */
    $foto = $record->fotoPublicaUrl('thumb');
@endphp

<div
    class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-50 ring-1 ring-gray-950/10 dark:bg-primary-950 dark:ring-white/15"
    title="{{ $record->name }}"
>
    @if ($foto)
        <img
            src="{{ $foto }}"
            alt="Retrato de {{ $record->name }}"
            class="size-full object-cover object-top"
            loading="lazy"
        >
    @else
        <span class="text-xs font-semibold tracking-wide text-primary-700 dark:text-primary-300" aria-label="Sin fotografía: {{ $record->name }}">
            {{ $record->iniciales }}
        </span>
    @endif
</div>
