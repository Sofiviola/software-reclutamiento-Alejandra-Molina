<?php

/**
 * Template Name: Dashboard Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WorkScout
 */

if (!is_user_logged_in()) {

    $errors = array();

    if (isset($_REQUEST['login'])) {
        $error_codes = explode(',', $_REQUEST['login']);

        foreach ($error_codes as $code) {
            switch ($code) {
                case 'empty_username':
                    $errors[] = esc_html__('You do have an email address, right?', 'workscout');
                    break;
                case 'username_exists':
                    $errors[] = esc_html__('This username already exists?', 'workscout');
                    break;
                case 'empty_password':
                    $errors[] =  esc_html__('You need to enter a password to login.', 'workscout');
                    break;
                case 'invalid_username':
                    $errors[] =  esc_html__(
                        "We don't have any users with that email address. Maybe you used a different one when signing up?",
                        'workscout'
                    );
                    break;
                case 'incorrect_password':
                    $err = __(
                        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                        'workscout'
                    );
                    $errors[] =  sprintf($err, wp_lostpassword_url());
                    break;
                default:
                    break;
            }
        }
    }
    // Retrieve possible errors from request parameters
    if (isset($_REQUEST['register-errors'])) {
        $error_codes = explode(',', $_REQUEST['register-errors']);

        foreach ($error_codes as $error_code) {

            switch ($error_code) {
                case 'email':
                    $errors[] = esc_html__('The email address you entered is not valid.', 'workscout');
                    break;
                case 'email_exists':
                    $errors[] = esc_html__('An account exists with this email address.', 'workscout');
                    break;
                case 'closed':
                    $errors[] = esc_html__('Registering new users is currently not allowed.', 'workscout');
                    break;
                case 'captcha-no':
                    $errors[] = esc_html__('Please check reCAPTCHA checbox to register.', 'workscout');
                    break;
                case 'captcha-fail':
                    $errors[] = esc_html__("You're a bot, aren't you?.", 'workscout');
                    break;
                case 'policy-fail':
                    $errors[] = esc_html__("Please accept the Privacy Policy to register account.", 'workscout');
                    break;
                case 'first_name':
                    $errors[] = esc_html__("Please provide your first name", 'workscout');
                    break;
                case 'last_name':
                    $errors[] = esc_html__("Please provide your last name", 'workscout');
                    break;
                case 'username_exists':
                    $errors[] = esc_html__('This username already exists?', 'workscout');
                    break;
                case 'empty_user_login':
                    $errors[] = esc_html__("Please provide your user login", 'workscout');
                    break;
                case 'password-no':
                    $errors[] = esc_html__("You have forgot about password.", 'workscout_core');
                    break;
                case 'incorrect_password':
                    $err = __(
                        "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                        'workscout'
                    );
                    $errors[] =  sprintf($err, wp_lostpassword_url());
                    break;
                default:
                    break;
            }
        }
    }
    $header_old = Kirki::get_option('workscout', 'pp_old_header');
    $header_type = (Kirki::get_option('workscout', 'pp_old_header') == true) ? 'old' : '';
    $header_type = apply_filters('workscout_header_type', $header_type);
    get_header($header_type);


    $titlebar = get_post_meta($post->ID, 'pp_page_titlebar', true);
    $submit_job_page = get_option('job_manager_submit_job_form_page_id');
    $resume_job_page = get_option('resume_manager_submit_resume_form_page_id');

    if ($titlebar == 'off') {
        // no titlebar
    } else {
?>
        <?php $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE);
        if (!empty($header_image)) {
            $transparent_status = get_post_meta($post->ID, 'pp_transparent_header', TRUE);
            if ($transparent_status == 'on') { ?>
                <div id="titlebar" class="photo-bg single with-transparent-header" style="background: url('<?php echo esc_url($header_image); ?>')">
                <?php } else { ?>
                    <div id="titlebar" class="photo-bg" style="background: url('<?php echo esc_url($header_image); ?>')">
                    <?php } ?>
                <?php } else { ?>
                    <div id="titlebar" class="single">
                    <?php } ?>
                    <div class="container">

                        <div class="sixteen columns">
                            <h1><?php the_title(); ?></h1>
                            <?php if (function_exists('bcn_display')) { ?>
                                <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                    <ul>
                                        <?php bcn_display_list(); ?>
                                    </ul>
                                </nav>
                            <?php } ?>
                        </div>
                    </div>
                    </div>
                <?php

            }


            $class  = "col-md-12"; ?>
                <div class="container">

                    <div class="row">

                        <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>
                            <div class=" sign-in-form style-1 margin-bottom-45">

                                <div class="my-account static-login-page">
                                    <?php if (count($errors) > 0) : ?>
                                        <?php foreach ($errors  as $error) : ?>
                                            <div class="notification error closeable" id="reglog_form">
                                                <p><?php echo ($error); ?></p>
                                                <a class="close"></a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if (isset($_REQUEST['registered'])) : ?>
                                        <div class="notification success closeable" id="reglog_form">
                                            <p>
                                                <?php
                                                $password_field = get_option('workscout_display_password_field');
                                                if ($password_field) {
                                                    printf(
                                                        esc_html__('You have successfully registered to %s.', 'workscout'),
                                                        '<strong>' . get_bloginfo('name') . '</strong>'
                                                    );
                                                } else {
                                                    printf(
                                                        esc_html__('You have successfully registered to %s. We have emailed your password to the email address you entered.', 'workscout'),
                                                        '<strong>' . get_bloginfo('name') . '</strong>'
                                                    );
                                                }

                                                ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <ul class="tabs-nav login-tabs">
                                        <li class=""><a href="#tab1"><?php esc_html_e('Login', 'workscout'); ?></a></li>
                                        <li <?php if (isset($_GET['register'])) {
                                                echo 'class="active"';
                                            } ?>><a href="#tab2"><?php esc_html_e('Register', 'workscout'); ?></a></li>
                                    </ul>

                                    <div class="tabs-container">
                                        <!-- Login -->
                                        <div class="tab-content" id="tab1" style="display: none;">
                                            <?php echo do_action('workscout_login_form');  ?>
                                        </div>

                                        <!-- Register -->
                                        <div class="tab-content" id="tab2" style="display: none;">
                                            <?php echo do_action('workscout_register_form');  ?>

                                        </div>
                                    </div>
                                    <?php if (count($errors) > 0) : ?>
                                        <?php foreach ($errors  as $error) : ?>
                                            <div class="notification error closeable reglog_form_bottom" id="reglog_form">
                                                <p><?php echo ($error); ?></p>
                                                <a class="close"></a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if (isset($_REQUEST['registered'])) : ?>
                                        <div class="notification success closeable reglog_form_bottom" id="reglog_form">
                                            <p>
                                                <?php
                                                $password_field = get_option('workscout_display_password_field');
                                                if ($password_field) {
                                                    printf(
                                                        esc_html__('You have successfully registered to %s.', 'workscout'),
                                                        '<strong>' . get_bloginfo('name') . '</strong>'
                                                    );
                                                } else {
                                                    printf(
                                                        esc_html__('You have successfully registered to %s. We have emailed your password to the email address you entered.', 'workscout'),
                                                        '<strong>' . get_bloginfo('name') . '</strong>'
                                                    );
                                                }

                                                ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </article>



                    </div>

                </div>
                <div class="clearfix"></div>
            <?php
            get_footer();
        } else { //is logged

            get_header('dashboard');
            $current_user = wp_get_current_user();
            $user_id = get_current_user_id();
            $roles = $current_user->roles;



            $task_submit = get_option('workscout_freelancer_submit_task_form_page_id');
            $task_dashboard = get_option('workscout_freelancer_task_dashboard_page_id');
            $task_my_bids = get_option('workscout_freelancer_manage_my_bids_page_id');
            $task_my_projects = get_option('workscout_freelancer_manage_my_project_page_id');
            ?>

                <div class="new-dashboard-container ">
                    <!-- Dashboard Sidebar
	================================================== -->
                    <div class="dashboard-sidebar">
                        <div class="dashboard-sidebar-inner" data-simplebar>
                            <div class="dashboard-nav-container">

                                <!-- Responsive Navigation Trigger -->
                                <a href="#" class="dashboard-responsive-nav-trigger">
                                    <span class="hamburger hamburger--collapse">
                                        <span class="hamburger-box">
                                            <span class="hamburger-inner"></span>
                                        </span>
                                    </span>
                                    <span class="trigger-title"><?php esc_html_e('Dashboard Navigation', 'workscout'); ?></span>
                                </a>

                                <!-- Navigation -->
                                <div class="dashboard-nav test">
                                    <div class="dashboard-nav-inner">

                                        <ul data-submenu-title="<?php esc_html_e('Notificaciones', 'workscout'); ?>">
                                            <!-- Elimina el tab de dashboard en todos los roles -->
                                            <?php
                                            /* $dashboard_page = get_option('workscout_dashboard_page');
                                            if ($dashboard_page) : ?>
                                                <li id="dashboard_page-menu" <?php if ($post->ID == $dashboard_page) : ?>class="active" <?php endif; ?>><a href="<?php echo esc_url(get_permalink($dashboard_page)); ?>"><i class="icon-material-outline-dashboard"></i> <?php echo get_the_title($dashboard_page); ?></a></li>
                                            <?php endif; 
                                            */ ?>
                                            <?php $messages_page = get_option('workscout_messages_page');
                                            if ($messages_page) : ?>
                                                <li id="messages_page-menu" <?php if ($post->ID == $messages_page) : ?>class="active" <?php endif; ?>>
                                                    <a href="<?php echo esc_url(get_permalink($messages_page)); ?>">
                                                        <i class="icon-material-outline-question-answer"></i>
                                                        <?php echo get_the_title($messages_page); ?>

                                                        <?php
                                                        $counter = workscout_get_unread_counter();
                                                        if ($counter) { ?>
                                                            <span class="nav-tag"><?php echo esc_html($counter); ?></span>
                                                        <?php } ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php $bookmarks_page = get_option('pp_bookmarks_page');
                                            if (class_exists('WP_Job_Manager_Bookmarks') && $bookmarks_page) :
                                                $bookmark_count = get_user_bookmark_count($user_id); ?>
                                                <li id="bookmarks_page-menu" <?php if ($post->ID == $bookmarks_page) : ?>class="active" <?php endif; ?>><a href="<?php echo esc_url(get_permalink($bookmarks_page)); ?>"><i class="icon-material-outline-star-border"></i> <?php echo get_the_title($bookmarks_page); ?>
                                                        <?php if ($bookmark_count) { ?>
                                                            <span class="nav-tag"><?php echo esc_html($bookmark_count); ?></span>
                                                        <?php } ?></a></li>
                                            <?php endif; ?>

                                            <?php  if (array_intersect($roles, array( 'candidate'))) : ?>
                                                <?php
                                                $alerts_page = get_option('job_manager_alerts_page_id');
                                                if (class_exists('WP_Job_Manager_Alerts') && $alerts_page) : ?>
                                                    <li id="alerts_page-menu" <?php if ($post->ID == $alerts_page) : ?>class="active" <?php endif; ?>>
                                                        <a href="<?php echo esc_url(get_permalink($alerts_page)); ?>">
                                                            <i class="icon-material-outline-notifications-active"></i>
                                                            <?php echo get_the_title($alerts_page); ?>
                                                            <span class="nav-tag"><?php

                                                                                    $total_alerts = workscout_count_posts_by_user($user_id, 'job_alert', 'publish');
                                                                                    echo $total_alerts; ?></span>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>


                                            <?php if (array_intersect($roles, array('administrator', 'admin', 'candidate'))) : ?>
                                                <?php
                                                $wallet_page = get_option('workscout_wallet_page');
                                                if (class_exists('WorkScout_Freelancer') && $wallet_page) : ?>
                                                    <li id="wallet_page-menu" <?php if ($post->ID == $wallet_page) : ?>class="active" <?php endif; ?>>
                                                        <a href="<?php echo esc_url(get_permalink($wallet_page)); ?>">
                                                            <i class="icon-material-outline-notifications-active"></i>
                                                            <?php echo get_the_title($wallet_page); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <!-- <li><a href="dashboard-reviews.html"><i class="icon-material-outline-rate-review"></i> Reviews</a></li> -->
                                        </ul>

                                        <?php if (array_intersect($roles, array('candidate'))) : ?>

                                            <?php
                                            $test = get_option('workscout_past_applications');
                                            $is_active = (isset($post) && $post->post_name === 'mis-postulaciones');
                                            ?>

                                            <ul data-submenu-title="<?php esc_html_e('Postulaciones', 'workscout'); ?>">


                                                <li id="past_applications_page-menu" <?php if ($is_active) : ?>class="active" <?php endif; ?>>
                                                    <a href="/mis-postulaciones">
                                                        <i class="icon-material-outline-business-center"></i>
                                                        <?php echo 'Mis Postulaciones' ?>
                                                    </a>
                                                </li>



                                            </ul>
                                        <?php endif; ?>



                                        <?php if (array_intersect($roles, array('administrator', 'admin', 'employer'))) : ?>
                                            <?php
                                            $jobs_dashboard = get_option('job_manager_job_dashboard_page_id');
                                            $submit_page = get_option('job_manager_submit_job_form_page_id');

                                            ?>
                                            <ul data-submenu-title="<?php esc_html_e('Organize and Manage', 'workscout'); ?>">
                                                <li <?php if (in_array($post->ID, array($jobs_dashboard, $submit_page))) {
                                                        echo 'class="active-submenu"';
                                                    } ?>><a href="#"><i class="icon-material-outline-business-center"></i> <?php esc_html_e('Jobs', 'workscout'); ?></a>
                                                    <ul>
                                                        <?php

                                                        if ($jobs_dashboard) : ?>
                                                            <li id="jobs_dashboard-menu" <?php if ($post->ID == $jobs_dashboard) : ?>class="active" <?php endif; ?>>
                                                                <a href="<?php echo esc_url(get_permalink($jobs_dashboard)); ?>">
                                                                    <?php esc_html_e('Administrar empleos', 'workscout'); ?> <span class="nav-tag"><?php
                                                                                                                                                    $count_publish =  workscout_count_posts_by_user($user_id, 'job_listing', 'publish');
                                                                                                                                                    $count_pending =  workscout_count_posts_by_user($user_id, 'job_listing', 'pending');
                                                                                                                                                    $count_pending_payment =  workscout_count_posts_by_user($user_id, 'job_listing', 'pending_payment');
                                                                                                                                                    $count_draft =  workscout_count_posts_by_user($user_id, 'job_listing', 'draft');
                                                                                                                                                    $total_pending_count = $count_publish + $count_pending + $count_draft;
                                                                                                                                                    echo $total_pending_count; ?></span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        <?php

                                                        if ($submit_page) : ?>
                                                            <li id="submit_page-menu" <?php if ($post->ID == $submit_page) : ?>class="active" <?php endif; ?>>
                                                                <a href="<?php echo esc_url(get_permalink($submit_page)); ?>">
                                                                    <?php esc_html_e('Submit Job', 'workscout'); ?>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>



                                                        <?php wp_nav_menu(array('theme_location' => 'employer', 'menu_id' => 'employer', 'container' => false, 'items_wrap' => '%3$s', 'fallback_cb' => false)); ?>
                                                    </ul>
                                                </li>
                                                <?php
                                                if (class_exists('MAS_WP_Job_Manager_Company')) {
                                                    $company_dashboard = get_option('job_manager_company_dashboard_page_id');
                                                    $submit_company = get_option('job_manager_submit_company_form_page_id');
                                                ?>
                                                    <li <?php if (in_array($post->ID, array($company_dashboard, $submit_company))) {
                                                            echo 'class="active-submenu"';
                                                        } ?>>
                                                        <a href="#"><i class="icon-material-outline-business"></i> <?php esc_html_e('Companies', 'workscout'); ?></a>
                                                        <ul>
                                                            <?php

                                                            if ($company_dashboard) : ?>
                                                                <li id="submit_page-menu" <?php if ($post->ID == $company_dashboard) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($company_dashboard)); ?>">
                                                                        <?php esc_html_e('Manage Companies', 'workscout'); ?>
                                                                        <span class="nav-tag"><?php
                                                                                                $count_publish =  workscout_count_posts_by_user($user_id, 'company', 'publish');
                                                                                                $count_pending =  workscout_count_posts_by_user($user_id, 'company', 'pending');
                                                                                                $count_pending_payment =  workscout_count_posts_by_user($user_id, 'company', 'pending_payment');
                                                                                                $count_draft =  workscout_count_posts_by_user($user_id, 'company', 'draft');
                                                                                                $total_pending_count = $count_publish + $count_pending;
                                                                                                echo $total_pending_count; ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php endif;


                                                            if ($submit_company) : ?>
                                                                <li id="submit_page-menu" <?php if ($post->ID == $submit_company) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($submit_company)); ?>">
                                                                        <?php esc_html_e('Add Company', 'workscout'); ?>
                                                                    </a>
                                                                </li>
                                                            <?php endif;

                                                            ?>
                                                        </ul>
                                                    </li>
                                                <?php } ?>
                                                <?php if (class_exists('WorkScout_Freelancer')) { ?>
                                                    <li <?php if (in_array($post->ID, array($task_submit, $task_dashboard, $task_my_bids))) {
                                                            echo 'class="active-submenu"';
                                                        } ?>><a href="#"><i class="icon-material-outline-assignment"></i> <?php esc_html_e('Tasks', 'workscout'); ?></a>
                                                        <ul>
                                                            <?php

                                                            if ($task_submit) : ?>
                                                                <li id="task_dashboard-menu" <?php if ($post->ID == $task_submit) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($task_submit)); ?>">
                                                                        <?php echo get_the_title($task_submit); ?>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php
                                                            // workscout_freelancer_manage_bidders_page_id
                                                            // workscout_freelancer_manage_my_bids_page_id

                                                            if ($task_dashboard) : ?>
                                                                <li id="task_dashboard-menu" <?php if ($post->ID == $task_dashboard) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($task_dashboard)); ?>">
                                                                        <?php echo get_the_title($task_dashboard); ?><span class="nav-tag"><?php
                                                                                                                                            $count_publish =  workscout_count_posts_by_user($user_id, 'task', 'publish');
                                                                                                                                            $count_pending =  workscout_count_posts_by_user($user_id, 'task', 'pending');
                                                                                                                                            $count_pending_payment =  workscout_count_posts_by_user($user_id, 'task', 'pending_payment');
                                                                                                                                            $count_draft =  workscout_count_posts_by_user($user_id, 'task', 'draft');
                                                                                                                                            $total_pending_count = $count_publish + $count_pending + $count_draft;
                                                                                                                                            echo $total_pending_count; ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>

                                                        </ul>
                                                    </li>
                                            <?php
                                                }
                                            endif;
                                            ?>
                                            <?php if (class_exists('WorkScout_Freelancer') || class_exists('WP_Resume_Manager')) { ?>
                                                <?php if (array_intersect($roles, array('candidate'))) : ?>
                                                    <ul data-submenu-title="<?php esc_html_e('Organize and Manage', 'workscout'); ?>">
                                                        <?php if (class_exists('WorkScout_Freelancer')) { ?>
                                                            <li id="task_my_bids-menu" <?php if ($post->ID == $task_my_bids) : ?>class="active" <?php endif; ?>>
                                                                <a href="<?php echo esc_url(get_permalink($task_my_bids)); ?>"> <i class="icon-material-outline-gavel"></i>
                                                                    <?php echo get_the_title($task_my_bids); ?><span class="nav-tag"><?php
                                                                                                                                        $count_publish =  workscout_count_posts_by_user($user_id, 'bid', 'publish');
                                                                                                                                        echo $count_publish; ?></span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>


                                                        <?php
                                                        $resumes_dashboard = get_option('resume_manager_candidate_dashboard_page_id');
                                                        $submit_resume = get_option('resume_manager_submit_resume_form_page_id');
                                                        $applications_page = get_option('workscout_past_applications');
                                                        if (class_exists('WP_Resume_Manager')) { ?>

                                                            <?php

                                                            if (class_exists('WP_Resume_Manager') &&  $resumes_dashboard) : ?>
                                                                <li id="resumes_dashboard-menu" <?php if ($post->ID == $resumes_dashboard) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($resumes_dashboard)); ?>">
                                                                        <?php esc_html_e('Manage Resumes', 'workscout'); ?> <span class="nav-tag"><?php
                                                                                                                                                    $count_publish =  workscout_count_posts_by_user($user_id, 'resume', 'publish');
                                                                                                                                                    $count_pending =  workscout_count_posts_by_user($user_id, 'resume', 'pending');
                                                                                                                                                    $count_pending_payment =  workscout_count_posts_by_user($user_id, 'resume', 'pending_payment');
                                                                                                                                                    $count_draft =  workscout_count_posts_by_user($user_id, 'resume', 'draft');
                                                                                                                                                    $total_pending_count = $count_publish + $count_pending + $count_draft;
                                                                                                                                                    echo $total_pending_count; ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                            <!-- Ocultar el agregar Cv para todo rol que no sea Candidato y si ya tiene uno cargado  -->
                                                            <?php
                                                            /*
if (class_exists('WP_Resume_Manager') && $submit_resume) : ?>
    <li id="submit_resume-menu" 
        <?php 
        if ($post->ID == $submit_resume) : ?>class="active"<?php endif; ?>>
        <a href="<?php echo esc_url(get_permalink($submit_resume)); ?>">
            <?php esc_html_e('Add Resume', 'workscout'); ?>
        </a>
    </li>
<?php 
endif; 
*/
                                                            ?>
                                                            <?php
                                                            if (class_exists('WP_Resume_Manager') && $submit_resume) :
                                                                $current_user = wp_get_current_user(); // Obtén al usuario actual
                                                                $roles = $current_user->roles; // Obtén los roles del usuario actual

                                                                // Comprueba si el usuario tiene el rol 'candidate'
                                                                if (array_intersect($roles, array('candidate'))) :
                                                                    // Obtiene los paquetes de usuario para verificar si tiene CV
                                                                    if (function_exists('wc_paid_listings_get_user_packages')) :
                                                                        $user_packages = wc_paid_listings_get_user_packages(get_current_user_id());

                                                                        // Comprueba si no hay paquetes (ningún CV cargado)
                                                                        if (empty($user_packages)) :
                                                            ?>
                                                                            <li id="submit_resume-menu" <?php if (isset($post->ID) && $post->ID == $submit_resume) : ?>class="active" <?php endif; ?>>
                                                                                <a href="<?php echo esc_url(get_permalink($submit_resume)); ?>">
                                                                                    <?php esc_html_e('Add Resume', 'workscout'); ?>
                                                                                </a>
                                                                            </li>
                                                            <?php
                                                                        endif; // Cierre de la verificación de paquetes
                                                                    endif; // Cierre de la función existente
                                                                endif; // Cierre de la verificación de rol
                                                            endif;
                                                            ?>


                                                            <?php

                                                            if (class_exists('WP_Job_Manager_Applications') && $applications_page) : ?>
                                                                <li id="alerts_page-menu" <?php if ($post->ID == $applications_page) : ?>class="active" <?php endif; ?>>
                                                                    <a href="<?php echo esc_url(get_permalink($applications_page)); ?>">
                                                                        <?php esc_html_e('My Applications', 'workscout'); ?>
                                                                        <span class="nav-tag"><?php

                                                                                                $user_post_count = workscout_count_user_applications($current_user->ID);

                                                                                                echo $user_post_count;

                                                                                                ?></span>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>


                                                            <?php wp_nav_menu(array('theme_location' => 'candidate', 'menu_id' => 'employer', 'container' => false, 'items_wrap' => '%3$s', 'fallback_cb' => false)); ?>
                                                        <?php } ?>
                                                    </ul>
                                            <?php endif;
                                            } ?>



                                            <?php if (array_intersect($roles, array('administrator', 'admin', 'employer'))) : ?>

                                                <?php
                                                $test = get_option('workscout_past_applications');
                                                $is_active = (isset($post) && $post->post_name === 'candidatos');
                                                ?>

                                                <ul data-submenu-title="<?php esc_html_e('Base de datos de candidatos', 'workscout'); ?>">


                                                    <li id="past_applications_page-menu" <?php if ($is_active) : ?>class="active" <?php endif; ?>>
                                                        <a href="/candidatos">
                                                            <i class="icon-material-outline-supervisor-account"></i>
                                                            <?php echo 'Candidatos' ?>
                                                        </a>
                                                    </li>



                                                </ul>
                                            <?php endif; ?>


                                            <ul data-submenu-title="<?php esc_html_e('Account', 'workscout'); ?>">
                                                <?php $profile_page = get_option('workscout_profile_page');
                                                if ($profile_page) : ?>
                                                    <li id="profile_page-menu" <?php if ($post->ID == $profile_page) : ?>class="active" <?php endif; ?>><a href="<?php echo esc_url(get_permalink($profile_page)); ?>"><i class="icon-material-outline-settings"></i> <?php esc_html_e('My Profile', 'workscout'); ?></a></li>
                                                <?php endif; ?>
                                                <?php

                                                $orders_page_status = get_option('workscout_orders_page');

                                                if (class_exists('woocommerce') && $orders_page_status) :

                                                    $orders_page =  wc_get_endpoint_url('orders', '', get_permalink(get_option('woocommerce_myaccount_page_id')));
                                                ?>
                                                    <li id="orders_page-menu" <?php if ($post->ID == $orders_page) : ?>class="active" <?php endif; ?>><a href="<?php echo esc_url($orders_page); ?>"><?php esc_html_e('My Orders', 'workscout'); ?></a></li>
                                                <?php endif; ?>


                                                <?php
                                                $subscription_page_status = get_option('workscout_subscription_page');
                                                if (class_exists('WC_Subscriptions') && $subscription_page_status) {
                                                    $subscription_page =  wc_get_endpoint_url('subscriptions', '', get_permalink(get_option('woocommerce_myaccount_page_id')));

                                                    if ($subscription_page) : ?>
                                                        <li id="subscription_page-menu" <?php if ($post->ID == $subscription_page) : ?>class="active" <?php endif; ?>><a href="<?php echo esc_url($subscription_page); ?>"><?php esc_html_e('My Subscriptions', 'workscout'); ?></a></li>
                                                <?php endif;
                                                } ?>


                                                <li id="logout-menu"><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="icon-material-outline-power-settings-new"></i> <?php esc_html_e('Logout', 'workscout'); ?></a></li>

                                            </ul>


                                    </div>
                                </div>
                                <!-- Navigation / End -->

                            </div>
                        </div>
                    </div>
                    <!-- Dashboard Sidebar / End -->

                    <!-- Dashboard Content
	================================================== -->

                    <div class="dashboard-content-container" data-simplebar>
                        <div class="dashboard-content-inner">
                            <?php
                            $current_user = wp_get_current_user();

                            $roles = $current_user->roles;
                            $role = array_shift($roles);
                            if (!empty($current_user->user_firstname)) {
                                $name = $current_user->user_firstname;
                            } else {
                                $name =  $current_user->display_name;
                            }
                            ?>
                            <!-- Dashboard Headline -->
                            <div class="dashboard-headline">
                                <?php
                                global $job_preview;

                                $is_dashboard_page = get_option('workscout_dashboard_page');
                                $is_booking_page = get_option('workscout_bookings_page');
                                global $post;
                                if ($is_dashboard_page == $post->ID) { ?>
                                    <h1><?php esc_html_e('Hi,', 'workscout'); ?> <?php echo esc_html($name); ?></h1>
                                <?php } else {
                                    // if the url has view-project use different title
                                    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view-project') {
                                        echo "<h1>" . esc_html__('Project Details', 'workscout') . "</h1>";
                                    } else {
                                        echo "<h1>" . get_the_title() . "</h1>";
                                    }
                                ?>

                                <?php } ?>

                                <?php if ($is_dashboard_page == $post->ID) { ?><span><?php esc_html_e('We are glad to see you again!', 'workscout'); ?></span><?php } ?>
                                <?php
                                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'show_bidders' && isset($_REQUEST['task_id'])) {
                                    $task_id = absint($_REQUEST['task_id']);
                                    echo "<span class='margin-top-7'>";
                                    printf(esc_html__('Bids for %s are listed below.', 'workscout'), '<a href="' . get_permalink($task_id) . '">' . get_the_title($task_id) . '</a>');
                                    echo "<span>";
                                }
                                ?>

                                <!-- Breadcrumbs -->
                                <nav id="breadcrumbs" class="dark">
                                    <ul>
                                        <li><a href="#"><?php esc_html_e('Home', 'workscout'); ?></a></li>
                                        <li><?php esc_html_e('Dashboard', 'workscout'); ?></li>
                                    </ul>
                                </nav>
                            </div>

                            <!-- Fun Facts Container -->
                            <?php

                            while (have_posts()) : the_post();
                                the_content();
                            endwhile; // End of the loop. 
                            ?>

                            <!-- Footer -->
                            <div class="dashboard-footer-spacer"></div>
                            <div class="small-footer margin-top-15">
                                <div class="small-footer-copyrights">
                                </div>

                                <div class="clearfix"></div>
                            </div>
                            <!-- Footer / End -->

                        </div>
                    </div>
                    <!-- Dashboard Content / End -->

                </div>
                <!-- Dashboard Container / End -->



            <?php
            get_footer('empty');
        } ?>