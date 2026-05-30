@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'left'])

<div class="reveal {{ $align === 'center' ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl' }}">
    @if ($eyebrow)
        <span class="eyebrow">{{ $eyebrow }}</span>
    @endif
    <h2 class="mt-2 text-3xl font-semibold leading-tight text-navy-900 md:text-4xl">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-3 font-sans text-lg text-gray-500">{{ $subtitle }}</p>
    @endif
</div>
