<?php
/**
 * workscout class.
 *
 * @category   Class
 * @package    Elementorworkscout
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

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * workscout widget class.
 *
 * @since 1.0.0
 */
class Headline extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'workscout-headline';
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
	public function get_title() {
		return __( 'Headline', 'workscout_elementor' );
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
	public function get_icon() {
		return 'fa fa-heading';
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
	public function get_categories() {
		return array( 'workscout' );
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
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'workscout_elementor' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'workscout_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Title', 'workscout_elementor' ),
			)
		);	
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'workscout_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
		  'style_section',
		  [
		    'label' => __( 'Style Section', 'workscout_elementor' ),
		    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		  ]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Element tag ', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => __( 'H1', 'workscout_elementor' ),
					'h2' => __( 'H2', 'workscout_elementor' ),
					'h3' => __( 'H3', 'workscout_elementor' ),
					'h4' => __( 'H4', 'workscout_elementor' ),
					'h5' => __( 'H5', 'workscout_elementor' ),
				],
			]
		);


		$this->add_control(
		  'text_align',
		  [
		    'label' => __( 'Text align', 'workscout_elementor' ),
		    'type' => \Elementor\Controls_Manager::CHOOSE,
		    'options' => [
		      'left' => [
		        'title' => __( 'Left', 'workscout_elementor' ),
		        'icon' => 'fa fa-align-left',
		      ],
		      'center' => [
		        'title' => __( 'Center', 'workscout_elementor' ),
		        'icon' => 'fa fa-align-center',
		      ],
		      'right' => [
		        'title' => __( 'Right', 'workscout_elementor' ),
		        'icon' => 'fa fa-align-right',
		      ],
		    ],
		    'default' => 'left',
		    'toggle' => true,
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
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'subtitle', 'none' );
		$css_class = 'headline ';
		if(isset($settings['text_align'])) {
				switch ($settings['text_align']) {
					case 'left':
						$css_class .= ' headline-aligned-to-left ';
						break;
					case 'right':
						$css_class .= ' headline-aligned-to-right ';
						break;
					case 'center':
						$css_class .= ' headline-aligned-to-center centered headline-extra-spacing';
						break;
					
					default:
						# code...
						break;
				}	
			}
	

		if ( !empty($settings['subtitle']) ) {
			$css_class .= ' headline-with-subtitle ';
		}
		$style = 'style="';
		$style .= (isset($settings['text_align'])) ? 'text-align:'.$settings['text_align'].';' : '' ;
		$style .= '"';
		?>
		<<?php echo  $settings['type']; ?> 
		<?php echo $style; ?> class="<?php echo esc_attr($css_class); ?>"> <?php echo $settings['title']; ?> <?php if($settings['subtitle']) : ?> <span <?php echo $this->get_render_attribute_string( 'subtitle' ); ?>> <?php echo $settings['subtitle']; ?></span><?php endif; ?></<?php echo $settings['type'] ?>>
		<?php
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */

}