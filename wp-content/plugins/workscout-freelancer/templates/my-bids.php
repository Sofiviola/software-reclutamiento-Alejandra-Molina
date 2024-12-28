<div class="dashboard-box dashboard-tasks-box  margin-top-0">
    <?php $currency_position =  get_option('workscout_currency_position', 'before'); ?>
    <!-- Headline -->
    <div class="headline">
        <h3><i class="icon-material-outline-gavel"></i> <?php esc_html_e('Bids List', 'workscout-freelancer'); ?></h3>
        <div class="sort-by">
            <form id="tasks-sort-by-form" action=" <?php the_permalink(); ?>" method="get">
                <?php
                $selected = isset($_REQUEST['sort-by']) ? $_REQUEST['sort-by'] : '';
                ?>
                <select name="sort-by" class="select2-single hide-tick tasks-sort-by">
                    <option <?php selected($selected, '') ?> value=""><?php esc_html_e('Active', 'workscout-freelancer'); ?></option>
                    <option <?php selected($selected, 'closed') ?>value="closed"><?php esc_html_e('Closed', 'workscout-freelancer'); ?></option>
                </select>
            </form>
        </div>
    </div>

    <div class="content">
        <ul class="dashboard-box-list">
            <?php if (!$bids) : ?>
                <li><?php esc_html_e('You do not have any bids.', 'workscout'); ?></li>
            <?php endif;  ?>
            <?php foreach ($bids as $key => $bid) {

                $task_id = wp_get_post_parent_id($bid->ID);
                $has_won = get_post_meta($bid->ID, '_selected_for_task_id', true);
                // check status of task_id
                $task_status = get_post_status($task_id);
            ?>
                <li id="my-bids-bid-id-<?php echo $bid->ID; ?>" data-bid-id="<?php echo $bid->ID; ?>" class=" <?php if ($has_won) echo "bid-selected-for-task" ?>">
                    <!-- Job Listing -->
                    <div class="item-listing width-adjustment">

                        <!-- Job Listing Details -->
                        <div class="item-listing-details">

                            <!-- Details -->
                            <div class="item-listing-description">
                                <h3 class="item-listing-title"><a href="<?php echo get_the_permalink($task_id); ?>"><?php echo get_the_title($task_id); ?></a></h3>
                                <?php if ($has_won) : ?>
                                    <p><?php esc_html_e('Your bid was accepted for task', 'workscout-freelancer'); ?> <a href="<?php echo get_the_permalink($has_won); ?>"><?php echo get_the_title($has_won); ?></a></p>

                                <?php endif;
                                if (!$has_won && $task_status == 'expired') : ?>
                                    <p><?php esc_html_e('Task has expired, your bid was not accepted', 'workscout-freelancer'); ?></p>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <?php
                    $budget = get_post_meta($bid->ID, '_budget', true);
                    $scale = get_post_meta($bid->ID, '_time_scale', true);
                    $time = get_post_meta($bid->ID, '_time', true);
                    if ($budget) { ?>
                        <!-- Bid Details -->
                        <ul class="dashboard-task-info">
                            <li id="bid-info-budget"><strong><?php
                                                                if ($currency_position == 'before') {
                                                                    echo get_workscout_currency_symbol();
                                                                }  ?><?php echo (is_numeric($budget)) ? number_format_i18n($budget) : $budget;
                                                                        if ($currency_position == 'after') {
                                                                            echo get_workscout_currency_symbol();
                                                                        }  ?></strong><span><?php esc_html_e('Hourly Rate', 'workscout-freelancer'); ?></span></li>
                            <li id="bid-info-time"><strong><?php echo $time; ?> <?php echo $scale; ?></strong><span><?php esc_html_e('Delivery Time', 'workscout-freelancer'); ?></span></li>
                        </ul>
                    <?php } ?>
                    <?php
                    if (!$has_won) : ?>
                        <!-- Buttons -->
                        <div data-bid-id="<?php echo esc_attr($bid->ID) ?>" class="buttons-to-right always-visible">
                            <a class="bids-action-edit-bid button dark ripple-effect ico" title="<?php esc_html_e('Edit Bid', 'workscout-freelancer'); ?>" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>

                            <?php
                            $actions = apply_filters('task_manager_bookmark_actions', array(
                                'delete' => array(
                                    'label' => esc_html__('Cancel Bid', 'workscout-freeelancer'),
                                    'url'   =>  wp_nonce_url(add_query_arg('remove_bid', $bid->ID), 'remove_bid')
                                )
                            ), $bid);

                            foreach ($actions as $action => $value) {
                                echo '<a href="' . esc_url($value['url']) . '" title=' . $value['label'] . '  data-tippy-placement="top" class="button red ripple-effect ico delete  workscout-bid-action-' . $action . '"><i class="icon-feather-trash-2"></i> </a>';
                            }
                            ?>
                        </div>

                    <?php endif; ?>
                </li>
            <?php } ?>



        </ul>
    </div>
</div>


<!-- Edit Bid Popup
================================================== -->
<?php
$currency_position =  get_option('workscout_currency_position', 'before');
$currency_symbol = get_workscout_currency_symbol();
?>

<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Edit Bid', 'workscout-freelancer'); ?></h3>
    </div>

    <!-- Bidding -->
    <div class="bidding-widget">
        <!-- Headline -->
        <form ​ autocomplete="off" id="form-bidding-update" data-post_id="" class="" method="post">
            <!-- Headline -->

            <span class="bidding-detail bidding-detail-hourly"><?php esc_html_e('Set your', 'workscout'); ?> <strong><?php esc_html_e('hourly rate', 'workscout-freelancer'); ?></strong></span>
            <span class="bidding-detail bidding-detail-fixed"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('bid amount', 'workscout-freelancer'); ?></strong></span>

            <!-- Price Slider -->
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

            <input name="budget" class=" bidding-slider-popup" type="text" value="" data-slider-handle="custom" data-slider-currency="$" data-slider-min="10" data-slider-max="20" data-slider-step="1" data-slider-tooltip="hide" />

            <!-- Headline -->

            <span class="bidding-detail bidding-detail-hourly margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in hours', 'workscout-freelancer'); ?></span>
            <span class="bidding-detail bidding-detail-fixed margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in days', 'workscout-freelancer'); ?></span>

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
            <input type="hidden" name="bid_id" id="bid_id">
            <!-- Button -->
            <button id="snackbar-place-bid" form="form-bidding-update" class="button ripple-effect move-on-hover full-width margin-top-30"><span><?php esc_html_e('Update your Bid', 'workscout-freelancer'); ?></span></button>

        </form>
    </div>



</div>
<a style="display: none;" href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect ico" title="Edit Bid" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
<!-- Edit Bid Popup / End -->