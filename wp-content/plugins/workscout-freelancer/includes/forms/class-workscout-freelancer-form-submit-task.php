<?php
/**
 * File containing the WP_Task_Manager_Form_Submit_Task.
 *
 * @package wp-job-manager-tasks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Task_Manager_Form_Submit_Task class.
 */
class WorkScout_Freelancer_Form_Submit_Task extends WP_Job_Manager_Form {

	/**
	 * Form name slug.
	 *
	 * @var string
	 */
	public $form_name = 'submit-task';

	/**
	 * Current task ID.
	 *
	 * @var int
	 */
	protected $task_id;


	/**
	 * The single instance of the class.
	 *
	 * @var WorkScout_Freelancer_Form_Submit_Task
	 */
	protected static $instance = null;

	/**
	 * Main Instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'process' ] );
		add_action( 'submit_task_form_start', [ $this, 'output_submit_form_nonce_field' ] );
		add_action( 'preview_task_form_start', [ $this, 'output_preview_form_nonce_field' ] );

	


		$this->steps = (array) apply_filters(
			'submit_task_steps',
			[
				'submit'  => [
					'name'     => __( 'Submit Details', 'workscout-freelancer' ),
					'view'     => [ $this, 'submit' ],
					'handler'  => [ $this, 'submit_handler' ],
					'priority' => 10,
				],
				'preview' => [
					'name'     => __( 'Preview', 'workscout-freelancer' ),
					'view'     => [ $this, 'preview' ],
					'handler'  => [ $this, 'preview_handler' ],
					'priority' => 20,
				],
				'done'    => [
					'name'     => __( 'Done', 'workscout-freelancer' ),
					'view'     => [ $this, 'done' ],
					'handler'  => '',
					'priority' => 30,
				],
			]
		);

		uasort( $this->steps, [ $this, 'sort_by_priority' ] );
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		// Get step/job.
		if ( isset( $_REQUEST['step'] ) ) {
			$this->step = is_numeric( $_REQUEST['step'] ) ? max( absint( $_REQUEST['step'] ), 0 ) : array_search( sanitize_text_field( $_REQUEST['step'] ), array_keys( $this->steps ), true );
		} elseif (!empty($_GET['step'])) {
			$this->step = is_numeric($_GET['step']) ? max(absint($_GET['step']), 0) : array_search(sanitize_text_field($_GET['step']), array_keys($this->steps), true);
		}


		$this->task_id = !empty($_GET['task_id']) ? absint($_GET['task_id']) : 0;
		if (0 === $this->task_id) {
			$this->task_id = !empty($_POST['task_id']) ? absint($_POST['task_id']) : 0;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended

		if ( !workscout_freelancer_user_can_edit_task( $this->task_id ) ) {
			$this->task_id = 0;
		}

		// Load task details.
		if ( $this->task_id ) {
			$task_status = get_post_status( $this->task_id );
			if ( 'expired' === $task_status ) {
				if ( !workscout_freelancer_user_can_edit_task( $this->task_id ) ) {
					$this->task_id = 0;
					$this->step    = 0;
				}
			} elseif (
				0 === $this->step
				&& ! in_array( $task_status, apply_filters( 'workscout_freelancer_valid_submit_task_statuses', [ 'preview' ] ), true )
				 ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Safe use of input.
			 {
				$this->task_id = 0;
				$this->step    = 0;
			}
		}

		// // Clear job ID if it isn't a published job.
		// if (
		// 	empty( $this->task_id )
		// 	|| 'task' !== get_post_type( $this->task_id )
		// 	|| 'publish' !== get_post_status( $this->task_id )
		// ) {
		// 	$this->task_id = 0;
		// }
	}

	/**
	 * Get the submitted task ID.
	 *
	 * @return int
	 */
	public function get_task_id() {
		return absint( $this->task_id );
	}

	/**
	 * Get the job ID if applying.
	 *
	 * @return int
	 */
	// public function get_job_id() {
	// 	return absint( $this->job_id );
	// }

