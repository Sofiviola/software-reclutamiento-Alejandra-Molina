<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPSight_Meta_Boxes class
 */
class WorkScout_Freelancer_Meta_Boxes {
	/**
	 * Constructor
	 */
	public function __construct() {

		// Add custom meta boxes
		add_action( 'cmb2_admin_init', array( $this, 'add_meta_boxes' ) );
		add_filter('submit_company_form_fields', array($this, 'workscout_frontend_add_company_countries_fields'));
		add_filter('company_manager_company_fields', array($this, 'workscout_backend_add_company_countries_fields'));
		//add_action( 'task_category_add_form_fields', array( $this,'wcf_task_category_add_new_meta_field'), 10, 2 );
		//	add_action( 'task_category_edit_form_fields', array( $this,'wcf_task_category_edit_meta_field'), 10, 2 );

		//	add_action( 'edited_task_category', array( $this,'wcf_save_taxonomy_custom_meta'), 10, 2 );  
		//	add_action( 'created_task_category', array( $this,'wcf_save_taxonomy_custom_meta'), 10, 2 );

		add_filter('submit_resume_form_fields', array($this, 'workscout_freelancer_frontend_add_resume_field'));
		add_filter('resume_manager_resume_fields', array($this, 'workscout_freelancer_admin_add_resume_field'));
		add_filter('resume_manager_resume_links_fields', array($this, 'workscout_freelancer_admin_add_resume_links_field'));
		//add_action( 'cmb2_admin_init', array( $this,'wcf_register_taxonomy_metabox' ) );
		add_filter( 'cmb2_sanitize_checkbox', array( $this, 'sanitize_checkbox'), 10, 2 );

		//resume metaboxes for qualifications
		add_action('add_meta_boxes', [$this, 'add_resume_meta_boxes']);
		add_action('resume_manager_save_resume', [$this, 'save_resume_data'], 1, 2);
	}

	function workscout_backend_add_company_countries_fields($fields){
		if(function_exists('workscoutGetCountries')){
			$fields['_country'] = array(
				'label'       => esc_html__('Country', 'workscout-freelancer'),
				'type'        => 'select',
				'required'    => false,
				'options' 		=>   workscoutGetCountries(),
				'priority'    => 16
			);
		}
		
		return $fields;
	}

	function workscout_frontend_add_company_countries_fields($fields)
	{
		if (function_exists('workscoutGetCountries')) {
		$fields['company_fields']['country'] = array(
			'label'       => esc_html__('Country', 'workscout-freelancer'),
			'type'        => 'select',
			'required'    => false,
			'options' 		=>   workscoutGetCountries(),
			'priority'    => 16
		);
		}


		return $fields;
	}
	
	function sanitize_checkbox( $override_value, $value ) {
	    // Return 0 instead of false if null value given. This hack for
	    // checkbox or checkbox-like can be setting true as default value.
	
	    return is_null( $value ) ? '0' : $value;
	}




