<?php

/**
 * WorkScout functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WorkScout
 */

update_option( 'WorkScout_lic_Key', 'activated' );

remove_filter('the_title', 'add_breadcrumb_to_the_title');
include_once(get_template_directory() . '/kirki/kirki.php');

function workscout_kirki_update_url($config)
{

	$config['url_path'] = get_template_directory_uri() . '/kirki/';
	return $config;
}
add_filter('kirki/config', 'workscout_kirki_update_url');

// add_action( 'wp_ajax_nopriv_get_logged_header2', 'ajax_get_header_part' );
// add_action( 'wp_ajax_get_logged_header2', 'ajax_get_header_part' );


function cc_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');



if (!function_exists('workscout_setup')) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */

	// set theme version as option and check if it's changed
	
	add_action('after_setup_theme', 'woocommerce_support');
	function woocommerce_support()
	{
		add_theme_support('woocommerce');
	}

	function workscout_setup()
	{
		/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on WorkScout, use a find and replace
	 * to change 'workscout' to the name of your theme in all the template files.
	 */
		load_theme_textdomain('workscout', get_template_directory() . '/languages');

		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		add_theme_support('resume-manager-templates');
		/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
		add_theme_support('title-tag');

		do_action('purethemes-testimonials');

		/*
	 * Enabling Full Template Support for WP Job Manager
	 */
		add_theme_support('job-manager-templates');

		/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(840, 430, true); //size of thumbs
		add_image_size('workscout-small-thumb', 96, 105, true);     //slider
		add_image_size('workscout-small-blog', 498, 315, true);     //slider
		add_image_size('workscout-resume', 110, 110, true);     //slider

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(array(
			'primary' => esc_html__('Primary Menu', 'workscout'),
			'mobilemenu' => esc_html__('Mobile Menu', 'workscout'),
			'employer' => esc_html__('Employer Dashboard Menu', 'workscout'),
			'candidate' => esc_html__('Candidate Dashboard Menu', 'workscout'),

		));

		/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
		add_theme_support('html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		));

		/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
		add_theme_support('post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		));

		// Set up the WordPress core custom background feature.
		add_theme_support('custom-background', apply_filters('workscout_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		)));
	}
endif; // workscout_setup
add_action('after_setup_theme', 'workscout_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function workscout_content_width()
{
	$GLOBALS['content_width'] = apply_filters('workscout_content_width', 860);
}
add_action('after_setup_theme', 'workscout_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function workscout_widgets_init()
{
	register_sidebar(array(
		'name'          => esc_html__('Sidebar', 'workscout'),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Jobs page sidebar', 'workscout'),
		'id'            => 'sidebar-jobs',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Single job sidebar before', 'workscout'),
		'id'            => 'sidebar-job-before',
		'description'   => 'This widgets will be displayed before the Job Overview on single job page',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Single job sidebar after', 'workscout'),
		'id'            => 'sidebar-job-after',
		'description'   => 'This widgets will be displayed after the Job Overview on single job page',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Single task sidebar ', 'workscout'),
		'id'            => 'sidebar-task',
		'description'   => 'This widgets will be displayed after the Job Overview on single job page',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Single resume sidebar ', 'workscout'),
		'id'            => 'sidebar-resume',
		'description'   => 'This widgets will be displayed after the Job Overview on single job page',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Resumes page sidebar', 'workscout'),
		'id'            => 'sidebar-resumes',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Shop page sidebar', 'workscout'),
		'id'            => 'sidebar-shop',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'name'          => esc_html__('Companies sidebar', 'workscout'),
		'id'            => 'sidebar-companies',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footer1',
		'name' => esc_html__('Footer 1st Column', 'workscout'),
		'description' => esc_html__('1st column for widgets in Footer', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footer2',
		'name' => esc_html__('Footer 2nd Column', 'workscout'),
		'description' => esc_html__('2nd column for widgets in Footer', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footer3',
		'name' => esc_html__('Footer 3rd Column', 'workscout'),
		'description' => esc_html__('3rd column for widgets in Footer', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footer4',
		'name' => esc_html__('Footer 4th Column', 'workscout'),
		'description' => esc_html__('4th column for widgets in Footer', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));
	register_sidebar(array(
		'id' => 'footer5',
		'name' => esc_html__('Footer 5th Column', 'workscout'),
		'description' => esc_html__('5th column for widgets in Footer', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));


	register_sidebar(array(
		'id' => 'mobilemenu',
		'name' => esc_html__('Mobile Menu widget', 'workscout'),
		'description' => esc_html__('Mobilel Menu area', 'workscout'),
		'before_widget' => '<aside id="%1$s" class="mobile-menu-widget widget %2$s">',
		'after_widget' => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	));
	$sidebars =  Kirki::get_option('workscout', 'incr_sidebars', '');
	if ($sidebars) :


		if (!empty($sidebars)) :
			foreach ($sidebars as $pp_sidebar) {

				register_sidebar(array(
					'name' => esc_html($pp_sidebar["sidebar_name"]),
					'id' => esc_attr($pp_sidebar["id"]),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h4 class="widget-title">',
					'after_title'   => '</h4>',
				));
			}
		endif;
	endif;
}
add_action('widgets_init', 'workscout_widgets_init');


add_action('admin_enqueue_scripts', 'workscout_admin_scripts');
function workscout_admin_scripts($hook)
{

	$my_theme = wp_get_theme();
	$ver =  $my_theme->get('Version');

	wp_enqueue_style('workscout-global-admin', get_template_directory_uri() . '/css/admin-global.css');

	if ($hook == 'edit-tags.php' || $hook == 'term.php' || $hook == 'toplevel_page_workscout_settings') {

		wp_enqueue_style('workscout-admin', get_template_directory_uri() . '/css/admin.css');
		wp_enqueue_style('workscout-icons', get_template_directory_uri() . '/css/font-awesome.css');
		wp_enqueue_style('workscout-material-icons', get_template_directory_uri() . '/css/material-icons.css');
		wp_enqueue_style('workscout-all-icons', get_template_directory_uri() . '/css/icons.css');
		if (!get_option('workscout_linear_icons_status') != 'hide') {
		wp_enqueue_style('workscout-line-icons', get_template_directory_uri() . '/css/line-awesome.css');
		}
		wp_enqueue_script('workscout-icon-selector', get_template_directory_uri() . '/js/iconselector.min.js', array('jquery'), $ver, true);
	}

	// $api_key = Kirki::get_option( 'workscout','pp_maps_browser_api', '');
	// $geocode = Kirki::get_option( 'workscout','pp_maps_geocode', 0);
	// if(!empty($api_key) && $geocode == 1){
	// 	wp_enqueue_script( 'google-maps', 'https://maps.google.com/maps/api/js?key='.$api_key.'&libraries=places&v=3.30' );
	// 	wp_enqueue_script( 'workscout-wpjm-geo', get_template_directory_uri() . '/js/admin.workscout.maps.min.js', array('jquery'), $ver, true );
	// }

}

/**
 * Enqueue scripts and styles.
 */

 
function workscout_scripts()
{

	$my_theme = wp_get_theme();
	$ver =  $my_theme->get('Version');

	wp_register_style('workscout-base', get_template_directory_uri() . '/css/base.min.css', array(), $ver);
	wp_register_style('workscout-v2', get_template_directory_uri() . '/css/v2style.css', array(), $ver);
	wp_register_style('workscout-responsive', get_template_directory_uri() . '/css/responsive.min.css', array(), $ver);
	wp_register_style('workscout-font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), $ver);
	if (get_option('workscout_linear_icons_status') != 'hide') {
		
		wp_enqueue_style('workscout-line-icons', get_template_directory_uri() . '/css/line-awesome.css');
	}
	wp_enqueue_style('workscout-all-icons', get_template_directory_uri() . '/css/icons.css');


	wp_enqueue_style('workscout-style', get_stylesheet_uri(), array('workscout-base', 'workscout-responsive', 'workscout-font-awesome'), $ver);
	wp_enqueue_style('workscout-woocommerce', get_template_directory_uri() . '/css/woocommerce.min.css', array(), $ver);
	wp_enqueue_style('workscout-v2');

	if (class_exists('woocommerce')) {
		wp_dequeue_style('select2');

		wp_deregister_style('select2');
		//wp_dequeue_script( 'select2');
		//  wp_deregister_script('select2');
	}

	//remove default WPJM styles
	wp_dequeue_style('wp-job-manager-frontend');
	wp_dequeue_style('wp-job-manager-job-listings');
	wp_dequeue_style('wp-job-manager-resume-frontend');
	wp_dequeue_style('chosen');
	wp_dequeue_style('wp-subscribe');
	wp_dequeue_style('wp-subscribe-css');

	wp_dequeue_style('wp-job-manager-bookmarks-frontend');
	wp_dequeue_style('wp-job-manager-applications-frontend');


	wp_deregister_script('wp-job-manager-bookmarks-bookmark-js');

	// add recaptcha TODO is it needed
	//	wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );

	//TODO - add transient here
	if (defined('JOB_MANAGER_VERSION')) {

		global $wpdb;
		$ajax_url  = WP_Job_Manager_Ajax::get_endpoint();
		
		$min = workscout_get_salary_min();
		$max = workscout_get_salary_max();

		$ratemin = workscout_get_rate_min();
		$ratemax =workscout_get_rate_max();

		wp_dequeue_script('wp-job-manager-ajax-filters');
		wp_deregister_script('wp-job-manager-ajax-filters');

		wp_register_script('workscout-wp-job-manager-ajax-filters', get_template_directory_uri() . '/js/workscout-ajax-filters.min.js', array('jquery', 'jquery-deserialize'), $ver, true);

		if (function_exists('get_workscout_currency_symbol')) {
			$currency = get_workscout_currency_symbol();
		} else {
			$currency = '$';
		}
		wp_localize_script('workscout-wp-job-manager-ajax-filters', 'job_manager_ajax_filters', array(
			'ajax_url'                	=> $ajax_url,
			'is_rtl'                  	=> is_rtl() ? 1 : 0,
			'lang'                    	=> defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '', // WPML workaround until this is standardized
			'i18n_load_prev_listings' 	=> esc_html__('Load previous listings', 'workscout'),
			'salary_min'		      	=> $min,
			'salary_max'		      	=> $max,
			'rate_min'		      		=> $ratemin,
			'rate_max'		      		=> $ratemax,
			'single_job_text'			=> esc_html__('job offer', 'workscout'),
			'plural_job_text'			=> esc_html__('job offers', 'workscout'),
			'currency'		      		=> $currency,
			'currency_postion'     		=> get_option('workscout_currency_position', 'before'),


		));
		$ajax_url = admin_url('admin-ajax.php', 'relative');


		// $resume_ratemin = floor($wpdb->get_var("
	    //         SELECT min(meta_value + 0)
	    //         FROM $wpdb->posts AS p
	    //     	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
	    //         WHERE meta_key IN ('_rate_min')
	    //         AND meta_value != ''  AND post_status = 'publish' AND post_type = 'resume'
	    //    "));

		// $resume_ratemax = ceil($wpdb->get_var("
		//     SELECT max(meta_value + 0)
		//     FROM $wpdb->posts AS p
        // 	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
		//     WHERE meta_key IN ('_rate_min')  AND post_status = 'publish' AND post_type = 'resume'
		// "));

		wp_dequeue_script('wp-resume-manager-ajax-filters');
		wp_deregister_script('wp-resume-manager-ajax-filters');

		wp_register_script('workscout-wp-resume-manager-ajax-filters', get_template_directory_uri() . '/js/workscout-resumes-ajax-filters.min.js', array('jquery', 'jquery-deserialize'), $ver, true);

		wp_localize_script('workscout-wp-resume-manager-ajax-filters', 'resume_manager_ajax_filters', array(
			'ajax_url' => $ajax_url,
			// 'rate_min'		      		=> $resume_ratemin,
			// 'rate_max'		      		=> $resume_ratemax,
			'currency'		      		=> $currency,
			'showing_all'		      	=> __('Showing all resumes', 'workscout')
		));
	}

	//wp_register_script( 'jquery-touch-punch-ws', get_template_directory_uri() . '/js/jquery.ui.touch-punch.js', array( 'jquery' ), $ver, true );

	wp_enqueue_script('jquery-ui-autocomplete');

	wp_enqueue_script('workscout-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.min.js', array(), '20130115', true);

	//wp_enqueue_script('jquery-ui-slider');
	//wp_enqueue_script( 'jquery-touch-punch-ws' );
	wp_dequeue_style('jquery-ui-css');

	wp_enqueue_script('slick-min', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), $ver, true);

	wp_enqueue_script('workscout-hoverIntent', get_template_directory_uri() . '/js/hoverIntent.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-counterup', get_template_directory_uri() . '/js/jquery.counterup.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array('jquery'), $ver, true);


	wp_enqueue_script('workscout-gmaps', get_template_directory_uri() . '/js/jquery.gmaps.min.js', array('jquery'), $ver, true);


	//wp_enqueue_script( 'mmenu-min', get_template_directory_uri() . '/js/mmenu.min.js', array('jquery'), $ver, true );
	wp_enqueue_script('workscout-isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array(), $ver, true);
	wp_enqueue_script('workscout-magnific', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-superfish', get_template_directory_uri() . '/js/jquery.superfish.min.js', array('jquery'), $ver, true);

	wp_register_script('slick-min', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-stacktable', get_template_directory_uri() . '/js/stacktable.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-waypoints', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'), $ver, true);
	wp_enqueue_script('workscout-headroom', get_template_directory_uri() . '/js/headroom.min.js', array('jquery'), $ver, true);

	wp_register_script('dropzone', get_template_directory_uri() . '/js/dropzone.js', array('jquery'), $ver);
	if (!class_exists('woocommerce')) {
		wp_enqueue_script('select2', get_template_directory_uri() . '/js/select2.full.js', array('jquery'), $ver, true);
	}
	wp_enqueue_script('dropzone');



	wp_enqueue_script('workscout-custom', get_template_directory_uri() . '/js/custom.min.js', array('jquery'), time(), true);
	$ajax_url = admin_url('admin-ajax.php', 'relative');

	wp_localize_script(
		'workscout-custom',
		'ws',
		array(
			'logo'					=> Kirki::get_option('workscout', 'pp_logo_upload', ''),
			'retinalogo'			=> Kirki::get_option('workscout', 'pp_retina_logo_upload', ''),
			'transparentlogo'		=> Kirki::get_option('workscout', 'pp_transparent_logo_upload', ''),
			'transparentretinalogo'	=> Kirki::get_option('workscout', 'pp_transparent_retina_logo_upload', ''),
			'ajaxurl' 				=> $ajax_url,
			'theme_color' 			=> Kirki::get_option('workscout', 'pp_main_color'),
			'woo_account_page'		=> get_permalink(get_option('woocommerce_myaccount_page_id')),
			'theme_url'				=> get_template_directory_uri(),
			'header_breakpoint'		=> Kirki::get_option('workscout', 'pp_alt_menu_width', '1290'),
			'no_results_text'		=> __('No results match', 'workscout'),
			'menu_back'     			=> esc_html__('Back', 'workscout'),
			'i18n_confirm_delete'     			=> esc_html__('Are you sure?', 'workscout'),
			'single_task_text'			=> esc_html__('task', 'workscout'),
			'plural_task_text'			=> esc_html__('tasks', 'workscout'),
		)
	);


	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'workscout_scripts');

add_action('wp_enqueue_scripts', 'workscout_remove_select2', PHP_INT_MAX);
function workscout_remove_select2()
{
	wp_dequeue_style('select2');
	wp_deregister_style('select2');
	wp_dequeue_style('wc-paid-listings-packages');
	wp_deregister_style('wc-paid-listings-packages');
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Custom registration form
 */

//require get_template_directory() . '/inc/registration.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';


/**
 * Load Job Menager related stuff
 */
require get_template_directory() . '/inc/wp-job-manager.php';

/**
 * Load Job Menager related stuff
 */
require get_template_directory() . '/inc/wp-job-manager-maps.php';

/**
 * Load shortcodes
 */
//require get_template_directory() . '/inc/shortcodes.php';

/**
 * Load ptshortcodes
 */
require get_template_directory() . '/inc/ptshortcodes.php';

/**
 * Load woocommerce 
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Load TGMPA.
 */
require get_template_directory() . '/inc/tgmpa.php';

/**
 * Load widgets.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Load activation screen.
 */
//require get_template_directory() . '/inc/activation.php';

/**
 * Load activation screen.
 */
require get_template_directory() . '/inc/wp-job-manager-colors-types.php';


require get_template_directory() . '/inc/cmb2-meta-boxes.php';
if (!class_exists("b372b0Base")) {
	require_once get_template_directory() . '/inc/b372b0Base.php';
}
require get_template_directory() . '/inc/licenser.php';


/**
 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
 */
add_action('vc_before_init', 'workscout_vcSetAsTheme');
function workscout_vcSetAsTheme()
{
	vc_set_as_theme($disable_updater = true);
	if (defined('WPB_VC_VERSION')) {
		$_COOKIE['vchideactivationmsg_vc11'] = WPB_VC_VERSION;
	}
}

function workscout_remove_frontend_links()
{
	vc_disable_frontend(); // this will disable frontend editor
}
//add_action( 'vc_after_init', 'workscout_remove_frontend_links' );

/**
 * Load Visual Composer compatibility file.
 */
define('REV_SLIDER_AS_THEME', true);

// 

/**
 * Load shortcodes.
 */
require get_template_directory() . '/envato_setup/envato_setup.php';


// Please don't forgot to change filters tag.
// It must start from your theme's name.
add_filter('workscout_theme_setup_wizard_username', 'workscout_set_theme_setup_wizard_username', 10);
if (!function_exists('workscout_set_theme_setup_wizard_username')) {
	function workscout_set_theme_setup_wizard_username($username)
	{
		return 'purethemes';
	}
}

add_filter('workscout_theme_setup_wizard_oauth_script', 'workscout_set_theme_setup_wizard_oauth_script', 10);
if (!function_exists('workscout_set_theme_setup_wizard_oauth_script')) {
	function workscout_set_theme_setup_wizard_oauth_script($oauth_url)
	{
		return 'http://purethemes.net/envato/api/server-script.php';
	}
}

add_filter('job_manager_mime_types', 'bk_add_more_types', 10, 2);
function bk_add_more_types($mime_types, $field)
{
	if ('company_logo' !== $field) {
		$mime_types['xls'] = 'application/vnd.ms-excel';
		$mime_types['xlsx'] = 'application/octet-stream';
	}
	return $mime_types;
}
//add_filter('job_manager_enhanced_select_enabled','__return_false');


add_action('after_switch_theme', 'workscout_setup_options');

function workscout_setup_options()
{
	$activation_date = get_option('workscout_activation_date');
	if (!$activation_date) {
		update_option('workscout_activation_date', time());
	}
}


function get_job_application_avatar($application_id, $size = 42)
{
	$email     = get_job_application_email($application_id);
	$resume_id = get_job_application_resume_id($application_id);

	if ($resume_id && 'publish' === get_post_status($resume_id) && function_exists('get_the_candidate_photo')) {
		if (get_the_candidate_photo($resume_id)) {
			return '<img src="' . esc_attr(get_the_candidate_photo($resume_id)) . '" height="' . esc_attr($size) . '" />';
		} else {
			return get_avatar($email, $size);
		}
	}

	return $email ? get_avatar($email, $size) : '';
}
add_filter('register_post_type_job_listing', function ($args) {
	$args['show_in_nav_menus'] = true;
	return $args;
});
add_filter('register_post_type_resume', function ($args) {
	$args['show_in_nav_menus'] = true;
	return $args;
});

add_theme_support('mas-wp-job-manager-company-archive');

add_filter('mas_company_taxonomies_list', 'change_strengh');

function change_strengh($args)
{
	$args['company_strength']['singular'] = __('Company Size', 'workscout');
	$args['company_strength']['plural'] = __('Company Size', 'workscout');
	$args['company_strength']['slug'] = __('company-size', 'workscout');
	return $args;
}

add_filter('submit_company_form_fields', 'change_strengh_submit');

function change_strengh_submit($args)
{
	$args['company_fields']['company_strength']['label'] = __('Company Size', 'workscout');
	return $args;
}
function ws_register_elementor_locations($elementor_theme_manager)
{

	$elementor_theme_manager->register_location('header');
	$elementor_theme_manager->register_location('footer');
	// $elementor_theme_manager->register_location( 'single' );
	// $elementor_theme_manager->register_location( 'archive' );

}
add_action('elementor/theme/register_locations', 'ws_register_elementor_locations');

// add_filter('submit_company_form_fields', 'change_industry_multi');
// function change_industry_multi($fields)
// {
// 	$fields['company_fields']['company_category']['type'] = 'term-multiselect';
// 	return $fields;
// }

// // add_filter('mas_company_taxonomies_list', 'change_mas_company_taxonomies_list');
// // function change_mas_company_taxonomies_list($fields)
// // {
// // 	unset($fields['company_revenue']);
// // 	unset($fields['company_average_salary']);
// // 	return $fields;
// // }
// add_filter('submit_resume_form_fields', 'remove_submit_resume_form_fields',99);
// function remove_submit_resume_form_fields($fields)
// {
// 	unset($fields['resume_fields']['gallery']);
// 	unset($fields['resume_fields']['header_image']);
// 	unset($fields['resume_fields']['candidate_photo']);
// 	return $fields;
// }

// // Add your own function to filter the fields
//  add_filter('submit_resume_form_fields', 'change_category_submit_resume_form_fields');

// // // This is your function which takes the fields, modifies them, and returns them
// function change_category_submit_resume_form_fields($fields)
// {

// 	// Here we target one of the job fields (candidate name) and change it's label
// 	$fields['resume_fields']['resume_skills']['type'] = "term-multiselect";
// 	$fields['resume_fields']['resume_skills']['taxonomy'] = "resume_skill";
// 	$fields['resume_fields']['resume_skills']['placeholder'] = "List of relevant skills";

// 	// And return the modified fields
// 	return $fields;
// }
