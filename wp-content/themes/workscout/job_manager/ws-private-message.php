<?php 
global $post;
 ?>
<form action="" id="send-message-from-widget" data-listingid="<?php echo esc_attr($post->ID); ?>">
	<textarea 
	required
	data-recipient="<?php echo esc_attr($post->post_author); ?>"  
	data-referral="listing_<?php echo esc_attr($post->ID); ?>"  
	cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message to ','workscout'); the_company_name( ) ?>"></textarea>
	<button class="button">
	<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'workscout'); ?></button>	
	<div class="notification closeable success margin-top-20"></div>

</form>
