<?php
/**
 * Get a users packages from the DB. By default this will only return packages
 * that are not used up. If the $all parameter is `true`, all packages will be
 * returned.
 *
 * @param int          $user_id
 * @param string|array $package_type
 * @param bool         $all
 * @return array of objects
 */
function workscout_freelancer_get_user_packages( $user_id, $package_type = '', $all = false ) {
	global $wpdb;

	if ( empty( $package_type ) ) {
		$package_type = array( 'task' );
	} else {
		$package_type = array( $package_type );
	}

	$query = "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND package_type IN ( '" . implode( "','", $package_type ) . "' )";

	if ( ! $all ) {
		$query .= ' AND ( package_count < package_limit OR package_limit = 0 )';
	}

	$packages = $wpdb->get_results( $wpdb->prepare( $query, $user_id ), OBJECT_K );

	return $packages;
}

/**
 * Get a package
 *
 * @param  int $package_id
 * @return WC_Paid_Listings_Package
 */
function workscout_freelancer_get_user_package( $package_id ) {
	global $wpdb;

	$package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
	return workscout_freelancer_get_package( $package );
}

/**
 * Give a user a package
 *
 * @param  int  $user_id        The user ID.
 * @param  int  $product_id     The product ID.
 * @param  int  $order_id       The order ID.
 * @param  bool $check_existing Check for existing records. (Default: false)
 * @return int|bool false
 */
function workscout_freelancer_give_user_package( $user_id, $product_id, $order_id = 0, $check_existing = false ) {
	global $wpdb;

	$package = wc_get_product( $product_id );

	if ( ! $package->is_type( 'task_package' ) && ! $package->is_type( 'task_package_subscription' )  ) {
		return false;
	}

	$is_featured = false;
	if ( $package instanceof WC_Product_Task_Package || $package instanceof WC_Product_Task_Package_Subscription ) {
		$is_featured = $package->is_task_featured();
	} 

	if ( $check_existing ) {
		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wcpl_user_packages WHERE
			user_id = %d
			AND product_id = %d
			AND order_id = %d
			AND package_duration = %d
			AND package_limit = %d
			AND package_featured = %d
			AND package_type = %d",
				$user_id,
				$product_id,
				$order_id,
				$package->get_duration(),
				$package->get_limit(),
				$is_featured ? 1 : 0,
				'task'
			)
		);

		if ( $id ) {
			return $id;
		}
	}

	$wpdb->insert(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'user_id'          => $user_id,
			'product_id'       => $product_id,
			'order_id'         => $order_id,
			'package_count'    => 0,
			'package_duration' => $package->get_duration(),
			'package_limit'    => $package->get_limit(),
			'package_featured' => $is_featured ? 1 : 0,
			'package_type'     =>  'task',
		)
	);

	return $wpdb->insert_id;
}

/**
 * Get customer ID from Order
 *
 * @param WC_Order $order
 * @return int
 */
function workscout_freelancer_get_order_customer_id( $order ) {
	
	return $order->get_customer_id();
}

/**
 * Get customer ID from Order
 *
 * @param WC_Order $order
 * @return int
 */
function workscout_freelancer_get_order_id( $order ) {
	
	return $order->get_id();
}

/**
 * @deprecated
 */
function get_user_task_packages( $user_id ) {
	return workscout_freelancer_get_user_packages( $user_id, 'task' );
}

/**
 * @deprecated
 */
function get_user_task_package( $package_id ) {
	return workscout_freelancer_get_user_package( $package_id );
}

/**
 * @deprecated
 */
function give_user_task_package( $user_id, $product_id ) {
	return workscout_freelancer_give_user_package( $user_id, $product_id );
}

/**
 * @deprecated
 */
function user_task_package_is_valid( $user_id, $package_id ) {
	return workscout_freelancer_package_is_valid( $user_id, $package_id );
}

/**
 * @deprecated
 */
function increase_task_package_job_count( $user_id, $package_id ) {
	workscout_freelancer_increase_package_count( $user_id, $package_id );
}

/**
 * Get listing IDs for a user package
 *
 * @return array
 */
function workscout_freelancer_get_listings_for_package( $user_package_id ) {
	global $wpdb;

	return $wpdb->get_col(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} " .
			"LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID " .
			"WHERE meta_key = '_user_package_id' " .
			'AND meta_value = %s;',
			$user_package_id
		)
	);
}



///////////////////////////////////////
// Package Functions
///////////////////////////////////////


/**
 * Get a package
 *
 * @param  stdClass $package
 * @return WC_Paid_Listings_Package
 */
function workscout_freelancer_get_package($package)
{
	return new Workscout_Freelancer_Paid_Listings_Package($package);
}

/**
 * Approve a listing
 *
 * @param  int $listing_id
 * @param  int $user_id
 * @param  int $user_package_id
 * @param  int $package_id
 * @return void
 */
