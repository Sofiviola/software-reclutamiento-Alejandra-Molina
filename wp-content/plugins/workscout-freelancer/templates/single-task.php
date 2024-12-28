<?php
if (!defined('ABSPATH')) {
    exit;
}


$template_loader = new WorkScout_Freelancer_Template_Loader;

get_header(get_option('header_bar_style', 'standard'));
$currency_position =  get_option('workscout_currency_position', 'before');
$currency_symbol = get_workscout_currency_symbol();
$task_type = get_post_meta($post->ID, '_task_type', true);
// get current post status
$task_status = get_post_status($post->ID);
//if task status is expired, then show the message

$selected_bid = get_post_meta($post->ID, '_selected_bid_id', true);
if (have_posts()) :
    while (have_posts()) : the_post();

        $show_bid_form = false;
        $budget_min = get_post_meta($post->ID, '_budget_min', true);
        $budget_max = get_post_meta($post->ID, '_budget_max', true);

        $hourly_min = get_post_meta($post->ID, '_hourly_min', true);
        $hourly_max = get_post_meta($post->ID, '_hourly_max', true);

        if ($task_type == 'hourly') {
            $range_min = $hourly_min;
            $range_max = $hourly_max;
        } else {
            $range_min = $budget_min;
            $range_max = $budget_max;
        }

        if (empty($range_min) && $range_min !== "" && !empty($range_max)) {
            $range_min = $range_max - ($range_max * 0.3);
        }

        if (empty($range_max) && $range_max !== "" && !empty($range_min)) {

            // why is this empty?

            $range_max = $range_min - ($range_max * 0.3);
        }



        if (!empty($range_min) && is_numeric($range_min) && !empty($range_max) && is_numeric($range_max)) {
            $show_bid_form = true;
        }

        if ($show_bid_form) {
            $range = $range_max - $range_min;
            if ($range <= 1000) {
                $step = 1; // Set a small step for a narrow range
            } else if ($range <= 10000) {
                $step = 100; // Set a medium step for a moderate range
            } else {
                $step = 500; // Set a larger step for a wide range
            }
        }

        if ($selected_bid) {
            $show_bid_form = false;
        }
        $company = get_post_meta($post->ID, '_company_id', true);
        // check if company post exists
        if ($company) {
            $company_post = get_post($company);
            if (!$company_post) {
                $company = false;
            }
        }

        $header_bg_image = get_post_meta($post->ID, '_header_bg_image', true);
