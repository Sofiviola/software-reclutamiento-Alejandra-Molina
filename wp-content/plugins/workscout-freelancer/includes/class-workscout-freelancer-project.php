<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * WorkScout_Freelancer_Task class
 */
class WorkScout_Freelancer_Project
{

    private static $_instance = null;
    private $meta_key = 'project_milestones';

    public function __construct()
    {
        add_action('init', array($this, 'workscout_handle_project_comment'));

        add_action('wp_ajax_save_milestone', array($this, 'ajax_save_milestone'));
        add_action('wp_ajax_approve_milestone', array($this, 'ajax_approve_milestone'));
        add_action('wp_ajax_complete_milestone', array($this, 'ajax_complete_milestone'));
        add_action( 'woocommerce_order_status_changed', array($this, 'handle_order_status_change'), 10, 3);
    }
    /**
     * Get instance of the class
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function workscout_handle_project_comment()
    {
        if (!isset($_POST['submit_project_comment'])) {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        // Verify nonce
        if (!isset($_POST['project_comment_nonce']) || !wp_verify_nonce($_POST['project_comment_nonce'], 'project_comment_action')) {
            return;
        }

        $project_id = absint($_POST['project_id']);
        $comment_content = wp_kses_post($_POST['comment_content']);
        $is_milestone = isset($_POST['is_milestone']) ? 1 : 0;

        // Check if user is allowed to comment (project owner or assigned freelancer)
        $freelancer_id = get_post_meta($project_id, '_freelancer_id', true);
        $employer_id = get_post_meta($project_id, '_employer_id', true);
        $current_user_id = get_current_user_id();

        if ($current_user_id != $freelancer_id && $current_user_id != $employer_id) {
            return;
        }

        // Prepare comment data
        $comment_data = array(
            'comment_post_ID' => $project_id,
            'comment_content' => $comment_content,
            'user_id' => $current_user_id,
            'comment_type' => 'project_comment',
            'comment_approved' => 1
        );

        // Insert comment
        $comment_id = wp_insert_comment($comment_data);

        if ($comment_id) {
            // Handle milestone
            if ($is_milestone) {
                add_comment_meta($comment_id, '_is_milestone', '1');

                // Add milestone status
                add_comment_meta($comment_id, '_milestone_status', 'pending');
            }

            // Handle file attachments
            if (!empty($_FILES['comment_files'])) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $files = $_FILES['comment_files'];
                $files_array = array();

                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );

                        $_FILES = array('upload_file' => $file);
                        $attachment_id = media_handle_upload('upload_file', $project_id);

                        if (!is_wp_error($attachment_id)) {
                            $files_array[] = $attachment_id;
                        }
                    }
                }

                if (!empty($files_array)) {
                    add_comment_meta($comment_id, '_comment_files', $files_array);
                }
            }
        }

        return $comment_id;
    }

    /**
     * Get all files attached to project comments
     *
     * @param int $project_id The ID of the project
     * @return array Array of attachment objects with file details
     */
    public function get_project_files($project_id)
    {
        // Get all comments for this project
        $comments = get_comments(array(
            'post_id' => $project_id,
            'type' => 'project_comment',
            'status' => 'approve'
        ));

        $files = array();

        foreach ($comments as $comment) {
            // Get files attached to this comment
            $comment_files = get_comment_meta($comment->comment_ID, '_comment_files', true);

            if (!empty($comment_files) && is_array($comment_files)) {
                foreach ($comment_files as $attachment_id) {
                    $attachment = get_post($attachment_id);

                    if ($attachment) {
                        $file_url = wp_get_attachment_url($attachment_id);
                        $file_type = wp_check_filetype(get_attached_file($attachment_id));
                        $file_size = filesize(get_attached_file($attachment_id));

                        $files[] = array(
                            'id' => $attachment_id,
                            'name' => $attachment->post_title,
                            'url' => $file_url,
                            'type' => $file_type['ext'],
                            'size' => size_format($file_size),
                            'date' => $attachment->post_date,
                            'comment_id' => $comment->comment_ID,
                            'comment_author' => $comment->comment_author,
                            'comment_date' => $comment->comment_date
                        );
                    }
                }
            }
        }

        return $files;
    }