function workscout_freelancer_approve_listing_with_package($listing_id, $user_id, $user_package_id, $package_id = null)
{
	if (workscout_freelancer_package_is_valid($user_id, $user_package_id)) {
		$resumed_post_status = get_post_meta($listing_id, '_post_status_before_package_pause', true);
		if (!empty($resumed_post_status)) {
			$listing = array(
				'ID'          => $listing_id,
				'post_status' => $resumed_post_status,
			);
			delete_post_meta($listing_id, '_post_status_before_package_pause');
		} else {
			$listing = array(
				'ID'            => $listing_id,
				'post_date'     => current_time('mysql'),
				'post_date_gmt' => current_time('mysql', 1),
			);

			switch (get_post_type($listing_id)) {
				case 'task':
					$listing['post_status'] = get_option('workscout_freelancer_task_submission_requires_approval') ? 'pending' : 'publish';
					break;
			
			}
		}

		if ('task' === get_post_type($listing_id)) {
			delete_post_meta($listing_id, '_task_expires');
		}

		update_post_meta($listing_id, '_user_package_id', $user_package_id);

		if (null !== $package_id) {
			update_post_meta($listing_id, '_package_id', $package_id);
		}

		wp_update_post($listing);

		/**
		 * Checks to see whether or not a particular job listing affects the package count.
		 *
		 * @since 2.7.3
		 *
		 * @param bool $job_listing_affects_package_count True if it affects package count.
		 * @param int  $listing_id                        Post ID.
		 */
		if (apply_filters('job_manager_job_listing_affects_package_count', true, $listing_id)) {
			workscout_freelancer_increase_package_count($user_id, $user_package_id);
		}
	}
}

/**
 * Approve a job listing
 *
 * @param  int $job_id
 * @param  int $user_id
 * @param  int $user_package_id
 * @return void
 */
function workscout_freelancer_approve_job_listing_with_package($job_id, $user_id, $user_package_id)
{
	workscout_freelancer_approve_listing_with_package($job_id, $user_id, $user_package_id);
}

/**
 * Approve a resume
 *
 * @param  int $resume_id
 * @param  int $user_id
 * @param  int $user_package_id
 * @return void
 */
function workscout_freelancer_approve_resume_with_package($resume_id, $user_id, $user_package_id)
{
	workscout_freelancer_approve_listing_with_package($resume_id, $user_id, $user_package_id);
}

/**
 * See if a package is valid for use
 *
 * @param int $user_id
 * @param int $package_id
 * @return bool
 */
function workscout_freelancer_package_is_valid($user_id, $package_id)
{
	global $wpdb;

	$package = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND id = %d;", $user_id, $package_id));

	if (!$package) {
		return false;
	}

	if ($package->package_count >= $package->package_limit && $package->package_limit != 0) {
		return false;
	}

	return true;
}

/**
 * Increase job count for package
 *
 * @param  int $user_id
 * @param  int $package_id
 * @return int affected rows
 */
function workscout_freelancer_increase_package_count($user_id, $package_id)
{
	global $wpdb;

	$packages = workscout_freelancer_get_user_packages($user_id, '', true);

	if (isset($packages[$package_id])) {
		$new_count = $packages[$package_id]->package_count + 1;
	} else {
		$new_count = 1;
	}

	return $wpdb->update(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'package_count' => $new_count,
		),
		array(
			'user_id' => $user_id,
			'id'      => $package_id,
		),
		array('%d'),
		array('%d', '%d')
	);
}

/**
 * Decrease job count for package
 *
 * @param  int $user_id
 * @param  int $package_id
 * @return int affected rows
 */
function workscout_freelancer_decrease_package_count($user_id, $package_id)
{
	global $wpdb;

	$packages = workscout_freelancer_get_user_packages($user_id, '', true);

	if (isset($packages[$package_id])) {
		$new_count = $packages[$package_id]->package_count - 1;
	} else {
		$new_count = 0;
	}

	return $wpdb->update(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'package_count' => max(0, $new_count),
		),
		array(
			'user_id' => $user_id,
			'id'      => $package_id,
		),
		array('%d'),
		array('%d', '%d')
	);
}

/**
 * Handle listing renewal when package is paid for.
 *
 * @param int  $package_id Package/product ID.
 * @param int  $job_id Job listing ID.
 * @param bool $is_order_confirmation Whether this is an order confirmation.
 *
 * @return void
 */
function workscout_freelancer_handle_listing_renewal($package_id, $job_id, $is_order_confirmation = false)
{
	if (!class_exists('WP_Job_Manager_Helper_Renewals')) {
		return;
	}

	$user_package = workscout_freelancer_get_user_package($package_id);
	$package      = wc_get_product($user_package->get_product_id());

	$is_job_package          = $package instanceof WC_Product_Task_Package;
	$is_subscription_package = $package instanceof WC_Product_Task_Package_Subscription && 'package' === $package->get_package_subscription_type();
	$is_subscription_listing = $package instanceof WC_Product_Task_Package_Subscription && 'listing' === $package->get_package_subscription_type();
	$user_has_subscription   = function_exists('wcs_user_has_subscription') && wcs_user_has_subscription(get_current_user_id(), $package_id, 'active');
	if (WP_Job_Manager_Helper_Renewals::job_can_be_renewed($job_id)) {
		if ($is_job_package || $is_subscription_package) {
			/** This filter is documented in includes/package-functions.php */
			if (apply_filters('job_manager_job_listing_affects_package_count', true, $job_id)) {
				workscout_freelancer_increase_package_count(get_current_user_id(), $package_id);
				WP_Job_Manager_Helper_Renewals::renew_job_listing(get_post($job_id));
			}
		}
	} elseif ($is_subscription_listing && !$user_has_subscription) {
		workscout_freelancer_increase_package_count(get_current_user_id(), $package_id);
	}
	if ($is_order_confirmation) {
		delete_post_meta($job_id, '_cancelled_package_order_id');
	}
}
