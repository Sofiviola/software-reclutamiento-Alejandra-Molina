<?php

/**
 * Workscout Elementor Address Box class.
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
class FlipBanner extends Widget_Base
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
		return 'workscout-flip-banner';
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
		return __('Flip Banner', 'workscout_elementor');
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
	//           'background'	=>	'',
	//           'color'			=>	'#274abb',
	//           'opacity'		=>	'0.9',
	//           'text_visible'	=>	'',
	//           'text_hidden'	=>	'',
	//           'from_vs' 		=>	'no'
	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __('Content', 'workscout_elementor'),
			)
		);

		$this->add_control(
			'text_visible',
			array(
				'label'   => __('Front text', 'workscout_elementor'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Title', 'workscout_elementor'),
			)
		);
		$this->add_control(
			'text_hidden',
			array(
				'label'   => __('Flipped text', 'workscout_elementor'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Text after hover', 'workscout_elementor'),
			)
		);

		$this->add_control(
			'website_link',
			[
				'label' => __('Link', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'workscout_elementor'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'background',
			[
				'label' => __('Choose Background Image', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
			]
		);


		$this->add_control(
			'color',
			[
				'label' => __('Overlay Color', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,

				// 'selectors' => [
				// 	'{{WRAPPER}} .title' => 'color: {{VALUE}}',
				// ],
			]
		);
		$this->add_control(
			'opacity',
			[
				'label' => __('Overlay Opacity', 'workscout_elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [

					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 70,
				],
			]
		);

		// $this->add_control(
		// 	'content',
		// 	[
		// 		'label' => __( 'Message', 'plugin-name' ),
		// 		'type' => \Elementor\Controls_Manager::TEXTAREA,
		// 		'rows' => 5,
		// 		'placeholder' => __( 'Enter your notification text', 'workscout_elementor' ),
		// 	]
		// );

		// $this->add_control(
		// 	'type',
		// 	[
		// 		'label' => __( 'Notice type tag ', 'workscout_elementor' ),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'default' => 'success',
		// 		'options' => [
		// 			'success' 	=> __( 'Success', 'workscout_elementor' ),
		// 			'error' 	=> __( 'Error', 'workscout_elementor' ),
		// 			'warning' 	=> __( 'Warning', 'workscout_elementor' ),
		// 			'notice' 	=> __( 'Notice', 'workscout_elementor' ),

		// 		],
		// 	]
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
		$target = $settings['website_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['website_link']['nofollow'] ? ' rel="nofollow"' : '';
		if (!empty($settings['website_link']['url'])) {
			$full_url = 'href="' . $settings['website_link']['url'] . '"' . $target . $nofollow;
		} else {
			$full_url = 'href="#"';
		} ?>

		<!-- Flip banner -->
		<a <?php echo $full_url; ?>" class="flip-banner parallax" data-background="<?php echo esc_url($settings['background']['url']); ?>" data-color="<?php echo esc_attr($settings['color']); ?>" data-color-opacity="<?php echo esc_attr($settings['opacity']['size'] / 100); ?>" data-img-width="2500" data-img-height="1600">

			<div class="flip-banner-content">
				<h2 class="flip-visible"><?php echo esc_html($settings['text_visible']); ?></h2>
				<h2 class="flip-hidden"><?php echo esc_html($settings['text_hidden']); ?> <svg width="48px" height="48px" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="48" height="48" fill="white" fill-opacity="0.01" />
						<path d="M19 12L31 24L19 36" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</h2>
			</div>
		</a>
<?php
	}
}
