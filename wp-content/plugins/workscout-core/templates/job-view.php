<?php
if (isset($data)) :
	$ID	 	= (isset($data->job_id)) ? $data->job_id : '';
endif;

if ($ID) {
	$post = get_post($ID);
}

$company_placeholder = get_template_directory_uri() . '/images/company_placeholder_white.jpeg';

?>
<?php $company_id = get_post_meta($ID, '_company_id', true);
if (!empty($company_id)) {
	$company_url = get_permalink($company_id);
}  ?>
<div class="single-job-view-container">
	<!-- Preloader -->
	<div class="listings-loader">
		<div class="spinner">
			<div class="double-bounce1"></div>
			<div class="double-bounce2"></div>
		</div>
	</div>
	<div id="titlebar" class="single single-job-view-titlebar">
		<?php $logo_position = Kirki::get_option('workscout', 'pp_job_list_logo_position', 'left'); ?>

		<div class="ajax-job-view-logo">
			<?php if (class_exists('Astoundify_Job_Manager_Companies')) {
				echo workscout_get_company_link(get_post_meta($ID, '_company_name', true));
			} ?>
			<?php
			if ($company_id) {
				($logo_position == 'left') ? the_company_logo('thumbnail', null, $company_id) : the_company_logo('medium', null, $company_id);
			} else {
				($logo_position == 'left') ? the_company_logo() : the_company_logo('medium');
			} ?>
			<?php if (class_exists('Astoundify_Job_Manager_Companies')) {
				echo "</a>";
			} ?>
		</div>

		<div class="ajax-job-view-details">
			<div class="ajax-job-view-types">
				<?php if (get_option('job_manager_enable_types')) {
					$types = get_the_terms($ID, 'job_listing_type');
					if ($types && !is_wp_error($types)) :
						foreach ($types as $type) { ?>
							<span class="job-type <?php echo sanitize_title($type->slug); ?>"><?php echo $type->name; ?></span>
					<?php }
					endif; ?>
				<?php } ?>
			</div>

			<h1 class="ajax-job-view-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?> </a></h1>
			<h3 class="ajax-job-view-company">
				<?php if (class_exists('Astoundify_Job_Manager_Companies')) {
					echo workscout_get_company_link(get_post_meta($ID, '_company_name', true));
				} ?>
				<?php if ($company_id) { ?> <a href="<?php echo esc_url($company_url) ?>"><?php } ?>
					<?php the_company_name('<strong>', '</strong>'); ?>
					<?php if ($company_id) { ?> </a><?php } ?>
				<?php if (class_exists('Astoundify_Job_Manager_Companies')) {
					echo "</a>";
				} ?>
				<?php the_company_tagline('<span class="company-tagline">- ', '</span>'); ?>
			</h3>
			<div class="ajax-job-view-links">

				<?php if ($company_id) {

					if (!empty(mas_wpjmc_get_the_meta_data('_company_website', $company_id))  || !empty(mas_wpjmc_get_the_meta_data('_company_email', $company_id)) || !empty(mas_wpjmc_get_the_meta_data('_company_twitter', $company_id)) || !empty(mas_wpjmc_get_the_meta_data('_company_facebook', $company_id)) || !empty(mas_wpjmc_get_the_meta_data('_company_phone', $company_id))) {
				?>


						<?php if (!empty($company_website = mas_wpjmc_get_the_meta_data('_company_website', $company_id))) : ?>
							<span class="company-data__content--list-item _company_website"><a class="website" href="<?php echo esc_url($company_website); ?>" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e('Website', 'workscout'); ?></a></span>
						<?php endif; ?>
						<?php if (!empty($company_email = mas_wpjmc_get_the_meta_data('_company_email', $company_id))) : ?>
							<span class="company-data__content--list-item _company_email">
								<a href="mailto:<?php echo esc_url($company_email); ?>" target="_blank"><i class="fa fa-envelope"></i> <?php echo esc_html($company_email); ?></a>
							</span>
						<?php endif; ?>
						<?php if (!empty($company_twitter = mas_wpjmc_get_the_meta_data('_company_twitter', $company_id))) : ?>
							<span class="company-data__content--list-item _company_twitter">
								<a href="<?php echo get_the_mas_company_twitter($company_id); ?>">
									<i class="fa fa-twitter"></i>
									<?php esc_html_e('Twitter', 'workscout'); ?>
								</a></span>

						<?php endif; ?>
						<?php if (!empty($company_facebook = mas_wpjmc_get_the_meta_data('_company_facebook', $company_id))) : ?>
							<span class="company-data__content--list-item _company_phone"><a href="<?php echo esc_url($company_facebook); ?>">
									<i class="fa fa-facebook"></i>
									<?php esc_html_e('Facebook', 'workscout'); ?>
								</a></span>

						<?php endif; ?>
						<?php if (!empty($company_phone = mas_wpjmc_get_the_meta_data('_company_phone', $company_id))) : ?>
							<span class="company-data__content--list-item _company_phone">
								<a href="tel:<?php echo esc_url($company_phone); ?>" target="_blank"><i class="fa fa-phone"></i>
									<?php echo esc_html($company_phone); ?>
								</a>
							</span>
						<?php endif; ?>

					<?php
					}
				} else { ?>

					<?php if ($website = get_the_company_website($post)) : ?>
						<span><a class="website" href="<?php echo esc_url($website); ?>" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e('Website', 'workscout'); ?></a></span>
					<?php endif; ?>
					<?php if (get_the_company_twitter()) : ?>
						<span><a href="http://twitter.com/<?php echo get_the_company_twitter(); ?>">
								<i class="fa fa-twitter"></i>
								@<?php echo get_the_company_twitter(); ?>
							</a></span>
					<?php endif; ?>
					<?php
					$company_facebook = get_post_meta($ID, '_company_facebook', true);
					if ($company_facebook) : ?>
						<span><a href="<?php echo esc_url($company_facebook); ?>">
								<i class="fa fa-facebook"></i>
								<?php esc_html_e('Facebook', 'workscout'); ?>
							</a></span>
					<?php endif; ?>
				<?php } ?>
			</div>
		</div>

		<div class="ajax-job-view-apply">
			<?php
			if (candidates_can_apply()) { ?>
				<?php

				$apply = get_the_job_application_method();

				if ($apply->type == 'url') {
					echo '<a class="button" target="_blank" href="' . esc_url($apply->url) . '">' . esc_html__('Apply for job', 'workscout') . '</a>';
				} else {

					echo '<a class="button" target="_blank" href="' . get_permalink() . '#apply-dialog">' . esc_html__('Apply for job', 'workscout') . '</a>';
				}

				?>

			<?php } ?>
		</div>


	</div>


	<div class="single-job-view-actions">


		<?php if (class_exists('WP_Job_Manager_Applications')) : ?>
			<?php if (is_position_filled()) : ?>
				<div class="notification closeable notice "><?php esc_html_e('This position has been filled', 'workscout'); ?></div>
				<div class="margin-bottom-35"></div>
			<?php elseif (!candidates_can_apply() && 'preview' !== $post->post_status) : ?>
				<div class="notification closeable notice "><?php esc_html_e('Applications have closed', 'workscout'); ?></div>
			<?php endif; ?>
		<?php endif;  ?>


		<?php do_action('workscout_bookmark_hook') ?>

		<?php
		$private_messages = get_option('workscout_private_messages_job');
		if ($private_messages && is_user_logged_in()) :

		?>
			<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


				<div class="small-dialog-header">
					<h3><?php esc_html_e('Send Message', 'workscout'); ?></h3>
				</div>

				<div class="message-reply margin-top-0">
					<?php get_job_manager_template('ws-private-message.php'); ?>

				</div>
			</div>
		<?php endif; ?>
		<?php

		if ($private_messages) : ?>
			<br>
			<?php if (is_user_logged_in()) : ?>
				<a href="#small-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="icon-material-outline-email"></i> <?php esc_html_e('Send Message', 'workscout'); ?></a>
			<?php else : ?>
				<?php
				$popup_login = get_option('workscout_popup_login');
				if ($popup_login == 'ajax') { ?>
					<a href="#login-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="icon-material-outline-email"></i> <?php esc_html_e('Login to Send Message', 'workscout'); ?></a>
				<?php } else {
					$login_page = get_option('workscout_profile_page'); ?>
					<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="send-message-to-owner button"><i class="icon-material-outline-email"></i> <?php esc_html_e('Login to Send Message', 'workscout'); ?></a>
				<?php } ?>

			<?php endif; ?>
		<?php endif; ?>

	</div>


	<!-- Widgets -->
	<div id="job-details">
		<?php dynamic_sidebar('sidebar-job-before'); ?>
		<!-- Sort by -->
		<div class="widget">
			<?php $overview_elements = Kirki::get_option('workscout', 'pp_job_overview', array('date_posted', 'expiration_date', 'application_deadline', 'location', 'job_title', 'hours', 'rate', 'salary'));  ?>
			<div class="job-overview">
				<?php do_action('single_job_listing_meta_before'); ?>
				<ul>
					<?php do_action('single_job_listing_meta_start'); ?>
					<?php if (in_array("date_posted", $overview_elements)) : ?>
						<li>
							<?php echo workscout_get_icon('date'); ?>
							<div>
								<strong><?php esc_html_e('Date Posted', 'workscout'); ?>:</strong>
								<span><?php the_job_publish_date($post) ?></span>
							</div>
						</li>
					<?php endif; //overview elements 
					?>
					<?php if (in_array("expiration_date", $overview_elements)) : ?>
						<?php
						$expired_date = get_post_meta($ID, '_job_expires', true);
						$hide_expiration = get_post_meta($ID, '_hide_expiration', true);

						if (empty($hide_expiration)) {
							if (!empty($expired_date)) { ?>
								<li>
									<?php echo workscout_get_icon('expiration'); ?>
									<div>
										<strong><?php esc_html_e('Expiration date', 'workscout'); ?>:</strong>
										<span><?php echo date_i18n(get_option('date_format'), strtotime(get_post_meta($ID, '_job_expires', true))) ?></span>
									</div>
								</li>
						<?php }
						} ?>
					<?php endif; //overview elements 
					?>

					<?php if (in_array("application_deadline", $overview_elements)) : ?>
						<?php
						if ($deadline = get_post_meta($ID, '_application_deadline', true)) {
							$expiring_days = apply_filters('job_manager_application_deadline_expiring_days', 2);
							$expiring = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= $expiring_days);
							$expired  = (floor((time() - strtotime($deadline)) / (60 * 60 * 24)) >= 0);

							echo '<li class="ws-application-deadline ' . ($expiring ? 'expiring' : '') . ' ' . ($expired ? 'expired' : '') . '"><i class="fa fa-calendar"></i>
						<div>
							<strong>' . ($expired ? __('Closed', 'workscout') : __('Closes', 'workscout')) . ':</strong><span>' . date_i18n(get_option('date_format'), strtotime($deadline)) . '</span></div></li>';
						} ?>
					<?php endif; //overview elements 
					?>

					<?php if (in_array("location", $overview_elements)) : ?>
						<li>
							<?php echo workscout_get_icon('location'); ?>
							<div>
								<strong><?php esc_html_e('Location', 'workscout'); ?>:</strong>
								<span class="location"><?php ws_job_location(); ?></span>
							</div>
						</li>
					<?php endif; //overview elements 
					?>
					<?php if (in_array("job_title", $overview_elements)) : ?>
						<li>
							<?php echo workscout_get_icon('title'); ?>
							<div>
								<strong><?php esc_html_e('Job Title', 'workscout'); ?>:</strong>
								<span><?php the_title(); ?></span>
							</div>
						</li>
					<?php endif; //overview elements 
					?>

					<?php if (in_array("hours", $overview_elements)) : ?>
						<?php $hours = get_post_meta($ID, '_hours', true);
						if ($hours) { ?>
							<li>
								<?php echo workscout_get_icon('hours'); ?>
								<div>
									<strong><?php esc_html_e('Hours', 'workscout'); ?>:</strong>
									<span><?php echo esc_html($hours) ?><?php esc_html_e('h / week', 'workscout'); ?></span>
								</div>
							</li>
						<?php } ?>
					<?php endif; //overview elements 
					?>

					<?php
					$currency_position =  get_option('workscout_currency_position', 'before');

					if (in_array("rate", $overview_elements)) : ?>
						<?php $rate_min = get_post_meta($ID, '_rate_min', true);
						if ($rate_min) {
							$rate_max = get_post_meta($ID, '_rate_max', true);  ?>
							<li>
								<?php echo workscout_get_icon('money'); ?>
								<div>
									<strong><?php esc_html_e('Rate:', 'workscout'); ?></strong>
									<span>
										<?php
										if ($currency_position == 'before') {
											echo get_workscout_currency_symbol();
										}
										echo esc_html($rate_min);
										if ($currency_position == 'after') {
											echo get_workscout_currency_symbol();
										}  ?>
										<?php
										if (!empty($rate_max)) {
											if (!empty($rate_min)) {
												echo '- ';
											}
											if ($currency_position == 'before') {
												echo get_workscout_currency_symbol();
											}
											echo $rate_max;
											if ($currency_position == 'after') {
												echo get_workscout_currency_symbol();
											}
										} ?><?php esc_html_e(' / hour', 'workscout'); ?>
									</span>
								</div>
							</li>
						<?php } ?>
					<?php endif; //overview elements 
					?>

					<?php if (in_array("salary", $overview_elements)) : ?>
						<?php
						$salary_min = get_post_meta($ID, '_salary_min', true);
						$salary_max = get_post_meta($ID, '_salary_max', true);
						$decimals = get_option('workscout_number_decimals', 2);
						if (!empty($salary_min) || !empty($salary_max)) { ?>
							<li>
								<?php echo workscout_get_icon('money'); ?>
								<div>
									<strong><?php esc_html_e('Salary:', 'workscout'); ?></strong>
									<span>
										<?php
										if (!empty($salary_min) && is_numeric($salary_min)) {
											if ($currency_position == 'before') {
												echo get_workscout_currency_symbol();
											}
											echo esc_html(number_format_i18n($salary_min));
											if ($currency_position == 'after') {
												echo get_workscout_currency_symbol();
											}
										} else {
											echo $salary_min;
										}
										if (!empty($salary_max) && is_numeric($salary_max)  && $salary_max > $salary_min) {
											if ($salary_min) {
												echo ' - ';
											}
											if ($currency_position == 'before') {
												echo get_workscout_currency_symbol();
											}
											echo esc_html(number_format_i18n($salary_max));
											if ($currency_position == 'after') {
												echo get_workscout_currency_symbol();
											}
										} ?>
									</span>
								</div>
							</li>
						<?php } ?>
					<?php endif; //overview elements 
					?>
					<?php do_action('single_job_listing_meta_end'); ?>
				</ul>



			</div>

		</div>


	</div>
	<!-- Widgets / End -->

	<?php do_action('job_content_start'); ?>



	<div class="single_job_listing">

		<?php if (get_option('job_manager_hide_expired_content', 1) && 'expired' === $post->post_status) : ?>
			<div class="job-manager-info"><?php esc_html_e('This listing has expired.', 'workscout'); ?></div>
		<?php else : ?>
			<div class="job_description">
				<?php do_action('workscout_single_job_before_content'); ?>
				<?php the_company_video(); ?>
				<?php echo do_shortcode(apply_filters('the_job_description', get_the_content())); ?>
			</div>
			<?php
			/**
			 * single_job_listing_end hook
			 */
			do_action('single_job_listing_end');
			?>

			<?php
			$share_options = Kirki::get_option('workscout', 'pp_job_share');

			if (!empty($share_options)) {
				$id = $ID;
				$title = urlencode($post->post_title);
				$url =  urlencode(get_permalink($id));
				$summary = urlencode(workscout_string_limit_words($post->post_excerpt, 20));
				$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'medium');
				$imageurl = urlencode($thumb[0]);
			?>
				<ul class="share-post">
					<?php if (in_array("facebook", $share_options)) { ?><li><?php echo '<a target="_blank" class="facebook-share" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '">Facebook</a>'; ?></li><?php } ?>
					<?php if (in_array("twitter", $share_options)) { ?><li><?php echo '<a target="_blank" class="twitter-share" href="https://twitter.com/share?url=' . $url . '&amp;text=' . esc_attr($summary) . '" title="' . __('Twitter', 'workscout') . '">Twitter</a>'; ?></li><?php } ?>
					<?php if (in_array("google-plus", $share_options)) { ?><li><?php echo '<a target="_blank" class="google-plus-share" href="https://plus.google.com/share?url=' . $url . '&amp;title="' . esc_attr($title) . '" onclick=\'javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;\'>Google Plus</a>'; ?></li><?php } ?>
					<?php if (in_array("pinterest", $share_options)) { ?><li><?php echo '<a target="_blank"  class="pinterest-share" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . esc_attr($summary) . '&media=' . esc_attr($imageurl) . '" onclick="window.open(this.href); return false;">Pinterest</a>'; ?></li><?php } ?>
					<?php if (in_array("linkedin", $share_options)) { ?><li><?php echo '<a target="_blank"  class="linkedin-share" href="https://www.linkedin.com/cws/share?url=' . $url . '">LinkedIn</a>'; ?></li><?php } ?>


				</ul>
			<?php } ?>
			<div class="clearfix"></div>



		<?php endif; ?>


	</div>

	<?php
	$single_map = Kirki::get_option('workscout', 'pp_enable_single_jobs_map');
	$lng = $post->geolocation_long;
	if ($single_map && !empty($lng)) :
	?>

		<div class="widget margin-top-40">
			<h4><?php esc_html_e('Job Location', 'workscout') ?></h4>

			<div id="job_map" data-longitude="<?php echo esc_attr($post->geolocation_long); ?>" data-latitude="<?php echo esc_attr($post->geolocation_lat); ?>">

			</div>
		</div>

	<?php
	endif; ?>


</div>



<div class="clearfix"></div>
</div>