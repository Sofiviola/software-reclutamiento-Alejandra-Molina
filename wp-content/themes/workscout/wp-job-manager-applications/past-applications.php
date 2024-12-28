	<!-- Row -->
	<div class="row">

		<!-- Dashboard Box -->
		<div class="col-xl-12">
			<div class="dashboard-box margin-top-0">

				<!-- Headline -->
				<div class="headline">
					<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('My Applications', 'workscout'); ?></h3>
				</div>

				<div class="content">
					<ul class="dashboard-box-list">
						<?php foreach ($applications as $application) {
							global $wp_post_statuses;

							$application_id = $application->ID;
							$job_id         = wp_get_post_parent_id($application_id);
							$job            = get_post($job_id);
							$job_title      = get_post_meta($application_id, '_job_applied_for', true); ?>

							<li>
								<!-- Job Listing -->
								<div class="item-listing">

									<!-- item Listing Details -->
									<div class="item-listing-details">



										<!-- Details -->
										<div class="item-listing-description">
											<h3 class="item-listing-title"> <?php if ($job && $job->post_status == 'publish') { ?>
													<a href="<?php echo esc_url(get_permalink($job_id)); ?>"><?php echo esc_html($job_title); ?></a>
												<?php } else {
																				echo esc_html($job_title);
																			} ?>
											</h3>
											
												<?php echo wpautop(wp_kses_post($application->post_content)); ?>
											


											<!-- item Listing Footer -->
											<div class="item-listing-footer">
												<ul>
													<li><i class="icon-material-outline-business"></i> Status: <?php echo esc_html($wp_post_statuses[get_post_status($application_id)]->label); ?></li>
													<li><i class="icon-material-outline-access-time"></i> <?php echo esc_html(get_the_date(get_option('date_format'), $application_id)); ?></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>


						<?php } ?>
					</ul>
					<?php get_job_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
				</div>
			</div>
		</div>
	</div> <?php wp_reset_postdata(); ?>