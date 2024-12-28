<?php

/**
 * Message to display when a task has been submitted.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-tasks/task-submitted.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-tasks
 * @category    Template
 * @version     1.18.0
 *
 * @var int     $task_id When initiating task submission, this is the job that the user intends to apply for.
 * @var WP_Post $task task post object that was just submitted.
 */

if (!defined('ABSPATH')) {
	exit;
}

?>
<div class="listing-added-notice">
	<div class="booking-confirmation-page">
		<i class="fa fa-check-circle"></i>
		<h2 class="margin-top-30"><?php esc_html_e('Thanks for your submission!', 'workscout-freelancer') ?></h2>
		<?php
		switch ( $task->post_status ) :
		case 'publish':

		echo '<p class="task-submitted">';
			echo wp_kses_post(
			sprintf(
			// translators: Placeholder is URL to view the task.
			__( 'Your task has been submitted successfully. To view your task <a href="%s">click here</a>.', 'workscout-freelancer' ),
			esc_url( get_permalink( $task->ID ) )
			)
			);
			echo '</p>';

		break;
		case 'pending':
		echo '<p class="task-submitted">';
			echo esc_html( __( 'Your task has been submitted successfully and is pending approval.', 'workscout-freelancer' ) );
			if (
			$task_id
			&& 'publish' === get_post_status( $task_id )
			&& 'task' === get_post_type( $task_id )
			) {
			$task_title = get_the_title( $task_id );
			$task_permalink = get_permalink( $task_id );
			echo wp_kses_post(
			sprintf(
			// translators: %1$s is the url to the job listing; %2$s is the title of the job listing.
			__( ' You will be able to apply for <a href="%1$s">%2$s</a> once your task has been approved.', 'workscout-freelancer' ),
			$task_permalink,
			$task_title
			)
			);
			}
			echo '</p>';
		break;
		default:
		$hook_friendly_post_status = str_replace( '-', '_', sanitize_title( $task->post_status ) );
		do_action( 'task_manager_task_submitted_content_' . $hook_friendly_post_status, $task );
		break;
		endswitch; ?>
		</p>
		<?php if(get_post_status( $task->id ) == 'publish') : ?>
			<a class="button margin-top-30" href="<?php echo get_permalink( $task->id ); ?>"><?php  esc_html_e( 'View &rarr;', 'workscout-freelancer' );  ?></a>
		<?php endif; ?>
	</div>
</div>

