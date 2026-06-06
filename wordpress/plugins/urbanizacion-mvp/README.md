# Urbanizacion MVP

Plugin + tema de WordPress para el portal de una urbanización de vecinos (en español).

## Qué hace

El **plugin** (`urbanizacion-mvp`) aporta la funcionalidad:

- Tipos de contenido: **Anuncios**, **Noticias**, **Órdenes del día** (juntas) y **Propuestas de acuerdo**.
- Rol **Vecino**.
- **Control de acceso**: las páginas *Inicio*, *La Urbanización* y *Contacto* son públicas; el resto (anuncios, noticias, órdenes del día, propuestas y el foro) solo es visible para usuarios identificados.
- Al activarse, crea las páginas básicas, fija la portada y construye el **Menú principal**.

El **tema** (`urbanizacion`) aporta el diseño: estilo mediterráneo (terracota, azul, arena), portada con héroe, tarjetas, zona de vecinos y pie con contactos.

## Secciones del sitio

| Sección | Tipo | Acceso |
|---|---|---|
| Inicio | Página (portada) | Público |
| La Urbanización (con fotos) | Página | Público |
| Contacto (administrador y servicios) | Página | Público |
| Anuncios | CPT `announcement` | Vecinos |
| Noticias | CPT `noticia` | Vecinos |
| Órdenes del día | CPT `agenda` | Vecinos |
| Propuestas de acuerdo | CPT `propuesta` | Vecinos |
| Foro (preguntas/respuestas y debate) | bbPress | Vecinos |

## Instalación / puesta en marcha

1. `docker compose up -d` (el plugin y el tema se montan en el contenedor).
2. En `Apariencia → Temas`, activa **Urbanización**.
3. En `Plugins`, activa **Urbanizacion MVP**. Al activarlo se crean las páginas, la portada y el menú.
4. El foro usa **bbPress** (ya instalado) con dos secciones: *Preguntas a la administración* y *Problemas de la urbanización*. El acceso está restringido a vecinos automáticamente.
5. Los vecinos se **registran solos** en `wp-login.php?action=register`. Entran como **Vecino (pendiente)** y todavía **no** ven la zona privada.
6. El administrador los **aprueba** en `Usuarios`: enlace *«Aprobar como vecino»* en cada fila (o la acción en lote del mismo nombre). Un aviso en el panel indica cuántos vecinos están pendientes. Al aprobar, el vecino obtiene acceso inmediato y puede crear temas y responder en ambos foros.

El administrador también puede crear usuarios manualmente con el rol **Vecino** (ya aprobados).

> Si activas el plugin **antes** de subir esta versión, desactívalo y vuelve a activarlo para que se ejecute la creación de páginas y menú.

### Enlaces permanentes (permalinks)

El sitio usa enlaces tipo `/anuncios/`, `/noticias/`, etc. En `Ajustes → Enlaces permanentes` elige **Nombre de la entrada** y guarda. (En el contenedor Apache esto requiere un `.htaccess` con las reglas de WordPress en la raíz del sitio.)

## Fotos

- **Héroe de la portada**: `Apariencia → Personalizar → Imagen de cabecera`. Por defecto se usa `assets/img/urbanizacion.png` del tema.
- **Logo**: `Apariencia → Personalizar → Identidad del sitio → Logotipo`.
- **Galería de "La Urbanización"**: edita la página y usa el bloque *Galería*.
- **Foto de cada anuncio/noticia**: usa la *Imagen destacada* — aparece en las tarjetas y en la entrada.

## Documentos (PDF)

Sube el PDF en `Medios → Añadir nuevo` y enlázalo desde el cuerpo del anuncio (como en el aviso de *Fumigación* incluido como ejemplo).
