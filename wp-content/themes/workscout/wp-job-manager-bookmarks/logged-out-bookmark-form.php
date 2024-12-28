<?php $popup_login = get_option( 'workscout_popup_login' ); 
if( $popup_login == 'ajax' ) { ?>
<div class="job-manager-form wp-job-manager-bookmarks-form">
	<div><a href="#login-dialog" class="small-dialog popup-with-zoom-anim bookmark-notice button dark bookmark-notice"><?php  esc_html_e( 'Login to bookmark', 'workscout' ); ?></a></div>
</div>
<?php } else {

	$login_page = get_option('workscout_profile_page'); ?>
<div class="job-manager-form wp-job-manager-bookmarks-form">
	<div><a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="bookmark-notice button dark bookmark-notice"><?php  esc_html_e( 'Login to bookmark', 'workscout' ); ?></a></div>
</div>
<?php } ?>