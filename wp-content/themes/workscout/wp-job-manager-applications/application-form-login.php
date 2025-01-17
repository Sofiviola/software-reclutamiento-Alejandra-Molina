<?php
$popup_login = get_option('workscout_popup_login');
?>
<div class="login-register-buttons">
    <?php
    if (function_exists('WorkScout_Core')) :
        if ($popup_login == 'ajax' && !is_page_template('template-dashboard.php')) { ?>
            <p class="account-sign-in">
                <?php esc_html_e('You need to be signed in to apply for this position.', 'workscout'); ?>
            </p>
            <a href="#login-dialog" class="button small-dialog popup-with-zoom-anim login-btn" >
                <i class="la la-sign-in-alt"></i> <?php esc_html_e('Log In', 'workscout_core'); ?>
            </a>
        <?php
        }
    endif;
    ?>
</div>
