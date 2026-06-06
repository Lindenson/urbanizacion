# Portal de la Urbanización

Sitio web para una urbanización de vecinos, hecho con **WordPress**. Incluye un
área pública (presentación y contacto) y una **zona privada para vecinos**
(anuncios, noticias, órdenes del día, propuestas de acuerdo y un foro de
preguntas y debate). Diseño de estilo mediterráneo en español.

El proyecto se compone de dos piezas propias:

- **Plugin** `urbanizacion-mvp` — la funcionalidad: secciones, rol de vecino,
  control de acceso y registro con aprobación.
- **Tema** `urbanizacion` — el diseño (terracota, azul mediterráneo, arena).

> 🚀 **¿Quieres publicarlo en internet?** Sigue la
> **[Guía de hosting: de GitHub a tu sitio publicado](GUIA-HOSTING.md)**
> (paso a paso, con ejemplo gratuito en InfinityFree).

---

## Características

| Sección | Acceso |
|---|---|
| Inicio | Público |
| La Urbanización (con fotos) | Público |
| Contacto (administrador y servicios) | Público |
| Anuncios | Solo vecinos |
| Noticias | Solo vecinos |
| Órdenes del día (juntas) | Solo vecinos |
| Propuestas de acuerdo | Solo vecinos |
| Foro: *Preguntas a la administración* y *Problemas de la urbanización* | Solo vecinos |

- **Registro de vecinos con aprobación**: cualquiera puede registrarse, pero el
  administrador debe aprobarlo antes de que acceda a la zona privada.
- **Foro** con dos secciones (bbPress); los vecinos pueden abrir temas y responder.

---

## Puesta en marcha local (Docker)

Requisitos: Docker y Docker Compose.

```bash
git clone https://github.com/Lindenson/urbanizacion.git
cd urbanizacion
docker compose up -d
```

Abre **http://localhost:5000** y completa el asistente de instalación de
WordPress (idioma, título del sitio, usuario administrador).

Después, en el panel `wp-admin`:

1. **Apariencia → Temas** → activa **Urbanización**.
2. **Plugins** → activa **Urbanizacion MVP**. Al activarlo se crean las páginas
   (Inicio, La Urbanización, Contacto), se fija la portada y se construye el menú.
3. **Plugins → Añadir nuevo** → instala y activa **bbPress** (para el foro).
4. **Ajustes → Enlaces permanentes** → elige **Nombre de la entrada** y guarda.

Para parar: `docker compose down` (los datos se conservan en los volúmenes).

> Para producción, copia `.env.example` a `.env` y cambia las contraseñas.

Para publicarlo en un hosting de WordPress, la guía recomendada y completa es
**[GUIA-HOSTING.md](GUIA-HOSTING.md)** (de GitHub a tu sitio publicado).
Como referencia, hay además dos documentos más detallados:
**[DESPLIEGUE.md](DESPLIEGUE.md)** (instalar el código) y
**[MIGRACION.md](MIGRACION.md)** (migrar el contenido).

---

## Manual de uso (administrador)

### Acceder al panel
Entra en `tudominio/wp-admin` con tu usuario administrador.

### Publicar anuncios, noticias, órdenes del día y propuestas
Todas funcionan igual; cada una tiene su propio menú a la izquierda
(**Anuncios**, **Noticias**, **Órdenes del día**, **Propuestas de acuerdo**).

1. Pulsa **Añadir nuevo**.
2. Escribe el **título** y el **contenido** (con el botón **+** añades párrafos,
   listas, imágenes, archivos, etc.).
3. *(Opcional)* A la derecha, **Imagen destacada** → la foto aparecerá en la
   tarjeta del listado y en la entrada.
4. Pulsa **Publicar**.

**Editar:** abre la sección → pulsa sobre el título → cambia → **Actualizar**.
**Eliminar:** en el listado, *Papelera*; para borrar del todo, entra en
*Papelera → Eliminar permanentemente*.

### Adjuntar un PDF (avisos, actas, etc.)
Dentro del contenido: botón **+** → bloque **Archivo** → *Subir* o
*Biblioteca de medios*. Se añade como enlace de descarga.

### Fotos
- **Foto de cabecera (portada):** *Apariencia → Personalizar → Imagen de cabecera*.
- **Logotipo:** *Apariencia → Personalizar → Identidad del sitio*.
- **Galería de "La Urbanización":** edita la página y usa el bloque *Galería*.
- **Foto de cada anuncio/noticia:** *Imagen destacada*.

### Registro y aprobación de vecinos
1. El vecino se registra en `tudominio/wp-login.php?action=register`.
   Entra como **Vecino (pendiente)** y todavía **no** ve la zona privada.
2. Tú lo apruebas en **Usuarios**: enlace **Aprobar como vecino** en su fila
   (o la acción en lote del mismo nombre). Un aviso arriba indica cuántos hay
   pendientes. Al aprobarlo, obtiene acceso inmediato.

### Foro
En **Foros** gestionas los temas y respuestas. Los vecinos aprobados pueden
abrir temas en las dos secciones. Como administrador eres *keymaster* (control total).

### Páginas y menú
- Editar **Inicio / La Urbanización / Contacto**: menú **Páginas**.
- Cambiar el menú de navegación: *Apariencia → Menús*.

---

## Estructura del proyecto

```
docker-compose.yaml                         Stack local (MySQL + WordPress)
.env.example                                Variables (copiar a .env)
GUIA-HOSTING.md                             Guía completa: de GitHub al sitio publicado
DESPLIEGUE.md                               Referencia: instalar el código en hosting
tools/package.sh                            Genera los ZIP del tema y el plugin
tools/backup.sh                             Exporta base de datos + uploads (migración)
MIGRACION.md                                Guía para migrar el contenido al hosting
wordpress/
  plugins/urbanizacion-mvp/                 Plugin (funcionalidad)
  themes/urbanizacion/                      Tema (diseño)
```

El núcleo de WordPress y la base de datos **no** están en el repositorio: viven
en volúmenes de Docker. Lo versionado es el plugin y el tema.
