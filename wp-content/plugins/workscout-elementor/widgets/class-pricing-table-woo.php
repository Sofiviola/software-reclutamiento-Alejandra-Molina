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
class PricingTableWoo extends Widget_Base
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
		return 'workscout-pricingtable-woocommerce';
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
		return __('Pricing Table WooCommerce', 'workscout_elementor');
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
		return 'fa fa-cart-plus';
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
		// "type"          => 'color-1',
		//      
		//        "color"         => '',
		//        "title"         => '',
		//       
		//        "price"         => '',
		//        "discounted"    => '',
		//        "per"           => '',

		//        "buttonlink"    => '',
		//        "buttontext"    => 'Sign Up',


		$this->start_controls_section(
			'section_content',
			array(
				'label' => __('Content', 'workscout_elementor'),
			)
		);


		$this->add_control(
			'orderby',
			[
				'label' => __('Order by', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'price' 		=>  __('Price', 'workscout_elementor'),
					'price-desc' 	=> __('Price desc', 'workscout_elementor'),
					'rating' 		=> __('Rating', 'workscout_elementor'),
					'title' 		=> __('Title', 'workscout_elementor'),
					'popularity' 	=> __('Popularity', 'workscout_elementor'),
					'random' 		=> __('Random', 'workscout_elementor'),

				],
			]
		);
		$this->add_control(
			'type',
			[
				'label' => __('Type of packages', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'jobs',
				'options' => [
					'jobs' 		=>  __('Job Packages', 'workscout_elementor'),
					'resumes' 	=> __('Resume packages', 'workscout_elementor'),
					'both' 		=> __('Both', 'workscout_elementor'),


				],
			]
		);
		$this->add_control(
			'columns_per_row',
			[
				'label' => __('Columns in a row', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'step' => 1,
				'default' => 3,
			]
		);

		$this->add_control(
			'buttonlink',
			[
				'label' => __('Option URL for add Listing overide', 'workscout_elementor'),
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

		$target = $settings['buttonlink']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['buttonlink']['nofollow'] ? ' rel="nofollow"' : '';

		ob_start();

		switch ($settings['type']) {
			case 'jobs':
				$terms = array('job_package', 'job_package_subscription');
				break;
			case 'resumes':
				$terms = array('resume_package', 'resume_package_subscription');
				break;
			case 'both':
				$terms = array('job_package', 'job_package_subscription', 'resume_package', 'resume_package_subscription');
				break;

			default:
				$terms = array('job_package', 'job_package_subscription');
				break;
		}
		$args = array(
			'post_type'  => 'product',
			'limit'      => -1,
			'tax_query'  => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $terms
				)
			)
		);

		switch ($settings['orderby']) {
			case 'price':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = '_price';
				$args['order'] = 'asc';
				break;

			case 'price-desc':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = '_price';
				$args['order'] = 'desc';
				break;

			case 'rating':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = '_wc_average_rating';
				$args['order'] = 'desc';
				break;

			case 'popularity':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = 'total_sales';
				$args['order'] = 'desc';
				break;

			case 'random':
				$args['orderby'] = 'rand';
				$args['order'] = '';
				$args['meta_key'] = '';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order'] = 'ASC';
				$args['meta_key'] = '';
				break;
		}

		$products = new \WP_Query($args); ?>

		<div class=" pricing-plans-container margin-top-60">

			<?php
			$counter = 1;
			while ($products->have_posts()) : $products->the_post();

				$count = $products->found_posts;
				$class_width = 'col-md-3';
				if ($count == 2) {
					$class_width = 'col-md-6 ';
				}
				if ($count == 3) {
					$class_width = 'col-md-4 ';
				}
				if ($count == 4) {
					$class_width = 'col-md-3 ';
				}
				$product = wc_get_product(get_post()->ID);

				if (!$product->is_type(array('job_package', 'job_package_subscription')) || !$product->is_purchasable()) {
					continue;
				}

			?>
				<div class=" pricing-plan <?php if ($product->is_featured()) echo "recommended"; ?> ">


					<?php if ($product->is_featured()) { ?>
						<div class=" recommended-badge"><?php esc_html_e('Recommended', 'workscout') ?></div>
					<?php } ?>
					<h3><?php echo $product->get_title(); ?></h3>
					<?php if ($product->get_short_description()) { ?> <p class="margin-top-10"><?php echo $product->get_short_description(); ?></p><?php } ?>

					<div class="pricing-plan-label billed-monthly-label"><?php echo $product->get_price_html(); ?></div>
					<div class="pricing-plan-features">

						<ul>
							<?php
							$listingslimit = $product->get_limit();
							if (!$listingslimit) {
								echo "<li>";
								esc_html_e('Unlimited number of listings', 'workscout');
								echo "</li>";
							} else { ?>
								<li>
									<?php esc_html_e('This plan includes ', 'workscout');
									printf(_n('%d listing', '%s listings', $listingslimit, 'workscout') . ' ', $listingslimit); ?>
								</li>
							<?php }
							$duration = $product->get_duration();
							if ($duration > 0) : ?>
								<li>
									<?php esc_html_e('Listings are visible ', 'workscout');
									printf(_n('for %s day', 'for %s days', $product->get_duration(), 'workscout'), $product->get_duration()); ?>
								</li>
							<?php else : ?>
								<li>
									<?php esc_html_e('Unlimited availability of listings', 'workscout');  ?>
								</li>
							<?php endif; ?>

							<?php
							// check if the _job_listing_featured meta  field from the product is enabled


							$job_listing_featured = get_post_meta($product->get_id(), '_job_listing_featured', true);

							if ($job_listing_featured == "yes") : ?>
								<li><?php esc_html_e('Highlighted in Search Results', 'workscout');  ?></li>
							<?php endif; ?>
						</ul>
						<?php

						echo $product->get_description();

						?>
						<div class="clearfix"></div>
					</div>
					<div class="plan-features">
						<?php

						echo $product->get_description();
						$link   = $product->add_to_cart_url();
						$label  = apply_filters('add_to_cart_text', esc_html__('Add Listing', 'workscout_elementor'));

						if (!empty($settings['buttonlink']['url'])) {
							echo '<a  class="button" href="' . $settings['buttonlink']['url'] . '"' . $target . $nofollow . '>' . $label . '</a>';
						} else { ?>
							<a href="<?php echo esc_url($link); ?>" class="button"><i class="fa fa-shopping-cart"></i> <?php echo esc_html($label); ?></a>

						<?php } ?>

					</div>

				</div>

				<?php if (($counter % $settings['columns_per_row']) == 0) { ?>
		</div>
		<div class="pricing-plans-container margin-top-30">
		<?php } ?>
	<?php
				$counter++;
			endwhile; ?>
		</div>
<?php $pricing__output =  ob_get_clean();
		wp_reset_postdata();
		echo $pricing__output;
	}
}
