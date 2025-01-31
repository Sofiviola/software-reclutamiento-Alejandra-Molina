<?php
/**
 * File containing the class WP_Resume_Manager_Shortcodes.
 *
 * @package wp-job-manager-resumes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP_Resume_Manager_Shortcodes class.
 */
class WP_Resume_Manager_Shortcodes {

	private $resume_dashboard_message = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'handle_redirects' ] );
		add_action( 'wp', [ $this, 'shortcode_action_handler' ] );
		add_shortcode( 'submit_resume_form', [ $this, 'submit_resume_form' ] );
		add_shortcode( 'candidate_dashboard', [ $this, 'candidate_dashboard' ] );
		add_shortcode( 'resumes', [ $this, 'output_resumes' ] );
		add_action( 'resume_manager_output_resumes_no_results', [ $this, 'output_no_results' ] );
	}

	/**
	 * Handle redirects
	 */
	public function handle_redirects() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		if ( ! get_current_user_id() || ( ! empty( $_REQUEST['resume_id'] ) && resume_manager_user_can_edit_resume( intval( $_REQUEST['resume_id'] ) ) ) ) {
			return;
		}

		$submit_resume_form_page_id = get_option( 'resume_manager_submit_resume_form_page_id' );
		$submission_limit           = get_option( 'resume_manager_submission_limit' );
		$resume_count               = resume_manager_count_user_resumes();

		if (
			$submit_resume_form_page_id
			&& $submission_limit
			&& $resume_count >= $submission_limit
			&& is_page( $submit_resume_form_page_id )
		) {
			$candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
			if ( $candidate_dashboard_page_id ) {
				$redirect_url = get_permalink( $candidate_dashboard_page_id );
			} else {
				$redirect_url = home_url( '/' );
			}

			/**
			 * Filter on the URL visitors will be redirected upon exceeding submission limit.
			 *
			 * @since 1.18.0
			 *
			 * @param string $redirect_url     URL to redirect when user has exceeded submission limit.
			 * @param int    $submission_limit Maximum number of listings a user can submit.
			 * @param int    $resume_count     Number of resumes the user has submitted.
			 */
			$redirect_url = apply_filters(
				'resume_manager_redirect_url_exceeded_listing_limit',
				$redirect_url,
				$submission_limit,
				$resume_count
			);

			if ( $redirect_url ) {
				wp_safe_redirect( esc_url( $redirect_url ) );

				exit;
			}
		}
	}

	/**
	 * Handle actions which need to be run before the shortcode e.g. post actions
	 */
	public function shortcode_action_handler() {
		global $post;

		/**
		 * Force the shortcode handler to run.
		 *
		 * @param bool $force_shortcode_action_handler Whether it should be forced to run.
		 */
		$force_shortcode_action_handler = apply_filters( 'resume_manager_force_shortcode_action_handler', false );

		if ( is_page() && strstr( $post->post_content, '[candidate_dashboard' ) || $force_shortcode_action_handler ) {
			$this->candidate_dashboard_handler();
		}
	}

	/**
	 * Show the resume submission form
	 */
	public function submit_resume_form( $atts = [] ) {
		return $GLOBALS['resume_manager']->forms->get_form( 'submit-resume', $atts );
	}

	/**
	 * Handles actions on candidate dashboard
	 */
	public function candidate_dashboard_handler() {
		if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'resume_manager_my_resume_actions' ) ) {

			$action    = sanitize_title( $_REQUEST['action'] );
			$resume_id = absint( $_REQUEST['resume_id'] );

			try {
				// Get resume
				$resume = get_post( $resume_id );

				// Check ownership
				if ( ! $resume || $resume->post_author != get_current_user_id() ) {
					throw new Exception( __( 'Invalid Resume ID', 'wp-job-manager-resumes' ) );
				}

				switch ( $action ) {
					case 'delete':
						// Trash it
						wp_trash_post( $resume_id );

						// Message
						$this->resume_dashboard_message = '<div class="job-manager-message">' . sprintf( __( '%s ha sido eliminado', 'wp-job-manager-resumes' ), $resume->post_title ) . '</div>';

						break;
					case 'hide':
						if ( $resume->post_status === 'publish' ) {
							$update_resume = [
								'ID'          => $resume_id,
								'post_status' => 'hidden',
							];
							wp_update_post( $update_resume );
							$this->resume_dashboard_message = '<div class="job-manager-message">' . sprintf( __( '%s has been hidden', 'wp-job-manager-resumes' ), $resume->post_title ) . '</div>';
						}
						break;
					case 'publish':
						if ( $resume->post_status === 'hidden' ) {
							$update_resume = [
								'ID'          => $resume_id,
								'post_status' => 'publish',
							];
							wp_update_post( $update_resume );
							$this->resume_dashboard_message = '<div class="job-manager-message">' . sprintf( __( '%s has been published', 'wp-job-manager-resumes' ), $resume->post_title ) . '</div>';
						}
						break;
					case 'relist':
						// redirect to post page
						wp_redirect( add_query_arg( [ 'resume_id' => absint( $resume_id ) ], get_permalink( get_option( 'resume_manager_submit_resume_form_page_id' ) ) ) );

						break;
				}

				do_action( 'resume_manager_my_resume_do_action', $action, $resume_id );

			} catch ( Exception $e ) {
				$this->resume_dashboard_message = '<div class="job-manager-error">' . $e->getMessage() . '</div>';
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
	public static function add_candidate_dashboard_message( $message, $is_error = false ) {
		$candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
		if ( ! wp_get_session_token() || ! $candidate_dashboard_page_id ) {
			// We only handle flash messages when the candidate dashboard page ID is set and user has valid session token.

			return false;
		}
		$messages_key = self::get_candidate_dashboard_message_key();
		$messages     = self::get_candidate_dashboard_messages( false );

		$messages[] = [
			'message'  => $message,
			'is_error' => $is_error,
		];

		set_transient( $messages_key, wp_json_encode( $messages ), HOUR_IN_SECONDS );

		return true;
	}

	/**
	 * Gets the current flash messages for the candidate dashboard.
	 *
	 * @param bool $clear Flush messages after retrieval.
	 * @return array
	 */
	private static function get_candidate_dashboard_messages( $clear ) {
		$messages_key = self::get_candidate_dashboard_message_key();
		$messages     = get_transient( $messages_key );

		if ( empty( $messages ) ) {
			$messages = [];
		} else {
			$messages = json_decode( $messages, true );
		}

		if ( $clear ) {
			delete_transient( $messages_key );
		}

		return $messages;
	}

	/**
	 * Get the transient key to use to store candidate dashboard messages.
	 *
	 * @return string
	 */
	private static function get_candidate_dashboard_message_key() {
		return 'candidate_dashboard_messages_' . md5( wp_get_session_token() );
	}

	/**
	 * Shortcode which lists the logged in user's resumes
	 */
	public function candidate_dashboard( $atts ) {
		global $resume_manager;

		if ( ! is_user_logged_in() ) {
			ob_start();
			get_job_manager_template( 'candidate-dashboard-login.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
			return ob_get_clean();
		}

		$posts_per_page = isset( $atts['posts_per_page'] ) ? intval( $atts['posts_per_page'] ) : 25;

		wp_enqueue_script( 'wp-resume-manager-candidate-dashboard' );

		// If doing an action, show conditional content if needed....
		if ( ! empty( $_REQUEST['action'] ) ) {

			$action    = sanitize_title( $_REQUEST['action'] );
			$resume_id = absint( $_REQUEST['resume_id'] );

			switch ( $action ) {
				case 'edit':
					return $resume_manager->forms->get_form( 'edit-resume' );
			}
		}

		// ....If not show the candidate dashboard
		$args = apply_filters(
			'resume_manager_get_dashboard_resumes_args',
			[
				'post_type'           => 'resume',
				'post_status'         => [ 'publish', 'expired', 'pending', 'hidden' ],
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $posts_per_page,
				'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * $posts_per_page,
				'orderby'             => 'date',
				'order'               => 'desc',
				'author'              => get_current_user_id(),
			]
		);

		$resumes = new WP_Query();

		ob_start();

		echo wp_kses_post( $this->resume_dashboard_message );

		// Get the flash messages sent by external handlers.
		$messages = self::get_candidate_dashboard_messages( true );
		foreach ( $messages as $message ) {
			$div_class = 'job-manager-message';
			if ( ! empty( $message['is_error'] ) ) {
				$div_class = 'job-manager-error';
			}
			echo '<div class="' . esc_attr( $div_class ) . '">' . wp_kses_post( $message['message'] ) . '</div>';
		}

		$candidate_dashboard_columns = apply_filters(
			'resume_manager_candidate_dashboard_columns',
			[
				'resume-title'       => __( 'Name', 'wp-job-manager-resumes' ),
				'candidate-title'    => __( 'Title', 'wp-job-manager-resumes' ),
				'candidate-location' => __( 'Location', 'wp-job-manager-resumes' ),
				'resume-category'    => __( 'Category', 'wp-job-manager-resumes' ),
				'date'               => __( 'Date Posted', 'wp-job-manager-resumes' ),
			]
		);

		if ( ! get_option( 'resume_manager_enable_categories' ) ) {
			unset( $candidate_dashboard_columns['resume-category'] );
		}

		get_job_manager_template(
			'candidate-dashboard.php',
			[
				'resumes'                     => $resumes->query( $args ),
				'max_num_pages'               => $resumes->max_num_pages,
				'candidate_dashboard_columns' => $candidate_dashboard_columns,
			],
			'wp-job-manager-resumes',
			RESUME_MANAGER_PLUGIN_DIR . '/templates/'
		);

		return ob_get_clean();
	}

	/**
	 * output_resumes function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function output_resumes( $atts ) {
		global $resume_manager;

		ob_start();

		if ( ! resume_manager_user_can_browse_resumes() ) {
			get_job_manager_template_part( 'access-denied', 'browse-resumes', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
			return ob_get_clean();
		}

		extract(
			$atts = shortcode_atts(
				apply_filters(
					'resume_manager_output_resumes_defaults',
					[
						'per_page'                  => get_option( 'resume_manager_per_page' ),
						'order'                     => 'DESC',
						'orderby'                   => 'featured',
						'show_filters'              => true,
						'show_categories'           => get_option( 'resume_manager_enable_categories' ),
						'categories'                => '',
						'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
						'show_category_multiselect' => get_option( 'resume_manager_enable_default_category_multiselect', false ),
						'selected_category'         => '',
						'show_pagination'           => false,
						'show_more'                 => true,
					]
				),
				$atts
			)
		);

		$categories = array_filter( array_map( 'trim', explode( ',', $categories ) ) );
		$keywords   = '';
		$location   = '';
		$skills     = '';

		// String and bool handling
		$show_filters              = $this->string_to_bool( $show_filters );
		$show_categories           = $this->string_to_bool( $show_categories );
		$show_category_multiselect = $this->string_to_bool( $show_category_multiselect );
		$show_more                 = $this->string_to_bool( $show_more );
		$show_pagination           = $this->string_to_bool( $show_pagination );

		if ( ! is_null( $featured ) ) {
			$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, [ '1', 'true', 'yes' ] ) ? true : false;
		}

		if ( ! empty( $_GET['search_keywords'] ) ) {
			$keywords = sanitize_text_field( $_GET['search_keywords'] );
		}

		if ( ! empty( $_GET['search_location'] ) ) {
			$location = sanitize_text_field( $_GET['search_location'] );
		}

		if ( ! empty( $_GET['search_category'] ) ) {
			$selected_category = sanitize_text_field( $_GET['search_category'] );
		}

		if ( ! empty( $_GET['search_skills'] ) ) {
			$skills = sanitize_text_field( $_GET['search_skills'] );
		}

		if ( $show_filters ) {

			get_job_manager_template(
				'resume-filters.php',
				[
					'per_page'                  => $per_page,
					'orderby'                   => $orderby,
					'order'                     => $order,
					'show_categories'           => $show_categories,
					'categories'                => $categories,
					'selected_category'         => $selected_category,
					'atts'                      => $atts,
					'location'                  => $location,
					'keywords'                  => $keywords,
					'show_category_multiselect' => $show_category_multiselect,
					'skills'                    => $skills,
				],
				'wp-job-manager-resumes',
				RESUME_MANAGER_PLUGIN_DIR . '/templates/'
			);

			get_job_manager_template( 'resumes-start.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
			get_job_manager_template( 'resumes-end.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );

			if ( ! $show_pagination && $show_more ) {
				echo '<a class="load_more_resumes" href="#" style="display:none;"><strong>' . __( 'Load more resumes', 'wp-job-manager-resumes' ) . '</strong></a>';
			}
		} else {

			$resumes = get_resumes(
				apply_filters(
					'resume_manager_output_resumes_args',
					[
						'search_categories' => $categories,
						'orderby'           => $orderby,
						'order'             => $order,
						'posts_per_page'    => $per_page,
						'featured'          => $featured,
					]
				)
			);

			if ( $resumes->have_posts() ) : ?>

				<?php get_job_manager_template( 'resumes-start.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

				<?php
				while ( $resumes->have_posts() ) :
					$resumes->the_post();
					?>
					<?php get_job_manager_template_part( 'content', 'resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
				<?php endwhile; ?>

				<?php get_job_manager_template( 'resumes-end.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

				<?php if ( $resumes->found_posts > $per_page && $show_more ) : ?>

					<?php wp_enqueue_script( 'wp-resume-manager-ajax-filters' ); ?>

					<?php if ( $show_pagination ) : ?>
						<?php echo get_job_listing_pagination( $resumes->max_num_pages ); ?>
					<?php else : ?>
						<a class="load_more_resumes" href="#"><strong><?php _e( 'Load more resumes', 'wp-job-manager-resumes' ); ?></strong></a>
					<?php endif; ?>

				<?php endif; ?>

				<?php
					else :
						do_action( 'resume_manager_output_resumes_no_results' );
					endif;

					wp_reset_postdata();
		}

		$data_attributes_string = '';
		$data_attributes        = [
			'location'        => $location,
			'keywords'        => $keywords,
			'show_filters'    => $show_filters ? 'true' : 'false',
			'show_pagination' => $show_pagination ? 'true' : 'false',
			'per_page'        => $per_page,
			'orderby'         => $orderby,
			'order'           => $order,
			'categories'      => implode( ',', $categories ),
		];
		if ( ! is_null( $featured ) ) {
			$data_attributes['featured'] = $featured ? 'true' : 'false';
		}

		/**
		 * Pass additional data to the resume listing <div> wrapper.
		 *
		 * @since 1.18.5
		 *
		 * @param array $data_attributes {
		 *     Key => Value array of data attributes to pass.
		 *
		 *     @type string $$key Value to pass as a data attribute.
		 * }
		 * @param array $atts            Attributes for the shortcode.
		 */
		$data_attributes = apply_filters( 'job_manager_resumes_shortcode_data_attributes', $data_attributes, $atts );

		foreach ( $data_attributes as $key => $value ) {
			$data_attributes_string .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		/**
		 * Get current output buffer contents ( ob_get_clean() )
		 *
		 * @since 1.18.5
		 */
		$resume_listings_output = apply_filters( 'job_manager_resume_listings_output', ob_get_clean() );

		return '<div class="resumes" ' . $data_attributes_string . '>' . $resume_listings_output . '</div>';
	}

	/**
	 * Output some content when no results were found
	 */
	public function output_no_results() {
		get_job_manager_template( 'content-no-resumes-found.php', [], 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
	}

	/**
	 * Get string as a bool
	 *
	 * @param  string $value
	 * @return bool
	 */
	public function string_to_bool( $value ) {
		return ( is_bool( $value ) && $value ) || in_array( $value, [ '1', 'true', 'yes' ] ) ? true : false;
	}
}

new WP_Resume_Manager_Shortcodes();
