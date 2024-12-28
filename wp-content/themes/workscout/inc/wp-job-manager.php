<?php


add_post_type_support('job_listing', 'excerpt');
add_post_type_support('resume', 'excerpt');

function workscout_job_manager_output_jobs_defaults($defaults)
{
    $job_page = get_option('job_manager_jobs_page_id');
    if (!empty($job_page) && is_page($job_page)) {
        $defaults['show_filters'] = false;
    }
    return $defaults;
}
add_filter('job_manager_output_jobs_defaults', 'workscout_job_manager_output_jobs_defaults');


remove_shortcode('jobs');
remove_shortcode('resumes');


/* sending user to sign up to Login page if exists */
add_filter('submit_job_form_login_url', 'workscout_custom_login_url');
add_filter('job_manager_job_dashboard_login_url', 'workscout_custom_login_url');
add_filter('submit_resume_form_login_url', 'workscout_custom_login_url');
add_filter('resume_manager_candidate_dashboard_login_url', 'workscout_custom_login_url');
add_filter('job_manager_alerts_login_url', 'workscout_custom_login_url');
add_filter('job_manager_bookmark_form_login_url', 'workscout_custom_login_url');
add_filter('job_manager_job_applications_login_required_message', 'workscout_custom_login_url');


function workscout_custom_login_url()
{

    $login_page = get_option('workscout_dashboard_page');
    if (empty($login_page)) {
        $login_page = wp_login_url(get_permalink());
    }


    return get_permalink($login_page);
}

/*remove bookmarks link*/
if (class_exists('WP_Job_Manager_Bookmarks')) {
    global $job_manager_bookmarks;
    remove_action('single_job_listing_meta_after', array($job_manager_bookmarks, 'bookmark_form'));
    remove_action('single_resume_start', array($job_manager_bookmarks, 'bookmark_form'));

    add_action('workscout_bookmark_hook', array($job_manager_bookmarks, 'bookmark_form'));
    add_action('workscout_bookmark_hook', array($job_manager_bookmarks, 'bookmark_form'));
}

/* register with role */

add_action('register_form', 'workscout_register_form');
function workscout_register_form()
{
    $role_status  = Kirki::get_option('workscout', 'pp_singup_role_status', false);
    $role_revert  = Kirki::get_option('workscout', 'pp_singup_role_revert', false);
    if (!$role_status) {
        global $wp_roles;
        echo '<label for="user_email">' . esc_html__('I want to register as', 'workscout') . '</label>';
        echo '<select name="role" class="input chosen-select">';
        if ($role_revert) {
            echo '<option value="candidate">' . esc_html__("Candidate", "workscout") . '</option>';
        }
        echo '<option value="employer">' . esc_html__("Employer", "workscout") . '</option>';
        if (!$role_revert) {
            echo '<option value="candidate">' . esc_html__("Candidate", "workscout") . '</option>';
        }

        echo '</select>';
    }
}


// //2. Add validation.
add_filter('registration_errors', 'workscout_registration_errors', 10, 3);
function workscout_registration_errors($errors, $sanitized_user_login, $user_email)
{

    if (empty($_POST['role']) || ! empty($_POST['role']) && trim($_POST['role']) == '') {
        $errors->add('role_error', esc_html__('<strong>ERROR</strong>: You must include a role.', 'workscout'));
    }

    return $errors;
}

//3. Finally, save our extra registration user meta.
add_action('user_register', 'workscout_user_register');
function workscout_user_register($user_id)
{

    if (isset($_POST['role'])) {
        $role = $_POST['role'];
        if (!in_array($role, array('employer', 'candidate'))) {
            $role = get_option('default_role');
        }

        $user_id = wp_update_user(array('ID' => $user_id, 'role' => $role));
    }
}


// Add comment support to the post type
add_filter('register_post_type_resume', 'register_post_type_resume_enable_comments');

function register_post_type_resume_enable_comments($post_type)
{
    $post_type['supports'][] = 'comments';
    return $post_type;
}





function custom_job_manager_get_listings_result($result, $jobs)
{
    $result['post_count'] = $jobs->found_posts;
    return $result;
}
add_filter('job_manager_get_listings_result', 'custom_job_manager_get_listings_result', 10, 2);



