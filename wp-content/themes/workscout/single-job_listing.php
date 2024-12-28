<?php
/**
 * The template for displaying all single jobs.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WorkScout
 */

get_header(); 
	$style = Kirki::get_option( 'workscout', 'pp_job_style' ); 
	if($style == 'modern') {
		get_template_part('template-parts/single-job');	
	} else {
		get_template_part('template-parts/single-job-classic');	
	}
	
	

get_footer(); ?>
