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

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class Listings extends Widget_Base {

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
		return 'workscout-listings';
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
		return __( 'Listings', 'workscout_elementor' );
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
		return 'fa fa-map-marked-alt';
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

//             'layout'        =>'standard',


//             'relation'    => 'OR',
//         
//             '_property_type' => '',
//             '_offer_type'   => '',
//             'featured'      => '',
//             'fullwidth'     => '',
//             'style'         => '',
//             'autoplay'      => '',
//             'autoplayspeed'      => '3000',
//             'from_vs'       => 'no',


	$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query Settings', 'workscout_elementor' ),
			)
		);

		$this->add_control(
			'limit',
			[
				'label' => __( 'Listings to display', 'workscout_elementor' ),
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
				'label' => __( 'Order by', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date' =>  __( ' Order by date.', 'workscout_elementor' ),
					'rand' =>  __( ' Random order.', 'workscout_elementor' ),
					'featured' =>  __( 'Featured', 'workscout_elementor' ),
					'highest' =>  __( 'Best rated', 'workscout_elementor' ),
					'views' =>  __( 'Most views', 'workscout_elementor' ),
					'reviewed' =>  __( 'Most reviews', 'workscout_elementor' ),
					'ID' =>  __(  'Order by post id. ', 'workscout_elementor' ),
					'author'=>  __(  'Order by author.', 'workscout_elementor' ),
					'title' =>  __(  'Order by title.', 'workscout_elementor' ),
					'name' =>  __( ' Order by post name (post slug).', 'workscout_elementor' ),
					'modified' =>  __( ' Order by last modified date.', 'workscout_elementor' ),
					'parent' =>  __( ' Order by post/page parent id.', 'workscout_elementor' ),
					'comment_count' =>  __( ' Order by number of commen', 'workscout_elementor' ),
					
				],
			]
		);
		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' =>  __( 'Descending', 'workscout_elementor' ),
					'ASC' =>  __(  'Ascending. ', 'workscout_elementor' ),
				
					
				],
			]
		);
		
		$this->add_control(
			'_listing_type',
			[
				'label' => __( 'Show only Listing Types', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'default' => '',
				'options' => [
					'' =>  __( 'All', 'workscout_elementor' ),
					'service' =>  __( 'Service', 'workscout_elementor' ),
					'rental' =>  __(  'Rentals. ', 'workscout_elementor' ),
					'event' =>  __(  'Events. ', 'workscout_elementor' ),
					'classifieds' => __('Classifieds', 'workscout_elementor'),
					
				],
			]
		);


			$this->add_control(
				'tax-listing_category',
				[
					'label' => __( 'Show only from listing categories', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('listing_category'),
					
				]
			);				

			$this->add_control(
				'tax-service_category',
				[
					'label' => __( 'Show only from service categories', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('service_category'),
					
				]
			);	

			$this->add_control(
				'tax-rental_category',
				[
					'label' => __( 'Show only from rental categories', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('rental_category'),
					
				]
			);				

			$this->add_control(
				'tax-event_category',
				[
					'label' => __( 'Show only from events categories', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('event_category'),
					
				]
			);


		$this->add_control(
			'tax-classifieds_category',
			[
				'label' => __('Show only from classifieds categories', 'workscout_elementor'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => [],
				'options' => $this->get_terms('classifieds_category'),

			]
		);	

			
			

			$this->add_control(
				'feature',
				[
					'label' => __( 'Show only listings with features', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('listing_feature'),
				]
			);

			$this->add_control(
				'region',
				[
					'label' => __( 'Show only listings from region', 'workscout_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('region'),
				]
			);	


		$this->add_control(
			'keyword',
			array(
				'label'   => __( 'Keyword search', 'workscout_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$this->add_control(
			'location',
			array(
				'label'   => __( 'Location search', 'workscout_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$this->add_control( 
			'search_radius',
			array(
				'label'   => __( 'Radius distance search', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 500,
				'step' => 1,
				'default' => 50
			)
		);

		$this->add_control(
				'featured',
				[
					'label' => __( 'Show only featured listings', 'workscout_elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'your-plugin' ),
					'label_off' => __( 'Hide', 'your-plugin' ),
					'return_value' => 'yes',
					'default' => '',
				]
			);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Layout Settings', 'workscout_elementor' ),
			)
		);
	

			
		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'standard' =>  __( 'Standard', 'workscout_elementor' ),
					'compact' =>  __( 'Compact', 'workscout_elementor' ),
					'grid' =>  __( 'Grid. ', 'workscout_elementor' ),
				
					
				],
			]
		);
		
		$this->add_control(
			'grid_columns',
			[
				'label' => __( 'Grid columns', 'workscout_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' =>  __( '1', 'workscout_elementor' ),
					'2' =>  __( '2', 'workscout_elementor' ),
					'3' =>  __( '3', 'workscout_elementor' ),
					
				
					
				],
			]
		);

		$this->add_control(
			'list_top_buttons',
			[
				'label' => __( 'Show Elements', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'layout'  => __( 'Layout switcher', 'plugin-domain' ),
					'filters' => __( 'Filters', 'plugin-domain' ),
					'order' => __( 'Orderby', 'plugin-domain' ),
					'radius' => __( 'Radius', 'plugin-domain' ),
				],
				'default' => [ 'order' ],
			]
		);


		$this->add_control(
				'show_pagination',
				[
					'label' => __( 'Show pagination', 'workscout_elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'workscout_elementor' ),
					'label_off' => __( 'Hide', 'workscout_elementor' ),
					'return_value' => 'yes',
					'default' => 'yes',
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
	protected function render() {
		// 'limit'         =>'6',
  //           'layout'        =>'standard',
  //           'orderby'       => 'date',
  //           'order'         => 'DESC',
  //           'tax-listing_category'    => '',
  //           'tax-service_category'    => '',
  //           'tax-rental_category'    => '',
  //           'tax-event_category'    => '',
  //           'relation'    => 'OR',
  //           'exclude_posts' => '',
  //           'include_posts' => '',
  //           'feature'       => '',
  //           'region'        => '',
  //           '_property_type' => '',
  //           '_offer_type'   => '',
  //           'featured'      => '',
  //           'fullwidth'     => '',
  //           'style'         => '',
  //           'autoplay'      => '',
  //           'autoplayspeed'      => '3000',
  //           'from_vs'       => 'no',
			$settings = $this->get_settings_for_display();
		 	$limit = $settings['limit'] ? $settings['limit'] : 6;
		 	$orderby = $settings['orderby'] ? $settings['orderby'] : 'newest';
		 	$order = $settings['order'] ? $settings['order'] : 'ASC';
			$featured = $settings['featured'] ? true : null;
		// $exclude_posts = $settings['exclude_posts'] ? $settings['exclude_posts'] : array();
		// $include_posts = $settings['include_posts'] ? $settings['include_posts'] : array();
			$style = $settings['layout'];
			
			
			$ajax_browsing = get_option('workscout_ajax_browsing');

       
        $args = array(
          
            'posts_per_page' 	=> $limit,
            'orderby' 			=> $orderby,
            'order' 			=> $order,
         

			'keyword'   	=> $settings['keyword'],
			'location'   => $settings['location'],
			 'search_radius'   	=> $settings['search_radius'],
			// 'radius_type'   	=> $settings['radius_type'],
			 'workscout_orderby'   	=> $orderby,
        );


        if(isset($settings['tax-listing_category']) && !empty($settings['tax-listing_category']) ){
           $args['tax-listing_category'] =  $settings['tax-listing_category'][0];
        }
        if(isset($settings['tax-service_category']) && !empty($settings['tax-service_category']) ){
           $args['tax-service_category'] =  $settings['tax-service_category'][0];
        }
        if(isset($settings['tax-rental_category']) && !empty($settings['tax-rental_category']) ){
           $args['tax-rental_category'] =  $settings['tax-rental_category'][0];
        } 
        if(isset($settings['tax-event_category']) && !empty($settings['tax-event_category']) ){
           $args['tax-event_category'] =  $settings['tax-event_category'][0];
        }
        if(isset($settings['tax-classifieds_category']) && !empty($settings['tax-classifieds_category']) ){
           $args['tax-classifieds_category'] =  $settings['tax-classifieds_category'][0];
        }
 		if(isset($settings['feature']) && !empty($settings['feature']) ){
           $args['tax-listing_feature'] =  $settings['feature'][0];
        }
        if(isset($settings['region']) && !empty($settings['region']) ){
           $args['tax-region'] =  $settings['region'][0];
        }

        if(isset($settings['_listing_type']) && !empty($settings['_listing_type']) ){
           $args['_listing_type'] =  $settings['_listing_type'];
        }

 		if(isset($settings['list_top_buttons']) && !empty($settings['list_top_buttons']) ){
        	$list_top_buttons = implode( '|', $settings['list_top_buttons']);
 		} else {
 			$list_top_buttons = '';
 		}
        $wp_query = new \WP_Query( $args );
        if(!class_exists('Workscout_Core_Template_Loader')) {
            return;
        }
        $template_loader = new \Workscout_Core_Template_Loader;

        ob_start();
       
		
		// Get listings query
		$ordering_args = \Workscout_Core_Listing::get_listings_ordering_args( $orderby, $order );
 
		// if ( ! is_null( $featured ) ) {

		// 	$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;

		// }

	
		$args['featured'] = $featured;
		$workscout_core_query = \Workscout_Core_Listing::get_real_listings( apply_filters( 'workscout_core_output_defaults_args', $args ));
		
		?>

			<div class="row margin-bottom-25">

				<?php do_action( 'workscout_before_archive', $style, $list_top_buttons ); ?>
			</div>
		<?php
		
		if ( $workscout_core_query->have_posts() ) { 
			$style_data = array(
				'style' 		=> $style, 
				// 'class' 		=> $custom_class, 
				// 'in_rows' 		=> $in_rows, 
				'grid_columns' 	=> $settings['grid_columns'],
				'per_page' 		=> $limit,
				'max_num_pages'	=> $workscout_core_query->max_num_pages, 
				'counter'		=> $workscout_core_query->found_posts,
				'ajax_browsing' => $ajax_browsing,
				);
			
			$search_data = array_merge($style_data,$args);
			$template_loader->set_template_data( $search_data )->get_template_part( 'listings-start' ); 
			
			// Loop through listings
			while ( $workscout_core_query->have_posts() ) {
				// Setup listing data
				$workscout_core_query->the_post();
				
				$template_loader->set_template_data( $style_data )->get_template_part( 'content-listing',$style ); 	
			
			}
			
			if($style_data['ajax_browsing'] == 'on'){ ?>
			</div>
			<?php if($settings['show_pagination'] == 'yes') : ?>
				<div class="pagination-container margin-top-20 margin-bottom-20 ajax-search">
					<?php
					echo workscout_core_ajax_pagination( $workscout_core_query->max_num_pages, 1 ); ?>
				</div>
			<?php endif; ?>
			<?php } else {
				$template_loader->set_template_data( $style_data )->get_template_part( 'listings-end' ); 
			}
		} else {

			$template_loader->get_template_part( 'archive/no-found' ); 
		}

		wp_reset_query();
	
        echo ob_get_clean();
	
	
		
	}


		protected function get_terms($taxonomy) {
			$taxonomies = get_terms( array( 'taxonomy' =>$taxonomy,'hide_empty' => false) );

			$options = [ '' => '' ];
			
			if ( !empty($taxonomies) ) :
				foreach ( $taxonomies as $taxonomy ) {
					$options[ $taxonomy->slug ] = $taxonomy->name;
				}
			endif;

			return $options;
		}

		protected function get_posts() {
			$posts = get_posts( 
				array( 
					'numberposts' => -1, 
					'post_type' => 'listing', 
					'suppress_filters' =>true
				) );

			$options = [ '' => '' ];
			
			if ( !empty($posts) ) :
				foreach ( $posts as $post ) {
					$options[ $post->ID ] = get_the_title($post->ID);
				}
			endif;

			return $options;
		}
	
}