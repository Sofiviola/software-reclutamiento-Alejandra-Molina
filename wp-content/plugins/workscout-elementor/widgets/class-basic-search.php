<?php

/**
 * Awesomesauce class.
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
use Elementor\Scheme_Color;

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class BasicSearchForm extends Widget_Base
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
        return 'workscout-basicsearch';
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
        return __('Basic WorkScout Search Form', 'workscout_elementor');
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
        return 'fa fa-palette';
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
            'content_section',
            [
                'label' => __('Content', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        //select control for elementor
        $this->add_control(
            'source_type',
            [
                'label' => __('Search for:', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'jobs',
                'multiple' => true,
                'options' => [

                    'jobs' =>  __('Job Listings ', 'workscout_elementor'),
                    'resumes' =>  __('Resumes', 'workscout_elementor'),
                    'tasks' =>  __('Tasks ', 'workscout_elementor'),

                ],
            ]
        );
        $this->add_control(
            'searchform',
            [
                'label' => __('Search form elements fields', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'default' => array('keyword', 'location', 'category'),
                'multiple' => true,
                'label_block' => true,
                'options' => [
                    'keyword' =>  __('Keyword search', 'workscout_elementor'),
                    'location' =>  __('Location. ', 'workscout_elementor'),
                    'category' =>  __('Category. ', 'workscout_elementor'),
                ],
            ]
        );

        $this->add_control(
            'keyword_label',
            array(
                'label'   => __('"Keyword" field label', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('What job are you looking for?', 'workscout_elementor'),
                'label_block' => true,
                'condition' => [
                    'searchform' => 'keyword'
                ],
            )
        );
        $this->add_control(
            'keyword_placeholder',
            array(
                'label'   => __('"Keyword" field placeholder', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Job title, Skill, Industry', 'workscout_elementor'),
                'condition' => [
                    'searchform' => 'keyword'
                ],
            )
        );

        $this->add_control(
            'location_label',
            array(
                'label'   => __('"Location" field label', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Where?', 'workscout_elementor'),
                'condition' => [
                    'searchform' => 'location'
                ],
            )
        );
        $this->add_control(
            'location_placeholder',
            array(
                'label'   => __('"Location" field placeholder', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('City, State or Zip', 'workscout_elementor'),
                'condition' => [
                    'searchform' => 'location'
                ],
            )
        );

        $this->add_control(
            'category_label',
            array(
                'label'   => __('"Category" field label', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Categories', 'workscout_elementor'),
                'condition' => [
                    'searchform' => 'category'
                ],
            )
        );
        $this->add_control(
            'category_placeholder',
            array(
                'label'   => __('"Category" field placeholder', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('All Categories', 'workscout_elementor'),
                'condition' => [
                    'searchform' => 'category'
                ],
            )
        );
        $this->add_control(
            'search_label',
            array(
                'label'   => __('Search button label', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Search', 'workscout_elementor'),
            )
        );



        $this->add_control(
            'featured_categories_status',
            [
                'label' => __('Show Featured Categories', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __('Hide', 'workscout_elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'taxonomy_job_listing',
            [
                'label' => __('Taxonomy', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => [],
                'options' => $this->get_taxonomies('job_listing'),
                'condition' => [
                    'source_type' => 'jobs',
                ],
            ]
        );
        $this->add_control(
            'taxonomy_resume',
            [
                'label' => __('Taxonomy', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => [],
                'options' => $this->get_taxonomies('resume'),
                'condition' => [
                    'source_type' => 'resumes',
                ],
            ]
        );
        $this->add_control(
            'taxonomy_task',
            [
                'label' => __('Taxonomy', 'workscout_elementor'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => [],
                'options' => $this->get_taxonomies('task'),
                'condition' => [
                    'source_type' => 'tasks',
                ],
            ]
        );


        $taxonomy_names = get_object_taxonomies(
            array('job_listing', 'resume', 'task'),
            'objects'
        );

        foreach ($taxonomy_names as $key => $value) {


            $this->add_control(
                $value->name . 'term',
                [
                    'label' => __('Show term from ' . $value->label, 'workscout_elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'default' => [],
                    'multiple' => true,
                    'options' => $this->get_terms($value->name),
                    'condition' => [
                        'taxonomy_' . $value->object_type[0] => $value->name,
                    ],
                ]
            );
        }




        //Jobs Search Form elements to display




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


?>


        <?php
        $search_elements = $settings['searchform'];
        $el_nr = count($search_elements);
        ?>

        <?php if ($settings['source_type'] == 'jobs') { ?>
            <form method="GET" class="workscout_main_search_form" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">
        <?php } ?>
        <?php if ($settings['source_type'] == 'resumes') { ?>
             <form method="GET" class="workscout_main_search_form" action="<?php echo get_permalink(get_option('resume_manager_resumes_page_id')); ?>">
        <?php } ?>
        <?php if ($settings['source_type'] == 'tasks') { ?>
            <form method="GET" class="workscout_main_search_form" action="<?php echo get_post_type_archive_link('task'); ?>">
        <?php } ?>


            <!-- Search Bar -->
            <div class="row">
                <div class="sixteen columns  sc-jobs">
                    <div class="intro-banner-search-form">
                        <?php if (apply_filters('workscout_template_home_job_intro_banner_search_form', true)) : ?>
                            <?php if (in_array("keyword", $search_elements)) : ?>
                                <!-- Search Field -->
                                <div class="intro-search-field">
                                    <label for="intro-keywords" class="field-title ripple-effect"><?php echo $settings['keyword_label'] ?></label>
                                    <input id="intro-keywords" name="search_keywords" type="text" placeholder="<?php echo $settings['keyword_placeholder'] ?>">
                                </div>
                            <?php endif; ?>

                            <?php if (in_array("location", $search_elements)) : ?>
                                <!-- Search Field -->
                                <div class="intro-search-field with-autocomplete">
                                    <label for="search_location" class="field-title ripple-effect"><?php echo $settings['location_label'] ?></label>
                                    <?php if (class_exists('Astoundify_Job_Manager_Regions') && $settings['source_type'] != 'tasks' && get_option('job_manager_regions_filter') || is_tax('job_listing_region')) {  ?>
                                    <?php
                                        $dropdown = wp_dropdown_categories(array(
                                            'show_option_all'           => __('All Regions', 'workscout_elementor'),
                                            'hierarchical'              => true,
                                            'orderby'                   => 'name',
                                            'taxonomy'                  => 'job_listing_region',
                                            'name'                      => 'search_region',
                                            'id'                        => 'search_location',
                                            'class'                     => 'search_region select-on-basichome job-manager-category-dropdown',
                                            'hide_empty'                => 1,
                                            'selected'                  => isset($_GET['search_region']) ? $_GET['search_region'] : '',
                                            'echo'                      => false,
                                        ));
                                        $fixed_dropdown = str_replace("&nbsp;", "", $dropdown);
                                        echo $fixed_dropdown;
                                    } else { ?>
                                        <div class="input-with-icon location">
                                            <input id="search_location" name="search_location" type="text" placeholder="<?php echo $settings['location_placeholder'] ?>">

                                            <a href="#"><i title="<?php esc_html_e('Find My Location', 'workscout_elementor') ?>" class="tooltip left la la-map-marked-alt"></i></a>
                                            <?php if (get_option('workscout_map_address_provider', 'osm') == 'osm') : ?><span class="type-and-hit-enter"><?php esc_html_e('type and hit enter', 'workscout_elementor') ?></span> <?php endif; ?>
                                        </div>
                                    <?php } ?>

                                </div>
                            <?php endif; ?>


                            <?php
                            switch ($settings['source_type']) {
                                case 'jobs':
                                    $taxonomy = 'job_listing_category';
                                    $post_type = 'job_listing';
                                    break;
                                case 'resumes':
                                    $taxonomy = 'resume_category';
                                    $post_type = 'resume';
                                    break;
                                case 'tasks':
                                    $taxonomy = 'task_category';
                                    $post_type = 'task';
                                    break;


                                default:
                                    $taxonomy = 'job_listing_category';
                                    $post_type = 'job_listing';
                                    break;
                            }
                            if (in_array("category", $search_elements)) :   ?>
                                <!-- Search Field -->
                                <div class="intro-search-field">
                                    <label for="categories" class="field-title ripple-effect"><?php echo $settings['category_label'] ?></label>
                                    <?php

                                    $name = 'search_category';
                                    if( $settings['source_type'] == 'tasks' )
                                        $name = 'tax-task_category';
                                    
                                    $html =  wp_dropdown_categories(
                                        array(
                                            'taxonomy'          => $taxonomy,
                                            'name'              => $name,
                                            'orderby'           => 'name',
                                            'class'             => 'select-on-basichome',
                                            'hierarchical'      => true,
                                            'hide_empty'        => true,
                                            'show_option_all'   => $settings['category_placeholder'],
                                            'echo' => 0
                                        )
                                    );
                                    echo str_replace('&nbsp;&nbsp;&nbsp;', '- ', $html);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <!-- Button -->
                            <div class="intro-search-button">
                                <button class="button ripple-effect">
                                    <span><?php echo $settings['search_label'] ?></span>
                                    <i></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            </form>

                    <?php

                    if ($settings['featured_categories_status'] == 'yes') :

                        if (isset($taxonomy) && isset($settings[$taxonomy . 'term'])) :

                            $category = is_array($settings[$taxonomy . 'term']) ? $settings[$taxonomy . 'term'] : array_filter(array_map('trim', explode(',', $settings[$taxonomy . 'term'])));


                            if (!empty($category)) : ?>
                                <div class="row">

                                    <h5 class="highlighted-categories-headline"><?php esc_html_e('Or browse featured categories:', 'workscout_elementor') ?></h5>


                                    <div class="highlighted-categories">

                                        <?php

                                        foreach ($category as $value) {

                                            $term = get_term($value, $taxonomy);

                                            if ($term && !is_wp_error($term)) {
                                                $icon = get_term_meta($value, 'icon', true);
                                                $_icon_svg = get_term_meta($value, '_icon_svg', true);
                                        ?>
                                                <!-- Box -->
                                                <a href="<?php echo get_term_link($term->slug, $taxonomy); ?>" class="highlighted-category">
                                                    <?php if (!empty($_icon_svg)) { ?>
                                                        <i>
                                                            <?php echo workscout_render_svg_icon($_icon_svg); ?>
                                                        </i>
                                                    <?php } else if ($icon && $icon != 'empty') { ?><i class="<?php echo esc_attr($icon); ?>"></i><?php } ?>
                                                    <h4><?php echo esc_html($term->name) ?></h4>
                                                </a>

                                        <?php }
                                        } ?>

                                    </div>


                                </div>
                    <?php
                            endif;
                        endif;
                    endif; ?>





            <?php


        }

        protected function get_taxonomies($type)
        {
            $taxonomies = get_object_taxonomies($type, 'objects');

            $options = ['' => ''];

            foreach ($taxonomies as $taxonomy) {
                $options[$taxonomy->name] = $taxonomy->label;
            }

            return $options;
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
    }