    public function display_project_files($project_id)
    {
        $files = $this->get_project_files($project_id);

        if (empty($files)) {
            echo '<p>No files attached to this project.</p>';
            return;
        }

      

        foreach ($files as $file) {
?>
            <li class="project-file">
                <div class="file-info">
                    <a href="<?php echo esc_url($file['url']); ?>" target="_blank" class="file-name">
                        <?php echo esc_html($file['name']); ?>
                    </a>
                    <span class="file-meta">
                        <?php
                        echo esc_html($file['type']) . ' | ' .
                            esc_html($file['size']) . ' | ' .
                            'Uploaded by ' . esc_html($file['comment_author']) . ' on ' .
                            date('M j, Y', strtotime($file['comment_date']));
                        ?>
                    </span>
                </div>
            </li>
<?php
        }

      
    }

    // Get milestones for a project
    public function get_milestones($project_id)
    {
        $milestones = get_post_meta($project_id, $this->meta_key, true);
        return !empty($milestones) ? $milestones : array();
    }

    // Save a new milestone or update existing one
    public function save_milestone($project_id, $milestone_data)
    {
        $milestones = $this->get_milestones($project_id);

        if (isset($milestone_data['id'])) {
            // Update existing milestone
            foreach ($milestones as $key => $milestone) {
                if ($milestone['id'] === $milestone_data['id']) {
                    $milestones[$key] = array_merge($milestone, $milestone_data);
                    break;
                }
            }
        } else {
            // Add new milestone
            $milestone_data['id'] = uniqid();
            $milestone_data['status'] = 'pending';
            $milestone_data['client_approval'] = false;
            $milestone_data['freelancer_approval'] = false;
            $milestones[] = $milestone_data;
        }

        return update_post_meta($project_id, $this->meta_key, $milestones);
    }

    // Handle milestone approval
    public function approve_milestone($project_id, $milestone_id, $user_type)
    {
        $milestones = $this->get_milestones($project_id);

        foreach ($milestones as &$milestone) {
            if ($milestone['id'] === $milestone_id) {
                if ($user_type === 'client') {
                    $milestone['client_approval'] = true;
                } else {
                    $milestone['freelancer_approval'] = true;
                }

                // Check if both parties approved
                if ($milestone['client_approval'] && $milestone['freelancer_approval']) {
                    $milestone['status'] = 'approved';

                    // Create WooCommerce order
                    $order_id = $this->create_milestone_order($project_id, $milestone);
                    if ($order_id) {
                        $milestone['order_id'] = $order_id;
                    }
                }
                break;
            }
        }

        return update_post_meta($project_id, $this->meta_key, $milestones);
    }

    // In the WorkScout_Project_Milestones class
    public function ajax_approve_milestone()
    {
        // Verify nonce
        check_ajax_referer('workscout_milestone_nonce', 'nonce');

        $project_id = intval($_POST['project_id']);
        $milestone_id = sanitize_text_field($_POST['milestone_id']);

        // Check if user has permission
        if (!$this->can_approve_milestone($project_id)) {
            wp_send_json_error('Permission denied');
            return;
        }

        // Determine if current user is client or freelancer
        $user_type = $this->get_user_type($project_id);

        $result = $this->approve_milestone($project_id, $milestone_id, $user_type);

        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }



