@props([
    'title',
    'description',
    'action' => null,
    'href' => null,
])

<div {{ $attributes->class(['editorial-empty']) }}>
    <div class="flex max-w-3xl flex-col gap-5 sm:flex-row sm:items-start">
        <div class="editorial-empty-icon" aria-hidden="true">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M7 3.75h7.5L19 8.25v12H7z"/><path d="M14.5 3.75v4.5H19M10 13h6m-6 3h6"/>
            </svg>
        </div>
        <div class="min-w-0 grow">
            <h3 class="text-xl font-semibold text-navy-900">{{ $title }}</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-500">{{ $description }}</p>
            @if ($action && $href)
                <a href="{{ $href }}" wire:navigate.hover class="link-arrow mt-5 inline-flex text-sm font-semibold text-navy-800">
                    {{ $action }} <span class="ml-2" aria-hidden="true">→</span>
                </a>
            @endif
        </div>
    </div>
</div>
