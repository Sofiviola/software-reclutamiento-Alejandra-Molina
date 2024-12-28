<?php
/**
 * Widgets class.
 *
 * @category   Class
 * @package    ElementorWorkscout
 * @subpackage WordPress
 * @author     Purethemes.net
 * @copyright  Purethemes.net
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorWorkscout;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || die();

/**
 * Class Plugin
 *
 * Main Plugin class
 *
 * @since 1.0.0
 */
class Widgets {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers the widget scripts.
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		//wp_register_script( 'workscout_elementor', plugins_url( '/assets/js/elementorworkscout.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	}

	public function backend_preview_scripts() {
		wp_enqueue_script( 'elementor-preview-workscout', plugins_url( '/assets/js/elementor_preview_workscout.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files() {

		require_once 'widgets/class-headline.php';
		require_once 'widgets/class-tax-grid.php';
		require_once 'widgets/class-tax-grid-resume.php';
		require_once 'widgets/class-tax-grid-task.php';
		require_once 'widgets/class-iconbox.php';
		require_once 'widgets/class-spotlight-jobs.php';
		require_once 'widgets/class-spotlight-resumes.php';
		require_once 'widgets/class-spotlight-tasks.php'; //NEW
		require_once 'widgets/class-companies.php';
		require_once 'widgets/class-basic-search.php';
		require_once 'widgets/class-home-search.php';
		require_once 'widgets/class-home-search-boxed.php';
		require_once 'widgets/class-home-search-resumes-boxed.php';
		require_once 'widgets/class-home-search-tasks-boxed.php';
		require_once 'widgets/class-home-search-resumes.php';
		require_once 'widgets/class-home-search-tasks.php';
		//require_once 'widgets/class-home-search-slider.php';
		require_once 'widgets/class-home-search-wide.php';
		require_once 'widgets/class-home-search-resumes-wide.php';
		require_once 'widgets/class-home-search-tasks-wide.php';
		require_once 'widgets/class-jobs.php';
		require_once 'widgets/class-resumes.php';
		require_once 'widgets/class-tasks.php'; //NEW
		require_once 'widgets/class-testimonials.php';
		require_once 'widgets/class-flip-banner.php';
		require_once 'widgets/class-post-grid.php';
		require_once 'widgets/class-imagebox.php';
		require_once 'widgets/class-freelancerbox.php';
		require_once 'widgets/class-counters.php';
		require_once 'widgets/class-pricing-table.php';
		require_once 'widgets/class-pricing-table-woo.php';
		require_once 'widgets/class-logo-slider.php';
		//TODO
		require_once 'widgets/class-tax-list.php';
		// testimonials 2nd layout




// resumes list
// resumes spotlight
// icon box 2?
// 



		// require_once 'widgets/class-tax-carousel.php';
		
		// require_once 'widgets/class-woo-tax-grid.php';
		
		// require_once 'widgets/class-tax-gallery.php';
		
		
		
		// require_once 'widgets/class-listings-carousel.php';
		// require_once 'widgets/class-listings.php';
		
		
		;
		// //require_once 'widgets/class-woo-products-grid.php';
		// require_once 'widgets/class-woo-products-carousel.php';
		// require_once 'widgets/class-dokan-vendors-carousel.php';
		// require_once 'widgets/class-dokan-vendors-grid.php';
		
		// //require_once 'widgets/class-home-banner.php';
		// require_once 'widgets/class-home-search-slider.php';
		
		// 
		// require_once 'widgets/class-address-box.php';
		// require_once 'widgets/class-alertbox.php';
		// home search boxes


		
		//require_once 'widgets/class-widget2.php';
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {
		// It's now safe to include Widgets files.
		$this->include_widgets_files();
			
//   'vc_map/box_resume_categories_full.php',


//   'vc_map/spotlight_jobs.php',
//   'vc_map/spotlight_resumes.php',
//   'vc_map/testimonials_wide.php',
//   'vc_map/testimonials_carousel.php',
//   'vc_map/actionbox.php',
//   'vc_map/actionbox_centered.php',
//   'vc_map/carousel.php',
//   'vc_map/recent_blog.php',
//   'vc_map/box_job_categories_full.php',
//   'vc_map/box.php',
//   'vc_map/infobanner.php',
//   'vc_map/search_jobs.php',
//   'vc_map/map.php',
//   'vc_map/counterbox.php',
//   'vc_map/pricing_table.php',
//   'vc_map/jobs.php',
//   'vc_map/pricing_woo_tables.php',
//   'vc_map/flip_banner.php',
//   'vc_map/imagebox.php',
		// Register the plugin widget classes.

		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\TaxonomyGrid());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\ResumeTaxonomyGrid());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\TaskTaxonomyGrid());
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Headline() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\IconBox() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\SpotlightJobs() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\SpotlightTasks() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Companies() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\SpotlightResumes() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Jobs() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Resumes() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Tasks() );
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Testimonials());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\FlipBanner());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\PostGrid());
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\ImageBox() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\FreelancerBox() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Counters() );
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\PricingTable());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\PricingTableWoo());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\LogoSlider());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\TaxonomyList());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\BasicSearchForm());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearch());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchBoxed());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchResumes());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchResumesBoxed());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchTasks());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchTasksBoxed());
		
		
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchWide());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchResumesWide());
		\Elementor\Plugin::instance()->widgets_manager->register(new Widgets\HomeSearchTasksWide());
	

	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'elementor/elements/categories_registered', array( $this, 'create_custom_categories') );

		// Register the widget scripts.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts' ) );

		add_action('elementor/preview/enqueue_styles', array($this, 'backend_preview_scripts'), 10);
        
        //add_action('elementor/frontend/after_register_scripts', array($this, 'cocobasic_frontend_enqueue_script'));

		// Register the widgets.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		
	}


	function create_custom_categories( $elements_manager ) {

	    $elements_manager->add_category(
	        'workscout',
	        [
	         'title' => __( 'Workscout', 'workscout_elementor' ),
	         'icon' => 'fa fa-clipboard',
	        ]
	    );
	}
}

// Instantiate the Widgets class.
Widgets::instance();