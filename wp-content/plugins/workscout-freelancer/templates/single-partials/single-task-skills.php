<?php
$terms = get_the_terms($post->ID, 'task_skill');
if ($terms && !is_wp_error($terms)) : ?>
<!-- Skills -->
<div class="single-page-section  margin-bottom-60 ">
	<h3><?php esc_html_e('Skills Required', 'workscout-freelancer'); ?></h3>
	<div class="task-tags">
	
		<?php
	
			foreach ($terms as $term) {
				$term_link = get_term_link($term);
				if (is_wp_error($term_link))
					continue;
				echo '<span><a href="' . $term_link . '">' . $term->name . '</a></span>';
			}
	
		?>
		
	</div>
</div>
<div class="clearfix"></div>
<?php endif; ?>