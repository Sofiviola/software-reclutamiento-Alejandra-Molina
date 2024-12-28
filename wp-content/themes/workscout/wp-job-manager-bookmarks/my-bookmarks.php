	<!-- Row -->
	<div class="row">

		<!-- Dashboard Box -->
		<div class="col-xl-12">
			<div class="dashboard-box margin-top-0">

				<!-- Headline -->
				<div class="headline">
					<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Bookmarked Jobs', 'workscout'); ?></h3>
				</div>


				<div class="content">
					<ul class="dashboard-box-list">
						<?php
						foreach ($bookmarks as $bookmark) :
							if (get_post_status($bookmark->post_id) !== 'publish') {
								continue;
							}
							$has_bookmark = true;
							$company_id = get_post_meta($bookmark->post_id, '_company_id', true);
							if (!empty($company_id) && get_post_status($company_id)) {
								$logo_id = (int)$company_id;
							} else {
								$logo_id = (int)$bookmark->post_id;
							}
							$post = get_post($bookmark->post_id);
						?>
							<li>
								<!-- Job Listing -->
								<div class="item-listing">

									<!-- item Listing Details -->
									<div class="item-listing-details">

										<!-- Logo -->
										<a href="#" class="item-listing-company-logo">
											<?php the_company_logo('medium', null, $logo_id); ?>
										</a>

										<!-- Details -->
										<div class="item-listing-description">
											<h3 class="item-listing-title"> <?php echo '<a href="' . get_permalink($bookmark->post_id) . '">' . get_the_title($bookmark->post_id) . '</a>'; ?></h3>

											<!-- item Listing Footer -->
											<div class="item-listing-footer">
												<ul>
													<li><i class="icon-material-outline-business"></i> <?php echo get_the_company_name($bookmark->post_id); ?></li>
													<li><i class="icon-material-outline-location-on"></i> <?php echo  ws_job_location(false, $post); ?></li>
													<li><i class="icon-material-outline-business-center"></i> <?php echo strip_tags( get_the_term_list( $post->ID, 'job_listing_type', '', ', ') )?></li>
													<li><i class="icon-material-outline-access-time"></i> <?php the_job_publish_date($post); ?></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<!-- Buttons -->
								<div class="buttons-to-right">
									<?php
									$actions = apply_filters('job_manager_bookmark_actions', array(
										'delete' => array(
											'label' => esc_html__('Delete', 'workscout'),
											'url'   =>  wp_nonce_url(add_query_arg('remove_bookmark', $bookmark->post_id), 'remove_bookmark')
										)
									), $bookmark);

									foreach ($actions as $action => $value) {
										echo '<a href="' . esc_url($value['url']) . '" title=' . $value['label'] . '  data-tippy-placement="top" class="button red ripple-effect ico delete job-manager-bookmark-action-' . $action . '"><i class="icon-feather-trash-2"></i> </a>';
									}
									?>

								</div>
							</li>
						<?php endforeach; ?>
						<?php if (empty($has_bookmark)) : ?>
							<li>
								<?php esc_html_e('You currently have no bookmarks', 'workscout'); ?>
							</li>
						<?php endif; ?>

					</ul>
				</div>
			</div>
		</div>
	</div>