?>
        <!-- Titlebar
	    ================================================== -->
        <div class="single-page-header" <?php if ($header_bg_image) : ?>data-background-image="<?php echo $header_bg_image; ?>" <?php endif; ?>>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="single-page-header-inner">
                            <div class="left-side">
                                <?php if (!empty($company)) { ?>
                                    <div class="header-image"><a href="<?php get_permalink($company); ?>">
                                            <?php
                                            if ($company) {
                                                the_company_logo('medium', null, $company);
                                            } else {
                                                the_company_logo('medium');
                                            } ?>
                                        </a></div>
                                <?php } ?>
                                <div class="header-details">
                                    <h3><?php the_title(); ?></h3>
                                    <?php

                                    if (!$company) { ?>
                                        <h5><?php esc_html_e('Added by', 'workscout-freelancer'); ?></h5>
                                        <ul>

                                            <li><i class="icon-material-outline-account-circle"></i> <?php esc_html_e('Private Person', 'workscout-freelancer'); ?></li>


                                        </ul>
                                    <?php } else {
                                        $template_loader->get_template_part('single-partials/single-company', 'data');
                                    } ?>

                                </div>
                            </div>
                            <div class="right-side">
                                <?php if ($show_bid_form) { ?>
                                    <div class="salary-box">
                                        <div class="salary-type">
                                            <?php
                                            if ($task_type == 'hourly') {
                                                esc_html_e('Hourly Rate', 'workscout-freelancer');
                                            } else {
                                                esc_html_e('Budget', 'workscout-freelancer');
                                            } ?>
                                        </div>
                                        <div class="salary-amount">
                                            <?php
                                            if ($task_type == 'hourly') {
                                                if ($hourly_min) {
                                                    if ($currency_position == 'before') {
                                                        echo $currency_symbol;
                                                    }
                                                    echo esc_html(workscoutThousandsCurrencyFormat($hourly_min));
                                                    if ($currency_position == 'after') {
                                                        echo $currency_symbol;
                                                    }
                                                }
                                                if ($hourly_max && $hourly_max > $hourly_min) {
                                                    if ($hourly_min) {
                                                        echo ' - ';
                                                    }
                                                    if ($currency_position == 'before') {
                                                        echo $currency_symbol;
                                                    }
                                                    echo esc_html(workscoutThousandsCurrencyFormat($hourly_max));
                                                    if ($currency_position == 'after') {
                                                        echo $currency_symbol;
                                                    }
                                                }
                                            } else {
                                                if ($budget_min) {
                                                    if ($currency_position == 'before') {
                                                        echo $currency_symbol;
                                                    }
                                                    echo esc_html(workscoutThousandsCurrencyFormat($budget_min));
                                                    if ($currency_position == 'after') {
                                                        echo $currency_symbol;
                                                    }
                                                }
                                                if ($budget_max && $budget_max > $budget_min) {
                                                    if ($budget_min) {
                                                        echo ' - ';
                                                    }
                                                    if ($currency_position == 'before') {
                                                        echo $currency_symbol;
                                                    }
                                                    echo esc_html(workscoutThousandsCurrencyFormat($budget_max));
                                                    if ($currency_position == 'after') {
                                                        echo $currency_symbol;
                                                    }
                                                }
                                            }

                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Page Content
================================================== -->
        <div class="container">
            <div class="row">

                <!-- Content -->
                <div class="col-xl-8 col-lg-8 content-right-offset">

                    <div class="single-page-section">
                        <h3 class="margin-bottom-25"><?php esc_html_e('Project Description', 'workscout-freelancer'); ?></h3>

                        <?php the_content(); ?>
                    </div>

                    <?php $template_loader->get_template_part('single-partials/single-task', 'attachments');  ?>
                    <?php $template_loader->get_template_part('single-partials/single-task', 'skills');  ?>
                    <?php $template_loader->get_template_part('single-partials/single-task', 'bids');  ?>

                </div>


                <!-- Sidebar -->
                <div class="col-xl-4 col-lg-4">
                    <div class="sidebar-container">


                        <?php
                        if ($task_status == 'expired') { ?>
                            <div class="countdown yellow margin-bottom-35" role="alert"><?php esc_html_e('This task has expired.', 'workscout-freelancer'); ?></div>
                            <?php } else {
                            $deadline = workscout_get_bidding_deadline($post->ID);
                            if ($selected_bid) {
                                $deadline = 'closed';
                            }
                            if ($deadline) {

                                if (is_array($deadline)) : ?>
                                    <div class="countdown green margin-bottom-35">
                                        <?php esc_html_e(' Bidding ends in ', 'workscout-freelancer'); ?><?php echo $deadline['days'];
                                                                                                            echo $deadline['hours'] ?> </div>
                                <?php else :
                                    $show_bid_form = false; ?>
                                    <div class="countdown green margin-bottom-35"><?php esc_html_e('Bidding has closed', 'workscout-freelancer'); ?></div>
                            <?php endif;
                            }  ?>

                            <div class="sidebar-widget widget">
                                <?php if ($show_bid_form) : ?>
                                    <div class="bidding-widget">
                                        <div class="bidding-headline">
                                            <h3><?php esc_html_e('Bid on this job!', 'workscout-freelancer'); ?></h3>
                                        </div>
                                        <div class="bidding-inner">



                                            <!-- Headline -->
                                            <?php if ($task_type == 'hourly') { ?>
                                                <span class="bidding-detail"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('hourly rate', 'workscout-freelancer'); ?></strong></span>
                                            <?php } else { ?>
                                                <span class="bidding-detail"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('bid amount', 'workscout-freelancer'); ?></strong></span>
                                            <?php } ?>

                                            <!-- Price Slider -->
                                            <div class="bidding-value">

                                                <?php
                                                if ($currency_position == 'before') {
                                                    echo $currency_symbol;
                                                }
                                                ?><span class="biddingVal"></span>
                                                <?php
                                                if ($currency_position == 'after') {
                                                    echo $currency_symbol;
                                                }
                                                ?></div>

                                            <input name="budget" class="bidding-slider bidding-slider-widget" type="text" value="" data-slider-handle="custom" data-slider-currency="<?php echo $currency_symbol; ?>" data-slider-min="<?php echo $range_min; ?>" data-slider-max="<?php echo $range_max; ?>" data-slider-value="auto" data-slider-step="<?php echo $step; ?>" data-slider-tooltip="hide" />

                                            <!-- Headline -->
                                            <?php if ($task_type == 'hourly') { ?>
                                                <span class="bidding-detail margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in hours', 'workscout-freelancer'); ?></span>
                                            <?php } else { ?>
                                                <span class="bidding-detail margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in days', 'workscout-freelancer'); ?></span>
                                            <?php } ?>
                                            <!-- Fields -->
                                            <div class="bidding-fields">
                                                <div class="bidding-field">
                                                    <!-- Quantity Buttons -->
                                                    <div class="qtyButtons">
                                                        <div class="qtyDec"></div>
                                                        <input type="text" class="bidding-time bidding-time-widget" id="qtyInput" name="time" value="1">
                                                        <div class="qtyInc"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php if (workscout_freelancer_user_can_bid($post->ID)) { ?>
                                                <!-- Button -->
                                                <button id="snackbar-place-bid" class="button bid-now-btn ripple-effect move-on-hover full-width margin-top-30"><span><?php esc_html_e('Place a Bid', 'workscout-freelancer'); ?></span></button>
                                                <a style="display: none;" href="#small-dialog" class="popup-with-zoom-anim button trigger-bid-popup ripple-effect ico button ripple-effect move-on-hover full-width margin-top-30"><span><?php esc_html_e('Place a Bid', 'workscout-freelancer'); ?></span></a>


                                                <?php } else {
                                                if (is_user_logged_in()) { ?>
                                                    <div class="bidding-detail margin-top-20"><?php esc_html_e('You have already bid on this project.', 'workscout-freelancer'); ?> <br> <?php esc_html_e('You can edit your bids', 'workscout-freelancer'); ?> <a href="<?php echo get_permalink(get_option('workscout_freelancer_manage_my_bids_page_id')) ?>"><?php esc_html_e('here', 'workscout-freelancer'); ?></a>.</div>
                                                <?php } else { ?>
                                                    <a href="#login-dialog" class="small-dialog popup-with-zoom-anim login-btn button margin-top-30"><?php esc_html_e('Login to Bid', 'workscout-freelancer'); ?></a>
                                                <?php } ?>

                                            <?php } ?>
                                        </div>
                                        <div class="bidding-inner-success bidding-inner" style="display:none;">

                                            <i class="fa fa-check-circle"></i>
                                            <br>
                                            <h3><?php esc_html_e('Thanks for the bid!', 'workscout-freelancer'); ?></h3>


                                        </div>
                                        <?php if (!is_user_logged_in()) : ?>
                                            <div class="bidding-signup"><?php esc_html_e('Don\'t have an account? ', 'workscout-freelancer'); ?><a href="#signup-dialog" class="register-tab sign-in popup-with-zoom-anim"><?php esc_html_e('Sign Up', 'workscout-freelancer'); ?></a></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php } ?>

                        <?php get_sidebar('task'); ?>

                    </div>
                </div>

            </div>
        </div>


    <?php endwhile; ?>

<?php else : ?>

    <?php get_template_part('content', 'none'); ?>

<?php endif; ?>
<?php if ($show_bid_form) : ?>
    <!-- Reply to review popup -->
    <div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


        <div class="small-dialog-header">
            <h3><?php esc_html_e('Place Bid', 'workscout-freelancer'); ?></h3>
        </div>

        <!-- Bidding -->
        <div class="bidding-widget">
            <!-- Headline -->
            <form ​ autocomplete="off" id="form-bidding" data-post_id="<?php echo $post->ID; ?>" class="form-bidding-<?php echo $post->ID; ?>" method="post">
                <!-- Headline -->
                <?php if ($task_type == 'hourly') { ?>
                    <span class="bidding-detail"><?php echo sprintf(__('Set your %s hourly rate %s', 'workscout-freelancer'), '<strong>', '</strong>'); ?></span>
                <?php } else { ?>
                    <span class="bidding-detail"><?php echo sprintf(__('Set your %s bid amount %s', 'workscout-freelancer'), '<strong>', '</strong>'); ?></span>
                <?php } ?>

                <!-- Price Slider -->
                <div class="bidding-value"><?php echo $currency_symbol; ?><span class="biddingVal"></span></div>

                <input name="budget" class="bidding-slider bidding-slider-popup" type="text" value="" data-slider-handle="custom" data-slider-currency="<?php echo $currency_symbol; ?>" data-slider-min="<?php echo $range_min; ?>" data-slider-max="<?php echo $range_max; ?>" data-slider-value="auto" data-slider-step="<?php echo $step; ?>" data-slider-tooltip="hide" />

                <!-- Headline -->
                <?php if ($task_type == 'hourly') { ?>
                    <span class="bidding-detail margin-top-30"><?php echo sprintf(__('Set your %s delivery time %s in hours', 'workscout-freelancer'), '<strong>', '</strong>'); ?></span>
                <?php } else { ?>
                    <span class="bidding-detail margin-top-30"><?php echo sprintf(__('Set your %s delivery time %s in days', 'workscout-freelancer'), '<strong>', '</strong>'); ?></span>
                <?php } ?>
                <!-- Fields -->
                <div class="bidding-fields">
                    <div class="bidding-field">
                        <!-- Quantity Buttons -->
                        <div class="qtyButtons">
                            <div class="qtyDec"></div>
                            <input type="text" class="bidding-time  bidding-time-popup" id="qtyInput" name="bid-time" value="1">
                            <div class="qtyInc"></div>
                        </div>
                    </div>

                </div>

                <div>
                    <div class="bidding-field">
                        <span class="bidding-detail margin-top-30"><?php esc_html_e('Describe your proposal', 'workscout-freelancer'); ?></span>

                        <textarea name="bid-proposal" id="bid-proposal" cols="30" rows="5" placeholder="<?php esc_html_e('What makes you the best candidate for that project?', 'workscout-freelancer'); ?>"></textarea>
                    </div>
                </div>

                <!-- Button -->
                <button id="snackbar-place-bid" form="form-bidding" class="button ripple-effect move-on-hover full-width margin-top-30"><span><?php esc_html_e('Place a Bid', 'workscout-freelancer'); ?></span></button>

            </form>
        </div>



    </div>

<?php endif; ?>
<?php



get_footer(); ?>