    private function create_milestone_order($project_id, $milestone)
    {
        // Get project details
        $project = get_post($project_id);
        $client_id = $project->post_author;
        $freelancer_id = get_post_meta($project_id, 'freelancer_id', true);

        try {
            // Create the order
            $order = wc_create_order([
                'customer_id' => $client_id,  // Set client as customer
                'status' => 'pending'
            ]);

            // Create or get the product for milestone payment
            $product_id = $this->get_or_create_milestone_product();

            // Add the product to the order
            $order->add_product(wc_get_product($product_id), 1, [
                'subtotal' => $milestone['amount'],
                'total' => $milestone['amount']
            ]);

            // Add milestone and project details as order meta
            $order->update_meta_data('project_id', $project_id);
            $order->update_meta_data('project_title', $project->post_title);
            $order->update_meta_data('milestone_id', $milestone['id']);
            $order->update_meta_data('milestone_title', $milestone['title']);
            $order->update_meta_data('freelancer_id', $freelancer_id);

            // Update order totals
            $order->calculate_totals();

            // Add order note
            $order->add_order_note(
                sprintf(
                    'Order created for project milestone: %s (Project: %s)',
                    $milestone['title'],
                    $project->post_title
                )
            );

            // Save the order
            $order->save();

            // Maybe send notification email to client
            do_action('workscout_milestone_order_created', $order->get_id(), $project_id, $milestone);

            return $order->get_id();
        } catch (Exception $e) {
            error_log('Failed to create milestone order: ' . $e->getMessage());
            return false;
        }
    }


    private function get_or_create_milestone_product()
    {
        // Try to get existing milestone product
        $product_id = get_option('workscout_milestone_product_id');

        if ($product_id && wc_get_product($product_id)) {
            return $product_id;
        }

        // Create new product if doesn't exist
        $product = new WC_Product_Simple();
        $product->set_name('Project Milestone Payment');
        $product->set_status('private');  // Hide from shop
        $product->set_catalog_visibility('hidden');  // Hide from search and catalog
        $product->set_price(0);  // Base price, will be adjusted per milestone
        $product->set_virtual(true);  // No shipping needed
        $product->set_downloadable(false);
        $product->save();

        // Save product ID for future use
        update_option('workscout_milestone_product_id', $product->get_id());

        return $product->get_id();
    }


    // Hook for order status changes
    public function handle_order_status_change($order_id, $old_status, $new_status)
    {
        $order = wc_get_order($order_id);
        if (!$order) return;

        // Get milestone info from order
        $project_id = $order->get_meta('project_id');
        $milestone_id = $order->get_meta('milestone_id');

        if (!$project_id || !$milestone_id) return;

        // Update milestone status based on order status
        $milestones = $this->get_milestones($project_id);

        foreach ($milestones as &$milestone) {
            if ($milestone['id'] === $milestone_id) {
                switch ($new_status) {
                    case 'completed':
                    case 'processing':
                        $milestone['payment_status'] = 'paid';
                        break;

                    case 'refunded':
                        $milestone['payment_status'] = 'refunded';
                        break;

                    case 'failed':
                        $milestone['payment_status'] = 'failed';
                        break;
                }
                break;
            }
        }

        update_post_meta($project_id, $this->meta_key, $milestones);
    }

    // Get total project value from custom field
    private function get_project_value($project_id)
    {
        return floatval(get_post_meta($project_id, 'project_value', true));
    }

    // Calculate total percentage of existing milestones
    public function get_total_milestone_percentage($project_id, $exclude_milestone_id = null)
    {
        $milestones = $this->get_milestones($project_id);
        $total = 0;

        foreach ($milestones as $milestone) {
            // Skip the milestone we're updating if provided
            // check if milestone has percentage
            if (!isset($milestone['percentage'])) {
                continue;
                // check if milestone has amount
            }
            if ($exclude_milestone_id && $milestone['id'] === $exclude_milestone_id) {
                continue;
            }
            $total += floatval($milestone['percentage']);
        }

        return $total;
    }

    // Validate milestone percentage
    private function validate_milestone_percentage($project_id, $new_percentage, $milestone_id = null)
    {
        $current_total = $this->get_total_milestone_percentage($project_id, $milestone_id);
        $total_with_new = $current_total + floatval($new_percentage);

        return $total_with_new <= 100;
    }

    // Calculate amount based on percentage
    private function calculate_amount_from_percentage($project_id, $percentage)
    {
        $project_value = $this->get_project_value($project_id);
        return ($project_value * floatval($percentage)) / 100;
    }

