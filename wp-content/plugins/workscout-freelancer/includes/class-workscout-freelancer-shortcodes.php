<?php

/**
 * File containing the class WorkScout_Freelancer_Shortcodes.
 *
 * @package wp-job-manager-tasks
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * WorkScout_Freelancer_Shortcodes class.
 */
class WorkScout_Freelancer_Shortcodes
{

	private $task_dashboard_message = '';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('wp', [$this, 'handle_redirects']);
		add_action('wp', [$this, 'shortcode_action_handler']);
		add_shortcode('workscout_submit_task', [$this, 'submit_task_form']);
		add_shortcode('workscout_task_dashboard', [$this, 'task_dashboard']);
		add_shortcode('workscout_project_dashboard', [$this, 'project_dashboard']);
		add_shortcode('workscout_my_bids', [$this, 'task_my_bids']);

		add_action('workscout_freelancer_task_dashboard_content_view-project', [$this, 'load_project_view']);
		add_shortcode('workscout_freelancer_project_view', [$this, 'freelancer_project_view']);
		add_shortcode('tasks', [$this, 'output_tasks']);
		//	add_action( 'workscout_freelancer_output_tasks_no_results', [ $this, 'output_no_results' ] );
	}

	/**
	 * Handle redirects
	 */
	public function handle_redirects()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		if (!get_current_user_id() || (!empty($_REQUEST['task_id']) && workscout_freelancer_user_can_edit_task(intval($_REQUEST['task_id'])))) {
			return;
		}

		$submit_task_form_page_id = get_option('workscout_freelancer_submit_task_form_page_id');
		$submission_limit           = get_option('workscout_freelancer_submission_limit');
		//$task_count               = workscout_freelancer_count_user_tasks();
		$task_count               = 0;

		if (
			$submit_task_form_page_id
			&& $submission_limit
			&& $task_count >= $submission_limit
			&& is_page($submit_task_form_page_id)
		) {
			$task_dashboard_page_id = get_option('workscout_freelancer_task_dashboard_page_id');
			if ($task_dashboard_page_id) {
				$redirect_url = get_permalink($task_dashboard_page_id);
			} else {
				$redirect_url = home_url('/');
			}

			/**
			 * Filter on the URL visitors will be redirected upon exceeding submission limit.
			 *
			 * @since 1.18.0
			 *
			 * @param string $redirect_url     URL to redirect when user has exceeded submission limit.
			 * @param int    $submission_limit Maximum number of listings a user can submit.
			 * @param int    $task_count     Number of tasks the user has submitted.
			 */
			$redirect_url = apply_filters(
				'workscout_freelancer_redirect_url_exceeded_listing_limit',
				$redirect_url,
				$submission_limit,
				$task_count
			);

			if ($redirect_url) {
				wp_safe_redirect(esc_url($redirect_url));

				exit;
			}
		}
	}

	/**
	 * Handle actions which need to be run before the shortcode e.g. post actions
	 */
	public function shortcode_action_handler()
	{
		global $post;

		/**
		 * Force the shortcode handler to run.
		 *
		 * @param bool $force_shortcode_action_handler Whether it should be forced to run.
		 */
		$force_shortcode_action_handler = apply_filters('workscout_freelancer_force_shortcode_action_handler', false);

		if (is_page() && strstr($post->post_content, '[workscout_task_dashboard') || $force_shortcode_action_handler) {
			$this->task_dashboard_handler();
		}


		if (is_page() && strstr($post->post_content, '[candidate_dashboard') || $force_shortcode_action_handler) {
			
			$this->candidate_dashboard_handler();
		}
	}

	/**
	 * Show the task submission form
	 */
	public function submit_task_form($atts = [])
	{
		return $GLOBALS['workscout_freelancer']->forms->get_form('submit-task', $atts);
	}

	/**
	 * Handles actions on candidate dashboard
	 */
	public function candidate_dashboard_handler() {
		if (!empty($_REQUEST['action']) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'resume_manager_my_resume_actions')) {

			$action    = sanitize_title($_REQUEST['action']);
			$resume_id = absint($_REQUEST['resume_id']);

			try {
				// Get resume
				$resume = get_post($resume_id);

				// Check ownership
				if (!$resume || $resume->post_author != get_current_user_id()) {
					throw new Exception(__('Invalid Resume ID', 'wp-job-manager-resumes'));
				}
				$user_id = get_current_user_id();
				switch ($action) {
					case 'set_as_profile':
						// Trash it
						//get current user id
						
						//save resume id as user meta 'freelancer_profile'
						update_user_meta($user_id, 'freelancer_profile', $resume_id);
						

						// Message
						//$this->resume_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been set as your Freelancer profile', 'wp-job-manager-resumes'), $resume->post_title) . '</div>';

						break;
					case 'unset_as_profile':
						// Trash it
						//remove user meta 'freelancer_profile'
						delete_user_meta($user_id, 'freelancer_profile');

						// Message
						//$this->resume_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been set as your Freelancer profile', 'wp-job-manager-resumes'), $resume->post_title) . '</div>';

						break;
				}

				do_action('resume_manager_my_resume_do_action', $action, $resume_id);
			} catch (Exception $e) {
				//$this->resume_dashboard_message = '<div class="job-manager-error">' . $e->getMessage() . '</div>';
			}
		}
	}

	/**
	 * Handles actions on candidate dashboard
	 */
	public function task_dashboard_handler()
	{
		if (!empty($_REQUEST['action']) && !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'workscout_freelancer_my_task_actions')) {

			$action    = sanitize_title($_REQUEST['action']);
			$task_id = absint($_REQUEST['task_id']);

			try {
				// Get task
				$task = get_post($task_id);

				// Check ownership
				if (!$task || $task->post_author != get_current_user_id()) {
					throw new Exception(__('Invalid Task ID', 'workscout-freelancer'));
				}

				switch ($action) {
					case 'delete':
						// Trash it
						wp_trash_post($task_id);

						// Message
						$this->task_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been deleted', 'workscout-freelancer'), $task->post_title) . '</div>';

						break;
					case 'hide':
						if ($task->post_status === 'publish') {
							$update_task = [
								'ID'          => $task_id,
								'post_status' => 'hidden',
							];
							wp_update_post($update_task);
							$this->task_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been hidden', 'workscout-freelancer'), $task->post_title) . '</div>';
						}
						break;
					case 'completed':
						if ($task->post_status === 'in_progress') {
							$update_task = [
								'ID'          => $task_id,
								'post_status' => 'completed',
							];
							wp_update_post($update_task);
							// get the bid
							$bid_id = get_post_meta($task_id, '_selected_bid_id', true);
							//set bid post status as closed
							if($bid_id){
								$update_bid = [
									'ID'          => $bid_id,
									'post_status' => 'closed',
								];
								wp_update_post($update_bid);
							}
							
							
							$this->task_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been completed', 'workscout-freelancer'), $task->post_title) . '</div>';
						}
						break;
					case 'publish':
						if ($task->post_status === 'hidden') {
							$update_task = [
								'ID'          => $task_id,
								'post_status' => 'publish',
							];
							wp_update_post($update_task);
							$this->task_dashboard_message = '<div class="job-manager-message">' . sprintf(__('%s has been published', 'workscout-freelancer'), $task->post_title) . '</div>';
						}
						break;
					case 'relist':
						// redirect to post page
						wp_redirect(add_query_arg(['task_id' => absint($task_id)], get_permalink(get_option('workscout_freelancer_submit_task_form_page_id'))));

						break;
				}

				do_action('workscout_freelancer_my_task_do_action', $action, $task_id);
			} catch (Exception $e) {
				$this->task_dashboard_message = '<div class="job-manager-error">' . $e->getMessage() . '</div>';
			}
		}
	}

	/**
	 * Add a flash message to display on a candidate dashboard.
	 *
	 * @param string $message Flash message to show on candidate dashboard.
	 * @param bool   $is_error True this message is an error.
	 *
	 * @return bool
	 */
	public static function add_task_dashboard_message($message, $is_error = false)
	{
		$task_dashboard_page_id = get_option('workscout_freelancer_task_dashboard_page_id');
		if (!wp_get_session_token() || !$task_dashboard_page_id) {
			// We only handle flash messages when the candidate dashboard page ID is set and user has valid session token.

			return false;
		}
		$messages_key = self::get_task_dashboard_message_key();
		$messages     = self::get_task_dashboard_messages(false);

		$messages[] = [
			'message'  => $message,
			'is_error' => $is_error,
		];

		set_transient($messages_key, wp_json_encode($messages), HOUR_IN_SECONDS);

		return true;
	}

	/**
	 * Gets the current flash messages for the candidate dashboard.
	 *
	 * @param bool $clear Flush messages after retrieval.
	 * @return array
	 */
	private static function get_task_dashboard_messages($clear)
	{
		$messages_key = self::get_task_dashboard_message_key();
		$messages     = get_transient($messages_key);

		if (empty($messages)) {
			$messages = [];
		} else {
			$messages = json_decode($messages, true);
		}

		if ($clear) {
			delete_transient($messages_key);
		}

		return $messages;
	}

	/**
	 * Get the transient key to use to store candidate dashboard messages.
	 *
	 * @return string
	 */
	private static function get_task_dashboard_message_key()
	{
		return 'task_dashboard_messages_' . md5(wp_get_session_token());
	}

	function freelancer_project_view($atts){
		global $workscout_freelancer;

		if (!is_user_logged_in()) {
			ob_start();
			get_job_manager_template('task-dashboard-login.php', [], 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/');
			return ob_get_clean();
		}

		$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 25;


		ob_start();

		// If doing an action, show conditional content if needed....
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		$action = isset($_REQUEST['action']) ? sanitize_title(wp_unslash($_REQUEST['action'])) : false;
		if (!empty($action)) {
			// Show alternative content if a plugin wants to.
			if (has_action('workscout_freelancer_task_dashboard_content_' . $action)) {
				do_action('workscout_freelancer_task_dashboard_content_' . $action, $atts);

				return ob_get_clean();
			}
		}

		// ....If not show the candidate dashboard
		$args = apply_filters(
			'workscout_freelancer_get_dashboard_projects_args',
			[
				'post_type'           => 'project',
				'post_status'         => ['publish', 'expired', 'pending', 'hidden', 'preview'],
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $posts_per_page,
				'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
				'orderby'             => 'date',
				'order'               => 'desc',
	
			]
		);

		// query only posts that have meta field "_freelancer_id" with value of current user id
		$args['meta_query'] = [
			[
				'key'     => '_freelancer_id',
				'value'   => get_current_user_id(),
				'compare' => '=',
			],
		];
		
		if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
			if ($_REQUEST['sort-by'] == 'active') {
				$statuses = ['publish'];
			} else {
				$statuses = ['closed', 'expired', 'pending', 'hidden', 'preview'];
			}
			$args['post_status'] = $statuses;
		}


		$projects = new WP_Query();
		get_job_manager_template(
			'my-projects.php',
			[
				'projects'                     => $projects->query($args),
				'max_num_pages'               => $projects->max_num_pages,
				//'task_dashboard_columns' => $task_dashboard_columns,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
	}

	function load_project_view(){
		// get_job_manager_template(
		// 	'project-view.php',
		// 	[
		// 		'project' => get_post($_REQUEST['project_id'])
		// 	],
		// 	'workscout-freelancer',
		// 	WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		// );
		$project_id = absint($_REQUEST['project_id']);
		$project    = get_post($project_id);

	
		// Permissions
		//AD LATER 
		// if (! job_manager_user_can_edit_job($job_id)) {
		// 	_e('You do not have permission to view this job.', 'wp-job-manager-applications');
		// 	return;
		// }

		//wp_enqueue_script('wp-job-manager-applications-dashboard');

		

		get_job_manager_template(
			'project-view.php',
			[
				'project' => get_post($_REQUEST['project_id'])
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
	}

	//comment
	function add_comment() {
		$post_id =123; // Your project post ID:

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
			$comment_author = wp_get_current_user();
			$time = current_time('mysql');

			$data = array(
				'comment_post_ID' => $post_id,
				'comment_author' => $comment_author->display_name,
				'comment_author_email' => $comment_author->user_email,
				'comment_author_url' => $comment_author->user_url,
				'comment_content' => $_POST['comment_content'],
				'comment_type' => '',
				'comment_parent' => 0,
				'user_id' => $comment_author->ID,
				'comment_date' => $time,
				'comment_approved' => 1,
			);

			wp_new_comment($data);
		}
	}
	/**
	 * Shortcode which lists the logged in user's tasks
	 */
	public function task_dashboard($atts)
	{
		global $workscout_freelancer;

		if (!is_user_logged_in()) {
			ob_start();
			get_job_manager_template('task-dashboard-login.php', [], 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/');
			return ob_get_clean();
		}

		$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 25;

		wp_enqueue_script('wp-task-manager-candidate-dashboard');

		// If doing an action, show conditional content if needed....
		if (!empty($_REQUEST['action'])) {

			$action    = sanitize_title($_REQUEST['action']);

			switch ($action) {
				case 'edit':
					return $workscout_freelancer->forms->get_form('edit-task');
				break;
				
			}
		}
		ob_start();

		// If doing an action, show conditional content if needed....
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		$action = isset($_REQUEST['action']) ? sanitize_title(wp_unslash($_REQUEST['action'])) : false;
		if (!empty($action)) {
			// Show alternative content if a plugin wants to.
			if (has_action('workscout_freelancer_task_dashboard_content_' . $action)) {
				do_action('workscout_freelancer_task_dashboard_content_' . $action, $atts);

				return ob_get_clean();
			}
		}

		// ....If not show the candidate dashboard
		$args = apply_filters(
			'workscout_freelancer_get_dashboard_tasks_args',
			[
				'post_type'           => 'task',
				'post_status'         => ['in_progress', 'publish', 'expired', 'pending', 'hidden', 'preview', 'completed'],
				//'ignore_sticky_posts' => 1,
				'posts_per_page'      => $posts_per_page,
				'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
				'orderby'             => 'date',
				'order'               => 'desc',
				'author'              => get_current_user_id(),
			]
		);
		if(isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != ''){
			$args['post_status'] = $_REQUEST['sort-by'];
		}
		
		$tasks = new WP_Query();



		echo wp_kses_post($this->task_dashboard_message);

		// Get the flash messages sent by external handlers.
		$messages = self::get_task_dashboard_messages(true);
		foreach ($messages as $message) {
			$div_class = 'job-manager-message';
			if (!empty($message['is_error'])) {
				$div_class = 'job-manager-error';
			}
			echo '<div class="' . esc_attr($div_class) . '">' . wp_kses_post($message['message']) . '</div>';
		}

		$task_dashboard_columns = apply_filters(
			'workscout_freelancer_task_dashboard_columns',
			[
				'task-title'       => __('Name', 'workscout-freelancer'),
				'task-bidders'    => __('Bids', 'workscout-freelancer'),
				'task-bid-info'    => __('Info', 'workscout-freelancer'),
				'task-category'    => __('Title', 'workscout-freelancer'),
			//	'date'               => __('Date Posted', 'workscout-freelancer'),
			]
		);

		if (!get_option('workscout_freelancer_enable_categories')) {
			unset($task_dashboard_columns['task-category']);
		}

		get_job_manager_template(
			'task-dashboard.php',
			[
				'tasks'                     => $tasks->query($args),
				'max_num_pages'               => $tasks->max_num_pages,
				'task_dashboard_columns' => $task_dashboard_columns,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);

		return ob_get_clean();
	}

	/**
	 * output_tasks function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function output_tasks($atts)
	{
		

		ob_start();

		extract($atts = shortcode_atts(apply_filters('workscout_freelancer_tasks_output_defaults', array(

			'style'						=> 'list', //compact, grid
			'layout_switch'				=> 'off',
			'list_top_buttons'			=> 'filters|order|layout|radius', //filters|order|layout
			'per_page'                  => Kirki::get_option('workscout', 'tasks_per_page'),
			'orderby'                   => '',
			'order'                     => '',
			'keyword'                   => '',
			'location'                   => '',
			'search_radius'             => '',
			'radius_type'               => '',
			'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
			'custom_class'				=> '',
			'grid_columns'				=> '2',
			'in_rows'					=> '',
			'ajax_browsing'				=> get_option('task_ajax_browsing'),
			'from_vs'				=> '',
		)),
			$atts
		));
		$template_loader = new WorkScout_Freelancer_Template_Loader;
		$ordering_args = WorkScout_Freelancer_Task::get_task_ordering_args ($orderby, $order);
		wp_enqueue_script("workscout-freelancer-ajaxsearch");
		$get_tasks = array_merge($atts, array(
			'posts_per_page'    => $per_page,
			'orderby'           => $ordering_args['orderby'],
			'order'             => $ordering_args['order'],
			'keyword_search'   	=> $keyword,
			'search_keywords'   	=> $keyword,
			'location_search'   => $location,
			'search_radius'   	=> $search_radius,
			'radius_type'   	=> $radius_type,
			'listeo_orderby'   	=> $orderby,

		));
		switch ($style) {
			case 'list':
				$template_style = '';
				$list_class = '';
				break;
			case 'compact':
				$template_style = '';
				$list_class = 'compact-list';
				break;
			
			case 'grid':
				$template_style = 'grid';
				$list_class = 'tasks-grid-layout';
				break;
			
			default:
				$template_style = '';
				$list_class = '';
				break;
		}
		$get_tasks['featured'] = $featured;
		$tasks_query = WorkScout_Freelancer_Task::get_tasks(apply_filters('workscout_freelancer_output_defaults_args', $get_tasks));
		
		if ($tasks_query->have_posts()) {
			$style_data = array(
				'style' 		=> $style,
				'class' 		=> $custom_class,
				'in_rows' 		=> $in_rows,
				'grid_columns' 	=> $grid_columns,
				'per_page' 		=> $per_page,
				'max_num_pages'	=> $tasks_query->max_num_pages,
				'counter'		=> $tasks_query->found_posts,
				'ajax_browsing' => $ajax_browsing,
			);

			$search_data = array_merge($style_data, $get_tasks);
			$template_loader->set_template_data( $search_data )->get_template_part( 'tasks-start' );

			
			while ($tasks_query->have_posts()) {
				// Loop through listings
				// Setup listing data
				$tasks_query->the_post();
				
				$template_loader->set_template_data( $style_data )->get_template_part( 'content-task', $template_style ); 	
			
			}
			
			if($style_data['ajax_browsing']){?>
			</div>
			<div class="pagination-container ajax-search">
				<?php
				echo workscout_core_ajax_pagination($tasks_query->max_num_pages, 1 ); ?>
			</div>
			<?php } else {
				$template_loader->set_template_data( $style_data )->get_template_part( 'tasks-end' ); 
			}
		} else {

			$template_loader->get_template_part( 'archive/no-found' ); 
		}

		wp_reset_query();
		return ob_get_clean();
	}


	/**
	 * Output some content when no results were found
	 */
	public function output_no_results()
	{
		get_job_manager_template('content-no-tasks-found.php', [], 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/');
	}

	/**
	 * Get string as a bool
	 *
	 * @param  string $value
	 * @return bool
	 */
	public function string_to_bool($value)
	{
		return (is_bool($value) && $value) || in_array($value, ['1', 'true', 'yes']) ? true : false;
	}


	function task_my_bids(){
		global $workscout_freelancer;

		if (!is_user_logged_in()) {
			ob_start();
			get_job_manager_template('task-dashboard-login.php', [], 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/');
			return ob_get_clean();
		}

		$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 25;

		

		// // If doing an action, show conditional content if needed....
		// if (!empty($_REQUEST['action'])) {

		// 	$action    = sanitize_title($_REQUEST['action']);

		// 	switch ($action) {
		// 		case 'edit':
		// 			return $workscout_freelancer->forms->get_form('edit-task');
		// 	}
		// }
		ob_start();

		// If doing an action, show conditional content if needed....
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		// $action = isset($_REQUEST['action']) ? sanitize_title(wp_unslash($_REQUEST['action'])) : false;
		// if (!empty($action)) {
		// 	// Show alternative content if a plugin wants to.
		// 	if (has_action('workscout_freelancer_task_dashboard_content_' . $action)) {
		// 		do_action('workscout_freelancer_task_dashboard_content_' . $action, $atts);

		// 		return ob_get_clean();
		// 	}
		// }

		// ....If not show the candidate dashboard
		$args = apply_filters(
			'workscout_freelancer_get_dashboard_tasks_args',
			[
				'post_type'           => 'bid',
				'post_status'         => ['publish', 'expired', 'pending', 'hidden', 'preview'],
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $posts_per_page,
				'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
				'orderby'             => 'date',
				'order'               => 'desc',
				'author'              => get_current_user_id(),
			]
		);
		if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
			if($_REQUEST['sort-by'] == 'active') {
				$statuses = ['publish'];
			} else {
				$statuses = [ 'closed', 'expired', 'pending', 'hidden', 'preview'];
			}
			$args['post_status'] = $statuses;
		}
		

		$bids = new WP_Query();
		get_job_manager_template(
			'my-bids.php',
			[
				'bids'                     => $bids->query($args),
				'max_num_pages'               => $bids->max_num_pages,
				//'task_dashboard_columns' => $task_dashboard_columns,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
	}

	/**
	 * Show the project dashboard
	 */
	public function project_dashboard($atts){
		global $workscout_freelancer;

		if (!is_user_logged_in()) {
			ob_start();
			get_job_manager_template('task-dashboard-login.php', [], 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/');
			return ob_get_clean();
		}

		$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 25;

		wp_enqueue_script('wp-task-manager-candidate-dashboard');

		// If doing an action, show conditional content if needed....
		if (!empty($_REQUEST['action'])) {

			$action    = sanitize_title($_REQUEST['action']);

			// switch ($action) {
			// 	case 'edit':
			// 		return $workscout_freelancer->forms->get_form('edit-task');
			// }
		}
		ob_start();

		// If doing an action, show conditional content if needed....
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		$action = isset($_REQUEST['action']) ? sanitize_title(wp_unslash($_REQUEST['action'])) : false;
		if (!empty($action)) {
			// Show alternative content if a plugin wants to.
			if (has_action('workscout_freelancer_project_dashboard_content_' . $action)) {
				do_action('workscout_freelancer_project_dashboard_content_' . $action, $atts);

				return ob_get_clean();
			}
		}

		// ....If not show the candidate dashboard
		$args = apply_filters(
			'workscout_freelancer_get_dashboard_project_args',
			[
				'post_type'           => 'project',
				'post_status'         => ['in_progress', 'publish', 'expired', 'pending', 'hidden', 'preview', 'completed'],
				//'ignore_sticky_posts' => 1,
				'posts_per_page'      => $posts_per_page,
				'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
				'orderby'             => 'date',
				'order'               => 'desc',
				'author'              => get_current_user_id(),
			]
		);
		if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
			$args['post_status'] = $_REQUEST['sort-by'];
		}

		$projects = new WP_Query();



		echo wp_kses_post($this->task_dashboard_message);

		// Get the flash messages sent by external handlers.
		$messages = self::get_task_dashboard_messages(true);
		foreach ($messages as $message) {
			$div_class = 'job-manager-message';
			if (!empty($message['is_error'])) {
				$div_class = 'job-manager-error';
			}
			echo '<div class="' . esc_attr($div_class) . '">' . wp_kses_post($message['message']) . '</div>';
		}

		$project_dashboard_columns = apply_filters(
			'workscout_freelancer_task_dashboard_columns',
			[
				'task-title'       => __('Name', 'workscout-freelancer'),
				'task-bidders'    => __('Bids', 'workscout-freelancer'),
				'task-bid-info'    => __('Info', 'workscout-freelancer'),
				'task-category'    => __('Title', 'workscout-freelancer'),
				//	'date'               => __('Date Posted', 'workscout-freelancer'),
			]
		);

		if (!get_option('workscout_freelancer_enable_categories')) {
			unset($projects_dashboard_columns['project-category']);
		}

		get_job_manager_template(
			'project-dashboard.php',
			[
				'projects'                     => $projects->query($args),
				'max_num_pages'               => $projects->max_num_pages,
				'project_dashboard_columns' => $project_dashboard_columns,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);

		return ob_get_clean();
	}
}

new WorkScout_Freelancer_Shortcodes();
