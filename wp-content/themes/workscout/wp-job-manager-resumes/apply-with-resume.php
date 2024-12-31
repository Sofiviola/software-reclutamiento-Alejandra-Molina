<div id="tab1" class="tab-slider--body">
	<?php global $post;

	if (is_user_logged_in() && sizeof($resumes)) : ?>
		<form class="apply_with_resume" method="post">
			<p><?php _e('Apply using your online resume; just enter a short message and choose one of your resumes to email your application.', 'workscout'); ?></p>
			<p>
				<label for="resume_id"><?php _e('Online resume', 'workscout'); ?>*:</label>
				<select name="resume_id" class="select2-single" id="resume_id" required>
					<option value=""><?php _e('Choose a resume...', 'workscout'); ?></option>
					<?php
					foreach ($resumes as $resume) {
						echo '<option value="' . absint($resume->ID) . '">' . $resume->post_title . '</option>';
					}
					?>
				</select>
			</p>
			<p>
				<label><?php _e('Message', 'workscout'); ?>*:</label>
				<textarea name="application_message" cols="20" rows="4" placeholder="Escribe aquí un mensaje para está propuesta" ><?php
																					if (isset($_POST['application_message'])) {
																						echo esc_textarea(stripslashes($_POST['application_message']));
																					} else {
																						echo '';
																					}
																					?></textarea>
			</p>
			<p>
				<input type="submit" name="wp_job_manager_resumes_apply_with_resume" value="<?php esc_attr_e('Send application', 'workscout'); ?>" />
				<input type="hidden" name="job_id" value="<?php echo absint($post->ID); ?>" />
			</p>
		</form>
	<?php else : ?>
		<form class="apply_with_resume" method="post" action="<?php echo get_permalink(get_option('resume_manager_submit_resume_form_page_id')); ?>">
			<p><?php _e('You can apply to this job and others using your online resume. Click the link below to submit your online resume and email your application to this employer.', 'workscout'); ?></p>

			<p>
				<input type="submit" name="wp_job_manager_resumes_apply_with_resume_create" value="<?php esc_attr_e('Submit resume and apply', 'workscout'); ?>" />
				<input type="hidden" name="job_id" value="<?php echo absint($post->ID); ?>" />
			</p>
		</form>
	<?php endif; ?>
</div>