function custom_default_company_logo($logo_url)
{
    $image =  Kirki::get_option('workscout', 'pp_jobs_default_image_upload', '');
    if ($image) {
        return $image;
    }
    return $logo_url;
}
add_filter('job_manager_default_company_logo', 'custom_default_company_logo');

/*
function has_active_job_package_capability_check( $allcaps, $cap, $args ) {
	// Only interested in has_active_job_package
	if ( empty( $cap[0] ) || $cap[0] !== 'has_active_job_package' || ! function_exists( 'wc_paid_listings_get_user_packages' ) ) {
		return $allcaps;
	}

	$user_id  = $args[1];
	$packages = wc_paid_listings_get_user_packages( $user_id, 'job_listing' );

	// Has active package
	if ( is_array( $packages ) && sizeof( $packages ) > 0 ) {
		$allcaps[ $cap[0] ] = true;
	}

	return $allcaps;
}

add_filter('job_manager_candidates_can_apply','block_applying');
function block_applying($can_apply ){
	if(current_user_can( 'has_active_job_package' )) {
		$can_apply = true;
	} else {
		$can_apply = false;
	}
	return $can_apply;

}


add_filter('job_manager_candidates_can_apply','block_employer_applying');
function block_employer_applying($can_apply ){
    $current_user = wp_get_current_user();
    $user_id = get_current_user_id();
    $roles = $current_user->roles;
    $role = array_shift( $roles ); 
	if($role ==  'employer' ) {
		$can_apply = false;
	} else {
		$can_apply = true;
	}
	return $can_apply;

}

 wp_get_current_user*/


// add_filter( 'job_manager_geolocation_endpoint', 'workscout_add_geolocation_key_to_endpoint' ); 
// function workscout_add_geolocation_key_to_endpoint( $endpoint ) { 

// 	$api_key = Kirki::get_option( 'workscout','pp_maps_browser_api', '');
// 	if(!empty($api_key)) {
// 		$endpoint = add_query_arg( 'key', $api_key, $endpoint ); 
// 		$endpoint = str_ireplace('http:', 'https:', $endpoint);
// 	}
// 	return $endpoint; 
// }add_action( 'single_company', 'mas_wpjmc_single_company_features', 20 );