	public function add_meta_boxes( ) {
		//TASK 
		$task_admin_options = array(
				'id'           => 'wcf_core_task_admin_metaboxes',
				'title'        => __( 'Task data', 'workscout-freelancer' ),
				'object_types' => array( 'task' ),
				'show_names'   => true,

		);
		$cmb_task_admin = new_cmb2_box( $task_admin_options );

		$cmb_task_admin->add_field( array(
			'name' => __( 'Expiration date', 'workscout-freelancer' ),
			'desc' => '',
			'id'   => '_task_expires',
			'type' => 'text_date',
			'date_format' => 'Y-m-d',
			
		) );

		$cmb_task_admin->add_field( array(
			'name' => __( 'Keywords', 'workscout-freelancer' ),
			'id'   => 'keywords',
			'type' => 'text',
			'desc' => 'Optional keywords used in search',
			
		));  
		if(function_exists('mas_wpjmc')){
			$cmb_task_admin->add_field(
				array(
					'name'       => esc_html__('Company', 'workscout-freelancer'),
					'id'       => '_company_id',
					'type'        => 'select',
					'options'     => mas_wpjmc()->company->job_manager_get_current_user_companies_select_options(),
					'priority'    => 2,
				)
			);

		}
		$cmb_task_admin->add_field(array(
			'name' => __('Task deadline date', 'workscout-freelancer'),
			'desc' => '',
			'id'   => '_task_deadline',
			'type' => 'text',

		));
		

		// Verified 
		$verified_box_options = array(
				'id'           => 'wcf_core_verified_metabox',
				'title'        => __( 'Verified Listing', 'workscout-freelancer' ),
				'context'	   => 'side',
				'priority'     => 'core', 
				'object_types' => array( 'task' ),
				'show_names'   => false,

		);

		// Setup meta box
		$cmb_verified = new_cmb2_box( $verified_box_options );

		$cmb_verified->add_field( array(
			'name' => __( 'Verified', 'workscout-freelancer' ),
			'id'   => '_verified',
			'type' => 'checkbox',
			'desc' => __( 'Tick the checkbox to mark it as Verified', 'workscout-freelancer' ),
		));
		// EOF Verified


		$featured_box_options = array(
				'id'           => 'wcf_core_featured_metabox',
				'title'        => __( 'Featured Listing', 'workscout-freelancer' ),
				'context'	   => 'side',
				'priority'     => 'core', 
				'object_types' => array( 'task' ),
				'show_names'   => false,

		);

		// Setup meta box
		$cmb_featured = new_cmb2_box( $featured_box_options );

		$cmb_featured->add_field( array(
			'name' => __( 'Featured', 'workscout-freelancer' ),
			'id'   => '_featured',
			'type' => 'checkbox',
			'desc' => __( 'Tick the checkbox to make it Featured', 'workscout-freelancer' ),
		));
		

		$advanced_box_options = array(
				'id'           => 'wcf_core_advanced_metabox',
				'title'        => __( 'Advanced meta data Listing', 'workscout-freelancer' ),
				'priority'     => 'core', 
				'object_types' => array( 'task' ),
				'show_names'   => true,

		);

		// Setup meta box
		$cmb_advanced = new_cmb2_box( $advanced_box_options );

		$cmb_advanced->add_field( array(
			'name' => __( 'WooCommerce Product ID', 'workscout-freelancer' ),
			'id'   => 'product_id',
			'type' => 'text',
			'desc' => __( 'WooCommerce Product ID. Don\'t change it unless you know what you are doing:)', 'workscout-freelancer' ),
		));


		$tabs_box_options = array(
				'id'           => 'wcf_tabbed_task_metaboxes',
				'title'        => __( 'Listing fields', 'workscout-freelancer' ),
				'object_types' => array( 'task' ),
				'show_names'   => true,
			);

		// Setup meta box
		$cmb_tabs = new_cmb2_box( $tabs_box_options );

		// setting tabs
		$tabs_setting  = array(
			'config' => $tabs_box_options,
			'layout' => 'vertical', // Default : horizontal
			'tabs'   => array()
		);
		
		$tabs_setting['tabs'] = array( 
			// $this->meta_boxes_main_details(),
			$this->meta_boxes_prices(),
			$this->meta_boxes_video(),
			$this->meta_boxes_custom(),
			 
		);

		// set tabs
		$cmb_tabs->add_field( array(
			'id'   => '_task_tabs',
			'type' => 'tabs',
			'tabs' => $tabs_setting
		) );


		//BIDS Custom Meta Box
		$bid_admin_options = array(
			'id'           => 'wcf_core_bid_admin_metaboxes',
			'title'        => __('Bid data', 'workscout-freelancer'),
			'object_types' => array('bid'),
			'show_names'   => true,

		);
		$cmb_bid_admin = new_cmb2_box($bid_admin_options);

		$cmb_bid_admin->add_field(array(
			'name' => __('Budget', 'workscout-freelancer'),
			'desc' => '',
			'id'   => '_budget',
			'type' => 'text',

		));

		$cmb_bid_admin->add_field(array(
			'name' => __('Time', 'workscout-freelancer'),
			'id'   => '_time',
			'type' => 'text',
			'desc' => 'Time to do the task',

		));
		$cmb_bid_admin->add_field(array(
			'name' => __('Time Scale', 'workscout-freelancer'),
			'id'   => '_time_scale',
			'type' => 'text',
			'desc' => 'Time scale',

		));

		$cmb_tasks = new_cmb2_box(array(
			'id'            => 'workscout_tasks',
			'title'         => esc_html__('Tasks attachments', 'workscout-freelancer'),
			'object_types'  => array('task'), // Post type
			'priority'   	=> 'high',
		));

		$cmb_tasks->add_field(array(

			'name' => __('Task Attachments ', 'workscout-freelancer'),
			'desc' => '',
			'id'   => '_task_file',
			'type' => 'file_list',
			// 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		//	'query_args' => array('type' => 'image'), // Only images attachment
			// Optional, override default text strings
			'text' => array(
				'add_upload_files_text' => __('Add or Upload Files', 'workscout-freelancer'),
			),

		));

	}


