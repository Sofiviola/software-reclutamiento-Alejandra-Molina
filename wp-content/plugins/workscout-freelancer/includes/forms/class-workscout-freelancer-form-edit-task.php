<?php

/**
 * File containing the WP_Resume_Manager_Form_Edit_Resume.
 *
 * @package wp-job-manager-tasks
 */

if (!defined('ABSPATH')) {
	exit;
}
require_once 'class-workscout-freelancer-form-submit-task.php';

/**
 * WP_Resume_Manager_Form_Edit_Resume class.
 */
class WorkScout_Freelancer_Form_Edit_Task extends WorkScout_Freelancer_Form_Submit_Task
{

	/**
	 * Form name slug.
	 *
	 * @var string
	 */
	public $form_name = 'edit-task';

	/**
	 * Messaged shown on save.
	 *
	 * @var bool|string
	 */
	private $save_message = false;

	
	/**
	 * Message shown on error.
	 *
	 * @var bool|string
	 */
	private $save_error = false;

	/**
	 * The single instance of the class.
	 *
	 * @var WorkScout_Freelancer_Form_Edit_Task
	 */
	protected static $instance = null;

	/**
	 * Main Instance
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		add_action('wp', [$this, 'submit_handler']);
		add_action('submit_task_form_start', [$this, 'output_submit_form_nonce_field']);
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		$this->task_id = !empty($_REQUEST['task_id']) ? absint($_REQUEST['task_id']) : 0;
		
		if (!workscout_freelancer_user_can_edit_task($this->task_id)) {
			$this->task_id = 0;
		}
		
		if (!empty($this->task_id)) {
			
			$published_statuses = ['publish', 'hidden'];
			$post_status        = get_post_status($this->task_id);

			if (
				(in_array($post_status, $published_statuses, true) && !workscout_freelancer_user_can_edit_published_submissions())
				|| (!in_array($post_status, $published_statuses, true) && !workscout_freelancer_user_can_edit_pending_submissions())
			) {
				$this->task_id = 0;
			}
		}
	}

	/**
	 * Output the edit task form.
	 *
	 * @param array $atts Attributes passed (ignored).
	 */
	public function output($atts = [])
	{
		if (!empty($this->save_message)) {
			echo '<div class="job-manager-message">' . wp_kses_post($this->save_message) . '</div>';
			return;
		}
		if (!empty($this->save_error)) {
			echo '<div class="job-manager-error">' . wp_kses_post($this->save_error) . '</div>';
		}

		$this->submit();
	}

	/**
	 * Submit step.
	 */
	public function submit()
	{
		
		$task = get_post($this->task_id);
		
		if (empty($this->task_id)) {
			echo wp_kses_post(wpautop(__('Invalid task', 'workscout-freelancer')));
			return;
		}

		$this->init_fields();

		foreach ($this->fields as $group_key => $group_fields) {
			foreach ($group_fields as $key => $field) {
				if (!isset($this->fields[$group_key][$key]['value'])) {
					if ('task_title' === $key) {
						$this->fields[$group_key][$key]['value'] = $task->post_title;
					} elseif ('task_content' === $key) {
						$this->fields[$group_key][$key]['value'] = $task->post_content;
					} elseif (!empty($field['taxonomy'])) {
						$this->fields[$group_key][$key]['value'] = wp_get_object_terms($task->ID, $field['taxonomy'], ['fields' => 'ids']);
					} elseif ('task_skills' === $key) {
						$this->fields[$group_key][$key]['value'] = implode(', ', wp_get_object_terms($task->ID, 'task_skill', ['fields' => 'names']));
					} else {
						$this->fields[$group_key][$key]['value'] = get_post_meta($task->ID, '_' . $key, true);
					}
				}
			}
		}

		$this->fields = apply_filters('submit_task_form_fields_get_task_data', $this->fields, $task);

		$save_button_text   = __('Save changes', 'workscout-freelancer');
		$published_statuses = ['publish', 'hidden'];
		if (
			in_array(get_post_status($this->task_id), $published_statuses, true)
			&& wpjm_published_submission_edits_require_moderation()
		) {
			$save_button_text = __('Submit changes for approval', 'workscout-freelancer');
		}

		/**
		 * Change button text for submitting changes to a task.
		 *
		 * @since 1.18.0
		 *
		 * @param string $save_button_text Button text to filter.
		 * @param int    $task_id        Resume post ID.
		 */
		$save_button_text = apply_filters('task_manager_update_task_form_submit_button_text', $save_button_text, $this->task_id);

		get_job_manager_template(
			'task-submit.php',
			[
				'class'              => $this,
				'form'               => $this->form_name,
				'job_id'             => '',
				'task_id'          => $this->get_task_id(),
				'action'             => $this->get_action(),
				'company_fields'      => $this->get_fields('company_fields'),
				'task_fields'      => $this->get_fields('task_fields'),
				'step'               => $this->get_step(),
				'submit_button_text' => $save_button_text,
			],
			'workscout-freelancer',
			WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
		);
	}

