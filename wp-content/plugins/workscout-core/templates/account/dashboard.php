<?php
$current_user = wp_get_current_user();
$user_post_count = count_user_posts($current_user->ID, 'job_listings');
$roles = $current_user->roles;
$role = array_shift($roles);



?>


<!-- Content -->
<div class="fun-facts-container">

	<?php if (in_array($role, array('administrator', 'admin', 'employer'))) : ?>
		<!-- Item -->
		<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_1'); ?>">
			<div class="fun-fact-text">
				<span><?php esc_html_e('Active Job Listings', 'workscout_core'); ?></span>
				<h4><?php $user_post_count = count_user_posts($current_user->ID, 'job_listing');
					echo $user_post_count; ?></h4>
			</div>
			<div class="fun-fact-icon"><i class="icon-material-outline-business-center"></i></div>
		</div>

	<?php else : ?>
		<!-- Item -->
		<?php if (class_exists('WP_Resume_Manager')) : ?>
			<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_1'); ?>">
				<div class="fun-fact-text">
					<span><?php esc_html_e('Active Resumes', 'workscout_core'); ?></span>
					<h4><?php $user_post_count = count_user_posts($current_user->ID, 'resume');
						echo $user_post_count; ?></h4>
				</div>
				<div class="fun-fact-icon"><i class="icon-material-outline-business-center"></i></div>
			</div>
		<?php endif; ?>
	<?php endif; ?>


	<?php if (in_array($role, array('administrator', 'admin', 'employer'))) : ?>
		<?php $total_views = get_user_meta($current_user->ID, 'workscout_total_jobs_views', true);
		if (!$total_views) {
			$total_views = 0;
		} ?>

		<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_2'); ?>">
			<div class="fun-fact-text">
				<span><?php esc_html_e('Total Jobs Views', 'workscout_core'); ?></span>
				<h4><?php echo esc_html($total_views); ?></h4>
			</div>
			<div class="fun-fact-icon"><i class="icon-feather-trending-up"></i></div>
		</div>
	<?php else : ?>

		<?php if (class_exists('WP_Resume_Manager')) : ?>
			<?php $total_views = get_user_meta($current_user->ID, 'workscout_total_resumes_views', true);
			if (!$total_views) {
				$total_views = 0;
			} ?>
			<!-- Item -->
			<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_2'); ?>">
				<div class="fun-fact-text">
					<span><?php esc_html_e('Resumes Views', 'workscout_core'); ?></span>
					<h4><?php echo esc_html($total_views); ?></h4>
				</div>
				<div class="fun-fact-icon"><i class="icon-feather-trending-up"></i></div>
			</div>

		<?php endif; ?>

	<?php endif; ?>
	<?php if (class_exists('WP_Job_Manager_Applications')) :  ?>
		<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_3'); ?>">
			<div class="fun-fact-text">
				<span><?php
						if ($role == 'candidate') {
							esc_html_e('Your Applications', 'workscout_core');
						} else {
							esc_html_e('Total Applications', 'workscout_core');
						} ?></span>
				<h4><?php

					if ($role == 'employer' || $role == 'administrator') {

						$args = array(
							'author'        =>  $current_user->ID,
							'post_type'       =>  'job_listing',
							'orderby'       =>  'post_date',
							'order'         =>  'ASC',
							'posts_per_page' => -1, // no limit
							'fields'        => 'ids',
							'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'expired')
						);
						$current_user_jobs = get_posts($args);
						$total_apps = 0;
						foreach ($current_user_jobs as $id) {
							$total_apps = $total_apps + get_job_application_count($id);
						}
						echo $total_apps;
					}
					if ($role == 'candidate') {


						$user_post_count = workscout_count_user_applications($current_user->ID);

						echo $user_post_count;
					}
					?></h4>
			</div>
			<div class="fun-fact-icon"><i class="icon-material-outline-rate-review"></i></div>
		</div>
	<?php endif; ?>

	<!-- Item -->
	<?php
	if (class_exists('WP_Job_Manager_Bookmarks')) :
		if ($role == 'employer' || $role == 'administrator') {
			$total_bookmarks = workscout_count_all_user_jobs_bookmarks($current_user->ID);
		} else {
			$total_bookmarks = workscout_count_all_user_bookmarks($current_user->ID);
		} ?>
		<!-- Item -->
		<div class="fun-fact" data-fun-fact-color="<?php echo Kirki::get_option('workscout', 'dashboard_box_color_4'); ?>">
			<div class="fun-fact-text">
				<span><?php
						if ($role == 'candidate') {
							esc_html_e('Bookmarks', 'workscout_core');
						} else {
							esc_html_e('Times Bookmarked', 'workscout_core');
						} ?></span>
				<h4><?php echo esc_html($total_bookmarks); ?></h4>
			</div>
			<div class="fun-fact-icon"><i class="icon-material-outline-bookmarks"></i></div>
		</div>

	<?php endif; ?>
