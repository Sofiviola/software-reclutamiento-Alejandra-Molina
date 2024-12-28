<?php

/**
 * Plugin Name: WorkScout Freelancer For WP Job Manager
 * Description: This plugin adds freelancer functionality for WorkScout WP Job Manager
 * Version: 1.0.10
 * Author: Purethemes
 * Author URI: https://purethemes.net/
 *
 * Text Domain: workscout-freelancer
 * Domain Path: /languages/
 *
 * @package WorkScout Freelancer
 * @category Core
 * @author PureThemes
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WorkScout_Freelancer class.
 */
class WorkScout_Freelancer
{
    const JOB_MANAGER_CORE_MIN_VERSION = '1.31.1';

    public $post_types;
    public $writepanels;
    public $bid;
    public $task;
    public $forms;
    public $reviews;
    public $freelancer_project;

    /**
     * __construct function.
     */
    public function __construct()
    {
        // Define constants.
        define('WORKSCOUT_FREELANCER_VERSION', '1.0');
        define('WORKSCOUT_FREELANCER_PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
        define('WORKSCOUT_FREELANCER_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));

        // Includes.
        
        
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-cpt.php';

        // Init class needed for activation.
        $this->post_types = WorkScout_Freelancer_CPT::instance();

  
        register_activation_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'flush_rewrite_rules', 15);
        register_activation_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), array($this, 'package_terms'), 15);

        add_action('plugins_loaded', array($this, 'init_plugin'), 13);
        add_action('plugins_loaded', array($this, 'admin'), 14);
        add_action('admin_notices', array($this, 'version_check'));
        add_action('after_setup_theme', array($this, 'include_template_functions'), 11);

        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_script'), 10);

        add_filter('template_include', array($this, 'task_templates'));

