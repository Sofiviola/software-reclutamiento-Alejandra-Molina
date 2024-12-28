<?php
/**
 * File containing the class Workscout_Freelancer_Admin.
 *
 * @package wp-job-manager-resumes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Workscout_Freelancer_Admin class.
 */

class Workscout_Freelancer_Admin {
    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        include_once 'class-workscout-freelancer-settings.php';
        
        add_filter('job_manager_admin_screen_ids', [$this, 'add_screen_ids']);
        add_action('admin_menu', [$this, 'admin_menu'], 12);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 20);
        
        $this->settings_page = new Workscout_Freelancer_Settings();
    }

    /**
     * Add screen ids
     *
     * @param array $screen_ids
     * @return  array
     */
    public function add_screen_ids($screen_ids)
    {
        $screen_ids[] = 'edit-task';
        $screen_ids[] = 'task';
        $screen_ids[] = 'task_page_workscout-freelancer-settings';
        return $screen_ids;
    }

    /**
     * admin_enqueue_scripts function.
     *
     * @access public
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        global $wp_scripts;

        // $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
        // $jquery_version = preg_replace('/-wp/', '', $jquery_version);
        // wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css');
        // wp_enqueue_style('resume_manager_admin_css', RESUME_MANAGER_PLUGIN_URL . '/assets/dist/css/admin.css', ['dashicons'], RESUME_MANAGER_VERSION);
        // wp_enqueue_script('resume_manager_admin_js', RESUME_MANAGER_PLUGIN_URL . '/assets/dist/js/admin.js', ['jquery', 'jquery-tiptip', 'jquery-ui-datepicker', 'jquery-ui-sortable'], RESUME_MANAGER_VERSION, true);
    }

    /**
     * admin_menu function.
     *
     * @access public
     * @return void
     */
    public function admin_menu()
    {
        add_submenu_page('edit.php?post_type=task', __('Task Settings', 'workscout-freelancer'), __('Task Settings', 'workscout-freelancer'), 'manage_options', 'workscout-freelancer-settings', [$this->settings_page, 'output']);
    }
}
new Workscout_Freelancer_Admin();
