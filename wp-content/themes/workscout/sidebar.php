<?php

/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WorkScout
 */
$sidebar_width = get_post_meta($post->ID, 'pp_page_sidebar_width', true);

if($sidebar_width == 'narrow'){
$class = "col-xl-3 col-lg-3";
} else {
    $class = "five columns"; 
}

?>

<div class="<?php echo $class; ?> sidebar">
    <?php
    if (is_singular()) {
        $sidebar = get_post_meta($post->ID, "pp_sidebar_set", $single = true);
        if ($sidebar) {
            if (!dynamic_sidebar($sidebar)) : ?>

                <aside id="archives" class="widget">
                    <h4 class="widget-title"><?php esc_html_e('Archives', 'workscout'); ?></h4>
                    <ul>
                        <?php wp_get_archives(array('type' => 'monthly')); ?>
                    </ul>
                </aside>

                <aside id="meta" class="widget">
                    <h4 class="widget-title"><?php esc_html_e('Meta', 'workscout'); ?></h4>
                    <ul>
                        <?php wp_register(); ?>
                        <li><?php wp_loginout(); ?></li>
                        <?php wp_meta(); ?>
                    </ul>
                </aside>
            <?php endif;
        } else {
            if (!dynamic_sidebar('sidebar-1')) : ?>
                <aside id="archives" class="widget">
                    <h4 class="widget-title"><?php esc_html_e('Archives', 'workscout'); ?></h4>
                    <ul>
                        <?php wp_get_archives(array('type' => 'monthly')); ?>
                    </ul>
                </aside>

                <aside id="meta" class="widget">
                    <h4 class="widget-title"><?php esc_html_e('Meta', 'workscout'); ?></h4>
                    <ul>
                        <?php wp_register(); ?>
                        <li><?php wp_loginout(); ?></li>
                        <?php wp_meta(); ?>
                    </ul>
                </aside>
            <?php endif;
        }
    } else {
        if (!dynamic_sidebar('sidebar-1')) : ?>
            <aside id="archives" class="widget">
                <h4 class="widget-title"><?php esc_html_e('Archives', 'workscout'); ?></h4>
                <ul>
                    <?php wp_get_archives(array('type' => 'monthly')); ?>
                </ul>
            </aside>

            <aside id="meta" class="widget">
                <h4 class="widget-title"><?php esc_html_e('Meta', 'workscout'); ?></h4>
                <ul>
                    <?php wp_register(); ?>
                    <li><?php wp_loginout(); ?></li>
                    <?php wp_meta(); ?>
                </ul>
            </aside>
    <?php endif;
    } // end primary widget area
    ?>
</div><!-- #secondary -->