	/**
	 * Get a field from either task manager or job manager. Used by `task-submit.php`
	 * and `form-fields/repeated-field.php` templates.
	 *
	 * @param string $key   Name of field.
	 * @param array  $field Configuration arguments for the field.
	 */
	public function get_field_template( $key, $field ) {
		switch ( $field['type'] ) {
			case 'radio':
	
				get_job_manager_template(
					'form-fields/' . $field['type'] . '-field.php',
					[
						'key'   => $key,
						'field' => $field,
						'class' => $this,
					],
					'workscout-freelancer',
					WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
				);
				break;
			case 'dynamic-input':
	
				get_job_manager_template(
					'form-fields/' . $field['type'] . '-field.php',
					[
						'key'   => $key,
						'field' => $field,
						'class' => $this,
					],
					'workscout-freelancer',
					WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
				);
				break;
			default:
				get_job_manager_template(
					'form-fields/' . $field['type'] . '-field.php',
					[
						'key'   => $key,
						'field' => $field,
						'class' => $this,
					],
					
				);
				break;
		}
	}

	/**
	 * Initialize fields.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}

		$max_skills        = get_option( 'task_manager_max_skills' );
		$max_skills_notice = null;
		if ( $max_skills ) {
			// translators: Placeholder %d is the maximum number of skills a visitor can add.
			$max_skills_notice = ' ' . sprintf( __( 'Maximum of %d.', 'workscout-freelancer' ), $max_skills );
		}

		$this->fields = apply_filters(
			'submit_task_form_fields',
			[
				'company_fields' => [
					'company_id' => array(
						'label'         => esc_html__('Company', 'mas-wp-job-manager-company'),
						'type'          => 'select',
						'required'      => false,
						'placeholder'   => esc_html__('Choose a Company', 'mas-wp-job-manager-company'),
						'priority'      => 0,
						
						'options'       => $this->get_companies_list(),
					),
					
				],
				'task_fields' => [
					
					'task_title'       => [
						'label'         => __('Project Name', 'workscout-freelancer' ),
						'type'          => 'text',
						'required'      => true,
						'placeholder'   => __( 'e.g build me a website', 'workscout-freelancer' ),
						'priority'      => 1,
						'width' 		=> 4
					],
					'task_category'      => [
						'label'         => __('Category', 'workscout-freelancer'),
						'type'          => 'term-select',
						'taxonomy'      => 'task_category',
						'required'      => true,
						'placeholder'   => '',
						'priority'      => 2,
						'default' => '',
						'width' 		=> 4
					],
					'task_location'   => [
						'label'         => __( 'Location', 'workscout-freelancer' ),
						'type'          => 'text',
						'required'      => true,
						'placeholder'   => __( 'e.g. "London, UK", "New York", "Houston, TX"', 'workscout-freelancer' ),
						'priority'      => 3,
						'width' 		=> 4
					],
					'remote_position'     => [
						'label'       => __('Remote Position', 'workscout-freelancer'),
						'description' => __('Select if remote job.', 'workscout-freelancer'),
						'type'        => 'checkbox',
						'required'    => false,
						'priority'    => 4,
						'width' 		=> 2
					],
					'task_deadline'   => [
						'label'         => __( 'Deadline', 'workscout-freelancer' ),
						'type'          => 'date',
						'required'      => true,
						
						'priority'      => 11,
						'width' 		=> 2
					],
					'task_type'     => [
						'label'       => __('Billing type', 'workscout-freelancer'),
						'description' => __('Select billing type.', 'workscout-freelancer'),
						'type'        => 'radio',
						'required'    => false,
						'options'  => array(
							'fixed' => __('Fixed Price Project', 'workscout-freelancer'),
							'hourly' => __('Hourly Project', 'workscout-freelancer'),
						),
						'priority'    => 5,
						'width' 		=> 4
					],
					'budget_min'   => [
						'label'         => __('What is your estimated budget?', 'workscout-freelancer' ),
						'type'          => 'number',
						'required'      => false,
						'placeholder'   => __('Budget Min.', 'workscout-freelancer' ),
						'currency' 		=> get_workscout_currency_symbol(),
						'priority'      => 7,
						'width' 		=> 3
						
					],
					'budget_max'   => [
						'label'         => '&nbsp;',
						'type'          => 'number',
						'required'      => false,
						'placeholder'   => __( 'Budget Max.', 'workscout-freelancer' ),
						'currency' 		=> get_workscout_currency_symbol(),
						'priority'      => 8,
						'width' 		=> 3
						
					],
				
					'hourly_min'   => [
						'label'         => __('What is your min hourly rate ?', 'workscout-freelancer' ),
						'type'          => 'number',
						'required'      => false,
						'placeholder'   => __( 'Minimum', 'workscout-freelancer' ),
						'priority'      => 9,
						'currency' 		=> get_workscout_currency_symbol(),
						'width' 		=> 3
						
					],
					'hourly_max'   => [
						'label'         =>'&nbsp;',
						'type'          => 'number',
						'required'      => false,
						'placeholder'   => __( 'Maximum', 'workscout-freelancer' ),
						'priority'      => 10,
						'currency' 		=> get_workscout_currency_symbol(),
						'width' 		=> 3
						
					],


					
					'task_skill'      => [
						'label'         => __('Skills', 'workscout-freelancer'),
						'type'          => 'dynamic-input',
						'taxonomy'      => 'task_skill',
						'required'      => true,
						'multiple'      => true,
						'placeholder'   => '',
						'priority'      =>11,
						'default' 		=> '',
						'width' 		=> 4
					],
					'task_content'       => [
						'label'         => __('Describe your Project', 'workscout-freelancer'),
						'type'          => 'wp-editor',
						'required'      => true,
						'placeholder'   => '',
						'priority'      => 12,
						'personal_data' => true,
						'width' 		=> 12
					],
					'task_file'          => [
						'label'         => __( 'Attachments (e.g. project brief)', 'workscout-freelancer' ),
						'type'          => 'attachments',
						'required'      => false,
						'ajax'          => true,
						'multiple'          => true,
						// translators: Placeholder %s is the maximum file size of the upload.
						'description'   => sprintf( __( 'Optionally upload your documents to view. Max. file size: %s.', 'workscout-freelancer' ), size_format( wp_max_upload_size() ) ),
						'priority'      => 13,
						'placeholder'   => '',
						'personal_data' => true,
						'width' 		=> 12
					],
				],
			]
		);

	}

	/**
	 * Reset the `fields` variable so it gets reinitialized. This should only be
	 * used for testing!
	 */
	public function reset_fields() {
		$this->fields = null;
	}

