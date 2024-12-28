<?php global $post;

$company_id = get_post_meta($post->ID, '_company_id', true);
if (!empty($company_id) && get_post_status($company_id)) {
	$logo_id = (int)$company_id;
} else {
	$logo_id = (int)$post->ID;
}

$position = Kirki::get_option('workscout', 'pp_job_list_logo_position', 'left');
$layout = Kirki::get_option('workscout', 'pp_jobs_old_layout', false);

if (isset($_POST['list_layout'])) {
	$list_layout = $_POST['list_layout'];
}

if (isset($_POST['data_params']['list_layout'])) {
	$list_layout = $_POST['data_params']['list_layout'];
};
$types_below = Kirki::get_option('workscout', 'pp_job_tags_below_title', false);
//$list_layout = Kirki::get_option( 'workscout','jobs_listings_list_layout', 'list' );
$_color_job_type = Kirki::get_option('workscout', 'pp_maps_marker_color_job_type', false);
if (get_option('job_manager_enable_types')) {
	$types = get_the_terms($post->ID, 'job_listing_type');
	if ($types && !is_wp_error($types) && $_color_job_type) :
		$single_type = $types[0];
		$single_type_slug = $single_type->slug;
		$markercolor = get_term_meta($single_type->term_id, 'color', true);
	endif;
}