	public static function meta_boxes_prices() {
		
		$fields = array(
			'id'     => 'prices_tab',
			'title'  => __( 'Prices fields', 'workscout-freelancer' ),
			'fields' => array(
				array(
					'name' 	=> __( 'Type:', 'workscout-freelancer' ),
					'id'   	=> '_task_type',
					'default'   	=> 'fixed',
					'options' => array(
						'fixed' => __( 'Fixed', 'workscout-freelancer' ),
						'hourly' => __( 'Hourly', 'workscout-freelancer' ),
					),
					'type' 	=> 'radio',					
				),	
				array(
					'name' 	=> __( 'Minimum Budget Range:', 'workscout-freelancer' ),
					'id'   	=> '_budget_min',
					'type' 	=> 'text',					
				),	
				array(
					'name' 	=> __( 'Maximum Budget Range:', 'workscout-freelancer' ),
					'id'   	=> '_budget_max',
					'type' 	=> 'text',
				),							
				array(
					'name' 	=> __( 'Min rate per hour:', 'workscout-freelancer' ),
					'id'   	=> '_hourly_min',
					'type' 	=> 'text',
				),							
				array(
					'name' 	=> __( 'Max rate per hour:', 'workscout-freelancer' ),
					'id'   	=> '_hourly_max',
					'type' 	=> 'text',
				),							
				
			)
		);

		// Set meta box
		return apply_filters( 'wcf_prices_fields', $fields );
	}







	public static function meta_boxes_video() {
		
		$fields = array(
			'id'     => 'video_tab',
			'title'  => __( 'Video', 'workscout-freelancer' ),
			'fields' => array(
				'video' => array(
					'name' => __( 'Video', 'workscout-freelancer' ),
					'id'   => '_video',
					'type' => 'textarea',
					'desc'      => __( 'URL to oEmbed supported service','workscout-freelancer' ),
				),
			
			)
		);
		$fields = apply_filters( 'wcf_video_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function meta_boxes_custom() {
		
		$fields = array(
			'id'     => 'custom_tab',
			'title'  => __( 'Custom fields', 'workscout-freelancer' ),
			'fields' => array(
				'video' => array(
					'name' => __( 'Example field', 'workscout-freelancer' ),
					'id'   => '_example',
					'type' => 'text',
					'desc'      => __( 'Example field description','workscout-freelancer' ),
				),
			
			)
		);
		$fields = apply_filters( 'wcf_custom_fields', $fields );
		
		// Set meta box
		return $fields;
	}

		
	function cmb2_render_opening_hours_wcf_field_callback( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		//var_dump($escaped_value);
		if(is_array($escaped_value)){
			foreach ($escaped_value as $key => $time) {
				echo $field_type_object->input( 
					array( 
						'type' => 'text_time', 
						
						'value' => $time,
						'name'  => $field_type_object->_name( '[]' ),
						
					
						'time_format' => 'H:i',
					) );
					echo "<br>";	
			}
		} else {
			echo $field_type_object->input( 
				array( 
					'type' => 'text', 
					'class' => 'input', 
					'name'  => $field_type_object->_name( '[]' ),

				) );	
		}
		
	}
			

	


	function wcf_register_taxonomy_metabox() {
		$prefix = 'wcf_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$cmb_term = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => esc_html__( 'Listing Taxonomy Meta', 'workscout-freelancer' ), // Doesn't output for term boxes
			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'       => array( 'task_category' ), // Tells CMB2 which taxonomies should have these fields
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		) );


