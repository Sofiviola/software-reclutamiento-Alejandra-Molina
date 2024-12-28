<?php 
if ( ! empty( $_GET['search_region'] ) ) {
	$location = sanitize_text_field( $_GET['search_region'] );
} else {
	$location = get_query_var('job_listing_region');

	if(!empty($location)) {
		$term = get_term_by('slug', $location, 'job_listing_region');
		
		if($term){
			$location = $term->term_id;
		}
		
	}
}    
          

$dropdown = wp_dropdown_categories( array(
    'show_option_all'           => __( 'All Regions', 'workscout' ),
    'hierarchical'              => true,
    'orderby'                   => 'name',
    'taxonomy'                  => 'job_listing_region',
    'name'                      => 'search_region',
    'id'                        => 'search_region',
    'class'                     => 'select2-single job-manager-category-dropdown',
    'hide_empty'                => 1,
    'selected'                  => $location,
    'echo'                      => false,
)  );
$fixed_dropdown = str_replace("&nbsp;", "", $dropdown); echo $fixed_dropdown;