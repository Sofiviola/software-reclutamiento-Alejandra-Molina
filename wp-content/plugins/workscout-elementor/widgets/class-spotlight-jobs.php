<?php

/**
 * Workscout Elementor Spotlight Jobs Box class.
 *
 * @category   Class
 * @package    ElementorAwesomesauce
 * @subpackage WordPress
 * @author     Ben Marshall <me@benmarshall.me>
 * @copyright  2020 Ben Marshall
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://www.benmarshall.me/build-custom-elementor-widgets/,
 *             Build Custom Elementor Widgets)
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorWorkscout\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class SpotlightJobs extends Widget_Base
{

    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'workscout-spotlight-jobs';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Spotlight Jobs', 'workscout_elementor');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-bullhorn';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return array('workscout');
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */


    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __('Content', 'workscout_elementor'),
            )
        );

        $this->add_control(
            'title',
            array(
                'label'   => __('Title', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Featured Jobs', 'workscout_elementor'),
            )
        );

        $this->add_control(
            'per_page',
            [
                'label' => __('Listings to display', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 21,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order by', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' =>  __(' Order by date.', 'workscout_elementor'),
                    'rand' =>  __(' Random order.', 'workscout_elementor'),
                    'featured' =>  __('Featured', 'workscout_elementor'),
                    'ID' =>  __('Order by post id. ', 'workscout_elementor'),
                    'author' =>  __('Order by author.', 'workscout_elementor'),
                    'title' =>  __('Order by title.', 'workscout_elementor'),
                ],
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => __('Order', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' =>  __('Descending', 'workscout_elementor'),
                    'ASC' =>  __('Ascending. ', 'workscout_elementor'),


                ],
            ]
        );
        // $this->add_control(
        //     'visible',
        //     array(
        //         'label'   => __('Visible items', 'workscout_elementor'),
        //         'type'    => \Elementor\Controls_Manager::TEXT,
        //         'default' => '1,1,1,1',
        //     )
        // );

        $this->add_control(
            'meta',
            [
                'label' => __('Meta fields', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'default' => 'location',
                'multiple' => true,
                'options' => [
                    'company' =>  __('Company name', 'workscout_elementor'),
                    'location' =>  __('Location. ', 'workscout_elementor'),
                    'rate' =>  __('Rate. ', 'workscout_elementor'),
                    'salary' =>  __('Salary. ', 'workscout_elementor'),
                    'wpjm_salary' =>  __('WPJM Salary. ', 'workscout_elementor'),


                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Auto Play', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'your-plugin'),
                'label_off' => __('Hide', 'your-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',

            ]
        );


        $this->add_control('delay', array(
                'label'   => __('Auto Play Speed', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => __('Subtitle', 'workscout_elementor'),
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'default' => 5000,
            )
        );

        $this->add_control(
            'slides_to_show',
            array(
                'label'   => __('Slides to show at once', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 1,
            )
        );

        $this->add_control(
            'limitby',
            [
                'label' => __('Limit text by', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'words',
                'multiple' => true,
                'options' => [
                    'words' =>  __('Words', 'workscout_elementor'),
                    'letters' =>  __('Letters. ', 'workscout_elementor'),

                ],
            ]
        );
        $this->add_control(
            'limit',
            [
                'label' => __('Limit text lenght', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 250,
                'step' => 1,
                'default' => 20,
            ]
        );

        $this->add_control(
            'categories',
            [
                'label' => __('Show only from categories', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('job_listing_category'),

            ]
        );
        $this->add_control(
            'job_types',
            [
                'label' => __('Show only from job types', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('job_listing_type'),

            ]
        );
        $this->add_control(
            'job_ids',
            [
                'label' => __('Show only selected jobs', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_posts(),

            ]
        );

        $this->add_control(
            'featured',
            [
                'label' => __('Featured jobs', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'null',
                'multiple' => true,
                'options' => [
                    'true' =>  __('Show only featured', 'workscout_elementor'),
                    'false' =>  __('Hide featured. ', 'workscout_elementor'),
                    'null' =>  __('Show all. ', 'workscout_elementor'),

                ],
            ]
        );

        $this->add_control(
            'filled',
            [
                'label' => __('Filled', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'null',
                'multiple' => true,
                'options' => [
                    'true' =>  __('Show only filled', 'workscout_elementor'),
                    'false' =>  __('Hide filled. ', 'workscout_elementor'),
                    'null' =>  __('Show all. ', 'workscout_elementor'),

                ],
            ]
        );




        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();



        // 'per_page'                  => get_option( 'job_manager_per_page' ),
        // 'orderby'                   => 'featured',
        // 'order'                     => 'DESC',
        // 'title'                     => 'Job Spotlight',
        // 'visible'                   => '1,1,1,1',
        // 'meta'                      => 'company,location,rate,salary',
        // 'autoplay'                  => "off",
        // 'delay'                     => 5000,
        // 'limit'                     => 20,
        // 'limitby'                   => 'words', //characters
        // // Limit what jobs are shown based on category and type
        // 'categories'                => '',
        // 'job_types'                 => '',
        // 'job_ids'                   => '',
        // 'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
        // 'filled'                    => null, // True to show only filled, false to hide filled, leave null to show both/use the settings.

        $per_page = $settings['per_page'] ? $settings['per_page'] : get_option('job_manager_per_page');
        $orderby = $settings['orderby'] ? $settings['orderby'] : 'featured';
        $order = $settings['order'] ? $settings['order'] : 'DESC';
        // $visible = $settings['visible'] ? $settings['visible'] : '1,1,1,1';
        $title = $settings['title'];
        $meta = $settings['meta'];
        $autoplay = $settings['autoplay'];
        $delay = $settings['delay'];
        $limit = $settings['limit'];
        $limitby = $settings['limitby'];
        $categories = $settings['categories'];
        $job_types = $settings['job_types'];
        $job_ids = $settings['job_ids'];
        $featured = $settings['featured'];
        $slides_to_show = $settings['slides_to_show'];
        $filled = $settings['filled'];


        if ($filled != "null") {
            $filled = (is_bool($filled) && $filled) || in_array($filled, array('1', 'true', 'yes')) ? true : false;
        } else {
            $filled = null;
        }

        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));
        $job_types          = is_array($job_types) ? $job_types : array_filter(array_map('trim', explode(',', $job_types)));

        if ($featured != "null") {

            $featured = (is_bool($featured) && $featured) || in_array($featured, array('1', 'true', 'yes')) ? true : false;
        } else {
            $featured = null;
        }

        $query_args = array(
            'post_type'              => 'job_listing',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => 1,
            'offset'                 => 0,
            'posts_per_page'         => intval($per_page),
            'orderby'                => $orderby,
            'order'                  => $order,
            'fields'                 => 'all'
        );

        if (!empty($job_ids)) {

            $query_args['post__in'] = $job_ids;
        }

        if (!is_null($featured)) {
            $query_args['meta_query'][] = array(
                'key'     => '_featured',
                'value'   => '1',
                'compare' => $featured ? '=' : '!='
            );
        }

        if (!is_null($filled) || 1 === absint(get_option('job_manager_hide_filled_positions'))) {
            $query_args['meta_query'][] = array(
                'key'     => '_filled',
                'value'   => '1',
                'compare' => $filled ? '=' : '!='
            );
        }

        if (!empty($job_types)) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'job_listing_type',
                'field'    => 'slug',
                'terms'    => $job_types
            );
        }

        if (!empty($categories)) {
            $field    = is_numeric($categories[0]) ? 'term_id' : 'slug';

            $operator = 'all' === get_option('job_manager_category_filter_type', 'all') && sizeof($categories) > 1 ? 'AND' : 'IN';
            $query_args['tax_query'][] = array(
                'taxonomy'         => 'job_listing_category',
                'field'            => $field,
                'terms'            => array_values($categories),
                'include_children' => $operator !== 'AND',
                'operator'         => $operator
            );
        }

        if ('featured' === $orderby) {
            $orderby = array(
                'menu_order' => 'ASC',
                'date'       => 'DESC'
            );
        }
?><h3 class="margin-bottom-5"><?php echo esc_html($title); ?></h3><?php
                                                                    $wp_query = new \WP_Query($query_args);
                                                                    if ($wp_query->have_posts()) :   ?>

            <!-- Showbiz Container -->
            <?php $slick_autplay = ($autoplay == 'on') ? 'true' : 'false'; ?>
            <div id="job-spotlight" data-slick='{"slidesToShow":<?php echo $slides_to_show; ?>,"slidesToScroll":<?php echo $slides_to_show; ?>,  "autoplay":<?php echo $slick_autplay; ?>, "autoplaySpeed":<?php echo $delay; ?>}' class="job-spotlight-car showbiz-container">
                <?php while ($wp_query->have_posts()) : $wp_query->the_post();
                                                                            $id = get_the_id(); ?>

                    <div class="job-spotlight">
                        <a href="<?php the_permalink(); ?>">
                            <h4><?php the_title(); ?>
                                <?php
                                                                            $types = get_the_terms($id, 'job_listing_type');
                                                                            if ($types && !is_wp_error($types)) :
                                                                                foreach ($types as $type) { ?>
                                        <span class="job-type <?php echo sanitize_title($type->slug); ?>"><?php echo $type->name; ?></span>
                                <?php }
                                                                            endif; ?>
                            </h4>
                        </a>

                        <?php
                                                                            $job_meta         = is_array($meta) ? $meta : array_filter(array_map('trim', explode(',', $meta)));
                        ?>

                        <?php if (in_array("company", $job_meta) && get_the_company_name()) { ?>
                            <span class="ws-meta-company-name"><?php echo workscout_get_icon('company'); ?><?php the_company_name(); ?></span>
                        <?php } ?>

                        <?php if (in_array("location", $job_meta)) { ?>
                            <span class="ws-meta-job-location"><?php echo workscout_get_icon('location'); ?> <?php ws_job_location(false); ?></span>
                        <?php } ?>

                        <?php
                                                                            $currency_position =  get_option('workscout_currency_position', 'before');

                                                                            $rate_min = get_post_meta($id, '_rate_min', true);
                                                                            if ($rate_min && in_array("rate", $job_meta)) {
                                                                                $rate_max = get_post_meta($id, '_rate_max', true);  ?>
                            <span class="ws-meta-rate">
                                <?php echo workscout_get_icon('money'); ?>
                                <?php
                                                                                if ($currency_position == 'before') {
                                                                                    echo get_workscout_currency_symbol();
                                                                                }
                                                                                echo esc_html($rate_min);
                                                                                if ($currency_position == 'after') {
                                                                                    echo get_workscout_currency_symbol();
                                                                                }
                                                                                if (!empty($rate_max)) {
                                                                                    echo '- ';
                                                                                    if ($currency_position == 'before') {
                                                                                        echo get_workscout_currency_symbol();
                                                                                    }
                                                                                    echo esc_html($rate_max);
                                                                                    if ($currency_position == 'after') {
                                                                                        echo get_workscout_currency_symbol();
                                                                                    }
                                                                                } ?> <?php esc_html_e('/ hour', 'workscout_core'); ?>
                            </span>
                        <?php } ?>

                        <?php
                                                                            $salary_min = get_post_meta($id, '_salary_min', true);
                                                                            $salary_max = get_post_meta($id, '_salary_max', true);
                                                                            if (in_array("salary", $job_meta)) {
                                                                                if (!empty($salary_min) || !empty($salary_max)) { ?>

                                <span class="ws-meta-salary">
                                    <?php echo workscout_get_icon('money'); ?>
                                    <?php
                                                                                    if (!empty($salary_min)) {
                                                                                        if ($currency_position == 'before') {
                                                                                            echo get_workscout_currency_symbol();
                                                                                        }
                                                                                        echo esc_html($salary_min);
                                                                                        if ($currency_position == 'after') {
                                                                                            echo get_workscout_currency_symbol();
                                                                                        }
                                                                                    } ?> <?php
                                                                                            if (!empty($salary_max)) {
                                                                                                if (!empty($salary_min)) {
                                                                                                    echo ' - ';
                                                                                                }
                                                                                                if ($currency_position == 'before') {
                                                                                                    echo get_workscout_currency_symbol();
                                                                                                }
                                                                                                echo $salary_max;
                                                                                                if ($currency_position == 'after') {
                                                                                                    echo get_workscout_currency_symbol();
                                                                                                }
                                                                                            } ?>
                                </span>

                        <?php }
                                                                            } ?>
                        <?php if (in_array("wpjm_salary", $job_meta)) :
                                                                                if (get_the_job_salary()) { ?>
                                <span>
                                    <?php echo workscout_get_icon('money'); ?>
                                    <?php echo the_job_salary(); ?>
                                </span>
                        <?php }
                                                                            endif;
                        ?>
                        <?php if (in_array("date", $job_meta)) { ?>
                            <span class="ws-meta-job-date"><i class="icon-material-outline-history"></i> <?php the_job_publish_date() ?></span>
                        <?php } ?>

                        <?php if (in_array("deadline", $job_meta)) { ?>
                            <?php
                                                                                if ($deadline = get_post_meta($id, '_application_deadline', true)) {
                                                                                    $expiring_days = apply_filters('job_manager_application_deadline_expiring_days', 2);
                                                                                    $expiring = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= $expiring_days);
                                                                                    $expired  = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= 0); ?>
                                <span class="ws-meta-job-deadline"><i class="icon-material-outline-history"></i>
                                    <?php echo ($expired ? __('Closed', 'workscout_core') : __('Closes', 'workscout_core')) . ': ' . date_i18n(get_option('date_format'), strtotime($deadline)) ?>
                                </span>
                        <?php }
                                                                            } ?>

                        <?php if (in_array("expires", $job_meta)) { ?>
                            <span class="ws-meta-job-expires"><i class="icon-material-outline-history"></i>
                                <?php esc_html_e('Expires', 'workscout_core') ?>: <?php echo date_i18n(get_option('date_format'), strtotime(get_post_meta($id, '_job_expires', true))) ?>
                            </span>
                        <?php } ?>

                        <p><?php
                                                                            $excerpt = get_the_excerpt();
                                                                            if ($limitby == 'words') {
                                                                                echo workscout_string_limit_words($excerpt, $limit);
                                                                            } else {
                                                                                echo workscout_get_excerpt($excerpt, $limit);
                                                                            }
                            ?>

                        </p>
                        <a href="<?php the_permalink(); ?>" class="button"><?php esc_html_e('Apply For This Job', 'workscout_core') ?></a>
                    </div>

                <?php endwhile; ?>
            </div>
<?php

                                                                    endif;
                                                                    wp_reset_postdata();
                                                                }



                                                                protected function get_terms($taxonomy)
                                                                {
                                                                    $taxonomies = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

                                                                    $options = ['' => ''];

                                                                    if (!empty($taxonomies)) :
                                                                        foreach ($taxonomies as $taxonomy) {
                                                                             if(isset($taxonomy->slug) && isset($taxonomy->name)){
                    $options[$taxonomy->slug] = $taxonomy->name;
                }
                                                                        }
                                                                    endif;

                                                                    return $options;
                                                                }
                                                                protected function get_posts()
                                                                {
                                                                    $posts = get_posts(
                                                                        array(
                                                                            'numberposts' => 199,
                                                                            'post_type' => 'job_listing',
                                                                            'suppress_filters' => true
                                                                        )
                                                                    );

                                                                    $options = ['' => ''];

                                                                    if (!empty($posts)) :
                                                                        foreach ($posts as $post) {
                                                                            $options[$post->ID] = get_the_title($post->ID);
                                                                        }
                                                                    endif;

                                                                    return $options;
                                                                }
                                                            }
