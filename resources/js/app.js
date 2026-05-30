// Alpine.js viene incluido con Livewire (cargado por @livewireScripts en el layout),
// que también provee la navegación SPA con wire:navigate. Por eso NO importamos
// Alpine por separado: tendríamos dos instancias y Livewire lanzaría un error.
//
// Aquí solo va el scroll-reveal y los contadores, reinicializados en cada
// navegación SPA (wire:navigate dispara el evento 'livewire:navigated').

// ---- Scroll reveal: replica el componente Reveal de animations.jsx ----
function initReveal() {
    const els = document.querySelectorAll('.reveal:not(.is-visible)');
    if (!els.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
    );

    els.forEach((el) => {
        const delay = el.dataset.revealDelay;
        if (delay) el.style.transitionDelay = `${delay}s`;
        observer.observe(el);
    });
}

// ---- Contadores animados: replica AnimatedNumber (1500ms) ----
function initCounters() {
    const els = document.querySelectorAll('[data-count]');
    if (!els.length) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const animate = (el) => {
        if (el.dataset.counted) return; // evita doble animación
        el.dataset.counted = '1';

        const target = parseFloat(el.dataset.count);
        const suffix = el.dataset.countSuffix || '';

        // Sin movimiento: mostrar el valor final directamente
        if (reduceMotion) {
            el.textContent = target + suffix;
            return;
        }

        const duration = 1400;
        let startTime = null;
        // Ease-out (sensación Framer): rápido al inicio, desacelera al final
        const easeOut = (t) => 1 - Math.pow(1 - t, 3);

        const tick = (now) => {
            if (startTime === null) startTime = now;
            const progress = Math.min((now - startTime) / duration, 1);
            const value = Math.round(target * easeOut(progress));
            el.textContent = value + suffix;
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = target + suffix;
        };
        requestAnimationFrame(tick);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 }
    );

    els.forEach((el) => observer.observe(el));
}

function initAnimations() {
    initReveal();
    initCounters();
}

// 'livewire:navigated' se dispara en la carga inicial Y tras cada navegación SPA.
document.addEventListener('livewire:navigated', initAnimations);

// Fallback por si Livewire no estuviera disponible (las guardas evitan duplicados).
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Livewire) initAnimations();
});
