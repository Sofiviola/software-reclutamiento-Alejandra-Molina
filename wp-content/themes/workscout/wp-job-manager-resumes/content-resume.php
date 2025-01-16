<?php
$category = get_the_resume_category();


?>
<!--Freelancer -->
<li class="freelancer <?php if (is_position_featured()) {
							echo 'freelancer-featured';
						} ?>" data-longitude="<?php echo esc_attr($post->geolocation_long); ?>" data-latitude="<?php echo esc_attr($post->geolocation_lat); ?>" data-color="#333333" data-image="<?php echo (get_the_candidate_photo($post)) ?  get_the_candidate_photo($post) : apply_filters('resume_manager_default_candidate_photo', RESUME_MANAGER_PLUGIN_URL . '/assets/images/candidate.png'); ?>" data-title="<?php echo wp_strip_all_tags(get_the_title()); ?>" data-profession="<?php the_candidate_title(); ?>" data-location="<?php echo esc_html(get_the_candidate_location($post)); ?>" data-rate="<?php echo esc_html(ws_get_candidate_rate($post)); ?>" data-skills="<?php echo esc_html(ws_get_candidate_skills($post)); ?>">
	<?php if (is_position_featured()) { ?>
		<div class="listing-badge"><i class="fa fa-star"></i></div>
	<?php } ?>
	<div class="freelancer-overview">

		<div class="freelancer-overview-inner">


			<!-- Avatar -->
			<div class="freelancer-avatar">
				<?php if (workscout_is_user_verified($post->ID)) { ?><div class="verified-badge"></div><?php } ?>
				<div class="header-image freelancer-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 200); ?></div>
				</div>

			<!-- Name -->
			<div class="freelancer-name">
				<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?>
						<?php
						$country = get_post_meta($post->ID, '_country', true);

						if ($country) {
							$countries = workscoutGetCountries();
						?>
							<img class=" flag" src="<?php echo get_template_directory_uri() ?>/images/flags/<?php echo strtolower($country); ?>.svg" alt="" title="<?php echo $countries[$country]; ?>" data-tippy-placement="top">
						<?php } ?>

					</a>

				</h4>
				<?php the_candidate_title('<span>', '</span> '); ?>

				<?php if (class_exists('WorkScout_Freelancer')) { ?>
					<?php $rating_value = get_post_meta($post->ID, 'workscout-avg-rating', true);
					if ($rating_value) {  ?>
						<div class="freelancer-rating">
							<div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>
						</div>
					<?php } else { ?>
						<div class="company-not-rated margin-bottom-5"><?php esc_html_e('Not rated yet', 'workscout'); ?></div>
				<?php }
				} ?>
			</div>



		</div>
	</div>

	<!-- Details -->
	<div class="freelancer-details">
		<div class="freelancer-details-list">
			<ul>
				<li class="freelancer-details-list-location"><?php esc_html_e('Location', 'workscout'); ?> <strong title="<?php ws_candidate_location(false); ?>" data-tippy-placement="top"><i class="icon-material-outline-location-on"></i> <?php ws_candidate_location(false); ?></strong>
			</li>
			<?php  $rate = get_post_meta($post->ID, '_rate_min', true); ?>
			<li class="freelancer-details-list-rate <?php if (empty($rate)) { echo "no-rate"; } ?>"><?php esc_html_e('Rate', 'workscout'); ?> <strong>
				<?php
					$currency_position =  get_option('workscout_currency_position', 'before');
					if (!empty($rate)) { ?>
			<?php
				if ($currency_position == 'before') {
					echo get_workscout_currency_symbol();
				}
				echo get_post_meta($post->ID, '_rate_min', true);
				if ($currency_position == 'after') {
					echo get_workscout_currency_symbol();
				}
			?> <?php esc_html_e('/ hour', 'workscout') ?>
		<?php } else {
																								esc_html_e('Negotiable', 'workscout');
																							} ?></strong></li>

</ul>
</div>
<a href="<?php the_permalink(); ?>" class="button button-sliding-icon ripple-effect"><?php esc_html_e('View Profile', 'workscout'); ?> <i class="icon-material-outline-arrow-right-alt"></i></a>
</div>
</li>
<!-- Freelancer / End -->