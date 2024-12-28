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
class Resumes extends Widget_Base
{

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        wp_enqueue_script('workscout-wp-resume-manager-ajax-filters');
    }
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
        return 'workscout-resumes';
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
        return __('Resumes List', 'workscout_elementor');
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
                'options' => $this->get_terms('resume_category'),

            ]
        );



        $this->add_control(
            'featured',
            [
                'label' => __('Featured resumes', 'workscout_elementor'),
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
                'label' => __('Show "More resumes" load button', 'workscout_elementor'),
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

            ]
        );
        $this->add_control(
            'show_link',
            [
                'label' => __('Show "More resumes" link', 'workscout_elementor'),
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
                'label' => esc_html__('"More resumes" link url', 'workscout_elementor'),
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
                'label'   => __('Default Link label', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' =>  esc_html__('More Resumes', 'workscout_core'),
            )
        );

        $this->add_control(
            'layout',
            [
                'label' => __('List layout', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => \Kirki::get_option('workscout', 'jobs_listings_list_layout', 'list'),
                'multiple' => false,
                'options' => [
                    'list' =>  __('List', 'workscout_elementor'),
                    'grid' =>  __('Grid ', 'workscout_elementor'),
                ],
            ]
        );

        // $this->add_control(
        //     'show_description',
        //     [
        //         'label' => __('Show description', 'workscout_elementor'),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'label_on' => __('Show', 'workscout_elementor'),
        //         'label_off' => __('Hide', 'workscout_elementor'),
        //         'return_value' => 'true',
        //         'default' => 'false',
        //         'condition' => [
        //             'list_layout' => 'list',
        //         ],
        //     ]
        // );

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
        $location = $settings['location'] ? $settings['location'] : '';
        $keywords = $settings['keywords'] ? $settings['keywords'] : '';
        $layout = $settings['layout'] ? $settings['layout'] : 'list';

        // String and bool handling
        $show_filters              = false;
        $show_categories           = false;
        // $show_category_multiselect = workscout_string_to_bool($settings['show_filters']);
        $show_more                 = workscout_string_to_bool($settings['show_more']);
        $show_link                 = workscout_string_to_bool($settings['show_link']);
        $show_link_label           = $settings['show_link_label'];
        $show_link_href           = $settings['show_link_href'];
        $show_pagination           = workscout_string_to_bool($settings['show_pagination']);
       // $show_description          = workscout_string_to_bool($settings['show_description']);


        $categories = $settings['categories'];


        $featured = $settings['featured'];

        $post_status = "publish";

        if (!is_null($featured)) {
            $featured = (is_bool($featured) && $featured) || in_array($featured, array('1', 'true', 'yes')) ? true : false;
        }


        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));


        $post_status        = is_array($post_status) ? $post_status : array_filter(array_map('trim', explode(',', $post_status)));
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

        if (!empty($selected_category) && is_array($selected_category)) {
            foreach ($selected_category as $cat_index => $category) {
                if (!is_numeric($category)) {
                    $term = get_term_by('slug', $category, 'resume_category');

                    if ($term) {
                        $selected_category[$cat_index] = $term->term_id;
                    }
                }
            }
        }
        ob_start();

        if ($show_filters) {

            get_job_manager_template('resume-filters.php', array('per_page' => $per_page, 'orderby' => $orderby, 'order' => $order, 'show_categories' => $show_categories, 'categories' => $categories, 'selected_category' => $selected_category, 'atts' => $atts, 'location' => $location, 'keywords' => $keywords, 'show_category_multiselect' => $show_category_multiselect), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/');

            get_job_manager_template('resumes-start.php', array('style'=>$layout), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/');
            get_job_manager_template('resumes-end.php', array('style' => $layout), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/');

            if (!$show_pagination && $show_more) {
                echo '<a class="load_more_resumes" href="#" style="display:none;"><strong>' . __('Load more resumes', 'wp-job-manager-resumes') . '</strong></a>';
            }
        } else {

            $resumes = get_resumes(apply_filters('resume_manager_output_resumes_args', array(
                'search_categories' => $categories,
                'orderby'           => $orderby,
                'order'             => $order,
                'categories'        => $categories,
                'search_keywords'   => $keywords,
                'search_location'   => $location,
                'posts_per_page'    => $per_page,
                'featured'          => $featured
            )));
            if ($resumes->have_posts()) : ?>

                <?php get_job_manager_template('resumes-start.php', array('style' => $layout), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/'); ?>

                <?php while ($resumes->have_posts()) : $resumes->the_post(); ?>
                    <?php get_job_manager_template_part('content', 'resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/'); ?>
                <?php endwhile; ?>

                <?php get_job_manager_template('resumes-end.php', array('style' => $layout), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/'); ?>

                <?php if ($resumes->found_posts > $per_page && $show_more) : ?>

                    <?php wp_enqueue_script('wp-resume-manager-ajax-filters'); ?>

                    <?php if ($show_pagination) : ?>
                        <?php echo get_job_listing_pagination($resumes->max_num_pages); ?>
                    <?php else : ?>
                        <a class="load_more_resumes" href="#"><strong><?php echo $show_link_label; ?></strong></a>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if ($show_link) : ?>
                    <a class="link_more_resumes button centered" href="<?php echo esc_url($show_link_href['url']); ?>"><?php echo $show_link_label; ?></a>
                    <div class="margin-bottom-55"></div>
                <?php endif; ?>

<?php else :

                do_action('resume_manager_output_resumes_no_results');

            endif;

            wp_reset_postdata();
        }

        $data_attributes_string = '';
        $data_attributes        = array(
            'location'        => $location,
            'keywords'        => $keywords,
            'show_filters'    => $show_filters ? 'true' : 'false',
            'show_pagination' => $show_pagination ? 'true' : 'false',

            'featured'          => $featured,

            'per_page'        => $per_page,
            'orderby'         => $orderby,
            'order'           => $order,
            'layout'     => $layout,
            'categories'      => implode(',', $categories),
            'disable-form-state-storage' => $disable_client_state,
        );
        if (!is_null($featured)) {
            $data_attributes['featured'] = $featured ? 'true' : 'false';
        }

        if (!empty($post_status)) {
            $data_attributes['post_status'] = implode(',', $post_status);
        }
        $data_attributes['post_id'] = isset($GLOBALS['post']) ? $GLOBALS['post']->ID : 0;


        // $data_attributes = apply_filters('job_manager_jobs_shortcode_data_attributes', $data_attributes, $atts);

        foreach ($data_attributes as $key => $value) {
            $data_attributes_string .= 'data-' . esc_attr($key) . '="' . esc_attr($value) . '" ';
        }



        $job_listings_output = apply_filters('job_manager_job_listings_output', ob_get_clean());

        echo '<div class="resumes" ' . $data_attributes_string . '>' . $job_listings_output . '</div>';
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
                'numberposts' => -1,
                'post_type' => 'resume',
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
