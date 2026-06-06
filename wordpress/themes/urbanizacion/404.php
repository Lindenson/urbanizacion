<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="page-hero">
    <div class="container"><h1>Página no encontrada</h1></div>
</div>

<div class="content">
    <div class="container">
        <article class="entry" style="text-align:center;">
            <p>Lo sentimos, la página que buscas no existe o se ha movido.</p>
            <p><a class="btn" href="<?php echo esc_url(home_url('/')); ?>">Volver al inicio</a></p>
        </article>
    </div>
</div>

<?php get_footer(); ?>
