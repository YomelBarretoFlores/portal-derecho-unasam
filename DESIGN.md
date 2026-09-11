---
name: Derecho UNASAM — Sitio Público
description: Sistema visual editorial-institucional para la Facultad de Derecho y Ciencias Políticas de la UNASAM.
colors:
  navy-900: "#17335c"
  navy-800: "#1d3e6e"
  navy-700: "#234a85"
  navy-600: "#2d529f"
  navy-200: "#c2d5e9"
  navy-50: "#f1f5fa"
  navy-950: "#0f2240"
  gold-400: "#c4aa4e"
  gold-300: "#d4c078"
  gold-600: "#826d3a"
  paper: "#faf8f4"
  surface-white: "#ffffff"
  ink-body: "#57534e"
  ink-muted: "#78716c"
  border-hairline: "#e7e5e4"
  border-strong: "#d6d3d1"
typography:
  display:
    fontFamily: "EB Garamond, ui-serif, Georgia, serif"
    fontSize: "clamp(2.75rem, 6vw, 5.5rem)"
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: "-0.01em"
  headline:
    fontFamily: "EB Garamond, ui-serif, Georgia, serif"
    fontSize: "clamp(1.875rem, 3vw, 2.25rem)"
    fontWeight: 600
    lineHeight: 1.15
    letterSpacing: "-0.01em"
  title:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "-0.02em"
  body:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: "normal"
  label:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: "0.14em"
rounded:
  none: "0px"
  sm: "0px"
  md: "0px"
  lg: "0px"
  full: "9999px"
spacing:
  section-mobile: "5rem"
  section: "6rem"
  section-hero: "7rem"
  gutter: "1.5rem"
components:
  button-primary:
    backgroundColor: "{colors.navy-900}"
    textColor: "{colors.surface-white}"
    rounded: "{rounded.none}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "{colors.navy-800}"
    textColor: "{colors.surface-white}"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.navy-900}"
    rounded: "{rounded.none}"
    padding: "12px 24px"
  card:
    backgroundColor: "{colors.surface-white}"
    textColor: "{colors.ink-body}"
    rounded: "{rounded.none}"
    padding: "28px"
  eyebrow:
    textColor: "{colors.navy-700}"
    typography: "{typography.label}"
---

# Design System: Derecho UNASAM — Sitio Público

## 1. Overview

**Creative North Star: "El Tratado Moderno"**

El sistema se comporta como un tratado jurídico contemporáneo: la autoridad la carga la palabra, no el ornamento. Titulares en serif (EB Garamond) le dan voz de doctrina y tradición; el soporte —cuerpo, navegación, datos— va en un sans limpio (Outfit) que mantiene la página vigente y nunca anticuada. Entre ambos, mucho aire y reglas finas de 1px que ordenan sin gritar. Es la "tradición vigente" del PRODUCT.md hecha superficie: peso institucional con ejecución actual.

La densidad es baja y deliberada. La jerarquía se construye con tamaño tipográfico, espacio y hairlines —no con tarjetas flotantes, sombras ni cajas anidadas. El dorado institucional aparece solo como trazo fino (subrayados, una regla de acento, cifras editoriales); el navy de la UNASAM es el color estructural que sostiene todo. El carácter de los componentes es **refinado y discreto**: micro-transiciones de color, realce por borde, cero movimiento gratuito.

Rechaza explícitamente lo que PRODUCT.md marca como anti-referencias: el **portal gubernamental** recargado y burocrático, el **SaaS genérico** de gradientes y tarjetas con emojis, y lo **recargado/barroco** de dorado excesivo. Si parece una plantilla, falló; si parece una publicación seria, acertó.

**Key Characteristics:**
- Serif para la voz, sans para el soporte — contraste tipográfico real.
- Geometría recta (radio 0) y planitud: hairlines de 1px en lugar de sombras.
- Navy estructural + dorado de acento ≤ trazos finos; nunca masas doradas.
- Jerarquía por tipografía y espacio, no por tarjetas repetidas.
- Accesible por diseño: contraste alto, foco de teclado visible, reduced-motion.

