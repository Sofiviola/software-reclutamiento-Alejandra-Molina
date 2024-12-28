<?php

/**
 * Template Name: Page with Tasks Full Page layout
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WorkScout
 */
get_header('split');

$template_loader = new WorkScout_Freelancer_Template_Loader;

?>
<!-- Page Content
================================================== -->
<div class="full-page-container-v2">

	<div class="full-page-sidebar-v2">
		<div class="full-page-sidebar-inner-v2" data-simplebar>
			<div class="sidebar-container-v2">
				<?php echo workscout_generate_tasks_sidebar(); ?>
			</div>
			<!-- Sidebar Container / End -->

			<!-- Search Button -->
			<div class="sidebar-search-button-container">
				<button class="button ripple-effect">Search</button>
			</div>
			<!-- Search Button / End-->

		</div>
	</div>
	<!-- Full Page Sidebar / End -->

	<!-- Full Page Content -->
	<div class="full-page-content-container-v2" data-simplebar>
		<div class="full-page-content-inner-v2">

			<h3 class="page-title"><?php esc_html_e('Search Results', 'workscout'); ?></h3>



			<?php /* Start the Loop */

			while (have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>

			<div class="clearfix"></div>
			<!-- Pagination / End -->

			<!-- Footer -->

			<!-- Footer / End -->
			<?php get_template_part('template-parts/split-footer'); ?>
		</div>
	</div>
	<!-- Full Page Content / End -->

</div>


</div>

<?php wp_footer(); ?>

</body>

</html>