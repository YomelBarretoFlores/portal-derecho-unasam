@props(['value', 'suffix' => '', 'label'])

<div>
    <div class="font-serif text-4xl font-bold text-gold-400 md:text-5xl"
         data-count="{{ $value }}" @if ($suffix) data-count-suffix="{{ $suffix }}" @endif>0{{ $suffix }}</div>
    <div class="mt-1 font-sans text-sm text-white/70">{{ $label }}</div>
</div>
