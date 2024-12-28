<?php
$company_id = get_post_meta($post->ID, '_company_id', true);
if ($company_id) : ?>
	<h5><?php esc_html_e('Added by', 'workscout-freelancer'); ?></h5>
	<ul>

		<li><a href="<?php echo get_permalink($company_id); ?>"><i class="icon-material-outline-business"></i> <?php echo get_the_title($company_id); ?></a></li>
		<?php

		if (function_exists('mas_wpjmcr_get_reviews_average')) {
			$rating =  mas_wpjmcr_get_reviews_average($company_id);

			if ($rating) { ?>
				<li>
					<div class="star-rating" data-rating="<?php echo number_format_i18n($rating, 1); ?>"></div>
				</li>
			<?php } else { ?>
				<li>
					<div class="no-reviews"><?php esc_html_e('No reviews yet', 'workscout-freelancer'); ?></div>
				</li>
				<?php }
		}

		$country = get_post_meta($company_id, '_country', true);
		if ($country) {
			$countries = workscoutGetCountries();
				?>
				<li><img class="flag" src="<?php echo WORKSCOUT_FREELANCER_PLUGIN_URL; ?>/assets/images/flags/<?php echo strtolower($country); ?>.svg" alt=""> <?php echo $countries[$country]; ?></li>
			<?php } ?>


			<!-- <li>
			<div class="verified-badge-with-title">Verified</div>
		</li> -->
	</ul>
<?php endif; ?>