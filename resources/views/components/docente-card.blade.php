@props(['docente'])

<article class="card-hover flex items-center gap-5 rounded-2xl border border-stone-200 bg-white p-5">
    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-navy-900 text-lg font-semibold text-white">{{ $docente->iniciales }}</div>
    <div>
        <h3 class="text-base font-semibold text-navy-900">{{ $docente->name }}</h3>
        <p class="mt-0.5 text-sm font-medium text-navy-600">{{ $docente->area }}</p>
        <p class="text-xs text-stone-400">{{ $docente->grado }}</p>
    </div>
</article>
