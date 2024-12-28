<?php
/**
 * Message to display when a company has been submitted.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-company/job-manager-message.php.
 *
 * @author      Madras Themes
 * @package     MAS Companies For WP Job Manager
 * @category    Template
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

switch ( $company->post_status ) :
	case 'publish' :
		if ( $company->ID ) {
			printf( '<p class="job-manager-message">' . __( 'Your company has been submitted successfully. To view your company <a href="%s">click here</a>.', 'workscout' ) . '</p>', get_permalink( $company->ID ) );
		} else {
			print( '<p class="job-manager-message">' . esc_html__( 'Your company has been submitted successfully.', 'workscout' ) . '</p>' );
		}
	break;
	case 'pending' :
		print( '<p class="job-manager-message">' . esc_html__( 'Your company has been submitted successfully and is pending approval.', 'workscout' ) . '</p>' );
	break;
	default :
		do_action( 'company_manager_company_submitted_content_' . str_replace( '-', '_', sanitize_title( $company->post_status ) ), $company );
	break;
endswitch;
