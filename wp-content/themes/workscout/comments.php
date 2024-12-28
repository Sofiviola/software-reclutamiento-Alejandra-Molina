<?php

/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WorkScout
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
	return;
}

if (get_post_type(get_the_ID()) == 'resume') { ?>

	<?php if (have_comments()) : ?>
		<div class="boxed-list margin-bottom-60">
			<div class="boxed-list-headline">
				<h3><i class="icon-material-outline-thumb-up"></i> Work History and Feedback</h3>
			</div>
			<ul class="boxed-list-ul">
				<?php wp_list_comments(array(
					'style'      	=> 'ul',
					'short_ping' 	=> true,
					'callback' 		=> 'workscout_review',
				)); ?>


			</ul>

			<!-- Pagination -->
			<div class="clearfix"></div>
			<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? 
			?>
				<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
					<h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'workscout'); ?></h2>
					<div class="nav-links">

						<div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'workscout')); ?></div>
						<div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'workscout')); ?></div>

					</div><!-- .nav-links -->
				</nav><!-- #comment-nav-below -->
			<?php endif; // Check for comment navigation. 
			?>
			<div class="clearfix"></div>
			<!-- Pagination / End -->
			
		</div>
		<!-- Boxed List / End -->

	<?php endif; // Check for have_comments(). 
	?>
<?php } else {

?>
	<section id="comments-section" class="comments">

		<?php // You can start editing here -- including this comment! 
		?>

		<?php if (have_comments()) : ?>
			<h4 class="comments-title">
				<?php
				if (is_singular('company') || is_singular('resume')) {
					printf( // WPCS: XSS OK.
						esc_html(_nx('Review %1$s', ' Reviews %1$s', get_comments_number(), 'comments title', 'workscout')),
						'<span class="comments-amount">(' . number_format_i18n(get_comments_number()) . ')</span>'
					);
				} else {
					printf( // WPCS: XSS OK.
						esc_html(_nx('Comments %1$s', ' Comments %1$s', get_comments_number(), 'comments title', 'workscout')),
						'<span class="comments-amount">(' . number_format_i18n(get_comments_number()) . ')</span>'
					);
				}

				?>
			</h4>

			<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? 
			?>
				<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
					<h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'workscout'); ?></h2>
					<div class="nav-links">

						<div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'workscout')); ?></div>
						<div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'workscout')); ?></div>

					</div><!-- .nav-links -->
				</nav><!-- #comment-nav-above -->
			<?php endif; // Check for comment navigation. 
			?>

			<ul class="comment-list">
				<?php

				if (get_post_type(get_the_ID()) == 'resume') {
					wp_list_comments(array(
						'style'      	=> 'ul',
						'short_ping' 	=> true,
						'callback' 		=> 'workscout_review',
					));
				} else {
					wp_list_comments(array(
						'style'      	=> 'ul',
						'short_ping' 	=> true,
						'callback' 		=> 'workscout_comment',
					));
				}


				?>
			</ul><!-- .comment-list -->

			<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? 
			?>
				<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
					<h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'workscout'); ?></h2>
					<div class="nav-links">

						<div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'workscout')); ?></div>
						<div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'workscout')); ?></div>

					</div><!-- .nav-links -->
				</nav><!-- #comment-nav-below -->
			<?php endif; // Check for comment navigation. 
			?>

		<?php endif; // Check for have_comments(). 
		?>

		<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
		?>
			<p class="no-comments"><?php esc_html_e('Comments are closed.', 'workscout'); ?></p>
		<?php endif; ?>

		<?php comment_form(); ?>

	</section><!-- #comments -->
<?php } ?>