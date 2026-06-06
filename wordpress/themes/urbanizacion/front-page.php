<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<!-- Héroe -->
<section class="hero" style="background-image:url('<?php echo esc_url(urb_hero_image_url()); ?>');">
    <div class="container">
        <div class="hero__inner">
            <h1><?php bloginfo('name'); ?></h1>
            <p><?php echo esc_html(get_bloginfo('description') ?: 'Portal de vecinos: información, anuncios y participación de nuestra urbanización.'); ?></p>
            <div class="hero__actions">
                <a class="btn" href="<?php echo esc_url(home_url('/la-urbanizacion/')); ?>">Conoce la urbanización</a>
                <?php if (current_user_can('umvp_acceso')) : ?>
                    <a class="btn btn--blue" href="<?php echo esc_url(get_post_type_archive_link('announcement')); ?>">Ver anuncios</a>
                <?php elseif (!is_user_logged_in()) : ?>
                    <a class="btn btn--blue" href="<?php echo esc_url(wp_login_url(home_url('/'))); ?>">Acceso vecinos</a>
                    <?php if (get_option('users_can_register')) : ?>
                        <a class="btn btn--ghost" href="<?php echo esc_url(wp_registration_url()); ?>">Registrarse</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Sobre la urbanización -->
<section class="section section--tint">
    <div class="container">
        <div class="split">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/urbanizacion.png')); ?>" alt="Nuestra urbanización">
            <div>
                <span class="eyebrow">Nuestra comunidad</span>
                <h2>Una urbanización para vivir y convivir</h2>
                <p>Descubre nuestras zonas comunes, servicios e instalaciones. Aquí encontrarás toda la información oficial de la administración y un espacio para participar en las decisiones de la comunidad.</p>
                <p><a class="btn btn--ghost" href="<?php echo esc_url(home_url('/la-urbanizacion/')); ?>">Saber más</a></p>
            </div>
        </div>
    </div>
</section>

<!-- Zona de vecinos -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="eyebrow">Zona de vecinos</span>
            <h2>Lo último de la comunidad</h2>
        </div>

        <?php if (current_user_can('umvp_acceso')) :
            $latest = new WP_Query([
                'post_type'      => ['announcement', 'noticia'],
                'posts_per_page' => 6,
                'no_found_rows'  => true,
            ]);
            if ($latest->have_posts()) : ?>
                <div class="cards">
                    <?php while ($latest->have_posts()) : $latest->the_post(); urb_card(); endwhile; ?>
                </div>
            <?php else : ?>
                <p style="text-align:center;color:var(--muted);">Todavía no hay publicaciones. La administración irá añadiendo anuncios y noticias.</p>
            <?php endif;
            wp_reset_postdata();
        elseif (is_user_logged_in()) : ?>
            <div class="notice">
                <h3>Tu cuenta está pendiente de aprobación</h3>
                <p>Gracias por registrarte. La administración revisará tu solicitud y activará tu acceso a la zona de vecinos. Recibirás acceso en cuanto te aprueben.</p>
            </div>
        <?php else : ?>
            <div class="notice">
                <h3>Contenido reservado a los vecinos</h3>
                <p>Los anuncios, noticias, órdenes del día, propuestas de acuerdo y el foro están disponibles solo para vecinos registrados.</p>
                <p>
                    <a class="btn" href="<?php echo esc_url(wp_login_url(home_url('/'))); ?>">Iniciar sesión</a>
                    <?php if (get_option('users_can_register')) : ?>
                        <a class="btn btn--ghost" href="<?php echo esc_url(wp_registration_url()); ?>">Registrarse como vecino</a>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Accesos rápidos -->
<section class="section section--tint">
    <div class="container">
        <div class="section__head">
            <span class="eyebrow">Accesos rápidos</span>
            <h2>Todo lo que necesitas</h2>
        </div>
        <div class="quicklinks">
            <a class="quicklink" href="<?php echo esc_url(get_post_type_archive_link('announcement')); ?>">
                <span class="dashicons dashicons-megaphone"></span><strong>Anuncios</strong>
                <span>Comunicados de la administración</span>
            </a>
            <a class="quicklink" href="<?php echo esc_url(get_post_type_archive_link('noticia')); ?>">
                <span class="dashicons dashicons-admin-post"></span><strong>Noticias</strong>
                <span>Novedades de la comunidad</span>
            </a>
            <a class="quicklink" href="<?php echo esc_url(get_post_type_archive_link('agenda')); ?>">
                <span class="dashicons dashicons-calendar-alt"></span><strong>Órdenes del día</strong>
                <span>Convocatorias de juntas</span>
            </a>
            <a class="quicklink" href="<?php echo esc_url(get_post_type_archive_link('propuesta')); ?>">
                <span class="dashicons dashicons-clipboard"></span><strong>Propuestas</strong>
                <span>Proyectos de acuerdo</span>
            </a>
            <a class="quicklink" href="<?php echo esc_url(home_url('/foros/')); ?>">
                <span class="dashicons dashicons-format-chat"></span><strong>Foro</strong>
                <span>Preguntas y debate</span>
            </a>
            <a class="quicklink" href="<?php echo esc_url(home_url('/contacto/')); ?>">
                <span class="dashicons dashicons-phone"></span><strong>Contacto</strong>
                <span>Administrador y servicios</span>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
