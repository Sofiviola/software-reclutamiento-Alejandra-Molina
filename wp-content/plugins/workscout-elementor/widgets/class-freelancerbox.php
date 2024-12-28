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
class FreelancerBox extends Widget_Base
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
		return 'workscout-freelancerbox';
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
		return __('FreelancerBox', 'workscout_elementor');
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
		return 'fa fa-images';
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

		//link
		//image
		//featured
		//term
		//style alternative-imagebox
		//
		//

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'workscout_elementor'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		//select control for elementor
		$this->add_control(
			'source_type',
			[
				'label' => __('Source', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'resume',
				'multiple' => true,
				'options' => [

					'resume' =>  __('Get data from Freelancer profile. ', 'workscout_elementor'),
					'custom' =>  __('Create data manually ', 'workscout_elementor'),

				],
			]
		);


		$this->add_control(
			'resume_id',
			[
				'label' => __('Show Freelancer:', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => false,
				'default' => [],
				'options' => $this->get_posts(),
				'condition' => [
					'source_type' => 'resume',
				],
			]
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
				'condition' => [
					'source_type' => 'custom',
				],
			]
		);


		$this->add_control(
			'cover',
			[
				'label' => __('Freelancer Box Cover', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'source_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'name',
			array(
				'label'   => __('Name', 'workscout_elementor'),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __('Kathy Brown', 'workscout_elementor'),
				'condition' => [
					'source_type' => 'custom',
				],
			)
		);

		$this->add_control(
			'country',
			[
				'label' => __('Country', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => workscoutGetCountries(),
				'condition' => [
					'source_type' => 'custom',
				],
			]
		);
		$this->add_control(
			'job_title',
			[
				'label' => __('Job Title', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Marketing Manager',
				'condition' => [
					'source_type' => 'custom',
				],

			]
		);

		$this->add_control(
			'rating',
			[
				'label' => __('Rating', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '4.8',
				'min'	=> '1',
				'max'	=> '5',
				'step'	=> '0.1',
				'condition' => [
					'source_type' => 'custom',
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

		if ($settings['source_type'] == 'resume' && isset($settings['resume_id'])) {
			//check if post with resume id exits
			if(is_numeric($settings['resume_id'])){
				$freelancer = get_post($settings['resume_id']);
			} else {
			
			
				$freelancer = get_posts(array(
					'numberposts' => 1,
					'post_type' => 'resume',
					'suppress_filters' => true,
					'orderby' => 'rand',
				));
				$freelancer = $freelancer[0];
				// this is fix for empty demo content
			}

			if ($freelancer) { ?>
				<a href="<?php the_permalink($freelancer); ?>" class="elementor-freelancer">
					<div class="elementor-freelancer-img">
						<?php the_candidate_photo('workscout_core-avatar', get_template_directory_uri() . '/images/candidate.png', $freelancer); ?>
					</div>
					<div class="elementor-freelancer-footer">
						<h3><?php echo get_the_title($freelancer->ID); ?>
							<?php
							$country = get_post_meta($freelancer->ID, '_country', true);

							if ($country) {
								$countries = workscoutGetCountries();
							?>
								<img class=" flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" alt="" title="<?php echo $countries[$country]; ?>" data-tippy-placement="top">
							<?php } ?>
						</h3>
						<?php the_candidate_title('<span>', '</span> ', true, $freelancer); ?>
						<?php $rating_value = get_post_meta($freelancer->ID, 'workscout-avg-rating', true);
						if ($rating_value) {  ?>
							<div class="freelancer-rating">
								<div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
							</div>
						<?php } else { ?>
							<div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout_elementor'); ?></div>
						<?php } ?>
					</div>
				</a>
			<?php }
		} else {
			$url = (!empty($settings['url']['url'])) ?  $settings['url']['url'] :  false;

			?>
			<a <?php if ($url) { ?> href="<?php echo esc_url($url); ?> " <?php } ?> class="elementor-freelancer">
				<div class="elementor-freelancer-img">
					<?php


					if (isset($settings['cover']['url']) && !empty(isset($settings['cover']['url']))) { ?><img src="<?php echo $settings['cover']['url']; ?>" alt=""> <?php  } ?>
				</div>
				<div class="elementor-freelancer-footer">
					<h3><?php echo $this->get_settings('name') ?>
						<?php
						$country = $this->get_settings('country');

						if ($country) {
							$countries = workscoutGetCountries();
						?>
							<img class=" flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" alt="" title="<?php echo $countries[$country]; ?>" data-tippy-placement="top">
						<?php } ?>
					</h3>
					<span><?php echo $this->get_settings('job_title') ?></span>
					<?php $rating_value = $settings['rating'];
					if ($rating_value) {  ?>
						<div class="freelancer-rating">
							<div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
						</div>
					<?php } else { ?>
						<div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout_elementor'); ?></div>
					<?php } ?>
				</div>
			</a>
<?php }
		//lik
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
