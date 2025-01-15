<?php
$locreg_widget = $remote_widget = $job_types_widget = $job_categories_widget = $salary_widget = $rate_widget = $job_tags_widget = 'on';

if (is_page()) {
	$locreg_widget 			= get_post_meta($post->ID, 'pp_jobs_filters_locreg_widget', TRUE);
	$job_types_widget 		= get_post_meta($post->ID, 'pp_jobs_filters_types_widget', TRUE);
	$job_tags_widget 		= get_post_meta($post->ID, 'pp_jobs_filters_tags_widget', TRUE);
	$remote_widget 			= get_post_meta($post->ID, 'pp_jobs_filters_remote_widget', TRUE);
	$job_categories_widget 	= get_post_meta($post->ID, 'pp_jobs_filters_categories_widget', TRUE);
	$salary_widget 			= get_post_meta($post->ID, 'pp_jobs_filters_salary_widget', TRUE);
	$rate_widget 			= get_post_meta($post->ID, 'pp_jobs_filters_rate_widget', TRUE);
}
?>
<!-- Widgets -->
<div class="five columns sidebar" role="complementary">
	<?php
	$search_in_sb =  Kirki::get_option('workscout', 'pp_jobs_search_in_sb');
	if ($search_in_sb) {
		if (!empty($_GET['search_keywords'])) {
			$keywords = sanitize_text_field($_GET['search_keywords']);
		} else {
			$keywords = '';
		}
	?>
		<div class="widget job-widget-keywords">
			<h4><?php esc_html_e('Keywords', 'workscout_core'); ?></h4>
			<?php if (is_page() && is_page_template('template-jobs.php')) { ?>
				<form class="list-search" method="GET" action="<?php echo get_permalink(); ?>">
				<?php } else { ?>
					<form class="list-search" method="GET" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">
					<?php }  ?>
					<div class="search_keywords">
						<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('job title, keywords or company', 'workscout_core'); ?>" value="<?php echo esc_attr($keywords); ?>" />
						<div class="clearfix"></div>
					</div>
					</form>
		</div>
	<?php } ?>
	<form class="job_filters in_sidebar">
		<?php
		if (!empty($_GET['search_keywords'])) {
			$keywords = sanitize_text_field($_GET['search_keywords']);
		} else {
			$keywords = '';
		}
		?>
		<input type="hidden" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('job title, keywords or company', 'workscout_core'); ?>" value="<?php echo esc_attr($keywords); ?>" />


		<?php if (get_query_var('company')) { ?>
			<input type="hidden" name="company_field" value="<?php echo urldecode(get_query_var('company')) ?>">
		<?php } ?>


		<?php if (get_option('workscout_enable_location_sidebar') == 1) {
		?>
			<?php if (class_exists('Astoundify_Job_Manager_Regions') &&  (get_option('job_manager_regions_filter') || is_tax('job_listing_region'))) {  ?>
				<div class="widget job-widget-regions" <?php if ($locreg_widget == "off" || is_tax('job_listing_region')) : echo ' style="display:none;" ';
														endif; ?>>
					<h4><?php esc_html_e('Region', 'workscout_core'); ?></h4>

					<?php

					if (is_tax('job_listing_region')) {
						$region = get_query_var('job_listing_region');
						$term = get_term_by('slug', $region, 'job_listing_region');
						$selected = $term->term_id;
					} else {
						$selected = isset($_GET['search_region']) ? $_GET['search_region'] : '';
					}


					$dropdown = wp_dropdown_categories(array(
						'show_option_all'           => __('All Regions', 'workscout_core'),
						'hierarchical'              => true,
						'orderby'                   => 'name',
						'taxonomy'                  => 'job_listing_region',
						'name'                      => 'search_region',
						'id'                        => 'search_region',
						'class'                     => 'select2-single job-manager-category-dropdown',
						'hide_empty'                => 1,
						'selected'                  => $selected,
						'echo'                      => false,
					));
					$fixed_dropdown = str_replace("&nbsp;", "", $dropdown);
					echo $fixed_dropdown;

					?>


				</div>
			<?php } else { ?>
				<div class="widget job-widget-location" <?php if ($locreg_widget == "off") : echo ' style="display:none;" ';
														endif; ?>>
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
								<!-- <div class="radius_amount range-indicator">
									<span><?php echo $default_radius; ?></span> 
								</div> -->
								<!-- <input type="hidden" name="search_radius" value="<?php echo $default_radius; ?>" id="radius_amount" type="checkbox"> -->
								<input name="search_radius" id="search_radius" data-slider-currency="<?php echo get_option('workscout_radius_unit'); ?>" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="50">
								<div class=" margin-bottom-50"></div>
							</div>
							<div class="clearfix"></div>
						<?php endif; ?>
					</div>


				</div>
			<?php } ?>
		<?php } ?>


		<?php if (get_option('job_manager_enable_types')) { ?>
			<div class="widget job-widget-job-types" <?php if ($job_types_widget == "off") : echo ' style="display:none;" ';
														endif; ?>>
				<?php if (!is_tax('job_listing_type')) : ?><h4><?php esc_html_e('Tipo de trabajo', 'workscout_core'); ?></h4><?php endif; ?>
				<?php get_job_manager_template('job-filter-job-types.php', array('job_types' => '', 'atts' => array('orderby' => 'rand'), 'selected_job_types' => '')); ?>
			</div>
		<?php } ?>
		<?php if (get_option('job_manager_enable_remote_position')) :  ?>
			<div class="widget widget_search_remote_position" <?php if ($remote_widget == "off") : echo ' style="display:none;" ';
																endif; ?>>
				<h4 class="checkboxes" style="margin-bottom: 0;">
					<input type="checkbox" class="input-checkbox" name="remote_position" id="remote_position" placeholder="Location" value="1">
					<label for="remote_position" id="remote_position_label"><?php esc_html_e('Remote positions only', 'workscout_core'); ?></label>
				</h4>
			</div>
		<?php endif; ?>

		<?php
		if (!is_tax('job_listing_category') && get_terms('job_listing_category')) :
			$show_category_multiselect = get_option('job_manager_enable_default_category_multiselect', false);

			if (!empty($_GET['search_category'])) {
				$selected_category = sanitize_text_field($_GET['search_category']);
			} else {
				$selected_category = "";
			}
		?>
			<div class="widget job-widget-categories" <?php if ($job_categories_widget == "off") : echo ' style="display:none;" ';
														endif; ?>>
				<h4><?php esc_html_e('Categorias', 'workscout_core'); ?></h4>
				<div class="search_categories">

					<?php if ($show_category_multiselect) : ?>
						<?php
						$dropdown = job_manager_dropdown_categories(
							array(
								'taxonomy' => 'job_listing_category',
								'hierarchical' => true,
								// 'depth' => -1,
								'class' =>  'select2-multiple job-manager-category-dropdown ' . (is_rtl() ? 'chosen-rtl' : ''),
								'name' => 'search_categories',
								'orderby' => 'name',
								'selected' => $selected_category,
								'placeholder'     => __('Choose a category', 'workscout-core'),
								'hide_empty' => false,
								'echo' => false
							)
						);

						$fixed_dropdown = str_replace('&nbsp;&nbsp;&nbsp;', '- ', $dropdown);
						echo $fixed_dropdown; ?>
					<?php else : ?>
						<?php

						$dropdown = job_manager_dropdown_categories(array(
							'taxonomy' => 'job_listing_category',
							'hierarchical' => true,
							'class' =>  'select2-single job-manager-category-dropdown ' . (is_rtl() ? 'chosen-rtl' : ''),
							'show_option_all' => esc_html__('Any category', 'workscout_core'),
							'name' => 'search_categories',
							'orderby' => 'name',
							'selected' => $selected_category,
							'placeholder'     => __('Choose a category', 'workscout_core'),
							'multiple' => false,
							'echo' => false,
							'hide_empty' => false
						));
						$fixed_dropdown = str_replace('&nbsp;&nbsp;&nbsp;', '- ', $dropdown);
						echo $fixed_dropdown;  ?>
					<?php endif; ?>

				</div>
			</div>
		<?php else : ?>
			<input type="hidden" name="search_categories[]" value="<?php echo sanitize_title(get_query_var('job_listing_category')); ?>" />
		<?php endif; ?>

		<?php if (get_option('workscout_enable_filter_salary')) : ?>
			<div class="widget widget_range_filter widget-salary-filter" <?php if ($salary_widget == "off") : echo ' style="display:none;" ';
																			endif; ?>>

				<h4 class="checkboxes" style="margin-bottom: 0;">
					<input type="checkbox" name="filter_by_salary_check" id="salary_check" class="filter_by_check">
					<label for="salary_check"><?php esc_html_e('Filter by Salary', 'workscout_core'); ?></label>
				</h4>

				<div class="widget_range_filter-inside">
					<!-- <div class="salary_amount range-indicator">
						<span class="from"></span> &mdash; <span class="to"></span>
					</div>
					<input type="hidden" name="filter_by_salary" id="salary_amount" type="checkbox">
					<div id="salary-range"></div> -->
					<?php
					$salary_min = workscout_get_min_meta('_salary_min');
					$salary_max = workscout_get_max_meta('_salary_max');

					// find step for slider between rate_min and rate_max
					$range = $salary_max - $salary_min;
					if ($range <= 1000) {
						$step = 1; // Set a small step for a narrow range
					} else if ($range <= 10000) {
						$step = 100; // Set a medium step for a moderate range
					} else {
						$step = 500; // Set a larger step for a wide range
					}

					?>
					<input class="range-slider" id="salary-range" name=" filter_by_salary" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="<?php echo esc_attr($salary_min); ?>" data-slider-max="<?php echo esc_attr($salary_max); ?>" data-slider-step="<?php echo esc_attr($step); ?>" data-slider-value="[<?php echo esc_attr($salary_min); ?>,<?php echo esc_attr($salary_max); ?>]" />
					<div class="margin-bottom-50"></div>
				</div>

			</div>
		<?php endif; ?>

		<?php if (get_option('workscout_enable_filter_rate')) : ?>
			<div class="widget widget_range_filter widget-rate-filter" <?php if ($rate_widget == "off") : echo ' style="display:none;" ';
																		endif; ?>>
				<h4 class="checkboxes" style="margin-bottom: 0;">
					<input type="checkbox" name="filter_by_rate_check" id="filter_by_rate" class="filter_by_check">
					<label for="filter_by_rate"><?php esc_html_e('Filter by Rate', 'workscout_core'); ?></label>
				</h4>
				<div class="widget_range_filter-inside">

					<?php
					$rate_min = workscout_get_min_meta('_rate_min');
					$rate_max = workscout_get_max_meta('_rate_max');

					// find step for slider between rate_min and rate_max
					$range = $rate_max - $rate_min;
					if ($range <= 1000) {
						$step = 1; // Set a small step for a narrow range
					} else if ($range <= 10000) {
						$step = 100; // Set a medium step for a moderate range
					} else {
						$step = 500; // Set a larger step for a wide range
					}
					?>
					<input class="range-slider" name="filter_by_rate" type="text" value="" data-slider-currency="<?php echo get_workscout_currency_symbol(); ?>" data-slider-min="<?php echo esc_attr($rate_min); ?>" data-slider-max="<?php echo esc_attr($rate_max); ?>" data-slider-step="<?php echo esc_attr($step); ?>" data-slider-value="[<?php echo esc_attr($rate_min); ?>,<?php echo esc_attr($rate_max); ?>]" />
				</div>
			</div>
		<?php endif; ?>

		<?php if (taxonomy_exists("job_listing_tag") && get_option('workscout_enable_job_tags_sidebar')) { ?>
			<div class="widget widget_range_filter widget-tag" <?php if ($job_tags_widget == "off") : echo ' style="display:none;" ';
																endif; ?>>
				<div class="filter_wide filter_by_tag">
					<h4><?php esc_html_e('Filtrar por palabra clave:', 'workscout_core') ?></h4>
					<span class="filter_by_tag_cloud"></span>
				</div>
			</div>
		<?php } ?>
		<div class="job_filters_links"></div>
	</form>
	<?php dynamic_sidebar('sidebar-jobs'); ?>
</div><!-- #secondary -->