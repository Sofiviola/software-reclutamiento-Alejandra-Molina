<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WorkScout_Freelancer {

    /**
     * The single instance of WorkScout_Freelancer.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.3.4')
    {
        $this->_version = $version;

        $this->_token = 'workscout_freelancer';

        // Load plugin environment variables
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        //  register_activation_hook($this->file, array($this, 'install'));

      //  add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
        add_action('after_setup_theme', array($this, 'include_template_functions'), 11);
        

        include('class-workscout-freelancer-cpt.php');
        
        $this->post_types     = WorkScout_Freelancer_CPT::instance();
        

        if (is_admin()) {
            include('class-workscout-freelancer-admin.php');
            include('class-workscout-freelancer-settings.php');
            include('class-workscout-freelancer-meta-boxes.php');
            $this->writepanels = new WorkScout_Freelancer_Meta_Boxes();
            $this->admin = new Workscout_Freelancer_Admin();
        }


        add_filter('template_include', array($this, 'listing_templates'));

        // Schedule cron jobs
        
    }

    public function include_template_functions()
    {
        include(WORKSCOUT_FREELANCER_PLUGIN_DIR . '/workscout-freelancer-functions.php');
    }


    /**
     * Main WorkScout_Freelancer Instance
     *
     * Ensures only one instance of WorkScout_Freelancer is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WorkScout_Freelancer()
     * @return Main WorkScout_Freelancer instance
     */
    public static function instance($file = '', $version = '1.2.1')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    } // End instance ()

    /* handles single listing and archive listing view */
    public static function listing_templates($template)
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

        if (is_tax('listing_category')) {
            $template = $template_loader->locate_template('archive-job_listings.php');
        }

        if (is_post_type_archive('listing')) {

            $template = $template_loader->locate_template('archive-job_listings.php');
        }

        return $template;
    }



    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {
        wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/style.css', array(), $this->_version);
       // wp_register_style($this->_token . '-frontend-custom', esc_url($this->assets_url) . 'css/custom.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-frontend');
        wp_enqueue_style($this->_token . '-frontend-custom');
    } // End enqueue_styles ()

}



?>