<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 *  class
 */
class Workscout_Freelancer_Paid_Properties {
	
	/**
	 * Returns static instance of class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 */
	public function __construct() {

		/* Hooks */
		add_action( 'woocommerce_product_options_general_product_data', array( $this,  'workscout_core_add_custom_settings' ) );
		add_action( 'woocommerce_process_product_meta_task_package', array( $this, 'save_package_data' ) );
		add_action( 'woocommerce_process_product_meta_task_package_subscription', array( $this, 'save_package_data' ) );
		

		add_filter( 'woocommerce_subscription_product_types', array( $this, 'woocommerce_subscription_product_types' ) );
		/* Includes */
		include_once( 'class-workscout-freelancer-paid-listings-orders.php' );
		include_once( 'class-workscout-freelancer-paid-listings-package.php' );
		include_once( 'class-workscout-freelancer-paid-listings-cart.php' );

	}


	/**
	 * Types for subscriptions
	 *
	 * @param  array $types
	 * @return array
	 */
	public function woocommerce_subscription_product_types( $types ) {
		$types[] = 'task_package_subscription';
		return $types;
	}

	function workscout_core_add_custom_settings() {
	    global $woocommerce, $post;
	    echo '<div class="options_group show_if_task_package show_if_task_package_subscription">';


	    // Create a number field, for example for UPC
	     woocommerce_wp_text_input( array(
			'id' 				=> '_listing_limit',
			'label' 			=> __( 'Listing limit', 'workscout-freelancer' ),
			'description' 		=> __( 'The number of listings a user can post with this package.', 'workscout-freelancer' ),
			'value' 			=> ( $limit = get_post_meta( $post->ID, '_listing_limit', true ) ) ? $limit : '',
			'placeholder' 		=> __( 'Unlimited', 'workscout-freelancer' ),
			'type' 				=> 'number',
			'desc_tip' 			=> true,
			'custom_attributes' => array(
			'min'   			=> '',
			'step' 				=> '1',
			),
		) ); 

	    woocommerce_wp_text_input( array(
			'id' 				=> '_listing_duration',
			'label' 			=> __( 'Listing duration', 'workscout-freelancer' ),
			'description' 		=> __( 'The number of days that the listing will be active.', 'workscout-freelancer' ),
			'value' 			=> get_post_meta(  $post->ID, '_listing_duration', true ),
			'placeholder' 		=> get_option( 'job_manager_submission_duration' ),
			'desc_tip' 			=> true,
			'type' 				=> 'number',
			'custom_attributes' => array(
			'min'  				=> '',
			'step' 				=> '1',
			),
		) );

		woocommerce_wp_checkbox( array(
			'id' => '_listing_featured',
			'label' => __( 'Feature Listing?', 'workscout-freelancer' ),
			'description' => __( 'Feature this listing - it will have a badge and sticky status.', 'workscout-freelancer' ),
			'value' => get_post_meta(  $post->ID, '_listing_featured', true ),
		) ); 		


	
		

	    echo '</div>';
	    ?>
	    <script type="text/javascript">
		jQuery(function(){
			jQuery('#product-type').change( function() {
				jQuery('#woocommerce-product-data').removeClass(function(i, classNames) {
					var classNames = classNames.match(/is\_[a-zA-Z\_]+/g);
					if ( ! classNames ) {
						return '';
					}
					return classNames.join(' ');
				});
				jQuery('#woocommerce-product-data').addClass( 'is_' + jQuery(this).val() );
			} );
			jQuery('.pricing').addClass( 'show_if_task_package' );
			jQuery('._tax_status_field').closest('div').addClass( 'show_if_task_package' ).addClass( 'show_if_task_package_subscription' );
			
			jQuery('.show_if_subscription, .options_group.pricing').addClass( 'show_if_task_package_subscription' );
			jQuery('.options_group.pricing ._regular_price_field').addClass( 'hide_if_task_package_subscription' );
			
			jQuery('#product-type').change();
			jQuery('#_task_package_subscription_type').change(function(){
				if ( jQuery(this).val() === 'listing' ) {
					jQuery('#_listing_duration').closest('.form-field').hide().val('');
				} else {
					jQuery('#_listing_duration_duration').closest('.form-field').show();
				}		
			}).change();
			
		});
	</script>
	<?php
	}

	/**
	 * Save Job Package data for the product
	 *
	 * @param  int $post_id
	 */
	public function save_package_data( $post_id ) {
		global $wpdb;

		// Save meta
		$meta_to_save = array(
			'_listing_duration'             	=> '',
			'_listing_limit'                	=> 'int',
			'_listing_featured'             	=> 'yesno',
		);

		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
			switch ( $sanitize ) {
				case 'int' :
					$value = absint( $value );
					break;
				case 'float' :
					$value = floatval( $value );
					break;
				case 'yesno' :
					$value = $value == 'yes' ? 'yes' : 'no';
					break;
				default :
					$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $meta_key, $value );
		}

		$_package_subscription_type = ! empty( $_POST['_task_package_subscription_type'] ) ? $_POST['task_package_subscription_type'] : 'package';
		update_post_meta( $post_id, '_package_subscription_type', $_package_subscription_type );

	}

}

new Workscout_Freelancer_Paid_Properties();



