@props(['delay' => null, 'direction' => 'up'])

<div {{ $attributes->class([
        'reveal',
        'reveal-left' => $direction === 'left',
        'reveal-right' => $direction === 'right',
    ]) }}
    @if ($delay) data-reveal-delay="{{ $delay }}" @endif>
    {{ $slot }}
</div>
