# Gestión del contenido docente

## Alcance

El portal diferencia la recepción documental de la publicación pública. Los diez perfiles remitidos se conservan en Neon como pendientes; no aparecen en `/docentes`, en sus rutas individuales ni en el sitemap hasta que un editor los revise en Filament.

## Datos públicos

El perfil público puede mostrar nombre, categoría, dedicación, grado, especialidad, reseña académica, correo institucional, ORCID, perfiles académicos, publicaciones seleccionadas y fotografía institucional.

No deben registrarse ni publicarse teléfonos, correos personales, firmas, capturas de conversaciones, informes administrativos completos ni otros datos ajenos a la finalidad informativa del portal.

## Flujo de revisión

1. Revisar el documento fuente y las observaciones internas del registro.
2. Confirmar nombre, categoría, dedicación, grados, especialidades y reseña.
3. Verificar que ORCID, Google Scholar, CTI Vitae y ALICIA estén correctamente identificados y pertenezcan a la persona indicada.
4. Sustituir fotografías ausentes, de baja resolución, capturas o selfies por un archivo institucional JPG, PNG o WebP.
5. Confirmar la autorización de publicación del perfil.
6. Cambiar el estado editorial a `Verificado`.
7. Revisar la vista previa privada.
8. Cambiar el estado a `Publicado`. El modelo impide publicar registros sin fuente o incompletos.

## Fuente de verdad

No existe un seeder de docentes. Neon y el panel administrativo son la fuente de verdad. Las fotografías documentales aptas se conservan en `docs/sources/docentes` únicamente como respaldo; cualquier alta o modificación debe realizarse desde Filament y quedará registrada en la auditoría.
