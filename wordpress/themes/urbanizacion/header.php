<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                echo esc_html(get_bloginfo('name'));
            }
            ?>
        </a>

        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>

        <nav class="main-nav" aria-label="Menú principal">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(['theme_location' => 'primary', 'container' => false]);
            } else {
                echo '<ul><li><a href="' . esc_url(admin_url('nav-menus.php')) . '">Configura el menú principal</a></li></ul>';
            }
            ?>
        </nav>
    </div>
</header>

<main id="main">
