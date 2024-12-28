<?php

/**
 * company in the loop.
 *
 * This template can be overridden by copying it to yourtheme/mas-wp-job-manager-company/content-company.php.
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $post;

// Ensure visibility.
if (empty($post)) {
    return;
}

?>

<li <?php mas_wpjmc_company_class(); ?>>
    <a href="<?php the_permalink(); ?>" class="company-title--link">
        <?php
        do_action('company_content_area_before');

        do_action('company_start');

        ?>
        <div class="company-logo">
            <?php $logo =  get_the_company_logo(null, 'thumbnail') ? get_the_company_logo(null, 'thumbnail') : apply_filters('job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png'); ?>
            <img src="<?php echo esc_url($logo) ?>" class="company-logo--image" alt="<?php the_title(); ?>">


            <ul id="company-meta">
                <?php if (!empty(get_post_meta($post->ID, '_company_location', true))) { ?><li id="company-meta-location"><?php echo get_post_meta($post->ID, '_company_location', true) ?> </li><?php } ?>

                <?php
                $args = apply_filters('mas_wpjmc_single_company_features_args', array(
                    'company_headquarters'  => array(
                        'title' => esc_html__('Headquarters', 'workscout'),
                        'content' => mas_wpjmc_get_the_meta_data('_company_headquarters'),
                        'icon' => 'icon-material-outline-business'
                    ),
                    'company_since'  => array(
                        'title' => esc_html__('Founded', 'workscout'),
                        'content' => mas_wpjmc_get_the_meta_data('_company_since'),
                        'icon' => 'icon-material-outline-date-range'
                    ),
                    'company_strength'  => array(
                        'title' => esc_html__('a', 'workscout'),
                        'content' => mas_wpjmc_get_taxomony_data('company_strength'),
                        'icon' => 'icon-material-outline-supervisor-account'
                    ),


                    'company_average_salary'  => array(
                        'title' => esc_html__('Avg. Salary', 'workscout'),
                        'content' => mas_wpjmc_get_taxomony_data('company_average_salary'),
                        'icon' => 'icon-material-outline-money'
                    ),
                ));

                if (is_array($args) && count($args) > 0) {
                    $i = 0;
                    foreach ($args as $key => $arg) :
                        if (isset($arg['content']) && !empty($arg['content'])) :
                            $i++;
                            break;
                        endif;
                    endforeach;
                    if ($i > 0) :

                        foreach ($args as $key => $arg) :

                            if (isset($arg['content']) && !empty($arg['content'])) :
                ?>
                                <li class=" company-meta-<?php esc_attr_e($key) ?>" id="company-meta-<?php esc_attr_e($key) ?>"><?php echo wp_kses_post($arg['content']); ?>
                                </li>

                <?php
                            endif;
                        endforeach;
                    endif;
                } ?>

            </ul>
        </div>
        <div class="company-body">
            <h3 class="company-title">
                <?php the_title(); ?>
            </h3>
            <?php if (!empty($company_tagline = mas_wpjmc_get_the_meta_data('_company_tagline'))) : ?>
                <p class="company-data_tagline"><?php echo esc_html($company_tagline); ?></p>
            <?php endif; ?>

            <?php
            if (function_exists('mas_wpjmcr_get_reviews_count') && mas_wpjmcr_get_reviews_count() > 0) { ?>
                <div class="company-list-rating">
                    <?php
                    $rating =  mas_wpjmcr_get_reviews_average();

                    $number = mas_wpjmcr_get_reviews_count(); ?>
                    <div class="star-rating" data-rating="<?php echo number_format_i18n($rating, 1); ?>"></div>
                    <div class="rating-counter">(<?php printf(_n('%s review', '%s reviews', $number, 'workscout'), number_format_i18n($number));  ?>)</div>
                </div>
            <?php } else { ?>
                <div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout'); ?></div>
            <?php } ?>
            <div class="company-list-excerpt"><?php $excerpt = get_post_meta($post->ID, '_company_excerpt', true);
                                                if (!empty($excerpt)) {
                                                    echo wpautop($excerpt);
                                                } else {
                                                    the_excerpt();
                                                } ?></div>
        </div>
        <?php

        do_action('company_end');

        do_action('company_content_area_after');
        ?>
    </a>
</li>