<?php if (!defined('ABSPATH')) exit; ?>
</main><!-- #main -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-cols">
            <div>
                <h4><?php bloginfo('name'); ?></h4>
                <p><?php echo esc_html(get_bloginfo('description')); ?></p>
            </div>
            <div>
                <h4>Secciones</h4>
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu(['theme_location' => 'footer', 'container' => false]);
                } elseif (has_nav_menu('primary')) {
                    wp_nav_menu(['theme_location' => 'primary', 'container' => false]);
                }
                ?>
            </div>
            <div>
                <h4>Contacto</h4>
                <ul>
                    <li>Administración de la urbanización</li>
                    <li><a href="<?php echo esc_url(home_url('/contacto/')); ?>">Datos de contacto y servicios</a></li>
                    <li>Emergencias: 112</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?>. Portal de vecinos.
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
