<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * Hireo  class.
 */
class WorkScout_Freelancer_CPT
{

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26
	 * @static
	 * @return self Main instance.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('init', array($this, 'register_task_post_types'), 5);
		add_action('init', array($this, 'register_bid_post_types'), 5);
		//add_action('init', array($this, 'register_project_post_types'), 5);

		add_action('workscout_freelancer_check_for_expired_tasks', array($this, 'workscout_freelancer_check_for_expired_tasks'));
		

	}

	
	function set_default_featured($post_id)
	{
		add_post_meta($post_id, '_featured', '0', true);
	}


	/**
	 * Get the permalink settings directly from the option.
	 *
	 * @return array Permalink settings option.
	 */
	public static function get_raw_permalink_settings()
	{
		/**
		 * Option `wpjm_permalinks` was renamed to match other options in 1.32.0.
		 *
		 * Reference to the old option and support for non-standard plugin updates will be removed in 1.34.0.
		 */
		$legacy_permalink_settings = '[]';
		if (false !== get_option('wsf_permalinks', false)) {
			$legacy_permalink_settings = wp_json_encode(get_option('wsf_permalinks', array()));
			delete_option('wsf_permalinks');
		}

		return (array) json_decode(get_option('wsf_core_task_permalinks', $legacy_permalink_settings), true);
	}

	/**
	 * Retrieves permalink settings.
	 *
	 * @see https://github.com/woocommerce/woocommerce/blob/3.0.8/includes/wc-core-functions.php#L1573
	 * @since 1.28.0
	 * @return array
	 */
	public static function get_permalink_structure()
	{
		// Switch to the site's default locale, bypassing the active user's locale.
		if (function_exists('switch_to_locale') && did_action('admin_init')) {
			switch_to_locale(get_locale());
		}

		$permalink_settings = self::get_raw_permalink_settings();

		// First-time activations will get this cleared on activation.
		if (!array_key_exists('tasks_archive', $permalink_settings)) {
			// Create entry to prevent future checks.
			$permalink_settings['tasks_archive'] = '';

			// This isn't the first activation and the theme supports it. Set the default to legacy value.
			$permalink_settings['tasks_archive'] = _x('tasks', 'Post type archive slug - resave permalinks after changing this', 'workscout-freelancer');

			update_option('wsf_task_permalinks', wp_json_encode($permalink_settings));
		}

		$permalinks         = wp_parse_args(
			$permalink_settings,
			array(
				'task_base'      => '',
				'task_category_base' => '',
				'tasks_archive'  => '',
			)
		);

		// Ensure rewrite slugs are set. Use legacy translation options if not.
		$permalinks['task_rewrite_slug']          = untrailingslashit(empty($permalinks['task_base']) ? _x('task', 'Task permalink - resave permalinks after changing this', 'workscout-freelancer') : $permalinks['task_base']);
		$permalinks['task_category_rewrite_slug']     = untrailingslashit(empty($permalinks['task_category_base']) ? _x('task-category', 'Task Listing category slug - resave permalinks after changing this', 'workscout-freelancer') : $permalinks['category_base']);

		$permalinks['tasks_archive_rewrite_slug'] = untrailingslashit(empty($permalinks['tasks_archive']) ? 'tasks' : $permalinks['tasks_archive']);

		// Restore the original locale.
		if (function_exists('restore_current_locale') && did_action('admin_init')) {
			restore_current_locale();
		}
		return $permalinks;
	}




	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_task_post_types()
	{
		/*
		if ( post_type_exists( "task" ) )
			return;*/

		// Custom admin capability
		$admin_capability = 'edit_listing';
		$permalink_structure = self::get_permalink_structure();


		// Set labels and localize them

		$task_name		= apply_filters('wsf_cpt_task_name', __('Tasks', 'workscout-freelancer'));
		$task_singular	= apply_filters('wsf_cpt_task_singular', __('Task', 'workscout-freelancer'));

		register_post_type(
			"task",
			apply_filters("register_post_type_task", array(
				'labels' => array(
					'name'					=> $task_name,
					'singular_name' 		=> $task_singular,
					'menu_name'             => esc_html__('Tasks', 'workscout-freelancer'),
					'all_items'             => sprintf(esc_html__('All %s', 'workscout-freelancer'), $task_name),
					'add_new' 				=> esc_html__('Add New', 'workscout-freelancer'),
					'add_new_item' 			=> sprintf(esc_html__('Add %s', 'workscout-freelancer'), $task_singular),
					'edit' 					=> esc_html__('Edit', 'workscout-freelancer'),
					'edit_item' 			=> sprintf(esc_html__('Edit %s', 'workscout-freelancer'), $task_singular),
					'new_item' 				=> sprintf(esc_html__('New %s', 'workscout-freelancer'), $task_singular),
					'view' 					=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $task_singular),
					'view_item' 			=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $task_singular),
					'search_items' 			=> sprintf(esc_html__('Search %s', 'workscout-freelancer'), $task_name),
					'not_found' 			=> sprintf(esc_html__('No %s found', 'workscout-freelancer'), $task_name),
					'not_found_in_trash' 	=> sprintf(esc_html__('No %s found in trash', 'workscout-freelancer'), $task_name),
					'parent' 				=> sprintf(esc_html__('Parent %s', 'workscout-freelancer'), $task_singular),
				),
				'description' => sprintf(esc_html__('This is where you can create and manage %s.', 'workscout-freelancer'), $task_name),
				'public' 				=> true,
				'show_ui' 				=> true,
				'show_in_rest' 			=> true,
				'capability_type' 		=> array('job_listing', 'job_listings'),
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'menu_icon'           => 'dashicons-clipboard',
				'rewrite' 				=> array(
					'slug'       => $permalink_structure['task_rewrite_slug'],
					'with_front' => true,
					'feeds'      => true,
					'pages'      => true
				),
				'query_var' 			=> true,
				'supports' 				=> array('title', 'author', 'editor', 'excerpt', 'custom-fields', 'publicize', 'thumbnail', 'comments'),
				'has_archive' 			=> $permalink_structure['tasks_archive_rewrite_slug'],
				'show_in_nav_menus' 	=> true
			))
		);


		register_post_status('preview', array(
			'label'                     => _x('Preview', 'post status', 'workscout-freelancer'),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'workscout-freelancer'),
		));

		register_post_status('expired', array(
			'label'                     => _x('Expired', 'post status', 'workscout-freelancer'),
			'public'                    => false,
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'workscout-freelancer'),
		));

		register_post_status('pending_payment', array(
			'label'                     => _x('Pending Payment', 'post status', 'workscout-freelancer'),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'workscout-freelancer'),
		));
		register_post_status('in_progress', array(
			'label'                     => _x('In progress', 'post status', 'workscout-freelancer'),
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('In progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>', 'workscout-freelancer'),
		));
		register_post_status('completed', array(
			'label'                     => _x('Completed', 'post status', 'workscout-freelancer'),
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'workscout-freelancer'),
		));



		// Register taxonomy "Job Listing Categry"
		$singular  = __('Task Category', 'workscout-freelancer');
		$plural    = __('Task Categories', 'workscout-freelancer');
		$rewrite   = array(
			'slug'         => $permalink_structure['task_category_rewrite_slug'],
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy(
			"task_category",
			apply_filters('register_taxonomy_task_category_object_type', array('task')),
			apply_filters('register_taxonomy_task_category_args', array(
				'hierarchical' 			=> true,
				/*'update_count_callback' => '_update_post_term_count',*/
				'label' 				=> $plural,
				'show_in_rest' => true,
				'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords($plural),
					'search_items'      => sprintf(__('Search %s', 'workscout-freelancer'), $plural),
					'all_items'         => sprintf(__('All %s', 'workscout-freelancer'), $plural),
					'parent_item'       => sprintf(__('Parent %s', 'workscout-freelancer'), $singular),
					'parent_item_colon' => sprintf(__('Parent %s:', 'workscout-freelancer'), $singular),
					'edit_item'         => sprintf(__('Edit %s', 'workscout-freelancer'), $singular),
					'update_item'       => sprintf(__('Update %s', 'workscout-freelancer'), $singular),
					'add_new_item'      => sprintf(__('Add New %s', 'workscout-freelancer'), $singular),
					'new_item_name'     => sprintf(__('New %s Name', 'workscout-freelancer'),  $singular)
				),
				'show_ui' 				=> true,
				'show_tagcloud'			=> false,
				'public' 	     		=> $public,
				/*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
				'rewrite' 				=> $rewrite,
			))
		);


		// Register taxonomy "Features"
		$singular  = __('Task Skill', 'workscout-freelancer');
		$plural    = __('Task Skill', 'workscout-freelancer');
		$rewrite   = array(
			'slug'         => _x('task-skill', 'Task Skills slug - resave permalinks after changing this', 'workscout-freelancer'),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy(
			"task_skill",
			apply_filters('register_taxonomy_task_types_object_type', array('task')),
			apply_filters('register_taxonomy_task_types_args', array(
				'hierarchical' 			=> true,
				/*'update_count_callback' => '_update_post_term_count',*/
				'label' 				=> $plural,
				'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords($plural),
					'search_items'      => sprintf(__('Search %s', 'workscout-freelancer'), $plural),
					'all_items'         => sprintf(__('All %s', 'workscout-freelancer'), $plural),
					'parent_item'       => sprintf(__('Parent %s', 'workscout-freelancer'), $singular),
					'parent_item_colon' => sprintf(__('Parent %s:', 'workscout-freelancer'), $singular),
					'edit_item'         => sprintf(__('Edit %s', 'workscout-freelancer'), $singular),
					'update_item'       => sprintf(__('Update %s', 'workscout-freelancer'), $singular),
					'add_new_item'      => sprintf(__('Add New %s', 'workscout-freelancer'), $singular),
					'new_item_name'     => sprintf(__('New %s Name', 'workscout-freelancer'),  $singular)
				),
				'show_ui' 				=> true,
				'show_in_rest' => true,
				'show_tagcloud'			=> false,
				'public' 	     		=> $public,
				/*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
				'rewrite' 				=> $rewrite,
			))
		);
		// Register taxonomy "Features"
	
		
	} /* eof register*/


	public function register_project_post_types()
	{
		/*
		if ( post_type_exists( "task" ) )
			return;*/

		// Custom admin capability
		$admin_capability = 'edit_listing';
		$permalink_structure = self::get_permalink_structure();

		// Set labels and localize them

		$project_name		= apply_filters('wsf_cpt_project_name', __('Projects', 'workscout-freelancer'));
		$project_singular	= apply_filters('wsf_cpt_project_singular', __('Project', 'workscout-freelancer'));

		register_post_type(
			"project",
			apply_filters("register_post_type_project", array(
				'labels' => array(
					'name'					=> $project_name,
					'singular_name' 		=> $project_singular,
					'menu_name'             => esc_html__('Projects', 'workscout-freelancer'),
					'all_items'             => sprintf(esc_html__('All %s', 'workscout-freelancer'), $project_name),
					'add_new' 				=> esc_html__('Add New', 'workscout-freelancer'),
					'add_new_item' 			=> sprintf(esc_html__('Add %s', 'workscout-freelancer'), $project_singular),
					'edit' 					=> esc_html__('Edit', 'workscout-freelancer'),
					'edit_item' 			=> sprintf(esc_html__('Edit %s', 'workscout-freelancer'), $project_singular),
					'new_item' 				=> sprintf(esc_html__('New %s', 'workscout-freelancer'), $project_singular),
					'view' 					=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $project_singular),
					'view_item' 			=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $project_singular),
					'search_items' 			=> sprintf(esc_html__('Search %s', 'workscout-freelancer'), $project_name),
					'not_found' 			=> sprintf(esc_html__('No %s found', 'workscout-freelancer'), $project_name),
					'not_found_in_trash' 	=> sprintf(esc_html__('No %s found in trash', 'workscout-freelancer'), $project_name),
					'parent' 				=> sprintf(esc_html__('Parent %s', 'workscout-freelancer'), $project_singular),
				),
				'description' => sprintf(esc_html__('This is where you can create and manage %s.', 'workscout-freelancer'), $project_name),
				'public' 				=> false,
				'show_ui' 				=> true,
				'show_in_rest' 			=> true,
				'capability_type' 		=> array('job_listing', 'job_listings'),
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> false,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'menu_icon'           => 'dashicons-clipboard',
				'rewrite' 				=> array(
					'slug'       => 'project',
					'with_front' => false,
					'feeds'      => true,
					'pages'      => true
				),
				'query_var' 			=> true,
				'supports' 				=> array('title', 'author', 'editor', 'excerpt', 'custom-fields', 'publicize', 'thumbnail', 'comments'),
				'has_archive' 			=> false,
				'show_in_nav_menus' 	=> true
			))
		);

	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_bid_post_types()
	{
		/*
		if ( post_type_exists( "task" ) )
			return;*/

		// Custom admin capability
		$admin_capability = 'edit_listing';
		$permalink_structure = self::get_permalink_structure();


		// Set labels and localize them

		$bid_name		= apply_filters('wsf_cpt_bid_name', __('Bids', 'workscout-freelancer'));
		$bid_singular	= apply_filters('wsf_cpt_bid_singular', __('Bid', 'workscout-freelancer'));

		register_post_type(
			"bid",
			apply_filters("register_post_type_task", array(
				'labels' => array(
					'name'					=> $bid_name,
					'singular_name' 		=> $bid_singular,
					'menu_name'             => esc_html__('Bids', 'workscout-freelancer'),
					'all_items'             => sprintf(esc_html__('All %s', 'workscout-freelancer'), $bid_name),
					'add_new' 				=> esc_html__('Add New', 'workscout-freelancer'),
					'add_new_item' 			=> sprintf(esc_html__('Add %s', 'workscout-freelancer'), $bid_singular),
					'edit' 					=> esc_html__('Edit', 'workscout-freelancer'),
					'edit_item' 			=> sprintf(esc_html__('Edit %s', 'workscout-freelancer'), $bid_singular),
					'new_item' 				=> sprintf(esc_html__('New %s', 'workscout-freelancer'), $bid_singular),
					'view' 					=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $bid_singular),
					'view_item' 			=> sprintf(esc_html__('View %s', 'workscout-freelancer'), $bid_singular),
					'search_items' 			=> sprintf(esc_html__('Search %s', 'workscout-freelancer'), $bid_name),
					'not_found' 			=> sprintf(esc_html__('No %s found', 'workscout-freelancer'), $bid_name),
					'not_found_in_trash' 	=> sprintf(esc_html__('No %s found in trash', 'workscout-freelancer'), $bid_name),
					'parent' 				=> sprintf(esc_html__('Parent %s', 'workscout-freelancer'), $bid_singular),
				),
				'description' => sprintf(esc_html__('This is where you can create and manage %s.', 'workscout-freelancer'), $bid_name),
				'public' 				=> true,
				'show_ui' 				=> true,
				'show_in_rest' 			=> true,
				'capability_type' 		=> array('job_listing', 'job_listings'),
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> true,
				'hierarchical' 			=> false,
				'menu_icon'           => 'dashicons-clipboard',
				'query_var' 			=> true,
				'supports' 				=> array('title', 'author', 'editor', 'custom-fields', 'publicize', 'thumbnail', 'comments'),
				'has_archive' 			=> false,
				'show_in_nav_menus' 	=> true
			))
		);


	
	} /* eof register*/

	/**
	 * Adds columns to admin task of task Job Listings.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function columns($columns)
	{
		if (!is_array($columns)) {
			$columns = array();
		}

		$columns["task_type"]     	  = __("Type", 'workscout-freelancer');
		$columns["task_posted"]       = __("Posted", 'workscout-freelancer');
		$columns["expires"]           = __("Expires", 'workscout-freelancer');
		$columns['featured_task']     = '<span class="tips" data-tip="' . __("Featured?", 'workscout-freelancer') . '">' . __("Featured?", 'workscout-freelancer') . '</span>';
		$columns['task_actions']      = __("Actions", 'workscout-freelancer');
		return $columns;
	}

	/**
	 * Displays the content for each custom column on the admin list for task Job Listings.
	 *
	 * @param mixed $column
	 */
	public function custom_columns($column)
	{
		global $post;

		switch ($column) {
			case "task_type":
				$type = get_post_meta($post->ID, '_task_type', true);
				echo $type;
				break;

			

			case "expires":
				$expires = get_post_meta($post->ID, '_task_expires', true);
				if ((is_numeric($expires) && (int)$expires == $expires && (int)$expires > 0)) {
					echo date_i18n(get_option('date_format'), $expires);
				} else {
					echo $expires;
				}

				break;

			case "featured_task":
				if (ws_freelancer_is_featured($post->ID)) echo '&#10004;';
				else echo '&ndash;';
				break;
			case "task_posted":
				echo '<strong>' . date_i18n(__('M j, Y', 'workscout-freelancer'), strtotime($post->post_date)) . '</strong><span>';
				echo (empty($post->post_author) ? __('by a guest', 'workscout-freelancer') : sprintf(__('by %s', 'workscout-freelancer'), '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>')) . '</span>';
				break;

			case "task_actions":
				echo '<div class="actions">';

				$admin_actions = apply_filters('wsf_post_row_actions', array(), $post);

				if (in_array($post->post_status, array('pending', 'preview', 'pending_payment')) && current_user_can('publish_post', $post->ID)) {
					$admin_actions['approve']   = array(
						'action'  => 'approve',
						'name'    => __('Approve', 'workscout-freelancer'),
						'url'     =>  wp_nonce_url(add_query_arg('approve_task', $post->ID), 'approve_task')
					);
				}
				/*				if ( $post->post_status !== 'trash' ) {
					if ( current_user_can( 'read_post', $post->ID ) ) {
						$admin_actions['view']   = array(
							'action'  => 'view',
							'name'    => __( 'View', 'workscout-freelancer'),
							'url'     => get_permalink( $post->ID )
						);
					}
					if ( current_user_can( 'edit_post', $post->ID ) ) {
						$admin_actions['edit']   = array(
							'action'  => 'edit',
							'name'    => __( 'Edit', 'workscout-freelancer'),
							'url'     => get_edit_post_link( $post->ID )
						);
					}
					if ( current_user_can( 'delete_post', $post->ID ) ) {
						$admin_actions['delete'] = array(
							'action'  => 'delete',
							'name'    => __( 'Delete', 'workscout-freelancer'),
							'url'     => get_delete_post_link( $post->ID )
						);
					}
				}*/

				$admin_actions = apply_filters('task_manager_admin_actions', $admin_actions, $post);

				foreach ($admin_actions as $action) {
					if (is_array($action)) {
						printf('<a class="button button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']));
					} else {
						echo str_replace('class="', 'class="button ', $action);
					}
				}

				echo '</div>';

				break;
		}
	}


	/**
	 * Sets expiry date when status changes.
	 *
	 * @param WP_Post $post
	 */
	public function set_expiry($post)
	{
		if ($post->post_type !== 'task') {
			return;
		}

		// See if it is already set
		if (get_post_meta($post->ID, '_task_expires', true)) {
			$expires =  get_post_meta($post->ID, '_task_expires', true);

			if ((is_numeric($expires) && (int)$expires == $expires && (int)$expires > 0)) {
				//
			} else {
				$expires = CMB2_Utils::get_timestamp_from_value($expires, 'm/d/Y');
			}

			if ($expires && $expires < current_time('timestamp')) {
				update_post_meta($post->ID, '_task_expires', '');
			} else {
				update_post_meta($post->ID, '_task_expires', strtotime($expires));
			}
		}


		// See if the user has set the expiry manually:
		if (!empty($_POST['_task_expires'])) {
			$expires = $_POST['_task_expires'];
			if ((is_numeric($expires) && (int)$expires == $expires && (int)$expires > 0)) {
				//
			} else {
				$expires = CMB2_Utils::get_timestamp_from_value($expires, 'm/d/Y');
			}
			update_post_meta($post->ID, '_task_expires',  $expires);

			// No manual setting? Lets generate a date if there isn't already one
		} elseif (false == isset($expires)) {
			$expires = calculate_task_expiry($post->ID);
			update_post_meta($post->ID, '_task_expires', $expires);

			// In case we are saving a post, ensure post data is updated so the field is not overridden
			if (isset($_POST['_task_expires'])) {
				$expires = $_POST['_task_expires'];
				// // if ((is_numeric($expires) && (int)$expires == $expires && (int)$expires > 0)) {
				// // 	//
				// // } else {
				// // 	$expires = CMB2_Utils::get_timestamp_from_value($expires, 'm/d/Y');
				// // }
				// $_POST['_task_expires'] = $expires;
			}
		}
	}


	/**
	 * Maintenance task to expire tasks.
	 */
	public function workscout_freelancer_check_for_expired_tasks()
	{
		global $wpdb;
		$date_format = get_option('date_format');
		// Change status to expired
		$task_ids = $wpdb->get_col($wpdb->prepare("
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_task_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'task'
		",
			date('Y-m-d', current_time('timestamp'))
		)
	);

		if ($task_ids) {
			foreach ($task_ids as $task_id) {

				$task_data       = array();
				$task_data['ID'] = $task_id;
				$task_data['post_status'] = 'expired';
				wp_update_post($task_data);
				do_action('wsf_expired_task', $task_id);
			}
		}

		// Notifie expiring in 5 days
		$task_ids = $wpdb->get_col($wpdb->prepare("
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_task_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'task'
		", 
			date('Y-m-d', strtotime('+5 days'))
		)
	);

		if ($task_ids) {
			foreach ($task_ids as $task_id) {
				$task_data['ID'] = $task_id;
				do_action('wsf_expiring_soon_task', $task_id);
			}
		}
		// Delete old expired tasks
		if (apply_filters('wsf_delete_expired_tasks', false)) {
			$task_ids = $wpdb->get_col($wpdb->prepare("
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'task'
				AND posts.post_modified < %s
				AND posts.post_status = 'expired'
			", strtotime(date($date_format, strtotime('-' . apply_filters('wsf_delete_expired_tasks_days', 30) . ' days', current_time('timestamp'))))));

			if ($task_ids) {
				foreach ($task_ids as $task_id) {
					wp_trash_post($task_id);
				}
			}
		}
	}


	/**
	 * Adds bulk actions to drop downs on Job Job Listing admin page.
	 *
	 * @param array $bulk_actions
	 * @return array
	 */
	public function add_bulk_actions($bulk_actions)
	{
		global $wp_post_types;

		foreach ($this->get_bulk_actions() as $key => $bulk_action) {
			if (isset($bulk_action['label'])) {
				$bulk_actions[$key] = sprintf($bulk_action['label'], $wp_post_types['task']->labels->name);
			}
		}
		return $bulk_actions;
	}


	/**
	 * Performs bulk actions on Job Job Listing admin page.
	 *
	 * @since 1.27.0
	 *
	 * @param string $redirect_url The redirect URL.
	 * @param string $action       The action being taken.
	 * @param array  $post_ids     The posts to take the action on.
	 */
	public function do_bulk_actions($redirect_url, $action, $post_ids)
	{
		$actions_handled = $this->get_bulk_actions();
		if (isset($actions_handled[$action]) && isset($actions_handled[$action]['handler'])) {
			$handled_jobs = array();
			if (!empty($post_ids)) {
				foreach ($post_ids as $post_id) {
					if (
						'task' === get_post_type($post_id)
						&& call_user_func($actions_handled[$action]['handler'], $post_id)
					) {
						$handled_jobs[] = $post_id;
					}
				}
				wp_redirect(add_query_arg('handled_jobs', $handled_jobs, add_query_arg('action_performed', $action, $redirect_url)));
				exit;
			}
		}
	}

	/**
	 * Returns the list of bulk actions that can be performed on job tasks.
	 *
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions_handled = array();
		$actions_handled['approve_tasks'] = array(
			'label' => __('Approve %s', 'workscout-freelancer'),
			'notice' => __('%s approved', 'workscout-freelancer'),
			'handler' => array($this, 'bulk_action_handle_approve_task'),
		);
		$actions_handled['expire_tasks'] = array(
			'label' => __('Expire %s', 'workscout-freelancer'),
			'notice' => __('%s expired', 'workscout-freelancer'),
			'handler' => array($this, 'bulk_action_handle_expire_task'),
		);


		return apply_filters('wsf_bulk_actions', $actions_handled);
	}

	/**
	 * Performs bulk action to approve a single job task.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_approve_task($post_id)
	{
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'publish',
		);
		if (
			in_array(get_post_status($post_id), array('pending', 'pending_payment'))
			&& current_user_can('publish_post', $post_id)
			&& wp_update_post($job_data)
		) {
			return true;
		}
		return false;
	}

	/**
	 * Performs bulk action to expire a single job task.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_expire_task($post_id)
	{
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'expired',
		);
		if (
			current_user_can('manage_tasks', $post_id)
			&& wp_update_post($job_data)
		) {
			return true;
		}
		return false;
	}


	/**
	 * Approves a single task.
	 */
	public function approve_task()
	{
		if (!empty($_GET['approve_task']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_task') && current_user_can('publish_post', $_GET['approve_task'])) {
			$post_id = absint($_GET['approve_task']);
			$task_data = array(
				'ID'          => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post($task_data);
			wp_redirect(remove_query_arg('approve_task', add_query_arg('handled_tasks', $post_id, add_query_arg('action_performed', 'approve_tasks', admin_url('edit.php?post_type=task')))));
			exit;
		}
	}

	/**
	 * Shows a notice if we did a bulk action.
	 */
	public function action_notices()
	{
		global $post_type, $pagenow;

		$handled_jobs = isset($_REQUEST['handled_tasks']) ? $_REQUEST['handled_tasks'] : false;
		$action = isset($_REQUEST['action_performed']) ? $_REQUEST['action_performed'] : false;
		$actions_handled = $this->get_bulk_actions();

		if (
			$pagenow == 'edit.php'
			&& $post_type == 'task'
			&& $action
			&& !empty($handled_jobs)
			&& isset($actions_handled[$action])
			&& isset($actions_handled[$action]['notice'])
		) {
			if (is_array($handled_jobs)) {
				$handled_jobs = array_map('absint', $handled_jobs);
				$titles       = array();
				foreach ($handled_jobs as $job_id) {
					$titles[] = wsf_get_the_task_title($job_id);
				}
				echo '<div class="updated"><p>' . sprintf($actions_handled[$action]['notice'], '&quot;' . implode('&quot;, &quot;', $titles) . '&quot;') . '</p></div>';
			} else {

				echo '<div class="updated"><p>' . sprintf($actions_handled[$action]['notice'], '&quot;' . wsf_get_the_task_title(absint($handled_jobs)) . '&quot;') . '</p></div>';
			}
		}
	}


	public function add_icon_column($columns)
	{

		$columns['icon'] = __('Icon', 'workscout-freelancer');
		return $columns;
	}


	/**
	 * Adds the Employment Type column content when task job type terms in WP Admin.
	 *
	 * @param string $content
	 * @param string $column_name
	 * @param int    $term_id
	 * @return string
	 */
	public function add_icon_column_content($content, $column_name, $term_id)
	{

		if ('icon' !== $column_name) {
			return $content;
		}

		$term_id = absint($term_id);
		$icon = get_term_meta($term_id, 'icon', true);

		if ($icon) {
			$content .= '<i style="font-size:30px;" class="' . $icon . '"></i>';
		}

		return $content;
	}

	public function add_assigned_features_column($columns)
	{

		$columns['features'] = __('Features', 'workscout-freelancer');
		return $columns;
	}

	public function add_assigned_features_content($content, $column_name, $term_id)
	{
		if ('features' !== $column_name) {
			return $content;
		}

		$term_id = absint($term_id);
		$term_meta = get_term_meta($term_id, 'wsf_taxonomy_multicheck', true);
		if ($term_meta) {
			foreach ($term_meta as $feature) {
				$feature_obj = get_term_by('slug', $feature, 'task_feature');

				if ($feature_obj) {
					$term_link = get_term_link($feature_obj);
					$content .= '<a href="' . esc_url($term_link) . '">' . $feature_obj->name . '</a>, ';
				}
			}
			$content  = substr($content, 0, -2);
		}
		return $content;
	}

	public function set_default_avg_rating_new_post($post_ID)
	{
		$current_field_value = get_post_meta($post_ID, 'wsf-avg-rating', true); //change YOUMETAKEY to a default 
		$default_meta = '0'; //set default value

		if ($current_field_value == '' && !wp_is_post_revision($post_ID)) {
			add_post_meta($post_ID, 'wsf-avg-rating', $default_meta, true);
		}
		return $post_ID;
	}



	function add_tasks_permastructure()
	{
		global $wp_rewrite;

		$standard_slug = apply_filters('wsf_rewrite_task_slug', 'task');
		$permalinks = self::get_permalink_structure();
		$slug = (isset($permalinks['task_base']) && !empty($permalinks['task_base'])) ? $permalinks['task_base'] : $standard_slug;


		//add_permastruct( 'region', $slug.'/%region%', false );
		//add_permastruct( 'task_category', $slug.'/%task_category%', false );
		add_permastruct('task', $slug . '/%region%/%task_category%/%task%', false);
	}

	function task_permalinks($permalink, $post)
	{
		if ($post->post_type !== 'task')
			return $permalink;

		$regions = get_the_terms($post->ID, 'region');
		if (!$regions) {
			$permalink = str_replace('%region%/', '-/', $permalink);
		} else {

			$post_regions = array();
			foreach ($regions as $region)
				$post_regions[] = $region->slug;

			$permalink = str_replace('%region%', implode(',', $post_regions), $permalink);
		}

		$categories = get_the_terms($post->ID, 'task_category');
		if (!$categories) {
			$permalink = str_replace('%task_category%/', '-/', $permalink);
		} else {



			$post_categories = array();
			foreach ($categories as $category)
				$post_categories[] = $category->slug;

			$permalink = str_replace('%task_category%', implode(',', $post_categories), $permalink);
		}


		return $permalink;
	}

	// Make sure that all term links include their parents in the permalinks

	function add_term_parents_to_permalinks($permalink, $term)
	{
		$term_parents = $this->get_term_parents($term);
		foreach ($term_parents as $term_parent)
			$permlink = str_replace($term->slug, $term_parent->slug . ',' . $term->slug, $permalink);
		return $permalink;
	}

	function get_term_parents($term, &$parents = array())
	{
		$parent = get_term($term->parent, $term->taxonomy);

		if (is_wp_error($parent))
			return $parents;

		$parents[] = $parent;
		if ($parent->parent)
			self::get_term_parents($parent, $parents);
		return $parents;
	}

	public function default_comments_on($data)
	{
		if ($data['post_type'] == 'task') {
			$data['comment_status'] = 'open';
		}

		return $data;
	}


	function save_as_product($post_ID, $post, $update)
	{
		if (!is_admin()) {

			return;
		}

		if ($post->post_type == 'task') {


			$product_id = get_post_meta($post_ID, 'product_id', true);

			// basic task informations will be added to task
			$product = array(
				'post_author' => get_current_user_id(),
				'post_content' => $post->post_content,
				'post_status' => 'publish',
				'post_title' => $post->post_title,
				'post_parent' => '',
				'post_type' => 'product',
			);

			// add product if not exist
			if (!$product_id ||  get_post_type($product_id) != 'product') {

				// insert task as WooCommerce product
				$product_id = wp_insert_post($product);
				wp_set_object_terms($product_id, 'task_booking', 'product_type');
			} else {

				// update existing product
				$product['ID'] = $product_id;
				wp_update_post($product);
			}


			// set product category
			$term = get_term_by('name', apply_filters('wsf_default_product_category', 'Hireo booking'), 'product_cat', ARRAY_A);

			if (!$term) $term = wp_insert_term(
				apply_filters('wsf_default_product_category', 'Hireo booking'),
				'product_cat',
				array(
					'description' => __('Task category', 'wsf-core'),
					'slug' => str_replace(' ', '-', apply_filters('wsf_default_product_category', 'Hireo booking'))
				)
			);

			wp_set_object_terms($product_id, $term['term_id'], 'product_cat');

			update_post_meta($post_ID, 'product_id', $product_id);
		}
	}
}
