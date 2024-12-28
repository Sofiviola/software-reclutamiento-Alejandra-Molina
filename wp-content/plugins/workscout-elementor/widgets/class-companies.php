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
class Companies extends Widget_Base
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
        return 'workscout-companies';
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
        return __('Companies', 'workscout_elementor');
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



        // Filters + cats


        // Limit what jobs are shown based on category and type
        //    'per_page'          => get_option( 'job_manager_companies_per_page', 10 ),
        //                 'orderby'           => 'date',
        //                 'order'             => 'DESC',
        //                 'category'          => '',
        //                 'average_salary'    => '',
        //                 'post_status'       => '',
        //                 'show_pagination'   => false,



        $this->start_controls_section(
            'section_content',
            array(
                'label' => __('Content', 'workscout_elementor'),
            )
        );

        $this->add_control(
            'per_page',
            [
                'label' => __('Companies to display', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 99,
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


        $this->add_control(
            'categories',
            [
                'label' => __('Show only from categories', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('company_category'),

            ]
        );
        $this->add_control(
            'average_salary',
            [
                'label' => __('Show only with average salary', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('company_average_salary'),

            ]
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
        $this->add_control(
            'show_featured',
            [
                'label' => __('Show only featured', 'workscout_elementor'),
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


        $per_page = $settings['per_page'] ? $settings['per_page'] : get_option('job_manager_per_page');
        $orderby = $settings['orderby'] ? $settings['orderby'] : 'featured';
        $order = $settings['order'] ? $settings['order'] : 'DESC';
        $featured = $settings['show_featured'] ? $settings['show_featured'] : null;
        $show_pagination           = workscout_string_to_bool($settings['show_pagination']);
        $list_layout = $settings['list_layout'] ? $settings['list_layout'] : 'list';

        $atts = array();

        $categories = $settings['categories'];
        $average_salary = $settings['average_salary'];


        $post_status = "publish";

        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));

        $average_salary          = is_array($average_salary) ? $average_salary : array_filter(array_map('trim', explode(',', $average_salary)));

        //$selected_job_types = is_array($selected_job_types) ? $selected_job_types : array_filter(array_map('trim', explode(',', $selected_job_types)));
        //  $selected_category  = is_array($selected_category) ? $selected_category : array_filter(array_map('trim', explode(',', $selected_category)));

        $disable_client_state = false;
        // Get keywords and location from querystring if set
        
        ob_start();
        $show_pagination = wp_validate_boolean($show_pagination);

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $offset = intval($per_page * ($paged - 1));

        $companies = mas_wpjmc_get_companies(apply_filters('mas_job_manager_company_output_companies_args', array(
            
            'category'          => $categories,
            'average_salary'    => $average_salary,
            'orderby'           => $orderby,
            'order'             => $order,
            'posts_per_page'    => $per_page,
            'page'              => $paged,
            'featured'          => $featured,
            'offset'            => $offset,
        )));

        ob_start();

        if ($companies->have_posts()) : ?>

            <?php do_action('mas_wpjmc_before_shortcode_company_start', $companies, $atts); ?>

            <ul class="wpjmc-companies <?php echo $list_layout; ?>" >

                <?php while ($companies->have_posts()) : $companies->the_post(); ?>
                    <?php get_job_manager_template_part('content', 'company', 'mas-wp-job-manager-company', mas_wpjmc()->plugin_dir . 'templates/'); ?>
                <?php endwhile; ?>

            </ul>

            <?php if ($show_pagination) : ?>
                <?php get_job_manager_template('pagination.php', array('max_num_pages' => $companies->max_num_pages)); ?>
            <?php endif; ?>

<?php else :
            do_action('job_manager_output_jobs_no_results');
        endif;

        wp_reset_postdata();

        $output = apply_filters('mas_job_manager_companies_output', ob_get_clean());

        echo  $output;
    }



    protected function get_terms($taxonomy)
    {
        $taxonomies = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

        $options = ['' => ''];

        if (!empty($taxonomies)) :
            foreach ($taxonomies as $taxonomy) {
                $options[$taxonomy->slug] = $taxonomy->name;
            }
        endif;

        return $options;
    }

    protected function get_posts()
    {
        $posts = get_posts(
            array(
                'numberposts' => -99,
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
