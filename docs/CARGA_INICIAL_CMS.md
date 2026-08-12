# Carga inicial mediante Filament

Este documento es una guía operativa. Neon y `/admin` son la única fuente de verdad del contenido editorial; no se deben recrear seeders.

## Revista Derecho y Cultura

1. Entrar a `/admin`, abrir **Revista Derecho y Cultura → Revista** y crear la ficha como **Borrador**.
2. Registrar el nombre oficial `Derecho y Cultura, Revista Científica de Derecho y Antropología Jurídica` y el nombre corto `Derecho y Cultura`.
3. Registrar como unidad responsable la `Unidad de Investigación de la Facultad de Derecho y Ciencia Política`.
4. Registrar la resolución `N.° 063-2026-UNASAM-FDCCPP/D.`, con fecha `06/07/2026`, y adjuntar `docs/sources/revista/rcf-063-creacion-revista-derecho-y-cultura.pdf`.
5. Registrar periodicidad semestral, modalidad digital, idiomas español e inglés, revisión por pares doble ciego, APA 7.ª edición y `revista-dc@unasam.edu.pe`.
6. Mantener el ISSN vacío y no registrar una URL OJS ni un logo mientras no exista documentación oficial.
7. Copiar la presentación, alcance, normas y los 32 integrantes directamente de la resolución. La resolución prevalece ante cualquier diferencia.
8. Guardar como **Pendiente de revisión**, abrir **Vista previa** y contrastar todos los campos con el PDF.
9. Cambiar a **Verificado**. Solo cambiar a **Publicado** cuando el PDF también se encuentre disponible en el entorno público.
10. No crear números ni artículos hasta recibir evidencia de una edición publicada.

## Docentes

Los diez perfiles existentes se revisan, no se recrean. Para cada ficha:

1. Contrastar nombre, grados, categoría, dedicación y reseña con `documento_fuente`.
2. Verificar que ORCID y perfiles académicos pertenezcan a la persona.
3. Revisar que no existan teléfonos, correos personales, firmas o capturas.
4. Cambiar a **Verificado**, guardar y revisar **Vista previa**.
5. Publicar inicialmente solo Fabel Bernabé Robles Espinoza, Lucía Buleje Ayala, Fany Soledad Vera Gutierrez y Katherine Mónica Castro Menacho, siempre que la revisión visual y la autorización estén confirmadas.
6. Mantener los otros seis perfiles pendientes hasta contar con fotografías y enlaces adecuados.

Después de una carga importante se puede ejecutar `php artisan content:cache:warm`; las modificaciones hechas en Filament invalidan automáticamente la versión anterior.
