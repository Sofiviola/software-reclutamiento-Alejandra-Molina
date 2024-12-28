<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Integration
 */
class WP_Job_Manager_WCPL_Submit_Task_Form {

	private static $package_id      = 0;
	private static $is_user_package = false;

	/**
	 * Init
	 */
	public static function init() {
		// add_filter( 'the_title', array( __CLASS__, 'append_package_name' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'styles' ) );
		add_filter( 'submit_task_steps', array( __CLASS__, 'submit_task_steps' ), 10 );

		// Posted Data
		if ( ! empty( $_POST['task_package'] ) ) {
			if ( is_numeric( $_POST['task_package'] ) ) {
				self::$package_id      = absint( $_POST['task_package'] );
				self::$is_user_package = false;
			} else {
				self::$package_id      = absint( substr( $_POST['task_package'], 5 ) );
				self::$is_user_package = true;
			}
		} elseif ( ! empty( $_COOKIE['chosen_package_id'] ) ) {
			self::$package_id      = absint( $_COOKIE['chosen_package_id'] );
			self::$is_user_package = absint( $_COOKIE['chosen_package_is_user_package'] ) === 1;
		}
	}

	/**
	 * Replace a page title with the endpoint title
	 *
	 * @param  string $title
	 * @return string
	 */
	public static function append_package_name( $title ) {
		if ( ! empty( $_POST ) && ! is_admin() && is_main_query() && in_the_loop() && is_page( get_option('workscout_freelancer_submit_task_form_page_id' ) ) && self::$package_id && 'before' === get_option('task_paid_listings_flow' ) && apply_filters( 'wcpl_append_package_name', true ) ) {
			if ( self::$is_user_package ) {
				$package = wc_paid_listings_get_user_package( self::$package_id );
				$title  .= ' &ndash; ' . $package->get_title();
			} else {
				$post = get_post( self::$package_id );
				if ( $post ) {
					$title .= ' &ndash; ' . $post->post_title;
				}
			}
			remove_filter( 'the_title', array( __CLASS__, 'append_package_name' ) );
		}
		return $title;
	}

	/**
	 * Add form styles
	 */
	public static function styles() {
		wp_enqueue_style( 'wc-paid-listings-packages' );
	}

	/**
	 * Change submit button text
	 *
	 * @return string
	 */
	public static function submit_button_text() {
		return __( 'Choose a package &rarr;', 'workscout-freelancer' );
	}

	/**
	 * Change initial job status
	 *
	 * @return string
	 */
	public static function submit_task_post_status( $status, $task ) {
		switch ( $task->post_status ) {
			case 'preview':
				return 'pending_payment';
			break;
			case 'expired':
				return 'expired';
			break;
			default:
				return $status;
			break;
		}
	}