## 2. Colors

Paleta institucional UNASAM: un navy estructural omnipresente, dorado como único acento de trazo fino, sobre papel cálido casi blanco con texto en gris tibio (stone).

### Primary
- **Navy Institucional** (#17335c, `navy-900`): color estructural principal. Títulos serif, secciones oscuras (hero, estadísticas, footer, topbar a #0f2240), botón primario. Es la identidad de la facultad.
- **Navy Enlace** (#234a85, `navy-700`): texto de acento de alto contraste sobre papel — eyebrows, enlaces, etiquetas de categoría. Sustituye al dorado donde se necesita texto pequeño legible (8.28:1 sobre papel).

### Secondary
- **Dorado Acento** (#c4aa4e, `gold-400`): único acento decorativo, reservado a **trazos finos**: subrayado del nav activo/hover, `.accent-line` de 1px, el `·` del marquee. Nunca como masa ni fondo grande.
- **Dorado Tinta** (#826d3a, `gold-600`): variante oscura para cifras editoriales grandes (`.num-editorial`) y donde el dorado necesita acercarse a texto (4.72:1 sobre papel).
- **Dorado Claro** (#d4c078, `gold-300`): solo sobre fondos navy oscuros — barras del gráfico de estadísticas y eyebrows de cifra en el hero (6.98:1 sobre navy-900).

### Neutral
- **Papel** (#faf8f4): fondo cálido de secciones de respiro (`bg-paper`); la superficie sobre la que descansa el contenido.
- **Blanco** (#ffffff): fondo de tarjetas y del cuerpo principal.
- **Tinta Cuerpo** (#57534e, stone-600): texto de párrafo. Bajo, cálido, alto contraste sobre papel/blanco.
- **Tinta Apoyo** (#78716c, stone-500): subtítulos y texto secundario.
- **Hairline** (#e7e5e4, stone-200 / #d6d3d1, stone-300): bordes y reglas de 1px que estructuran la página.

### Named Rules
**La Regla del Trazo Dorado.** El dorado vive solo en trazos finos y detalles (≤ líneas de 1px, subrayados, cifras decorativas). Está prohibido como fondo, relleno de botón o masa. Su escasez es lo que lo hace institucional, no barroco.

**La Regla del 55 % sobre Navy.** El texto blanco con opacidad sobre los navy de marca no baja del 55 %. Medido sobre `navy-900` (#17335c): 40 % → 3.36:1, 45 % → 3.83:1, 50 % → 4.37:1 (incumplen AA); 55 % → 4.94:1, 60 % → 5.56:1 (cumplen). Sobre `navy-950`, 45 % → 4.26:1 también incumple. Por debajo del 55 % solo caben elementos decorativos, que van con `aria-hidden` y a los que WCAG pide 3:1 por ser contenido no textual. `AccesibilidadTest` lo vigila por archivo y línea.

**La Regla Navy-No-Negro.** Las secciones oscuras y los títulos usan navy `#17335c`, nunca negro puro. El navy ES la identidad de la facultad; perderlo por "elegancia" es perder la marca.

## 3. Typography

**Display Font:** EB Garamond (con ui-serif, Georgia, serif)
**Body Font:** Outfit (con ui-sans-serif, system-ui, sans-serif)

**Character:** Contraste en eje serif↔sans. El serif clásico da gravedad de doctrina jurídica a los titulares; el sans geométrico-humanista mantiene el cuerpo y la UI contemporáneos y legibles. La tensión entre ambos es la "tradición vigente" de la marca.

### Hierarchy
- **Display** (EB Garamond, 700, `clamp(2.75rem, 6vw, 5.5rem)`, lh 1.05, ls -0.01em): titular del hero. Protagonista, dramático pero con aire.
- **Headline** (EB Garamond, 600–700, `clamp(1.875rem, 3vw, 2.25rem)`, lh 1.15): títulos de sección (h2) e internos. Serif, peso editorial.
- **Title** (Outfit, 600, 1.125rem, ls -0.02em): subtítulos (h3/h4). En sans, a propósito, para marcar jerarquía frente a los títulos serif.
- **Body** (Outfit, 400, 1.0625rem, lh 1.6): párrafos en tinta stone-600. Largo de línea acotado a 65–75ch.
- **Label / Eyebrow** (Outfit, 600, 0.75rem, mayúsculas, tracking 0.14em, navy-700): antetítulos y etiquetas.

### Named Rules
**La Regla Serif-Sans.** Títulos h1/h2 SIEMPRE en serif (EB Garamond); subtítulos h3/h4 y todo el cuerpo/UI en sans (Outfit). No mezclar: el contraste entre familias ES la jerarquía. Prohibido poner títulos mayores en sans.

**La Regla del Eyebrow Navy.** El antetítulo va en navy-700, no en dorado: el dorado claro (#a4894a/`gold-500`) no alcanza 4.5:1 en texto pequeño. Legibilidad antes que decoración.

## 4. Elevation

Sistema **plano por principio**. No hay sombras de elevación difusas. La profundidad y el orden se transmiten con **reglas finas de 1px** (bordes, hairlines, divisores) y con el contraste de fondos (papel claro vs. secciones navy). Las "sombras" del tema están reducidas a hairlines casi imperceptibles para no romper utilidades heredadas, pero su rol visual es nulo.

### Shadow Vocabulary
- **Hairline reposo** (`box-shadow: 0 1px 0 rgba(26,26,26,0.06)`): línea inferior sutil; única "sombra" admitida, equivalente a un borde.
- **Hairline énfasis** (`0 1px 0 rgba(26,26,26,0.10)` / `0.12`): variantes apenas más marcadas para nav fijo o paneles; siguen siendo líneas, no volumen.

### Named Rules
**La Regla Plana.** Las superficies son planas en reposo y en hover. Una tarjeta no se "levanta": su borde se oscurece a navy-900. Prohibido `translateY` en hover, sombras difusas o glassmorphism decorativo.

## 5. Components

Carácter general: **refinado y discreto**. Geometría recta, micro-transiciones de color (0.2–0.25s, `cubic-bezier(0.16, 1, 0.3, 1)`), realce por borde y color, nunca por movimiento.

### Buttons
- **Shape:** esquinas rectas (radio 0).
- **Primary:** fondo navy-900 (#17335c), texto blanco, padding 12px 24px. Hover → navy-800 (#1d3e6e); el texto blanco siempre mantiene ≥10:1.
- **Ghost:** transparente, texto navy-900, borde 1px stone-300; hover refuerza el borde a navy-900. Sin cambio de fondo ni movimiento.
- **Sobre fondos oscuros:** `light` (fondo blanco, texto navy) y `ghost-light` (borde blanco/30, hover bg blanco/10).
- **Hover / Focus:** transición solo de color; foco de teclado con outline 2px (navy-600 sobre claro, blanco sobre navy). El anillo blanco cubre enlaces **y botones** sobre navy: navy-600 sobre navy-950 rinde 2.13:1, por debajo del 3:1 que WCAG exige a un indicador de foco.

### Cards / Containers
- **Corner Style:** rectas (radio 0).
- **Background:** blanco sobre secciones de papel.
- **Shadow Strategy:** ninguna (ver Elevation). Borde 1px stone-200 en reposo.
- **Hover:** el borde pasa a navy-900 (`.card-hover`); sin elevación.
- **Internal Padding:** 24–28px.

### Inputs / Fields
- **Style:** borde 1px stone-300, fondo blanco, esquinas rectas.
- **Focus:** borde navy-700 + anillo `ring-2 ring-navy-700/10`.

### Navigation
- **Arquitectura:** agrupada por audiencia —**La Facultad · Estudiantes · Investigación · Comunicados**—, no por organigrama. Vive en `App\Support\Navegacion`, fuente única que alimenta el menú, el pie y las migas de pan: sin ella cada superficie acababa nombrando los mismos contenidos de forma distinta.
- **Buscador:** enlace con lupa, no panel plegable. Funciona sin JavaScript y no añade estado a una barra que ya gestiona tres desplegables.
- **Style:** barra fija sobre fondo blanco/papel; al hacer scroll gana fondo translúcido (`bg-white/80 backdrop-blur`) + hairline inferior (sin sombra).
- **Links:** texto navy-800; hover muestra subrayado dorado animado (`.nav-link`); página activa marcada con una barra dorada de 1px bajo el enlace.
- **Dropdowns:** panel recto, borde 1px; items en stone-600 con hover a navy-900. Accesibles por teclado (focus/Escape).
- **Mobile:** menú desplegable que cierra al navegar; toggle con `aria-expanded`.

### Hero fotográfico
La portada abre con la fotografía institucional a sangre completa (el patio de la facultad, con el escudo en mosaico y la Cordillera Blanca al fondo), no encajonada a media pantalla. Sobre ella, **dos velos navy**: uno parejo (`navy-950/55`) que garantiza el contraste en cualquier zona de la imagen, y otro direccional (`from-navy-950 via-navy-950/75 to-navy-950/25`) que oscurece el lado del titular sin apagar la imagen entera. Al pie, una banda **opaca** —nunca translúcida— con las últimas publicaciones: su texto baja a 11px y su contraste no puede depender de qué haya detrás.

### Cabecera de la revista
`Derecho y Cultura` es una publicación con identidad propia dentro del sitio: **todas** sus secciones llevan cabecera navy-950 (variante `publication` de `x-page-hero`), frente al papel claro del resto del portal. Es el mismo recurso que emplean Harvard Law Review y Derecho PUCP con su cabecera de color.

### Signature: Eyebrow + Accent-line + Cifra editorial
- **Eyebrow** (`.eyebrow`): mayúsculas tracked 0.14em en navy-700. Úsese con cadencia, no sobre cada sección.
- **Accent-line** (`.accent-line`): regla dorada de 1px × 12 — firma del sistema sobre cabeceras de página y columnas de footer.
- **Cifra editorial** (`.num-editorial`): numeración 01/02 en EB Garamond dorado-tinta (gold-600), solo cuando hay una secuencia real. Sobre navy usa `.num-editorial-claro` (gold-300, 6.98:1), porque el dorado tinta se apaga hasta ser ilegible. Se emplea en la tira de novedades del hero y en la tabla de contenidos del número de la revista.

## 6. Do's and Don'ts

### Do:
- **Do** usar navy #17335c como color estructural y dorado solo en trazos de 1px, subrayados y cifras (la "Regla del Trazo Dorado").
- **Do** poner títulos h1/h2 en EB Garamond y cuerpo/UI en Outfit; el contraste serif↔sans es la jerarquía.
- **Do** estructurar con hairlines de 1px y espacio en blanco; superficies planas, borde a navy en hover.
- **Do** verificar contraste ≥4.5:1 (texto) — eyebrow en navy-700, no en dorado claro; cuerpo en stone-600, nunca gris claro "por elegancia".
- **Do** mantener foco de teclado visible y alternativa `prefers-reduced-motion` en toda animación (meta WCAG 2.1 AA).

### Don't:
- **Don't** verse como **portal gubernamental genérico**: menús infinitos, tablas feas, recargado. Curar, no burocratizar.
- **Don't** verse como **SaaS/startup genérico**: gradientes morados, tarjetas con emojis, plantilla tech. (Anti-referencia de PRODUCT.md.)
- **Don't** caer en lo **recargado/barroco**: dorado como masa o fondo, ornamento pesado. El dorado es trazo fino o no es.
- **Don't** usar sombras de elevación, `translateY` en hover, glassmorphism decorativo ni esquinas redondeadas (radio 0 es ley). La ley se aplica en los tokens —todos los `--radius-*` valen 0—, así que escribir `rounded-2xl` no redondea nada: solo afirma en el código lo contrario del sistema. No se escriben; `rounded-full` sí existe y es la única admitida.
- **Don't** usar bordes laterales de color (`border-left`/`right` >1px) como acento, ni texto con gradiente (`background-clip: text`).
- **Don't** poner eyebrow ni numeración 01/02 sobre cada sección por reflejo; solo donde hay una secuencia o jerarquía real.
