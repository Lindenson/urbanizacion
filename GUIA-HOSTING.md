# De GitHub a tu sitio publicado

Guía completa para coger este proyecto desde GitHub y tener la web de la
urbanización funcionando en un **hosting de WordPress** (gratuito o de pago).
Como ejemplo usamos **InfinityFree** (gratis), pero los pasos sirven para
cualquier hosting con WordPress + phpMyAdmin.

> **Idea clave:** en GitHub está el **código** (tema y plugin). Tu **contenido**
> (publicaciones, páginas, foro, tu cuenta) está en la **base de datos** y tus
> **fotos/PDF** en `wp-content/uploads`. El código se sube; el contenido se migra
> aparte (paso 4).

---

## Resumen de pasos

1. Conseguir el tema y el plugin desde GitHub.
2. Crear el hosting e instalar WordPress.
3. Subir el tema y el plugin, e instalar bbPress.
4. Llevar tu contenido (base de datos + fotos).
5. Ajustes finales (enlaces permanentes, HTTPS, datos reales).

---

## 1. Conseguir el tema y el plugin

**Opción A — Descargar los ZIP ya preparados (más fácil)**
En la página del repositorio en GitHub → **Releases** → descarga:
- `urbanizacion-theme.zip`
- `urbanizacion-mvp.zip`

**Opción B — Generarlos desde el código**
```bash
git clone https://github.com/Lindenson/urbanizacion.git
cd urbanizacion
./tools/package.sh        # crea los dos ZIP en dist/
```

---

## 2. Crear el hosting e instalar WordPress (ejemplo: InfinityFree)

1. Regístrate en **infinityfree.com**, confirma el correo.
2. **Create Account** → elige un **subdominio gratis** (p. ej. `mi-urbanizacion.rf.gd`)
   o conecta tu dominio. Espera unos minutos a que se active.
3. Abre el **Control Panel** → **Softaculous Apps Installer** → **Instalar WordPress**.
   - Protocolo: `http://` de momento (a `https://` lo pasamos en el paso 5).
   - Si vas a migrar con el **método manual** (4-B), pon el **prefijo de tablas `wp_`**.
4. Entra en `tudominio/wp-admin` con el usuario que creó el instalador.

---

## 3. Subir el tema y el plugin

1. **Apariencia → Temas → Añadir nuevo → Subir tema** → `urbanizacion-theme.zip`
   → Instalar → **Activar**.
2. **Plugins → Añadir nuevo → Subir plugin** → `urbanizacion-mvp.zip`
   → Instalar → **Activar**. *(Al activarlo crea las páginas y el menú.)*
3. **Plugins → Añadir nuevo** → busca **bbPress** → Instalar → **Activar**.

---

## 4. Llevar tu contenido

### Opción A — All-in-One WP Migration (recomendada)

Mueve **todo de una vez** (base de datos + fotos + foro + tu cuenta) y cambia las
direcciones automáticamente.

1. En tu sitio **local**: `Plugins → Añadir nuevo → All-in-One WP Migration` → Activar.
2. `All-in-One WP Migration → Exportar → EXPORTAR A → Archivo` → **Descargar** el `.wpress`.
3. En el **hosting**: instala el mismo plugin → `Importar → Archivo` → sube el `.wpress`.
4. Al terminar, guarda los enlaces permanentes (dos veces) y entra con **tu cuenta**.

> **⚠️ Límite de importación gratis: ~20 MB.** Si el `.wpress` es más grande:
> - Borra los **temas que no usas** antes de exportar (*Apariencia → Temas*): los
>   temas por defecto de WordPress ocupan ~15 MB.
> - En **Opciones avanzadas** del export marca: *no exportar temas inactivos*,
>   *revisiones de entradas* ni *comentarios spam*.
>
> Con eso el archivo de esta web baja a ~12–15 MB y la importación pasa.

### Opción B — Manual (phpMyAdmin + FTP), sin límite de tamaño

Genera los archivos ligeros en tu ordenador:
```bash
./tools/backup.sh                     # crea backup/urbanizacion-db.sql y backup/uploads.tgz
gzip -k backup/urbanizacion-db.sql    # (opcional) lo comprime para phpMyAdmin
```
Después, en el hosting:
1. WordPress instalado con **prefijo `wp_`** (paso 2) y tema + plugin + bbPress (paso 3).
2. **phpMyAdmin** (panel del hosting) → tu base de datos → **Importar** →
   sube `urbanizacion-db.sql` (o el `.sql.gz`) → Continuar.
3. Por **FTP**, descomprime `uploads.tgz` dentro de `wp-content/uploads/`.
4. Instala el plugin **Better Search Replace** y reemplaza
   `http://localhost:5000` → `https://tudominio` en todas las tablas.
5. **Ajustes → Enlaces permanentes → Guardar.**

Más detalle en **[MIGRACION.md](MIGRACION.md)**.

---

## 5. Ajustes finales

- **HTTPS:** en el panel del hosting activa el **SSL gratuito** del dominio (puede
  tardar hasta una hora); luego pon la dirección con `https://` en
  *Ajustes → Generales*.
- **Enlaces permanentes:** *Ajustes → Enlaces permanentes → Nombre de la entrada → Guardar*.
- **Datos reales:** edita la página **Contacto**, sube tus fotos y borra el
  contenido de ejemplo.

---

## Hosting gratuito: ten en cuenta

- **Correo:** muchos hosting gratis (InfinityFree incluido) **no envían email**.
  Por eso "restablecer contraseña" y los avisos por correo no funcionarán.
  Solución: instala un plugin SMTP (**FluentSMTP** o **Brevo**) con un correo
  gratuito. La **aprobación de vecinos no necesita email**: la haces a mano en
  *Usuarios* (un aviso indica cuántos hay pendientes).
- **Límites:** CPU/visitas diarias y tamaño de importación. Para una urbanización
  pequeña suele bastar.
- **Copias de seguridad:** hazlas tú con `tools/backup.sh` o un plugin como
  **UpdraftPlus**.

---

## Checklist final

- [ ] Tema y plugin activos; **bbPress** activo.
- [ ] Contenido migrado (anuncios, noticias, foro, fotos, tu cuenta).
- [ ] URLs apuntando a tu dominio (sin `localhost`).
- [ ] **HTTPS** activo.
- [ ] Enlaces permanentes guardados.
- [ ] SMTP configurado (si quieres que salgan correos).
- [ ] Datos de **Contacto** y fotos reales puestos.
- [ ] Contenido de ejemplo eliminado.