	/**
	 * Get the value of a repeated fields (e.g. education, links).
	 *
	 * @param string $field_prefix Prefix added to the field names.
	 * @param array  $fields       List of the fields to be repeated.
	 * @return array
	 */
	public function get_repeated_field( $field_prefix, $fields ) {
		$items = [];

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Safe use of input and sanitized below.
		$input_repeated_row = ! empty( $_POST[ 'repeated-row-' . $field_prefix ] ) ? wp_unslash( $_POST[ 'repeated-row-' . $field_prefix ] ) : false;

		if ( $input_repeated_row && is_array( $input_repeated_row ) ) {
			// Sanitize the input "repeated-row-{$field_prefix}" from above.
			$indexes = array_map( 'absint', $input_repeated_row );

			foreach ( $indexes as $index ) {
				$item = [];
				foreach ( $fields as $key => $field ) {
					$field_name = $field_prefix . '_' . $key . '_' . $index;
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Input sanitized below. Nonce check in standard edit/submit flows.
					$input_field_value = isset( $_POST[ $field_name ] ) ? wp_unslash( $_POST[ $field_name ] ) : null;

					switch ( $field['type'] ) {
						case 'textarea':
							// Sanitize text area input.
							$item[ $key ] = wp_kses_post( $input_field_value );
							break;
						case 'file':
							try {
								$file = $this->upload_file( $field_name, $field );
							} catch ( Exception $e ) {
								$file = false;
							}

							// Fetch and sanitize file input using `\WP_Job_Manager_Form::get_posted_field()`.
							if ( ! $file ) {
								$file = $this->get_posted_field( 'current_' . $field_name, $field );
							} elseif ( is_array( $file ) ) {
								$file = array_filter( array_merge( $file, (array) $this->get_posted_field( 'current_' . $field_name, $field ) ) );
							}

							$item[ $key ] = $file;
							break;
						default:
							$sanitize_callback = 'sanitize_text_field';

							if ( isset( $field['sanitizer'] ) ) {
								$sanitize_callback = $field['sanitizer'];
							}

							// Fetch and sanitize all other input.
							if ( is_array( $input_field_value ) ) {
								$item[ $key ] = array_filter( array_map( $sanitize_callback, $input_field_value ) );
							} else {
								$item[ $key ] = call_user_func( $sanitize_callback, $input_field_value );
							}
							break;
					}
					if ( empty( $item[ $key ] ) && ! empty( $field['required'] ) ) {
						continue 2;
					}
				}
				$items[] = $item;
			}
		}
		return $items;
	}



