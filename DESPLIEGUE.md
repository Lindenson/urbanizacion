# Despliegue en un hosting de WordPress

Hay dos formas de poner el sitio en producción. La **opción A** es la habitual
si contratas un hosting de WordPress (cPanel, Plesk, hosting gestionado…). La
**opción B** es para un servidor propio (VPS) con Docker.

En ambos casos, el plugin al activarse crea las páginas, la portada y el menú
automáticamente.

---

## Opción A — Hosting de WordPress (subir tema y plugin)

### 1. Genera los paquetes ZIP

En tu ordenador, dentro del proyecto:

```bash
./tools/package.sh
```

Se crean en `dist/`:
- `dist/urbanizacion-theme.zip`
- `dist/urbanizacion-mvp.zip`

(Si no puedes ejecutar el script, comprime a mano las carpetas
`wordpress/themes/urbanizacion` y `wordpress/plugins/urbanizacion-mvp`.)

### 2. Instala WordPress
Usa el instalador de tu hosting (la mayoría tiene "Instalar WordPress" en un
clic) o instálalo manualmente. Necesitarás una base de datos MySQL/MariaDB.

### 3. Sube el tema
`wp-admin` → **Apariencia → Temas → Añadir nuevo → Subir tema** →
elige `urbanizacion-theme.zip` → **Instalar** → **Activar**.

### 4. Sube el plugin
**Plugins → Añadir nuevo → Subir plugin** → elige `urbanizacion-mvp.zip` →
**Instalar** → **Activar**. (Al activarlo se crean las páginas y el menú.)

### 5. Instala el foro
**Plugins → Añadir nuevo** → busca **bbPress** → **Instalar** → **Activar**.
Las dos secciones del foro (*Preguntas a la administración* y *Problemas de la
urbanización*) puedes crearlas en **Foros → Añadir nuevo**, o se conservan si
migras la base de datos.

### 6. Enlaces permanentes
**Ajustes → Enlaces permanentes** → **Nombre de la entrada** → **Guardar**.
(Esto hace que las direcciones sean `/anuncios/`, `/noticias/`, `/foros/`…)

### 7. Comprueba el registro
**Ajustes → Generales**: "Cualquiera puede registrarse" debe estar activo y el
"Perfil predeterminado" debe ser **Vecino (pendiente)**. El plugin lo configura
al activarse; verifícalo por si tu hosting lo sobrescribe.

### 8. Datos reales
- Cambia el contenido de **Contacto** (teléfonos, email, horario).
- Sube tus fotos (cabecera, galería de "La Urbanización", imágenes destacadas).
- Borra el contenido de ejemplo si no lo necesitas.

---

## Opción B — Servidor propio con Docker (VPS)

1. Instala Docker y Docker Compose en el servidor.
2. Clona el repositorio y configura las variables:

   ```bash
   git clone https://github.com/Lindenson/urbanizacion.git
   cd urbanizacion
   cp .env.example .env
   nano .env          # pon contraseñas fuertes y, si quieres, otro WP_PORT
   docker compose up -d
   ```

3. Pon un **proxy inverso con HTTPS** delante (Nginx, Caddy o Traefik) apuntando
   al puerto `WP_PORT`. WordPress debe servirse por `https://tudominio`.
4. Entra en `https://tudominio`, completa el asistente y sigue los pasos 3–8 de
   la Opción A (activar tema, activar plugin, bbPress, permalinks, datos reales).

> En este modo, el tema y el plugin se montan desde el repositorio (vía
> `docker-compose.yaml`), así que para actualizarlos basta con `git pull`.

---

## Checklist de producción

- [ ] Contraseñas de base de datos fuertes (en `.env`, **nunca** en el repo).
- [ ] HTTPS activo (certificado SSL).
- [ ] Copias de seguridad automáticas (p. ej. plugin **UpdraftPlus**).
- [ ] WordPress, tema, plugins y bbPress actualizados.
- [ ] Enlaces permanentes en "Nombre de la entrada".
- [ ] Registro de vecinos probado de principio a fin (registrar → aprobar → entrar).
- [ ] Datos de **Contacto** y fotos reales puestos.
- [ ] Contenido de ejemplo eliminado.
