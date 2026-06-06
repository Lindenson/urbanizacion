<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <div class="page-hero">
        <div class="container"><h1><?php the_title(); ?></h1></div>
    </div>

    <div class="content">
        <div class="container">
            <article class="entry">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large', ['class' => 'entry__featured']); ?>
                <?php endif; ?>
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
        </div>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