if (!isset($list_layout)) {
	$list_layout = Kirki::get_option('workscout', 'jobs_listings_list_layout', 'list');
}
if (!$layout) {
	if ($list_layout == 'grid' || $list_layout == 'grid-three-in-row') { ?>

		<!-- Job Listing -->

		<li <?php job_listing_class("job-listing grid"); ?> data-longitude="<?php echo esc_html($post->geolocation_long); ?>" data-latitude="<?php echo esc_html($post->geolocation_lat); ?>" <?php if (isset($markercolor)) {
																																																	echo 'data-color="#' . $markercolor . '"';
																																																} ?> data-title="<?php echo wp_strip_all_tags(get_the_title()); ?>" data-image="<?php echo (get_the_company_logo($logo_id, 'thumbnail')) ?  get_the_company_logo($logo_id, 'thumbnail') : apply_filters('job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png'); ?>" data-company="<?php the_company_name(); ?>" data-address="<?php echo esc_attr(get_the_job_location($post)); ?>" data-job_type="<?php echo esc_attr(ws_get_job_types($post)); ?>" data-rate="<?php echo esc_attr(ws_get_job_rate($post)); ?>" data-job_id="<?php echo esc_attr($post->ID); ?>" data-salary="<?php echo esc_attr(ws_get_job_salary($post)); ?>" data-job_type_class="<?php if (isset($single_type_slug)) echo esc_attr($single_type_slug); ?>" <?php if ($types_below) : ?>class="types_below" <?php endif; ?>>
			<?php if (is_position_featured($post)) { ?><div class="listing-badge"><i class="fa fa-star"></i></div><?php } ?>
			<!-- Job Listing Details -->
			<div class="job-listing-details">
				<!-- Logo -->
				<div class="job-listing-company-logo">
					<?php the_company_logo('medium', null, $logo_id); ?>
				</div>

				<!-- Details -->
				<div class="job-listing-description">
					<?php $job_meta = Kirki::get_option('workscout', 'pp_meta_job_list', array('company', 'location', 'rate', 'salary')); ?>
					<?php if (in_array("company", $job_meta) && get_the_company_name()) { ?>
						<h4 class="job-listing-company"> <?php the_company_name(); ?></h4>
					<?php } ?>
					<h3 class="job-listing-title"><?php the_title(); ?> <div class="listing-types-list"><?php echo ws_get_job_types($post); ?></div>
					</h3>

				</div>
			</div>

			<!-- Job Listing Footer -->
			<div class="job-listing-footer">

				<ul>
					<?php do_action('workscout_job_listing_meta_start'); ?>

					<?php $job_meta = Kirki::get_option('workscout', 'pp_meta_job_list', array('company', 'location', 'rate', 'salary')); ?>
					<?php if (in_array("company", $job_meta) && get_the_company_name()) { ?>
						<li class="job-listing-footer-company"><?php echo workscout_get_icon('company'); ?> <?php the_company_name(); ?></li>
					<?php } ?>

					<?php if (in_array("location", $job_meta)) { ?>
						<li class="job-listing-footer-location"><?php echo workscout_get_icon('location'); ?></i> <?php ws_job_location(false); ?></li>
					<?php } ?>

					<?php echo ws_get_job_rate($post); ?>

					<?php echo ws_get_job_salary($post); ?>
					<?php if (in_array("wpjm_salary", $job_meta)) :
						if (get_the_job_salary()) { ?>
							<li>
								<?php echo workscout_get_icon('money'); ?>
								<?php echo the_job_salary(); ?>
							</li>
					<?php }
					endif;
					?>
					<?php
					if (in_array("date", $job_meta)) {
						if (workscout_newly_posted()) {
							echo '<li  class="job-listing-footer-date"><div class="listing-date new"> ' . esc_html__('new', 'workscout') . '</div></li>';
						} else { ?>
							<li class="job-listing-footer-date">
								<div class="listing-date"> <?php the_job_publish_date() ?></div>
							</li>
					<?php	}
					} ?>
					<?php if (in_array("deadline", $job_meta)) { ?>
						<?php
						if ($deadline = get_post_meta($post->ID, '_application_deadline', true)) {
							$expiring_days = apply_filters('job_manager_application_deadline_expiring_days', 2);
							$expiring = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= $expiring_days);
							$expired  = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= 0); ?>
							<li>
								<div class="listing-date">
									<?php echo ($expired ? __('Closed', 'workscout') : __('Closes', 'workscout')) . ': ' . date_i18n(get_option('date_format'), strtotime($deadline)) ?>
								</div>
							</li>
					<?php }
					} ?>

					<?php
					$expires_meta =  get_post_meta($post->ID, '_job_expires', true);
					if (in_array("expires", $job_meta) && !empty($expires_meta)) { ?>
						<li class="job-listing-footer-expires">
							<div class="listing-date">
								<?php esc_html_e('Expires', 'workscout') ?>: <?php echo date_i18n(get_option('date_format'), strtotime(get_post_meta($post->ID, '_job_expires', true))) ?>
							</div>
						</li>
					<?php } ?>


				</ul>

			</div>
			<a href="<?php the_job_permalink(); ?>" class="workscout-grid-job-link-handler"></a>
		</li>
	<?php } else {


	?>
		<!-- Listing -->
		<li <?php
			$company_id = get_post_meta($post->ID, '_company_id', true);
			if (!empty($company_id) && get_post_status($company_id)) {
				$logo_id = $company_id;
			} else {
				$logo_id = $post;
			} ?> data-longitude="<?php echo esc_html($post->geolocation_long); ?>" data-latitude="<?php echo esc_html($post->geolocation_lat); ?>" <?php if (isset($markercolor)) {
																																						echo 'data-color="#' . $markercolor . '"';
																																					} ?> data-title="<?php echo wp_strip_all_tags(get_the_title()); ?>" data-image="<?php echo (get_the_company_logo($logo_id, 'thumbnail')) ?  get_the_company_logo($logo_id, 'thumbnail') : apply_filters('job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png'); ?>" data-company="<?php the_company_name(); ?>" data-address="<?php echo esc_attr(get_the_job_location($post)); ?>" data-job_type="<?php echo esc_attr(ws_get_job_types($post)); ?>" data-job_id="<?php echo esc_attr($post->ID); ?>" data-rate="<?php echo esc_attr(ws_get_job_rate($post)); ?>" data-salary="<?php echo esc_attr(ws_get_job_salary($post)); ?>" data-job_type_class="<?php if (isset($single_type_slug)) echo esc_attr($single_type_slug); ?>" <?php if ($types_below) : ?>class="types_below" <?php endif; ?>>
			<a href="<?php the_job_permalink(); ?>" <?php job_listing_class($position); ?> data-longitude="<?php echo esc_attr($post->geolocation_long); ?>" data-latitude="<?php echo esc_attr($post->geolocation_lat); ?>">
				<div class="listing-logo">
					<?php ($position == 'left') ? the_company_logo('thumbnail', null, $logo_id) : the_company_logo('medium', null, $logo_id); ?>
				</div>
				<div class="listing-title">
					<h4><?php the_title(); ?>
						<div class="listing-types-list"><?php echo ws_get_job_types($post); ?></div>

					</h4>

					<ul class="listing-icons">
						<?php do_action('workscout_job_listing_meta_start'); ?>

						<?php $job_meta = Kirki::get_option('workscout', 'pp_meta_job_list', array('company', 'location', 'rate', 'salary')); ?>
						<?php if (in_array("company", $job_meta) && get_the_company_name()) { ?>
							<li><?php echo workscout_get_icon('company'); ?> <?php the_company_name(); ?></li>
						<?php } ?>

						<?php if (in_array("location", $job_meta)) { ?>
							<li><?php echo workscout_get_icon('location'); ?> <?php ws_job_location(false); ?></li>
						<?php } ?>

						<?php echo ws_get_job_rate($post); ?>

						<?php echo ws_get_job_salary($post); ?>
						<?php if (in_array("wpjm_salary", $job_meta)) :
							if (get_the_job_salary()) { ?>
								<li>
									<?php echo workscout_get_icon('money'); ?>
									<?php echo the_job_salary(); ?>
								</li>
						<?php }
						endif; //overview elements 
						?>
						<?php
						if (in_array("date", $job_meta)) {
							if (workscout_newly_posted()) {
								echo '<li><div class="listing-date new"> ' . esc_html__('new', 'workscout') . '</div></li>';
							} else { ?>
								<li>
									<div class="listing-date"> <?php the_job_publish_date() ?></div>
								</li>
						<?php	}
						} ?>
						<?php if (in_array("deadline", $job_meta)) { ?>
							<?php
							if ($deadline = get_post_meta($post->ID, '_application_deadline', true)) {
								$expiring_days = apply_filters('job_manager_application_deadline_expiring_days', 2);
								$expiring = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= $expiring_days);
								$expired  = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= 0); ?>
								<li>
									<div class="listing-date">
										<?php echo ($expired ? __('Closed', 'workscout') : __('Closes', 'workscout')) . ': ' . date_i18n(get_option('date_format'), strtotime($deadline)) ?>
									</div>
								</li>
						<?php }
						} ?>


						<?php
						$expires_meta =  get_post_meta($post->ID, '_job_expires', true);
						if (in_array("expires", $job_meta) && !empty($expires_meta)) { ?>

							<li>
								<div class="listing-date">
									<?php esc_html_e('Expires', 'workscout') ?>: <?php echo date_i18n(get_option('date_format'), strtotime(get_post_meta($post->ID, '_job_expires', true))) ?>
								</div>
							</li>
						<?php } ?>
						<?php $show_excerpt = Kirki::get_option('workscout', 'pp_jobs_hide_content_on_list', false);
						if (!$show_excerpt) : ?>
							<div class="listing-desc"><?php the_excerpt(); ?></div>
						<?php endif; ?>


					</ul>

				</div>
			</a>
		</li>
	<?php
	}
} else { ?>


	<li <?php job_listing_class($position); ?> data-longitude="<?php echo esc_attr($post->geolocation_long); ?>" data-latitude="<?php echo esc_attr($post->geolocation_lat); ?>" <?php if (isset($markercolor)) {
																																														echo 'data-color="#' . $markercolor . '"';
																																													} ?>>
		<a href="<?php the_job_permalink(); ?>">
			<?php

			($position == 'left') ? the_company_logo('thumbnail', null, $logo_id) : the_company_logo('medium', null, $logo_id);  ?>
			<div class="job-list-content">
				<h4><?php the_title(); ?>
					<?php if (get_option('job_manager_enable_types')) {
						$types = get_the_terms($post->ID, 'job_listing_type');
						if ($types && !is_wp_error($types)) :
							foreach ($types as $type) { ?>
								<span class="job-type <?php echo sanitize_title($type->slug); ?>"><?php echo $type->name; ?></span>
						<?php }
						endif; ?>
					<?php } ?>
					<?php if (workscout_newly_posted()) {
						echo '<span class="new_job">' . esc_html__('NEW', 'workscout') . '</span>';
					} ?>
				</h4>

				<div class="job-icons">
					<?php do_action('workscout_job_listing_meta_start'); ?>

					<?php $job_meta = Kirki::get_option('workscout', 'pp_meta_job_list', array('company', 'location', 'rate', 'salary')); ?>

					<?php if (in_array("company", $job_meta) && get_the_company_name()) { ?>
						<span class="ws-meta-company-name"><i class="fa fa-briefcase"></i> <?php the_company_name(); ?></span>
					<?php } ?>

					<?php if (in_array("location", $job_meta)) { ?>
						<span class="ws-meta-job-location"><i class="fa fa-map-marker"></i> <?php ws_job_location(false); ?></span>
					<?php } ?>

					<?php
					$currency_position =  get_option('workscout_currency_position', 'before');

					$rate_min = get_post_meta($post->ID, '_rate_min', true);
					if ($rate_min && in_array("rate", $job_meta)) {
						$rate_max = get_post_meta($post->ID, '_rate_max', true);  ?>
						<span class="ws-meta-rate">
							<?php echo workscout_get_icon('money'); ?> <?php
																		if ($currency_position == 'before') {
																			echo get_workscout_currency_symbol();
																		}
																		echo esc_html($rate_min);
																		if ($currency_position == 'after') {
																			echo get_workscout_currency_symbol();
																		}
																		if (!empty($rate_max)) {
																			echo ' - ';
																			if ($currency_position == 'before') {
																				echo get_workscout_currency_symbol();
																			}
																			echo $rate_max;
																			if ($currency_position == 'after') {
																				echo get_workscout_currency_symbol();
																			}
																		} ?> <?php esc_html_e('/ hour', 'workscout'); ?>
						</span>
					<?php } ?>

					<?php
					$salary_min = get_post_meta($post->ID, '_salary_min', true);
					$salary_max = get_post_meta($post->ID, '_salary_max', true);
					if (in_array("salary", $job_meta)) :
						if (!empty($salary_min) || !empty($salary_max)) { ?>
							<span class="ws-meta-salary">
								<?php echo workscout_get_icon('money'); ?>
								<?php
								if ($salary_min) {
									if ($currency_position == 'before') {
										echo get_workscout_currency_symbol();
									}
									echo esc_html($salary_min);
									if ($currency_position == 'after') {
										echo get_workscout_currency_symbol();
									}
								}
								if ($salary_max) {
									if ($salary_min) {
										echo ' - ';
									}
									if ($currency_position == 'before') {
										echo get_workscout_currency_symbol();
									}
									echo $salary_max;
									if ($currency_position == 'after') {
										echo get_workscout_currency_symbol();
									}
								} ?>
							</span>
					<?php }
					endif; ?>

					<?php if (in_array("date", $job_meta)) { ?>
						<span class="ws-meta-job-date"><i class="fa fa-calendar"></i> <?php the_job_publish_date() ?></span>
					<?php } ?>

					<?php if (in_array("deadline", $job_meta)) { ?>
						<?php
						if ($deadline = get_post_meta($post->ID, '_application_deadline', true)) {
							$expiring_days = apply_filters('job_manager_application_deadline_expiring_days', 2);
							$expiring = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= $expiring_days);
							$expired  = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= 0); ?>
							<span class="ws-meta-job-deadline"><i class="fa fa-calendar-times-o"></i>
								<?php echo ($expired ? __('Closed', 'workscout') : __('Closes', 'workscout')) . ': ' . date_i18n(get_option('date_format'), strtotime($deadline)) ?>
							</span>
					<?php }
					} ?>

					<?php
					$expires_meta =  get_post_meta($post->ID, '_job_expires', true);
					if (in_array("expires", $job_meta) && !empty($expires_meta)) { ?>
						<span class="ws-meta-job-expires"><i class="fa fa-calendar-check-o"></i>
							<?php esc_html_e('Expires', 'workscout') ?>: <?php echo date_i18n(get_option('date_format'), strtotime(get_post_meta($post->ID, '_job_expires', true))) ?>
						</span>
					<?php } ?>

					<?php do_action('workscout_job_listing_meta_end'); ?>
				</div>
				<div class="listing-desc"><?php the_excerpt(); ?>

				</div>


			</div>
		</a>
		<div class="clearfix"></div>
	</li>
<?php } ?>