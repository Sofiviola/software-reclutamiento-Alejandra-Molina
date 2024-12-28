<?php

/**
 * Lists the job applications for a particular job listing.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-applications/job-applications.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Applications
 * @category    Template
 * @version     1.7.1
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($_REQUEST['task_id'])) {
    return;
}
if (isset($_REQUEST['task_id'])) {
    $task_id = absint($_REQUEST['task_id']);
    $task    = get_post($task_id);
}
$currency_position =  get_option('workscout_currency_position', 'before');

?>


<!-- Row -->
<div class="row">

    <!-- Dashboard Box -->
    <div class="col-xl-12">
        <div class="dashboard-box dashboard-tasks-box margin-top-0">

            <!-- Headline -->
            <div class="headline">
                <h3><i class="icon-material-outline-supervisor-account"></i> <?php $count = count($bids);
                                                                                printf(_n('%s Bidder', '%s Bidders', $count, 'workscout-freelancer'), number_format_i18n($count)); ?> </h3>
                <div class="sort-by">
                    <select class="select2-single hide-tick">
                        <option><?php esc_html_e('Highest First', 'workscout-freelancer'); ?></option>
                        <option><?php esc_html_e('Lowest First', 'workscout-freelancer'); ?></option>
                        <option><?php esc_html_e('Fastest First', 'workscout-freelancer'); ?></option>
                    </select>
                </div>
            </div>

            <div class="content">
                <ul class="dashboard-box-list">
                    <?php foreach ($bids as $bid) :
                        //get post author avatar
                        $post = get_post($bid->ID);

                        $author_id = $bid->post_author;

                        //check if user has active freelance profile
                        $user_profile_id = get_user_meta($author_id, 'freelancer_profile', true);

                        // get wordpress avatar 
                    ?>
                        <li>
                            <!-- Job Listing -->
                            <div class="item-listing">
                                <?php
                                $user_info = get_userdata($author_id);
                                if ($user_profile_id) {
                                    $avatar = "<img src=" . get_the_candidate_photo($user_profile_id) . " class='avatar avatar-32 photo'/>";
                                    $username = get_the_title($user_profile_id);
                                } else {

                                    $avatar = get_avatar($bid->post_author, 32);
                                    $username =  workscout_get_users_name($author_id);
                                }
                                ?>
                                <!-- Job Listing Details -->
                                <div class="item-listing-details">

                                    <a href="#" class="item-listing-company-logo">
                                        <?php if (workscout_is_user_verified($bid->post_author)) { ?> <div class="verified-badge"></div><?php } ?>
                                        <?php echo $avatar; ?>

                                    </a>
                                    <!-- Details -->
                                    <div class="item-listing-description">
                                        <h3 class="item-listing-title">
                                            <a href="#"><?php
                                                        //get user display name

                                                        echo  $username;
                                                        ?></a>


                                        </h3>
                                        <?php
                                        if ($user_profile_id) { ?>
                                            <?php $rating_value = get_post_meta($user_profile_id, 'workscout-avg-rating', true);
                                            if ($rating_value) {  ?>
                                                <div class="freelancer-rating">
                                                    <div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="freelancer-proposal">
                                            <?php echo get_the_excerpt($bid) ?>
                                        </div>
                                        <!-- Job Listing Footer -->
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




                                    <ul class="dashboard-task-info bid-info">
                                        <li><strong>

                                                <?php
                                                if (
                                                    $currency_position == 'before'
                                                ) {
                                                    echo get_workscout_currency_symbol();
                                                }
                                                echo get_post_meta($bid->ID, '_budget', true);
                                                if (
                                                    $currency_position == 'after'
                                                ) {
                                                    echo get_workscout_currency_symbol();
                                                }  ?>
                                            </strong>
                                            <span><?php echo get_workscout_task_type($task); ?></span>
                                        </li>
                                        <li><strong><?php echo get_post_meta($bid->ID, '_time', true); ?> <?php esc_html_e('days', 'workscout-freelancer'); ?></strong><span><?php esc_html_e('Delivery Time', 'workscout-freelancer'); ?></span></li>
                                    </ul>

                                    <div data-bid-id="<?php echo esc_attr($bid->ID) ?>" class="buttons-to-right always-visible margin-top-25 margin-bottom-0">

                                        <a href="#" class="bids-action-accept-offer button ripple-effect"><i class="icon-material-outline-check"></i> <?php esc_html_e('Accept Offer', 'workscout-freelancer'); ?></a>
                                        <a href="#" data-recipient="<?php echo esc_attr($author_id); ?>" data-bid_id="bid_<?php echo esc_attr($bid->ID); ?>" class="bids-action-send-msg button dark ripple-effect"><i class="icon-feather-mail"></i> Send Message</a>
                                        <?php
                                        // $actions['remove_bid'] = array(
                                        //     'label' => esc_html__('Delete', 'workscout'),
                                        //     'nonce' => true,
                                        //     'class' => 'task-dashboard-action-delete'
                                        // );

                                        // $actions = apply_filters('workscout_freelancer_my_task_bids_actions', $actions, $task);

                                        // foreach ($actions as $action => $value) {
                                        //     $action_url = add_query_arg(array('action' => $action, 'bid_id' => $bid->ID));

                                        //     $class = isset($value['class']) ? $value['class'] : '';
                                        //     if ($value['nonce'])
                                        //         $action_url = wp_nonce_url($action_url, 'workscout_freelancer_my_task_bids_actions');
                                        //     echo '<a  class=" ' . $class . ' button gray ripple-effect ico" title="' . $value['label'] . '" href="' . $action_url . '" data-tippy-placement="top" class="task-dashboard-action-' . $action . '"><i class="icon-feather-trash-2"></i></a>';
                                        // }
                                        ?>
                                        <!-- <a href="#" class="bids-action-delete-bid button gray ripple-effect ico" title="Remove Bid" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a> -->
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

</div>
<!-- Row / End -->


<!-- Bid Acceptance Popup
================================================== -->
<!-- Reply to review popup -->
<div id="small-dialog-1" class="zoom-anim-dialog mfp-hide small-dialog apply-popup  bid-accept-popup">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Accept Offer', 'workscout'); ?></h3>
    </div>

    <!-- Welcome Text -->
    <div class="welcome-text">

        <div class="bid-acceptance margin-top-15"> </div>
        <div class="bid-proposal margin-top-15">
            <div class="bid-proposal-text"></div> </div>

    </div>
    <!-- Tab -->
    <!-- Tab -->
    <!-- Bidding -->
    <div class="bidding-widget">
        <form id="accept-bid-form">
            <input type="hidden" id="task_id" name="task_id" value="">
            <input type="hidden" id="bid_id" name="bid_id" value="">
            <div class="radio">
                <input id="radio-1" name="radio" type="radio" required>
                <label for="radio-1"><span class="radio-label"></span> <?php esc_html_e('I have read and agree to the Terms and Conditions', 'workscout-freelancer'); ?></label>
            </div>
            <button id="approve-bid" class="margin-top-15 button full-width button-sliding-icon ripple-effect" type="submit" form="accept-bid-form"><?php esc_html_e('Accept', 'workscout-freelancer'); ?> <i class="icon-material-outline-arrow-right-alt"></i></button>
        </form>
    </div>
    <!-- Button -->


</div>


<a style="display: none;" href="#small-dialog-1" class="bids-popup-accept-offer popup-with-zoom-anim  ripple-effect"><?php esc_html_e('Accept Offer', 'workscout-freelancer'); ?></a>
<!-- Bid Acceptance Popup / End -->


<!-- Send Direct Message Popup
================================================== -->
<!-- Reply to review popup -->
<div id="small-dialog-2" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Send Message', 'workscout-freelancer'); ?></h3>
    </div>

    <div class="message-reply margin-top-0">
        <!-- Form -->

        <form action="" id="send-message-from-task" data-task_id="">
            <textarea data-recipient="" data-referral="" id="contact-message" name="textarea" cols="10" placeholder="Message" class="with-border" required></textarea>

            <!-- Button -->
            <button class="button full-width button-sliding-icon ripple-effect" type="submit" form="send-message-from-task"><?php esc_html_e('Send', 'workscout-freelancer'); ?> <i class="icon-material-outline-arrow-right-alt"></i></button>
            <div class="notification closeable success margin-top-20"></div>
        </form>


    </div>

</div>

<a style="display: none;" href="#small-dialog-2" class="bids-popup-msg popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> <?php esc_html_e('Send Message', 'workscout-freelancer'); ?></a>
<!-- Send Direct Message Popup / End -->