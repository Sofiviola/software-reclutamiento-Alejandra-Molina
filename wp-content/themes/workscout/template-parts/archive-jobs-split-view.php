<?php 

wp_dequeue_script('wp-job-manager-ajax-filters' );
wp_enqueue_script( 'workscout-wp-job-manager-ajax-filters' );

?>


<!-- Page Content
================================================== -->
<div class="full-page-container full-page-jobs">

	<div class="full-page-sidebar hidden-sidebar">
		<div class="full-page-sidebar-inner">
			<div class="sidebar-container">
				<div class="filter-button-container">
					<button class="enable-filters-button">
						<i class="enable-filters-button-icon"></i>
						<span class="show-text"><?php esc_html_e('Show Filters','workscout') ?></span>
						<span class="hide-text"><?php esc_html_e('Hide Filters','workscout') ?></span>
					</button>
				</div>
				<?php  get_sidebar('jobs');?>
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
						<span class="show-text"><?php esc_html_e('Show Filters','workscout') ?></span>
						<span class="hide-text"><?php esc_html_e('Hide Filters','workscout') ?></span>
					</button>
				</div>
			</div>
		<div class="full-page-content-inner">

			<!-- Filters Container -->
			<div class="filters-container">

				<!-- Page Title -->
				<h3 class="filters-headline">
					<?php $count_jobs = wp_count_posts( 'job_listing', 'readable' ); 
 					printf(_n(  'We have <strong class="count_jobs">%s</strong> <strong class="job_text">job offer</strong> that could be the right fit!', 'We have <strong class="count_jobs">%s</strong> <strong class="job_text">job offers</strong> that could be the right fit!' , $count_jobs->publish, 'workscout' ), $count_jobs->publish); ?>
 				</h3>
				
				
				<!-- Enable Filters Button -->
				<div class="filter-button-container">
					<button class="enable-filters-button">
						<i class="enable-filters-button-icon"></i>
						<span class="show-text"><?php esc_html_e('Show Filters','workscout') ?></span>
						<span class="hide-text"><?php esc_html_e('Hide Filters','workscout') ?></span>
					</button>

				</div>

			</div>
			<!-- Filters Container / End -->


			<!-- Full Page Job Listings Container -->
			<div class="full-page-job-listings-container">

				<?php
					$order = Kirki::get_option( 'workscout', 'pp_jobs_order', 'DESC' ); 
					$orderby = Kirki::get_option( 'workscout', 'pp_jobs_orderby', 'date' ); 
					$per_page = Kirki::get_option( 'workscout', 'pp_jobs_per_page', 10 ); 
				
					echo do_shortcode('[jobs orderby="'.$orderby.'" order="'.$order.'" per_page="'.$per_page.'"  show_filters="false"]'); ?>

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