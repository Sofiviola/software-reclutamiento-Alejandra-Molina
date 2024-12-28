<?php
$submission_limit           = get_option('workscout_freelancer_submission_limit');
$submit_task_form_page_id = get_option('workscout_freelancer_submit_task_form_page_id');
global $post;
?>

<!-- Row -->
<div class="row">

    <!-- Dashboard Box -->
    <div class="col-xl-12">
        <div class="dashboard-box dashboard-tasks-box margin-top-0">

            <!-- Headline -->
            <div class="headline">
                <h3><i class="icon-material-outline-assignment"></i><?php esc_html_e(' My Tasks', 'workscout-freelancer'); ?></h3>
                <div class="sort-by">
                    <form id="tasks-sort-by-form" action=" <?php the_permalink(); ?>" method="get">
                        <?php
                        $selected = isset($_REQUEST['sort-by']) ? $_REQUEST['sort-by'] : '';
                        ?>
                        <select name="sort-by" class="select2-single hide-tick tasks-sort-by">
                            <option <?php selected($selected, '') ?> value=""><?php esc_html_e('All', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'publish') ?>value="publish"><?php esc_html_e('Published', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'in_progress') ?>value="in_progress"><?php esc_html_e('In progress', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'completed') ?>value="completed"><?php esc_html_e('Completed', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'hidden') ?>value="hidden"><?php esc_html_e('Hidden', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'pending') ?>value="pending"><?php esc_html_e('Pending', 'workscout-freelancer'); ?></option>
                            <option <?php selected($selected, 'closed') ?>value="closed"><?php esc_html_e('Closed', 'workscout-freelancer'); ?></option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="content">
                <ul class="dashboard-box-list">
                    <?php if (!$tasks) : ?>
                        <li><?php esc_html_e('You do not have any active task listings.', 'workscout-freelancer'); ?></li>

                    <?php else : ?>
                        <?php foreach ($tasks as $task) :
                            $status = get_post_status($task);
                        ?>
                            <li>
                                <!-- Job Listing -->
                                <div class="item-listing width-adjustment">

                                    <!-- Job Listing Details -->
                                    <div class="item-listing-details">

                                        <!-- Details -->
                                        <div class="item-listing-description">
                                            <h3 class="item-listing-title"> <a href="<?php echo get_permalink($task->ID); ?>"><?php echo esc_html($task->post_title); ?></a> <span class="dashboard-status-button <?php switch (get_the_job_status_class($task)) {
                                                                                                                                                                                                                        case 'expired':
                                                                                                                                                                                                                            echo 'red';
                                                                                                                                                                                                                            break;
                                                                                                                                                                                                                        case 'completed':
                                                                                                                                                                                                                        case 'in_progress':
                                                                                                                                                                                                                            echo 'green';
                                                                                                                                                                                                                            break;
                                                                                                                                                                                                                        case 'publish':
                                                                                                                                                                                                                            echo 'green';
                                                                                                                                                                                                                            break;

                                                                                                                                                                                                                        default:
                                                                                                                                                                                                                            echo 'yellow';
                                                                                                                                                                                                                            break;
                                                                                                                                                                                                                    }; ?>"><?php the_task_status($task); ?></span></h3>

                                            <!-- Job Listing Footer -->
                                            <div class="item-listing-footer">
                                                <ul>
                                                    <?php

                                                    $deadline = workscout_get_bidding_deadline($task->ID);

                                                    if ($deadline) {

                                                        if (is_array($deadline)) :
                                                    ?>
                                                            <li><i class="icon-material-outline-access-time"></i>
                                                                <?php echo $deadline['days'];
                                                                echo $deadline['hours'] ?> <?php esc_html_e('left', 'workscout-freelancer'); ?> </li>
                                                        <?php else : ?>
                                                            <li><i class="icon-material-outline-access-time"></i><?php esc_html_e('Bidding has closed', 'workscout-freelancer'); ?></li>
                                                    <?php endif;
                                                    } ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task Details -->
                                <?php
                                $count = get_task_bidders_count($task->ID);
                                $task_range = get_workscout_task_range($task);
                                if (!empty($count) || !empty($task_range)) {
                                ?>
                                    <ul class="dashboard-task-info">
                                        <?php
                                        if ($count) { ?>
                                            <li><strong><?php echo get_task_bidders_count($task->ID) ?></strong><span><?php esc_html_e('Bids', 'workscout-freelancer'); ?></span></li>

                                            <li><strong><?php echo get_workscout_task_bidders_average($task->ID); ?></strong><span><?php esc_html_e('Avg. Bid', 'workscout-freelancer'); ?></span></li>
                                        <?php } ?>

                                        <?php
                                        if (!empty($task_range)) { ?>
                                            <li><strong><?php echo get_workscout_task_range($task); ?></strong><span><?php echo get_workscout_task_type($task); ?></span></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>

                                <!-- Buttons -->
                                <div class="buttons-to-right always-visible">
                                    <?php
                                    $actions = array();

                                    switch ($task->post_status) {
                                        case 'publish':

                                            $actions['edit'] = array(
                                                'label' => esc_html__('Edit', 'workscout-freelancer'),
                                                'nonce' => false
                                            );

                                            $actions['hide'] = array(
                                                'label' => esc_html__('Hide', 'workscout-freelancer'),
                                                'nonce' => true
                                            );
                                            break;
                                        case 'in_progress':
                                            $actions['completed'] = array(
                                                'label' => esc_html__('Set Task as Completed', 'workscout-freelancer'),
                                                'nonce' => true,
                                                'class' => 'task-dashboard-action-delete'
                                            );
                                            break;
                                        case 'hidden':

                                            $actions['edit'] = array(
                                                'label' => esc_html__('Edit', 'workscout-freelancer'),
                                                'nonce' => false
                                            );

                                            $actions['publish'] = array(
                                                'label' => esc_html__('Publish', 'workscout-freelancer'),
                                                'nonce' => true
                                            );
                                            break;
                                        case 'pending_payment':
                                        case 'pending':

                                            $actions['edit'] = array(
                                                'label' => __('Edit', 'workscout-freelancer'),
                                                'nonce' => false,
                                            );

                                            break;
                                        case 'expired':
                                            if (get_option('workscout_freelancer_submit_task_form_page_id')) {
                                                $actions['relist'] = array('label' => esc_html__('Relist', 'workscout-freelancer'), 'nonce' => true);
                                            }
                                            break;
                                    }

                                    if (!in_array($status, array('completed', 'in_progress'))) {
                                        $actions['delete'] = array(
                                            'label' => esc_html__('Delete', 'workscout-freelancer'),
                                            'nonce' => true,
                                            'class' => 'task-dashboard-action-delete'
                                        );
                                    }


                                    $actions = apply_filters('workscout_freelancer_my_task_actions', $actions, $task);


                                    ?>
                                    <?php
                                    global $post;
                                    if ($status == 'publish') {


                                        echo ($count = get_task_bidders_count($task->ID)) ? '<a href="' . add_query_arg(
                                            [
                                                'action' => 'show_bidders',
                                                'task_id' => $task->ID,
                                            ],
                                            get_permalink($post->ID)
                                        ) . '" class="button ripple-effect"><i class="icon-material-outline-supervisor-account"></i>' . esc_html__("Manage Bidders", 'workscout-freelancer') . '<span class="button-info">' . $count . '</span></a>' : '';
                                    }

                                    if ($status == "in_progress" || $status == "completed") {
                                        //$project_id = get_post_meta($task->ID, '_project_id', true);
                                        $project_id = false;
                                        if ($project_id) {
                                            $action_url = add_query_arg(array('action' => 'view-project', 'task_id' => $task->ID, 'project_id' => $project_id));
                                            ?>
                                            <a href="<?php echo $action_url; ?>" class="button dark ripple-effect" href="#"><i class="icon-material-outline-supervisor-account"></i><?php esc_html_e('View Project ', 'workscout-freelancer'); ?></a>
                                        <?php } ?>
                                        <a data-task="<?php echo  $task->ID; ?>" class="button dark task-dashboard-action-contact-bidder ripple-effect" href="#"><i class="icon-material-outline-supervisor-account"></i><?php esc_html_e('Order Summary', 'workscout-freelancer'); ?></a>
                                    <?php }
                                    ?>
                                    <?php
                                    $selected_bid = get_post_meta($task->ID, '_selected_bid_id', true);
                                    // get author of selected bid
                                    $selected_bid_author = get_post_field('post_author', $selected_bid);
                                    //var_dump($selected_bid_author);
                                    if ($status == 'completed') {
                                        //check if selected bid author has any comments
                                        $reviewed_id = get_the_author_meta('freelancer_profile', $selected_bid_author);
                                        if (empty($reviewed_id)) {
                                            // get ID of last post by user
                                            $reviewed_id = get_posts(array(
                                                'author' => $selected_bid_author,
                                                'posts_per_page' => 1,
                                                'post_type' => 'resume',
                                                'fields' => 'ids',
                                                'orderby' => 'date',
                                                'order' => 'DESC'
                                            ));
                                            $reviewed_id = $reviewed_id[0];
                                        }
                                        //check if post with id $reviewed_id has a comment made by current user
                                        $reviewed = get_comments(array(
                                            'post_id' => $reviewed_id,
                                            'user_id' => get_current_user_id(),
                                            'count' => true
                                        ));
                                        //var_dump($reviewed);
                                        if ($reviewed == 0) { ?>
                                            <a href="" data-task="<?php echo  $task->ID; ?>" class="button gray task-dashboard-action-review ripple-effect" href="#"><i class="icon-material-outline-rate-review"></i><?php esc_html_e('Rate Freelancer', 'workscout-freelancer'); ?></a>
                                        <?php } else { ?>
                                            <a href="" data-task="<?php echo  $task->ID; ?>" class="button gray task-dashboard-action-review ripple-effect" href="#"><i class="icon-material-outline-rate-review"></i><?php esc_html_e('Edit your rating', 'workscout-freelancer'); ?></a>

                                    <?php }
                                    }
                                    foreach ($actions as $action => $value) {
                                        $action_url = add_query_arg(array('action' => $action, 'task_id' => $task->ID));
                                        $class = isset($value['class']) ? $value['class'] : '';
                                        if ($value['nonce'])
                                            $action_url = wp_nonce_url($action_url, 'workscout_freelancer_my_task_actions');
                                        echo '<a  class=" ' . $class . ' button gray ripple-effect ico" title="' . $value['label'] . '" href="' . $action_url . '" data-tippy-placement="top" class="task-dashboard-action-' . $action . '">' . workscout_manage_action_icons($action) . '</a>';
                                    }
                                    ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

</div>
<!-- Row / End -->




<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Contact Freelancer', 'workscout-freelancer'); ?></h3>
    </div>

    <!-- Bidding -->
    <div class="bidding-widget">
        <!-- Headline -->

    </div>



</div>
<a style="display: none;" href="#small-dialog" class="contact-popup popup-with-zoom-anim button dark ripple-effect ico" title="<?php esc_html_e('Edit Bid', 'workscout-freelancer'); ?>" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
<!-- Edit Bid Popup / End -->


<!-- Send Direct Message Popup
================================================== -->
<!-- Reply to review popup -->
<div id="small-dialog-2" class="workscout-rate-popup zoom-anim-dialog mfp-hide small-dialog apply-popup ">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Rate Freelancer', 'workscout-freelancer'); ?></h3>
    </div>

    <div class="rate-form margin-top-0">
        <!-- Form -->
    </div>
    <div class="notification closeable success margin-top-20" style="display: none;"></div>

</div>

<a style="display: none;" href="#small-dialog-2" class="rate-popup popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> <?php esc_html_e('Send Message', 'workscout-freelancer'); ?></a>
<!-- Send Direct Message Popup / End -->

<?php get_job_manager_template('pagination.php', array('max_num_pages' => $max_num_pages)); ?>