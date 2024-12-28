<?php
if (!defined('ABSPATH')) {
    exit;
}


$template_loader = new WorkScout_Freelancer_Template_Loader;


$currency_position =  get_option('workscout_currency_position', 'before');


$currency_position =  get_option('workscout_currency_position', 'before');
$task_type = get_post_meta($post->ID, '_task_type', true);
$company = get_post_meta($post->ID, '_company_id', true);

$header_bg_image = get_post_meta($post->ID, '_header_bg_image', true);
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

if (empty($range_min) && !empty($range_max)) {
    $range_min = $range_max - ($range_max * 0.3);
}
if (empty($range_max) && !empty($range_min)) {
    $range_max = $range_min - ($range_max * 0.3);
}


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
                            <div class="header-image"><a href="<?php get_permalink($company); ?>"> <?php
                                                                                                    if ($company) {
                                                                                                        the_company_logo('medium', null, $company);
                                                                                                    } else {
                                                                                                        the_company_logo('medium');
                                                                                                    } ?></a></div>
                        <?php } ?>
                        <div class="header-details">
                            <h3><?php the_title(); ?></h3>
                            <?php $template_loader->get_template_part('single-partials/single-company', 'data');  ?>

                        </div>
                    </div>
                    <div class="right-side">
                        <div class="salary-box">
                            <div class="salary-type">

                                <?php
                                if ($task_type == 'hourly') {
                                    esc_html_e('Hourly Rate', 'workscout-freelancer');
                                } else {
                                    esc_html_e('Project Budget', 'workscout-freelancer');
                                } ?>
                            </div>
                            <div class="salary-amount">
                                <?php
                                if ($task_type == 'hourly') {
                                    if ($hourly_min) {
                                        if ($currency_position == 'before') {
                                            echo get_workscout_currency_symbol();
                                        }
                                        echo esc_html(workscoutThousandsCurrencyFormat($hourly_min));
                                        if ($currency_position == 'after') {
                                            echo get_workscout_currency_symbol();
                                        }
                                    }
                                    if ($hourly_max && $hourly_max > $hourly_min) {
                                        if ($hourly_min) {
                                            echo ' - ';
                                        }
                                        if ($currency_position == 'before') {
                                            echo get_workscout_currency_symbol();
                                        }
                                        echo esc_html(workscoutThousandsCurrencyFormat($hourly_max));
                                        if ($currency_position == 'after') {
                                            echo get_workscout_currency_symbol();
                                        }
                                    }
                                } else {
                                    if ($budget_min) {
                                        if ($currency_position == 'before') {
                                            echo get_workscout_currency_symbol();
                                        }
                                        echo esc_html(workscoutThousandsCurrencyFormat($budget_min));
                                        if ($currency_position == 'after') {
                                            echo get_workscout_currency_symbol();
                                        }
                                    }
                                    if ($budget_max && $budget_max > $budget_min) {
                                        if ($budget_min) {
                                            echo ' - ';
                                        }
                                        if ($currency_position == 'before') {
                                            echo get_workscout_currency_symbol();
                                        }
                                        echo esc_html(workscoutThousandsCurrencyFormat($budget_max));
                                        if ($currency_position == 'after') {
                                            echo get_workscout_currency_symbol();
                                        }
                                    }
                                }

                                ?>
                            </div>
                        </div>
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


        </div>


        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-4">
            <div class="sidebar-container">


                <?php
                $deadline = workscout_get_bidding_deadline($post->ID);
                if (!$deadline) {

                    if (is_array($deadline)) : ?>
                        <div class="countdown green margin-bottom-35">
                            <?php echo $deadline['days'];
                            echo $deadline['hours'] ?> left </div>
                    <?php else : ?>
                        <div class="countdown green margin-bottom-35"><?php esc_html_e('Bidding has closed', 'workscout-freelancer'); ?></div>
                <?php endif;
                } ?>


                <?php get_sidebar('task'); ?>

            </div>
        </div>

    </div>
</div>