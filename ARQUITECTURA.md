# Arquitectura

El proyecto usa una arquitectura Laravel convencional. Los controladores aplican filtros y paginación mediante Eloquent; los modelos encapsulan relaciones, casts y scopes de publicación. Solo se conservan servicios cuando existe transformación o lógica editorial real.

## Flujo principal

    Ruta HTTP → Controlador → scopes Eloquent / servicio de dominio → Blade
                             ↘ caché pública de arrays normalizados

    Filament → policies → modelos → observadores de caché y auditoría

Servicios conservados:

- PublicContentCache: portada cacheada como arrays, nunca modelos serializados.
- RevistaService: disponibilidad de la ficha y consulta editorial de integrantes, números y artículos.
- CursoService, ObjetivoService y CompetenciaService: agrupaciones necesarias para las vistas académicas.

content.version invalida la portada cuando cambia una entidad pública o un archivo. Los ajustes globales utilizan su propio mapa cacheado.

## Dominio editorial

- Revista contiene la ficha institucional, la resolución, el enfoque y las políticas de publicación.
- RevistaMiembro representa una función y afiliación dentro del equipo editorial, con visibilidad y orden administrables.
- RevistaNumero representa un volumen/número publicable.
- Articulo pertenece opcionalmente a un número durante la migración, pero necesita esa relación para ser público.
- BlogPost y Comunicado se publican únicamente cuando están activos y su fecha ya llegó.
- Curso distingue planes 2019/2023 y requiere publicado=true para aparecer.

La autorización se resuelve con policies:

- super_admin tiene control total.
- editor administra modelos de contenido.
- La auditoría y los usuarios quedan reservados al superadministrador.

El registro content_audits conserva evento, actor, entidad, cambios, IP y fecha. Excluye contraseñas y secretos de autenticación.

## Seguridad

- No existe lista blanca de correos ni contraseña predeterminada.
- TRUSTED_PROXIES acepta solo IP/CIDR explícitos; * detiene el arranque en producción.
- El middleware global añade CSP, anti-framing, nosniff, política de referencia y restricciones de permisos.
- El contenido enriquecido público se procesa con RichContentRenderer.
- El acceso al panel requiere una cuenta autorizada y contraseña robusta.

La migración de is_admin es progresiva: role ya gobierna la autorización y el campo anterior se conserva temporalmente para compatibilidad. Se eliminará en una migración posterior, después de verificar que todos los administradores fueron convertidos.
