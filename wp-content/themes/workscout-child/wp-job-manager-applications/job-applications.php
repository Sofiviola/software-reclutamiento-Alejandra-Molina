<?php

/**
 * Lists the job applications for a particular job listing.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-applications/job-applications.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Applications
 * @category    Template
 * @version     1.7.1
 */

if (! defined('ABSPATH')) {
	exit;
}

?>
<div id="job-manager-job-applications">
	<div class="sixteen columns alpha omega">
		<p class="margin-bottom-25" style="float: left;"><?php printf(esc_html__('The job applications for "%s" are listed below.', 'workscout'), '<a href="' . get_permalink($job_id) . '"><strong>' . get_the_title($job_id) . '</strong></a>'); ?></p>
		<strong><a href="<?php echo esc_url(add_query_arg('download-excel', true)); ?>" class="download-csv job-applications-download-csv">Descargar Excel</a></strong>


	</div>
	<div class="job-applications">
		<form class="filter-job-applications" method="GET">
			<div class="eight columns alpha">
				<select name="application_status" class="select2-single">
					<option value=""><?php esc_html_e('Filter by status', 'workscout'); ?>...</option>
					<?php foreach (get_job_application_statuses() as $name => $label) : ?>
						<option value="<?php echo esc_attr($name); ?>" <?php selected($application_status, $name); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
				<div class="margin-bottom-15"></div>
			</div>

			<div class="eight columns omega">
				<select name="application_orderby" class="select2-single">
					<option value=""><?php esc_html_e('Newest first', 'workscout'); ?></option>
					<option value="name" <?php selected($application_orderby, 'name'); ?>><?php esc_html_e('Sort by name', 'workscout'); ?></option>
					<option value="rating" <?php selected($application_orderby, 'rating'); ?>><?php esc_html_e('Sort by rating', 'workscout'); ?></option>
				</select>
				<input type="hidden" name="action" value="show_applications" />
				<input type="hidden" name="job_id" value="<?php echo absint($_GET['job_id']); ?>" />
				<?php if (! empty($_GET['page_id'])) : ?>
					<input type="hidden" name="page_id" value="<?php echo absint($_GET['page_id']); ?>" />
				<?php endif; ?>
				<div class="margin-bottom-35"></div>
			</div>
		</form>

		<!-- Applications -->
		<div class="sixteen columns alpha omega">

			<?php
			// Ordenar las aplicaciones: primero las que tienen el estado 'Nuevo'
			usort($applications, function ($a, $b) {
				global $wp_post_statuses;

				// Obtener el estado de cada aplicación
				$status_a = $wp_post_statuses[$a->post_status]->label;
				$status_b = $wp_post_statuses[$b->post_status]->label;

				// Comparar si uno de los estados es 'Nuevo' y ordenarlo en consecuencia
				if ($status_a === 'Nuevo' && $status_b !== 'Nuevo') {
					return -1; // 'Nuevo' debe ir primero
				}
				if ($status_b === 'Nuevo' && $status_a !== 'Nuevo') {
					return 1; // 'Nuevo' debe ir primero
				}
				return 0; // Si ambos tienen el mismo estado, mantener el orden original
			});
			foreach ($applications as $application) :
			?>

				<!-- Agrego condicional para saber status de la card del usuario en cuestion, si es "Nuevo" por css se agrega color de fondo -->
				<?php global $wp_post_statuses; ?>
				<?php
				// Obtener la etiqueta del estado
				$status_label = $wp_post_statuses[$application->post_status]->label;
				// Verificar si el estado es "Nuevo"
				$is_new_status = $status_label === "Nuevo";
				?>

				<div class="application job-application <?php echo ($wp_post_statuses[$application->post_status]->label === 'Nuevo') ? 'new-status' : ''; ?> custom-style" id="application-<?php echo esc_attr($application->ID); ?>">
					<div class="app-content">

						<!-- Name / Avatar -->
						<div class="info">
							<?php echo get_job_application_avatar($application->ID, 90) ?>
							<span><?php if (($resume_id = get_job_application_resume_id($application->ID)) && 'publish' === get_post_status($resume_id) && function_exists('get_resume_share_link') && ($share_link = get_resume_share_link($resume_id))) : ?>
									<a href="<?php echo esc_attr($share_link); ?>"><?php echo $application->post_title; ?></a>
								<?php else : ?>
									<?php echo $application->post_title; ?>
								<?php endif; ?>
							</span>
							<ul>
								<?php if ($attachments = get_job_application_attachments($application->ID)) : ?>
									<?php foreach ($attachments as $attachment) : ?>
										<li><a href="<?php echo esc_url($attachment); ?>" title="<?php echo esc_attr(get_job_application_attachment_name($attachment)); ?>" class=" job-application-attachment"><i class="fa fa-file-text"></i> <?php echo esc_html(get_job_application_attachment_name($attachment, 15)); ?></a></li>
									<?php endforeach; ?>
								<?php endif; ?>
								<?php if ($email = get_job_application_email($application->ID)) : ?>
									<li><a href="mailto:<?php echo esc_attr($email); ?>?subject=<?php echo esc_attr(sprintf(esc_html__('Your job application for %s', 'workscout'), strip_tags(get_the_title($job_id)))); ?>&amp;body=<?php echo esc_attr(sprintf(esc_html__('Hello %s', 'workscout'), get_the_title($application->ID))); ?>" title="<?php esc_html_e('Email', 'workscout'); ?>" class="bjob-application-contact"><i class="fa fa-envelope"></i> <?php esc_html_e('Email', 'workscout'); ?></a></li>
								<?php endif; ?>


								<?php
								//print_r($application);
								?>


								<?php
								if (($resume_id = get_job_application_resume_id($application->ID)) && 'publish' === get_post_status($resume_id)
									&& function_exists('get_resume_share_link') && ($share_link = get_resume_share_link($resume_id))
								) :
									// Generar un nonce único para esta aplicación
									$nonce = wp_create_nonce('change_status_' . $application->ID); // Usamos el ID de la aplicación para el nonce
								?>
									<li>
										<a
											href="<?php echo esc_attr(add_query_arg([
														'application_id' => $application->ID,
														'nonce' => $nonce,  // Pasar el nonce único para verificar
													], $share_link)); ?>"
											target="_blank"
											class="job-application-resume"
											data-id="<?php echo esc_attr($application->ID); ?>">
											<i class="fa fa-download" aria-hidden="true"></i>
											<?php esc_html_e('View Resume', 'workscout'); ?>
										</a>
									</li>
								<?php endif; ?>




							</ul>
						</div>

						<!-- Buttons -->
						<div class="buttons">

							<a href="#edit-<?php echo esc_attr($application->ID); ?>" title="<?php esc_html_e('Edit', 'workscout'); ?>" class="button gray app-link job-application-toggle-edit"><i class="fa fa-edit"></i> <?php esc_html_e('Edit', 'workscout'); ?></a>
							<a href="#notes-<?php echo esc_attr($application->ID); ?>" title="<?php esc_html_e('Nota', 'workscout'); ?>" class="button gray app-link job-application-toggle-notes"><i class="fa fa-sticky-note"></i> <?php esc_html_e('Nota', 'workscout'); ?></a>
							<!-- <a href="#details-<?php //echo esc_attr($application->ID); 
													?>" title="<?php //esc_html_e('Details', 'workscout'); 
																?>" class="button gray app-link job-application-toggle-content"><i class="fa fa-plus-circle"></i> <?php //esc_html_e('Details', 'workscout'); 
																																									?></a> -->

						</div>
						<!-- BOTÓN PARA ENVIAR MENSAJE DIRECTO  -->
						<div class="right-side" style="display: none">
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
						<div class="clearfix"></div>

					</div>

					<!-- application post_content -->
					<div class="app-content" style="padding: 0 40px;">
						<?php job_application_content($application); ?>
					</div>

					<!--  Hidden Tabs -->
					<div class="app-tabs">

						<a href="#" class="close-tab button gray"><i class="fa fa-close"></i></a>

						<!-- First Tab -->
						<div class="app-tab-content" id="edit-<?php echo esc_attr($application->ID); ?>">
							<form class="job-manager-application-edit-form job-manager-form" method="post">

								<fieldset class="select-grid fieldset-status">
									<label for="application-status-<?php echo esc_attr($application->ID); ?>"><?php esc_html_e('Application status', 'workscout'); ?>:</label>
									<div class="field">
										<select class="select2-single" id="application-status-<?php echo esc_attr($application->ID); ?>" name="application_status">
											<?php foreach (get_job_application_statuses() as $name => $label) : ?>
												<option value="<?php echo esc_attr($name); ?>" <?php selected($application->post_status, $name); ?>><?php echo esc_html($label); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</fieldset>

								<fieldset class="select-grid fieldset-rating">
									<label for="application-rating-<?php echo esc_attr($application->ID); ?>"><?php esc_html_e('Rating (out of 5)', 'workscout'); ?>:</label>
									<div class="field">
										<input type="number" step="1" id="application-rating-<?php echo esc_attr($application->ID); ?>" name="application_rating" step="0.1" max="5" min="0" placeholder="0" value="<?php echo esc_attr(get_job_application_rating($application->ID)); ?>" />
									</div>
								</fieldset>
								<div class="clearfix"></div>
								<p>
									<a class="button gray margin-top-15 delete-application delete_job_application" href="<?php echo wp_nonce_url(add_query_arg('delete_job_application', $application->ID), 'delete_job_application'); ?>"><?php esc_html_e('Delete this application', 'workscout'); ?></a>
									<input class="button margin-top-15" type="submit" name="wp_job_manager_edit_application" value="<?php esc_attr_e('Save changes', 'workscout'); ?>" />
									<input type="hidden" name="application_id" value="<?php echo absint($application->ID); ?>" />
									<?php wp_nonce_field('edit_job_application'); ?>
								</p>
							</form>
						</div>

						<!-- Second Tab -->
						<div class="app-tab-content" id="notes-<?php echo esc_attr($application->ID); ?>">
							<?php
							// Obtener la nota guardada
							$application_note = get_post_meta($application->ID, '_application_note', true);
							?>

							<form class="job-manager-application-notes-form" method="post">
								<fieldset>
									<textarea id="application-note-<?php echo esc_attr($application->ID); ?>" placeholder="Escribir una observación del candidato" name="application_note" rows="5"><?php echo esc_textarea($application_note); ?></textarea>
								</fieldset>
								<p style="margin-top: 15px;">
									<input class="button" type="submit" name="save_application_note" value="<?php esc_attr_e('Guardar', 'workscout'); ?>" />
									<input type="hidden" name="application_id" value="<?php echo absint($application->ID); ?>" />
									<?php wp_nonce_field('save_application_note', 'application_note_nonce'); ?>
								</p>
							</form>
						</div>

						<!-- Third Tab -->
						<div class="app-tab-content" id="details-<?php echo esc_attr($application->ID); ?>">
							<?php job_application_meta($application); ?>
							<?php job_application_content($application); ?>
						</div>

					</div>

					<!-- Footer -->
					<div class="app-footer">
						<?php $rating = get_job_application_rating($application->ID); ?>
						<div class="rating <?php echo workscout_get_rating_class($rating); ?>">
							<div class="star-rating"></div>
							<div class="star-bg"></div>
						</div>



						<?php global $wp_post_statuses; ?>
						<ul class="meta">
							<li><i class="fa fa-file-text-o"></i><?php echo $wp_post_statuses[$application->post_status]->label; ?></li>
							<li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), strtotime($application->post_date)); ?></li>
						</ul>
						<div class="clearfix"></div>
					</div>

				</div>
			<?php endforeach; ?>

		</div>
		<?php get_job_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>
	</div>
</div>