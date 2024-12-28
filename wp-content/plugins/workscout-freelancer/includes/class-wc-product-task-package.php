<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Task Package Product Type
 */
class WC_Product_Task_Package extends WP_Job_Manager_WCPL_Package_Product {

	/**
	 * Constructor
	 *
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'task_package';
	}

	/**
	 * We want to sell jobs one at a time
	 *
	 * @return boolean
	 */
	public function is_sold_individually() {
		return apply_filters( 'wcpl_' . $this->get_type() . '_is_sold_individually', true );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		return apply_filters( 'woocommerce_product_add_to_cart_url', $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() ), $this );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'workscout-freelancer' ) : __( 'Read More', 'workscout-freelancer' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}


	/**
	 * Return listing duration granted
	 *
	 * @return int
	 */
	public function get_duration() {
		$task_duration = $this->get_task_duration();
		if ( $task_duration ) {
			return $task_duration;
		} else {
			return get_option( 'task_submission_duration' );
		}
	}

	/**
	 * Return task limit
	 *
	 * @return int 0 if unlimited
	 */
	public function get_limit() {
		$task_limit = $this->get_task_limit();
		if ( $task_limit ) {
			return $task_limit;
		} else {
			return 0;
		}
	}

	/**
	 * Return if featured
	 *
	 * @return bool true if featured
	 */
	public function is_task_featured() {
		return 'yes' === $this->get_task_featured();
	}

	/**
	 * Get task featured flag
	 *
	 * @return string
	 */
	public function get_task_featured() {
		return $this->get_product_meta( 'listing_featured' );
	}

	/**
	 * Get task limit
	 *
	 * @return int
	 */
	public function get_task_limit() {
		return $this->get_product_meta( 'listing_limit' );
	}

	/**
	 * Get task duration
	 *
	 * @return int
	 */
	public function get_task_duration() {
		return $this->get_product_meta( 'listing_duration' );
	}
}
