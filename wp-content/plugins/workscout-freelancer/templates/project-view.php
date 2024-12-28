<?php
$project_id = $project->ID;
$task_id = get_post_meta($project_id, '_task_id', true);
$freelancer = get_post_meta($project_id, '_freelancer_id', true);
$employer = get_post_meta($project_id, '_employer_id', true);
$_selected_bid_id = get_post_meta($project_id, '_selected_bid_id', true);
$bid = get_post($_selected_bid_id);

$bid_meta = get_post_meta($_selected_bid_id);
$bid_author = $bid->post_author;
$bid_proposal = $bid->post_content;
$bid_data = array(
    'budget'    => get_post_meta($_selected_bid_id, '_budget', true),
    'time'      => get_post_meta($_selected_bid_id, '_time', true),
    'proposal'  => $bid_proposal,

);
$freelancer_project = WorkScout_Freelancer_Project::instance();
// check if current author is freelancer or employer, if not return
if (get_current_user_id() != $freelancer && get_current_user_id() != $employer) {
    return;
}
?>
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-box dashboard-tasks-box margin-top-0">
            <div class="headline">
                <h3><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Project', 'workscout-freelancer'); ?>: <?php echo get_the_title($project_id) ?> </h3>
            </div>

            <div class="content">
                <div class="project-view-content">


                    <h4><?php esc_html_e('Project details:', 'listeo_core'); ?></h4>
                    <div class="project-view-details">


                        <div class="bidding-detail">
                            <strong><?php esc_html_e('Posted: ', 'workscout-freelancer'); ?></strong>
                            <?php echo get_the_date('F j, Y', $project_id); ?>
                        </div>
                        <div class="bidding-detail">
                            <strong><?php esc_html_e('Status: ', 'workscout-freelancer'); ?></strong>
                            <?php the_task_status($task_id); ?>
                        </div>
                        <div class="bidding-detail">
                            <strong><?php esc_html_e('Budget: ', 'workscout-freelancer'); ?></strong>
                            <?php echo get_workscout_currency_symbol(); ?><?php echo $bid_data['budget']; ?>
                        </div>
                        <div class="bidding-detail">
                            <strong><?php esc_html_e('Time: ', 'workscout-freelancer'); ?></strong>
                            <?php echo $bid_data['time']; ?> days
                        </div>
                        <div class="bidding-detail">
                            <strong><?php esc_html_e('Proposal: ', 'workscout-freelancer'); ?></strong>
                            <div class="bid-proposal-text"><?php echo $bid_data['proposal']; ?></div>
                        </div>
                    </div>

                    <h4><?php esc_html_e('Project Description:', 'workscout-freelancer'); ?></h4>
                    <div class="project-view-description">
                        <?php echo apply_filters('the_content', get_post_field('post_content', $project_id)); ?>


                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-box dashboard-tasks-box">
            <div class="headline">
                <h3><i class="icon-material-outline-business-center"></i> Discussion </h3>
            </div>
            <div class="content">
                <div class="project-discussion-content">
                    <div class="notification notice margin-bottom-25 ">
                        This session is private, comments are only visible to you and your expert
                    </div>

                    <?php

                    $comments = get_comments(array(
                        'post_id' => $project_id,
                        'status' => 'approve'
                    ));

                    if ($comments) {

                        echo '<ul class="project-comment-list">';
                        wp_list_comments(array(
                            'per_page' => 10, // Number of comments to show per page

                            'callback' => 'workscout_project_comment',
                        ), $comments);
                        echo '</ul>';
                    } else {
                        echo '<p>No comments yet.</p>';
                    }

                    ?>
                    <form method="post" action="" enctype="multipart/form-data" class="project-comment-form">
                        <?php wp_nonce_field('project_comment_action', 'project_comment_nonce'); ?>
                        <input type="hidden" name="project_id" value="<?php echo esc_attr($project_id); ?>">

                        <div class="form-group">
                            <textarea name="comment_content" required class="with-border" placeholder="<?php esc_html_e('Type your message here...', 'workscout-freelancer'); ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="milestone-checkbox">
                                <input type="checkbox" name="is_milestone">
                                <span class="checkbox-icon"></span>
                                <?php esc_html_e(' Mark as milestone', 'workscout-freelancer'); ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Attach Files:</label>
                            <div class="uploadButton margin-top-0">
                                <input class="uploadButton-input" type="file" name="comment_files[]" multiple id="upload" accept="image/*, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/zip" />
                                <label class="uploadButton-button ripple-effect" for="upload">Upload Files</label>
                                <span class="uploadButton-file-name">Maximum file size: 10 MB</span>
                            </div>
                        </div>

                        <button type="submit" name="submit_project_comment" class="button ripple-effect"><?php esc_html_e('Submit Comment', 'workscout-freelancer'); ?></button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="col-lg-4">

        <?php if (get_current_user_id() == $employer) :

            $user_info = get_userdata($freelancer); ?>

            <div class="dashboard-box dashboard-tasks-box margin-top-0">
                <div class="headline">
                    <h3><i class="icon-material-outline-business-center"></i>Your Freelancer:

                        <div class="freelancer-overview">

                            <div class="freelancer-overview-inner">

                                <?php $user_profile_id = get_user_meta($freelancer, 'freelancer_profile', true);
                                if ($user_profile_id) {
                                    $avatar = "<img src=" . get_the_candidate_photo($user_profile_id) . " class='avatar avatar-32 photo'/>";
                                    $username = get_the_title($user_profile_id);
                                } else {

                                    $avatar = get_avatar($freelancer, 32);
                                    $username =  workscout_get_users_name($freelancer);
                                }
                                ?>
                                <!-- Avatar -->
                                <div class="freelancer-avatar">
                                    <?php if (workscout_is_user_verified($freelancer)) { ?><div class="verified-badge"></div><?php } ?>
                                    <?php echo $avatar; ?>
                                </div>

                                <!-- Name -->
                                <div class="freelancer-name">
                                    <h4><a href="<?php the_permalink(); ?>"><?php echo $username; ?>
                                            <?php
                                            if ($user_profile_id) {
                                                $country = get_post_meta($user_profile_id, '_country', true);

                                                if ($country) {
                                                    $countries = workscoutGetCountries();
                                            ?>
                                                    <img class=" flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" alt="" title="<?php echo $countries[$country]; ?>" data-tippy-placement="top">
                                            <?php }
                                            } ?>

                                        </a>

                                    </h4>
                                    <?php the_candidate_title('<span>', '</span> ', true, $freelancer); ?>

                                    <?php if (class_exists('WorkScout_Freelancer')) { ?>
                                        <?php $rating_value = get_post_meta($user_profile_id, 'workscout-avg-rating', true);
                                        if ($rating_value) {  ?>
                                            <div class="freelancer-rating">
                                                <div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout'); ?></div>
                                    <?php }
                                    } ?>
                                </div>



                            </div>
                        </div>
                </div>

                <div class="content">
                    <div class="project-view-content">

                        <div class="item-listing-footer">
                            <ul>
                                <li><a href="mailto:<?php echo $user_info->user_email; ?>"><i class="icon-feather-mail"></i>
                                        <?php echo $user_info->user_email; ?>
                                    </a></li>
                                <?php if ($user_info->phone) { ?>
                                    <li><i class="icon-feather-phone"></i> <?php echo $user_info->phone; ?></li>
                                <?php } ?>
                                <?php
                                $country = get_post_meta($user_profile_id, '_country', true);

                                if ($country) {
                                    $countries = workscoutGetCountries();
                                ?>
                                    <li class="dashboard-resume-flag"><img class="flag" src="<?php echo WORKSCOUT_FREELANCER_PLUGIN_URL; ?>/assets/images/flags/<?php echo strtolower($country); ?>.svg" alt=""> <?php echo $countries[$country]; ?></li>
                                <?php } ?>
                            </ul>
                        </div>


                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="dashboard-box dashboard-tasks-box margin-top-0">
                <div class="headline">
                    <h3><i class="icon-material-outline-business-center"></i> Project author:

                        <?php echo get_avatar(get_the_author_meta('ID', $employer), 50); ?>
                        <?php echo get_the_author_meta('display_name', $employer); ?> </h3>
                </div>

                <div class="content">
                    <div class="project-view-content">

                        <div class="employer-detail:"">
                        <strong><?php esc_html_e('Email: ', 'workscout-freelancer'); ?></strong>
                        <?php echo get_the_author_meta('user_email', $employer); ?>
                    </div>
                    <?php $location = get_the_author_meta('location', $employer);
                    if ($location) { ?>
                    <div class=" employer-detail">
                            <strong><?php esc_html_e('Location: ', 'workscout-freelancer'); ?></strong>
                            <?php echo get_the_author_meta('location', $employer); ?>
                        </div>
                    <?php } ?>
                    <div class="employer-detail">
                        <strong><?php esc_html_e('Profile: ', 'workscout-freelancer'); ?></strong>
                        <a href="<?php echo get_author_posts_url($employer); ?>"><?php esc_html_e('View Profile', 'listeo_core'); ?></a>
                    </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php
        // Milestones box
        $milestones = get_post_meta($project_id, '_milestones', true);
        ?>
        <div class="dashboard-box dashboard-tasks-box margin-top-20">
            <div class="headline">
                <h3><i class="icon-material-outline-business-center"></i> Milestones</h3>


            </div>
            <div class="content">

                <div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">

                    <div class="small-dialog-header">
                        <h3><?php esc_html_e('Add New Milestone', 'workscout-freelancer'); ?></h3>
                    </div>

                    <!-- Bidding -->
                    <div class="bidding-widget">
                        <form id="milestone-form" data-project-budget="<?php echo $bid_data['budget'];?>" class="milestone-form">
                            <?php
                          
                            $project_value = get_post_meta($project_id, 'project_value', true);
                            $remaining_percentage = 100 - $freelancer_project->get_total_milestone_percentage($project_id);
                            ?>

                            <?php wp_nonce_field('workscout_milestone_nonce', 'milestone_nonce'); ?>
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" id="milestone-title" name="milestone_title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea id="milestone-description" name="milestone_description" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Percentage of Project Value</label>
                                <div class="percentage-input-wrapper">
                                    <input type="number"
                                        id="milestone-percentage"
                                        name="percentage"
                                        class="form-control"
                                        step="0.1"
                                        min="0.1"
                                        max="<?php echo esc_attr($remaining_percentage); ?>"
                                        required>
                                    <span class="percentage-symbol">%</span>
                                </div>
                                <div class="amount-preview">
                                    Amount: $<span id="amount-preview">0.00</span>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label>Due Date</label>
                                <input type="date" id="milestone-due-date" name="milestone_due_date" class="form-control" required>
                            </div> -->
                            <button type="submit" class="button">Save Milestone</button>
                        </form>

                    </div>



                </div>





                <!-- Milestones List Template -->
                <div class="milestones-list">
                    <?php
                    $milestones = $freelancer_project->get_milestones($project_id);
                    foreach ($milestones as $milestone):
                    ?>
                        <div class="milestone-item" data-id="<?php echo esc_attr($milestone['id']); ?>">
                            <h4><?php echo esc_html($milestone['title']); ?><?php echo $freelancer_project->get_status_badge($milestone['status']); ?></h4>
                            <p><?php echo wp_kses_post($milestone['description']); ?></p>
                            <div class="milestone-meta">
                                <span class="amount">$<?php echo number_format($milestone['amount'], 2); ?></span>
                                <span class="due-date">Due: <?php echo esc_html($milestone['due_date']); ?></span>

                            </div>

                            <div class="milestone-approvals">
                                <?php
                                $user_type = $freelancer_project->get_user_type($project_id);

                                $can_approve = !($user_type === 'client' ? $milestone['client_approval'] : $milestone['freelancer_approval']);

                                ?>

                                <div class="approval-status">
                                    <span class="client-approval <?php echo $milestone['client_approval'] ? 'approved' : ''; ?>">
                                        Client: <?php echo $milestone['client_approval'] ? '✓ Approved' : 'Pending'; ?>
                                    </span>
                                    <span class="freelancer-approval <?php echo $milestone['freelancer_approval'] ? 'approved' : ''; ?>">
                                        Freelancer: <?php echo $milestone['freelancer_approval'] ? '✓ Approved' : 'Pending'; ?>
                                    </span>
                                </div>

                                <?php if ($milestone['status'] === 'pending' && $can_approve): ?>
                                    <div class="milestone-actions">
                                        <button class="approve-milestone button" data-id="<?php echo esc_attr($milestone['id']); ?>">
                                            Approve Milestone
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php if ($milestone['status'] === 'approved'): ?>
                                    <div class="milestone-payment">
                                        <?php echo $freelancer_project->get_milestone_payment_link($milestone); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="milestones-action">
                    <a href="#small-dialog" class="contact-popup popup-with-zoom-anim ripple-effect ico" title="<?php esc_html_e('Add Milestone', 'workscout-freelancer'); ?>" data-tippy-placement="top"><i class="icon-feather-plus-circle"></i> <?php esc_html_e('Submit Milestone', 'listeo_core'); ?></a>

                </div>
            </div>
        </div>


        <?php $attachments = get_post_meta($task_id, '_task_file', true);

        if ($attachments) { ?>
            <div class="dashboard-box dashboard-task-files-box margin-top-20">
                <div class="headline">
                    <h3><i class="icon-material-outline-business-center"></i><?php esc_html_e(' Task Files', 'listeo_core'); ?></h3>
                </div>
                <div class="content project-task-files">

                    <?php
                    //$attachments     = get_posts('post_parent=' . $post->ID . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1');


                    foreach ((array) $attachments as $attachment_id => $attachment_url) {


                        //get the attachment url
                        if (!$attachment_url) {
                            $attachment_url = wp_get_attachment_url($attachment_id);
                        }


                        if (!$attachment_url) {
                            //skip if no url
                            continue;
                        }

                        //get the attachment filename
                        $attachment_title = get_the_title($attachment_id);
                        if (!$attachment_title) {
                        }
                        $attachment_title = basename($attachment_url);
                        //$attachment_title = get_the_title($id);
                        //get the attachment file type
                        $attachment_filetype = wp_check_filetype($attachment_url);

                    ?>
                        <a href="<?php echo $attachment_url; ?>" class="attachment-box ripple-effect"><span><?php echo $attachment_title; ?></span><i><?php echo $attachment_filetype['ext']; ?></i></a>

                    <?php } ?>

                </div>
            </div>
        <?php }

        // get attachments from all the comments and display them
        $project_files = $freelancer_project->get_project_files($project_id);

        if (!empty($project_files)) { ?>
            <div class="dashboard-box dashboard-project-files-box margin-top-20">
                <div class="headline">
                    <h3><i class="icon-material-outline-business-center"></i> Project Files</h3>
                </div>
                <div class="content project-files">
                    <?php foreach ($project_files as $file) { ?>


                        <a href=" <?php echo esc_url($file['url']); ?>" target="_blank" class="attachment-box ripple-effect">

                            <span><?php echo esc_html($file['name']); ?></span><i> <?php
                                                                                    echo esc_html($file['type']) . ' | ' .
                                                                                        esc_html($file['size']) . ' | ' .
                                                                                        'Uploaded by ' . esc_html($file['comment_author']) . ' on ' .
                                                                                        date('M j, Y', strtotime($file['comment_date']));
                                                                                    ?></i>
                        </a>

                </div>

            <?php } ?>
            </div>
    </div>
<?php } ?>
</div>
</div>