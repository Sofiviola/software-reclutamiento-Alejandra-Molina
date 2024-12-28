<?php

/**
 * Template Name: Page with Tasks Filters
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WorkScout
 */
$header_old = Kirki::get_option('workscout', 'pp_old_header');
$header_type = (Kirki::get_option('workscout', 'pp_old_header') == true) ? 'old' : '';
$header_type = apply_filters('workscout_header_type', $header_type);
get_header($header_type); ?><!-- Titlebar
================================================== -->
<?php
$map 			= Kirki::get_option('workscout', 'pp_enable_tasks_map', 0);
$titlebar 		= get_post_meta($post->ID, 'pp_page_titlebar', true);
$header_image 	= Kirki::get_option('workscout', 'pp_tasks_header_upload', '');

if ($titlebar == 'off') {
	// no titlebar
} else {
	if (!empty($header_image)) { ?>
		<?php
		$transparent_status = get_post_meta($post->ID, 'pp_transparent_header', TRUE);

		if ($transparent_status) { ?>
			<div id="titlebar" class="photo-bg single with-transparent-header <?php if ($map) echo " with-map"; ?>"" style=" background: url('<?php echo esc_url($header_image); ?>')">
			<?php } else { ?>
				<div id="titlebar" class="photo-bg single <?php if ($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
				<?php } ?>
			<?php } else { ?>
				<div id="titlebar" class="single <?php if ($map) echo " with-map"; ?>">
				<?php } ?>
				<div class="container">
					<div class="sixteen columns">
						<h1><?php the_title(); ?></h1>
						<?php if (function_exists('bcn_display')) { ?>
							<nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
								<ul>
									<?php bcn_display_list(); ?>
								</ul>
							</nav>
						<?php } ?>
					</div>
				</div>
				</div>
			<?php
		}
		$layout  = get_post_meta($post->ID, 'pp_sidebar_layout', true);
		if (empty($layout)) {
			$layout = 'right-sidebar';
		}

			?>
			<div class="margin-top-90"></div>
			<div class="container <?php echo esc_attr($layout); ?>">
				<div class="row">
					<div class="col-xl-3 col-lg-3">
						<div class="sidebar-container">
							<?php get_sidebar('tasks'); ?>
						</div>
						<!-- Sidebar / End -->
					</div>
					<div class="col-xl-9 col-lg-9 content-left-offset">


						<?php
						while (have_posts()) : the_post(); ?>
							<?php the_content(); ?>

							<footer class="entry-footer">
								<?php edit_post_link(esc_html__('Edit', 'workscout'), '<span class="edit-link">', '</span>'); ?>
							</footer><!-- .entry-footer -->
						<?php endwhile; ?>


					</div>
				</div>
			</div>
			<?php
			get_footer();
