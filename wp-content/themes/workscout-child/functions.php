<?php 
add_action( 'wp_enqueue_scripts', 'workscout_enqueue_styles' );
function workscout_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('workscout-base','workscout-responsive','workscout-font-awesome') );

}

 
function remove_parent_theme_features() {
   	
}
add_action( 'after_setup_theme', 'remove_parent_theme_features', 10 );



add_filter('submit_resume_steps', function($steps) {
    // Elimina el paso de selección de paquetes en el envío de resumes
    unset($steps['choose_package']);
    return $steps;
});


// QUITAR CAMPOS DEL RESUME QUE SUBE EL USUARIO
add_filter( 'submit_resume_form_fields', 'remove_submit_resume_form_fields' );
function remove_submit_resume_form_fields( $fields ) {

// Unset any of the fields you'd like to remove - copy and repeat as needed
unset( $fields['resume_fields']['candidate_video'] );
unset( $fields['resume_fields']['links'] );
unset( $fields['resume_fields']['candidate_education'] );
unset( $fields['resume_fields']['candidate_experience'] );
unset( $fields['resume_fields']['candidate_photo'] );

// Unset any of the fields you'd like to keep

// And return the modified fields
return $fields;
}


// MODIFICAR CAMPOS DEL RESUME
// Add your own function to filter the fields
add_filter( 'submit_resume_form_fields', 'resume_file_required' );
// This is your function which takes the fields, modifies them, and returns them
function resume_file_required( $fields ) {

// Here we target one of the job fields (candidate name) and change it's label
$fields['resume_fields']['resume_file']['required'] = true;

// And return the modified fields
return $fields;
}




// Hook into user_has_cap filter. This assumes you have setup resumes to require the capability 'has_active_job_package'
add_filter( 'user_has_cap', 'has_active_job_package_capability_check', 10, 3 );

/**
* has_active_job_package_capability_check()
*
* Filter on the current_user_can() function.
*
* @param array $allcaps All the capabilities of the user
* @param array $cap [0] Required capability
* @param array $args [0] Requested capability
* [1] User ID
* [2] Associated object ID
*/
function has_active_job_package_capability_check( $allcaps, $cap, $args ) {
// Only interested in has_active_job_package
if ( empty( $cap[0] ) || $cap[0] !== 'has_active_job_package' || ! function_exists( 'wc_paid_listings_get_user_packages' ) ) {
return $allcaps;
}



$user_id = $args[1];
$packages = wc_paid_listings_get_user_packages( $user_id, 'job_listing' );

// Has active package
if ( is_array( $packages ) && sizeof( $packages ) > 0 ) {
$allcaps[ $cap[0] ] = true;
}

return $allcaps;
}