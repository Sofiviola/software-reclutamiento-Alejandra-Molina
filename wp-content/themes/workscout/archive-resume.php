<?php get_header();

$layout = Kirki::get_option('workscout', 'resumes_list_layout');
$sidebar = Kirki::get_option('workscout', 'resumes_sidebar');
if ($layout == 'full-page') { ?>

	<!-- Page Content
================================================== -->
	<div class="full-page-container-v2">

		<div class="full-page-sidebar-v2">
			<div class="full-page-sidebar-inner-v2" data-simplebar>
				<div class="sidebar-container-v2">

					<form class="resume_filters in_sidebar">
						<div class="job_filters_links"></div>
						<?php
						if (!empty($_GET['search_keywords'])) {
							$keywords = sanitize_text_field($_GET['search_keywords']);
						} else {
							$keywords = '';
						}
						?>
						<div class="widget ">
							<div class="search_resumes">
								<h4>Keywords</h4>
								<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('e.g. logo design, copywriting', 'workscout'); ?>" value="<?php echo esc_attr($keywords); ?>" />
								<div class="clearfix"></div>
							</div>
						</div>

						<?php if (get_option('resume_manager_enable_regions_filter') || is_tax('resume_region')) {  ?>
							<div class="widget ">
								<h4><?php esc_html_e('Region', 'workscout_core'); ?></h4>

								<?php

								if (is_tax('resume_region')) {
									$region = get_query_var('resume_region');
									$term = get_term_by('slug', $region, 'resume_region');
									$selected = $term->term_id;
								} else {
									$selected = isset($_GET['search_region']) ? $_GET['search_region'] : '';
								}

								$dropdown = wp_dropdown_categories(apply_filters('job_manager_regions_dropdown_args', array(
									'show_option_all' => __('All Regions', 'wp-job-manager-locations'),
									'hierarchical' => true,
									'orderby' => 'name',
									'taxonomy' => 'resume_region',
									'name' => 'search_region',
									'id' => 'search_region',
									'class' => 'search_region job-manager-category-dropdown select2-single ' . (is_rtl() ? 'chosen-rtl' : ''),
									'hide_empty' => 0,
									'selected' => $selected,
									'echo' => false,
								)));
								$fixed_dropdown = str_replace("&nbsp;", "", $dropdown);
								echo $fixed_dropdown;
								?>

							</div>
						<?php } else { ?>
							<div class="widget job-widget-location" >
								<h4><?php esc_html_e('Location', 'workscout_core'); ?></h4>
								<div class="search_location widget_range_filter">
									<?php
									if (!empty($_GET['search_location'])) {
										$location = sanitize_text_field($_GET['search_location']);
									} else {
										$location = '';
									} ?>
									<div class="sidebar-search_location-container">
										<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e('Location', 'workscout_core'); ?>" value="<?php echo esc_attr($location); ?>" />
										<a href="#"><i title="<?php esc_html_e('Find My Location', 'workscout_core') ?>" class="tooltip left la la-map-marked-alt"></i></a>
										<?php if (get_option('workscout_map_address_provider', 'osm') == 'osm') : ?><span class="type-and-hit-enter"><?php esc_html_e('type and hit enter', 'workscout_core') ?></span> <?php endif; ?>
									</div>

									<?php

									$geocode = get_option('workscout_maps_api_server', 0);
									$default_radius = get_option('workscout_maps_default_radius');
									if ($geocode) : ?>
										<h4 class="checkboxes" style="margin-bottom: 0;">
											<input type="checkbox" name="filter_by_radius_check" id="radius_check" class="filter_by_radius" <?php if (get_option('workscout_radius_state') == 'enabled') echo "checked"; ?>>
											<label for="radius_check"><?php esc_html_e('Search by Radius', 'workscout_core'); ?></label>
										</h4>


										<div class="widget_range_filter-inside">
											<span class="range-slider-subtitle"><?php esc_html_e('Radius around selected destination', 'workscout_core') ?></span>
										
											<input name="search_radius" id="search_radius" data-slider-currency="<?php echo get_option('workscout_radius_unit'); ?>" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="50">
											<div class=" margin-bottom-50"></div>
										</div>
										<div class="clearfix"></div>
									<?php endif; ?>
								</div>


							</div>
						<?php } ?>
						<!-- Skills -->
						<?php if (get_option('resume_manager_enable_skills')) : ?>
							<?php
							if (!empty($_GET['search_skills'])) {
								$selected_skills = sanitize_text_field($_GET['search_skills']);
							} else {
								$selected_skills  = '';
							}
							if (!is_tax('resume_skill') && get_terms('resume_skill')) : ?>
								<div class="widget">
									<h4><?php esc_html_e('Filter by Skills', 'workscout_core'); ?></h4>
									<div class="search_categories resume-filter">
										<?php job_manager_dropdown_categories(array('taxonomy' => 'resume_skill', 'hierarchical' => 1, 'name' => 'search_skills', 'orderby' => 'name', 'selected' => $selected_skills, 'hide_empty' => false, 'class' => 'select2-multiple', 'id' => 'search_skills', 'placeholder' => esc_html__('All skill', 'workscout_core'))); ?>

									</div>
								</div>
							<?php endif; ?>
						<?php endif; ?>


						<?php
						if (!empty($_GET['search_category'])) {
							$selected_category = sanitize_text_field($_GET['search_category']);
						} else {
							$selected_category  = '';
						}

						if (get_option('resume_manager_enable_categories') && !is_tax('resume_category') && get_terms('resume_category')) : ?>
							<div class="widget">
								<h4><?php esc_html_e('Filter by Categories', 'workscout_core'); ?></h4>
								<div class="search_categories resume-filter">
									<?php job_manager_dropdown_categories(array('taxonomy' => 'resume_category', 'hierarchical' => 1, 'name' => 'search_categories', 'orderby' => 'name', 'class' => 'select2-multiple', 'selected' => $selected_category, 'hide_empty' => false, 'id' => 'search_categories', 'placeholder' => esc_html__('All Categories', 'workscout_core'))); ?>
								</div>
							</div>
						<?php else : ?>
							<input type="hidden" name="search_categories[]" value="<?php echo sanitize_title(get_query_var('resume_category')); ?>" />
						<?php endif; ?>

						<?php if (get_option('workscout_enable_resume_filter_rate')) : ?>
							<div class="widget widget_range_filter">
								<h4 class="checkboxes" style="margin-bottom: 0;">
									<input type="checkbox" name="filter_by_rate_check" id="filter_by_rate" class="filter_by_check">
									<label for="filter_by_rate"><?php esc_html_e('Filter by Rate', 'workscout_core'); ?></label>
								</h4>
								<div class="widget_range_filter-inside">
									<div class="rate_amount range-indicator">
										<span class="from"></span> &mdash; <span class="to"></span>
									</div>
									<input type="hidden" name="filter_by_rate" id="rate_amount" type="checkbox">
									<div id="rate-range"></div>
								</div>
							</div>
						<?php endif; ?>


					</form>

				</div>
				<!-- Sidebar Container / End -->


			</div>
		</div>
		<!-- Full Page Sidebar / End -->


		<!-- Full Page Content -->
		<div class="full-page-content-container-v2" data-simplebar>
			<div class="full-page-content-inner-v2">

				<h3 class="page-title"><?php esc_html_e('Search Results', 'workscout'); ?></h3>


				<?php
				$order = Kirki::get_option('workscout', 'pp_resumes_order', 'DESC');
				$orderby = Kirki::get_option('workscout', 'pp_resumes_orderby', 'date');
				$per_page = Kirki::get_option('workscout', 'pp_resumes_per_page', 10);
				echo do_shortcode('[resumes show_filters="false" orderby="' . $orderby . '" order="' . $order . '" per_page="' . $per_page . '" show_pagination="true"]') ?>



				<?php get_template_part('template-parts/split-footer'); ?>


			</div>
		</div>
		<!-- Full Page Content / End -->



	</div>

	</div>
	<?php get_footer('empty'); ?>
<?php } else if ($layout == 'split') { ?>

	<!-- Page Content
================================================== -->
	<div class="full-page-container with-map">

		<!-- Full Page Content -->
		<div class="full-page-content-container" data-simplebar>
			<div class="full-page-content-inner">

				<?php get_template_part('template-parts/resume-split-filters'); ?>

				<div class="listings-container">

					<?php
					$order = Kirki::get_option('workscout', 'pp_resumes_order', 'DESC');
					$orderby = Kirki::get_option('workscout', 'pp_resumes_orderby', 'date');
					$per_page = Kirki::get_option('workscout', 'pp_resumes_per_page', 10);
					echo do_shortcode('[resumes show_filters="false" orderby="' . $orderby . '" order="' . $order . '" per_page="' . $per_page . '" show_pagination="true"]') ?>

				</div>

				<?php get_template_part('template-parts/split-footer'); ?>


			</div>
		</div>
		<!-- Full Page Content / End -->


		<!-- Full Page Map -->
		<div class="full-page-map-container">
			<?php $all_map = Kirki::get_option('workscout', 'pp_enable_all_jobs_map', 0);
			if ($all_map) {
				echo do_shortcode('[workscout-map type="resume" class="resumes_page"]');
			} else { ?>
				<div id="search_map" data-map-scroll="true" class="resumes_map"></div>
			<?php } ?>
		</div>
		<!-- Full Page Map / End -->

	</div>

	</div>

<?php
	get_footer('empty');
} else { ?>


	<!-- Titlebar
	================================================== -->
	<?php
	$map =  Kirki::get_option('workscout', 'pp_enable_resumes_map', 0);
	$header_image = Kirki::get_option('workscout', 'pp_resumes_header_upload', '');



	if (!empty($header_image)) { ?>
		<div id="titlebar" class="photo-bg single <?php if ($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
		<?php } else { ?>
			<div id="titlebar" class="single <?php if ($map) echo " with-map"; ?>">
			<?php } ?>

			<div class="container">
				<div class="sixteen columns">
					<div class="ten columns">
						<?php $count_jobs = wp_count_posts('resume', 'readable');	?>
						<span><?php printf(esc_html__('We have %s resumes in our database', 'workscout'), $count_jobs->publish) ?></span>
						<h2 class="showing_jobs"><?php esc_html_e('Showing all resumes', 'workscout') ?></h2>
					</div>

					<?php
					$call_to_action = Kirki::get_option('workscout', 'pp_call_to_action_resumes', 'resume');
					switch ($call_to_action) {
						case 'job':
							get_template_part('template-parts/button', 'job');
							break;
						case 'resume':
							get_template_part('template-parts/button', 'resume');
							break;
						default:
							# code...
							break;
					}
					?>
				</div>
			</div>
			</div>
			<?php
			if ($map) {
				$all_map = Kirki::get_option('workscout', 'pp_enable_all_resumes_map', 0);
				if ($all_map) {
					echo do_shortcode('[workscout-map type="resume" class="resumes_page"]');
				} else { ?>
					<div id="search_map" data-map-scroll="<?php echo Kirki::get_option('workscout', 'pp_maps_scroll_zoom', 1) == 1 ? 'true' : 'false'; ?>" class="resumes_map"></div>
			<?php
				}
			} ?>
			<div class="container wpjm-container <?php echo esc_attr($sidebar); ?>">
				<div class="row">
					<?php
					if ($sidebar == 'left-sidebar') {
						$classes = 'col-xl-9 col-lg-9 content-left-offset';
					} else {
						$classes = 'col-xl-9 col-lg-9 content-right-offset';
					} ?>

					<?php get_sidebar('resumes'); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>
						<div class="padding-right">

							<?php do_action('workscout_resumes_after_search_keywords'); ?>

							<?php
							$order = Kirki::get_option('workscout', 'pp_resumes_order', 'DESC');
							$orderby = Kirki::get_option('workscout', 'pp_resumes_orderby', 'date');
							$per_page = Kirki::get_option('workscout', 'pp_resumes_per_page', 10);
							echo do_shortcode('[resumes show_filters="false" orderby="' . $orderby . '" order="' . $order . '" per_page="' . $per_page . '" show_pagination="true"]') ?>
							<footer class="entry-footer">
								<?php edit_post_link(esc_html__('Edit', 'workscout'), '<span class="edit-link">', '</span>'); ?>
							</footer><!-- .entry-footer -->
						</div>
					</article>


				</div>
			</div>
			<?php get_footer();  ?>
		<?php } ?>