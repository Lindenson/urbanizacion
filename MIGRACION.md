# Migrar el contenido al hosting

El repositorio solo contiene el **código** (tema y plugin). Tu **contenido real**
—publicaciones, páginas, menú, ajustes, foro y tu cuenta de administrador— está
en la **base de datos**, y las **fotos y documentos** (PDF, imágenes) están en
`wp-content/uploads`. Para que todo eso aparezca en el hosting hay que migrarlo
aparte. Aquí tienes dos formas.

---

## Opción 1 — Plugin "All-in-One WP Migration" (recomendada, la más fácil)

Mueve **todo de una vez** (base de datos + fotos + tema + plugin + foro) en un
solo archivo, y **cambia las direcciones automáticamente** (de `localhost` a tu
dominio). Ideal para hosting gestionado.

**En tu sitio local (`localhost:5000`):**
1. `Plugins → Añadir nuevo` → busca **All-in-One WP Migration** → Instalar → Activar.
2. `All-in-One WP Migration → Exportar` → **Exportar a: Archivo**.
3. Descarga el archivo `.wpress` que genera.

**En el hosting:**
4. Instala WordPress (el instalador de 1 clic de tu hosting sirve).
5. Instala y activa el mismo plugin **All-in-One WP Migration**.
6. `All-in-One WP Migration → Importar` → sube el archivo `.wpress`.
7. Cuando termine, te pedirá **guardar los enlaces permanentes** dos veces
   (`Ajustes → Enlaces permanentes → Guardar`). Hazlo.

Listo: entras con **tu mismo usuario y contraseña** (Denys) y está todo —
anuncios, fotos, foro, vecinos— tal cual.

> El foro usa bbPress: este método ya lo incluye. Si tras importar el foro no se
> ve, activa **bbPress** en el hosting (`Plugins`).

---

## Opción 2 — Manual (base de datos + uploads)

Para hosting con **phpMyAdmin + FTP**, o para un VPS. Usa los archivos que genera
el script de copia de seguridad.

### Paso 0 — Genera la copia (en tu ordenador)

```bash
docker compose up -d        # si no está en marcha
./tools/backup.sh
```

Crea en `backup/`:
- `urbanizacion-db.sql` — la base de datos completa.
- `uploads.tgz` — las fotos y documentos.

> ⚠️ La carpeta `backup/` contiene datos de usuarios (correos, contraseñas
> cifradas). **No la subas a Git** (ya está en `.gitignore`).

### Paso 1 — Prepara WordPress en el hosting
Instala WordPress, sube y activa el **tema** y el **plugin** (los ZIP de
[DESPLIEGUE.md](DESPLIEGUE.md)) e instala **bbPress**. *(bbPress debe estar
activo para que el foro importado se vea.)*

### Paso 2 — Importa la base de datos
En **phpMyAdmin** de tu hosting: selecciona la base de datos de WordPress →
pestaña **Importar** → sube `urbanizacion-db.sql` → Continuar. Esto sustituye el
contenido por el tuyo.

> El nombre de las tablas debe coincidir (prefijo `wp_`). Si tu hosting instaló
> WordPress con otro prefijo, dímelo y ajustamos el volcado.

### Paso 3 — Sube las fotos y documentos
Descomprime `uploads.tgz` y sube su contenido por **FTP** a
`wp-content/uploads/` del hosting (de modo que queden `wp-content/uploads/2026/...`).

### Paso 4 — Cambia la dirección del sitio (¡importante!)
La base de datos apunta a `http://localhost:5000`. Hay que reemplazarla por tu
dominio. La forma correcta (respeta los datos serializados):

- Con **WP-CLI** (si tu hosting lo tiene):
  ```bash
  wp search-replace 'http://localhost:5000' 'https://tudominio.es' --all-tables
  ```
- Sin WP-CLI: instala el plugin **Better Search Replace**
  (`Herramientas → Better Search Replace`), busca `http://localhost:5000` y
  reemplaza por `https://tudominio.es` en todas las tablas.

### Paso 5 — Enlaces permanentes
`Ajustes → Enlaces permanentes` → **Nombre de la entrada** → **Guardar**.

---

## Opción 3 — VPS con Docker

Si despliegas con el `docker-compose.yaml` de este repo en un servidor:

```bash
# En el servidor, con el stack levantado y el código clonado:
# 1) Importar la base de datos
cat backup/urbanizacion-db.sql | docker compose exec -T db \
  sh -c 'exec mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'

# 2) Restaurar los uploads dentro del volumen
docker cp backup/uploads.tgz urbanizacion-wp:/tmp/uploads.tgz
docker compose exec -T wordpress tar xzf /tmp/uploads.tgz -C /var/www/html/wp-content

# 3) Cambiar la URL al dominio real
docker run --rm --network <red> --volumes-from urbanizacion-wp -u 33 \
  -e WORDPRESS_DB_HOST=db:3306 -e WORDPRESS_DB_USER=... -e WORDPRESS_DB_PASSWORD=... -e WORDPRESS_DB_NAME=... \
  wordpress:cli wp search-replace 'http://localhost:5000' 'https://tudominio.es' --all-tables
```

Después instala/activa **bbPress** y guarda los enlaces permanentes.

---

## Resumen

| Qué | Dónde está | Cómo llega al hosting |
|---|---|---|
| Tema y plugin (código) | Repositorio Git | ZIP (DESPLIEGUE.md) o el plugin de migración |
| Publicaciones, páginas, menú, ajustes, foro, **tu cuenta** | Base de datos | `urbanizacion-db.sql` o el plugin de migración |
| Fotos y PDF | `wp-content/uploads` | `uploads.tgz` o el plugin de migración |

La forma más sencilla y sin errores es la **Opción 1**. La **Opción 2/3** te da
control total y sirve para cualquier hosting.