	/**
	 * Return packages
	 *
	 * @return array
	 */
	public static function get_packages() {

return get_posts(
	apply_filters(
		'wcpl_get_task_packages_args',
		array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'suppress_filters' => false,
			'tax_query'        => WC()->query->get_tax_query(
				array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'task_package', 'task_package_subscription' ),
						'operator' => 'IN',
					),
				)
			),
			'meta_query'       => WC()->query->get_meta_query(),
		)
	)
);
	}

	/**
	 * Change the steps during the submission process
	 *
	 * @param  array $steps
	 * @return array
	 */
	public static function submit_task_steps( $steps ) {
		if ( self::get_packages() && apply_filters( 'wcpl_enable_paid_task_submission', true ) ) {
			
			// We need to hijack the preview submission to redirect to WooCommerce and add a step to select a package.
			// Add a step to allow the user to choose a package. Comes after preview.
			$steps['wc-choose-package'] = array(
				'name'     => __( 'Choose a package', 'workscout-freelancer' ),
				'view'     => array( __CLASS__, 'choose_package' ),
				'handler'  => array( __CLASS__, 'choose_package_handler' ),
				'priority' => 25,
			);

			// If we instead want to show the package selection FIRST, change the priority and add a new handler.
			if ( 'before' === get_option( 'task_paid_listings_flow' ) ) {
				$steps['wc-choose-package']['priority'] = 5;
				$steps['wc-process-package']            = array(
					'name'     => '',
					'view'     => false,
					'handler'  => array( __CLASS__, 'choose_package_handler' ),
					'priority' => 25,
				);
				// If showing the package step after preview, the preview button text should be changed to show this.
			} elseif ( 'before' !== get_option( 'task_paid_listings_flow' ) ) {
				add_filter( 'submit_task_step_preview_submit_text', array( __CLASS__, 'submit_button_text' ), 10 );
			}
			

			// We should make sure new jobs are pending payment and not published or pending.
			add_filter( 'submit_task_post_status', array( __CLASS__, 'submit_task_post_status' ), 10, 2 );
		
		}
	
		return $steps;
	}

	/**
	 * Get the package ID being used for task submission, expanding any user package
	 *
	 * @return int
	 */
	public static function get_package_id() {
		if ( self::$is_user_package ) {
			$package = wc_paid_listings_get_user_package( self::$package_id );
			return $package->get_product_id();
		}
		return self::$package_id;
	}

	/**
	 * Choose package form
	 */
	public static function choose_package() {
		
		$form          	= WorkScout_Freelancer_Form_Submit_Task::instance();
		$task_id     	= $form->get_task_id();
		$step          	= $form->get_step();
		$form_name     	= $form->form_name;
		$packages      	= self::get_packages();
		$user_packages 	= wc_paid_listings_get_user_packages( get_current_user_id(), 'task' );
		$button_text   	= 'before' !== get_option('task_paid_listings_flow' ) ? __( 'Submit &rarr;', 'workscout-freelancer' ) : __( 'Listing Details &rarr;', 'workscout-freelancer' );
	


				get_job_manager_template(
					'task-package-selection.php',
					array(
						'packages'      => $packages,
						'user_packages' => $user_packages,
						'task_id' => $task_id,
						'step' => $step,
						'form_name' => $form_name,
						'button_text' => $button_text,
					),
					'wp-job-manager-tasks',
					WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
				);
				?>
			
		<?php
	}

	/**
	 * Validate package
	 *
	 * @param  int  $package_id
	 * @param  bool $is_user_package
	 * @return bool|WP_Error
	 */
	private static function validate_package( $package_id, $is_user_package ) {
		if ( empty( $package_id ) ) {
			return new WP_Error( 'error', __( 'Invalid Package', 'workscout-freelancer' ) );
		} elseif ( $is_user_package ) {
			if ( ! wc_paid_listings_package_is_valid( get_current_user_id(), $package_id ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'workscout-freelancer' ) );
			}
		} else {
			$package = wc_get_product( $package_id );

			if ( ! $package->is_type( 'task_package' ) && ! $package->is_type( 'task_package_subscription' ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'workscout-freelancer' ) );
			}

			// Don't let them buy the same subscription twice
			if ( class_exists( 'WC_Subscriptions' )
				 && is_user_logged_in()
				 && $package->is_type( 'task_package_subscription' )
				 && $package instanceof WC_Product_task_Package_Subscription
				 && 'package' === $package->get_package_subscription_type()
			) {
				if ( wcs_user_has_subscription( get_current_user_id(), $package_id, 'active' ) ) {
					return new WP_Error( 'error', __( 'You already have this subscription.', 'workscout-freelancer' ) );
				}
			}
		}
		return true;
	}

	/**
	 * Purchase a job package
	 *
	 * @param  int|string $package_id
	 * @param  bool       $is_user_package
	 * @param  int        $task_id
	 * @return bool Did it work or not?
	 */
	private static function process_package( $package_id, $is_user_package, $task_id ) {
		// Make sure the job has the correct status
		if ( 'preview' === get_post_status( $task_id ) ) {
			// Update job listing
			$update                  = array();
			$update['ID']            = $task_id;
			$update['post_status']   = 'pending_payment';
			$update['post_date']     = current_time( 'mysql' );
			$update['post_date_gmt'] = current_time( 'mysql', 1 );
			$update['post_author']   = get_current_user_id();
			wp_update_post( $update );
		}

		if ( $is_user_package ) {
			$package = workscout_freelancer_get_user_package( $package_id );

			// Give task the package attributes
			update_post_meta( $task_id, '_featured', $package->is_featured() ? 1 : 0 );
			update_post_meta( $task_id, '_task_duration', $package->get_duration() );
			update_post_meta( $task_id, '_package_id', $package->get_product_id() );
			update_post_meta( $task_id, '_user_package_id', $package_id );

			// Approve the task
			if ( in_array( get_post_status( $task_id ), array( 'pending_payment', 'expired' ) ) ) {
				workscout_freelancer_approve_listing_with_package( $task_id, get_current_user_id(), $package_id );
			}

			do_action( 'wcpl_process_package_for_task', $package_id, $is_user_package, $task_id );

			return true;
		} elseif ( $package_id ) {
			$package = wc_get_product( $package_id );

			$is_featured = false;
			if ( $package instanceof WC_Product_task_Package || $package instanceof WC_Product_task_Package_Subscription ) {
				$is_featured = $package->is_task_featured();
			}

			// Give task the package attributes
			update_post_meta( $task_id, '_featured', $is_featured ? 1 : 0 );
			update_post_meta( $task_id, '_task_duration', $package->get_duration() );
			update_post_meta( $task_id, '_package_id', $package->get_product_id() );

			// Add package to the cart
			WC()->cart->add_to_cart(
				$package_id,
				1,
				'',
				'',
				array(
					'task_id' => $task_id,
				)
			);

			wc_add_to_cart_message( $package_id );

			// Clear cookie
			wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );
			wc_setcookie( 'chosen_package_is_user_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wcpl_process_package_for_task', $package_id, $is_user_package, $task_id );

			// Redirect to checkout page
			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}// End if().
	}

	/**
	 * Choose package handler
	 *
	 * @return bool
	 */
	public static function choose_package_handler() {
		
		$form = WorkScout_Freelancer_Form_Submit_Task::instance();

		// Validate Selected Package
		$validation = self::validate_package( self::$package_id, self::$is_user_package );

		// Error? Go back to choose package step.
		if ( is_wp_error( $validation ) ) {
			$form->add_error( $validation->get_error_message() );
			$form->set_step( array_search( 'wc-choose-package', array_keys( $form->get_steps() ) ) );
			return false;
		}

		// Store selection in cookie
		wc_setcookie( 'chosen_package_id', self::$package_id );
		wc_setcookie( 'chosen_package_is_user_package', self::$is_user_package ? 1 : 0 );

		// Process the package unless we're doing this before a task is submitted
		if ( 'before' !== get_option('task_paid_listings_flow' ) || 'wc-process-package' === $form->get_step_key() ) {
			// Product the package
			if ( self::process_package( self::$package_id, self::$is_user_package, $form->get_task_id() ) ) {
				$form->next_step();
			}
		} else {
			$form->next_step();
		}
	}
}

WP_Job_Manager_WCPL_Submit_task_Form::init();
