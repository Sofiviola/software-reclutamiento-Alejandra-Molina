<?php

/**
 * Template to show when previewing a task being submitted.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-tasks/task-preview.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-tasks
 * @category    Template
 * @version     1.18.0
 *
 * @var WorkScout_Freelancer_Form_Submit_Task $form Form object performing the action.
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<form method="post" id="task_preview" action="<?php echo esc_url($form->get_action()); ?>">
	<?php
	/**
	 * Fires at the top of the preview task form.
	 *
	 * @since 1.18.0
	 */
	do_action('preview_task_form_start');
	?>
	<div class="job_listing_preview_title">
		<input type="submit" name="continue" id="task_preview_submit_button" class="button job-manager-button-submit-listing" value="<?php echo esc_attr(apply_filters('submit_task_step_preview_submit_text', __('Submit Task &rarr;', 'workscout-freelancer'))); ?>" />
		<input type="submit" name="edit_task" class="button" value="<?php esc_attr_e('&larr; Edit task', 'workscout-freelancer'); ?>" />
		<input type="hidden" name="task_id" value="<?php echo esc_attr($form->get_task_id()); ?>" />
		
		<input type="hidden" name="step" value="<?php echo esc_attr($form->get_step()); ?>" />
		<input type="hidden" name="workscout_freelancer_form" value="<?php echo esc_attr($form->form_name); ?>" />

		<h2><?php esc_html_e('Preview', 'workscout-freelancer'); ?></h2>
	</div>
	<div class="task_preview single-task">
		<?php get_job_manager_template_part('content-single', 'task', 'workscout-freelancer', WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'); ?>
	</div>
	<?php
	/**
	 * Fires at the bottom of the preview task form.
	 *
	 * @since 1.18.0
	 */
	do_action('preview_task_form_end');
	?>
</form>