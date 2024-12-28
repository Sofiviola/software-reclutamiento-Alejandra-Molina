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
class ResumeTaxonomyGrid extends Widget_Base
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
		return 'workscout-taxonomy-grid-resume';
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
		return __('Taxonomy Grid - Resumes', 'workscout_elementor');
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
		return 'fa fa-th-large';
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
		// 	'taxonomy' => '',
		// 'xd' 	=> '',
		// 'only_top' 	=> 'yes',
		// 'autoplay'      => '',
		//          'autoplayspeed'      => '3000',

		$this->add_control(
			'taxonomy',
			[
				'label' => __('Taxonomy', 'elementor-pro'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'resume_category',
				'options' => $this->get_taxonomies(),

			]
		);

		$taxonomy_names = get_object_taxonomies('resume', 'object');
		foreach ($taxonomy_names as $key => $value) {

			$this->add_control(
				$value->name . '_include',
				[
					'label' => __('Include resumes from ' . $value->label, 'workscout_elementor'),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms($value->name),
					'condition' => [
						'taxonomy' => $value->name,
					],
				]
			);
			$this->add_control(
				$value->name . '_exclude',
				[
					'label' => __('Exclude resumes from ' . $value->label, 'workscout_elementor'),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms($value->name),
					'condition' => [
						'taxonomy' => $value->name,
					],
				]
			);
		}
		$this->add_control(
			'number',
			[
				'label' => __('Terms to display', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 99,
				'step' => 1,
				'default' => 6,
			]
		);
		$this->add_control(
			'only_top',
			[
				'label' => __('Show only top terms', 'workscout_elementor'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'your-plugin'),
				'label_off' => __('Hide', 'your-plugin'),
				'return_value' => 'yes',
				'default' => 'yes',

			]
		);


		$this->add_control(
			'show_counter',
			[
				'label' => __('Show resumes listings counter', 'workscout_elementor'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'your-plugin'),
				'label_off' => __('Hide', 'your-plugin'),
				'return_value' => 'yes',
				'default' => 'yes',

			]
		);

		$this->add_control(
			'browse_categories',
			[
				'label' => esc_html__('Browse more categories link', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__('https://your-link.com', 'plugin-name'),
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
					'custom_attributes' => '',
				],
			]
		);

		// $this->add_control(
		// 	'style',
		// 	[
		// 		'label' => __('Style ', 'workscout_elementor'),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'default' => '',
		// 		'options' => [
		// 			'default' => __('Default', 'workscout_elementor'),
		// 			'alt' => __('Alternative', 'workscout_elementor'),

		// 		],
		// 	]
		// );



		// $taxonomy_names = get_object_taxonomies( 'listing','object' );

		// foreach ($taxonomy_names as $key => $value) {
		// 	$shortcode_atts[$value->name.'_include'] = '';
		// 	$shortcode_atts[$value->name.'_exclude'] = '';
		// }


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


		$taxonomy_names = get_object_taxonomies('resume', 'object');

		$taxonomy = $settings['taxonomy'];


		if (empty($taxonomy)) {
			$taxonomy = "resume_category";
		}
		$query_args = array(
			'include' => $settings[$taxonomy . '_include'],
			'exclude' => $settings[$taxonomy . '_exclude'],
			'hide_empty' => false,
			'number' => $settings['number'],
		);
		if ($settings['only_top'] == 'yes') {
			$query_args['parent'] = 0;
		}
		$terms = get_terms($settings['taxonomy'], $query_args);

		if (!empty($terms) && !is_wp_error($terms)) {
?>

			<div class="categories-container">

				<!-- Item -->
				<?php
				foreach ($terms as $term) {
					$t_id = $term->term_id;
					$term_meta = get_option("taxonomy_$t_id");
					if (isset($term_meta['fa_icon'])) {
						if ($term_meta['fa_icon'] == 'fa fa-' || $term_meta['fa_icon'] == 'ln ln-') {
							$icon = '';
						} else {
							$icon = $term_meta['fa_icon'];
						}
					} else {
						$icon = '';
					}
					if (is_array($term_meta)) {
						$imageicon = $term_meta['upload_icon'];
						$image_bg = $term_meta['upload_header'];
					} else {
						$imageicon = false;
						$image_bg = false;
					}

					// retrieve the existing value(s) for this meta field. This returns an array

					$_icon_svg = get_term_meta($t_id, '_icon_svg', true);
					$_icon_svg_image = wp_get_attachment_image_src($_icon_svg, 'medium');
					if (empty($icon)) {
						$icon = 'fa fa-globe';
					}

				?>
					<a href="<?php echo get_term_link($term); ?>" class="new-category-box">
						<div class="category-box-icon">
							<?php
							if (!empty($_icon_svg)) { ?>
								<i class="listeo-svg-icon-box-grid">
									<?php echo workscout_render_svg_icon($_icon_svg); ?>
								</i>

								<?php } else {
								if (!empty($imageicon)) { ?>
									<img src="<?php echo esc_attr($imageicon) ?>" />
									<?php } else if (!empty($icon)) {
									$check_if_new = substr($icon, 0, 3);
									if ($check_if_new == 'fa ' || $check_if_new == 'ln '  || $check_if_new == 'la ') { ?>
										<i class="<?php echo esc_attr($icon); ?>"></i>
									<?php } else { ?>
										<i class="fa fa-<?php echo esc_attr($icon); ?>"></i>
							<?php }
								}
							}

							?>
						</div>
						<?php if ($settings['show_counter'] == "yes") {
							$count = workscout_get_term_post_count('resume_category', $term->term_id); ?> <div class="category-box-counter counter"><?php echo $count; ?></div><?php } ?>
						<div class="category-box-content">
							<h3><?php echo $term->name; ?></h3>
						</div>
					</a>

				<?php } ?>
			</div>
			<?php
			if ($settings['browse_categories']['url']) {
			?>

				<div class="browse-all-cat-btn"><a href="<?php echo esc_url($settings['browse_categories']['url']); ?>" class="button centered"><?php esc_html_e('Browse All Categories', 'workscout_elementor'); ?></a></div>
<?php
			}
		}
	}


	protected function get_taxonomies()
	{
		$taxonomies = get_object_taxonomies('resume', 'objects');

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
