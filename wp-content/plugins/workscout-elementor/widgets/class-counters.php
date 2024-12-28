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
class Counters extends Widget_Base
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
        return 'workscout-counters';
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
        return __('Counters', 'workscout_elementor');
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
        // 'title' 		=> 'Service Title',
        // 	    'url' 			=> '',
        // 	    'url_title' 	=> '',

        // 	   	'icon'          => 'im im-icon-Office',
        // 	    'type'			=> 'box-1', // 'box-1, box-1 rounded, box-2, box-3, box-4'
        // 	    'with_line' 	=> 'yes',
        // 	    
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $repeater = new \Elementor\Repeater();
     
        $repeater->add_control(
            'title_counter',
            [
                'label' => __('Title', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Counter Title', 'plugin-domain'),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'number_counter',
            [
                'label' => __('Custom number', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'counter_type',
            [
                'label' => __('Content', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'jobs' =>  __('Jobs counter.', 'workscout_elementor'), //jobs, resumes, posts, members, candidates, employers, 
                    'resumes' =>  __('Resumes counter.', 'workscout_elementor'),
                    'posts' =>  __('Fea', 'workscout_elementor'),
                    'members' =>  __('Users counter. ', 'workscout_elementor'),
                    'candidates' =>  __('Candidates counter', 'workscout_elementor'),
                    'employers' =>  __('Employers counter', 'workscout_elementor'),
                ],
                'show_label' => true,
            ]
        );
        $repeater->add_control(
            'scale',
            [
                'label' => __('Scale', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::TEXT,

                'show_label' => true,
            ]
        );

        $this->add_control(
            'list',
            [
                'label' => __('Repeater List', 'plugin-domain'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                // 'default' => [
                //     [
                //         'slide_title_1st' => __('Title #1', 'plugin-domain'),
                //         'slide_title_2nd' => __('Item content. Click the edit button to change this text.', 'plugin-domain'),
                //     ]

                // ],
                'title_field' => '{{{ title_counter }}}',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style Section', 'workscout_elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label' => __('Background Color', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,

                'selectors' => [
                    '{{WRAPPER}} #counters' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,

                'selectors' => [
                    '{{WRAPPER}} #counters .counter-box p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'counter_color',
            [
                'label' => __('Counter Color', 'workscout_elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,

                'selectors' => [
                    '{{WRAPPER}} .counter, {{WRAPPER}} .counter-box i' => 'color: {{VALUE}}',
                ],
            ]
        );




        /* Add the options you'd like to show in this tab here */

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
        <div id="counters">
            <div class="container">
                <?php
                $settings = $this->get_settings_for_display();

                if ($settings['list']) {


                    foreach ($settings['list'] as $item) {
                        $type = $item['counter_type'];
                        $number = $item['number_counter'];

                        switch ($type) {
                            case "jobs":
                                $count = wp_count_posts('job_listing', 'readable');
                                $number = (isset($count->publish))  ? $count->publish : '0';
                                break;

                            case "resumes":
                                $count = wp_count_posts('resume');
                                $number = (isset($count->publish))  ? $count->publish : '0';
                                break;

                            case "posts":
                                $count = wp_count_posts('posts', 'readable');
                                $number = (isset($count->publish))  ? $count->publish : '0';
                                break;



                            case "members":
                                $args = array(
                                    'fields' => 'ID',
                                );

                                $users = get_users($args);
                                $number = count($users);

                                break;

                            case "candidates":
                                $args = array(
                                    'role' => 'candidate', //substitute your role here as needed
                                    'fields' => 'ID',
                                );

                                $users = get_users($args);
                                $number = count($users);

                                break;

                            case "employers":
                                $args = array(
                                    'role' => 'employer', //substitute your role here as needed
                                    'fields' => 'ID',
                                );
                                $users = get_users($args);
                                $number = count($users);
                                break;

                            default:
                        }

                ?>
                        <div class="columns four">


                            <div class=" counter-box">
                                <span class="counter"><?php echo $number; ?></span>
                                <?php
                                if (!empty($item['scale'])) { ?>
                                    <i><?php echo $item['scale']; ?></i>
                                <?php } ?>

                                <p><?php echo $item['title_counter']; ?></p>
                            </div>
                        </div>

                <?php }
                }
                ?>
            </div>
        </div>
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
