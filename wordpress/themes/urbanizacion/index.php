<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="page-hero">
    <div class="container">
        <h1><?php is_search() ? printf('Resultados de: %s', esc_html(get_search_query())) : bloginfo('name'); ?></h1>
    </div>
</div>

<div class="content">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="cards">
                <?php while (have_posts()) : the_post(); urb_card(); endwhile; ?>
            </div>
            <div class="pagination"><?php echo paginate_links(['mid_size' => 1]); ?></div>
        <?php else : ?>
            <p style="text-align:center;color:var(--muted);">No se ha encontrado contenido.</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
