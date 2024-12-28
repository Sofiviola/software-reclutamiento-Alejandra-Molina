<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart
 */
class Workscout_Freelancer_Paid_Properties_Cart {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_task_package_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
		add_action( 'woocommerce_task_package_subscription_add_to_cart', 'WCS_Template_Loader::get_subscription_add_to_cart', 30 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'checkout_create_order_line_item' ), 10, 4 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		add_filter('woocommerce_cart_id', [$this, 'remove_job_data_from_cart_id'], 10, 5);

		// Force reg during checkout process
		add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', array( $this, 'enable_signup_and_login_from_checkout' ) );
		add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
	}

	/**
	 * Checks an cart to see if it contains a task_package.
	 *
	 * @return bool|null
	 */
	public function cart_contains_task_package() {
		global $woocommerce;

		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $product instanceof WC_Product && $product->is_type( 'task_package' ) && ! $product->is_type( 'task_package_subscription' )) {
					return true;
				}
			}
		}
	}


	/**
	 * Ensure this is yes
	 *
	 * @param string $value
	 * @return string
	 */
	public function enable_signup_and_login_from_checkout( $value ) {
		remove_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
		$woocommerce_enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' );
		add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );

		if ( 'yes' === $woocommerce_enable_guest_checkout && ( $this->cart_contains_task_package() ) ) {
			return 'yes';
		} else {
			return $value;
		}
	}

	/**
	 * Ensure this is no
	 *
	 * @param string $value
	 * @return string
	 */
	public function enable_guest_checkout( $value ) {
		if ( $this->cart_contains_task_package() ) {
			return 'no';
		} else {
			return $value;
		}
	}

	/**
	 * Get the data from the session on page load
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['task_id'] ) ) {
			$cart_item['task_id'] = $values['task_id'];
		}
	
		return $cart_item;
	}



	/**
	 * Set the order line item's meta data prior to being saved (WC >= 3.0.0).
	 *
	 * @since 2.7.3
	 *
	 * @param WC_Order_Item_Product $order_item
	 * @param string                $cart_item_key  The hash used to identify the item in the cart
	 * @param array                 $cart_item_data The cart item's data.
	 * @param WC_Order              $order          The order or subscription object to which the line item relates
	 */
	public function checkout_create_order_line_item( $order_item, $cart_item_key, $cart_item_data, $order ) {
		if ( isset( $cart_item_data['task_id'] ) ) {
			$listing = get_post( absint( $cart_item_data['task_id'] ) );

			$order_item->update_meta_data( __( 'Listing title', 'workscout-freelancer' ), $listing->post_title );
			$order_item->update_meta_data( '_task_id', $cart_item_data['task_id']  );
		}
		
	}

	/**
	 * Output listing name in cart
	 *
	 * @param  array $data
	 * @param  array $cart_item
	 * @return array
	 */
	public function get_item_data( $data, $cart_item ) {
		if ( isset( $cart_item['task_id'] ) ) {
			$listing = get_post( absint( $cart_item['task_id'] ) );
			$title  = (isset($listing->post_title)) ? $listing->post_title : '' ;
			$data[] = array(
				'name'  => __( 'Listing title', 'workscout-freelancer' ),
				'value' => $title,
			);
		}
	
		return $data;
	}

	/**
	 * Generates a cart id which does not take into account cart item data. Without this filter, each new job or resume
	 * submission would create a unique cart id which in turn would allow multiple items of the same product to be added
	 * to the cart.
	 *
	 * @see WC_Cart::generate_cart_id
	 *
	 * @param string $cart_id        Default cart id.
	 * @param int    $product_id     Id of the product.
	 * @param int    $variation_id   Variation id.
	 * @param array  $variation      Variation data for the cart item.
	 * @param array  $cart_item_data Other cart item data passed which affects this items uniqueness in the cart.
	 *
	 * @return string
	 */
	public function remove_job_data_from_cart_id($cart_id, $product_id, $variation_id, $variation, $cart_item_data)
	{
		$package = wc_get_product($product_id);

		if (!$package || !in_array($package->get_type(), ['task_package', 'task_package_subscription'])) {
			return $cart_id;
		}

		$id_parts = array($product_id);

		if ($variation_id && 0 !== $variation_id) {
			$id_parts[] = $variation_id;
		}

		if (is_array($variation) && !empty($variation)) {
			$variation_key = '';
			foreach ($variation as $key => $value) {
				$variation_key .= trim($key) . trim($value);
			}
			$id_parts[] = $variation_key;
		}

		return md5(implode('_', $id_parts));
	}
}
Workscout_Freelancer_Paid_Properties_Cart::get_instance();
