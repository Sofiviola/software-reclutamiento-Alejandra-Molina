<?php
$submission_limit           = get_option('resume_manager_submission_limit');
$submit_resume_form_page_id = get_option('resume_manager_submit_resume_form_page_id');
$user_id = get_current_user_id();
$freelancer_profile = get_user_meta($user_id, 'freelancer_profile', true);
?>

<div class="notification notice margin-bottom-25">
	<p class=""><?php echo _n('Your resume can be viewed, edited or removed below.', 'Your resume(s) can be viewed, edited or removed below.', resume_manager_count_user_resumes(), 'workscout'); ?></p>
</div>

<!-- Row -->
<div class="row">

	<!-- Dashboard Box -->
	<div class="col-xl-12">
		<div class="dashboard-box margin-top-0">

			<!-- Headline -->
			<div class="headline">
				<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Mi Currículum.', 'workscout'); ?></h3>
			</div>
			<div class="content">
				<ul class="dashboard-box-list">
					<?php if (!$resumes) : ?>
						<li><?php esc_html_e('You do not have any active resume listings.', 'workscout'); ?></li>
					<?php else : ?>
						<?php foreach ($resumes as $resume) : ?>
							<li>
								<!-- Job Listing -->
								<div class="item-listing">

									<!-- Job Listing Details -->
									<div class="item-listing-details">


										<a href="#" class="item-listing-company-logo">
											<?php the_candidate_photo('workscout-resume', get_template_directory_uri() . '/images/candidate.png'); ?>
										</a>
										<!-- Details -->
										<div class="item-listing-description">
											<h3 class="item-listing-title">
												<a href="<?php echo get_permalink($resume->ID); ?>"><?php echo esc_html($resume->post_title); ?></a>
												<?php if ($resume->ID == $freelancer_profile) { ?><span class="dashboard-status-button black"><?php esc_html_e('Your Freelance Profile', 'workscout'); ?></span><?php } ?>
												<span class="dashboard-status-button <?php switch (get_the_job_status_class($resume)) {
																							case 'expired':
																								echo 'red';
																								break;
																							case 'publish':
																								echo 'green';
																								break;

																							default:
																								echo 'yellow';
																								break;
																						}; ?>"><?php the_resume_status($resume); ?></span>
											</h3>

											<!-- Job Listing Footer -->
											<div class="item-listing-footer">
												<ul>
													<li><i class="icon-material-outline-account-circle"></i> <?php the_candidate_title('', '', true, $resume); ?></li>
													<li><i class="icon-material-outline-location-on"></i> <?php ws_candidate_location(false, $resume); ?></li>
													<!-- <li><i class="icon-material-outline-folder"></i> <?php the_resume_category($resume);  ?></li> -->
													<li><i class="icon-material-outline-date-range"></i> <?php if (!empty($resume->_resume_expires) && strtotime($resume->_resume_expires) > current_time('timestamp')) {
																												printf(esc_html__('Expires %s', 'workscout'), date_i18n(get_option('date_format'), strtotime($resume->_resume_expires)));
																											} else {
																												echo date_i18n(get_option('date_format'), strtotime($resume->post_date));
																											} ?></li>
												</ul>
											</div>
										</div>

									</div>
								</div>
								<!-- Buttons -->
								<div class="buttons-to-right always-visible">


									<?php
									$actions = array();

									switch ($resume->post_status) {
										case 'publish':
											if ($resume->ID != $freelancer_profile) {
												// $actions['set_as_profile'] = array('label' => esc_html__('Make this resume your freelancer profile', 'workscout'), 'nonce' => true);
											} else {
												$actions['unset_as_profile'] = array('label' => esc_html__('Unset this resume as your freelancer profile', 'workscout'), 'nonce' => true);
											}
											if (resume_manager_user_can_edit_published_submissions()) {
												$actions['edit'] = array('label' => esc_html__('Edit', 'workscout'), 'nonce' => false);
											}
											// $actions['hide'] = array('label' => esc_html__('Hide', 'workscout'), 'nonce' => true);
											break;
										case 'hidden':
											if (resume_manager_user_can_edit_published_submissions()) {
												$actions['edit'] = array('label' => esc_html__('Edit', 'workscout'), 'nonce' => false);
											}
											$actions['publish'] = array('label' => esc_html__('Publish', 'workscout'), 'nonce' => true);
											break;
										case 'pending_payment':
										case 'pending':
											if (resume_manager_user_can_edit_pending_submissions()) {
												$actions['edit'] = array(
													'label' => __('Edit', 'workscout'),
													'nonce' => false,
												);
											}
											break;
										case 'expired':
											if (get_option('resume_manager_submit_resume_form_page_id')) {
												$actions['relist'] = array('label' => esc_html__('Relist', 'workscout'), 'nonce' => true);
											}
											break;
									}

									$actions['delete'] = array('label' => esc_html__('Delete', 'workscout'), 'nonce' => true);

									$actions = apply_filters('resume_manager_my_resume_actions', $actions, $resume);

									foreach ($actions as $action => $value) {
										$action_url = add_query_arg(array('action' => $action, 'resume_id' => $resume->ID));
										if ($value['nonce'])
											$action_url = wp_nonce_url($action_url, 'resume_manager_my_resume_actions');
										echo '<a href="' . esc_url($action_url) . '"  data-tippy-placement="top" class="button gray ripple-effect ico candidate-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($value['label']) . '">' . workscout_manage_action_icons($action) . '</a>';
									}
									?>

								</div>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<?php get_job_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
			</div>
		</div>
	</div>

</div>
<?php /* if ($submit_resume_form_page_id && (resume_manager_count_user_resumes() < $submission_limit || !$submission_limit)) : ?>

	<a class="button margin-top-30" href="<?php echo esc_url(get_permalink($submit_resume_form_page_id)); ?>"><?php esc_html_e('Add Resume', 'workscout'); ?></a>

<?php endif; */ ?>


<?php 
$current_user = wp_get_current_user(); // Obtén al usuario actual
$roles = $current_user->roles; // Obtén los roles del usuario actual

// Comprueba si el usuario tiene el rol 'candidate'
if (in_array('candidate', $roles)) :
    // Verifica si el usuario ya tiene un CV cargado
    if ($submit_resume_form_page_id && resume_manager_count_user_resumes() == 0 && (resume_manager_count_user_resumes() < $submission_limit || !$submission_limit)) : 
?>
        <a class="button margin-top-30" href="<?php echo esc_url(get_permalink($submit_resume_form_page_id)); ?>">
            <?php esc_html_e('Add Resume', 'workscout'); ?>
        </a>
<?php 
    endif; 
endif; 
?>

