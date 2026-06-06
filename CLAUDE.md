# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress site (in Spanish) for a residential community ("urbanización"), run via Docker. Two custom pieces of code live in this repo and are bind-mounted into the WordPress container; WordPress core and the MySQL data live in Docker volumes (not committed):

- **Plugin** `wordpress/plugins/urbanizacion-mvp/` — all functionality: content types, the `vecino` role, access control, and one-time content/menu seeding.
- **Theme** `wordpress/themes/urbanizacion/` — all design (Mediterranean style: terracotta / Mediterranean blue / sand).

No build step, no tests, no dependency manager. PHP is validated by loading it in the container.

## Running

```bash
docker compose up -d        # start MySQL + WordPress (http://localhost:5000)
docker compose down         # stop (volumes persist)
docker compose logs -f wordpress
```

The plugin and theme are bind-mounted, so edits to PHP/CSS are live immediately — no rebuild. But note: the plugin's seeding and CPT rewrite rules run on the **activation hook**, so after pulling changes to those, deactivate + reactivate the plugin (see below).

### WP-CLI against this stack

The `wordpress:latest` image has no WP-CLI. Run it as a one-off container that shares the volumes and network — and pass the DB env vars, because the WordPress image's `wp-config.php` reads them from the environment at runtime (omitting them makes WP-CLI fail with "database server at `mysql`"):

```bash
docker run --rm --network urba_default --volumes-from urbanizacion-wp -u 33 \
  -e WORDPRESS_DB_HOST=db:3306 -e WORDPRESS_DB_USER=wpuser \
  -e WORDPRESS_DB_PASSWORD=wppassword -e WORDPRESS_DB_NAME=wordpress \
  wordpress:cli wp <command>
```

(`urba_default` is the compose network; `-u 33` is www-data.) Common: `wp theme activate urbanizacion`, `wp plugin activate urbanizacion-mvp`, `wp plugin deactivate … && wp plugin activate …` to re-run seeding.

### PHP lint

```bash
docker compose exec -T wordpress sh -c 'for f in /var/www/html/wp-content/themes/urbanizacion/*.php /var/www/html/wp-content/plugins/urbanizacion-mvp/*.php; do php -l "$f"; done'
```

### Pretty permalinks gotcha

The site uses `/anuncios/`, `/noticias/`, etc. WP-CLI's `wp rewrite flush` **cannot** write the `.htaccess` in this image (it warns and leaves the block empty → every pretty URL 404s). Fix by writing the standard WordPress rewrite block into `/var/www/html/.htaccess` directly (the image already sets `AllowOverride All` for `/var/www/` and loads `mod_rewrite`). The home page works regardless because it's served via the front-page query, which masks this — always test a sub-page like `/contacto/`.

## Architecture

### Plugin (`urbanizacion-mvp.php`), `umvp_` prefix

- **Four custom post types** registered on `init`: `announcement` (Anuncios), `noticia` (Noticias), `agenda` (Órdenes del día), `propuesta` (Propuestas de acuerdo). They are `public => true` (so they have front-end URLs/archives and a nice editor) — privacy is **not** enforced by the CPT flags but by the access-control hook below.
- **`vecino` role** created on activation.
- **Access control** (`umvp_restrict_access` on `template_redirect`): anonymous visitors are redirected to the login page for the four protected CPTs (singular + archive) and for bbPress. The public surface is exactly the regular Pages (Inicio, La Urbanización, Contacto). The set of protected types is `umvp_protected_post_types()` — extend there when adding private sections.
- **Seeding** (`umvp_seed_content`, guarded by the `umvp_seeded` option, runs on activation): creates the three Pages, sets the static front page, and builds the "Menú principal" assigned to the theme's `primary` location. Because it is an activation hook, **changes to seeding only take effect on reactivation**.

### Theme (`urbanizacion`)

Classic PHP templates (not block theme). `functions.php` registers menus (`primary`, `footer`), image size `urb-card`, custom-header + custom-logo support, enqueues Google Fonts + `style.css` + `assets/js/main.js`, and defines two helpers used by templates: `urb_card()` (listing card) and `urb_hero_image_url()`. `front-page.php` is a custom homepage whose "zona de vecinos" block branches on `is_user_logged_in()` — logged-in users see latest anuncios/noticias, anonymous users see a login CTA (so private titles never leak on the public home). `archive.php`/`single.php` are generic across the CPTs and render the post type's label. The default hero image is `assets/img/urbanizacion.png`.

### Forum & registration

The Q&A / discussion forum is **bbPress** (installed in the `wordpress_data` volume — not committed). Two forums exist: *Preguntas a la administración* (residents ask, admin answers) and *Problemas de la urbanización* (residents discuss). bbPress root slug is set to `foros` (`_bbp_root_slug` option) to match the seeded menu link `/foros/`; single forums live at `/foros/forum/<slug>/`. The access-control hook (`is_bbpress()`) restricts the whole forum to logged-in users.

**Self-registration with admin approval.** `users_can_register = 1` and `default_role = vecino_pendiente` (both set on plugin activation). Flow:

- A resident registers via `wp-login.php?action=register` → gets the **`vecino_pendiente`** role, which does **not** have the `umvp_acceso` capability. They are logged in but blocked from every private section (redirected to `/?pendiente=1`, and the homepage shows a "pending approval" notice).
- The admin approves from **Users**: a per-row "Aprobar como vecino" action and a "Aprobar como vecino" bulk action (both `promote_users`-gated, nonce-checked), plus an `admin_notices` banner counting pending residents. Approval swaps the role to **`vecino`** (which has `umvp_acceso`) — access opens immediately, no re-login. See `umvp_approve_user()` and the `3b` block in the plugin.

Access is gated on the **`umvp_acceso` capability** (`UMVP_CAP`), held by `vecino` and `administrator` — NOT on `is_user_logged_in()`. When adding any access check, use `current_user_can(UMVP_CAP)`, not the login state, or pending users will leak in. bbPress's `_bbp_default_role` is `bbp_participant`, so an approved vecino can create topics and reply in both forums.

## Conventions

- All UI strings and content are in **Spanish**.
- Functional behavior → plugin; visual/markup → theme. Keep that separation.
