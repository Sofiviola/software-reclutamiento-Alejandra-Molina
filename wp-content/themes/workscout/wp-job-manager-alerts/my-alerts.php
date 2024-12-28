<div class="notification notice margin-bottom-25">
	<p class=""><?php printf(esc_html__('Your job alerts are shown in the table below. Your alerts will be sent to %s.', 'workscout'), $user->user_email); ?></p>
</div>

<!-- Row -->
<div class="row">

	<!-- Dashboard Box -->
	<div class="col-xl-12">
		<div class="dashboard-box margin-top-0">

			<!-- Headline -->
			<div class="headline">
				<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Alerts', 'workscout'); ?></h3>
			</div>

			<div class="content">
				<ul class="dashboard-box-list">
					<?php if (!$alerts) : ?>
						<li><?php esc_html_e('You do not have any job alerts.', 'workscout'); ?></li>
					<?php endif;  ?>
					<?php foreach ($alerts as $alert) : ?>
						<?php
						$search_terms = WP_Job_Manager_Alerts_Post_Types::get_alert_search_terms($alert->ID);
						?>
						<li>
							<!-- Job Listing -->
							<div class="item-listing">

								<!-- item Listing Details -->
								<div class="item-listing-details">
									<!-- Details -->
									<div class="item-listing-description">
										<h3 class="item-listing-title"> <?php echo esc_html($alert->post_title); ?>
											<span class="dashboard-status-button <?php echo $alert->post_status == 'draft' ? 'yellow' : 'green'; ?> "><?php echo $alert->post_status == 'draft' ? esc_html__('Disabled', 'workscout') : esc_html__('Enabled', 'workscout'); ?></span>
										</h3>
										<!-- item Listing Footer -->
										<div class=" item-listing-footer">
											<ul>
												<?php if ($value = get_post_meta($alert->ID, 'alert_keyword', true)) { ?><li><i class="icon-material-outline-business"></i> <?php esc_html_e('Keyword:', 'workscout'); ?> <?php echo esc_html('&ldquo;' . $value . '&rdquo;'); ?> </li> <?php } ?>

												<?php if (get_option('job_manager_enable_categories') && wp_count_terms('job_listing_category') > 0) : ?>
													<?php
													$term_ids = !empty($search_terms['categories']) ? $search_terms['categories'] : array();
													$terms = array();
													if (!empty($term_ids)) {
														$terms = get_terms(array(
															'taxonomy'         => 'job_listing_category',
															'fields'           => 'names',
															'include'          => $term_ids,
															'hide_empty'       => false,
														));

														if (!empty($terms)) { ?>
															<li><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Categories:', 'workscout'); ?>
																<?php
																echo esc_html(implode(', ', $terms));
																?></li>
												<?php }
													}
												endif; ?>
												<?php if (taxonomy_exists('job_listing_tag')) : ?>
													<?php
													$term_ids = !empty($search_terms['tags']) ? $search_terms['tags'] : array();
													$terms = array();
													if (!empty($term_ids)) {
														$terms = get_terms(array(
															'taxonomy'         => 'job_listing_tag',
															'fields'           => 'names',
															'include'          => $term_ids,
															'hide_empty'       => false,
														));
														if (!empty($terms)) { ?>
															<li><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Tags:', 'workscout'); ?> <?php echo $terms ? esc_html(implode(', ', $terms)) : '&ndash;';
																															?></li>
												<?php
														}
													}
												endif; ?>
												<li><i class="icon-material-outline-location-on"></i> <?php esc_html_e('Location:', 'workscout'); ?> <?php
																												if (taxonomy_exists('job_listing_region') && wp_count_terms('job_listing_region') > 0) {
																													$term_ids = !empty($search_terms['regions']) ? $search_terms['regions'] : array();
																													$terms = array();
																													if (!empty($term_ids)) {
																														$terms = get_terms(array(
																															'taxonomy'         => 'job_listing_region',
																															'fields'           => 'names',
																															'include'          => $term_ids,
																															'hide_empty'       => false,
																														));
																													}
																													echo $terms ? esc_html(implode(', ', $terms)) : '&ndash;';
																												} else {
																													$value = get_post_meta($alert->ID, 'alert_location', true);
																													echo $value ? esc_html($value) : '&ndash;';
																												}
																												?></li>
												<li><i class="icon-material-outline-access-time"></i> <?php
																										$schedules = WP_Job_Manager_Alerts_Notifier::get_alert_schedules();
																										$freq      = get_post_meta($alert->ID, 'alert_frequency', true);

																										if (!empty($schedules[$freq])) {
																											echo esc_html($schedules[$freq]['display']);
																											echo ": ";
																										}

																										echo sprintf(__('Next: %s at %s', 'workscout'), date_i18n(get_option('date_format'), wp_next_scheduled('job-manager-alert', array($alert->ID))),  date_i18n(get_option('time_format'), wp_next_scheduled('job-manager-alert', array($alert->ID))));
																										?></li>


											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- Buttons -->
							<div class="buttons-to-right">
								<?php
								$actions = apply_filters('job_manager_alert_actions', array(
									'view' => array(
										'label' => esc_html__('Show Results', 'workscout'),
										'nonce' =>
										false,
										'color' => 'gray'
									),
									'email' => array(
										'label' => esc_html__('Email', 'workscout'),
										'nonce' =>
										true,
										'color' => 'gray'
									),
									'edit' => array(
										'label' => esc_html__('Edit', 'workscout'),
										'nonce' =>
										false,
										'color' => 'gray'
									),
									'toggle_status' => array(
										'label' => $alert->post_status == 'draft' ? esc_html__('Enable', 'workscout') : esc_html__('Disable', 'workscout'),
										'nonce' => true,
										'color' => 'gray'
									),
									'delete' => array(
										'label' => esc_html__('Delete', 'workscout'),
										'nonce' => true,
										'color' => 'red'
									)
								), $alert);

								foreach ($actions as $action => $value) {
									$action_url = remove_query_arg('updated', add_query_arg(array('action' => $action, 'alert_id' => $alert->ID)));

									if ($value['nonce'])
										$action_url = wp_nonce_url($action_url, 'job_manager_alert_actions');

									echo '<a href="' . $action_url . '" title=' . $value['label'] . '  data-tippy-placement="top" class="button ' . $value['color'] . '  ripple-effect ico delete job-manager-bookmark-action-' . $action . '">' . workscout_manage_action_icons($action) . ' </a>';
								}

								?>

							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<a class="button margin-top-30" href="<?php echo remove_query_arg('updated', add_query_arg('action', 'add_alert')); ?>"><?php esc_html_e('Add alert', 'workscout'); ?></a>