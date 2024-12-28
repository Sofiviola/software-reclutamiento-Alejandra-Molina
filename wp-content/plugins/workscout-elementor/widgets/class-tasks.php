<?php

/**
 * Workscout Elementor Task Box class.
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
class Tasks extends Widget_Base
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
        return 'workscout-tasks';
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
        return __('Tasks List', 'workscout_elementor');
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
        wp_enqueue_script('workscout-freelancer-ajaxsearch');

        return ['workscout-freelancer-ajaxsearch'];
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
                'label' => __('Tasks to display', 'workscout_elementor'),
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
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('task_category'),

            ]
        );
        $this->add_control(
            'task_skills',
            [
                'label' => __('Show only by selected skills', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' => [],
                'options' => $this->get_terms('task_skill'),

            ]
        );
    


        $this->add_control(
            'featured',
            [
                'label' => __('Featured tasks', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'null',
                'multiple' => true,
                'options' => [
                    'null' =>  __('Show all. ', 'workscout_elementor'),
                    'true' =>  __('Show only featured', 'workscout_elementor'),
                    // 'false' =>  __('Hide featured. ', 'workscout_elementor'),
                    

                ],
            ]
        );

        // $this->add_control(
        //     'remote_position',
        //     [
        //         'label' => __('Remote Tasks', 'workscout_elementor'),
        //         'type' => \Elementor\Controls_Manager::SELECT,
        //         'default' => 'null',
        //         'multiple' => true,
        //         'options' => [
        //             'true' =>  __('Show only remote tasks', 'workscout_elementor'),
        //             'false' =>  __('Hide remote. ', 'workscout_elementor'),
        //             'null' =>  __('Show all. ', 'workscout_elementor'),

        //         ],
        //     ]
        // );


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
                    'compact' =>  __('List', 'workscout_elementor'),
                    'grid' =>  __('Grid ', 'workscout_elementor'),
                ],
            ]
        );


        // $this->add_control(
        //     'show_more',
        //     [
        //         'label' => __('Show "More task" load button', 'workscout_elementor'),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'label_on' => __('Show', 'workscout_elementor'),
        //         'label_off' => __('Hide', 'workscout_elementor'),
        //         'return_value' => 'true',
        //         'default' => 'false',
        //     ]
        // );
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
                'label' => __('Show "More tasks" link', 'workscout_elementor'),
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
                'label' => esc_html__('"More tasks" link url', 'workscout_elementor'),
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
                'label'   => __('"More tasks" link label', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' =>  esc_html__('More Tasks', 'workscout_core'),
            )
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
        $location = $settings['location'] ? $settings['location'] : '';
        $keywords = $settings['keywords'] ? $settings['keywords'] : '';
        $list_layout = $settings['list_layout'] ? $settings['list_layout'] : 'compact';

        // String and bool handling
        $show_filters              = false;
        $show_categories           = false;
        // $show_category_multiselect = workscout_string_to_bool($settings['show_filters']);
        // $show_more                 = workscout_string_to_bool($settings['show_more']);
        $show_link                 = workscout_string_to_bool($settings['show_link']);
        $show_link_label           = $settings['show_link_label'];
        $show_link_href           = $settings['show_link_href'];
        $show_pagination           = workscout_string_to_bool($settings['show_pagination']);
        

        $template_loader = new \WorkScout_Freelancer_Template_Loader;
        $categories = $settings['categories'];
        $task_skills = $settings['task_skills'];

        $featured = $settings['featured'];

        // $remote_position = $settings['remote_position'];


        $post_status = "publish";

        if ($featured != 'null') {
            $featured = (is_bool($featured) && $featured) || in_array($featured, array('1', 'true', 'yes')) ? true : false;
        } else {
            $featured = null;
        }


        // if ($remote_position != 'null') {
        //     $remote_position = (is_bool($remote_position) && $remote_position) || in_array($remote_position, array('1', 'true', 'yes')) ? true : false;
        // } else {
        //     $remote_position = null;
        // }


        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));
        
        $task_skills          = is_array($task_skills) ? $task_skills : array_filter(array_map('trim', explode(',', $task_skills)));
        $post_status        = is_array($post_status) ? $post_status : array_filter(array_map('trim', explode(',', $post_status)));
       
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
        if (!empty($_GET['tax-task_category'])) {
            $selected_category = sanitize_text_field($_GET['tax-task_category']);
            $disable_client_state = true;
        }
        if (!empty($_GET['search_task_skills'])) {
            $selected_task_skills = sanitize_text_field(wp_unslash($_GET['search_task_skills']));
            $disable_client_state       = true;
        }
        if (!empty($selected_category) && is_array($selected_category)) {
            foreach ($selected_category as $cat_index => $category) {
                if (!is_numeric($category)) {
                    $term = get_term_by('slug', $category, 'task_category');

                    if ($term) {
                        $selected_category[$cat_index] = $term->term_id;
                    }
                }
            }
        }
        ob_start();
        wp_enqueue_script("workscout-freelancer-ajaxsearch");




        $data_attributes_string = '';
        $data_attributes        = array(
            'location'        => $location,
            'keywords'        => $keywords,
            'show_filters'    => $show_filters ? 'true' : 'false',
            'show_pagination' => $show_pagination ? 'true' : 'false',
            //'job_types'       => $atts_job_types,
            'featured'          => $featured,

            //'selected_job_types' => $atts_selected_job_types,
            'per_page'        => $per_page,
            'orderby'         => $orderby,
            // 'remote_position' => $remote_position,
            'order'           => $order,
            'list_layout'     => $list_layout,
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


        $data_attributes = apply_filters('job_manager_jobs_shortcode_data_attributes', $data_attributes, false);

        foreach ($data_attributes as $key => $value) {
            $data_attributes_string .= 'data-' . esc_attr($key) . '="' . esc_attr($value) . '" ';
        }
        
        $ordering_args = \WorkScout_Freelancer_Task::get_task_ordering_args($orderby, $order);
        $get_tasks = array(
            'posts_per_page'    => $per_page,
            'orderby'           => $ordering_args['orderby'],
            'order'             => $ordering_args['order'],
            'keyword'       => $keywords,
            'location'   => $location,
            'tax-task_category'   => $categories,
            'tax-task_skill'   => $task_skills,
            // 'search_radius'       => $search_radius,
            // 'radius_type'       => $radius_type,
            // 'listeo_orderby'       => $orderby,

        );
        switch ($list_layout) {
            case 'list':
                // $template_style = '';
                // $list_class = '';
                // break;
            case 'compact':
                $template_style = '';
                $list_class = 'compact-list';
                break;

            case 'grid':
                $template_style = 'grid';
                $list_class = 'tasks-grid-layout';
                break;

            default:
                $template_style = '';
                $list_class = '';
                break;
        }
        $get_tasks['featured'] = $featured;
        
        $tasks_query = \WorkScout_Freelancer_Task::get_tasks(apply_filters('workscout_freelancer_output_defaults_args', $get_tasks));

        if ($tasks_query->have_posts()) {
            $style_data = array(
                'style'         => $list_layout,
                'per_page'         => $per_page,
                'max_num_pages'    => $tasks_query->max_num_pages,
                'counter'        => $tasks_query->found_posts,
                
            );

            $search_data = array_merge($style_data, $get_tasks);
            $template_loader->set_template_data($search_data)->get_template_part('tasks-start');


            while ($tasks_query->have_posts()) {
                // Loop through listings
                // Setup listing data
                $tasks_query->the_post();

                $template_loader->set_template_data($style_data)->get_template_part('content-task', $template_style);
            }

           
            $template_loader->set_template_data($style_data)->get_template_part('tasks-end');
            
            if($settings['show_pagination'] == "true") {
             //    echo workscout_freelancer_pagination($tasks_query->max_num_pages, false);
                echo "<div class='pagination-container ajax-search'>";
                echo workscout_freelancer_ajax_pagination($tasks_query->max_num_pages, 1);
                echo "</div>";
            }
            if ($show_link) : ?>
                <a class="link_more_jobs button centered" href="<?php echo esc_url($show_link_href['url']); ?>"><i class="fa fa-plus-circle"></i><?php echo $show_link_label; ?></a>
                <div class="margin-bottom-55"></div>
            <?php endif;

        } else {

            $template_loader->get_template_part('archive/no-found');
        }

        wp_reset_query();
        echo ob_get_clean();
    }



    protected function get_terms($taxonomy)
    {
        $taxonomies = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

        $options = ['' => ''];

        if (!empty($taxonomies)) :
            foreach ($taxonomies as $taxonomy) {
                $options[$taxonomy->term_id] = $taxonomy->name;
            }
        endif;

        return $options;
    }

    protected function get_posts()
    {
        $posts = get_posts(
            array(
                'numberposts' => 99,
                'post_type' => 'task',
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
