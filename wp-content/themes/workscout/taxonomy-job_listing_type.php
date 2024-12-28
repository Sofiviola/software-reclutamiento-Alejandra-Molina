<?php

/**
 * Job Category
 *
 * @package WorkScout
 * @since WorkScout 1.0
 */

$taxonomy = get_taxonomy(get_queried_object()->taxonomy);


$header_old = Kirki::get_option('workscout', 'pp_old_header');
$header_type = (Kirki::get_option('workscout', 'pp_old_header') == true) ? 'old' : '';
$header_type = apply_filters('workscout_header_type', $header_type);

get_header($header_type);
$cat_desc = Kirki::get_option('workscout', 'pp_taxonomies_description');


$layout = Kirki::get_option('workscout', 'jobs_list_layout');


if ($layout == "split_job_view") { ?>


	<!-- Page Content
================================================== -->
	<div class="full-page-container full-page-jobs">

		<div class="full-page-sidebar hidden-sidebar">
			<div class="full-page-sidebar-inner">
				<div class="sidebar-container">
					<div class="filter-button-container">
						<button class="enable-filters-button">
							<i class="enable-filters-button-icon"></i>
							<span class="show-text"><?php esc_html_e('Show Filters', 'workscout') ?></span>
							<span class="hide-text"><?php esc_html_e('Hide Filters', 'workscout') ?></span>
						</button>
					</div>
					<?php get_sidebar('jobs'); ?>
				</div>
			</div>
		</div>
		<!-- Full Page Sidebar / End -->


		<!-- Full Page Content -->
		<div class="full-page-content-container">

			<div class="sticky-filter-button">
				<div class="filter-button-container">
					<button class="enable-filters-button">
						<i class="enable-filters-button-icon"></i>
						<span class="show-text"><?php esc_html_e('Show Filters', 'workscout') ?></span>
						<span class="hide-text"><?php esc_html_e('Hide Filters', 'workscout') ?></span>
					</button>
				</div>
			</div>
			<div class="full-page-content-inner">

				<!-- Filters Container -->
				<div class="filters-container">

					<!-- Page Title -->
					<h3 class="filters-headline">
						<?php $count_jobs = wp_count_posts('job_listing', 'readable');
						printf(_n('We have <strong class="count_jobs">%s</strong> <strong class="job_text">job offer</strong> that could be the right fit!', 'We have <strong class="count_jobs">%s</strong> <strong class="job_text">job offers</strong> that could be the right fit!', $count_jobs->publish, 'workscout'), $count_jobs->publish); ?>
					</h3>


					<!-- Enable Filters Button -->
					<div class="filter-button-container">
						<button class="enable-filters-button">
							<i class="enable-filters-button-icon"></i>
							<span class="show-text"><?php esc_html_e('Show Filters', 'workscout') ?></span>
							<span class="hide-text"><?php esc_html_e('Hide Filters', 'workscout') ?></span>
						</button>
						<!-- 					<div class="filter-button-tooltip">Click to expand sidebar with filters!</div> -->
					</div>

				</div>
				<!-- Filters Container / End -->


				<!-- Full Page Job Listings Container -->
				<div class="full-page-job-listings-container">

					<?php
					$order = Kirki::get_option('workscout', 'pp_jobs_order', 'DESC');
					$orderby = Kirki::get_option('workscout', 'pp_jobs_orderby', 'date');
					$per_page = Kirki::get_option('workscout', 'pp_jobs_per_page', 10);

					echo do_shortcode('[jobs orderby="' . $orderby . '" order="' . $order . '"  job_types=' . get_query_var('job_listing_type') . ' show_filters="false"]'); ?>

				</div>
				<!-- Full Page Job Listings Container / End -->

			</div>
		</div>
		<!-- Full Page Content / End -->


		<!-- Full Page Map -->
		<div class="full-page-job-container loading">
			<!-- Preloader -->
			<div class="listings-loader">
				<div class="spinner">
					<div class="double-bounce1"></div>
					<div class="double-bounce2"></div>
				</div>
			</div>

		</div>
		<!-- Full Page Map / End -->

	</div>
	<?php

	get_footer('empty'); ?>

<?php } else if ($layout == 'split') { ?>


	<!-- Page Content
================================================== -->
	<div class="full-page-container with-map">

		<!-- Full Page Content -->
		<div class="full-page-content-container" data-simplebar>
			<div class="full-page-content-inner">

				<?php get_template_part('template-parts/jobs-split-filters'); ?>

				<?php if ($cat_desc) : ?>
					<div class="job-category-description">
						<h1><?php the_archive_title(); ?></h1>
						<?php echo category_description(); ?>
					</div>
				<?php endif; ?>

				<div class="listings-container">


					<?php echo do_shortcode('[jobs job_types=' . get_query_var('job_listing_type') . ' show_filters="false"]'); ?>


				</div>


				<?php get_template_part('template-parts/split-footer'); ?>
				<!-- Footer / End -->

			</div>
		</div>
		<!-- Full Page Content / End -->


		<!-- Full Page Map -->
		<div class="full-page-map-container">
			<?php $all_map = Kirki::get_option('workscout', 'pp_enable_all_jobs_map', 0);
			if ($all_map) {
				echo do_shortcode('[workscout-map type="job_listing" class="jobs_page"]');
			} else { ?>
				<div id="search_map" data-map-scroll="true" class="jobs_map"></div>
			<?php } ?>
		</div>
		<!-- Full Page Map / End -->

	</div>

	</div>
	<?php

	get_footer('empty'); ?>


<?php } else { ?>


	<?php
	$t_id = get_queried_object()->term_id;
	$term_meta = get_option("taxonomy_$t_id");
	$map =  Kirki::get_option('workscout', 'pp_enable_jobs_map', 0);

	$header_image = isset($term_meta['upload_header']) ? $term_meta['upload_header'] : '';
	if (!empty($header_image)) { ?>
		<div id="titlebar" class="photo-bg single <?php if ($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
		<?php } else { ?>
			<div id="titlebar" class="single <?php if ($map) echo " with-map"; ?>">
			<?php } ?>
			<div class="container">
				<div class="sixteen columns">
					<h1><?php single_term_title(); ?></h1>
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
			$layout = Kirki::get_option('workscout', 'pp_blog_layout');
			if (empty($layout)) {
				$layout = 'right-sidebar';
			}

			wp_dequeue_script('wp-job-manager-ajax-filters');
			wp_enqueue_script('workscout-wp-job-manager-ajax-filters');

			if ($map) {
				$all_map = Kirki::get_option('workscout', 'pp_enable_all_jobs_map', 0);
				if ($all_map) {
					echo do_shortcode('[workscout-map type="job_listing" class="jobs_page"]');
				} else { ?>
					<div id="search_map" data-map-scroll="<?php echo Kirki::get_option('workscout', 'pp_maps_scroll_zoom', 1) == 1 ? 'true' : 'false'; ?>" class="jobs_map"></div>
			<?php
				}
			} ?>

			<div class="container  wpjm-container <?php echo esc_attr($layout); ?>">


				<?php if ($cat_desc) : ?>
					<div class="job-category-description sixteen columns margin-bottom-50">
						<h1><?php the_archive_title(); ?></h1>
						<?php echo category_description(); ?>
					</div>
				<?php endif; ?>

				<?php get_sidebar('jobs'); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('eleven columns'); ?>>
					<div class="padding-right">
						<?php
						$search_in_sb =  Kirki::get_option('workscout', 'pp_jobs_search_in_sb');
						if (!$search_in_sb) {
							if (!empty($_GET['search_keywords'])) {
								$keywords = sanitize_text_field($_GET['search_keywords']);
							} else {
								$keywords = '';
							}
						?>
							<?php if (apply_filters('workscout_jobs_show_search_keywords', true)) : ?>
								<form class="list-search" method="GET" action="<?php echo get_permalink(); ?>">
									<div class="search_keywords">
										<button><i class="fa fa-search"></i></button>
										<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e('job title, keywords or company name', 'workscout'); ?>" value="<?php echo esc_attr($keywords); ?>" />
										<div class="clearfix"></div>
									</div>
								</form>
							<?php endif; ?>
						<?php } ?>
						<?php 
						$order = Kirki::get_option('workscout', 'pp_jobs_order', 'DESC');
						$orderby = Kirki::get_option('workscout', 'pp_jobs_orderby', 'date');
						$per_page = Kirki::get_option('workscout', 'pp_jobs_per_page', 10);
						echo do_shortcode('[jobs  orderby="' . $orderby . '" order="' . $order . '"   job_types=' . get_query_var('job_listing_type') . ' show_filters="false"]'); ?>


					</div>
				</article>

			</div>
			<?php
			get_footer();
			?>
		<?php } ?>