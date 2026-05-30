@props(['docente'])

<article class="card-hover flex items-center gap-5 rounded-2xl border border-gray-200 bg-white p-5">
    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-navy-900 font-serif text-xl font-bold text-gold-400">
        {{ $docente->iniciales }}
    </div>
    <div>
        <h3 class="text-base font-semibold text-navy-900">{{ $docente->name }}</h3>
        <p class="mt-0.5 font-sans text-sm text-gold-600">{{ $docente->area }}</p>
        <p class="font-sans text-xs text-gray-400">{{ $docente->grado }}</p>
    </div>
</article>