	/**
	 * Submit Step is posted.
	 */
	public function submit_handler()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Check happens later when possible.
		if (empty($_POST['submit_task'])) {
			return;
		}

		$this->check_submit_form_nonce_field();

		try {

			// Init fields.
			$this->init_fields();

			// Get posted values.
			$values = $this->get_posted_fields();

			// Validate required.
			$validation_result = $this->validate_fields($values);
			if (is_wp_error($validation_result)) {
				throw new Exception($validation_result->get_error_message());
			}

			$original_post_status = get_post_status($this->task_id);
			$save_post_status     = $original_post_status;
			if (wpjm_published_submission_edits_require_moderation()) {
				$save_post_status = 'pending';
			}

			// Update the task.
			$this->save_task($values['task_fields']['task_title'], $values['task_fields']['task_content'], $save_post_status, $values);
			$this->update_task_data($values);

			// Successful.
			$save_message = __('Your changes have been saved.', 'workscout-freelancer');
			$post_status  = get_post_status($this->task_id);
			update_post_meta($this->task_id, '_task_edited', time());
			update_post_meta($this->task_id, '_task_edited_original_status', $original_post_status);

			$published_statuses = ['publish', 'hidden'];
			if ('publish' === $post_status) {
				$save_message = $save_message . ' <a href="' . get_permalink($this->task_id) . '">' . __('View &rarr;', 'workscout-freelancer') . '</a>';
			} elseif (in_array($original_post_status, $published_statuses, true) && 'pending' === $post_status) {
				$save_message = __('Your changes have been submitted and your task will be available again once approved.', 'workscout-freelancer');

				/**
				 * Resets the task expiration date when a user submits their task listing edit for re-approval.
				 * Defaults to `false`.
				 *
				 * @since 1.18.0
				 *
				 * @param bool $reset_expiration If true, reset expiration date.
				 */
				if (apply_filters('task_manager_reset_listing_expiration_on_user_edit', false)) {
					delete_post_meta($this->task_id, '_task_expires');
				}
			}

			/**
			 * Change the message that appears when a user edits a task.
			 *
			 * @since 1.18.0
			 *
			 * @param string $save_message  Save message to filter.
			 * @param int    $task_id     Resume ID.
			 * @param array  $values        Submitted values for task.
			 */
			$this->save_message = apply_filters('task_manager_update_task_listings_message', $save_message, $this->task_id, $values);

			// Add the message and redirect to the candidate dashboard if possible.
			if (WP_Resume_Manager_Shortcodes::add_candidate_dashboard_message($this->save_message)) {
				$candidate_dashboard_page_id = get_option('task_manager_candidate_dashboard_page_id');
				$candidate_dashboard_url     = get_permalink($candidate_dashboard_page_id);
				if ($candidate_dashboard_url) {
					wp_safe_redirect($candidate_dashboard_url);
					exit;
				}
			}
		} catch (Exception $e) {
			$this->save_error = $e->getMessage();
		}
	}
}
