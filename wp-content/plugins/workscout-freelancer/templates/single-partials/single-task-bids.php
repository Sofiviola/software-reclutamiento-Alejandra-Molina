		<!-- Freelancers Bidding -->

		<?php

		$bids_args = array(
			'post_type' => 'bid',
			'posts_per_page' => 10,
			'post_parent' => $post->ID,
			//'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		);
		$wp_query = new WP_Query($bids_args);
		$currency_position =  get_option('workscout_currency_position', 'before');
		if ($wp_query->have_posts()) { ?>
			<div class="boxed-list margin-bottom-60 ">
				<div class="boxed-list-headline">
					<h3><i class="icon-material-outline-group"></i> <?php echo esc_html_e('Freelancers Bidding', 'workscout-freelancer') ?></h3>
				</div>
				<ul class="boxed-list-ul">
					<?php while ($wp_query->have_posts()) : $wp_query->the_post();
						$author_id = $post->post_author;
						// check if has profile
						$has_profile = get_the_author_meta('freelancer_profile', $author_id);
						if ($has_profile) {
							$avatar = "<img src=" . get_the_candidate_photo($has_profile) . " class='avatar avatar-32 photo'/>";
							$username = get_the_title($has_profile);
						} else {
							$avatar = get_avatar($author_id, 100);
							$username = get_the_author_meta('display_name', $author_id);
						}
					?>
						<li>
							<div class="bid">
								<!-- Avatar -->
								<div class="bids-avatar">
									<div class="freelancer-avatar">
										<?php if (workscout_is_user_verified($author_id)) { ?>
											<div class="verified-badge"></div>
										<?php } ?>
										<a href="<?php echo get_author_posts_url($author_id); ?>"> <?php echo $avatar; ?></a>
									</div>
								</div>

								<!-- Content -->
								<div class="bids-content">
									<!-- Name -->
									<div class="freelancer-name">
										<h4><a href="<?php echo get_author_posts_url($author_id); ?>"><?php echo $username; ?>
												<?php
												if ($has_profile) { 
												$country = get_post_meta($has_profile, '_country', true);
												if ($country) {

													$countries = workscoutGetCountries();	?>
													<img class="flag" src="<?php echo WORKSCOUT_FREELANCER_PLUGIN_URL; ?>/assets/images/flags/<?php echo strtolower($country); ?>.svg" title=" <?php echo $countries[$country]; ?>" data-tippy-placement="top">
												<?php } 
												} ?>

											</a></h4>
										<?php
										if ($has_profile) { ?>
											<?php $rating_value = get_post_meta($has_profile, 'workscout-avg-rating', true);
											if ($rating_value) {  ?>
												
													<div class="star-rating" data-rating="<?php echo esc_attr(number_format(round($rating_value, 2), 1)); ?>"></div>

											<?php } ?>
										<?php } ?>

									</div>
								</div>
								<?php
								$budget = get_post_meta($post->ID, '_budget', true);
								$time = get_post_meta($post->ID, '_time', true);
								?>
								<!-- Bid -->
								<div class="bids-bid">
									<div class="bid-rate">
										<div class="rate">
											<?php
											if ($currency_position == 'before') {
												echo get_workscout_currency_symbol();
											}
											echo (is_numeric($budget)) ? number_format_i18n($budget) : $budget;
											if ($currency_position == 'after') {
												echo get_workscout_currency_symbol();
											} ?></div>
										<span><?php esc_html_e('in ', 'workscout-freelancer'); ?><?php echo $time; ?> <?php esc_html_e('days','workscout-freelancer'); ?></span>
									</div>
								</div>
							</div>
						</li>
					<?php endwhile; // end of the loop.  
					?>
				</ul>
			</div>
		<?php } ?>

		<?php wp_reset_postdata();
		wp_reset_query();
		?>