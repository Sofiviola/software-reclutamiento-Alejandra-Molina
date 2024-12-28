<?php

/**
 * Resume Submission Form
 */
if (!defined('ABSPATH')) exit;

wp_enqueue_script('wp-task-manager-task-submission');
    $has_company = false;
    if (class_exists('MAS_WP_Job_Manager_Company') && is_user_logged_in()) {
        if (!get_option('job_manager_job_submission_required_company')) {
            $has_company = true;
        } else {
            // Get the current logged in user's ID
            $current_user_id = get_current_user_id();

            // Count the user's posts for 'resume' CPT
            $user_post_count = (int) count_user_posts($current_user_id, 'company');

            // If the user has a 'resume' CPT published
            if ($user_post_count > 0) {
                $has_company = true;
            }
        }
    } else {
        $has_company = true;
    }

?>
<form action="<?php echo $action; ?>" method="post" id="submit-task-form" class="job-manager-form" enctype="multipart/form-data">

    <?php do_action('submit_task_form_start'); ?>



    <?php if (workscout_freelancer_user_can_post_task()) : ?>

        <?php if ($company_fields) : ?>
            <div class="dashboard-box margin-bottom-30">
                <div class="headline">
                    <h3><i class="icon-feather-folder-plus"></i><?php esc_html_e('Select Company', 'workscout-freelancer'); ?></h3>
                </div>
                <div class="task-form-container content with-padding padding-bottom-10">

                    <?php do_action('submit_job_form_company_fields_start'); ?>

                    <?php foreach ($company_fields as $key => $field) : ?>
                        <fieldset class="form fieldset-<?php echo esc_attr($key); ?>">
                            <label for="<?php echo esc_attr($key); ?>"><?php echo $field['label'] . apply_filters('submit_job_form_required_label', $field['required'] ? '' : ' <small>' . esc_html__('(optional)', 'workscout-freelancer') . '</small>', $field); ?></label>
                            <div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
                                <?php get_job_manager_template('form-fields/' . $field['type'] . '-field.php', array('key' => $key, 'field' => $field)); ?>
                            </div>

                        </fieldset>
                    <?php endforeach; ?>

                    <?php if (class_exists('MAS_WP_Job_Manager_Company') && !$has_company) { ?>
                        <?php $submit_company = get_option('job_manager_submit_company_form_page_id');   ?>
                        <div class="notification add-company-notice notice"><?php esc_html_e("You need to select company before adding job listing. If you didn't add company profile yet click button below.", 'workscout-freelancer'); ?></div>
                        <a href="<?php echo esc_url(get_permalink($submit_company)); ?>" class="button add-company-btn"><i class="fa fa-plus-circle"></i> <?php esc_html_e("Add Company", 'workscout-freelancer'); ?></a>
                    <?php } ?>
                    <?php do_action('submit_job_form_company_fields_end'); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="dashboard-box margin-top-0">

            <!-- Resume Fields -->
            <?php do_action('submit_task_form_task_fields_start'); ?>
            <div class="headline">
                <h3><i class="icon-feather-folder-plus"></i> <?php esc_html_e('Task Submission Form', 'workscout-freelancer'); ?></h3>
            </div>


            <div class="task-form-container content with-padding padding-bottom-10">
                <?php
                $total_width = 0;
                $keys = array_keys($task_fields); // Get the keys of the array
                if(isset($task_id) && !empty($task_id)){
                    $type = get_post_meta($task_id, '_task_type', true);
                    
                    if($type == 'fixed'){
                        $exclude_keys = array('hourly_min', 'hourly_max');
                    } else {
                        $exclude_keys = array('budget_min', 'budget_max');
                    }
                    
                } else {
                    $exclude_keys = array('hourly_min', 'hourly_max'); 
                }
                
                foreach ($keys as $index => $key) :
                    $field = $task_fields[$key];

                    
                    if (in_array($key, $exclude_keys)) {
                        
                      continue;
                    }
                    if (isset($field['width'])) {
                        switch ($field['width']) {
                            case 2:
                                $wrap_class = 'col-md-2';

                                break;
                            case 3:
                                $wrap_class = 'col-md-3';

                                break;
                            case 4:
                                $wrap_class = 'col-md-4';

                                break;
                            case 6:
                                $wrap_class = 'col-md-6';

                                break;
                            case 12:
                                $wrap_class = 'col-md-12';

                                break;

                            default:
                                $wrap_class = 'col-md-4';
                                break;
                        }
                    } else {
                        $wrap_class = 'col-md-4';
                    }
                    if (isset($field['width'])) {
                        $width = $field['width'];
                    } else {
                        $width = 4;
                    }

                    if ($total_width == 0) {
                        echo "<div class='row'>";
                    }
                ?>

                    <div class="<?php echo $wrap_class; ?> task-submit-form-container-<?php echo esc_attr($key); ?>">
                        <div class="submit-field">
                            <fieldset class="form fieldset-<?php echo esc_attr($key); ?>">
                                <label for="<?php echo esc_attr($key); ?>"><?php echo $field['label'] . apply_filters('submit_task_form_required_label', $field['required'] ? '' : ' ', $field); ?></label>
                                <div class="field">
                                    <?php 
                                    // if field is budget_min or budget_max then add required value to it
                                    if($key == 'budget_min' || $key == 'budget_max'){
                                        $field['required'] = true;
                                    }
                                    $class->get_field_template($key, $field); ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                <?php
                    $total_width += $width;
                    if ($index < count($keys) - 1) {
                        $next_key = $keys[$index + 1];
                        $next_element = $task_fields[$next_key];
                        if (isset($next_element['width'])) {
                            $next_width = $next_element['width'];
                        } else {
                            $next_width = 4;
                        }
                    }

                    if ($total_width > 12 || $total_width + $next_width > 12) {
                        echo "</div>";
                        $total_width = 0;
                    }
                endforeach; ?>

                <?php do_action('submit_task_form_task_fields_end'); ?>
            </div>
        </div>
        <div id="outside-task-form-container" style="display: none;">
            <?php foreach ($keys as $index => $key) :
                $field = $task_fields[$key];
                if (in_array($key, $exclude_keys)) {
                    if (isset($field['width'])) {
                        switch ($field['width']) {
                            case 2:
                                $wrap_class = 'col-md-2';

                                break;
                            case 3:
                                $wrap_class = 'col-md-3';

                                break;
                            case 4:
                                $wrap_class = 'col-md-4';

                                break;
                            case 6:
                                $wrap_class = 'col-md-6';

                                break;
                            case 12:
                                $wrap_class = 'col-md-12';

                                break;

                            default:
                                $wrap_class = 'col-md-4';
                                break;
                        }
                    } else {
                        $wrap_class = 'col-md-4';
                    }
                    if (isset($field['width'])) {
                        $width = $field['width'];
                    } else {
                        $width = 4;
                    }
                    
            ?>

                    <div class="<?php echo $wrap_class; ?> task-submit-form-container-<?php echo esc_attr($key); ?>">
                        <div class="submit-field">
                            <fieldset class="form fieldset-<?php echo esc_attr($key); ?>">
                                <label for="<?php echo esc_attr($key); ?>"><?php echo $field['label'] . apply_filters('submit_task_form_required_label', $field['required'] ? '' : ' ', $field); ?></label>
                                <div class="field">
                                    <?php $class->get_field_template($key, $field); ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
            <?php
                } else {
                    continue;
                }
            endforeach; ?>
        </div>
        <p class="send-btn-border">
            <input type="hidden" name="workscout_freelancer_form" value="<?php echo $form; ?>" />
            <input type="hidden" name="task_id" value="<?php echo esc_attr($task_id); ?>" />
            <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>" />
            <input type="submit" name="submit_task" class="button big" value="<?php echo esc_attr($submit_button_text); ?>" />
        </p>

    <?php else : ?>

        <?php do_action('submit_task_form_disabled'); ?>

    <?php endif; ?>

    <?php do_action('submit_task_form_end'); ?>
</form>