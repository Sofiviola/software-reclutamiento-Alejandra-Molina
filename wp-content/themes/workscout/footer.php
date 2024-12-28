<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WorkScout
 */

?>
<?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) { ?>
    <!-- Footer
================================================== -->
    <div class="margin-top-45"></div>

    <!-- Footer
================================================== -->
    <div id="footer-new" class="<?php if (Kirki::get_option('workscout', 'workscout_custom_footer', '') == "enable") echo 'custom-footer-colors'; ?>">

        <!-- Footer Top Section -->
        <div class="footer-new-top-section">
            <div class="container">
                <div class="row">
                    <div class="sixteen columns">

                        <!-- Footer Rows Container -->
                        <div class="footer-new-rows-container">

                            <?php $logo = Kirki::get_option('workscout', 'pp_footer_logo_upload', '');
                            if ($logo) { ?>
                                <!-- Left Side -->
                                <div class="footer-new-rows-left">
                                    <div class="footer-new-row">
                                        <div class="footer-new-row-inner footer-new-logo">
                                            <img src="<?php echo esc_url($logo); ?>" alt="">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Right Side -->
                            <div class="footer-new-rows-right">
                                <?php $stats_counter = Kirki::get_option('workscout', 'footer_stat_counters');

                                if (!empty($stats_counter)) :
                                    foreach ($stats_counter as $stat) {
                                        if(isset($stat['type'])){

                                        
                                        switch ($stat['type']) {
                                            case "jobs":
                                                $count = wp_count_posts('job_listing', 'readable');
                                                $number = (isset($count->publish))  ? $count->publish : '0';
                                                break;

                                            case "resumes":
                                                $count = wp_count_posts('resume');
                                                $number = (isset($count->publish))  ? $count->publish : '0';
                                                break;
                                            case "companies":
                                                $count = wp_count_posts('company');
                                                $number = (isset($count->publish))  ? $count->publish : '0';
                                                break;

                                            case "posts":
                                                $count = wp_count_posts('posts', 'readable');
                                                $number = (isset($count->publish))  ? $count->publish : '0';
                                                break;



                                            case "members":
                                                $number = get_user_count();

                                                break;

                                            case "candidates":
                                                $args = array(
                                                    'role' => 'candidate', //substitute your role here as needed
                                                    'fields' => 'ID',
                                                );

                                                $users = get_users($args);
                                                $number = count($users);

                                                break;

                                            case "employers":
                                                $args = array(
                                                    'role' => 'employer', //substitute your role here as needed
                                                    'fields' => 'ID',
                                                );
                                                $users = get_users($args);
                                                $number = count($users);
                                                break;

                                            default:
                                                $number = $stat['number'];
                                            break;
                                        }
                                        } else {
                                            $number = $stat['number'];
                                        }
                                        ?>
                                        <div class="footer-new-row">
                                            <div class="footer-new-row-inner">
                                                <ul class="intro-stats">
                                                    <li>
                                                        <i class="<?php echo esc_attr($stat['icons']); ?>"></i>
                                                        <strong class="counter"><?php echo esc_html($number); ?></strong>
                                                        <span><?php echo esc_html($stat['label']); ?></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                <?php }
                                endif; ?>
                                <!-- Fun Fact -->

                            </div>

                        </div>
                        <!-- Footer Rows Container / End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Top Section / End -->


        <!-- Footer Middle Section -->
        <div class="footer-new-middle-section">
            <div class="container">
                <div class="footer-row">
                    <?php
                    $footer_layout = Kirki::get_option('workscout', 'pp_new_footer_widgets');

                    $footer_layout_array = explode(',', $footer_layout);
                    $x = 0;
                    foreach ($footer_layout_array as $value) {
                        $x++;
                    ?>
                        <div class="footer-col-<?php echo esc_attr($value); ?> footer-col-s-3 footer-col-xs-6">
                            <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('footer' . $x)) : endif; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Footer Middle Section / End -->


        <!-- Footer Copyrights -->
        <div class="footer-new-bottom-section">
            <div class="container">
                <div class="row">
                    <div class="sixteen columns">
                        <div class="footer-new-bottom-inner">
                            <div class="footer-new-bottom-left"><?php $copyrights = Kirki::get_option('workscout', 'pp_copyrights');

                                                                if (function_exists('icl_register_string')) {
                                                                    icl_register_string('Copyrights in footer', 'copyfooter', $copyrights);
                                                                    echo icl_t('Copyrights in footer', 'copyfooter', $copyrights);
                                                                } else {
                                                                    echo wp_kses($copyrights, array('br' => array(), 'em' => array(), 'strong' => array(), 'a' => array('href' => array(), 'title' => array())));
                                                                } ?>

                            </div>
                            <div class="footer-new-bottom-right">

                                <?php /* get the slider array */
                                $footericons = Kirki::get_option('workscout', 'pp_footericons', array());
                                if (!empty($footericons)) {

                                    echo '<ul class="new-footer-social-icons">';
                                    foreach ($footericons as $icon) {
                                        if ($icon['icons_service'] == 'telegram') {
                                            echo '<li><a target="_blank" title="' . esc_attr($icon['icons_service']) . '" href="' . esc_url($icon['icons_url']) . '"><i class="fa fa-' . $icon['icons_service'] . '"></i></a></li>';
                                        } else {
                                            echo '<li><a target="_blank" title="' . esc_attr($icon['icons_service']) . '" href="' . esc_url($icon['icons_url']) . '"><i class="icon-brand-' . $icon['icons_service'] . '"></i></a></li>';
                                        }
                                    }
                                    echo '</ul>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Copyrights / End -->

        </div>
        <!-- Footer / End -->


        <div id="ajax_response"></div>
    </div>
    <!-- Wrapper / End -->
<?php } ?>
<?php if ((is_page_template('template-home.php') || is_page_template('template-home-box.php')) &&  Kirki::get_option('workscout', 'pp_jobs_home_typed_status') == 'enable') {
    $typed =  Kirki::get_option('workscout', 'pp_jobs_home_typed_text');
    if (empty($typed)) {
        $typed = 'healthcare, automotive, sales & marketing, accounting & finance';
    }
    $typed_array = explode(',', $typed);
?>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
    <script>
        var typed = new Typed('.typed-words', {
            strings: <?php echo json_encode($typed_array); ?>,
            typeSpeed: 80,
            backSpeed: 80,
            backDelay: 4000,
            startDelay: 1000,
            loop: true,
            showCursor: true
        });
    </script>
<?php } ?>

<?php if (is_page_template('template-home-resumes.php') && Kirki::get_option('workscout', 'pp_resume_home_typed_status') == 'enable') {
    $typed =  Kirki::get_option('workscout', 'pp_resume_home_typed_text');
    if (empty($typed)) {
        $typed = 'healthcare, automotive, sales & marketing, accounting & finance';
    }
    $typed_array = explode(',', $typed);
?>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
    <script>
        var typed = new Typed('.typed-words', {
            strings: <?php echo json_encode($typed_array); ?>,
            typeSpeed: 80,
            backSpeed: 80,
            backDelay: 4000,
            startDelay: 1000,
            loop: true,
            showCursor: true
        });
    </script>
<?php } ?>

<?php wp_footer(); ?>

</body>

</html>