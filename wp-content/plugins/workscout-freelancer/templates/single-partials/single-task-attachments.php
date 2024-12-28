<?php

$attachments = get_post_meta($post->ID, '_task_file',true);
			
			//if attachemnts is not empty
if($attachments) : 
			?>
<!-- Atachments -->
	<div class="single-page-section  margin-bottom-60 ">
		<h3><?php esc_html_e('Attachments', 'workscout-freelancer'); ?></h3>
		<div class="attachments-container">
			<?php
			//$attachments     = get_posts('post_parent=' . $post->ID . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1');


			foreach ((array) $attachments as $attachment_id => $attachment_url) {
				//get the attachment url
				
				$attachment_url = wp_get_attachment_url($attachment_id);
				if(!$attachment_url){
				  //skip if no url
				  continue;
				}
			
				//get the attachment filename
				$attachment_title = get_the_title($attachment_id);
				if(!$attachment_title){
					
				}
				$attachment_title = basename($attachment_url);
				//$attachment_title = get_the_title($id);
				//get the attachment file type
				$attachment_filetype = wp_check_filetype($attachment_url);
				
			?>
				<a href="<?php echo $attachment_url;?>" class="attachment-box ripple-effect"><span><?php echo $attachment_title; ?></span><i><?php echo $attachment_filetype['ext']; ?></i></a>

			<?php } ?>
		</div>
	</div>
<?php endif; ?>