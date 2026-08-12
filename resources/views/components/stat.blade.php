@props(['value', 'label', 'suffix' => ''])

<article>
    <div class="text-4xl font-semibold tracking-tight text-white tabular-nums" data-count="{{ $value }}" data-count-suffix="{{ $suffix }}">{{ $value }}{{ $suffix }}</div>
    <div class="mt-2 text-sm text-white/55">{{ $label }}</div>
</article>
