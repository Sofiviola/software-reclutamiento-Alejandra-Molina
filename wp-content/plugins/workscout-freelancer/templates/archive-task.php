<?php

/**
 * The template for displaying tasks
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package hireo
 */


$template_loader = new WorkScout_Freelancer_Template_Loader;
$task_layout = Kirki::get_option('workscout', 'tasks_archive_layout');
wp_enqueue_script("workscout-freelancer-ajaxsearch");
$sidebar_site = Kirki::get_option('workscout', 'tasks_sidebar_layout');

switch ($task_layout) {
    case 'standard-list':
        $task_list_class = 'compact-list';
        $template = 'content-task';
        break;
    // case 'standard-grid':
    //     $task_list_class = 'regular-list';
    //     $template = 'content-task';
    //     break;
    case 'standard-grid':
        $task_list_class = 'tasks-grid-layout ';
        $template = 'content-task-grid';
        break;

    case 'full-page':
        $task_list_class = 'tasks-grid-layout ';
        $template = 'content-task-grid';
        break;

    default:
        $task_list_class = 'compact-list';
        $template = 'content-task';
        break;
}

($task_layout == 'full-page') ? get_header('split') : get_header();


if ($task_layout == 'full-page') {
    $template_loader->get_template_part('archive-task-full');
} else {

    if (!empty($header_image)) {
        $transparent_status = Kirki::get_option('workscout', 'pp_jobs_transparent_header');

        if ($transparent_status) { ?>
            <div id="titlebar" class="photo-bg single with-transparent-header <?php if ($map) echo " with-map"; ?>"" style=" background: url('<?php echo esc_url($header_image); ?>')">
            <?php } else { ?>
                <div id="titlebar" class="photo-bg single <?php if ($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
                <?php } ?>

            <?php } else { ?>
                <div id="titlebar" class="single ">
                <?php } ?>
                <div class="container">
                    <div class="sixteen columns">
                        <div class="ten columns">
                            <?php 
                            $hide_counter =  Kirki::get_option('workscout', 'pp_disable_jobs_counter', true);
                            $hide_counter = false;
                            //if it's archive page for taxonomy show how many items in this taxonomy
                            
                            if ($hide_counter) { ?>
                                <?php $count_tasks = wp_count_posts('task', 'readable');
                                if (is_tax()) {
                                    $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                                    
                                    $count_tasks->publish = $term->count;
                                    
                                }?>
                                <span class="showing_jobs" style="display: none">
                                    <?php esc_html_e('Browse Tasks', 'workscout') ?>
                                </span>
                                <h2><?php
                                    printf(_n('We have <em class="count_jobs">%s</em> <em class="tasks_text">tasks</em> for you', 'We have <em class="count_jobs">%s</em> <em class="tasks_text">tasks</em> for you', $count_tasks->publish, 'workscout-freelancer'), $count_tasks->publish); ?>

                                </h2>
                            <?php } else { ?>
                             
                                    <h1><?php esc_html_e('Tasks', 'workscout'); ?></h1>
                               
                            <?php } ?>

                        </div>

                        <?php
                        $call_to_action = Kirki::get_option('workscout', 'pp_call_to_action_jobs', 'job');
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


                <div class="margin-top-90"></div>
                <div class="container">
                    <div class="row">


                        <?php
                        if ($sidebar_site == 'left') {
                            $classes = 'col-xl-9 col-lg-9 content-left-offset';
                        } else {
                            $classes = 'col-xl-9 col-lg-9 content-right-offset';
                        } ?>

                        <?php if ($sidebar_site == 'left') { ?>
                            <div class="col-xl-3 col-lg-3">
                                <div class="sidebar-container">
                                    <?php echo workscout_generate_tasks_sidebar(); ?>
                                    <div class="clearfix"></div>

                                </div>
                            </div>
                        <?php } ?>
                        <div class="<?php echo $classes; ?>">

                            <div class="tasks-list-container <?php echo $task_list_class; ?> margin-top-35">
                                <?php /* Start the Loop */
                                if (have_posts()) : 
                                    /* Start the Loop */
                                    while (have_posts()) : the_post();

                                        $template_loader->get_template_part($template);


                                    endwhile;
                                else :

                                    get_template_part('template-parts/content', 'none');

                                endif;
                                ?>
                            </div>
                            <!-- Pagination -->
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
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
                                                global $wp_query;
                                                $pages = $wp_query->max_num_pages;
                                                
                                                echo workscout_freelancer_pagination($pages, 1);
                                            } ?>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <!-- Pagination / End -->

                        </div>
                        <?php if ($sidebar_site == 'right') { ?>
                            <div class="col-xl-3 col-lg-3">
                                <div class="sidebar-container">
                                    <?php echo workscout_generate_tasks_sidebar(); ?>
                                    <div class="clearfix"></div>

                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php
            get_footer();
        }