</div>


<div class="row">

	<!-- Recent Activity -->
	<div class="col-lg-6">
		<div class="dashboard-box">

			<div class="headline">
				<h3><i class="icon-material-baseline-notifications-none"></i> <?php esc_html_e('Recent Activities', 'workscout_core'); ?></h3>

			</div>
			<div class="content">

				<?php
				global $wpdb;

				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;

				$rowcount = $wpdb->get_var(

					'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'workscout_core_activity_log
					WHERE related_to_id = ' . $user_id . '
					ORDER BY  log_time DESC'

				);
				if ($rowcount > 0) : ?>
					<a href="#" id="workscout-clear-activities" class="clear-all-activities" data-nonce="<?php echo wp_create_nonce('delete_activities'); ?>"><?php esc_html_e('Clear All', 'workscout_core') ?></a>
				<?php endif; ?>
				<?php echo do_shortcode('[workscout_activities]'); ?>

			</div>
		</div>

		<?php if (class_exists('WC_Paid_Listings')) : ?>

			<!-- Invoices -->
			<!-- Recent Activity -->
			<div class="col-lg-6">
				<div class="dashboard-box">

					<div class="headline">
						<h3><i class="icon-material-outline-shopping-cart"></i> <?php if ($role == 'candidate') { ?>
								<?php esc_html_e('Your Resume Packages', 'workscout_core') ?>
							<?php } else { ?>
								<?php esc_html_e('Your Listing Packages', 'workscout_core') ?>
							<?php } ?>
						</h3>

					</div>
					<div class="content">

						<ul class="dashboard-box-list products user-packages">
							<?php
							if (function_exists('wc_paid_listings_get_user_packages')) :
								$user_packages = wc_paid_listings_get_user_packages(get_current_user_id());

								if ($user_packages) :
									foreach ($user_packages as $key => $package) :
										$package = wc_paid_listings_get_package($package);
										if (!$package) {
											continue;
										}
							?>
										<li>
											<div class="invoice-list-item">
												<strong><?php echo $package->get_title(); ?></strong>
												<ul>
													<?php $order_id = $package->get_order_id();
													$order = wc_get_order($order_id);
													if(!$order){
														continue;
													}
													?>
													<li><?php esc_html_e('Order', 'workscout_core'); ?>: #<?php echo $order_id; ?></li>
													<li><?php esc_html_e('Price:', 'workscout_core'); ?> <?php echo $order->get_formatted_order_total(); ?></li>

													<li><?php
														if ($package->get_limit()) {
															printf(_n('%1$s listings out of %2$d', '%1$s listings out of %2$d', $package->get_count(), 'workscout_core'), $package->get_count(), $package->get_limit());
														} else {
															printf(_n('%s listings posted', '%s listings posted', $package->get_count(), 'workscout_core'), $package->get_count());
														} ?>
													</li>
													<li>
														<?php if ($package->get_duration()) {
															printf(_n('For %s day', 'For %s days', $package->get_duration(), 'workscout_core'), $package->get_duration());
														} ?>
													</li>
												</ul>
											</div>
											<!-- Buttons -->

										</li>


									<?php endforeach;
								else : ?>
									<li class="no-icon"><?php esc_html_e("You don't have any packages yet.", 'workscout_core'); ?></li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>