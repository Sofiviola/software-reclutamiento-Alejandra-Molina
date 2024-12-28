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
class HomeSearchResumes extends Widget_Base
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
        return 'workscout-homesearch-resumes';
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
        return __('Home Search Resumes Form', 'workscout_elementor');
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


        // $this->add_control(
        //     'background',
        //     [
        //         'label' => __('Choose Background Image', 'workscout_elementor'),
        //         'label_block' => true,
        //         'type' => \Elementor\Controls_Manager::MEDIA,
        //         'default' => [
        //             'url' => \Elementor\Utils::get_placeholder_image_src(),
        //         ]
        //     ]
        // );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'new_background',
                'label' => esc_html__('Background', 'plugin-name'),
                'types' => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .intro-banner',
            ]
        );

        $this->add_control(
            'title',
            array(
                'label'   => __('Title', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Find Candidate', 'workscout_elementor'),
            )
        );
        $this->add_control(
            'subtitle',
            array(
                'label'   => __('Title', 'workscout_elementor'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Hire Experts or be hirded in', 'workscout_elementor'),
            )
        );

        $this->add_control(
            'typed',
            [
                'label' => __('Enable Type words effect', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'your-plugin'),
                'label_off' => __('Hide', 'your-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',

            ]
        );
        $this->add_control(
            'typed_text',
            array(
                'label'   => __('Text to displayed in "typed" section, separate by coma', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('healthcare, automotive, sales & marketing, accounting', 'workscout_elementor'),
            )
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
                'default' => __('What candidate are you looking for?', 'workscout_elementor'),
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
                'default' => __('Search freelancer services', 'workscout_elementor'),
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
                'default' => __('city, province or region', 'workscout_elementor'),
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
            'advanced_search',
            [
                'label' => __('Show "Advanced Search" link', 'workscout_elementor'),

                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'workscout_elementor'),
                'label_off' => __(
                    'Hide',
                    'workscout_elementor'
                ),
                'return_value' => 'yes',
                'default' => 'yes',

            ]
        );

        $this->add_control(
            'advanced_search_label',
            array(
                'label'   => __('Advanced serach button label', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Advanced Search', 'workscout_elementor'),
            )
        );
        $this->add_control(
            'advanced_search_text',
            array(
                'label'   => __('Advanced serach text', 'workscout_elementor'),
                'label_block' => true,
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Need more search options?', 'workscout_elementor'),
            )
        );



        $this->add_control(
            'overlay',
            [
                'label' => __('Overlay Color', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,

                'selectors' => [
                    '{{WRAPPER}} .intro-banner.dark-overlay:before' => 'background-color: {{VALUE}}',
                ],
            ]
        );



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

        <!-- Intro Banner
    ================================================== -->
        <div class="intro-banner dark-overlay" <?php if ($settings['new_background_image'] == null) { ?>data-background-image="<?php echo esc_url($settings['background']['url']); ?>" <?php } ?>>

            <!-- Transparent Header Spacer -->
            <div class="transparent-header-spacer"></div>
            <div class="container">

                <!-- Intro Headline -->
                <div class="row">
                    <div class="sixteen columns">
                        <div class="banner-headline-alt">
                            <h3>
                                <strong><?php echo $settings['title']; ?></strong>
                                <span> <?php echo $settings['subtitle']; ?>
                                    <?php if ($settings['typed'] == 'yes') { ?> <div class="typed-words"></div><?php } ?>
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>

                <?php
                $search_elements = $settings['searchform'];
                $el_nr = count($search_elements);
                ?>
                <form method="GET" class="workscout_main_search_form" action="<?php echo get_permalink(get_option('resume_manager_resumes_page_id')); ?>">
                    <!-- Search Bar -->
                    <div class="row">
                        <div class="sixteen columns  sc-resumes">
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
                                            <?php if (class_exists('Astoundify_Job_Manager_Regions') && get_option('job_manager_regions_filter') || is_tax('resume_region')) {  ?>
                                            <?php
                                                $dropdown = wp_dropdown_categories(array(
                                                    'show_option_all'           => __('All Regions', 'workscout_elementor'),
                                                    'hierarchical'              => true,
                                                    'orderby'                   => 'name',
                                                    'taxonomy'                  => 'resume_region',
                                                    'name'                      => 'search_region',
                                                    'id'                        => 'search_location',
                                                    'class'                     => 'search_region select-on-home job-manager-category-dropdown',
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


                                    <?php if (in_array("category", $search_elements)) :   ?>
                                        <!-- Search Field -->
                                        <div class="intro-search-field">
                                            <label for="categories" class="field-title ripple-effect"><?php echo $settings['category_label'] ?></label>
                                            <?php


                                            $html =  wp_dropdown_categories(
                                                array(
                                                    'taxonomy'          => 'resume_category',
                                                    'name'              => 'search_category',
                                                    'orderby'           => 'name',
                                                    'class'             => 'select-on-home',
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
                                            <span><?php echo esc_html($settings['search_label']) ?></span>
                                            <i></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>


                <!-- Stats -->
                <?php if ($settings['advanced_search'] == 'yes') {  ?>
                    <div class="row">
                        <div class="sixteen columns">
                            <?php if (apply_filters('workscout_template_home_resume_intro_banner_search_form_advanced', true)) : ?>
                                <div class="adv-search-btn">
                                    <span><?php echo $settings['advanced_search_text'] ?> </span>
                                    <a href="<?php echo get_permalink(get_option('resume_manager_resumes_page_id')); ?>"><?php echo $settings['advanced_search_label']; ?> <i class="la la-long-arrow-alt-right"></i></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } ?>


            </div>
        </div>
        <?php
        $typed = $settings['typed_text'];

        $typed_array = explode(',', $typed);
        ?>
        <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
        <script>
            var typed = new Typed('.typed-words', {
                strings: <?php echo json_encode($typed_array); ?>,
                typeSpeed: 80,
                backSpeed: 80,
                backDelay: 4000,
                startDelay: 1000,
                loop: true,
                showCursor: true
            });
        </script>

<?php


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
