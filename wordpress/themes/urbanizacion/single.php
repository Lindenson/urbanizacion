<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<?php while (have_posts()) : the_post();
    $archive = get_post_type_archive_link(get_post_type());
?>
    <div class="page-hero">
        <div class="container">
            <p class="lead"><?php echo esc_html(urb_post_type_label()); ?></p>
            <h1><?php the_title(); ?></h1>
            <p class="lead"><?php echo esc_html(get_the_date()); ?></p>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <article class="entry">
                <?php if ($archive) : ?>
                    <a class="back-link" href="<?php echo esc_url($archive); ?>">← Volver a <?php echo esc_html(get_post_type_object(get_post_type())->labels->name); ?></a>
                <?php endif; ?>

                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large', ['class' => 'entry__featured']); ?>
                <?php endif; ?>

                <div class="entry-content"><?php the_content(); ?></div>

                <?php if (comments_open() || get_comments_number()) { comments_template(); } ?>
            </article>
        </div>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
