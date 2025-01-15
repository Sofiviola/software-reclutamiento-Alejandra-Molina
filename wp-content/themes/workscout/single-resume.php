<?php

/**
 * The template for displaying all single jobs.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WorkScout
 */

$header_old = Kirki::get_option('workscout', 'pp_old_header');
$header_type = (Kirki::get_option('workscout', 'pp_old_header') == true) ? 'old' : '';
$header_type = apply_filters('workscout_header_type', $header_type);
$user_id = get_current_user_id();
get_header($header_type);
$header_image_url = get_post_meta($post->ID, 'pp_job_header_bg', TRUE);

if (empty($header_image_url)) {
	$header_image_url = get_post_meta($post->ID, '_header_image', TRUE);
}

$header_image = apply_filters('workscout_single_job_header_image', $header_image_url);

?>


<?php while (have_posts()) : the_post(); ?>
	<?php if (resume_manager_user_can_view_resume($post->ID)) :

		$resume_photo_style = Kirki::get_option('workscout', 'pp_resume_rounded_photos', 'off');


	?>

		<!-- Titlebar
================================================== -->
		<div class="single-page-header freelancer-header <?php if (!$header_image) echo "no-photo"; ?>" data-background-image="<?php echo esc_url($header_image); ?>">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="single-page-header-inner">
							<div class="left-side">
								<div class="header-image freelancer-avatar"><?php echo get_avatar($user_id, 32); ?></div>
								<div class="header-details">
									<h3><?php the_title(); ?> <span><?php the_candidate_title(); ?></span></h3>
									<ul>
										<?php $rating_value = get_post_meta($post->ID, 'workscout-avg-rating', true);
										if ($rating_value) {  ?>
											<li>
												<div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
											</li>
										<?php } else { ?>
											<li>
												<div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout'); ?></div>
											</li>
										<?php } ?>
										<?php
										$country = get_post_meta($post->ID, '_country', true);

										if ($country) {
											$countries = workscoutGetCountries();
										?>
											<li><img class="flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" alt=""> <?php echo $countries[$country]; ?></li>
										<?php } ?>
										<li>
											<span class="icons"><i class="icon-material-outline-location-on"></i> <?php ws_candidate_location(); ?></span>
										</li>
										<li>
											<span class="icons"><i class="icon-brand-linkedin"></i> <?php 
												// get LinkedIn URL
												$linkedIn = get_post_meta($post->ID, '_candidate_linkedin', true);
												if ($linkedIn) {
													echo '<a href="' . esc_url($linkedIn) . '" target="_blank">' . esc_html__('LinkedIn', 'workscout') . '</a>';
												}
											?></span>
										</li>
										<!-- email -->
										<?php
										$email = get_post_meta($post->ID, '_candidate_email', true);
										if ($email) {
										?>
											<li>
												<span class="icons"><i class="icon-feather-mail"></i> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></span>
											</li>
										<?php } ?>
										<!-- phone -->
										<?php
										$phone = get_post_meta($post->ID, '_candidate_phone', true);
										if ($phone) {
										?>
											<li>
												<span class="icons"><i class="icon-feather-phone"></i> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></span>
											</li>
										<?php } ?>
										<!-- DNI -->
										<?php
										$dni = get_post_meta($post->ID, '_candidate_dni', true);
										if ($dni) {
										?>
											<li>
												<span class="icons"><i class="icon-feather-user"></i> <?php echo esc_html($dni); ?></span>
											</li>
										<?php } ?>

										<?php if (workscout_is_user_verified($user_id)) { ?>
											<li>
												<div class="verified-badge-with-title"><?php esc_html_e('Verified', 'workscout'); ?></div>
											</li>
										<?php } ?>
									</ul>
								</div>
							</div>
							<div class="right-side">

								<?php
								$private_messages = get_option('workscout_private_messages_resumes');

								if ($private_messages) :
									if (is_user_logged_in()) :
										$owner_id = get_the_author_meta('ID');
										$owner_data = get_userdata($owner_id);
								?>
										<!-- Reply to review popup -->
										<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


											<div class="small-dialog-header">
												<h3><?php esc_html_e('Send Message', 'workscout'); ?></h3>
											</div>
											<div class="message-reply margin-top-0">
												<?php get_job_manager_template('ws-private-message-resume.php'); ?>

											</div>
										</div>


										<a href="#small-dialog" class="send-message-resume button margin-top-35  margin-bottom-50 full-width ripple-effect popup-with-zoom-anim"><i class="icon-material-outline-email"></i> <?php esc_html_e('Send Message', 'workscout'); ?></a>

										<?php else :
										$popup_login = get_option('workscout_popup_login');
										if ($popup_login == 'ajax') { ?>
											<a href="#login-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="icon-material-outline-email"></i> <?php esc_html_e('Login to Send Message', 'workscout'); ?></a>
										<?php } else {
											$login_page = get_option('workscout_profile_page'); ?>
											<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="send-message-to-owner button"><i class="icon-material-outline-email"></i> <?php esc_html_e('Login to Send Message', 'workscout'); ?></a>
										<?php } ?>
									<?php endif; ?>
								<?php else : ?>
									<?php get_job_manager_template('contact-details.php', array('post' => $post), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/'); ?>
								<?php endif; ?>




							</div>

						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- Page Content
===============================s=================== -->
		<div class="container">
			<div class="row">

				<!-- Content -->
				<div class="col-xl-8 col-lg-8 content-right-offset">

					<!-- Page Content -->
					<div class="single-page-section">
						<h3 class="margin-bottom-25"><?php esc_html_e('About Me', 'workscout'); ?></h3>
						<?php do_action('single_resume_start'); ?>
						<?php the_candidate_video(); ?>
						<?php echo do_shortcode(apply_filters('the_resume_description', get_the_content())); ?>
						<?php do_action('single_resume_meta_start'); ?>
						<?php do_action('single_resume_meta_end'); ?>
					</div>

					<?php get_template_part('wp-job-manager-resumes/partials/gallery-grid'); ?>



					<?php if ($items = get_post_meta($post->ID, '_candidate_education', true)) : ?>
						<!-- Boxed List -->
						<div class="boxed-list margin-bottom-60">
							<div class="boxed-list-headline">
								<h3><i class="icon-material-outline-thumb-up"></i> <?php esc_html_e('Education', 'workscout'); ?></h3>
							</div>
							<ul class="boxed-list-ul">
								<?php
								foreach ($items as $item) : ?>
									<li>
										<div class="boxed-list-item">
											<!-- Content -->
											<div class="item-content">
												<h4>
													<?php
													if (!empty($item['qualification'])) {
														printf(esc_html__('%s at %s', 'workscout'),  esc_html($item['qualification']),  esc_html($item['location']));
													} else {
														printf(esc_html__('%s', 'workscout'),  esc_html($item['location']));
													}
													?>
												</h4>
												<div class="item-details margin-top-10">

													<div class="detail-item"><i class="icon-material-outline-date-range"></i> <?php echo esc_html($item['date']); ?></div>
												</div>
												<div class="item-description">
													<?php if (isset($item['notes'])) {
														echo wpautop(wptexturize($item['notes']));
													} ?>
												</div>
											</div>
										</div>
									</li>
								<?php endforeach;
								?>

							</ul>



						</div>
						<!-- Boxed List / End -->
					<?php endif; ?>


					<?php if ($items = get_post_meta($post->ID, '_candidate_experience', true)) : ?>
						<!-- Boxed List -->
						<div class="boxed-list margin-bottom-60">
							<div class="boxed-list-headline">
								<h3><i class="icon-material-outline-business"></i> <?php esc_html_e('Experience', 'workscout'); ?></h3>
							</div>
							<ul class="boxed-list-ul">
								<?php
								foreach ($items as $item) : ?>
									<li>
										<div class="boxed-list-item">


											<!-- Content -->
											<div class="item-content">
												<h4><a href="#"><?php echo esc_html($item['job_title']);  ?></a></h4>
												<div class="item-details margin-top-7">
													<div class="detail-item"><a href="#"><i class="icon-material-outline-business"></i> <?php echo esc_html($item['employer']);  ?> </a></div>
													<div class="detail-item"><i class="icon-material-outline-date-range"></i> <?php echo esc_html($item['date']); ?></div>
												</div>
												<div class="item-description">
													<?php echo wpautop(wptexturize($item['notes'])); ?>
												</div>
											</div>
										</div>
									</li>


								<?php endforeach; ?>


							</ul>
						</div>
						<!-- Boxed List / End -->
					<?php endif; ?>
					<?php

					// If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()) :
						comments_template();
					endif;

					?>
				</div>


				<!-- Sidebar -->
				<div class="col-xl-4 col-lg-4">
					<div class="sidebar-container">

						<?php if (class_exists('WorkScout_Freelancer')) { ?>
							<div class="sidebar-widget widget profile-overview-widget">
								<h3><?php esc_html_e('Profile Overview', 'workscout'); ?></h3>

								<!-- Profile Overview -->
								<div class="profile-overview">

									<div class="overview-item">
										<?php $rate = get_post_meta($post->ID, '_rate_min', true);
										$currency_position =  get_option('workscout_currency_position', 'before');

										?>
										<?php
										if (!empty($rate)) {
											echo '<strong>';
											if ($currency_position == 'before') {
												echo get_workscout_currency_symbol();
											}
											echo get_post_meta($post->ID, '_rate_min', true);
											if ($currency_position == 'after') {
												echo get_workscout_currency_symbol();
											}
											echo '</strong>';
										} else {
											echo '<strong class="negotiable">' . esc_html('Negotiable', 'workscout') . '</strong>';
										}
										?><span><?php esc_html_e('Hourly Rate', 'workscout'); ?></span>


									</div>
									<div class="overview-item"><strong><?php echo  workscout_count_posts_by_user(get_the_author_meta('ID'), 'task', 'completed'); ?></strong><span><?php esc_html_e('Jobs Done', 'workscout'); ?></span></div>

								</div>
							</div>



							<!-- Freelancer Indicators -->
							<?php if ($items = get_post_meta($post->ID, '_competencies', true)) : ?>
								<div class="sidebar-widget widget">
									<div class="freelancer-indicators">
										<?php
										foreach ($items as $item) : ?>
											<div class="indicator">
												<strong><?php echo $item['qualification']; ?>%</strong>
												<div class="indicator-bar" data-indicator-percentage="<?php echo $item['qualification']; ?>"><span></span></div>
												<span><?php echo $item['skill']; ?></span>
											</div>


										<?php endforeach; ?>

									</div>
								</div>
							<?php endif; ?>
							<!-- Widget -->
						<?php } else { ?>
 
						<?php } ?>
						<?php if (resume_has_links()) :  ?>
							<!-- <div class="sidebar-widget widget">
								<h3><?php // esc_html_e('Social Profiles', 'workscout'); ?></h3>
								<div class="freelancer-socials margin-top-25">
									<ul>

										<?php
										$services = workscoutBrandIcons();
										foreach (get_resume_links() as $link) : ?>
											<?php
											$parsed_url = parse_url($link['url']);
											$service = $link['name'];
											$service_name = $services[$service];
											?>
											<li><a rel="nofollow" href="<?php echo esc_url($link['url']); ?>" title="<?php echo esc_attr($service_name); ?>" data-tippy-placement="top"><i class="icon-brand-<?php echo $link['name'] ?>"></i></a></li>
										<?php endforeach; ?>



									</ul>
								</div>
							</div> -->
						<?php endif; ?>

						<!-- Widget -->

						<?php if (($skills = wp_get_object_terms($post->ID, 'resume_skill', array('fields' => 'names'))) && is_array($skills)) : ?>
							<div class="sidebar-widget widget">
								<h3><?php esc_html_e('Skills', 'workscout'); ?></h3>
								<div class="task-tags">
									<?php echo '<span>' . implode('</span><span>', $skills) . '</span>'; ?>
								</div>
								<div class="clearfix"></div>
							</div>
						<?php endif; ?>




						<?php if (resume_has_file()) : ?>
							<div class="sidebar-widget widget">
								<h3><?php esc_html_e('Attachments', 'workscout'); ?></h3>
								<div class="attachments-container">
									<?php
									if (($resume_files = get_resume_files()) && apply_filters('resume_manager_user_can_download_resume_file', true, $post->ID)) : ?>
										<?php foreach ($resume_files as $key => $resume_file) :
										?>
											<a href="<?php echo esc_url(get_resume_file_download_url(null, $key)); ?>" class="attachment-box ripple-effect"><span><?php echo basename($resume_file); ?></span><i><?php echo pathinfo($resume_file, PATHINFO_EXTENSION); ?></i></a>


										<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						<!-- Widget -->

						<?php get_sidebar('resume'); ?>
						<?php do_action('workscout_bookmark_hook') ?>
					</div>
				</div>

			</div>
		</div>
	<?php else : ?>

		<?php get_job_manager_template_part('access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/'); ?>

	<?php endif; ?>
<?php endwhile; // End of the loop. 
?>
<!-- Spacer -->
<div class="margin-top-15"></div>
<!-- Spacer / End-->
<?php get_footer(); ?>