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

if (!defined('ABSPATH')) {
	// Exit if accessed directly.
	exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class IconBox extends Widget_Base
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
		return 'workscout-iconbox';
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
		return __('IconBox', 'workscout_elementor');
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
				'type'    => Controls_Manager::TEXT,
				'default' => __('Title', 'workscout_elementor'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'content',
			array(
				'label'   => __('Content', 'workscout_elementor'),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __('Content', 'workscout_elementor'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			)
		);
		$this->add_control(
			'url',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style Section', 'workscout_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __('Type of iconbox', 'plugin-domain'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard' =>  __('Style 1', 'workscout_elementor'),
					'alt' =>  __('Style 2 ', 'workscout_elementor'),
					'style3' =>  __('Style 3 ', 'workscout_elementor'),


				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __('Icon', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
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

		$this->add_inline_editing_attributes('title', 'none');
		$this->add_inline_editing_attributes('subtitle', 'none');
		$target = $settings['url']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['url']['nofollow'] ? ' rel="nofollow"' : '';

		if ($settings['style'] == 'standard') {
			if (!empty($settings['url']['url'])) {
				echo '<a href="' . $settings['url']['url'] . '"' . $nofollow . '>';
			} ?>

			<div class="icon-box-2">
				<?php if (isset($settings['icon']['library']) && $settings['icon']['library'] == 'svg') { ?>
					<i class="workscout-svg-icon-box"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);  ?></i>
				<?php } else {
					\Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
				} ?>
				<h3><?php echo $this->get_settings('title') ?></h3>
				<p><?php echo $this->get_settings('content') ?></p>
			</div>

			<?php
			if (!empty($settings['url']['url'])) {
				echo "</a>";
			}
		}
		if ($settings['style'] == 'alt' || $settings['style'] == 'style3') { ?>

			<div class="icon-box rounded alt <?php if ($settings['style'] == 'style3') { echo "style3";  } ?> ">
				<a href="<?php echo $settings['url']['url']; ?>">

					<?php if (isset($settings['icon']['library']) && $settings['icon']['library'] == 'svg') { ?>
						<i class="workscout-svg-icon-box"><?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);  ?></i>
					<?php } else {
						\Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
					} ?>


					<h4><?php echo $this->get_settings('title') ?></h4>
					<p><?php echo $this->get_settings('content') ?></p>

				</a>
			</div>
		<?php }
		
	}
}