    public function get_milestone_payment_link($milestone)
    {
        if (!isset($milestone['order_id'])) {
            return '';
        }

        $order = wc_get_order($milestone['order_id']);
        if (!$order) {
            return '';
        }

        // Check if current user is the client
        if (get_current_user_id() !== $order->get_customer_id()) {
            return '';
        }

        switch ($order->get_status()) {
            case 'pending':
                return sprintf(
                    '<a href="%s" class="button pay-milestone">Pay Now</a>',
                    esc_url($order->get_checkout_payment_url())
                );

            case 'processing':
            case 'completed':
                return '<span class="milestone-paid">Payment Complete</span>';

            default:
                return sprintf(
                    '<span class="milestone-status">Order Status: %s</span>',
                    esc_html($order->get_status())
                );
        }
    }

    // Helper function to determine user type
    public function get_user_type($project_id)
    {
        $current_user_id = get_current_user_id();
        $project = get_post($project_id);
        $freelancer = get_post_meta($project_id, '_freelancer_id', true);
        $employer = get_post_meta($project_id, '_employer_id', true);

        if ($current_user_id === intval($employer)) {
            return 'client';
        } elseif ($current_user_id === intval($freelancer)) {
            return 'freelancer';
        }

        return false;
    }

    // Check if user can approve milestone
    private function can_approve_milestone($project_id)
    {
        $user_type = $this->get_user_type($project_id);
        return $user_type !== false;
    }

    // AJAX handler for saving milestone
    public function ajax_save_milestone()
    {
        check_ajax_referer('workscout_milestone_nonce', 'nonce');

        $project_id = intval($_POST['project_id']);
        $percentage = floatval($_POST['percentage']);
        $milestone_id = isset($_POST['milestone_id']) ? sanitize_text_field($_POST['milestone_id']) : null;

      


        // Check if user has permission
        if (!$this->can_edit_milestones($project_id)) {
            wp_send_json_error('Permission denied');
            return;
        }
        // Validate percentage
        if (!$this->validate_milestone_percentage($project_id, $percentage, $milestone_id)) {
            wp_send_json_error([
                'message' => 'Total milestone percentages cannot exceed 100%',
                'current_total' => $this->get_total_milestone_percentage($project_id, $milestone_id)
            ]);
            return;
        }

        $amount = $this->calculate_amount_from_percentage($project_id, $percentage);

        $milestone_data = [
            'title' => sanitize_text_field($_POST['title']),
            'description' => wp_kses_post($_POST['description']),
            'percentage' => $percentage,
            'amount' => $amount,
            'due_date' => sanitize_text_field($_POST['due_date'])
        ];

        if (isset($_POST['milestone_id'])) {
            $milestone_data['id'] = sanitize_text_field($_POST['milestone_id']);
        }
        $result = $this->save_milestone($project_id, $milestone_data);

        if ($result) {
            wp_send_json_success([
                'milestone' => $milestone_data,
                'remaining_percentage' => 100 - $this->get_total_milestone_percentage($project_id)
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to save milestone']);
        }
    }

    // AJAX handler to get remaining percentage
    public function ajax_get_remaining_percentage()
    {
        check_ajax_referer('workscout_milestone_nonce', 'nonce');

        $project_id = intval($_POST['project_id']);
        $milestone_id = isset($_POST['milestone_id']) ? sanitize_text_field($_POST['milestone_id']) : null;

        $remaining = 100 - $this->get_total_milestone_percentage($project_id, $milestone_id);

        wp_send_json_success(['remaining' => $remaining]);
    }

    // Check if user can edit milestones
    private function can_edit_milestones($project_id)
    {
   
        $current_user_id = get_current_user_id();
        $freelancer = get_post_meta($project_id, '_freelancer_id', true);
        $employer = get_post_meta($project_id, '_employer_id', true);
        return ( in_array($current_user_id, array($employer, $freelancer)));
    }

    // Get milestone status badge HTML
    public function get_status_badge($status)
    {
        $badges = array(
            'pending' => '<span class="badge badge-warning">Pending Approval</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'completed' => '<span class="badge badge-primary">Completed</span>'
        );

        return isset($badges[$status]) ? $badges[$status] : '';
    }
}
