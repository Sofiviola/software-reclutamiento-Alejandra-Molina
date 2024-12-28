<?php

/**
 * Workscout Elementor  Jobs Box class.
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
class Jobs extends Widget_Base
{
    // public function __construct($data = [], $args = null)
    // {
    //     parent::__construct($data, $args);

    //     wp_enqueue_script('workscout-wp-job-manager-ajax-filters');
    // }
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
        return 'workscout-jobs';
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
        return __('Jobs List', 'workscout_elementor');
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
    public function get_script_depends()
    {
        wp_enqueue_script('workscout-wp-job-manager-ajax-filters');
      
        return ['workscout-wp-job-manager-ajax-filters'];
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



        // Filters + cats


        // Limit what jobs are shown based on category and type




        $this->start_controls_section(
            'section_content',
            array(
                'label' => __('Content', 'workscout_elementor'),
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
                    'rand_featured' =>  __('Random with Featured on top', 'workscout_elementor'),
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


        $this->add_control(
            'categories',
            [
                'label' => __('Show only from categories', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
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
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('job_listing_type'),

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
        $this->add_control(
            'remote_position',
            [
                'label' => __('Remote Position', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'null',
                'multiple' => true,
                'options' => [
                    'true' =>  __('Show only remote position', 'workscout_elementor'),
                    'false' =>  __('Hide remote. ', 'workscout_elementor'),
                    'null' =>  __('Show all. ', 'workscout_elementor'),

                ],
            ]
        );


        $this->add_control(
            'location',
            array(
                'label'   => __('Default Location filter', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
            )
        );
        $this->add_control(
            'keywords',
            array(
                'label'   => __('Default Keywords filter', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
            )
        );


        $this->add_control(
            'list_layout',
            [
                'label' => __('List layout', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => \Kirki::get_option('workscout', 'jobs_listings_list_layout', 'list'),
                'multiple' => true,
                'options' => [
                    'list' =>  __('List', 'workscout_elementor'),
                    'grid' =>  __('Grid 2 columns', 'workscout_elementor'),
                    'grid-three-in-row' =>  __('Grid 3 columns ', 'workscout_elementor'),

                ],
            ]
        );


        // $this->add_control(
        //     'show_filters',
        //     [
        //         'label' => __('Show filters', 'workscout_elementor'),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'label_on' => __('Show', 'workscout_elementor'),
        //         'label_off' => __('Hide', 'workscout_elementor'),
        //         'return_value' => 'true',
        //         'default' => 'false',
        //     ]
        // );
        // $this->add_control(
        //     'show_categories',
        //     [
        //         'label' => __('Show categories', 'workscout_elementor'),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'label_on' => __('Show', 'workscout_elementor'),
        //         'label_off' => __('Hide', 'workscout_elementor'),
        //         'return_value' => 'true',
        //         'default' => 'false',
        //     ]
        // );

        $this->add_control(
            'show_more',
            [
                'label' => __('Show "More jobs" load button', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __('Hide', 'workscout_elementor'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show pagination', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __('Hide', 'workscout_elementor'),
                'return_value' => 'true',
                'default' => 'false',
                'condition' => [
                    'show_more' => 'true',
                ],
            ]
        );
        $this->add_control(
            'show_link',
            [
                'label' => __('Show "More jobs" link', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __('Hide', 'workscout_elementor'),
                'return_value' => 'true',
                'default' => 'false',
             
            ]
        );
        $this->add_control(
            'show_link_href',
            [
                'label' => esc_html__('"More jobs" link url', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                    'custom_attributes' => '',
                ],
            ]
        );
        $this->add_control(
            'show_link_label',
            array(
                'label'   => __('"More jobs" link label', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' =>  esc_html__('More Jobs', 'workscout_core'),
            )
        );

        $this->add_control(
            'show_description',
            [
                'label' => __('Show description', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __('Hide', 'workscout_elementor'),
                'return_value' => 'true',
                'default' => 'false',
                'condition' => [
                    'list_layout' => 'list',
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







        // 'show_category_multiselect' => get_option( 'job_manager_enable_default_category_multiselect', false ),


        // 'show_link'                 => false,
        // 'show_link_label'            => esc_html__('More Jobs','workscout_core'),


        // // Limit what jobs are shown based on category and type
        // 'post_status'               => '',

        // // Default values for filters
        // 'location'                  => '',
        // 'keywords'                  => '',
        // 'selected_category'         => '',
        // 'selected_job_types'        => implode( ',', array_values( get_job_listing_types( 'id=>slug' ) ) ),

        // 'list_layout'               => Kirki::get_option( 'workscout','jobs_listings_list_layout', 'list' ) //list, grid/ grid-three-in-row

        $per_page = $settings['per_page'] ? $settings['per_page'] : get_option('job_manager_per_page');
        $orderby = $settings['orderby'] ? $settings['orderby'] : 'featured';
        $order = $settings['order'] ? $settings['order'] : 'DESC';
        $location = $settings['location'] ? $settings['location'] : '';
        $keywords = $settings['keywords'] ? $settings['keywords'] : '';
        $list_layout = $settings['list_layout'] ? $settings['list_layout'] : 'list';

        // String and bool handling
        $show_filters              = false;
        $show_categories           = false;
        // $show_category_multiselect = workscout_string_to_bool($settings['show_filters']);
        $show_more                 = workscout_string_to_bool($settings['show_more']);
        $show_link                 = workscout_string_to_bool($settings['show_link']);
        $show_link_label           = $settings['show_link_label'];
        $show_link_href           = $settings['show_link_href'];
        $show_pagination           = workscout_string_to_bool($settings['show_pagination']);
        $show_description          = workscout_string_to_bool($settings['show_description']);


        $categories = $settings['categories'];
        $job_types = $settings['job_types'];

        $featured = $settings['featured'];
        $filled = $settings['filled'];
        $remote_position = $settings['remote_position'];


        $post_status = "publish";

        if ($featured != 'null') {
            $featured = (is_bool($featured) && $featured) || in_array($featured, array('1', 'true', 'yes')) ? true : false;
        } else {
            $featured = null;
        }

        if (!is_null($filled)) {
            $filled = (is_bool($filled) && $filled) || in_array($filled, array('1', 'true', 'yes')) ? true : false;
        }

        if ($remote_position != 'null') {
            $remote_position = (is_bool($remote_position) && $remote_position) || in_array($remote_position, array('1', 'true', 'yes')) ? true : false;
        } else {
            $remote_position = null;
        }


        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));

        $job_types          = is_array($job_types) ? $job_types : array_filter(array_map('trim', explode(',', $job_types)));
        $post_status        = is_array($post_status) ? $post_status : array_filter(array_map('trim', explode(',', $post_status)));
        //$selected_job_types = is_array($selected_job_types) ? $selected_job_types : array_filter(array_map('trim', explode(',', $selected_job_types)));
        //  $selected_category  = is_array($selected_category) ? $selected_category : array_filter(array_map('trim', explode(',', $selected_category)));

        $disable_client_state = false;
        // Get keywords and location from querystring if set
        if (!empty($_GET['search_keywords'])) {
            $keywords = sanitize_text_field($_GET['search_keywords']);
            $disable_client_state = true;
        }
        if (!empty($_GET['search_location'])) {
            $location = sanitize_text_field($_GET['search_location']);
            $disable_client_state = true;
        }
        if (!empty($_GET['search_category'])) {
            $selected_category = sanitize_text_field($_GET['search_category']);
            $disable_client_state = true;
        }
        if (!empty($_GET['search_job_type'])) {
            $selected_job_types = sanitize_text_field(wp_unslash($_GET['search_job_type']));
            $disable_client_state       = true;
        }
        if (!empty($selected_category) && is_array($selected_category)) {
            foreach ($selected_category as $cat_index => $category) {
                if (!is_numeric($category)) {
                    $term = get_term_by('slug', $category, 'job_listing_category');

                    if ($term) {
                        $selected_category[$cat_index] = $term->term_id;
                    }
                }
            }
        }
        ob_start();
        wp_enqueue_script('wp-job-manager-ajax-filters');
        if ($show_filters) {

            get_job_manager_template('job-filters.php', array('per_page' => $per_page, 'orderby' => $orderby, 'order' => $order, 'show_categories' => $show_categories, 'categories' => $categories, 'selected_category' => $selected_category, 'job_types' => $job_types, 'atts' => '', 'location' => $location, 'keywords' => $keywords, 'selected_job_types' => $selected_job_types, 'show_category_multiselect' => $show_category_multiselect));

            get_job_manager_template('job-listings-start.php');
            get_job_manager_template('job-listings-end.php');

            if (!$show_pagination && $show_more==true) {
                echo '<a class="load_more_jobs" href="#" style="display:none;"><strong>' . esc_html__('Load more listings', 'workscout_core') . '</strong></a>';
            }
        } else {

            $jobs = get_job_listings(apply_filters('job_manager_output_jobs_args', array(
                'search_location'   => $location,
                'search_keywords'   => $keywords,
                'search_categories' => $categories,
                'job_types'         => $job_types,
                'post_status'       => $post_status,
                'orderby'           => $orderby,
                'order'             => $order,
                'posts_per_page'    => $per_page,
                'featured'          => $featured,
                'remote_position'          => $remote_position,
                'filled'            => $filled
            )));

            $layout = \Kirki::get_option('workscout', 'pp_jobs_old_layout', false);
            $logo_position = \Kirki::get_option('workscout', 'pp_job_list_logo_position', 'left');

            if ($jobs->have_posts()) : ?>

                <!-- Listings Loader -->
                <div class="listings-loader">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                </div>

                <ul class="job_listings job-list full <?php if ($list_layout == 'grid-three-in-row') {
                                                            echo "grid-layout grid-three-in-row";
                                                        } ?>  
                <?php if ($list_layout == 'grid') {
                    echo "grid-layout";
                } ?>  <?php if ($logo_position == 'right') {
                            echo "logo-to-right ";
                        }
                        if (!$layout) {
                            echo "new-layout ";
                        }
                        if (!$show_description) {
                            echo "hide-desc";
                        } ?>">

                    <?php while ($jobs->have_posts()) : $jobs->the_post();

                        get_job_manager_template('content-job_listing.php', array('list_layout' => $list_layout));


                    endwhile; ?>

                    <?php get_job_manager_template('job-listings-end.php'); ?>

                    <?php if ($jobs->found_posts > $per_page && $show_more) : ?>



                        <?php if ($show_pagination) : ?>
                            <?php echo get_job_listing_pagination($jobs->max_num_pages); ?>
                        <?php else : ?>
                            <a class="load_more_jobs button centered" href="#"><i class="fa fa-plus-circle"></i><?php esc_html_e('Show More Jobs', 'workscout_core'); ?></a>
                            <div class="margin-bottom-55"></div>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if ($show_link) : ?>
                        <a class="link_more_jobs button centered" href="<?php echo esc_url($show_link_href['url']); ?>"><i class="fa fa-plus-circle"></i><?php echo $show_link_label; ?></a>
                        <div class="margin-bottom-55"></div>
                    <?php endif; ?>

                <?php else :
                ?>
                    <ul class="job_listings job-list full <?php if (!$layout) {
                                                                echo "new-layout ";
                                                            }
                                                            if (!$show_description) {
                                                                echo "hide-desc";
                                                            } ?>">
                        <?php do_action('job_manager_output_jobs_no_results');   ?>
                    </ul>
    <?php
            endif;

            wp_reset_postdata();
        }

        $data_attributes_string = '';
        $data_attributes        = array(
            'location'        => $location,
            'keywords'        => $keywords,
            'show_filters'    => $show_filters ? 'true' : 'false',
            'show_pagination' => $show_pagination ? 'true' : 'false',
            //'job_types'       => $atts_job_types,
            'featured'          => $featured,
            'filled'            => $filled,
            //'selected_job_types' => $atts_selected_job_types,
            'per_page'        => $per_page,
            'orderby'         => $orderby,
            'remote_position' => $remote_position,
            'order'           => $order,
            'list_layout'     => $list_layout,
            'categories'      => implode(',', $categories),
            'disable-form-state-storage' => $disable_client_state,
        );
        if (!is_null($featured)) {
            $data_attributes['featured'] = $featured ? 'true' : 'false';
        }
        if (!is_null($filled)) {
            $data_attributes['filled']   = $filled ? 'true' : 'false';
        }
        if (!empty($post_status)) {
            $data_attributes['post_status'] = implode(',', $post_status);
        }
        $data_attributes['post_id'] = isset($GLOBALS['post']) ? $GLOBALS['post']->ID : 0;


        $data_attributes = apply_filters('job_manager_jobs_shortcode_data_attributes', $data_attributes, false);

        foreach ($data_attributes as $key => $value) {
            $data_attributes_string .= 'data-' . esc_attr($key) . '="' . esc_attr($value) . '" ';
        }



        $job_listings_output = apply_filters('job_manager_job_listings_output', ob_get_clean());

        echo '<div class="job_listings"  ' . $data_attributes_string . '>' . $job_listings_output . '</div>';
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
                'numberposts' => 99,
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
