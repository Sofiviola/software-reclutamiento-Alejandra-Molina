<li <?php job_listing_class(); ?>>
	<?php
	global $post;
	$company_id = get_post_meta($post->ID, '_company_id', true);
	if (!empty($company_id) && get_post_status($company_id)) {
		$logo_id = $company_id;
	} else {
		$logo_id = $post;
	} ?>
	<a href="<?php the_job_permalink(); ?>">
		<div class="position">
			<h3><?php the_title(); ?></h3>
			<?php if (isset($show_logo) && $show_logo) { ?>
				<div class="image">
					<?php the_company_logo('thumbnail', null, $logo_id); ?>
				</div>
			<?php } ?>
		</div>
		<ul class="meta">
			<li class="location"><i class="fa fa-map-marker"></i> <?php ws_job_location(false); ?></li>
			<li class="company"><i class="fa fa-building-o"></i> <?php the_company_name(); ?></li>
			<?php if (get_option('job_manager_enable_types')) { ?>
				<?php $types = wpjm_get_the_job_types(); ?>
				<?php if (!empty($types)) : foreach ($types as $type) : ?>
						<li class="job-type <?php echo esc_attr(sanitize_title($type->slug)); ?>"><?php echo esc_html($type->name); ?></li>
				<?php endforeach;
				endif; ?>
			<?php } ?>
		</ul>
	</a>
</li>