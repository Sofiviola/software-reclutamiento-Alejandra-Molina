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
class PostGrid extends Widget_Base
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
		return 'workscout-posts-grid';
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
		return __('Posts Grid', 'workscout_elementor');
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
		return 'fa fa-file-alt';
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
		// 'limit'=>'6',
		//           'orderby'=> 'date',
		//           'order'=> 'DESC',
		//           'categories' => '',
		//           'exclude_posts' => '',
		//           'include_posts' => '',
		//           'ignore_sticky_posts' => 1,
		//           'limit_words' => 15,
		//           'from_vs' => 'no'


		$this->start_controls_section(
			'section_content',
			array(
				'label' => __('Query', 'workscout_elementor'),
			)
		);

		$this->add_control(
			'per_page',
			[
				'label' => __('Posts to display', 'workscout_elementor'),
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
					'none' =>  __('No order', 'workscout_elementor'),
					'ID' =>  __('Order by post id. ', 'workscout_elementor'),
					'author' =>  __('Order by author.', 'workscout_elementor'),
					'title' =>  __('Order by title.', 'workscout_elementor'),
					'name' =>  __(' Order by post name (post slug).', 'workscout_elementor'),
					'type' =>  __(' Order by post type.', 'workscout_elementor'),
					'date' =>  __(' Order by date.', 'workscout_elementor'),
					'modified' =>  __(' Order by last modified date.', 'workscout_elementor'),
					'parent' =>  __(' Order by post/page parent id.', 'workscout_elementor'),
					'rand' =>  __(' Random order.', 'workscout_elementor'),
					'comment_count' =>  __(' Order by number of commen', 'workscout_elementor'),

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


		$this->add_control(
			'categories',
			[
				'label' => __('Show from categories', 'workscout_elementor'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => [],
				'options' => $this->get_terms('category'),

			]
		);

		$this->add_control(
			'exclude_posts',
			[
				'label' => __('Exclude posts', 'workscout_elementor'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => [],
				'options' => $this->get_posts(),

			]
		);
		$this->add_control(
			'include_posts',
			[
				'label' => __('Include posts', 'workscout_elementor'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => [],
				'options' => $this->get_posts(),

			]
		);

		$this->add_control(
			'limit_words',
			[
				'label' => __('Excerpt length', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 99,
				'step' => 1,
				'default' => 15,
			]
		);

		$this->add_control(
			'after_excerpt',
			[
				'label' => __('Add after excerpt', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('...', 'plugin-domain'),

			]
		);

		$this->add_control(
			'show_view_blog_button',
			[
				'label' => __('Show "View Blog" button', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'workscout_elementor'),
				'label_off' => __('Hide', 'workscout_elementor'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_excerpt',
			[
				'label' => __('Show post excerpt', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'workscout_elementor'),
				'label_off' => __('Hide', 'workscout_elementor'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'show_author',
			[
				'label' => __('Show post author', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'workscout_elementor'),
				'label_off' => __('Hide', 'workscout_elementor'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'show_date',
			[
				'label' => __('Show post date', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'workscout_elementor'),
				'label_off' => __('Hide', 'workscout_elementor'),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'limitby',
			[
				'label' => __('Limit text by', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'words',
				'multiple' => true,
				'options' => [
					'words' =>  __('Words', 'workscout_elementor'),
					'letters' =>  __('Letters. ', 'workscout_elementor'),

				],
			]
		);
		$this->add_control(
			'limit',
			[
				'label' => __('Limit text nunber', 'workscout_elementor'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 250,
				'step' => 1,
				'default' => 20,
			]
		);



		$this->end_controls_section();


		// $this->add_control(
		// 	'with_line',
		// 	[
		// 		'label' => __( 'With Line', 'plugin-domain' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Show', 'workscout_elementor' ),
		// 		'label_off' => __( 'Hide', 'workscout_elementor' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'yes',
		// 	]
		// );




		/* Add the options you'd like to show in this tab here */
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
		$per_page = $settings['per_page'] ? $settings['per_page'] : 3;
		$orderby = $settings['orderby'] ? $settings['orderby'] : 'title';
		$order = $settings['order'] ? $settings['order'] : 'ASC';
		$exclude_posts = $settings['exclude_posts'] ? $settings['exclude_posts'] : 'ASC';
		$categories = $settings['categories'] ? $settings['categories'] : array();
		$limit_words = $settings['limit_words'] ? $settings['limit_words'] : 15;
		$show_author = $settings['show_author'] ? $settings['show_author'] : false;
		$show_date = $settings['show_date'] ? $settings['show_date'] : false;
		$limit = $settings['limit'];
		$limitby = $settings['limitby'];


		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $per_page,
			'orderby' => $orderby,
			'order' => $order,
		);

		if (!empty($exclude_posts)) {
			$exl = is_array($exclude_posts) ? $exclude_posts : array_filter(array_map('trim', explode(',', $exclude_posts)));
			$args['post__not_in'] = $exl;
		}

		if (!empty($include_posts)) {
			$exl = is_array($include_posts) ? $include_posts : array_filter(array_map('trim', explode(',', $include_posts)));
			$args['post__in'] = $exl;
		}


		if (!empty($categories)) {
			$categories         = is_array($categories) ? $categories : array_filter(array_map('trim', explode(',', $categories)));
			$args['category__in'] = $categories;
		}


		$i = 0;

		$wp_query = new \WP_Query($args);
		$author_id = $wp_query->post->post_author; ?>

		<div class="workscout-post-grid-wrapper">
			<div class="row">

				<?php if ($wp_query->have_posts()) { ?>


					<?php while ($wp_query->have_posts()) : $wp_query->the_post();
						$i++;
						$id = $wp_query->post->ID;
						$thumb = get_post_thumbnail_id();
						$img_url = wp_get_attachment_url($thumb, 'workscout-blog-related-post');



					?>
						<div class="col-md-4">
							<article class="recent-post">
								<?php $format = get_post_format();
								$output = '';
								if( false === $format ) $format = 'standard';

								if($format == 'standard' && has_post_thumbnail()){
								$output .= '
								<figure class="recent-post-img">
									<a href="'.get_permalink().'">'.get_the_post_thumbnail($id,'workscout-small-blog').'</a>
									<div class="hover-icon"></div>
								</figure>
								';
								}


								if($format == 'image' && has_post_thumbnail()){
								$output .= '
								<figure class="recent-post-img">
									<a href="'.get_permalink().'">'.get_the_post_thumbnail($id,'workscout-small-blog').'</a>
									<div class="hover-icon"></div>
								</figure>
								';
								}

								if($format == 'gallery') {
								$gallery = get_post_meta($id, '_format_gallery', TRUE);
								preg_match( '/ids=\'(.*?)\'/', $gallery, $matches );
								if ( isset( $matches[1] ) ) {
								// Found the IDs in the shortcode
								$ids = explode( ',', $matches[1] );
								} else {
								// The string is only IDs
								$ids = ! empty( $gallery ) && $gallery != '' ? explode( ',', $gallery ) : array();
								}
								$output .= '<div class="basic-slider royalSlider rsDefault">';
									foreach ($ids as $imageid) {
									$image_link = wp_get_attachment_url( $imageid );
									if ( ! $image_link )
									continue;
									$image = wp_get_attachment_image_src( $imageid, 'large');
									$imageRSthumb = wp_get_attachment_image_src( $imageid, $imagesize );
									$image_title = esc_attr( get_the_title( $imageid ) );
									$output .= '<a href="'.$image[0].'" class="mfp-gallery" title="'.esc_attr($image_title).'"><img class="rsImg" src="'.$imageRSthumb[0].'" data-rsTmb="'.$imageRSthumb[0].'" /></a>';
									}
									$output .= '</div>';
								}

								if($format == 'quote') {
								$output .= '<figure class="post-quote">
									<span class="icon"></span>
									<blockquote>
										'.get_post_meta($id, '_format_quote_content', TRUE).'
										<a href="'.esc_url(get_post_meta($id, '_format_quote_source_url', TRUE)).'"><span>- '.get_post_meta($id, '_format_quote_source_name', TRUE).'</span></a>
									</blockquote>
								</figure>';
								} // eof gallery


								if($format == 'video') {
								$video = get_post_meta($id, '_format_video_embed', true);
								if(!empty($video)) {
								$output .= '<div class="embed">';
									if(wp_oembed_get($video)) { $output .= wp_oembed_get($video); } else { $output .= $video;}
									$output .= '</div>';
									}
								}
								
									// eof gallery 
									$output .= '
                    <section class="from-the-blog-content">
                        <a href="' . get_permalink() . '"><h4>' . get_the_title() . '</h4></a>';

									$output .= ' <div class="meta-tags">';
									if ($show_author) {
										$output .= '<span>' . esc_html__('By', 'workscout_core') . ' <a class="author-link" itemprop="url" rel="author" href="' . get_author_posts_url(get_the_author_meta('ID', $author_id)) . '">' . get_the_author_meta('display_name', $author_id) . '</a></span>' . ' ';
									}
									if ($show_date) {
										$output .= '<span>' . get_the_date() . '</span>';
									}
									$excerpt = get_the_excerpt();

									$output .= '</div>
                            <p>';

									if ($limitby == 'words') {
										$output .= workscout_string_limit_words($excerpt, $limit_words);
									} else {
										$output .= workscout_get_excerpt($excerpt, $limit_words);
									}
									$output .= '</p>
                        <a href="' . get_permalink() . '" class="button">' . esc_html__("Read More", "workscout_elementor") . '</a>
                    </section>';
								echo $output;
					?>

							</article>
							
						</div>
				<?php
					endwhile; // end of the loop. 
				} else {
					//do_action( "woocommerce_shortcode_{$loop_name}_loop_no_results" );
				}
				?>
			</div>
		</div>
		<?php if ($settings['show_view_blog_button'] == 'yes') { ?>
			<div class="col-md-12 centered-content">
				<a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="button centered border margin-top-10"><?php esc_html_e('View Blog', 'workscout_elementor'); ?></a>
			</div>
<?php
		}
		wp_reset_postdata();
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

	protected function get_posts()
	{
		$posts = get_posts(array('numberposts' => 99,));

		$options = ['' => ''];

		if (!empty($posts)) :
			foreach ($posts as $post) {
				$options[$post->ID] = get_the_title($post->ID);
			}
		endif;

		return $options;
	}
}
