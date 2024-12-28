<?php

/**
 * Job Submission Form
 */
if (!defined('ABSPATH')) exit;

global $job_manager;
$has_company = false;
if (class_exists('MAS_WP_Job_Manager_Company') && is_user_logged_in()) {
	if (!get_option('job_manager_job_submission_required_company')) {
		$has_company = true;
	} else {
		// Get the current logged in user's ID
		$current_user_id = get_current_user_id();

		// Count the user's posts for 'resume' CPT
		$user_post_count = (int) count_user_posts($current_user_id, 'company');

		// If the user has a 'resume' CPT published
		if ($user_post_count > 0) {
			$has_company = true;
		}
	}
} else {
	$has_company = true;
}

?>


<form action="<?php echo esc_url($action); ?>" method="post" id="submit-job-form" class="job-manager-form" enctype="multipart/form-data">

	<?php do_action('submit_job_form_start'); ?>

	<?php if (apply_filters('submit_job_form_show_signin', true)) : ?>

		<?php get_job_manager_template('account-signin.php'); ?>

	<?php endif; ?>

	<div class="dashboard-list-box job-fields-submit-form <?php if (!$has_company) {
																echo "no-company-yet";
															} ?>">
		<div class="dashboard-box">

			<div class="headline">
				<h3><i class="icon-feather-folder-plus"></i> <?php esc_html_e('Job Submission Form', 'workscout'); ?></h3>
			</div>
			<?php if (job_manager_user_can_post_job()) : ?>
				<div class="submit-page">
					<!-- Job Information Fields -->
					<?php do_action('submit_job_form_job_fields_start'); ?>

					<?php foreach ($job_fields as $key => $field) : ?>
						<fieldset class="form fieldset-<?php echo esc_attr($key); ?>">
							<label for="<?php echo esc_attr($key); ?>"><?php echo $field['label'] . apply_filters('submit_job_form_required_label', $field['required'] ? '' : ' <small>' . esc_html__('(optional)', 'workscout') . '</small>', $field); ?></label>
							<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
								<?php get_job_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
							</div>
						</fieldset>
					<?php endforeach; ?>

					<?php do_action('submit_job_form_job_fields_end'); ?>

					<!-- Company Information Fields -->
					<?php if ($company_fields) : ?>
				</div>
		</div>
	</div>
	<div class="dashboard-list-box margin-top-0  company-fields-submit-form">
		<div class="dashboard-box  margin-top-0">
			<div class="headline">
				<h3><i class="icon-material-outline-business-center"></i>
					<?php if (class_exists('MAS_WP_Job_Manager_Company') && is_user_logged_in()) { ?>
						<?php esc_html_e('Select Company', 'workscout'); ?>
					<?php } else { ?>
						<?php esc_html_e('Company Details', 'workscout'); ?>
					<?php } ?>
				</h3>
			</div>
			<div class="submit-page">
				<?php do_action('submit_job_form_company_fields_start'); ?>

				<?php foreach ($company_fields as $key => $field) : ?>
					<fieldset class="form fieldset-<?php echo esc_attr($key); ?>">
						<label for="<?php echo esc_attr($key); ?>"><?php echo $field['label'] . apply_filters('submit_job_form_required_label', $field['required'] ? '' : ' <small>' . esc_html__('(optional)', 'workscout') . '</small>', $field); ?></label>
						<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
							<?php get_job_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
						</div>

					</fieldset>
				<?php endforeach; ?>

				<?php if (class_exists('MAS_WP_Job_Manager_Company') && !$has_company) { ?>
					<?php $submit_company = get_option('job_manager_submit_company_form_page_id');   ?>
					<div class="notification add-company-notice notice"><?php esc_html_e("You need to select company before adding job listing. If you didn't add company profile yet click button below.", 'workscout'); ?></div>
					<a href="<?php echo esc_url(get_permalink($submit_company)); ?>" class="button add-company-btn"><i class="fa fa-plus-circle"></i> <?php esc_html_e("Add Company", 'workscout'); ?></a>
				<?php } ?>
				<?php do_action('submit_job_form_company_fields_end'); ?>
			<?php endif; ?>

			<?php do_action('submit_job_form_end'); ?>



		<?php else : ?>

			<?php do_action('submit_job_form_disabled'); ?>

		<?php endif; ?>
			</div>
		</div>

	</div>
	<p class="send-btn-border">
		<input type="hidden" name="job_manager_form" value="<?php echo esc_attr($form); ?>" />
		<input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>" />

		<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
		<input type="submit" name="submit_job" class="button big" value="<?php echo esc_attr($submit_button_text); ?>" />

		<?php
		if (isset($can_continue_later) && $can_continue_later) {
			echo '<input type="submit" name="save_draft" class="button secondary save_draft" value="' . esc_attr__('Save Draft', 'workscout') . '" formnovalidate />';
		}
		?>

		<span class="spinner"></span>
	</p>
</form>