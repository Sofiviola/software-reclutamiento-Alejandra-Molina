<?php

/**
 * The template for displaying all single jobs.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WorkScout
 */

$header_old = Kirki::get_option('workscout', 'pp_old_header');
$header_type = (Kirki::get_option('workscout', 'pp_old_header') == true) ? 'old' : '';
$header_type = apply_filters('workscout_header_type', $header_type);
get_header($header_type); ?>


<?php
$layout = Kirki::get_option('workscout', 'pp_blog_layout');
if (empty($layout)) {
    $layout = 'right-sidebar';
}
while (have_posts()) : the_post();
?>
    <?php

    $header_image_url = get_post_meta($post->ID, '_header_image', TRUE);

    $header_image = apply_filters('workscout_single_company_header_image', $header_image_url);


    if (!empty($header_image)) { ?>
        <div id="company-background" style="background: url('<?php echo esc_url($header_image); ?>')"></div>
    <?php } ?>
    <div class="container <?php echo esc_attr($layout); ?>">

        <div class="company-contact-details">
            <?php if (!(function_exists('twentynineteen_can_show_post_thumbnail') && twentynineteen_can_show_post_thumbnail())) : ?>
                <div class="company-data">
                    <div class="company-logo">
                        <?php $logo =  get_the_company_logo(null, 'thumbnail') ? get_the_company_logo(null, 'thumbnail') : apply_filters('job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png'); ?>
                        <img src="<?php echo esc_url($logo) ?>" class="company-logo--image" alt="<?php the_title(); ?>">
                    </div>
                    <div class="company-data__content media-body">
                        <h1 class="company-title"> <?php
                                                    the_title();

                                                    $country = get_post_meta($post->ID, '_country', true);
                                                    if ($country) :
                                                        $countries = workscoutGetCountries();
                                                    ?>

                                <img class="flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" title="<?php echo $countries[$country]; ?>" data-tippy-placement="top" />

                            <?php endif; ?>
                        </h1>
                    <?php endif;

                if (!empty($company_tagline = mas_wpjmc_get_the_meta_data('_company_tagline'))) : ?>
                        <p class="company-data__content--list-item"><?php echo esc_html($company_tagline); ?></p>

                        <a href="<?php the_permalink(); ?>#comments-section"><?php endif;

                                                                            if (function_exists('mas_wpjmcr_get_reviews_count') && mas_wpjmcr_get_reviews_count() > 0) { ?>
                            <div class=" company-list-rating">
                                <?php
                                                                                $rating =  mas_wpjmcr_get_reviews_average();
                                                                                $number = mas_wpjmcr_get_reviews_count(); ?>
                                <div class="star-rating" data-rating="<?php echo number_format_i18n($rating, 1); ?>"></div>
                                <div class="rating-counter">(<?php printf(_n('%s review', '%s reviews', $number, 'workscout'), number_format_i18n($number));  ?>)</div>
                            </div>
                        <?php } else { ?>
                            <div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout'); ?></div>
                        <?php
                                                                            } ?>
                        </a>
                        <?php
                        if (!empty(mas_wpjmc_get_the_meta_data('_company_website'))  || !empty(mas_wpjmc_get_the_meta_data('_company_email')) || !empty(mas_wpjmc_get_the_meta_data('_company_twitter')) || !empty(mas_wpjmc_get_the_meta_data('_company_facebook')) || !empty(mas_wpjmc_get_the_meta_data('_company_phone'))) {
                        ?>
                            <div class="company-data__content--list _company_tagline">

                                <?php if (!empty($company_website = mas_wpjmc_get_the_meta_data('_company_website'))) : ?>
                                    <span class="company-data__content--list-item _company_website"><a class="website" href="<?php echo esc_url($company_website); ?>" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e('Website', 'workscout'); ?></a></span>
                                <?php endif; ?>
                                <?php if (!empty($company_email = mas_wpjmc_get_the_meta_data('_company_email'))) : ?>
                                    <span class="company-data__content--list-item _company_email">
                                        <a href="mailto:<?php echo ($company_email); ?>" target="_blank"><i class="fa fa-envelope"></i> <?php echo esc_html($company_email); ?></a>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($company_twitter = mas_wpjmc_get_the_meta_data('_company_twitter'))) : ?>
                                    <span class="company-data__content--list-item _company_twitter">
                                        <a href="<?php echo get_the_mas_company_twitter(); ?>">
                                            <i class="fa fa-twitter"></i>
                                            <?php esc_html_e('Twitter', 'workscout'); ?>
                                        </a></span>

                                <?php endif; ?>
                                <?php if (!empty($company_facebook = mas_wpjmc_get_the_meta_data('_company_facebook'))) : ?>
                                    <span class="company-data__content--list-item _company_phone"><a href="<?php echo esc_url($company_facebook); ?>">
                                            <i class="fa fa-facebook"></i>
                                            <?php esc_html_e('Facebook', 'workscout'); ?>
                                        </a></span>

                                <?php endif; ?>
                                <?php if (!empty($company_phone = mas_wpjmc_get_the_meta_data('_company_phone'))) : ?>
                                    <span class="company-data__content--list-item _company_phone">
                                        <a href="tel:<?php echo ($company_phone); ?>" target="_blank"><i class="fa fa-phone"></i>
                                            <?php echo esc_html($company_phone); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>



                            </div>
                        <?php
                        }
                        if (!(function_exists('twentynineteen_can_show_post_thumbnail') && twentynineteen_can_show_post_thumbnail())) : ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- eof company details -->


        <div class="eleven columns ">
            <div class="padding-right">
                <?php if (!empty(get_the_content())) { ?>
                    <h3><?php echo esc_html__('About Us', 'workscout'); ?></h3>
                    <div id="company_content">
                        <?php the_content(); ?>
                        <?php mas_wpjmc_single_company_video(); ?>
                    </div>

                <?php }

                global $post;
                $company_jobs = mas_wpjmc_workscout_get_the_company_job_listing();


                if (count($company_jobs)) :
                ?>
                    <div class="mas-company-jobs "><?php
                                                    ?><h3 class="mas-company-jobs__title"><?php echo esc_html__('Open Positions', 'workscout');
                                                                                        ?></h3><?php

                                                                                                    //  get_job_manager_template( 'job-listings-start.php' );
                                                                                                    ?>
                        <ul class="job_listings job-list full  grid-layout new-layout">
                            <?php

                            foreach ($company_jobs as $post) :
                                setup_postdata($post);
                                do_action('job_listing_loop');

                                get_job_manager_template_part('content-job_listing');

                            endforeach; // End of the loop. 
                            wp_reset_postdata();

                            get_job_manager_template('job-listings-end.php');
                            ?>
                    </div>
                    <?php
                endif;
                $company_tasks = workscout_get_the_company_tasks();


                if (count($company_tasks)) :

                    if (class_exists('WorkScout_Freelancer_Template_Loader')) {
                        $template_loader = new \WorkScout_Freelancer_Template_Loader;
                        $template_style = 'grid';

                        $style_data = array(
                            'style'         => $template_style,


                        );
                    ?>
                        <div class="mas-company-tasks ">
                            <h3 class="mas-company-tasks__title"><?php echo esc_html__('Open Tasks', 'workscout');  ?></h3>

                            <?php

                            $template_loader->set_template_data($style_data)->get_template_part('tasks-start');

                            foreach ($company_tasks as $post) :
                                setup_postdata($post);
                                $template_loader->set_template_data($style_data)->get_template_part('content-task', $template_style);

                            endforeach; // End of the loop. 
                            wp_reset_postdata();

                            $template_loader->set_template_data($style_data)->get_template_part('tasks-end');
                            ?>
                        </div>
                <?php
                    }
                endif; ?>
                <?php do_action('single_company_content_end'); ?>
            </div>
        </div>


        <div class="five columns" id="job-details">
            <?php
            $args = apply_filters('mas_wpjmc_single_company_features_args', array(
                'company_location'  => array(
                    'title' => esc_html__('Location', 'workscout'),
                    'content' => mas_wpjmc_get_the_meta_data('_company_location'),
                    'icon' => 'icon-material-outline-location-on'
                ),
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
                    'title' => esc_html__('Company Size', 'workscout'),
                    'content' => mas_wpjmc_get_taxomony_data('company_strength'),
                    'icon' => 'icon-material-outline-supervisor-account'
                ),
                'company_category'  => array(
                    'title' => esc_html__('Industry', 'workscout'),
                    'content' => mas_wpjmc_get_taxomony_data('company_category'),
                    'icon' => 'icon-material-outline-assignment'
                ),
                'company_revenue'  => array(
                    'title' => esc_html__('Revenue', 'workscout'),
                    'content' => mas_wpjmc_get_taxomony_data('company_revenue'),
                    'icon' => 'icon-material-outline-monetization-on'
                ),
                'company_average_salary'  => array(
                    'title' => esc_html__('Avg. Salary', 'workscout'),
                    'content' => mas_wpjmc_get_taxomony_data('company_average_salary'),
                    'icon' => 'icon-material-outline-monetization-on'
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
            ?>
                    <div class="widget">
                        <h4><?php esc_html_e('Company Overview', 'workscout') ?></h4>
                        <div class="job-overview">
                            <ul>


                                <?php
                                foreach ($args as $key => $arg) :

                                    if (isset($arg['content']) && !empty($arg['content'])) :
                                ?>
                                        <li class=" company-feature-<?php esc_attr_e($key) ?>" id="company-feature-<?php esc_attr_e($key) ?>">
                                            <?php if (isset($arg['icon'])) : ?><i class="<?php echo $arg['icon'] ?>"></i><?php endif; ?>
                                            <div>
                                                <strong><?php echo wp_kses_post($arg['title']); ?>:</strong>
                                                <span class="location"><?php echo wp_kses_post($arg['content']); ?></span>
                                            </div>
                                        </li>

                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>
                    </div><?php
                        endif;
                    } ?>
            <?php
            $single_map = Kirki::get_option('workscout', 'pp_enable_single_jobs_map');
            $lng = $post->geolocation_long;
            $lng = get_post_meta($post->ID, '_geolocation_long', true);
            $lat = get_post_meta($post->ID, '_geolocation_lat', true);
            if ($single_map && !empty($lng)) :
            ?>

                <div class="widget">
                    <h4><?php esc_html_e('Headquarters', 'workscout') ?></h4>

                    <div id="job_map" data-longitude="<?php echo esc_attr($lng); ?>" data-latitude="<?php echo esc_attr($lat); ?>">

                    </div>
                </div>

            <?php
            endif; ?>
        </div>


        <footer class="entry-footer">
            <?php edit_post_link(esc_html__('Edit', 'workscout'), '<span class="edit-link">', '</span>'); ?>
        </footer><!-- .entry-footer -->
    </div>
    </article>

    <?php // get_sidebar('jobs');
    ?>

    </div>

<?php
endwhile;
get_footer(); ?>