		$cmb_term->add_field( array(
			'name'           => 'Assign Features for this Category',
			'desc'           => 'Features can be created in Listings -> Features',
			'id'             =>  $prefix . 'taxonomy_multicheck',
			'taxonomy'       => 'task_feature', //Enter Taxonomy Slug
			'type'           => 'taxonomy_multicheck',
			// Optional :
			'text'           => array(
				'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
			),
			'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-1
		) );

		
			$cmb_term->add_field( array(
			'name'           => 'Category description',
			'id'             =>  $prefix . 'taxonomy_content_description',
			'type'           => 'textarea',
		) );
	}
	/*
	 * Custom Icon field for Job Categories taxonomy 
	 **/

	// Add term page
	function wcf_task_category_add_new_meta_field() {
		
		?>
		<div class="form-field">
	
			<label for="icon"><?php esc_html_e( 'Category Icon', 'workscout-freelancer' ); ?></label>
				<select class="wcf-icon-select" name="icon" id="icon">
					
				<?php 

				 	// $faicons = wcf_fa_icons_list();
				 	
				  //  	foreach ($faicons as $key => $value) {

				  //  		echo '<option value="fa fa-'.$key.'" ';
				  //  		echo '>'.$value.'</option>';
				  //  	}
			   		$faicons = wcf_fa_icons_list();
				 	
				   	foreach ($faicons as $key => $value) {
				   		if($key){
					   		echo '<option value="fa fa-'.$key.'" ';
					   		if ($icon == 'fa fa-'.$key) { echo ' selected="selected"';}
					   		echo '>'.$value.'</option>';	
				   		}
				   		
				   	}

				   	if(!get_option('wcf_iconsmind')=='hide'){
				   		$imicons = vc_iconpicker_type_iconsmind(array());
				   		
					   	foreach ($imicons as $key => $icon_array ) {
					   		$key = key($icon_array);
					   		$value = $icon_array[$key];
					   		echo '<option value="'.$key.'" ';
					   			if(isset($icon) && $icon == $key) { echo ' selected="selected"';}
					   		echo '>'.$value.'</option>';
					   	}
					}
				   ?>

				</select>
			<p class="description"><?php esc_html_e( 'Icon will be displayed in categories grid view','workscout-freelancer' ); ?></p>
		</div>
		<?php //wp_enqueue_media(); ?>
		<div class="form-field">
			<label for="_cover"><?php esc_html_e( 'Custom Icon (SVG files only)', 'workscout-freelancer' ); ?></label>
			
				
				<input style="width:100px" type="text" name="_icon_svg" id="_icon_svg" value="">
				<input type='button' class="wcf-custom-image-upload button-primary" value="<?php _e( 'Upload SVG Icon', 'workscout-freelancer' ); ?>" id="uploadimage"/><br />
		</div>
		<div class="form-field">
			<label for="_cover"><?php esc_html_e( 'Category Cover', 'workscout-freelancer' ); ?></label>
			<input style="width:100px" type="text" name="_cover" id="_cover" value="">
				<input type='button' class="wcf-custom-image-upload button-primary" value="<?php _e( 'Upload Image', 'workscout-freelancer' ); ?>" id="uploadimage"/><br />
			<p class="description"><?php esc_html_e( 'Similar to the single jobs you can add image to the category header. It should be 1920px wide','workscout-freelancer' ); ?></p>
		</div>

		
			
	<?php
	}
	

	// Edit term page
	function wcf_task_category_edit_meta_field($term) {
	 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		
		?>		
		<tr class="form-field">
			<th scope="row" valign="top">

				<label for="icon"><?php esc_html_e( 'Category Icon', 'workscout-freelancer' ); ?></label>

			<td>
				<select class="wcf-icon-select" name="icon" id="icon">
					<option value="empty">Empty</option>
				<?php 
					$icon = get_term_meta( $t_id, 'icon', true );
 
				 	$faicons = wcf_fa_icons_list();
				 	
				   	foreach ($faicons as $key => $value) {
				   		if($key){
					   		echo '<option value="fa fa-'.$key.'" ';
					   		if ($icon == 'fa fa-'.$key) { echo ' selected="selected"';}
					   		echo '>'.$value.'</option>';	
				   		}
				   		
				   	}

				   	if(!get_option('wcf_iconsmind')=='hide'){
				   		$imicons = vc_iconpicker_type_iconsmind(array());
				   		
					   	foreach ($imicons as $key => $icon_array ) {
					   		$key = key($icon_array);
					   		$value = $icon_array[$key];
					   		echo '<option value="'.$key.'" ';
					   			if(isset($icon) && $icon == $key) { echo ' selected="selected"';}
					   		echo '>'.$value.'</option>';
					   	}
					}
				   ?>

				</select>
				<p class="description"><?php esc_html_e( 'Icon will be displayed in categories grid view','workscout-freelancer' ); ?></p>
			</td>
		</tr>
		<?php wp_enqueue_media(); ?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="_cover"><?php esc_html_e( 'Custom Icon (SVG files only)', 'workscout-freelancer' ); ?></label></th>
			<td>
				<?php 
				$_icon_svg = get_term_meta( $t_id, '_icon_svg', true );
				
				if($_icon_svg) :
					$_icon_svg_image = wp_get_attachment_image_src($_icon_svg,'medium');
					
					if ($_icon_svg_image)  {
						echo '<img src="'.$_icon_svg_image[0].'" style="width:300px;height: auto;"/><br>';
					} 
				endif;
				?>
				<input style="width:100px" type="text" name="_icon_svg" id="_icon_svg" value="<?php echo $_icon_svg; ?>">
				<input type='button' class="wcf-custom-image-upload button-primary" value="<?php _e( 'Upload SVG Icon', 'workscout-freelancer' ); ?>" id="uploadimage"/><br />
				<p>We recommend using outline icons from <a href="https://www.iconfinder.com/search/?price=free&style=outline">iconfinder.com</a></p>
			</td>
		</tr>	

		<tr class="form-field">
			<th scope="row" valign="top"><label for="_cover"><?php esc_html_e( 'Category Cover', 'workscout-freelancer' ); ?></label></th>
			<td>
				<?php 
				$cover = get_term_meta( $t_id, '_cover', true );
				
				if($cover) :
					$cover_image = wp_get_attachment_image_src($cover,'medium');
					
					if ($cover_image)  {
						echo '<img src="'.$cover_image[0].'" style="width:300px;height: auto;"/><br>';
					} 
				endif;
				?>
				<input style="width:100px" type="text" name="_cover" id="_cover" value="<?php echo $cover; ?>">
				<input type='button' class="wcf-custom-image-upload button-primary" value="<?php _e( 'Upload Image', 'workscout-freelancer' ); ?>" id="uploadimage"/><br />
			</td>
		</tr>
	<?php
	}


	// Save extra taxonomy fields callback function.
	function wcf_save_taxonomy_custom_meta( $term_id, $tt_id ) {


		if( isset( $_POST['icon'] ) && '' !== $_POST['icon'] ){
	        $icon = $_POST['icon'];

	        update_term_meta( $term_id, 'icon', $icon );
	    }

	    if( isset( $_POST['_cover'] ) && '' !== $_POST['_cover'] ){
	        $cover = sanitize_title( $_POST['_cover'] );
	        update_term_meta( $term_id, '_cover', $cover );
	    } 

	    if( isset( $_POST['_icon_svg'] ) && '' !== $_POST['_icon_svg'] ){
	        $_icon_svg = sanitize_title( $_POST['_icon_svg'] );
	        update_term_meta( $term_id, '_icon_svg', $_icon_svg );
	    }
		
	}  

	function cmb2_render_callback_for_datetime( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		echo $field_type_object->input( array( 'type' => 'text', 'class' => 'input-datetime' ) );
	}


	public static function meta_boxes_user_owner(){

		$fields = array(
				'phone' => array(
					'id'                => 'phone',
					'name'              => __( 'Phone', 'workscout-freelancer' ),
					'label'             => __( 'Phone', 'workscout-freelancer' ),
					'type'              => 'text',
					
				),
				'header_social' => array(
					'label'       => __( 'Social', 'workscout-freelancer' ),
					'type'        => 'header',
					'id'          => 'header_social',
					'name'        => __( 'Social', 'workscout-freelancer' ),
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( '<i class="fa fa-twitter"></i> Twitter', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-twitter"></i> Twitter', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'facebook' => array(
					'id'                => 'facebook',
					'name'              => __( '<i class="fa fa-facebook-square"></i> Facebook', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-facebook-square"></i> Facebook', 'workscout-freelancer' ),
					'type'              => 'text',
				),				
			
				'linkedin' => array(
					'id'                => 'linkedin',
					'name'              => __( '<i class="fa fa-linkedin"></i> Linkedin', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-linkedin"></i> Linkedin', 'workscout-freelancer' ),
					'type'              => 'text',
					
				),	
				'instagram' => array(
					'id'                => 'instagram',
					'name'              => __( '<i class="fa fa-instagram"></i> Instagram', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-instagram"></i> Instagram', 'workscout-freelancer' ),
					'type'              => 'text',
				),				
				'youtube' => array(
					'id'                => 'youtube',
					'name'              => __( '<i class="fa fa-youtube"></i> YouTube', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-youtube"></i> YouTube', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'skype' => array(
					'id'                => 'skype',
					'name'              => __( '<i class="fa fa-skype"></i> Skype', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-skype"></i> Skype', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'whatsapp' => array(
					'id'                => 'whatsapp',
					'name'              => __( '<i class="fa fa-skype"></i> Whatsapp', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-skype"></i> Whatsapp', 'workscout-freelancer' ),
					'type'              => 'text',
				),
			);
		$fields = apply_filters( 'wcf_user_owner_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function meta_boxes_user_guest(){

		$fields = array(
				'phone' => array(
					'id'                => 'phone',
					'name'              => __( 'Phone', 'workscout-freelancer' ),
					'label'             => __( 'Phone', 'workscout-freelancer' ),
					'type'              => 'text',
					
				),
				'header_social' => array(
					'label'       => __( 'Social', 'workscout-freelancer' ),
					'type'        => 'header',
					'id'          => 'header_social',
					'name'        => __( 'Social', 'workscout-freelancer' ),
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( '<i class="fa fa-twitter"></i> Twitter', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-twitter"></i> Twitter', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'facebook' => array(
					'id'                => 'facebook',
					'name'              => __( '<i class="fa fa-facebook-square"></i> Facebook', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-facebook-square"></i> Facebook', 'workscout-freelancer' ),
					'type'              => 'text',
				),				
			
				'linkedin' => array(
					'id'                => 'linkedin',
					'name'              => __( '<i class="fa fa-linkedin"></i> Linkedin', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-linkedin"></i> Linkedin', 'workscout-freelancer' ),
					'type'              => 'text',
					
				),	
				'instagram' => array(
					'id'                => 'instagram',
					'name'              => __( '<i class="fa fa-instagram"></i> Instagram', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-instagram"></i> Instagram', 'workscout-freelancer' ),
					'type'              => 'text',
				),				
				'youtube' => array(
					'id'                => 'youtube',
					'name'              => __( '<i class="fa fa-youtube"></i> YouTube', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-youtube"></i> YouTube', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'skype' => array(
					'id'                => 'skype',
					'name'              => __( '<i class="fa fa-skype"></i> Skype', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-skype"></i> Skype', 'workscout-freelancer' ),
					'type'              => 'text',
				),
				'whatsapp' => array(
					'id'                => 'whatsapp',
					'name'              => __( '<i class="fa fa-skype"></i> Whatsapp', 'workscout-freelancer' ),
					'label'             => __( '<i class="fa fa-skype"></i> Whatsapp', 'workscout-freelancer' ),
					'type'              => 'text',
				),
			);
		$fields = apply_filters( 'wcf_user_guest_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	
	function workscout_freelancer_frontend_add_resume_field($fields){
		$fields['resume_fields']['candidate_photo']['priority'] = 13 ;
		$fields['resume_fields']['competencies']  = [
						'label'         => __('Competency Bars', 'workscout-freelancer' ),
						'add_row'       => __('Add competency', 'workscout-freelancer' ),
						'type'          => 'repeated', // Repeated field.
						'required'      => false,
						'placeholder'   => '',
						'priority'      => 11,
						'fields'        => [
							'skill'      => [
								'label'       => __( 'Skill', 'workscout-freelancer' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => '',
							],
							'qualification' => [
								'label'       => __( 'Level (0-100%)', 'workscout-freelancer' ),
							//	'type'        => 'select',
								//array from 0 to 100 every 5	)
							//	'options' => array_combine(range(0,100,5), range(0,100,5)),
								'type'        => 'number',
								'min' 		=>0,
								'max' 		=>100,
								'required'    => true,
								'placeholder' => '',
							],
							
						],
						'personal_data' => true,
					];
		if (function_exists('workscoutGetCountries')) {
		$fields['resume_fields']['country'] = array(
			'label'       => esc_html__('Country', 'workscout-freelancer'),
			'type'        => 'select',
			'required'    => false,
			'options' 		=>   workscoutGetCountries(),
			'priority'    => 4
		);
		}
		// $fields['resume_fields']['set_as_profile'] = array(
		// 	'label'       => esc_html__('Set this resume as your profile', 'workscout-freelancer'),
		// 	'type'        => 'checkbox',
		// 	'required'    => false,
			
		// 	'priority'    => 16
		// );
		$fields['resume_fields']['gallery'] = array(
			'label'       => esc_html__('Gallery', 'workscout-freelancer'),
			'type'        => 'gallery',
			'required'    => false,
			
			'priority'    => 5
		);
		$fields['resume_fields']['header_image'] = array(
			'label'       => __('Header Image', 'workscout-freelancer'),
			'type'        => 'file',
			'required'    => false,

			'priority'    => 13,
			'ajax'        => true,
			'multiple'    => false,
			'allowed_mime_types' => array(
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'gif'  => 'image/gif',
				'png'  => 'image/png'
			)
		);
		$fields['resume_fields']['links']['label'] = __( 'Social Sites', 'workscout-freelancer' );
		$fields['resume_fields']['links']['add_row'] = __( 'Add Site', 'workscout-freelancer' );
		$fields['resume_fields']['links']['fields'] =  [
							'name' => [
								'label'       => __( 'Service', 'workscout-freelancer' ),
								'type'        => 'select',
								'options' 	  => workscoutBrandIcons(),
								'required'    => true,
								'placeholder' => '',
								'priority'    => 1,
							],
							'url'  => [
								'label'       => __( 'URL', 'workscout-freelancer' ),
								'type'        => 'text',
								'sanitizer'   => 'esc_url_raw',
								'required'    => true,
								'placeholder' => '',
								'priority'    => 2,
							],
						];
		// 'links'                => [
		// 				'label'         => __( 'URL(s)', 'workscout-freelancer' ),
		// 				'add_row'       => __( 'Add URL', 'workscout-freelancer' ),
		// 				'type'          => 'links', // Repeated field.
		// 				'required'      => false,
		// 				'placeholder'   => '',
		// 				'description'   => __( 'Optionally provide links to any of your websites or social network profiles.', 'workscout-freelancer' ),
		// 				'priority'      => 10,
		// 				'fields'        => [
		// 					'name' => [
		// 						'label'       => __( 'Name', 'workscout-freelancer' ),
		// 						'type'        => 'text',
		// 						'required'    => true,
		// 						'placeholder' => '',
		// 						'priority'    => 1,
		// 					],
		// 					'url'  => [
		// 						'label'       => __( 'URL', 'workscout-freelancer' ),
		// 						'type'        => 'text',
		// 						'sanitizer'   => 'esc_url_raw',
		// 						'required'    => true,
		// 						'placeholder' => '',
		// 						'priority'    => 2,
		// 					],
		// 				],
		// 				'personal_data' => true,
		// 			],

		return $fields;
	}

	function workscout_freelancer_admin_add_resume_field($fields){
		if (function_exists('workscoutGetCountries')) {
		$fields['_country'] = array(
			'label'       => esc_html__('Country', 'workscout-freelancer'),
			'type'        => 'select',
			'required'    => false,
			'options' 		=>   workscoutGetCountries(),
			'priority'    => 16
		);
		}
		$fields['_header_image'] = array(
			'label'       => __('Header Image', 'workscout-freelancer'),
			'type'        => 'file',
			'required'    => false,

			'priority'    => 13,
			'ajax'        => true,
			'multiple'    => false,
			'allowed_mime_types' => array(
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'gif'  => 'image/gif',
				'png'  => 'image/png'
			)
		);
		// $fields['_gallery'] = array(
		// 	'label'       => esc_html__('Gallery', 'workscout-freelancer'),
		// 	'type'        => 'text',
		// 	'required'    => false,

		// 	'priority'    => 5
		// );

		return $fields;
	}
	function workscout_freelancer_admin_add_resume_links_field($fields)
	{



		$fields =  [
			'name' => [
				'label'       => __('Service', 'workscout-freelancer'),
				'type'        => 'select',
				'name'        => 'resume_url_name[]',
				'options' 	  => workscoutBrandIcons(),
				'required'    => true,
				'placeholder' => '',
				'priority'    => 1,
			],
			'url'  => [
				'label'       => __('URL', 'workscout-freelancer'),
				'type'        => 'text',
				'name'        => 'resume_url[]',
				'required'    => true,
				'placeholder' => '',
				'priority'    => 2,
			],
		];
		return $fields;
	}
	//resume_manager_resume_links_fields
	/**
	 * Resume fields
	 *
	 * @return array
	 */
	public static function resume_competencies_fields()
	{
		return apply_filters(
			'resume_manager_resume_competencies_fields',
			[
				'skill'  => [
					'label'       => __('Skill', 'workscout-freelancer'),
					'name'        => 'competencies_skill[]',
					'placeholder' => '',
					'description' => '',
					'required'    => true,
				],
				'qualification' => [
					'label'       => __('Level ( 0-100%)', 'workscout-freelancer'),
					'name'        => 'competencies_qualification[]',
					'placeholder' => '',
					// 'type'       =>'select',
					// 'options' => array_combine(range(0,100,5), range(0,100,5)),
				
					'type'        => 'text',
					'min' 		=> 0,
					'max' 		=> 100,
					'description' => '',
				],
				
			]
		);
	}

	function add_resume_meta_boxes(){
		add_meta_box('resume_competencies_data', __('Competency Bars', 'workscout-freelancer'), [$this, 'competencies_data'], 'resume', 'normal', 'high');
	}

	/**
	 * Resume Education data
	 *
	 * @param mixed $post
	 */
	public function competencies_data($post)
	{
		$fields = $this->resume_competencies_fields();
		WP_Resume_Manager_Writepanels::repeated_rows_html(__('Competency', 'workscout-freelancer'), $fields, get_post_meta($post->ID, '_competencies', true));
	}

	public function save_resume_data($post_id, $post)
	{
		global $wpdb;
		$save_repeated_fields = [
			'_competencies'                => $this->resume_competencies_fields(),
		
		];

		foreach ($save_repeated_fields as $meta_key => $fields) {
			WP_Resume_Manager_Writepanels::save_repeated_row($post_id, $meta_key, $fields);
		}
	}

}