remove_action('single_company', 'mas_wpjmc_single_company_header');
remove_action('single_company', 'mas_wpjmc_single_company_features', 20);
add_action('single_company', 'ws_mas_wpjmc_single_company_header', 10);
add_action('single_company', 'ws_mas_wpjmc_single_company_features', 20);
function ws_mas_wpjmc_single_company_header()
{
?>
    <div class="company-contact-details">
        <?php if (! (function_exists('twentynineteen_can_show_post_thumbnail') && twentynineteen_can_show_post_thumbnail())) : ?>
            <div class="company-data">
                <div class="company-logo">
                    <?php $logo =  get_the_company_logo(null, 'thumbnail') ? get_the_company_logo(null, 'thumbnail') : apply_filters('job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png'); ?>
                    <img src="<?php echo esc_url($logo) ?>" class="company-logo--image" alt="<?php the_title(); ?>">
                </div>
                <div class="company-data__content media-body">
                <?php
                the_title('<h1 class="company-title">', '</h1>');
            endif;

            if (! empty($company_tagline = mas_wpjmc_get_the_meta_data('_company_tagline'))) : ?>
                    <p class="company-data__content--list-item"><?php echo esc_html($company_tagline); ?></p>
                <?php endif;
            if (! empty(mas_wpjmc_get_the_meta_data('_company_website'))  || ! empty(mas_wpjmc_get_the_meta_data('_company_email')) || ! empty(mas_wpjmc_get_the_meta_data('_company_twitter')) || ! empty(mas_wpjmc_get_the_meta_data('_company_facebook')) || ! empty(mas_wpjmc_get_the_meta_data('_company_phone'))) {
                ?>
                    <div class="company-data__content--list _company_tagline">

                        <?php if (! empty($company_website = mas_wpjmc_get_the_meta_data('_company_website'))) : ?>
                            <span class="company-data__content--list-item _company_website"><a class="website" href="<?php echo esc_url($company_website); ?>" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e('Website', 'workscout'); ?></a></span>
                        <?php endif; ?>
                        <?php if (! empty($company_email = mas_wpjmc_get_the_meta_data('_company_email'))) : ?>
                            <span class="company-data__content--list-item _company_email">
                                <a href="mailto:<?php echo ($company_email); ?>" target="_blank"><?php echo esc_html($company_email); ?></a>
                            </span>
                        <?php endif; ?>
                        <?php if (! empty($company_twitter = mas_wpjmc_get_the_meta_data('_company_twitter'))) : ?>
                            <span class="company-data__content--list-item _company_twitter">
                                <a href="<?php echo get_the_mas_company_twitter(); ?>">
                                    <i class="fa fa-twitter"></i>
                                    @<?php echo get_the_mas_company_twitter() ?>
                                </a></span>

                        <?php endif; ?>
                        <?php if (! empty($company_facebook = mas_wpjmc_get_the_meta_data('_company_facebook'))) : ?>
                            <span class="company-data__content--list-item _company_phone"><a href="<?php echo esc_url($company_facebook); ?>">
                                    <i class="fa fa-facebook"></i>
                                    <?php esc_html_e('Facebook', 'workscout'); ?>
                                </a></span>

                        <?php endif; ?>
                        <?php if (! empty($company_phone = mas_wpjmc_get_the_meta_data('_company_phone'))) : ?>
                            <span class="company-data__content--list-item _company_phone">
                                <a href="tel:<?php echo ($company_phone); ?>" target="_blank">
                                    <?php echo esc_html($company_phone); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php
            }
            if (! (function_exists('twentynineteen_can_show_post_thumbnail') && twentynineteen_can_show_post_thumbnail())) : ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
function ws_mas_wpjmc_single_company_features()
{
    $args = apply_filters('mas_wpjmc_single_company_features_args', array(
        'company_headquarters'  => array(
            'title' => esc_html__('Headquarters', 'workscout'),
            'content' => mas_wpjmc_get_the_meta_data('_company_headquarters'),
        ),
        'company_since'  => array(
            'title' => esc_html__('Founded', 'workscout'),
            'content' => mas_wpjmc_get_the_meta_data('_company_since'),
        ),
        'company_strength'  => array(
            'title' => esc_html__('Employees', 'workscout'),
            'content' => mas_wpjmc_get_taxomony_data('company_strength'),
        ),
        'company_category'  => array(
            'title' => esc_html__('Industry', 'workscout'),
            'content' => mas_wpjmc_get_taxomony_data('company_category'),
        ),
        'company_revenue'  => array(
            'title' => esc_html__('Revenue', 'workscout'),
            'content' => mas_wpjmc_get_taxomony_data('company_revenue'),
        ),
        'company_average_salary'  => array(
            'title' => esc_html__('Avg. Salary', 'workscout'),
            'content' => mas_wpjmc_get_taxomony_data('company_average_salary'),
        ),
    ));

    if (is_array($args) && count($args) > 0) {
        $i = 0;
        foreach ($args as $key => $arg) :
            if (isset($arg['content']) && !empty($arg['content'])) :
                $i++;
                break;
            endif;
        endforeach;
        if ($i > 0) :
    ?><div class="company-features">
                <div class="company-features__inner"><?php
                                                        foreach ($args as $key => $arg) :

                                                            if (isset($arg['content']) && !empty($arg['content'])) :
                                                        ?>
                            <div class="company-feature company-feature-<?php esc_attr_e($key) ?>" id="company-feature-<?php esc_attr_e($key) ?>">
                                <span class="company-feature__title"><?php echo wp_kses_post($arg['title']); ?></span>
                                <span class="company-feature__content"><?php echo wp_kses_post($arg['content']); ?></span>
                            </div>
                    <?php
                                                            endif;
                                                        endforeach;
                    ?>
                </div>
            </div><?php
                endif;
            }
        }

        function get_the_mas_company_twitter($post = null)
        {
            $post = get_post($post);
            if (! $post || 'company' !== $post->post_type) {
                return null;
            }

            $company_twitter = $post->_company_twitter;

            if (0 === strlen($company_twitter)) {
                return null;
            }

            if (0 === strpos($company_twitter, '@')) {
                $company_twitter = substr($company_twitter, 1);
            }

            return apply_filters('the_company_twitter', $company_twitter, $post);
        }

        add_filter('comment_form_defaults', 'workscout_companies_comment_form_defaults');
        function workscout_companies_comment_form_defaults($defaults)
        {
            if (is_singular('company')) {


                $defaults['title_reply'] = __('Add Review', 'workscout');
                $defaults['label_submit'] = __('Submit Review', 'workscout');
            }
            return $defaults;
        }

        add_filter('submit_job_form_fields', 'companies_custom_submit_job_form_fields');

        function companies_custom_submit_job_form_fields($fields)
        {
            if (class_exists('MAS_WP_Job_Manager_Company')) {
                unset($fields['company']['company_name']);
                unset($fields['company']['company_website']);
                unset($fields['company']['company_tagline']);
                unset($fields['company']['company_video']);
                unset($fields['company']['company_twitter']);
                unset($fields['company']['company_facebook']);
                unset($fields['company']['company_logo']);
            }

            return $fields;
        }


        add_filter('job_manager_job_listing_data_fields', 'workscout_companies_dropdownfix');

        function workscout_companies_dropdownfix($fields)
        {
            if (class_exists('MAS_WP_Job_Manager_Company')) {
                unset($fields['_company_id']);

                $options = array(
                    ''  => esc_html__('Private Listing', 'workscout'),
                );
                $companies = get_posts(array(
                    'post_type'     => 'company',
                    'orderby'       => 'title',
                    'order'         => 'ASC',
                    'numberposts'   => -1,
                ));

                if (!empty($companies)) {
                    foreach ($companies as $company) {
                        $options[$company->ID] = get_the_title($company);
                    }
                }

                $fields['_company_id'] = array(
                    'label'       => esc_html__('Company', 'workscout'),
                    'type'        => 'select',
                    'options'     => $options,
                    'priority'    => 2,

                );
            }

            return $fields;
        }



        /**
         * Get the company openings jobs
         */
        if (!function_exists('mas_wpjmc_workscout_get_the_company_job_listing')) {
            function mas_wpjmc_workscout_get_the_company_job_listing($post = null)
            {
                if (!is_object($post)) {
                    $post = get_post($post);
                }
                $query_args = array(
                    'posts_per_page'  => '10',
                    'post_type' => 'job_listing',
                    'meta_key' => '_company_id',
                    'meta_value' => $post->ID,
                    //   'nopaging' => true
                );

                if (1 === absint(get_option('job_manager_hide_filled_positions'))) {
                    $query_args['meta_query'][] = [
                        'key'     => '_filled',
                        'value'   => '1',
                        'compare' => '!=',
                    ];
                }

                return get_posts($query_args);
            }
        }


        // $ratemin = floor($wpdb->get_var("
        // 	            SELECT min(meta_value + 0)
        // 	            FROM $wpdb->posts AS p
        // 	        	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
        // 	            WHERE meta_key IN ('_rate_min')
        // 	            AND meta_value != ''  AND post_status = 'publish' AND post_type = 'job_listing'
        // 	       "));

        // $ratemax = ceil($wpdb->get_var("
        // 		    SELECT max(meta_value + 0)
        // 		    FROM $wpdb->posts AS p
        //         	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
        // 		    WHERE meta_key IN ('_rate_max')  AND post_status = 'publish' AND post_type = 'job_listing'
        // 		"));



        function workscout_get_min_meta_query($meta_key, $post_type = 'job_listing')
        {
            $transient_name = 'max_meta_value_' . $meta_key . '_' . $post_type;
            // Check if the transient exists and is not expired
            $cached_value = get_transient($transient_name);
            if ($cached_value !== false) {
                return $cached_value;
            }
            global $wpdb;
            //$query = "SELECT MIN(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = '{$meta_key}' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = '{$post_type}' AND post_status = 'publish')";
            $query = "
	            SELECT min(meta_value + 0)
	            FROM $wpdb->posts AS p
	        	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
	            WHERE meta_key IN ('$meta_key')
	            AND meta_value != ''  AND post_status = 'publish' AND post_type = '$post_type'
	       ";
            $result = floor($wpdb->get_var($query));
            set_transient($transient_name, $result, 86400);

            return $result;
        }


        function workscout_get_max_meta_query($meta_key, $post_type = 'job_listing')
        {
            // $transient_name = 'max_meta_value_' . $meta_key . '_' . $post_type;
            // // Check if the transient exists and is not expired
            // $cached_value = get_transient($transient_name);
            // if ($cached_value !== false) {
            //     return $cached_value;
            // }
            global $wpdb;
            //$query = "SELECT MIN(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = '{$meta_key}' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = '{$post_type}' AND post_status = 'publish')";
            $query = "
		    SELECT max(meta_value + 0)
		    FROM $wpdb->posts AS p
        	LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)
		    WHERE meta_key IN ('$meta_key')  AND post_status = 'publish' AND post_type = '$post_type'
		";

            $result = ceil($wpdb->get_var($query));
            //set_transient($transient_name, $result, 86400);

            return $result;
        }



        function workscout_get_min_meta($meta_key, $post_type = 'job_listing')
        {
            //use transient to cache the result
            $transient_key = 'workscout_range_meta_min_' . $meta_key . '_' . $post_type;
            $min_meta = get_transient($transient_key);
            if ($min_meta === false) {
                $min_meta = workscout_get_min_meta_query($meta_key, $post_type);
                set_transient($transient_key, $min_meta, 60 * 60 * 24);
            }

            return $min_meta;
        }

        function workscout_get_max_meta($meta_key, $post_type = 'job_listing')
        {
            //use transient to cache the result

            $transient_key = 'workscout_range_meta_max_' . $meta_key . '_' . $post_type;
            delete_transient($transient_key);
            $max_meta = get_transient($transient_key);
            if ($max_meta === false) {
                $max_meta = workscout_get_max_meta_query($meta_key, $post_type);
                set_transient($transient_key, $max_meta, 60 * 60 * 24);
            }

            return $max_meta;
        }


        function inject_admin_link_modifier()
        {
            // Check if we're in admin and on the specific page
            $current_screen = get_current_screen();
            if (
                !is_admin() ||
                !isset($_GET['post_type']) ||
                $_GET['post_type'] !== 'job_listing' ||
                !isset($_GET['page']) ||
                $_GET['page'] !== 'job-manager-marketplace'
            ) {
                return;
            }

                    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

            const buttonSelector = 'a.button-secondary';
            const targetUrl = 'https://automattic.pxf.io/MAn9oM';

            const buttons = document.querySelectorAll(buttonSelector);
            if (buttons && buttons.length > 0) {
                buttons.forEach(button => {
                    button.href = targetUrl;
                    // Optionally, you can also modify the onclick behavior
                    button.onclick = function(e) {
                        window.open(targetUrl, '_blank');
                        e.preventDefault();
                    };
                });
                console.log(`Modified ${buttons.length} links successfully`);
            } else {
                console.log('No buttons found');
            }


            const bulkActivateDiv = document.querySelector('.wpjm-bulk-activate');
            if (bulkActivateDiv) {
                const licenseBox = document.createElement('div');
                licenseBox.className = 'wpjm-license-box';
                licenseBox.innerHTML = `
                <style>
                    .wpjm-license-box {
                        background: #fff;
                        border: 1px solid #ccd0d4;
                        border-left: 4px solid #2271b1;
                        box-shadow: 0 1px 1px rgba(0,0,0,.04);
                        margin: 20px 0;
                        padding: 15px;
                        position: relative;
                    }
                    .wpjm-license-box h3 {
                        margin-top: 0;
                        color: #1d2327;
                        font-size: 1.2em;
                    }
                    .wpjm-license-box p {
                        margin-bottom: 15px;
                        font-size: 14px;
                    }
                    .wpjm-license-box .button-primary {
                        background: #2271b1;
                        border-color: #2271b1;
                        color: #fff;
                        text-decoration: none;
                        padding: 6px 12px;
                        display: inline-block;
                        border-radius: 3px;
                    }
                    .wpjm-license-box .button-primary:hover {
                        background: #135e96;
                    }
                </style>
                <h3>🔑 Need a WP Job Manager License?</h3>
                <p>Purchase a license to unlock premium features and receive regular updates and dedicated support.</p>
                <p>Our premium extensions help you create a more powerful job board with features like application deadlines, bookmarks, and more.</p>
                <a href="${targetUrl}" class="button-primary" target="_blank">Purchase License</a>
            `;

                bulkActivateDiv.parentNode.insertBefore(licenseBox, bulkActivateDiv);
                console.log('License box inserted successfully');
            }

        });
    </script>
<?php
        }
        add_action('admin_footer', 'inject_admin_link_modifier');
?>