	/**
	 * Get the value of a posted repeated field
	 *
	 * @since  1.22.4
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	public function get_posted_repeated_field( $key, $field ) {
		return apply_filters( 'submit_task_form_fields_get_repeated_field_data', $this->get_repeated_field( $key, $field['fields'] ) );
	}

	/**
	 * Get the value of a posted file field
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	public function get_posted_links_field( $key, $field ) {
		return apply_filters( 'submit_task_form_fields_get_links_data', $this->get_repeated_field( $key, $field['fields'] ) );
	}

	/**
	 * Get the value of a posted file field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	public function get_posted_education_field( $key, $field ) {
		return apply_filters( 'submit_task_form_fields_get_education_data', $this->get_repeated_field( $key, $field['fields'] ) );
	}

	/**
	 * Get the value of a posted file field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	public function get_posted_experience_field( $key, $field ) {
		return apply_filters( 'submit_task_form_fields_get_experience_data', $this->get_repeated_field( $key, $field['fields'] ) );
	}

	/**
	 * Validate the posted fields.
	 *
	 * @param array $values Input values submitted.
	 * @return WP_Error|bool
	 * @throws Exception During validation error.
	 */
	protected function validate_fields( $values ) {
		foreach ( $this->fields as $group_key => $fields ) {
			foreach ( $fields as $key => $field ) {
				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {
					// translators: Placeholder %s is the name of the required field.
					// check if the fields with id remote_job is checked if it is don't require location tab
					if($key == 'task_location' && isset($values[ $group_key ]['remote_position']) && $values[ $group_key ]['remote_position'] == 1){
						continue;
					}
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'workscout-freelancer' ), $field['label'] ) );
				}
				if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], [ 'term-checklist', 'term-select', 'term-multiselect' ], true ) ) {
				

					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						foreach ( $values[ $group_key ][ $key ] as $term ) {
							if ( ! term_exists( $term, $field['taxonomy'] ) ) {
								// translators: Placeholder %s is the name of the invalid field.
								return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'workscout-freelancer' ), $field['label'] ) );
							}
						}
					} elseif ( ! empty( $values[ $group_key ][ $key ] ) ) {
						if ( ! term_exists( $values[ $group_key ][ $key ], $field['taxonomy'] ) ) {
							// translators: Placeholder %s is the name of the invalid field.
							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'workscout-freelancer' ), $field['label'] ) );
						}
					}
				}

				if ( 'candidate_email' === $key ) {
					if ( ! empty( $values[ $group_key ][ $key ] ) && ! is_email( $values[ $group_key ][ $key ] ) ) {
						throw new Exception( __( 'Please enter a valid email address', 'workscout-freelancer' ) );
					}
				}

				if ( 'task_skill' === $key ) {
					if ( is_string( $values[ $group_key ][ $key ] ) ) {
						$raw_skills = explode( ',', $values[ $group_key ][ $key ] );
					} else {
						$raw_skills = $values[ $group_key ][ $key ];
					}
					$max = get_option( 'task_manager_max_skills' );

					if ( $max && count( $raw_skills ) > $max ) {
						// translators: Placeholder %d is the maximum number of skills they can enter.
						return new WP_Error( 'validation-error', sprintf( __( 'Please enter no more than %d skills.', 'workscout-freelancer' ), $max ) );
					}
				}

				if ( 'file' === $field['type'] ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( [ $values[ $group_key ][ $key ] ] );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							if ( is_numeric( $file_url ) ) {
								continue;
							}
							$file_url = esc_url( $file_url, [ 'http', 'https' ] );
							if ( empty( $file_url ) ) {
								throw new Exception( __( 'Invalid attachment provided.', 'workscout-freelancer' ) );
							}
						}
					}
				}
			}
		}

		return apply_filters( 'submit_task_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * Submit Step
	 */
	public function submit() {
		$this->init_fields();

		// Load data if neccessary.
		if ( $this->task_id ) {
			$task = get_post( $this->task_id );
			foreach ( $this->fields as $group_key => $fields ) {
				foreach ( $fields as $key => $field ) {
					
					switch ( $key ) {
						case 'task_title':
							$this->fields[ $group_key ][ $key ]['value'] = $task->post_title;
							break;
						case 'task_content':
							$this->fields[ $group_key ][ $key ]['value'] = $task->post_content;
							break;
						case 'task_skill':
							
							$this->fields[ $group_key ][ $key ]['value'] = implode( ', ', wp_get_object_terms( $task->ID, 'task_skill', [ 'fields' => 'names' ] ) );
							break;
						case 'task_category':
							
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $task->ID, 'task_category', [ 'fields' => 'ids' ] );
							break;
						default:
							$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $task->ID, '_' . $key, true );
							break;
					}
				}
			}
			$this->fields = apply_filters( 'submit_task_form_fields_get_task_data', $this->fields, $task );
		} elseif (
			is_user_logged_in()
			&& empty( $_POST['submit_task'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Using input safely.
		) {
			$user = wp_get_current_user();
			foreach ( $this->fields as $group_key => $fields ) {
				foreach ( $fields as $key => $field ) {
					switch ( $key ) {
						case 'candidate_name':
							$this->fields[ $group_key ][ $key ]['value'] = $user->first_name . ' ' . $user->last_name;
							break;
						case 'candidate_email':
							$this->fields[ $group_key ][ $key ]['value'] = $user->user_email;
							break;
					}
				}
			}
			$this->fields = apply_filters( 'submit_task_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}
		
		get_job_manager_template(
			'task-submit.php',
			[
				'class'              => $this,
				'form'               => $this->form_name,
				'task_id'          => $this->get_task_id(),
			//	'job_id'             => $this->get_job_id(),
				'action'             => $this->get_action(),
				'company_fields'      => $this->get_fields( 'company_fields' ),
				'task_fields'      => $this->get_fields( 'task_fields' ),
				'step'               => $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_task_form_submit_button_text', __( 'Preview &rarr;', 'workscout-freelancer' ) ),
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
	}

	/**
	 * Submit Step is posted.
	 */
	public function submit_handler() {
		
		try {

			// Init fields.
			$this->init_fields();

			// Get posted values.
			$values = $this->get_posted_fields();

			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Check happens later when possible.
			if ( empty( $_POST['submit_task'] ) ) {
				return;
			}

			$this->check_submit_form_nonce_field();



			// Validate required.
			$validation_result = $this->validate_fields( $values );
			if ( is_wp_error( ( $validation_result ) ) ) {
				throw new Exception( $validation_result->get_error_message() );
			}

			// Account creation.
			

			if ( ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post your task.', 'workscout-freelancer' ) );
			}

			

			// Update the job.
			$this->save_task( $values['task_fields']['task_title'], $values['task_fields']['task_content'], $this->task_id ? '' : 'preview', $values );
			$this->update_task_data( $values );

			// Successful, show next step.
			$this->step++;

		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Update or create a job listing from posted data.
	 *
	 * @param string $post_title   Post title.
	 * @param string $post_content Post content.
	 * @param string $status       Post status to save.
	 * @param array  $values       Values from the form.
	 */
	protected function save_task( $post_title, $post_content, $status = 'preview', $values = [] ) {
		$task_data = [
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'task',
			'comment_status' => 'closed',
		];

		

		if ($status) {
			$task_data['post_status'] = $status;
		}

		$task_data = apply_filters('submit_task_form_save_job_data', $task_data, $post_title, $post_content, $status, $values);
		
		if ($this->task_id) {
		
			$task_data['ID'] = $this->task_id;
			wp_update_post($task_data);
		} else {
			$this->task_id = wp_insert_post($task_data);
			
			if (!headers_sent()) {
				$submitting_key = uniqid();

				setcookie('wp-job-manager-submitting-task-id', $this->task_id, false, COOKIEPATH, COOKIE_DOMAIN, false);
				setcookie('wp-job-manager-submitting-task-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false);

				update_post_meta($this->task_id, '_submitting_key', $submitting_key);
			}
		}
		// // Get random key.
		// if ( $this->task_id ) {
		// 	$prefix = get_post_meta( $this->task_id, '_task_name_prefix', true );

		// 	if ( ! $prefix ) {
		// 		$prefix = wp_generate_password( 10 );
		// 	}
		// } else {
		// 	$prefix = wp_generate_password( 10 );
		// }

		// $task_slug   = [];
		// $task_slug[] = current( explode( ' ', $post_title ) );
		// $task_slug[] = $prefix;

		// if ( ! empty( $values['task_fields']['task_title'] ) ) {
		// 	$task_slug[] = $values['task_fields']['task_title'];
		// }

		// if ( ! empty( $values['task_fields']['task_location'] ) ) {
		// 	$task_slug[] = $values['task_fields']['task_location'];
		// }

		// $data = [
		// 	'post_title'     => $post_title,
		// 	'post_content'   => $post_content,
		// 	'post_type'      => 'task',
		// 	'comment_status' => 'closed',
		// 	'post_password'  => '',
		// 	'post_name'      => sanitize_title( implode( '-', $task_slug ) ),
		// ];

		// if ( $status ) {
		// 	$data['post_status'] = $status;
		// }

		// $data = apply_filters( 'submit_task_form_save_task_data', $data, $post_title, $post_content, $status, $values, $this );

		// if ( $this->task_id ) {
		// 	$data['ID'] = $this->task_id;
		// 	wp_update_post( $data );
		// } else {
		// 	$this->task_id = wp_insert_post( $data );
		// 	update_post_meta( $this->task_id, '_task_name_prefix', $prefix );
		// 	update_post_meta( $this->task_id, '_public_submission', true );

		// 	// If and only if we're dealing with a logged out user and that is allowed, allow the user to continue a submission after it was started.
		// 	if ( ! is_user_logged_in()  ) {
		// 		$submitting_key = sha1( uniqid() );
		// 		setcookie( 'wp-job-manager-submitting-task-key-' . $this->task_id, $submitting_key, 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		// 		update_post_meta( $this->task_id, '_submitting_key', $submitting_key );
		// 	}

			
		// }
	}

	/**
	 * Set job meta + terms based on posted values
	 *
	 * @param  array $values
	 */
	protected function update_task_data( $values ) {
		// Set defaults.
		add_post_meta( $this->task_id, '_featured', 0, true );

		// Reset submission lifecycle flag.
		delete_post_meta( $this->task_id, '_submission_finalized' );

		$maybe_attach = [];

		// Loop fields and save meta and term data.
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies.
				if ( ! empty( $field['taxonomy'] ) ) {
				
					if( $field['type'] != 'dynamic-input' ){
						if ( is_array( $values[ $group_key ][ $key ] ) ) {
					
							wp_set_object_terms( $this->task_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
						} else {
							
							wp_set_object_terms( $this->task_id, [ $values[ $group_key ][ $key ] ], $field['taxonomy'], false );
						}
					}
					// Save meta data.
				} else {
					
					if ('task_location' === $key ) {
						if ( ! WP_Job_Manager_Geocode::has_location_data($this->task_id ) ) {
							
								WP_Job_Manager_Geocode::generate_location_data($this->task_id, sanitize_text_field(  $values[$group_key][$key] ) );
						}
					}
                
            
					update_post_meta( $this->task_id, '_' . $key, $values[ $group_key ][ $key ] );
				}

				

				// Handle attachments.
				if ( 'file' === $field['type'] ) {
					// Must be absolute.
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						foreach ( $values[ $group_key ][ $key ] as $file_url ) {
							$maybe_attach[] = str_replace( [ WP_CONTENT_URL, site_url() ], [ WP_CONTENT_DIR, ABSPATH ], $file_url );
						}
					} else {
						$maybe_attach[] = str_replace( [ WP_CONTENT_URL, site_url() ], [ WP_CONTENT_DIR, ABSPATH ], $values[ $group_key ][ $key ] );
					}
					
				}
			}
		}

		if ( isset( $values['task_fields']['task_skill'] ) ) {

			$tags     = [];
			$raw_tags = $values['task_fields']['task_skill'];
	
			if ( is_string( $raw_tags ) ) {
				// Explode and clean.
				$raw_tags = array_filter( array_map( 'sanitize_text_field', explode( ',', $raw_tags ) ) );
				
				if ( ! empty( $raw_tags ) ) {
					foreach ( $raw_tags as $tag ) {
						$term = get_term_by( 'name', $tag, 'task_skill' );
						if ( $term ) {
							$tags[] = $term->term_id;
						} else {
							$term = wp_insert_term( $tag, 'task_skill' );

							if ( ! is_wp_error( $term ) ) {
								$tags[] = $term['term_id'];
							}
						}
					}
				}
			} else {
				$tags = array_map( 'absint', $raw_tags );
		
			}

			wp_set_object_terms( $this->task_id, $tags, 'task_skill', false );
		}

		// Handle attachments.
		if ( count( $maybe_attach ) ) {
			
			/** WordPress Administration Image API */
			include_once ABSPATH . 'wp-admin/includes/image.php';

			// Get attachments.
			$attachments     = get_posts( 'post_parent=' . $this->task_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );
			$attachment_urls = [];

			// Loop attachments already attached to the job.
			foreach ( $attachments as $attachment_key => $attachment ) {
				$attachment_urls[] = str_replace( [ WP_CONTENT_URL, site_url() ], [ WP_CONTENT_DIR, ABSPATH ], wp_get_attachment_url( $attachment ) );
			}

			foreach ( $maybe_attach as $attachment_url ) {
				$attachment_url = esc_url( $attachment_url, [ 'http', 'https' ] );
				
				if ( empty( $attachment_url ) ) {
					continue;
				}

				if ( ! in_array( $attachment_url, $attachment_urls, true ) ) {
					$attachment = [
						'post_title'   => get_the_title( $this->task_id ),
						'post_content' => '',
						'post_status'  => 'inherit',
						'post_parent'  => $this->task_id,
						'guid'         => $attachment_url,
					];

					$info = wp_check_filetype( $attachment_url );
					if ( $info ) {
						$attachment['post_mime_type'] = $info['type'];
					}

					$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->task_id );
					
					if ( ! is_wp_error( $attachment_id ) ) {
						wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
					}
				}
			}
		}

		do_action( 'workscout_freelancer_update_task_data', $this->task_id, $values );
	}

	/**
	 * Preview Step
	 */
	public function preview() {
		global $post, $task_preview;

		$this->check_valid_task();

	//	wp_enqueue_script( 'wp-task-manager-task-submission' );

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Job preview depends on temporary override. Reset below.
		$post           = get_post( $this->task_id );
		$task_preview = true;

		setup_postdata( $post );
		get_job_manager_template(
			'task-preview.php',
			[
				'form' => $this,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
		wp_reset_postdata();
	}

	/**
	 * Preview Step Form handler
	 */
	public function preview_handler() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Input is used safely.
		if ( empty( $_POST ) ) {
			return;
		}

		$this->check_preview_form_nonce_field();
		$this->check_valid_task();

		// Edit = show submit form again.
		if ( ! empty( $_POST['edit_task'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce was checked above.
			$this->step--;
		}

		// Continue = change job status then show next screen.
		if ( ! empty( $_POST['continue'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce was checked above.
			$task = get_post( $this->task_id );

			if ( in_array( $task->post_status, [ 'preview', 'expired' ], true ) ) {
				// Reset expiry.
				delete_post_meta( $task->ID, '_task_expires' );

				// Update listing.
				$update_task                  = [];
				$update_task['ID']            = $task->ID;
				$update_task['post_date']     = current_time( 'mysql' );
				$update_task['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_task['post_author']   = get_current_user_id();
				$update_task['post_status']   = apply_filters( 'submit_task_post_status', get_option( 'task_manager_submission_requires_approval' ) ? 'pending' : 'publish', $task );

				wp_update_post( $update_task );
			}

			$this->step++;

			/**
			 * Do not redirect if WCPL is set to choose package before submitting listing
			 *
			 * By not redirecting, we allow $this->process() (@see abstract-wp-job-manager-form.php) to call the 'wc-process-package'
			 * handler first, instead of view, which does not exist in 'wc-process-package' (and would be called first on redirect).
			 */
			if ( 'before' !== get_option( 'task_paid_listings_flow' ) ) {
				wp_safe_redirect(
					esc_url_raw(
						add_query_arg(
							[
								'step'      => $this->step,
								'task_id' => $this->task_id,
							]
						)
					)
				);
				exit;
			}
		}
	}

	/**
	 * Done Step.
	 */
	public function done() {
		$this->check_valid_task();

		get_job_manager_template(
			'task-submitted.php',
			[
				'task' => get_post( $this->task_id ),
				'task_id' => $this->task_id,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);

		delete_post_meta( $this->task_id, '_submitting_key' );

		// Allow application.
		
	}

	/**
	 * Validate the task ID passed. Respond with a 400 Bad Request error if an invalid ID is passed.
	 * `self::$task_id` is already cleared out in the constructor if the user doesn't have
	 * permission to access it, but we still file actions without checking its value.
	 */
	private function check_valid_task() {
		if (
			! empty( $this->task_id )
			&& 'task' === get_post_type( $this->task_id )
		) {
			return;
		}

		wp_die(
			esc_html__( 'Invalid task', 'workscout-freelancer' ),
			'',
			[
				'response'  => 400,
				'back_link' => true,
			]
		);
	}

	/**
	 * Output the nonce field on job preview form.
	 *
	 * @access private
	 */
	public function output_preview_form_nonce_field() {
		wp_nonce_field( 'preview-task-' . $this->task_id, '_wpjm_nonce' );
	}

	/**
	 * Check the nonce field on the preview form.
	 *
	 * @access private
	 */
	public function check_preview_form_nonce_field() {
		if (
			empty( $_REQUEST['_wpjm_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpjm_nonce'] ), 'preview-task-' . $this->task_id ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
		) {
			wp_nonce_ays( 'preview-task-' . $this->task_id );
			die();
		}
	}

	/**
	 * Output the nonce field on job submission form.
	 *
	 * @access private
	 */
	public function output_submit_form_nonce_field() {
		wp_nonce_field( 'submit-task-' . $this->task_id, '_wpjm_nonce' );
	}

	/**
	 * Check the nonce field on the submit form.
	 *
	 * @access private
	 */
	public function check_submit_form_nonce_field() {
		if (
			empty( $_REQUEST['_wpjm_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpjm_nonce'] ), 'submit-task-' . $this->task_id ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
		) {
			wp_nonce_ays( 'submit-task-' . $this->task_id );
			die();
		}
	}


	/**
	 * Get the task fields use on the submission form.
	 *
	 * @return array
	 */
	public static function get_task_fields() {
		$instance = self::instance();
		$instance->init_fields();

		return $instance->get_fields( 'task_fields' );
	}

	public function get_companies_list(){
	
	
		global $current_user;
		$options = array(
			''  => esc_html__('Private Listing', 'workscout-freelancer'),
		);

		if (is_user_logged_in() && !empty($current_user)) {
			$args = array(
				'post_type'     => 'company',
				'orderby'       => 'title',
				'order'         => 'ASC',
				'numberposts'   => -1,
				
			);

			$args['author'] = $current_user->ID;
			$companies = get_posts(apply_filters('masjm_get_current_user_companies_args', $args));

			if (!empty($companies)) {
				foreach ($companies as $company) {
					$options[$company->ID] = get_the_title($company);
				}
			} else {
				$options = array(
					''  => esc_html__('Private Listing', 'workscout-freelancer'),
				);
			}
		} else {
			$options = array(
				''  => esc_html__('User Not Logged In', 'workscout-freelancer'),
			);
		}

		return $options;
	}
	
}
