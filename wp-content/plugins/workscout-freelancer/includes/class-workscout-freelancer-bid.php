<?php

if (!defined('ABSPATH')) exit;

class WorkScout_Freelancer_Bid
{

    /**
     * The single instance of WorkScout_Freelancer.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;



    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.3.4')
    {
        add_action('wp_ajax_workscout_task_bid', array($this, 'wp_ajax_workscout_task_bid'));
        add_action('wp_ajax_workscout_update_bid', array($this, 'wp_ajax_workscout_update_bid'));
        add_action('workscout_freelancer_task_dashboard_content_show_bidders', [$this, 'show_bidders']);

        add_action('wp_ajax_workscout_get_bid_data', array($this, 'get_bid_data'));
        add_action('wp_ajax_workscout_get_bid_data_for_edit', array($this, 'get_bid_data_for_edit'));
        add_action('wp_ajax_workscout_get_bid_data_for_contact', array($this, 'get_bid_data_for_contact'));
        add_action('wp_ajax_workscout_accept_bid_on_task', array($this, 'accept_bid_on_task'));

        add_action('wp', array($this, 'bid_handler'));
    }

    /**
     * Handle the bid form
     */
    public function bid_handler()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            return;
        }

        $action_data = null;

        if (!empty($_GET['remove_bid'])) {


            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'remove_bid')) {
                $action_data = array(
                    'error_code' => 400,
                    'error' => __('Bad request', 'workscout-freelancer'),
                );
            } else {
                $post_id = absint($_GET['remove_bid']);
                $user_id  = get_current_user_id();
                $bid = get_post($post_id);
                if (absint($bid->post_author) !== $user_id) {
                    throw new Exception(__('Invalid Bid', 'workscout-freelancer'));
                }
                wp_trash_post($post_id);
                $action_data = array('success' => true);
            }
        }

        if (
            null === $action_data
        ) {
            return;
        }
        if (!empty($_REQUEST['wpjm-ajax']) && !defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        if (wp_doing_ajax()) {
            wp_send_json($action_data, !empty($action_data['error_code']) ? $action_data['error_code'] : 200);
        } else {
            wp_redirect(remove_query_arg(array('submit_bid', 'remove_bid', '_wpnonce', 'wpjm-ajax')));
        }
    }

    function get_bid_data()
    {
        $bid_id = sanitize_text_field($_REQUEST['bid_id']);
        $bid = get_post($bid_id);

        $bid_meta = get_post_meta($bid_id);
        $bid_author = $bid->post_author;
        $bid_proposal = $bid->post_content;
        // $bid_data = array(
        //     'budget'    => $bid_meta['_bid_budget'][0],
        //     'time'      => $bid_meta['_bid_time'][0],
        //     'scale'     => $bid_meta['_bid_time_scale'][0],
        // );

        $currency_position =  get_option('workscout_currency_position', 'before');
        $currency_symbol = get_workscout_currency_symbol();

        $bid_data = '';

        if (
            $currency_position == 'before'
        ) {
            $bid_data .= $currency_symbol;
        }
        $bid_data .= $bid_meta['_budget'][0];
        if (
            $currency_position == 'after'
        ) {
            $bid_data .= $currency_symbol;
        }
        $bid_data .= ' in ';
        $bid_data .= $bid_meta['_time'][0];
        $bid_data .= ' days';


        $popup_data = array(
            'title' =>  esc_html__("Accept offer from ", 'workscout-freelancer') . workscout_get_users_name($bid_author),
            'content' => $bid_data,
            'proposal' => $bid_proposal,
            'bid_id' => $bid_id,
            'task_id' => $bid->post_parent,
        );
        $result = json_encode($popup_data);
        echo $result;
        die();
    }


    function get_bid_data_for_edit()
    {
        $bid_id = sanitize_text_field($_REQUEST['bid_id']);
        $bid = get_post($bid_id);
        //$task = get_post($bid->post_parent);
        $bid_author = $bid->post_author;
        $bid_proposal = $bid->post_content;

        $range = workscout_get_task_range($bid->post_parent);


        $popup_data = array(
            'task_type' => get_post_meta($bid->post_parent, '_task_type', true),
            'budget' => get_post_meta($bid->ID, '_budget', true),
            'time' => get_post_meta($bid->ID, '_time', true),
            'range_min' => $range['min'],
            'range_max' => $range['max'],
            'slider_step' => $range['step'],
            'proposal' => $bid_proposal,
            'bid_id' => $bid_id,
            'task_id' => $bid->post_parent,
        );
        $result = json_encode($popup_data);
        echo $result;
        die();
    }


    function get_bid_data_for_contact()
    {
        $task = sanitize_text_field($_REQUEST['task_id']);

        if (empty($task)) {
            wp_send_json_error();
        }

        $bid_id = get_post_meta($task, '_selected_bid_id', true);
        $bid = get_post($bid_id);
        $bid_author = $bid->post_author;
        $bid_author_date = get_userdata($bid_author);
        $bid_proposal = $bid->post_content;
        $bid_data = array(
            'budget'    => get_post_meta($bid->ID, '_budget', true),
            'time'      => get_post_meta($bid->ID, '_time', true),
            'proposal'  => $bid_proposal,

        );

        ob_start();

?>
        <p> <?php esc_html_e('You have selected', 'workscout-freelancer'); ?> <strong><a href="<?php echo get_author_posts_url($bid_author); ?>"><?php echo workscout_get_users_name($bid_author); ?></a></strong> <?php esc_html_e('for this task', 'workscout-freelancer'); ?></p>
        <div class="bidding-detail margin-top-20">
            <strong><?php esc_html_e('Budget: ', 'workscout-freelancer'); ?></strong>
            <?php echo get_workscout_currency_symbol(); ?><?php echo $bid_data['budget']; ?>
        </div>
        <div class="bidding-detail margin-top-20">
            <strong><?php esc_html_e('Time: ', 'workscout-freelancer'); ?></strong>
            <?php echo $bid_data['time']; ?> days
        </div>
        <div class="bidding-detail margin-top-20">
            <strong><?php esc_html_e('Proposal: ', 'workscout-freelancer'); ?></strong>
            <div class="bid-proposal-text"><?php echo $bid_data['proposal']; ?></div>
        </div>
        <p>You can contact him via Messages or using his email: <strong><?php echo ($bid_author_date->user_email); ?></strong></p>

        
<?php $message = ob_get_clean();
        $return = array(
            'message'  => $message,
        );

        wp_send_json($return);
    }

    function accept_bid_on_task()
    {
        $bid_id = sanitize_text_field($_REQUEST['bid_id']);
        $task_id = sanitize_text_field($_REQUEST['task_id']);
        $bid = get_post($bid_id);

        //
        if ($task_id == $bid->post_parent) {
            // set task statu "in progress"
            $update_task = [
                'ID'          => $task_id,
                'post_status' => 'in_progress',
            ];
            $update = wp_update_post($update_task);
            update_post_meta($task_id, '_selected_bid_id', $bid_id);
            update_post_meta($bid_id, '_selected_for_task_id', $task_id);
            //set all other bids as closed
            $args = array(
                'post_type' => 'bid',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'post_parent' => $task_id,
                'post__not_in' => array($bid_id),
            );
            $bids = get_posts($args);
            foreach ($bids as $bid) {
                $update_bid = [
                    'ID'          => $bid->ID,
                    'post_status' => 'closed',
                ];
                $update = wp_update_post($update_bid);
            }
            if ($update) {

                $result['type'] = 'success';
                $result['message'] = __('You have successfully choosed a bidder', 'workscout-freelancer');
                // create a project
                $this->create_project($task_id);
            } else {
                $result['type'] = 'error';
                $result['message'] = __('Error, please try again or contact support', 'workscout-freelancer');
            }
        } else {
            $result['type'] = 'error';
            $result['message'] = __('Error, please try again or contact support', 'workscout-freelancer');
        }
        $result = json_encode($result);
        echo $result;
        die();
    }

    // create a new post type project and copy content from task
    public function create_project($task_id){
        $task = get_post($task_id);
        $project_data = array(
            'post_title' => $task->post_title,
            'post_content' => $task->post_content,
            'post_status' => 'publish',
            'post_type' => 'project',
            'post_author' => $task->post_author,
        );
        $project_id = wp_insert_post($project_data);
        if ($project_id) {
            update_post_meta($task_id, '_project_id', $project_id);
            update_post_meta($project_id, '_task_id', $task_id);
            $selected_bid_id = get_post_meta($task_id, '_selected_bid_id', true);
            update_post_meta($project_id, '_selected_bid_id', $selected_bid_id);
            // get id of author of the selected bid
            $bid = get_post($selected_bid_id);
            $_freelancer_id = $bid->post_author;

            update_post_meta($project_id, '_freelancer_id', $_freelancer_id);
            update_post_meta($project_id, '_employer_id', $task->post_author);
            update_post_meta($project_id, '_project_status', 'in_progress');
            update_post_meta($project_id, '_project_start_date', current_time('mysql'));
            

            // copy attachments
            $attachments = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $task_id,
            ));
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    $attachment_data = array(
                        'post_title' => $attachment->post_title,
                        'post_content' => $attachment->post_content,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_author' => $attachment->post_author,
                        'post_parent' => $project_id,
                    );
                    $attachment_id = wp_insert_post($attachment_data);
                    if ($attachment_id) {
                        update_post_meta($attachment_id, '_wp_attached_file', get_post_meta($attachment->ID, '_wp_attached_file', true));
                        update_post_meta($attachment_id, '_wp_attachment_metadata', get_post_meta($attachment->ID, '_wp_attachment_metadata', true));
                    }
                }
            }

            // copy meta
            $meta = get_post_meta($task_id);
            if ($meta) {
                foreach ($meta as $key => $value) {
                    update_post_meta($project_id, $key, $value[0]);
                }
            }
        }
        

    }


    /**
     * Show applications on the job dashboard
     */
    public function show_bidders($atts)
    {
        $task_id = absint($_REQUEST['task_id']);
        $task    = get_post($task_id);

        extract(
            shortcode_atts(
                [
                    'posts_per_page' => '20',
                ],
                $atts
            )
        );

        //   remove_filter('the_title', [$this, 'add_breadcrumb_to_the_title']);

        // Permissions
        if (!workscout_freelancer_user_can_edit_task($task_id)) {
            _e('You do not have permission to view this task.', 'workscout-freelancer');
            return;
        }


        $args = apply_filters(
            'workscout_freelancer_task_bidders_args',
            [
                'post_type'           => 'bid',
                'post_status'         =>  ['publish'],
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => $posts_per_page,
                'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
                'post_parent'         => $task_id,
            ]
        );

        // Filters
        // $application_status  = !empty($_GET['application_status']) ? sanitize_text_field($_GET['application_status']) : '';
        // $application_orderby = !empty($_GET['application_orderby']) ? sanitize_text_field($_GET['application_orderby']) : '';

        // if ($application_status) {
        //     $args['post_status'] = $application_status;
        // }

        // switch ($application_orderby) {
        //     case 'name':
        //         $args['order']   = 'ASC';
        //         $args['orderby'] = 'post_title';
        //         break;
        //     case 'rating':
        //         $args['order']    = 'DESC';
        //         $args['orderby']  = 'meta_value';
        //         $args['meta_key'] = '_rating';
        //         break;
        //     default:
        //         $args['order']   = 'DESC';
        //         $args['orderby'] = 'date';
        //         break;
        // }

        $bids = new WP_Query();

        $columns = apply_filters(
            'job_manager_job_applications_columns',
            [
                'name'  => __('Name', 'workscout-freelancer'),
                'email' => __('Email', 'workscout-freelancer'),
                'date'  => __('Date Received', 'workscout-freelancer'),
            ]
        );

        get_job_manager_template(
            'task-bids.php',
            [
                'bids'              => $bids->query($args),
                'task_id'           => $task_id,
                'max_num_pages'     => $bids->max_num_pages,
                'columns'           => $columns,
                //    'application_status'  => $application_status,
                //    'application_orderby' => $application_orderby,
            ],
            'workscout-freelancer',
            WORKSCOUT_FREELANCER_PLUGIN_DIR . '/templates/'
        );
    }

    public function wp_ajax_workscout_task_bid()
    {
        $id = sanitize_text_field($_REQUEST['task_id']);

        $data = array(
            'budget'    => sanitize_text_field($_REQUEST['budget']),
            'time'      => sanitize_text_field($_REQUEST['time']),
            'proposal'     => sanitize_textarea_field($_REQUEST['proposal']),
        );

        $user_id = get_current_user_id();
        // check if user can bid on this task
        $can_bid = workscout_freelancer_user_can_bid($id);
        if ($can_bid) {
            $this->create_bid($id, $user_id, $data);
            $result['type'] = 'success';
            $result['message'] = __('Your bid was successfully sent', 'workscout-freelancer');
        } else {
            $result['type'] = 'error';
            $result['message'] = __('You can\'t bid on this task', 'workscout-freelancer');
        }



        $result = json_encode($result);
        echo $result;
        die();
    }


    public function wp_ajax_workscout_update_bid()
    {

        $bid_id =  sanitize_text_field($_REQUEST['bid_id']);
        $data = array(
            'budget'    => sanitize_text_field($_REQUEST['budget']),
            'time'      => sanitize_text_field($_REQUEST['time']),
            'proposal'  => sanitize_textarea_field($_REQUEST['proposal']),
            'bid_id'    => $bid_id,
        );

        $user_id = get_current_user_id();
        // check if user can bid on this task
        $can_bid = workscout_freelancer_user_can_bid($_REQUEST['bid_id']);
        if ($can_bid) {
            $this->update_bid($bid_id, $user_id, $data);
            $result['type'] = 'success';
            $result['message'] = __('Your bid was successfully updated', 'workscout-freelancer');
        } else {
            $result['type'] = 'error';
            $result['message'] = __('You can\' update this bid', 'workscout-freelancer');
        }



        $result = json_encode($result);
        echo $result;
        die();
    }


    function create_bid($task_id, $freelancer_id, $data = [])
    {
        $task = get_post($task_id);

        if (
            !$task || $task->post_type !== 'task'
        ) {
            return false;
        }
        $bid_title = get_the_title($task_id) . '-' . $freelancer_id;
        $bid_data = [
            'post_title'     => wp_kses_post($bid_title),
            'post_content'   => wp_kses_post($data['proposal']),
            'post_type'      => 'bid',
            'post_status'    => 'publish',
            'comment_status' => 'closed',
            'post_author'    => $freelancer_id,
            'post_parent'    => $task_id,
        ];
        $bid_id   = wp_insert_post($bid_data);
        if ($bid_id) {
            update_post_meta($bid_id, '_budget', $data['budget']);
            update_post_meta($bid_id, '_time', $data['time']);
          


            return $bid_id;
        }

        return false;
    }

    function update_bid($bid_id, $freelancer_id, $data = [])
    {
        workscout_write_log($data);
        $bid_data = [
            'ID'             => $data['bid_id'],
            'post_content'   => wp_kses_post($data['proposal']),
            'post_type'      => 'bid',
            'post_status'    => 'publish',
            'comment_status' => 'closed',
            'post_author'    => $freelancer_id,
        ];
        $bid_id   = wp_update_post($bid_data);
        if ($bid_id) {
            update_post_meta($bid_id, '_budget', $data['budget']);
            update_post_meta($bid_id, '_time', $data['time']);
            return $bid_id;
        }

        return false;
    }
    /**
     * Main WorkScout_Freelancer Instance
     *
     * Ensures only one instance of WorkScout_Freelancer is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WorkScout_Freelancer()
     * @return Main WorkScout_Freelancer instance
     */
    public static function instance($file = '', $version = '1.2.1')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    } // End instance ()



}