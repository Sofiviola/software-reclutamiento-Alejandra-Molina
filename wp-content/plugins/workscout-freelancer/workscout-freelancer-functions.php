<?php

function task_publish_date($post = null)
{
    $date_format = get_option('job_manager_date_format');

    if ('default' === $date_format) {
        $display_date = wp_date(get_option('date_format'), get_post_timestamp($post));
    } else {
        // translators: Placeholder %s is the relative, human readable time since the job listing was posted.
        $display_date =  human_time_diff(get_post_timestamp($post), time());
    }

    echo '<time datetime="' . esc_attr(get_post_datetime($post)->format('Y-m-d')) . '">' . wp_kses_post($display_date) . '</time>';
}


function workscoutThousandsCurrencyFormat($num)
{

    if ($num > 1000) {
// get option for separator 
        $separator = get_option('workscout_thousand_separator', '.');
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ?  $separator . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}

/**
 * True if an the user can bid a task.
 *
 * @param $task_id
 *
 * @return bool
 */
function workscout_freelancer_user_can_bid($task_id) {
    $can_bid = true;

    if (!$task_id || !is_user_logged_in()) {
        $can_bid = false;
    } else {
        $task = get_post($task_id);
        $get_bids = get_posts(array(
            'post_type' => 'bid',
            'post_parent' => $task_id,
            'post_status' => 'publish',
            'author' => get_current_user_id(),
            'posts_per_page' => -1
        ));
        //check if current user is author of any of the posts from $get_bids
        if (count($get_bids) > 0) {
            $can_bid = false;
        }
    }
    return apply_filters('workscout_freelancer_user_can_bid', $can_bid, $task_id);
}

/**
 * True if an the user can edit a task.
 *
 * @param $task_id
 *
 * @return bool
 */
function workscout_freelancer_user_can_edit_task($task_id)
{
    $can_edit = true;

    if (!$task_id || !is_user_logged_in()) {
        $can_edit = false;
        if (
            $task_id
        //    && !task_manager_user_requires_account()
            && isset($_COOKIE['wp-job-manager-submitting-task-key-' . $task_id])
            && $_COOKIE['wp-job-manager-submitting-task-key-' . $task_id] === get_post_meta($task_id, '_submitting_key', true)
        ) {
            $can_edit = true;
        }
    } else {

        $task = get_post($task_id);
        
        if (!$task || (absint($task->post_author) !== get_current_user_id() && !current_user_can('edit_post', $task_id))) {
            $can_edit = false;
        }
    }

    return apply_filters('workscout_freelancer_user_can_edit_task', $can_edit, $task_id);
}
/**
 * Checks if users are allowed to edit submissions that are pending approval.
 *
 * @since 1.16.1
 * @return bool
 */
function  workscout_freelancer_user_can_edit_pending_submissions()
{
    return apply_filters('workscout_freelancer_user_can_edit_pending_submissions', 1 === intval(get_option('workscout_freelancer_user_can_edit_pending_submissions')));
}

/**
 * Checks if users are allowed to edit published submissions.
 *
 * @since 1.29.0
 * @return bool
 */
function  workscout_freelancer_user_can_edit_published_submissions()
{
    /**
     * Override the setting for allowing a user to edit published job listings.
     *
     * @since 1.29.0
     *
     * @param bool $can_edit_published_submissions
     */
    return apply_filters('workscout_freelancer_user_can_edit_published_submissions', in_array(get_option('workscout_freelancer_user_edit_published_submissions'), ['yes', 'yes_moderated'], true));
}
/**
 * True if an the user can post a task. By default, you must be logged in.
 *
 * @return bool
 */
function workscout_freelancer_user_can_post_task()
{
    $can_post = true;

    if (!is_user_logged_in()) {
        // if (task_manager_user_requires_account() && !task_manager_enable_registration()) {
            $can_post = false;
        //}
    }

    return apply_filters('task_manager_user_can_post_task', $can_post);
}


/**
 * True if an account is required to post.
 *
 * @return bool
 */
function workscout_freelancer_user_requires_account()
{
    return apply_filters('task_manager_user_requires_account', get_option('task_manager_user_requires_account') == 1 ? true : false);
}

/**
 * Outputs the jobs status
 *
 * @param WP_Post|int $post (default: null)
 */
function the_task_status($post = null)
{
    echo get_the_task_status($post);
}

/**
 * Gets the jobs status
 *
 * @param WP_Post|int $post (default: null)
 * @return string
 */
function get_the_task_status($post = null)
{
    $post = get_post($post);

    $status = $post->post_status;

    if ($status == 'publish') {
        $status = __('Published', 'workscout-freelancer');
    } elseif ($status == 'expired') {
        $status = __('Expired', 'workscout-freelancer');
    } elseif ($status == 'pending') {
        $status = __('Pending Review', 'workscout-freelancer');
    } elseif ($status == 'hidden') {
        $status = __('Hidden', 'workscout-freelancer');
    } elseif ($status == 'in_progress') {
        $status = __('In Progress', 'workscout-freelancer');
    } elseif ($status == 'completed') {
        $status = __('Completed', 'workscout-freelancer');
    } else {
        $status = __('Inactive', 'workscout-freelancer');
    }

    return apply_filters('the_task_status', $status, $post);
}
/**
 * Gets the jobs status
 *
 * @param WP_Post|int $post (default: null)
 * @return string
 */
function get_the_task_status_class($post = null)
{
    $post = get_post($post);

    $status = $post->post_status;


    return apply_filters('the_task_status_class', $status, $post);
}



if (!function_exists('get_task_bidders_count')) {
    /**
     * Get number of applications for a job
     *
     * @param  int $task_id
     * @return int
     */
    function get_task_bidders_count($task_id)
    {
        $count = count(
            get_posts(
                [
                    'post_type'      => 'bid',
                    'post_status'    => ['publish'],
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'post_parent'    => $task_id,
                ]
            )
        );
        wp_reset_postdata();
        return $count;
    }
}



function workscout_freelancer_ajax_pagination($pages = '', $current = false, $range = 2)
{


    if (!empty($current)) {
        $paged = $current;
    } else {
        global $paged;
    }

    $output = false;
    if (empty($paged)) $paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ($range * 2) + 1;
    $range = 2; // change it to show more links

    if ($pages == '') {
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {


        $output .= '<nav class="pagination margin-top-30"><ul class="pagination">';
        $output .=  ($paged > 2 && $paged > $range + 1 && $showitems < $pages) ? '<li data-paged="prev"><a href="#"><i class="sl sl-icon-arrow-left"></i></a></li>' : '';
        //$output .=  ( $paged > 1 ) ? '<li><a class="previouspostslink" href="#"">'.__('Previous','workscout_core').'</a></li>' : '';
        for ($i = 1; $i <= $pages; $i++) {

            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                if ($paged == $i) {
                    $output .=  '<li class="current" data-paged="' . $i . '"><a href="#">' . $i . ' </a></li>';
                } else {
                    $output .=  '<li data-paged="' . $i . '"><a href="#">' . $i . '</a></li>';
                }
            }
        }
        // $output .=  ( $paged < $pages ) ? '<li><a class="nextpostslink" href="#">'.__('Next','workscout_core').'</a></li>' : '';
        $output .=  ($paged < $pages - 1 &&  $paged + $range - 1 < $pages && $showitems < $pages) ? '<li data-paged="next"><a  href="#"><i class="sl sl-icon-arrow-right"></i></a></li>' : '';
        $output .=  '</ul></nav>';
    }
    return $output;
}
function workscout_freelancer_pagination($pages = '', $current = false, $range = 2)
{
    if (!empty($current)) {
        $paged = $current;
    } else {
        global $paged;
       
    }
    
    if (empty($paged)) $paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ($range * 2) + 1;
    $range = 2; // change it to show more links

    if ($pages == '') {
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }
    // check on wchih page we are
 
    if (1 != $pages) {


        echo '<nav class="pagination margin-top-30"><ul class="pagination">';
        echo ($paged > 2 && $paged > $range + 1 && $showitems < $pages) ? '<li><a href="' . get_pagenum_link(1) . '"><i class="sl sl-icon-arrow-left"></i></a></li>' : '';
        // echo ( $paged > 1 ) ? '<li><a class="previouspostslink" href="'.get_pagenum_link($prev).'">'.__('Previous','workscout_core').'</a></li>' : '';
        for ($i = 1; $i <= $pages; $i++) {
            if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                if ($paged == $i) {
                    echo '<li class="current" data-paged="' . $i . '"><a href="' . get_pagenum_link($i) . '">' . $i . ' </a></li>';
                } else {
                    echo '<li data-paged="' . $i . '"><a href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                }
            }
        }
        // echo ( $paged < $pages ) ? '<li><a class="nextpostslink" href="'.get_pagenum_link($next).'">'.__('Next','workscout_core').'</a></li>' : '';
        echo ($paged < $pages - 1 &&  $paged + $range - 1 < $pages && $showitems < $pages) ? '<li><a  href="' . get_pagenum_link($pages) . '"><i class="sl sl-icon-arrow-right"></i></a></li>' : '';
        echo '</ul></nav>';
    }
}

if (!function_exists('workscout_generate_tasks_sidebar')) {
    function workscout_generate_tasks_sidebar()
    {
        $template_loader = new WorkScout_Freelancer_Template_Loader;
        ob_start();

        $template_loader->get_template_part('sidebar-tasks');
        $result = ob_get_clean();
        return $result;
    }
}

function workscout_get_options_array($type, $data)
{
    $options = array();
    if ($type == 'taxonomy') {

        $args = array(
            'taxonomy' => $data,
            'hide_empty' => true,
            'orderby' => 'term_order'
        );
        $args = apply_filters('listeo_taxonomy_dropdown_options_args', $args);
        $categories =  get_terms($data, $args);

        $options = array();
        foreach ($categories as $cat) {
            $options[$cat->term_id] = array(
                'name'  => $cat->name,
                'slug'  => $cat->slug,
                'id'    => $cat->term_id,
            );
        }
    }
    return $options;
}


function get_workscout_task_type($post = null)
{
    $post = get_post($post);

    if (is_null($post)) {
        return false;
    }
    
    switch (get_post_meta($post->ID, '_task_type', true)) {
        case 'fixed':
            return __('Fixed', 'workscout-freelancer');
            break;
        case 'hourly':
            return __('Hourly', 'workscout-freelancer');
            break;
        
        default:
            return __('Fixed', 'workscout-freelancer');
            break;
    } 
}

function get_workscout_task_range($post= null){
    $post = get_post($post);

    if (is_null($post)) {
        return false;
    }
    $currency_position =  get_option('workscout_currency_position', 'before'); 
    $task_type = get_post_meta($post->ID, '_task_type', true);
    ob_start();
       if($task_type == 'hourly') {
        $hourly_min = get_post_meta($post->ID, '_hourly_min', true);
        $hourly_max = get_post_meta($post->ID, '_hourly_max', true);

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
       }

    if ($task_type == 'fixed') {
            $budget_min = get_post_meta($post->ID, '_budget_min', true);
            $budget_max = get_post_meta($post->ID, '_budget_max', true);
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
    $output = ob_get_clean();
    return $output;
}

function get_workscout_task_bidders_average($task = null)
{
    $task = get_post($task);

    if (is_null($task)) {
        return false;
    }
    $currency_position =  get_option('workscout_currency_position', 'before'); 
    $counter = 0;
    $cumulative_value = 0;
   $avg_query = new WP_Query([
        'post_type'      => 'bid',
        'post_status'    => ['publish'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_parent'    => $task->ID,
    ]);
    
    if (
        $avg_query->have_posts()
    ) :
        while ($avg_query->have_posts()) : $avg_query->the_post();
            $post = get_post();
            
            if ($rating_value = get_post_meta($post->ID, '_budget', true)) :
               
                $cumulative_value += $rating_value;
                $counter++;

            endif;

        endwhile;

    endif;
    if($counter == 0){
        return 0;
    }
    
    $avg_value = $cumulative_value / $counter;
    ob_start();

    if ($currency_position == 'before') {
        echo get_workscout_currency_symbol();
    }
    
    echo $avg_value;

    if ($currency_position == 'after') {
        echo get_workscout_currency_symbol();
    }

    $avg = ob_get_clean();
    return $avg;
}


function workscout_get_bidding_deadline($id){

            $deadline_date = get_post_meta($id, '_task_deadline', true);
            if ($deadline_date) {
                // show how many days and hours are until the timestamp
                //$time_left = human_time_diff(current_time('U'), $deadline_date);
                // change 2023-10-31 to timestamp
                $deadline_date = strtotime($deadline_date);
                $currentTime = time();
                $timeDifference = $deadline_date - $currentTime;
                if ($timeDifference > 0) {
                    $days = floor($timeDifference / (60 * 60 * 24));
                    $hours = floor(($timeDifference % (60 * 60 * 24)) / (60 * 60));
                    $deadline = array(
                        'days' => sprintf(_n('%s day', '%s days', $days, 'workscout-freelancer'), $days),
                        'hours' => sprintf(_n(', %s hour', ', %s hours', $hours, 'workscout-freelancer'), $hours)
                    );
                    return $deadline;
             
                } else { 
                    return true;
                } 
            } else {
                return false;
            }
}

function workscout_get_task_range($task_id){
    if(!$task_id){
        return false;
    }

    $task_type = get_post_meta($task_id, '_task_type', true);
    
    $budget_min = get_post_meta($task_id, '_budget_min', true);
    $budget_max = get_post_meta($task_id, '_budget_max', true);

    $hourly_min = get_post_meta($task_id, '_hourly_min', true);
    $hourly_max = get_post_meta($task_id, '_hourly_max', true);

    
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
    
    $range = $range_max - $range_min;
    if ($range <= 1000) {
        $step = 1; // Set a small step for a narrow range
    } else if ($range <= 10000) {
        $step = 100; // Set a medium step for a moderate range
    } else {
        $step = 500; // Set a larger step for a wide range
    }

    $range_array = array(
        'min' => $range_min,
        'max' => $range_max,
        'step' => $step
    );
    return $range_array;
    
}


function workscout_get_reviews_criteria()
{
    $criteria = array(
        'quality' => array(
            'label' => esc_html__('Quality of Work', 'workscout-freelancer'),
            'tooltip' => esc_html__('Quality of customer service and attitude to work with you', 'workscout-freelancer')
        ),
        'communication-skills' => array(
            'label' => esc_html__('Communication Skills:', 'workscout-freelancer'),
            'tooltip' => esc_html__('Effective communication is crucial for a successful collaboration.', 'workscout-freelancer')
        ),
        'professionalism' => array(
            'label' => esc_html__('Professionalism', 'workscout-freelancer'),
            'tooltip' => esc_html__('Professionalism encompasses various aspects, including responsiveness, politeness, and the ability to handle challenges with a positive attitude.', 'workscout-freelancer')
        ),
      
    );

    return apply_filters('workscout_reviews_criteria', $criteria);
}

function calculate_task_expiry($id)
{
    // Get duration from the product if set...
    $duration = get_post_meta($id, '_duration', true);

    // ...otherwise use the global option
    if (!$duration) {
        $duration = absint(get_option('workscout_freelancer_default_duration'));
    }

    if ($duration > 0) {
        $new_date = date_i18n('Y-m-d', strtotime("+{$duration} days", current_time('timestamp')));

        return $new_date;
    }

    return '';
}



if (!function_exists('ws_task_location')) :
    function ws_task_location($map_link = true, $post = null)
    {
        if (!$post) {
            global $post;
        }
        if ($post) {
      
            if ($post->_remote_position) {
                $remote_label = apply_filters('the_task_location_anywhere_text', __('Remote', 'workscout-freelancer'));
              
                    $location = $remote_label;
                 
              
            }
            $location = $post->_task_location;
            if ($location) {
             
                echo wp_kses_post($location);
              
            } else {
                echo wp_kses_post(apply_filters('the_job_location_anywhere_text', __('Anywhere', 'workscout-freelancer')));
            }
        }
    }
endif;