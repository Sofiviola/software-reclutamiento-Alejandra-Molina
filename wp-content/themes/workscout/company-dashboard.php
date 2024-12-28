<?php

/**
 * Template for the company dashboard (`[mas_company_dashboard]`) shortcode.
 *
 * This template can be overridden by copying it to yourtheme/mas-wp-job-manager-company/company-dashboard.php.
 *
 * @author      MadrasThemes
 * @package     MAS Companies For WP Job Manager
 * @category    Template
 * @version     1.0.2
 */

if (!defined('ABSPATH')) {
	exit;
}

$submission_limit           = get_option('job_manager_company_submission_limit');
$submit_company_form_page_id = get_option('job_manager_submit_company_form_page_id');
?>

<!-- Row -->
<div class="row">

	<!-- Dashboard Box -->
	<div class="col-xl-12">
		<div class="dashboard-box margin-top-0">

			<!-- Headline -->
			<div class="headline">
				<h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('My Companies', 'workscout'); ?></h3>
			</div>

			<div class="content">
				<ul class="dashboard-box-list">

					<?php if (!$companies) : ?>
						<li>
							<?php _e('You do not have any active company listings.', 'workscout'); ?>
						</li>
					<?php else : ?>
						<?php foreach ($companies as $company) : ?>
							<li>
								<!-- Job Listing -->
								<div class="item-listing">

									<!-- Job Listing Details -->
									<div class="item-listing-details">

										<a href="#" class="item-listing-company-logo">
											<?php  the_company_logo('thumbnail', null, $company->ID) ?>
										</a>
										<!-- Details -->
										<div class="item-listing-description">
											<h3 class="item-listing-title">
												<a href="<?php echo get_permalink($company->ID); ?>"><?php echo esc_html($company->post_title); ?></a>
												<span class="dashboard-status-button <?php switch (get_the_job_status_class($company)) {
																							case 'expired':
																								echo 'red';
																								break;
																							case 'publish':
																								echo 'green';
																								break;

																							default:
																								echo 'yellow';
																								break;
																						}; ?>"><?php the_job_status($company); ?></span>
											</h3>

											<!-- Job Listing Footer -->
											<div class="item-listing-footer">
												<ul>
													<li><i class="icon-material-outline-date-range"></i> <?php echo date_i18n(get_option('date_format'), strtotime($company->post_date)); ?></li>


												</ul>
											</div>
										</div>

									</div>
								</div>
								<!-- Buttons -->
								<div class="buttons-to-right always-visible">


									<?php
									$actions = array();

									switch ($company->post_status) {
										case 'publish':
											$actions['edit'] = array(
												'label' => esc_html__('Edit', 'workscout'),
												'nonce' => false
											);
											$actions['hide'] = array(
												'label' => esc_html__('Hide', 'workscout'),
												'nonce' => true
											);
											break;
										case 'private':
											$actions['publish'] = array(
												'label' => esc_html__('Publish', 'workscout'),
												'nonce' => true
											);
											break;
										case 'hidden':
											$actions['edit'] = array(
												'label' => esc_html__('Edit', 'workscout'),
												'nonce' => false
											);
											$actions['publish'] = array(
												'label' => esc_html__('Publish', 'workscout'),
												'nonce' => true
											);
											break;
										case 'pending':
										case 'pending_review':
											if (get_option('job_manager_user_can_edit_pending_company_submissions')) {
												$actions['edit'] = array(
													'label' => esc_html__('Edit', 'workscout'),
													'nonce' => false
												);
											}
											break;
										case 'expired':
											if (get_option('job_manager_manager_submit_company_form_page_id')) {
												$actions['relist'] = array(
													'label' => esc_html__('Relist', 'workscout'),
													'nonce' => true
												);
											}
											break;
									}

									$actions['delete'] = array('label' => esc_html__('Delete', 'workscout'), 'nonce' => true);

									$actions = apply_filters('mas_job_manager_company_my_company_actions', $actions, $company);

									foreach ($actions as $action => $value) {
										$action_url = add_query_arg(array('action' => $action, 'company_id' => $company->ID));
										if ($value['nonce']) {
											$action_url = wp_nonce_url($action_url, 'mas_job_manager_company_my_company_actions');
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
<?php if ($submit_company_form_page_id && (mas_wpjmc_company_manager_count_user_companies() < $submission_limit || !$submission_limit)) : ?>
	<a class="button margin-top-30" href="<?php echo esc_url(get_permalink($submit_company_form_page_id)); ?>"><?php _e('Add Company', 'workscout'); ?></a>
<?php endif; ?>