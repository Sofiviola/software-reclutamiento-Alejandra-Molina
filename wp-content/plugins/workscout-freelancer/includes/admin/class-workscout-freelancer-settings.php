<?php

/**
 * File containing the class WP_Resume_Manager_Settings.
 *
 * @package wp-job-manager-resumes
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * WP_Resume_Manager_Settings class.
 */
class Workscout_Freelancer_Settings extends WP_Job_Manager_Settings
{

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->settings_group = 'workscout-freelancer';
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_action_update', [$this, 'pre_process_settings_save']);
        
    }

    /**
     * Init_settings function.
     *
     * @access protected
     * @return void
     */
    protected function init_settings()
    {
        // Prepare roles option.
        $roles         = get_editable_roles();
        $account_roles = [];

        foreach ($roles as $key => $role) {
            if ('administrator' === $key) {
                continue;
            }
            $account_roles[$key] = $role['name'];
        }

        $empty_trash_days = defined('EMPTY_TRASH_DAYS ') ? EMPTY_TRASH_DAYS : 30;
        if (empty($empty_trash_days) || $empty_trash_days < 0) {
            $trash_description = __('They will then need to be manually removed from the trash', 'workscout-freelancer');
        } else {
            // translators: Placeholder %d is the number of days before items are removed from trash.
            $trash_description = sprintf(__('They will then be permanently deleted after %d days.', 'workscout-freelancer'), $empty_trash_days);
        }

        $this->settings = apply_filters(
            'workscout_freelancer_settings',
            [
                'tasks'    => [
                    __('Task Listings', 'workscout-freelancer'),
                    [
                        // [
                        //     'name'        => 'task_per_page',
                        //     'std'         => '10',
                        //     'placeholder' => '',
                        //     'label'       => __('Task Per Page', 'workscout-freelancer'),
                        //     'cb_label'       => __('Task Per Page', 'workscout-freelancer'),
                        //     'desc'        => __('How many task should be shown per page by default?', 'workscout-freelancer'),
                        //     'attributes'  => [],
                        // ],
                        // [
                        //     'name'        => 'task_ajax_browsing',
                        //     'std'         => '10',
                        //     'placeholder' => '',
                        //     'label'       => __('Use ajax browsing', 'workscout-freelancer'),
                        //     'cb_label'       => __('Use ajax browsing', 'workscout-freelancer'),
                        //     'desc'        => __('If enabled search results will be automatically updated after every change', 'workscout-freelancer'),
                        //     'attributes'  => [],
                        //     'type'       => 'checkbox',
                        // ],
                        // [
                        //     'name'       => 'task_list_layout',
                        //     'std'        => 'list-1',
                        //     'label'      => __('Task list layout', 'workscout-freelancer'),
                        //     'cb_label'      => __('Task list layout', 'workscout-freelancer'),
                        //     'desc'       => __('Set layout for list of tasks', 'workscout-freelancer'),
                        //     'type'       => 'radio',
                        //     'options'    => [
                        //         'list-1'            => __('List layout 1', 'workscout-freelancer'),
                        //         'list-2'           => __('List layout 2', 'workscout-freelancer'),
                        //         'grid' => __('Grid', 'workscout-freelancer'),
                        //         'full' => __('Full page grid', 'workscout-freelancer'),
                        //     ],
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'task_list_layout_sidebar',
                        //     'std'        => 'left',
                        //     'label'      => __('Sidebar side', 'workscout-freelancer'),
                        //     'cb_label'      => __('Does not apply to full page', 'workscout-freelancer'),
                        //     'desc'       => __('Set sidebar side for list of tasks', 'workscout-freelancer'),
                        //     'type'       => 'radio',
                        //     'options'    => [
                                
                        //         'left'            => __('Left sidebar', 'workscout-freelancer'),
                        //         'right'           => __('Right sidebar', 'workscout-freelancer'),
                              
                        //     ],
                        //     'attributes' => [],
                        // ],
                        [
                            'name'       => 'task_paid_listings_flow',
                            'std'        => 'before',
                            'label'      => __('Paid listings flow', 'workscout-freelancer'),
                            'cb_label'      => __('Paid listings flow', 'workscout-freelancer'),
                            'desc'       => __('Set when the package option will appear', 'workscout-freelancer'),
                            'type'       => 'radio',
                            'options'    => [
                                'before'            => __('Before submit form', 'workscout-freelancer'),
                                'after'           => __('After submit form', 'workscout-freelancer'),
                                
                            ],
                            'attributes' => [],
                        ],
                        // [
                        //     'name'       => 'resume_manager_enable_categories',
                        //     'std'        => '0',
                        //     'label'      => __('Categories', 'workscout-freelancer'),
                        //     'cb_label'   => __('Enable resume categories', 'workscout-freelancer'),
                        //     'desc'       => __('Choose whether to enable resume categories. Categories must be setup by an admin for users to choose during job submission.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_enable_default_category_multiselect',
                        //     'std'        => '0',
                        //     'label'      => __('Multi-select Categories', 'workscout-freelancer'),
                        //     'cb_label'   => __('Enable category multiselect by default', 'workscout-freelancer'),
                        //     'desc'       => __('If enabled, the category select box will default to a multiselect on the [resumes] shortcode.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'    => 'resume_manager_category_filter_type',
                        //     'std'     => 'any',
                        //     'label'   => __('Category Filter Type', 'workscout-freelancer'),
                        //     'desc'    => __('Choose how to filter resumes when Multi-select Categories option is enabled.', 'workscout-freelancer'),
                        //     'type'    => 'select',
                        //     'options' => [
                        //         'any' => __('Resumes will be shown if within ANY selected category', 'workscout-freelancer'),
                        //         'all' => __('Resumes will be shown if within ALL selected categories', 'workscout-freelancer'),
                        //     ],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_enable_skills',
                        //     'std'        => '0',
                        //     'label'      => __('Skills', 'workscout-freelancer'),
                        //     'cb_label'   => __('Enable candidate skills', 'workscout-freelancer'),
                        //     'desc'       => __('Choose whether to enable the candidate skills field. Skills can be added by users during resume submission.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'        => 'resume_manager_max_skills',
                        //     'std'         => '',
                        //     'label'       => __('Maximum Skills', 'workscout-freelancer'),
                        //     'placeholder' => __('Unlimited', 'workscout-freelancer'),
                        //     'desc'        => __('Enter the number of skills per resume submission you wish to allow, or leave blank for unlimited skills.', 'workscout-freelancer'),
                        //     'type'        => 'input',
                        // ],
                        // [
                        //     'name'       => 'resume_manager_enable_resume_upload',
                        //     'std'        => '0',
                        //     'label'      => __('Resume Upload', 'workscout-freelancer'),
                        //     'cb_label'   => __('Enable resume upload', 'workscout-freelancer'),
                        //     'desc'       => __('Choose whether to allow candidates to upload a resume file.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_delete_files_on_resume_deletion',
                        //     'std'        => '0',
                        //     'label'      => __('Delete uploaded files', 'workscout-freelancer'),
                        //     'cb_label'   => __('Delete uploaded files when a resume is deleted', 'workscout-freelancer'),
                        //     'desc'       => __('Choose whether to deleted uploaded files when a resume is deleted and removed from the trash.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_erasure_request_removes_resumes',
                        //     'std'        => '0',
                        //     'label'      => __('Personal Data Erasure', 'workscout-freelancer'),
                        //     'cb_label'   => __('Remove resumes on account erasure requests', 'workscout-freelancer'),
                        //     'desc'       => sprintf(
                        //         // translators: Placeholder %1$s is the URL to the WP Admin page that handles account erasure requests. %2$s is trash notification.
                        //         __('If enabled, resumes with a matching email address will be sent to the trash during <a href="%1$s">personal data erasure requests</a>. %2$s', 'workscout-freelancer'),
                        //         esc_url(admin_url('tools.php?page=remove_personal_data')),
                        //         $trash_description
                        //     ),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                    ],
                ],
                'task_submission'  => [
                    __('Task Submission', 'workscout-freelancer'),
                    [
                        // [
                        //     'name'       => 'resume_manager_user_requires_account',
                        //     'std'        => '1',
                        //     'label'      => __('Account Required', 'workscout-freelancer'),
                        //     'cb_label'   => __('Submitting listings requires an account', 'workscout-freelancer'),
                        //     'desc'       => __('If disabled, non-logged in users will be able to submit listings without creating an account. Please note that this will prevent non-registered users from being able to edit their listings at a later date.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_enable_registration',
                        //     'std'        => '1',
                        //     'label'      => __('Account Creation', 'workscout-freelancer'),
                        //     'cb_label'   => __('Allow account creation', 'workscout-freelancer'),
                        //     'desc'       => __('If enabled, non-logged in users will be able to create an account by entering their email address on the resume submission form.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_generate_username_from_email',
                        //     'std'        => '1',
                        //     'label'      => __('Account Username', 'workscout-freelancer'),
                        //     'cb_label'   => __('Automatically Generate Username from Email Address', 'workscout-freelancer'),
                        //     'desc'       => __('If enabled, a username will be generated from the first part of the user email address. Otherwise, a username field will be shown.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'       => 'resume_manager_use_standard_password_setup_email',
                        //     'std'        => '1',
                        //     'label'      => __('Account Password', 'workscout-freelancer'),
                        //     'cb_label'   => __('Use WordPress\' default behavior and email new users link to set a password', 'workscout-freelancer'),
                        //     'desc'       => __('If enabled, an email will be sent to the user with their username and a link to set their password. Otherwise, a password field will be shown and their email address won\'t be verified.', 'workscout-freelancer'),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // [
                        //     'name'    => 'resume_manager_registration_role',
                        //     'std'     => 'candidate',
                        //     'label'   => __('Account Role', 'workscout-freelancer'),
                        //     'desc'    => __('If you enable registration on your submission form, choose a role for the new user.', 'workscout-freelancer'),
                        //     'type'    => 'select',
                        //     'options' => $account_roles,
                        // ],
                        [
                            'name'       => 'workscout_freelancer_task_submission_requires_approval',
                            'std'        => '1',
                            'label'      => __('Approval Required', 'workscout-freelancer'),
                            'cb_label'   => __('New submissions require admin approval', 'workscout-freelancer'),
                            'desc'       => __('If enabled, new submissions will be inactive, pending admin approval.', 'workscout-freelancer'),
                            'type'       => 'checkbox',
                            'attributes' => [],
                        ],
                        [
                            'name'       => 'workscout_freelancer_user_can_edit_pending_submissions',
                            'std'        => '0',
                            'label'      => __('Allow Pending Edits', 'workscout-freelancer'),
                            'cb_label'   => __('Allow editing of pending tasks', 'workscout-freelancer'),
                            'desc'       => __('Users can continue to edit pending tasks until they are approved by an admin.', 'workscout-freelancer'),
                            'type'       => 'checkbox',
                            'attributes' => [],
                        ],
                        [
                            'name'       => 'workscout_freelancer_user_edit_published_submissions',
                            'std'        => 'yes',
                            'label'      => __('Allow Published Edits', 'workscout-freelancer'),
                            'cb_label'   => __('Allow editing of published tasks', 'workscout-freelancer'),
                            'desc'       => __('Choose whether published tasks can be edited and if edits require admin approval. When moderation is required, the original resume will be unpublished while edits await admin approval.', 'workscout-freelancer'),
                            'type'       => 'radio',
                            'options'    => [
                                'no'            => __('Users cannot edit', 'workscout-freelancer'),
                                'yes'           => __('Users can edit without admin approval', 'workscout-freelancer'),
                                'yes_moderated' => __('Users can edit, but edits require admin approval', 'workscout-freelancer'),
                            ],
                            'attributes' => [],
                        ],
                        // [
                        //     'name'        => 'resume_manager_submission_duration',
                        //     'std'         => '',
                        //     'label'       => __('Listing Duration', 'workscout-freelancer'),
                        //     'desc'        => __('How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire. Expired listings must be relisted to become visible.', 'workscout-freelancer'),
                        //     'attributes'  => [],
                        //     'placeholder' => __('Never expire', 'workscout-freelancer'),
                        // ],
                        // [
                        //     'name'        => 'resume_manager_autohide',
                        //     'std'         => '',
                        //     'label'       => __('Auto-hide Resumes', 'workscout-freelancer'),
                        //     'desc'        => __('How many <strong>days</strong> un-modified resumes should be published before being hidden. Can be left blank to never hide resumes automatically. Candidates can re-publish hidden resumes form their dashboard.', 'workscout-freelancer'),
                        //     'attributes'  => [],
                        //     'placeholder' => __('Never auto-hide', 'workscout-freelancer'),
                        // ],
                        // [
                        //     'name'        => 'resume_manager_submission_limit',
                        //     'std'         => '',
                        //     'label'       => __('Listing Limit', 'workscout-freelancer'),
                        //     'desc'        => __('How many listings are users allowed to post. Can be left blank to allow unlimited listings per account.', 'workscout-freelancer'),
                        //     'attributes'  => [],
                        //     'placeholder' => __('No limit', 'workscout-freelancer'),
                        // ],
                        // [
                        //     'name'       => 'resume_manager_show_agreement_resume_submission',
                        //     'std'        => '0',
                        //     'label'      => __('Terms and Conditions Checkbox', 'workscout-freelancer'),
                        //     'cb_label'   => __('Enable required Terms and Conditions checkbox on the form', 'workscout-freelancer'),
                        //     'desc'       => sprintf(
                        //         // translators: Placeholder %s is the URL to the page in WP Job Manager's settings to set the pages.
                        //         __('Require a Terms and Conditions checkbox to be marked before a resume can be submitted. The linked page can be set from the <a href="%s">WP Job Manager\'s settings</a>.', 'workscout-freelancer'),
                        //         esc_url(admin_url('edit.php?post_type=job_listing&page=job-manager-settings#settings-job_pages'))
                        //     ),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                        // 'recaptcha' => [
                        //     'name'       => 'resume_manager_enable_recaptcha_resume_submission',
                        //     'std'        => '0',
                        //     'label'      => __('reCAPTCHA', 'workscout-freelancer'),
                        //     'cb_label'   => __('Display a reCAPTCHA field on resume submission form.', 'workscout-freelancer'),
                        //     'desc'       => sprintf(
                        //         // translators: Placeholder %s is the URL to the page in WP Job Manager's settings to make the change.
                        //         __('This will help prevent bots from submitting resumes. You must have entered a valid site key and secret key in <a href="%s">WP Job Manager\'s settings</a>.', 'workscout-freelancer'),
                        //         esc_url(admin_url('edit.php?post_type=job_listing&page=job-manager-settings#settings-recaptcha'))
                        //     ),
                        //     'type'       => 'checkbox',
                        //     'attributes' => [],
                        // ],
                    ],
                ],
              
                'task_pages'       => [
                    __('Pages', 'workscout-freelancer'),
                    [
                        [
                            'name'  => 'workscout_freelancer_submit_task_form_page_id',
                            'std'   => '',
                            'label' => __('Submit Task Page', 'workscout-freelancer'),
                            'desc'  => __('Select the page where you have placed the [workscout_submit_task] shortcode. This lets the plugin know where the form is located.', 'workscout-freelancer'),
                            'type'  => 'page',
                        ],
                        [
                            'name'  => 'workscout_freelancer_task_dashboard_page_id',
                            'std'   => '',
                            'label' => __('Manage Task Page', 'workscout-freelancer'),
                            'desc'  => __('Select the page where you have placed the [workscout_task_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'workscout-freelancer'),
                            'type'  => 'page',
                        ],
               
                        [
                            'name'  => 'workscout_freelancer_manage_my_bids_page_id',
                            'std'   => '',
                            'label' => __('Manage My Bids Page', 'workscout-freelancer'),
                            'desc'  => __('Select the page where you have placed the [workscout_my_bids] shortcode. This lets the plugin know where the dashboard is located.', 'workscout-freelancer'),
                            'type'  => 'page',
                        ],

                        [
                            'name'  => 'workscout_freelancer_manage_my_project_page_id',
                            'std'   => '',
                            'label' => __('Freelancer Projects Page', 'workscout-freelancer'),
                            'desc'  => __('Select the page where you have placed the [workscout_freelancer_project_view] shortcode. This lets the plugin know where the project list is located.', 'workscout-freelancer'),
                            'type'  => 'page',
                        ],
                    ],
                ],
            ]
        );

    }

    /**
     * Outputs the capabilities or roles input field.
     *
     * @param array    $option              Option arguments for settings input.
     * @param string[] $attributes          Attributes on the HTML element. Strings must already be escaped.
     * @param mixed    $value               Current value.
     * @param string   $ignored_placeholder We set the placeholder in the method. This is ignored.
     */
    protected function input_capabilities($option, $attributes, $value, $ignored_placeholder)
    {
        $value                 = self::capabilities_string_to_array($value);
        $option['options']     = self::get_capabilities_and_roles($value);
        $option['placeholder'] = esc_html__('Everyone (Public)', 'workscout-freelancer');

?>
        <select id="setting-<?php echo esc_attr($option['name']); ?>" class="regular-text settings-role-select" name="<?php echo esc_attr($option['name']); ?>[]" multiple="multiple" data-placeholder="<?php echo esc_attr($option['placeholder']); ?>" <?php
                                                                                                                                                                                                                                                                echo implode(' ', $attributes); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                                                                                                                                                                                                                                ?>>
            <?php
            foreach ($option['options'] as $key => $name) {
                echo '<option value="' . esc_attr($key) . '" ' . selected(in_array($key, $value, true) ? $key : null, $key, false) . '>' . esc_html($name) . '</option>';
            }
            ?>
        </select>
<?php

        if (!empty($option['desc'])) {
            echo ' <p class="description">' . wp_kses_post($option['desc']) . '</p>';
        }
    }

    /**
     * Role settings should be saved as a comma-separated list.
     */
    public function pre_process_settings_save()
    {
        $screen = get_current_screen();

        if (!$screen || 'options' !== $screen->id) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Settings save will handle the nonce check.
        if (!isset($_POST['option_page']) || 'workscout-freelancer' !== $_POST['option_page']) {
            return;
        }

        $capabilities_fields = [
            'resume_manager_view_name_capability',
            'resume_manager_browse_resume_capability',
            'resume_manager_view_resume_capability',
            'resume_manager_contact_resume_capability',
        ];
        foreach ($capabilities_fields as $capabilities_field) {
            // phpcs:disable WordPress.Security.NonceVerification.Missing -- Settings save will handle the nonce check.
            if (isset($_POST[$capabilities_field])) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by `WP_Resume_Manager_Settings::capabilities_array_to_string()`
                $input_capabilities_field_value = wp_unslash($_POST[$capabilities_field]);
                if (is_array($input_capabilities_field_value)) {
                    $_POST[$capabilities_field] = self::capabilities_array_to_string($input_capabilities_field_value);
                }
            }
            // phpcs:enable WordPress.Security.NonceVerification.Missing
        }
    }

    /**
     * Convert list of capabilities and roles into array of values.
     *
     * @param string $value Comma separated list of capabilities and roles.
     * @return array
     */
    private static function capabilities_string_to_array($value)
    {
        return array_filter(
            array_map(
                function ($value) {
                    return trim(sanitize_text_field($value));
                },
                explode(',', $value)
            )
        );
    }

    /**
     * Convert array of capabilities and roles into a comma separated list.
     *
     * @param array $value Array of capabilities and roles.
     * @return string
     */
    private static function capabilities_array_to_string($value)
    {
        if (!is_array($value)) {
            return '';
        }

        $caps = array_filter(array_map('sanitize_text_field', $value));

        return implode(',', $caps);
    }

    /**
     * Get the list of roles and capabilities to use in select dropdown.
     *
     * @param array $caps Selected capabilities to ensure they show up in the list.
     * @return array
     */
    private static function get_capabilities_and_roles($caps = [])
    {
        $capabilities_and_roles = [];
        $roles                  = get_editable_roles();

        foreach ($roles as $key => $role) {
            $capabilities_and_roles[$key] = $role['name'];
        }

        // Go through custom user selected capabilities and add them to the list.
        foreach ($caps as $value) {
            if (isset($capabilities_and_roles[$value])) {
                continue;
            }
            $capabilities_and_roles[$value] = $value;
        }

        return $capabilities_and_roles;
    }
}
