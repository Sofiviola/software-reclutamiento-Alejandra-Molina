<div class="dashboard-box dashboard-tasks-box  margin-top-0">
    <?php $currency_position =  get_option('workscout_currency_position', 'before'); ?>
    <!-- Headline -->
    <div class="headline">
        <h3><i class="icon-material-outline-gavel"></i> <?php esc_html_e('Projects List', 'workscout-freelancer'); ?></h3>
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
            <?php if (!$projects) : ?>
                <li><?php esc_html_e('You do not have any projects.', 'workscout'); ?></li>
            <?php endif;  ?>
            <?php foreach ($projects as $key => $project) {

                $task_id = get_post_meta($project->ID, '_task_id', true);
                $status = get_post_meta($project->ID, '_status', true);
$task_status = get_post_status($task_id);
            ?>
                <li id="my-projects-project-id-<?php echo $project->ID; ?>" data-project-id="<?php echo $project->ID; ?>" class="">
                    <!-- Job Listing -->
                    <div class="item-listing width-adjustment">

                        <!-- Job Listing Details -->
                        <div class="item-listing-details">

                            <!-- Details -->
                            <div class="item-listing-description">
                                <h3 class="item-listing-title"><a href="<?php echo get_the_permalink($project->ID); ?>"><?php echo get_the_title($project->ID); ?></a></h3>
                               

                            </div>
                        </div>
                    </div>

                    <div data-project-id="<?php echo esc_attr($project->ID) ?>" class="buttons-to-right always-visible">
                                <?php  
                                    if ($project->ID) {
                                        $action_url = add_query_arg(array('action' => 'view-project', 'task_id' => $task_id, 'project_id' => $project->ID));
                                        ?>
                                        <a href="<?php echo $action_url; ?>" class="button dark ripple-effect" href="#"><i class="icon-material-outline-supervisor-account"></i><?php esc_html_e('View Project ', 'workscout-freelancer'); ?></a>
                                    <?php } else { ?>


                                    <?php } ?>
                                    
                                <?php                                         
                        
                     
                        ?>
                    </div>

                  
                </li>
            <?php } ?>



        </ul>
    </div>
</div>


<!-- Edit project Popup
================================================== -->
<?php
$currency_position =  get_option('workscout_currency_position', 'before');
$currency_symbol = get_workscout_currency_symbol();
?>

<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog apply-popup ">


    <div class="small-dialog-header">
        <h3><?php esc_html_e('Edit project', 'workscout-freelancer'); ?></h3>
    </div>

    <!-- projectding -->
    <div class="projectding-widget">
        <!-- Headline -->
        <form ​ autocomplete="off" id="form-projectding-update" data-post_id="" class="" method="post">
            <!-- Headline -->

            <span class="projectding-detail projectding-detail-hourly"><?php esc_html_e('Set your', 'workscout'); ?> <strong><?php esc_html_e('hourly rate', 'workscout-freelancer'); ?></strong></span>
            <span class="projectding-detail projectding-detail-fixed"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('project amount', 'workscout-freelancer'); ?></strong></span>

            <!-- Price Slider -->
            <!-- Price Slider -->
            <div class="projectding-value">

                <?php
                if ($currency_position == 'before') {
                    echo $currency_symbol;
                }
                ?><span class="projectdingVal"></span>
                <?php
                if ($currency_position == 'after') {
                    echo $currency_symbol;
                }
                ?></div>

            <input name="budget" class=" projectding-slider-popup" type="text" value="" data-slider-handle="custom" data-slider-currency="$" data-slider-min="10" data-slider-max="20" data-slider-step="1" data-slider-tooltip="hide" />

            <!-- Headline -->

            <span class="projectding-detail projectding-detail-hourly margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in hours', 'workscout-freelancer'); ?></span>
            <span class="projectding-detail projectding-detail-fixed margin-top-30"><?php esc_html_e('Set your', 'workscout-freelancer'); ?> <strong><?php esc_html_e('delivery time', 'workscout-freelancer'); ?></strong> <?php esc_html_e('in days', 'workscout-freelancer'); ?></span>

            <!-- Fields -->
            <div class="projectding-fields">
                <div class="projectding-field">
                    <!-- Quantity Buttons -->
                    <div class="qtyButtons">
                        <div class="qtyDec"></div>
                        <input type="text" class="projectding-time  projectding-time-popup" id="qtyInput" name="project-time" value="1">
                        <div class="qtyInc"></div>
                    </div>
                </div>

            </div>

            <div>
                <div class="projectding-field">
                    <span class="projectding-detail margin-top-30"><?php esc_html_e('Describe your proposal', 'workscout-freelancer'); ?></span>

                    <textarea name="project-proposal" id="project-proposal" cols="30" rows="5" placeholder="<?php esc_html_e('What makes you the best candidate for that project?', 'workscout-freelancer'); ?>"></textarea>
                </div>
            </div>
            <input type="hidden" name="project_id" id="project_id">
            <!-- Button -->
            <button id="snackbar-place-project" form="form-projectding-update" class="button ripple-effect move-on-hover full-width margin-top-30"><span><?php esc_html_e('Update your project', 'workscout-freelancer'); ?></span></button>

        </form>
    </div>



</div>
<a style="display: none;" href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect ico" title="Edit project" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
<!-- Edit project Popup / End -->