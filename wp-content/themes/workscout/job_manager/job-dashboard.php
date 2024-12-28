	<!-- Row -->
	<div class="row">

		<!-- Dashboard Box -->
		<div class="col-xl-12">
			<div class="dashboard-box margin-top-0">

				<!-- Headline -->
				<div class="headline">
					<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('My Job Listings', 'workscout'); ?></h3>
				</div>

				<div class="content">
					<ul class="dashboard-box-list">

						<?php if (!$jobs) : ?>
							<li>
								<?php esc_html_e('You do not have any active listings.', 'workscout'); ?>
							</li>
						<?php else : ?>
							<?php foreach ($jobs as $job) :

							?>

								<li>
									<!-- Job Listing -->
									<div class="item-listing">

										<!-- Job Listing Details -->
										<div class="item-listing-details">

											<!-- Details -->
											<div class="item-listing-description jm-dashboard-job">
												<h3 class="item-listing-title">
													<a class="job-title" data-job-id="<?php echo esc_attr((string) $job->ID) ?>" href="<?php echo get_permalink($job->ID); ?>"><?php echo esc_html($job->post_title); ?></a>
													<span class="dashboard-status-button <?php switch (get_the_job_status_class($job)) {
																								case 'expired':
																									echo 'red';
																									break;
																								case 'publish':
																									echo 'green';
																									break;

																								default:
																									echo 'yellow';
																									break;
																							}; ?>"><?php the_job_status($job); ?></span>
												</h3>

												<!-- Job Listing Footer -->
												<div class="item-listing-footer">
													<ul>
														<li><i class="icon-material-outline-date-range"></i> <?php echo date_i18n(get_option('date_format'), strtotime($job->post_date)); ?></li>
														<li><i class="icon-material-outline-date-range"></i> <?php esc_html_e('Expiring', 'workscout'); ?> <?php echo $job->_job_expires ? date_i18n(get_option('date_format'), strtotime($job->_job_expires)) : '&ndash;'; ?></li>
														<li><i class="icon-material-outline-check-circle"></i> <?php esc_html_e('Filled:', 'workscout'); ?> <?php echo is_position_filled($job) ? '&#10004;' : '&ndash;'; ?></li>
														<?php
														if (array_key_exists('stats', $job_dashboard_columns)) {
															do_action('workscout_job_manager_job_dashboard_column_stats', $job);
														}
														?>
													</ul>
												</div>
											</div>

										</div>
									</div>
									<!-- Buttons -->
									<div class="buttons-to-right always-visible">
										<?php
										if (array_key_exists('applications', $job_dashboard_columns)) {
											global $post;
											echo ($count = get_job_application_count($job->ID)) ? '<a class="button  ripple-effect" href="' . add_query_arg(array('action' => 'show_applications', 'job_id' => $job->ID), get_permalink($post->ID)) . '"><i class="icon-material-outline-supervisor-account"></i>' . __('Manage Candidates', 'workscout') . '  <span class="button-info">' . $count . '</span></a>' : '';
										?>

										<?php }


										?>

										<?php
										$actions = array();

										switch ($job->post_status) {
											case 'publish':
												if (wpjm_user_can_edit_published_submissions()) {
													$actions['edit'] = array('label' => __('Edit', 'workscout'), 'nonce' => false);
												}

												if (is_position_filled($job)) {
													$actions['mark_not_filled'] = array('label' => esc_html__('Mark not filled', 'workscout'), 'nonce' => true);
												} else {
													$actions['mark_filled'] = array('label' => esc_html__('Mark filled', 'workscout'), 'nonce' => true);
												}

												$actions['duplicate'] = array('label' => __('Duplicate', 'wp-job-manager'), 'nonce' => true);
												break;
											case 'expired':
												if (job_manager_get_permalink('submit_job_form')) {
													$actions['relist'] = array('label' => esc_html__('Relist', 'workscout'), 'nonce' => true);
												}
												break;
											case 'pending_payment':
											case 'pending':
												if (job_manager_user_can_edit_pending_submissions()) {
													$actions['edit'] = array('label' => esc_html__('Edit', 'workscout'), 'nonce' => false);
												}
												break;
											case 'draft':
											case 'preview':
												$actions['continue'] = array('label' => __('Continue Submission', 'workscout'), 'nonce' => true);
												break;
												break;
										}

										$actions['delete'] = array('label' => esc_html__('Delete', 'workscout'), 'nonce' => true);
										$actions           = apply_filters('job_manager_my_job_actions', $actions, $job);

										foreach ($actions as $action => $value) {
											$action_url = add_query_arg(array('action' => $action, 'job_id' => $job->ID));
											if ($value['nonce']) {
												$action_url = wp_nonce_url($action_url, 'job_manager_my_job_actions');
											}
											echo '<a href="' . esc_url($action_url) . '"  data-tippy-placement="top" class="button gray ripple-effect ico job-dashboard-action-' . esc_attr($action) . '" title="' . esc_html($value['label']) . '">' . workscout_manage_action_icons($action) . '</a>';
										} ?>

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
	<?php
	$submit_job_page = get_option('job_manager_submit_job_form_page_id');
	if (!empty($submit_job_page)) {  ?>
		<a href="<?php echo get_permalink($submit_job_page) ?>" class="button margin-top-30"><?php esc_html_e('Add Job', 'workscout'); ?></a>
	<?php } ?>