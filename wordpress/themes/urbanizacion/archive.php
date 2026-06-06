<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<div class="page-hero">
    <div class="container">
        <h1><?php post_type_archive_title(); ?></h1>
        <?php
        $obj = get_queried_object();
        if (!empty($obj->description)) {
            echo '<p class="lead">' . esc_html($obj->description) . '</p>';
        }
        ?>
    </div>
</div>

<div class="content">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="cards">
                <?php while (have_posts()) : the_post(); urb_card(); endwhile; ?>
            </div>

            <div class="pagination">
                <?php echo paginate_links(['mid_size' => 1]); ?>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--muted);">Todavía no hay publicaciones en esta sección.</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
