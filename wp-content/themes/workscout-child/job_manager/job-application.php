<?php

/**
 * The template for displaying the job application form.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/job-application.php.
 *
 * HOWEVER, on occasion WP Job Manager will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 *
 * @see 	    https://wpjobmanager.com/document/template-overrides/
 * @package 	WP Job Manager
 * @category 	Templates
 * @version     1.29.0
 */
if ($apply = get_the_job_application_method()) :
	wp_enqueue_script('wp-job-manager-job-application');
?>
	<div class="job_application application">
		<?php do_action('job_application_start', $apply); ?>
		<?php $show_tabs = false;
		if (get_option('resume_manager_enable_application') && class_exists('WP_Resume_Manager')) {
			$show_tabs = true;
		}
		
		if (get_option('resume_manager_force_application')) {
			$show_tabs = false;
		}
		?>

		<a href="#apply-dialog" class="small-dialog popup-with-zoom-anim button apply-dialog-button"><?php esc_html_e('Apply for job', 'workscout'); ?></a>

		<div id="apply-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup">
			<div class="small-dialog-headline">
				<h2><?php esc_html_e('Apply For This Job', 'workscout') ?></h2>
			</div>
			<div class="small-dialog-content">

				<?php if ($show_tabs) { ?>
					<!-- <div class="tab-slider--nav">
						<ul class="tab-slider--tabs">
							<li class="tab-slider--trigger active" rel="tab1"><?php // esc_html_e('Apply with Resume', 'workscout'); ?></li>
							 <li class="tab-slider--trigger" rel="tab2"><?php // esc_html_e('Send Application', 'workscout'); ?></li>
						</ul>
					</div> -->
					<div class="tab-slider--container">
					<?php } ?>
					<?php
					/**
					 * job_manager_application_details_email or job_manager_application_details_url hook
					 */

					do_action('job_manager_application_details_' . $apply->type, $apply);

					?>
					<?php if ($show_tabs) { ?> </div>
				<?php } ?>
			</div>



			<?php do_action('job_application_end', $apply); ?>
		</div>
		</div>
	<?php endif; ?>