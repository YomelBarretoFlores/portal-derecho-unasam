# CLAUDE.md

## Design Context

This project's strategic and visual design system is documented alongside this file, at the repo root:

- **[PRODUCT.md](PRODUCT.md)** — register (`brand`), users, purpose, brand personality, anti-references, and design principles. Read it before any design work on the public site.
- **[DESIGN.md](DESIGN.md)** — visual system (palette, typography, components, layout). Read it before generating or modifying UI so variants stay on-brand.

**Quick reference:**
- Register: **brand** (public institutional site of the Facultad/Programa de Derecho y Ciencias Políticas, UNASAM). The `/admin` panel (Filament) is a separate `product` surface and not the design focus.
- Core principles: *authority through substance not ornament · visible academic rigor · living tradition (not dated/governmental) · official content first · legibility as a public duty (WCAG 2.1 AA)*.
- Identity: navy UNASAM (structural) + gold (fine-line accent only), EB Garamond (display) + Outfit (body), editorial layout (straight geometry, 1px hairlines, no elevation shadows).

The Laravel app is this repository. The public design system is `resources/css/app.css` (`@theme` tokens + `@layer` components).