        add_filter('get_the_author_url', array(__CLASS__, 'author_link'), 10, 2);
        add_filter('author_link', array(__CLASS__, 'author_link'), 10, 2);
    }

    /**
     * Initializes plugin.
     */
    public function init_plugin()
    {
        if (!class_exists('WP_Job_Manager')) {
            return;
        }

        // Includes.
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-meta-boxes.php';
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-bid.php';
        // include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-user.php';
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-forms.php';
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-shortcodes.php';
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-task.php';
        include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-reviews.php';
       // include_once dirname(__FILE__) . '/includes/class-workscout-freelancer-project.php';
        
        if(class_exists('WP_Job_Manager_Paid_Listings') || class_exists('WC_Paid_Listings')){
        include_once dirname(__FILE__) . '/includes/class-wc-paid-listings-submit-task-form.php';
        include(dirname(__FILE__) . '/includes/paid-listings/class-workscout-freelancer-paid-listings.php');
        
        include(dirname(__FILE__) . '/includes/paid-listings/class-wc-product-task-package.php');
        include(dirname(__FILE__) . '/includes/paid-listings/class-workscout-freelancer-paid-listings-admin.php');
        include(dirname(__FILE__) . '/includes/paid-listings/class-workscout-freelancer-paid-listings-admin-listings.php');
        }
        include_once dirname(__FILE__) . '/includes/paid-listings/user-functions.php';


        
        // // Init classes.
        $this->writepanels = new WorkScout_Freelancer_Meta_Boxes();
        $this->bid = new WorkScout_Freelancer_Bid();
        $this->task = new WorkScout_Freelancer_Task();
        $this->forms = new WorkScout_Freelancer_Forms();
        $this->reviews = new WorkScout_Freelancer_Reviews();
     //   $this->freelancer_project = new WorkScout_Freelancer_Project();
        
      
        add_action('switch_theme', 'flush_rewrite_rules', 15);
        self::maybe_schedule_cron_jobs();
    }

    public function include_template_functions()
    {
        include(dirname(__FILE__) . '/workscout-freelancer-functions.php');
    }


    static function author_link($permalink, $user_id)
    {
        $author_id = get_user_meta($user_id, 'freelancer_profile', true);

        if ($author_id) {
            $permalink = get_post_permalink($author_id);
        }
        return $permalink;
    }

    /* handles single listing and archive listing view */
    public static function task_templates($template)
    {
        $post_type = get_post_type();
        $custom_post_types = array('task');

        $template_loader = new WorkScout_Freelancer_Template_Loader;
        if (in_array($post_type, $custom_post_types)) {

            if (is_archive() && !is_author()) {

                $template = $template_loader->locate_template('archive-' . $post_type . '.php');

                return $template;
            }

            if (is_single()) {
                $template = $template_loader->locate_template('single-' . $post_type . '.php');
                return $template;
            }
        }

        return $template;
    }


    /**
     * Checks WPJM core version.
     */
    public function version_check()
    {
        if (!class_exists('WP_Job_Manager') || !defined('JOB_MANAGER_VERSION')) {
            $screen = get_current_screen();
            if (null !== $screen && 'plugins' === $screen->id) {
                $this->display_error(__('<em>WorkScout Freelancer</em> requires WP Job Manager to be installed and activated.', 'workscout-freelancer'));
            }
        }
    }


    /**
     * Schedule cron jobs for Listeo_Core events.
     */
    public static function maybe_schedule_cron_jobs()
    {

        if (!wp_next_scheduled('workscout_freelancer_check_for_expired_tasks')) {
            wp_schedule_event(time(), 'hourly', 'workscout_freelancer_check_for_expired_tasks');
        }
    }


    /**
     * Display error message notice in the admin.
     *
     * @param string $message
     */
    private function display_error($message)
    {
        echo '<div class="error">';
        echo '<p>' . wp_kses_post($message) . '</p>';
        echo '</div>';
    }

    function package_terms(){
        if (!get_term_by('slug', sanitize_title('task_package'), 'product_type')) {
            wp_insert_term('task_package', 'product_type');
        }
        if (!get_term_by('slug', sanitize_title('task_package_subscription'), 'product_type')) {
            wp_insert_term('task_package_subscription', 'product_type');
        }
    }

    // /**
    //  * Loads the REST API functionality.
    //  */
    // public function rest_init()
    // {
    //     include_once RESUME_MANAGER_PLUGIN_DIR . '/includes/class-wp-resume-manager-rest-api.php';
    //     WP_Resume_Manager_REST_API::init();
    // }

  

    /**
     * Include admin
     */
    public function admin()
    {
        
        if (is_admin() && class_exists('WP_Job_Manager')) {
            include_once 'includes/admin/class-workscout-freelancer-admin.php';
        }
    }

  
    /**
     * Localisation
     *
     * @access private
     * @return void
     */
    public function load_plugin_textdomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), 'workscout-freelancer');

        load_textdomain('workscout-freelancer', WP_LANG_DIR . "/workscout-freelancer/workscout-freelancer-$locale.mo");
        load_plugin_textdomain('workscout-freelancer', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }





    /**
     * Frontend_scripts function.
     *
     * @access public
     * @return void
     */
    public function frontend_scripts()
    {
        global $post;
        $ajax_url         = admin_url('admin-ajax.php', 'relative');
        $ajax_filter_deps = array('jquery');

        // WPML workaround until this is standardized.
        if (defined('ICL_LANGUAGE_CODE')) {
            $ajax_url = add_query_arg('lang', ICL_LANGUAGE_CODE, $ajax_url);
        }

        if (wp_script_is('select2', 'registered')) {
            $ajax_filter_deps[] = 'select2';
            wp_enqueue_style('select2');
        }

      
    }

 

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {
     
        //wp_register_style('workscout-freelancer-icons', WORKSCOUT_FREELANCER_PLUGIN_URL. '/assets/css/icons.css', array(), WORKSCOUT_FREELANCER_VERSION);
        wp_register_style('workscout-freelancer-frontend', WORKSCOUT_FREELANCER_PLUGIN_URL. '/assets/css/freelancer.css', array(), WORKSCOUT_FREELANCER_VERSION);

        //wp_enqueue_style('workscout-freelancer-icons');
        wp_enqueue_style('workscout-freelancer-frontend');

    } // End enqueue_styles ()
    /**
     * Load frontend JS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_script()
    {
       
        wp_register_script('bootstrap-select', WORKSCOUT_FREELANCER_PLUGIN_URL . '/assets/js/bootstrap-select.min.js', array('jquery'), WORKSCOUT_FREELANCER_VERSION, true);
        wp_register_script('snackbar', WORKSCOUT_FREELANCER_PLUGIN_URL . '/assets/js/snackbar.js', array('jquery'), WORKSCOUT_FREELANCER_VERSION, true);
        wp_register_script('tippy', WORKSCOUT_FREELANCER_PLUGIN_URL . '/assets/js/tippy.all.min.js', array('jquery'), WORKSCOUT_FREELANCER_VERSION, true);
        wp_register_script('workscout-freelancer-frontend', WORKSCOUT_FREELANCER_PLUGIN_URL . '/assets/js/workscout-freelancer-frontend.js', array('bootstrap-slider','snackbar', 'tippy', 'bootstrap-select'), WORKSCOUT_FREELANCER_VERSION, true);
        wp_enqueue_script('workscout-freelancer-frontend');
        wp_register_script('workscout-freelancer-ajaxsearch', WORKSCOUT_FREELANCER_PLUGIN_URL . '/assets/js/ajax.search.min.js', array('jquery'), WORKSCOUT_FREELANCER_VERSION, true);
      //  wp_enqueue_script('ajaxsearch');	
    } // End enqueue_styles ()

}

$GLOBALS['workscout_freelancer'] = new WorkScout_Freelancer();

if (!class_exists('Gamajo_Template_Loader')) {
    require_once dirname(__FILE__) . '/lib/class-gamajo-template-loader.php';
}
include(dirname(__FILE__) . '/includes/class-workscout-freelancer-templates.php');
if (file_exists(dirname(__FILE__) . '/lib/cmb2-tabs/plugin.php')) {
    require_once dirname(__FILE__) . '/lib/cmb2-tabs/plugin.php';
}