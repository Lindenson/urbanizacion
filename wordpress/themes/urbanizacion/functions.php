<?php
if (!defined('ABSPATH')) exit;

/* Configuración del tema -------------------------------------------------- */
function urb_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', ['height' => 60, 'width' => 200, 'flex-width' => true, 'flex-height' => true]);
    add_theme_support('custom-header', [
        'default-image' => get_theme_file_uri('assets/img/urbanizacion.png'),
        'width'         => 1920,
        'height'        => 900,
        'flex-height'   => true,
        'flex-width'    => true,
        'header-text'   => false,
    ]);
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);

    add_image_size('urb-card', 720, 480, true);

    register_nav_menus([
        'primary' => 'Menú principal',
        'footer'  => 'Menú del pie',
    ]);
}
add_action('after_setup_theme', 'urb_setup');

/* Estilos y scripts ------------------------------------------------------- */
function urb_assets() {
    wp_enqueue_style(
        'urb-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;500;600&display=swap',
        [],
        null
    );
    wp_enqueue_style('dashicons');
    wp_enqueue_style('urb-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_script('urb-main', get_theme_file_uri('assets/js/main.js'), [], wp_get_theme()->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'urb_assets');

/* Imagen del héroe: portada del tema o imagen destacada ------------------- */
function urb_hero_image_url() {
    if (has_header_image()) {
        return get_header_image();
    }
    return get_theme_file_uri('assets/img/urbanizacion.png');
}

/* Etiqueta legible del tipo de contenido ---------------------------------- */
function urb_post_type_label($post = null) {
    $obj = get_post_type_object(get_post_type($post));
    return $obj ? $obj->labels->singular_name : '';
}

/* Longitud del extracto --------------------------------------------------- */
function urb_excerpt_length($len) { return 24; }
add_filter('excerpt_length', 'urb_excerpt_length');
function urb_excerpt_more($more) { return '…'; }
add_filter('excerpt_more', 'urb_excerpt_more');

/* Añadir "Salir" / "Acceder" al menú principal ---------------------------- */
function urb_menu_auth_item($items, $args) {
    if (isset($args->theme_location) && $args->theme_location === 'primary') {
        if (is_user_logged_in()) {
            $items .= '<li class="menu-item menu-item-auth"><a href="' . esc_url(wp_logout_url(home_url('/'))) . '">Salir</a></li>';
        } else {
            $items .= '<li class="menu-item menu-item-auth"><a href="' . esc_url(wp_login_url(home_url('/'))) . '">Acceder</a></li>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'urb_menu_auth_item', 10, 2);

/* Una tarjeta para los listados ------------------------------------------- */
function urb_card() { ?>
    <article class="card">
        <?php if (has_post_thumbnail()) : ?>
            <a class="card__thumb" href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('urb-card'); ?>
            </a>
        <?php endif; ?>
        <div class="card__body">
            <span class="card__type"><?php echo esc_html(urb_post_type_label()); ?></span>
            <h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p class="card__meta"><?php echo esc_html(get_the_date()); ?></p>
            <p class="card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22, '…')); ?></p>
            <span class="card__more">Leer más →</span>
        </div>
    </article>
<?php }
