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
class SpotlightTasks extends Widget_Base
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
        return 'workscout-spotlight-tasks';
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
        return __('Spotlight Tasks', 'workscout_elementor');
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
                'default' => __('Featured Tasks', 'workscout_elementor'),
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


        $this->add_control(
            'delay',
            array(
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
            'task_skill',
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
            'task_ids',
            [
                'label' => __('Show only selected tasks', 'workscout_elementor'),
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
                'label' => __('Featured tasks', 'workscout_elementor'),
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
        // $visible = $settings['visible'] ? $settings['visible'] : '1,1,1,1';
        $title = $settings['title'];
        
        $autoplay = $settings['autoplay'];
        $delay = $settings['delay'];
        
        $categories = $settings['categories'];
        $task_skill = $settings['task_skill'];
        $task_ids = $settings['task_ids'];
        $featured = $settings['featured'];
        $slides_to_show = $settings['slides_to_show'];


        // Array handling
        $categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));
        $task_skill          = is_array($task_skill) ? $task_skill : array_filter(array_map('trim', explode(',', $task_skill)));

        if ($featured != "null") {

            $featured = (is_bool($featured) && $featured) || in_array($featured, array('1', 'true', 'yes')) ? true : false;
        } else {
            $featured = null;
        }

        $query_args = array(
            'post_type'              => 'task',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => 1,
            'offset'                 => 0,
            'posts_per_page'         => intval($per_page),
            'orderby'                => $orderby,
            'order'                  => $order,
            'fields'                 => 'all'
        );

        if (!empty($task_ids)) {

            $query_args['post__in'] = $task_ids;
        }

        if (!is_null($featured)) {
            $query_args['meta_query'][] = array(
                'key'     => '_featured',
                'value'   => '1',
                'compare' => $featured ? '=' : '!='
            );
        }


        if (!empty($task_skill)) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'task_skill',
                'field'    => 'slug',
                'terms'    => $task_skill
            );
        }

        if (!empty($categories)) {
            $field    = is_numeric($categories[0]) ? 'term_id' : 'slug';

            $operator = 'all' === get_option('job_manager_category_filter_type', 'all') && sizeof($categories) > 1 ? 'AND' : 'IN';
            $query_args['tax_query'][] = array(
                'taxonomy'         => 'task_category',
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
        ?>
<h3 class="margin-bottom-5"><?php echo esc_html($title); ?></h3>
<?php
            $wp_query = new \WP_Query($query_args);
            if ($wp_query->have_posts()) :   ?>

            <!-- Showbiz Container -->
            <?php $slick_autplay = ($autoplay == 'on') ? 'true' : 'false'; ?>
            <div id="task-spotlight" data-slick='{"slidesToShow":<?php echo $slides_to_show; ?>,"slidesToScroll":<?php echo $slides_to_show; ?>,  "autoplay":<?php echo $slick_autplay; ?>, "autoplaySpeed":<?php echo $delay; ?>}' class="job-spotlight-car  showbiz-container">
                <?php while ($wp_query->have_posts()) :
                    $wp_query->the_post();
                    $id = get_the_id(); ?>
                    <div class="task-spotlight">
                        <!-- Task -->
                        <a href="<?php the_permalink(); ?>" class="task-listing <?php if (is_position_featured()) {   echo 'task-listing-featured';  } ?>">

                            <!-- Job Listing Details -->
                            <div class="task-listing-details">
                                <?php if (is_position_featured()) { ?>
                                    <div class="listing-badge"><i class="fa fa-star"></i></div>
                                <?php } ?>
                                <!-- Details -->
                                <div class="task-listing-description">
                                    <h3 class="task-listing-title">
                                        <?php the_title(); ?>
                                    </h3>
                                    <ul class="task-icons">
                                        <?php $company = get_post_meta($id, '_company_id', true);
                                             if ($company) { ?>
                                            <li><i class="icon-material-outline-business"></i> <?php echo get_the_title($company); ?></li>
                                        <?php } else { ?>
                                            <li><i class="icon-material-outline-account-circle"></i> Private Person</li>
                                        <?php } ?>
                                        <li><i class="icon-material-outline-location-on"></i> <?php ws_task_location(); ?></li>
                                        <li><i class="icon-material-outline-access-time"></i> <?php task_publish_date() ?></li>
                                    </ul>

                                    <?php
                                        $terms = get_the_terms($id, 'task_skill');

                                        if ($terms && !is_wp_error($terms)) :
                                            echo '<div class="task-tags">';
                                            $jobcats = array();
                                            foreach ($terms as $term) {
                                                echo "<span>" . $term->name . "</span>";
                                            }   ?>
                                            </div>
                                        <?php endif; ?>
                            </div>
                    </div>

                    <div class="task-listing-bid">
                        <div class="task-listing-bid-inner">
                            <div class="task-offers">
                                <strong><?php echo get_workscout_task_range($id); ?></strong>
                                <span><?php echo get_workscout_task_type($id); ?></span>
                            </div>
                            <span class="button button-sliding-icon ripple-effect">Bid Now <i class="icon-material-outline-arrow-right-alt"></i></span>
                        </div>
                    </div>
                    </a>


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
                $options[$taxonomy->id] = $taxonomy->name;
            }
        endif;

        return $options;
    }
    protected function get_posts()
    {
        $posts = get_posts(
            array(
                'numberposts' => 199,
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
