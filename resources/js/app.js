// Alpine.js viene incluido con Livewire (cargado por @livewireScripts en el layout),
// que también provee la navegación SPA con wire:navigate. Por eso NO importamos
// Alpine por separado: tendríamos dos instancias y Livewire lanzaría un error.

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ---- Scroll reveal ----
function initReveal() {
    const els = document.querySelectorAll('.reveal:not(.is-visible)');
    if (!els.length) return;

    if (!('IntersectionObserver' in window) || reduceMotion) {
        els.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.08, rootMargin: '0px 0px -24px 0px' }
    );

    els.forEach((el) => {
        const delay = el.dataset.revealDelay;
        if (delay) el.style.transitionDelay = `${delay}s`;
        observer.observe(el);
    });
}

// ---- Contadores animados ----
function initCounters() {
    const els = document.querySelectorAll('[data-count]');
    if (!els.length) return;

    const animate = (el) => {
        if (el.dataset.counted) return;
        el.dataset.counted = '1';

        const target = parseFloat(el.dataset.count);
        const suffix = el.dataset.countSuffix || '';

        if (reduceMotion) {
            el.textContent = target + suffix;
            return;
        }

        const duration = 1400;
        let startTime = null;
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

// ---- Barras del chart: animar crecimiento al entrar en viewport ----
function initBars() {
    const bars = document.querySelectorAll('.bar-grow');
    if (!bars.length || reduceMotion) {
        bars.forEach((b) => b.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 }
    );

    bars.forEach((el) => observer.observe(el));
}

// ---- Parallax sutil en la imagen del hero ----
function initParallax() {
    const el = document.querySelector('[data-parallax]');
    if (!el || reduceMotion) return;

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const y = window.scrollY;
            if (y < 800) {
                el.style.transform = `translateY(${y * 0.12}px)`;
            }
            ticking = false;
        });
    };

    window.addEventListener('scroll', onScroll, { passive: true });
}

function initAnimations() {
    initReveal();
    initCounters();
    initBars();
    initParallax();
}

document.addEventListener('livewire:navigated', initAnimations);

document.addEventListener('DOMContentLoaded', () => {
    if (!window.Livewire) initAnimations();
});
