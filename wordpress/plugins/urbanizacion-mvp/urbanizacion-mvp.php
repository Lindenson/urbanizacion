<?php
/*
Plugin Name: Urbanizacion MVP
Description: Portal de vecinos: información de la urbanización, contactos, anuncios, noticias, órdenes del día de las juntas, propuestas de acuerdo y foro privado (bbPress).
Version: 0.2
Author: MVP
Text Domain: urbanizacion-mvp
*/

if (!defined('ABSPATH')) exit;

/* ===========================================================================
 * 1. Tipos de contenido (Custom Post Types)
 *
 * Son "public => true" para que tengan página y archivo en el front-end,
 * pero el acceso de los visitantes anónimos se restringe en la sección 3.
 * ========================================================================= */

function umvp_post_types() {

    register_post_type('announcement', [
        'labels' => [
            'name'          => 'Anuncios',
            'singular_name' => 'Anuncio',
            'add_new_item'  => 'Añadir anuncio',
            'edit_item'     => 'Editar anuncio',
            'all_items'     => 'Todos los anuncios',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => 'anuncios',
        'menu_icon'    => 'dashicons-megaphone',
        'rewrite'      => ['slug' => 'anuncios'],
        'supports'     => ['title', 'editor', 'author', 'thumbnail', 'excerpt'],
    ]);

    register_post_type('noticia', [
        'labels' => [
            'name'          => 'Noticias',
            'singular_name' => 'Noticia',
            'add_new_item'  => 'Añadir noticia',
            'edit_item'     => 'Editar noticia',
            'all_items'     => 'Todas las noticias',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => 'noticias',
        'menu_icon'    => 'dashicons-admin-post',
        'rewrite'      => ['slug' => 'noticias'],
        'supports'     => ['title', 'editor', 'author', 'thumbnail', 'excerpt'],
    ]);

    register_post_type('agenda', [
        'labels' => [
            'name'          => 'Órdenes del día',
            'singular_name' => 'Orden del día',
            'add_new_item'  => 'Añadir orden del día',
            'edit_item'     => 'Editar orden del día',
            'all_items'     => 'Órdenes del día',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => 'ordenes-del-dia',
        'menu_icon'    => 'dashicons-calendar-alt',
        'rewrite'      => ['slug' => 'ordenes-del-dia'],
        'supports'     => ['title', 'editor', 'author', 'thumbnail', 'excerpt'],
    ]);

    register_post_type('propuesta', [
        'labels' => [
            'name'          => 'Propuestas de acuerdo',
            'singular_name' => 'Propuesta de acuerdo',
            'add_new_item'  => 'Añadir propuesta',
            'edit_item'     => 'Editar propuesta',
            'all_items'     => 'Propuestas de acuerdo',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => 'propuestas',
        'menu_icon'    => 'dashicons-clipboard',
        'rewrite'      => ['slug' => 'propuestas'],
        'supports'     => ['title', 'editor', 'author', 'thumbnail', 'excerpt'],
    ]);
}
add_action('init', 'umvp_post_types');


/* ===========================================================================
 * 2. Roles y permisos
 *
 * - "vecino"            : vecino aprobado, con acceso a la zona privada
 *                         (capacidad "umvp_acceso").
 * - "vecino_pendiente"  : recién registrado, SIN acceso hasta que la
 *                         administración lo apruebe.
 * ========================================================================= */

const UMVP_CAP = 'umvp_acceso';

function umvp_create_roles() {

    if (!get_role('vecino')) {
        add_role('vecino', 'Vecino', ['read' => true]);
    }
    if ($vecino = get_role('vecino')) {
        $vecino->add_cap(UMVP_CAP);
    }

    if (!get_role('vecino_pendiente')) {
        add_role('vecino_pendiente', 'Vecino (pendiente)', ['read' => true]);
    }

    // El administrador siempre tiene acceso a la zona privada en el front-end.
    if ($admin = get_role('administrator')) {
        $admin->add_cap(UMVP_CAP);
    }
}


/* ===========================================================================
 * 3. Control de acceso
 *
 * Público: inicio, "La Urbanización" y "Contacto" (páginas normales).
 * Privado (solo usuarios identificados): anuncios, noticias, órdenes del día,
 * propuestas de acuerdo y el foro (bbPress).
 * ========================================================================= */

function umvp_protected_post_types() {
    return ['announcement', 'noticia', 'agenda', 'propuesta'];
}

function umvp_restrict_access() {

    if (is_admin()) return;

    // Vecinos aprobados y administradores tienen acceso.
    if (current_user_can(UMVP_CAP)) return;

    $protected = umvp_protected_post_types();
    $restrict  = false;

    if (is_singular($protected) || is_post_type_archive($protected)) {
        $restrict = true;
    }

    // Foro bbPress (si el plugin está activo)
    if (function_exists('is_bbpress') && is_bbpress()) {
        $restrict = true;
    }

    if ($restrict) {
        if (is_user_logged_in()) {
            // Registrado pero pendiente de aprobación.
            wp_safe_redirect(add_query_arg('pendiente', '1', home_url('/')));
        } else {
            $target = is_singular() ? get_permalink() : home_url(add_query_arg([], $_SERVER['REQUEST_URI'] ?? '/'));
            wp_safe_redirect(wp_login_url($target));
        }
        exit;
    }
}
add_action('template_redirect', 'umvp_restrict_access');


/* ===========================================================================
 * 3b. Aprobación de vecinos por la administración
 *
 * Los nuevos registros entran como "vecino_pendiente". El administrador los
 * aprueba desde Usuarios (enlace por fila o acción en lote) y pasan a "vecino".
 * ========================================================================= */

function umvp_approve_user($user_id) {
    $u = get_userdata($user_id);
    if ($u && in_array('vecino_pendiente', (array) $u->roles, true)) {
        $u->remove_role('vecino_pendiente');
        $u->add_role('vecino');
        return true;
    }
    return false;
}

// Enlace "Aprobar" en cada fila de usuario pendiente.
function umvp_user_row_actions($actions, $user) {
    if (current_user_can('promote_users') && in_array('vecino_pendiente', (array) $user->roles, true)) {
        $url = wp_nonce_url(
            add_query_arg(['action' => 'umvp_aprobar', 'user' => $user->ID], admin_url('users.php')),
            'umvp_aprobar_' . $user->ID
        );
        $actions['umvp_aprobar'] = '<a href="' . esc_url($url) . '">Aprobar como vecino</a>';
    }
    return $actions;
}
add_filter('user_row_actions', 'umvp_user_row_actions', 10, 2);

// Procesar la aprobación individual.
function umvp_handle_approval() {
    if (empty($_GET['action']) || $_GET['action'] !== 'umvp_aprobar') return;
    $uid = isset($_GET['user']) ? (int) $_GET['user'] : 0;
    if (!$uid || !current_user_can('promote_users')) return;
    check_admin_referer('umvp_aprobar_' . $uid);
    umvp_approve_user($uid);
    wp_safe_redirect(add_query_arg('umvp_aprobado', 1, admin_url('users.php')));
    exit;
}
add_action('admin_init', 'umvp_handle_approval');

// Acción en lote "Aprobar como vecino".
function umvp_bulk_actions($actions) {
    $actions['umvp_aprobar'] = 'Aprobar como vecino';
    return $actions;
}
add_filter('bulk_actions-users', 'umvp_bulk_actions');

function umvp_handle_bulk($redirect, $action, $user_ids) {
    if ($action !== 'umvp_aprobar' || !current_user_can('promote_users')) return $redirect;
    $count = 0;
    foreach ((array) $user_ids as $uid) {
        if (umvp_approve_user((int) $uid)) $count++;
    }
    return add_query_arg('umvp_aprobado', $count, $redirect);
}
add_filter('handle_bulk_actions-users', 'umvp_handle_bulk', 10, 3);

// Aviso con el número de vecinos pendientes.
function umvp_pending_notice() {
    if (!current_user_can('promote_users')) return;

    if (isset($_GET['umvp_aprobado'])) {
        printf('<div class="notice notice-success is-dismissible"><p>%d vecino(s) aprobado(s).</p></div>', (int) $_GET['umvp_aprobado']);
    }

    $pending = count(get_users(['role' => 'vecino_pendiente', 'fields' => 'ID']));
    if ($pending > 0) {
        printf(
            '<div class="notice notice-warning"><p>Hay <strong>%d</strong> vecino(s) pendiente(s) de aprobación. <a href="%s">Revisar solicitudes</a>.</p></div>',
            $pending,
            esc_url(admin_url('users.php?role=vecino_pendiente'))
        );
    }
}
add_action('admin_notices', 'umvp_pending_notice');


/* ===========================================================================
 * 3c. Ocultar el escritorio (wp-admin) y la barra de administración
 *
 * Los vecinos (y los pendientes) no gestionan el sitio: no deben ver el panel
 * de WordPress ni la barra negra superior. Solo quien puede editar contenido
 * (administrador) conserva el acceso al escritorio.
 * ========================================================================= */

// Ocultar la barra superior a quien no gestiona contenido.
function umvp_hide_admin_bar($show) {
    return current_user_can('edit_posts') ? $show : false;
}
add_filter('show_admin_bar', 'umvp_hide_admin_bar');

// Bloquear el acceso a /wp-admin/ (salvo peticiones AJAX) y enviar a la portada.
function umvp_block_admin() {
    if (!current_user_can('edit_posts') && !wp_doing_ajax()) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
add_action('admin_init', 'umvp_block_admin');


/* ===========================================================================
 * 4. Contenido inicial (se ejecuta una sola vez, al activar el plugin)
 *
 * Crea las páginas básicas, fija la portada y construye el menú principal.
 * ========================================================================= */

function umvp_seed_content() {

    if (get_option('umvp_seeded')) return;

    // --- Páginas públicas ---------------------------------------------------
    $pages = [
        'inicio' => [
            'title'   => 'Inicio',
            'content' => '<!-- wp:paragraph --><p>Bienvenidos al portal de nuestra urbanización.</p><!-- /wp:paragraph -->',
        ],
        'la-urbanizacion' => [
            'title'   => 'La Urbanización',
            'content' =>
                '<!-- wp:heading --><h2>Sobre nuestra urbanización</h2><!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Describe aquí la urbanización: ubicación, número de viviendas, zonas comunes, piscina, jardines y servicios.</p><!-- /wp:paragraph -->' .
                '<!-- wp:gallery --><figure class="wp-block-gallery"><!-- Añade aquí tus fotos desde la biblioteca de medios --></figure><!-- /wp:gallery -->',
        ],
        'contacto' => [
            'title'   => 'Contacto',
            'content' =>
                '<!-- wp:heading --><h2>Administración</h2><!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Administrador: <strong>Nombre Apellidos</strong><br>Teléfono: 000 000 000<br>Email: administracion@ejemplo.es<br>Horario de oficina: L–V, 9:00–14:00</p><!-- /wp:paragraph -->' .
                '<!-- wp:heading --><h2>Servicios y emergencias</h2><!-- /wp:heading -->' .
                '<!-- wp:list --><ul><li>Emergencias: 112</li><li>Mantenimiento: 000 000 000</li><li>Limpieza: 000 000 000</li><li>Seguridad / portería: 000 000 000</li></ul><!-- /wp:list -->',
        ],
    ];

    $ids = [];
    foreach ($pages as $slug => $data) {
        $existing = get_page_by_path($slug);
        if ($existing) {
            $ids[$slug] = $existing->ID;
            continue;
        }
        $ids[$slug] = wp_insert_post([
            'post_title'   => $data['title'],
            'post_name'    => $slug,
            'post_content' => $data['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
    }

    // --- Portada estática ---------------------------------------------------
    if (!empty($ids['inicio'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $ids['inicio']);
    }

    // --- Menú principal -----------------------------------------------------
    $menu_name = 'Menú principal';
    if (!wp_get_nav_menu_object($menu_name)) {

        $menu_id = wp_create_nav_menu($menu_name);
        $pos = 0;

        $add_page = function ($title, $page_id) use ($menu_id, &$pos) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'     => $title,
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_id,
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
                'menu-item-position'  => ++$pos,
            ]);
        };

        $add_archive = function ($title, $post_type) use ($menu_id, &$pos) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'     => $title,
                'menu-item-object'    => $post_type,
                'menu-item-type'      => 'post_type_archive',
                'menu-item-status'    => 'publish',
                'menu-item-position'  => ++$pos,
            ]);
        };

        $add_link = function ($title, $url) use ($menu_id, &$pos) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'  => $title,
                'menu-item-url'    => $url,
                'menu-item-type'   => 'custom',
                'menu-item-status' => 'publish',
                'menu-item-position' => ++$pos,
            ]);
        };

        $add_page('Inicio', $ids['inicio']);
        $add_page('La Urbanización', $ids['la-urbanizacion']);
        $add_archive('Anuncios', 'announcement');
        $add_archive('Noticias', 'noticia');
        $add_archive('Órdenes del día', 'agenda');
        $add_archive('Propuestas', 'propuesta');
        $add_link('Foro', home_url('/foros/'));
        $add_page('Contacto', $ids['contacto']);

        $locations = get_theme_mod('nav_menu_locations', []);
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }

    update_option('umvp_seeded', 1);
}


/* ===========================================================================
 * 5. Activación
 * ========================================================================= */

function umvp_activate() {
    umvp_post_types();
    umvp_create_roles();
    umvp_seed_content();

    // Registro abierto, pero los nuevos usuarios quedan pendientes de aprobación.
    update_option('users_can_register', 1);
    update_option('default_role', 'vecino_pendiente');

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'umvp_activate');

function umvp_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'umvp_deactivate');
