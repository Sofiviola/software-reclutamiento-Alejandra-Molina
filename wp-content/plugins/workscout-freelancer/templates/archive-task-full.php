<?php

/**
 * The template for displaying tasks
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package hireo
 */
$top_layout = get_option('wf_task_layout');
wp_enqueue_script("workscout-freelancer-ajaxsearch");
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
                <button class="button ripple-effect"><?php esc_html_e('Search', 'workscout-freelancer')?></button>
            </div>
            <!-- Search Button / End-->

        </div>
    </div>
    <!-- Full Page Sidebar / End -->
    <!-- Full Page Content -->
    <div class="full-page-content-container-v2" data-simplebar>
        <div class="full-page-content-inner-v2">

            <h3 class="page-title"><?php esc_html_e('Search Results', 'workscout-freelancer'); ?></h3>


            <div class="tasks-list-container tasks-grid-layout margin-top-35">
                <?php /* Start the Loop */

                while (have_posts()) : the_post();

                    $template_loader->get_template_part('content-task-grid');


                endwhile;

                ?>
            </div>
            <!-- Pagination -->
            <div class="clearfix"></div>

            <?php $ajax_browsing = get_option('task_ajax_browsing'); ?>

            <!-- Pagination -->
            <div class="pagination-container <?php if ($ajax_browsing == '1') {
                                                    echo 'ajax-search';
                                                } ?> ">
                <nav class="pagination">
                    <?php

                    if ($ajax_browsing == '1') {
                        global $wp_query;
                        $pages = $wp_query->max_num_pages;
                        echo workscout_freelancer_ajax_pagination($pages, 1);
                    } else
                    if (function_exists('wp_pagenavi')) {
                        wp_pagenavi(array(
                            'next_text' => '<i class="fa fa-chevron-right"></i>',
                            'prev_text' => '<i class="fa fa-chevron-left"></i>',
                            'use_pagenavi_css' => false,
                        ));
                    } else {
                        the_posts_navigation();
                    } ?>
                </nav>

            </div>
            <!-- Pagination / End -->

            <div class="clearfix"></div>
            <!-- Pagination / End -->

            <!-- Footer -->
            <?php get_template_part('template-parts/split-footer'); ?>
            <!-- Footer / End -->

        </div>
    </div>
    <!-- Full Page Content / End -->

</div>


</div>

<?php wp_footer(); ?>

</body>

</html>