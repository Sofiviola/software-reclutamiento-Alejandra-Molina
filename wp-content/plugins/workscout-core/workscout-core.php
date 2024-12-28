<?php
/*
 * Plugin Name: WorkScout-Core - WorkScout WPJM Plugin by Purethemes
 * Version: 1.6.11
 * Plugin URI: http://www.purethemes.net/
 * Description: WPJM Plugin from Purethemes.net for WorkScout theme
 * Author: Purethemes.net
 * Author URI: http://www.purethemes.net/
 * Requires at least: 4.7
 * Tested up to: 5.3
 *
 * Text Domain: workscout_core
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Lukasz Girek
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WORKSCOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( 'includes/class-workscout-core-admin.php' );
require_once( 'includes/class-workscout-core.php' );


function this_plugin_last() {
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
        array_splice($active_plugins, $this_plugin_key, 1);
        array_push($active_plugins, $this_plugin);
        update_option('active_plugins', $active_plugins);
}
add_action("activated_plugin", "this_plugin_last");
/**
 * Returns the main instance of workscout_core to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object workscout_core
 */
function WorkScout_Core () {
	$instance = WorkScout_Core::instance( __FILE__, '1.6.6' );

	/*if ( is_null( $instance->settings ) ) {
		$instance->settings =  WorkScout_Core_Settings::instance( $instance );
	}*/
	

	return $instance;
}

/* load template engine*/
if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require_once dirname( __FILE__ ) . '/lib/class-gamajo-template-loader.php';
}

include( dirname( __FILE__ ) . '/includes/class-workscout-core-templates.php' );
$GLOBALS['workscout_core'] = WorkScout_Core();



function workscout_core_activity_log() {
	global $wpdb;

	//$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Table for user packages
	 */
	$sql = "
	CREATE TABLE {$wpdb->prefix}workscout_core_activity_log (
	  id bigint(20) NOT NULL auto_increment,
	  user_id bigint(20) NOT NULL,
	  post_id  bigint(20) NOT NULL,
	  related_to_id bigint(20) NOT NULL,
	  action varchar(255) NOT NULL,
	  log_time int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY  (id)
	) $collate;
	";
	
	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'workscout_core_activity_log' );


function workscout_core_messages_db() {
	global $wpdb;

	//$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Table for user packages
	 */
	$sql = "
	CREATE TABLE {$wpdb->prefix}workscout_core_messages (
	  id bigint(20) NOT NULL auto_increment,
	  conversation_id bigint(20) NOT NULL,
	  sender_id bigint(20) NOT NULL,
	  message  longtext NOT NULL,
	  created_at bigint(20) NOT NULL,
	  PRIMARY KEY  (id)
	) $collate;
	";
	
	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'workscout_core_messages_db' );

function workscout_core_conversations_db() {
	global $wpdb;

	//$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Table for user packages
	 */
	$sql = "
	CREATE TABLE {$wpdb->prefix}workscout_core_conversations (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `timestamp` varchar(255) NOT NULL DEFAULT '',
	  `user_1` int(11) NOT NULL,
	  `user_2` int(11) NOT NULL,
	  `referral` varchar(255) NOT NULL DEFAULT '',
	  `read_user_1` int(11) NOT NULL,
	  `read_user_2` int(11) NOT NULL,
	  `last_update` bigint(20) DEFAULT NULL,
	  `notification` varchar(20) DEFAULT '',
	  PRIMARY KEY  (id)
	) $collate;
	";
	
	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'workscout_core_conversations_db' );




function workscout_core_commisions_db()
{
	global $wpdb, $workscout_core_db_version;

	//$wpdb->hide_errors();

	$collate = '';
	if ($wpdb->has_cap('collation')) {
		if (!empty($wpdb->charset)) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if (!empty($wpdb->collate)) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		$sql = "
        CREATE TABLE {$wpdb->prefix}workscout_core_commissions (
            id bigint(20) UNSIGNED NOT NULL auto_increment,
            user_id bigint(20) NOT NULL,
            order_id bigint(20) NOT NULL,
            amount double(15,4) NOT NULL,
            rate  decimal(5,4) NOT NULL,
            status  varchar(255) NOT NULL,
            date DATETIME NOT NULL,
            type  varchar(255) NOT NULL,
            booking_id  bigint(20) NOT NULL,
            listing_id  bigint(20) NOT NULL,
            pp_status_code varchar (50) DEFAULT NULL, 
            payout_batch_id varchar (50) DEFAULT NULL,
            batch_status varchar (50) DEFAULT NULL,
            time_created DATETIME DEFAULT NULL,
            time_completed DATETIME DEFAULT NULL,
            fees_currency varchar (5) DEFAULT NULL,
            fee_value double (15, 4) DEFAULT NULL,
            funding_source varchar (50) DEFAULT NULL,
            sent_amount_currency varchar (5) DEFAULT NULL,
            sent_amount_value double (15, 4) DEFAULT NULL,
            payout_item_id varchar (50) DEFAULT NULL,
            payout_item_transaction_id varchar (50) DEFAULT NULL,
            payout_item_activity_id varchar (50) DEFAULT NULL,
            payout_item_transaction_status varchar (50) DEFAULT NULL,
            error_name varchar (100) DEFAULT NULL,
            error_message mediumtext DEFAULT NULL,
            payout_item_link varchar(255) DEFAULT NULL,
			commission_type  varchar(255) NOT NULL,
          PRIMARY KEY  (id)
        ) $collate;
        ";

		dbDelta($sql);
	
}

register_activation_hook(__FILE__, 'workscout_core_commisions_db');

// if (!function_exists('workscout_update_commission_table_check')) {
// 	function workscout_update_commission_table_check()
// 	{
// 		global $workscout_core_db_version;

// 		if (get_site_option('workscout_commission_table_version') != $workscout_core_db_version) {
// 			workscout_core_commisions_db();
// 		}
// 	}
// 	add_action('plugins_loaded', 'workscout_update_commission_table_check');
// }


function workscout_core_commisions_payouts_db()
{
	global $wpdb;

	//$wpdb->hide_errors();

	$collate = '';
	if ($wpdb->has_cap('collation')) {
		if (!empty($wpdb->charset)) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if (!empty($wpdb->collate)) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	/**
	 * Table for user packages
	 */
	$sql = "
	CREATE TABLE {$wpdb->prefix}workscout_core_commissions_payouts (
	  id bigint(20) UNSIGNED NOT NULL auto_increment,
	  user_id bigint(20) NOT NULL,
	  status  varchar(255) NOT NULL,
	  orders  varchar(255) NOT NULL,
	  payment_method  text NOT NULL,
	  payment_details  text NOT NULL,
	  `date`  DATETIME NOT NULL,
	  amount double(15,4) NOT NULL,
	  PRIMARY KEY  (id)
	) $collate;
	";

	dbDelta($sql);
}
register_activation_hook(__FILE__, 'workscout_core_commisions_payouts_db');


function workscout_core_missing_cmb2() { ?>
	<div class="error">
		<p><?php _e( 'CMB2 Plugin is missing CMB2!', 'workscout_core' ); ?></p>
	</div>
<?php }

if(function_exists('vc_map')) {
    require_once('workscout-core-vc.php');
    //require_once get_template_directory() . '/inc/vc_modified_shortcodes.php';
}

WorkScout_Core();