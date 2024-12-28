<?php
/*
 * Plugin Name: WorkScout Elementor
 * Version: 1.2.6
 * Plugin URI: http://www.purethemes.net/
 * Description: Workscout widgets for Elementor
 * Author: Purethemes.net
 * Author URI: http://www.purethemes.net/
 *
 * Text Domain: workscout_elementor
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Lukasz Girek
 * @since 1.0.0
 * Elementor tested up to: 3.10.1
 */


define( 'ELEMENTOR_WORKSCOUT', __FILE__ );


/**
 * Include the Elementor_Workscout class.
 */
require plugin_dir_path( ELEMENTOR_WORKSCOUT ) . 'class-elementor-workscout.php';