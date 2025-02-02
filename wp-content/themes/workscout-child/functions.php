<?php 

// import autoload IMPORTANTE NO BORRAR FV
require_once __DIR__ . '/../../../vendor/autoload.php';



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
add_filter( 'submit_resume_form_fields', 'customize_resume_form_fields_order' );
function customize_resume_form_fields_order( $fields ) {
    // Remover los campos que no necesitas
    unset( $fields['resume_fields']['candidate_video'] );
    unset( $fields['resume_fields']['links'] );
    unset( $fields['resume_fields']['candidate_education'] );
    unset( $fields['resume_fields']['candidate_experience'] );
    unset( $fields['resume_fields']['candidate_photo'] );

    // Agregar los campos personalizados
    $custom_fields = array(
        // 'candidate_linkedin' => array(
        //     'label' => 'LinkedIn',
        //     'type' => 'text',
        //     'required' => false,
        // ),
        // 'candidate_phone' => array(
        //     'label' => 'Teléfono',
        //     'type' => 'text',
        //     'required' => false,
        // ),
        // 'candidate_dni' => array(
        //     'label' => 'DNI',
        //     'type' => 'text',
        //     'required' => false,
        // ),
    );

    // Reorganizar los campos (incluir los personalizados antes de "resume_content")
    $new_order = array();

    foreach ( $fields['resume_fields'] as $key => $value ) {
        if ( $key === 'resume_content' ) {
            // Insertar los campos personalizados antes de "resume_content"
            $new_order = array_merge( $new_order, $custom_fields );
        }

        $new_order[ $key ] = $value; // Mantener los campos originales
    }

    $fields['resume_fields'] = $new_order;

    return $fields;
}

add_action('init', function () {
    if (isset($_POST['save_application_note']) && isset($_POST['application_note_nonce']) && wp_verify_nonce($_POST['application_note_nonce'], 'save_application_note')) {
        $application_id = absint($_POST['application_id']);
        $application_note = sanitize_textarea_field($_POST['application_note']);

        // Guardar la nota como metadato
        update_post_meta($application_id, '_application_note', $application_note);

        // Redirigir para evitar reenvíos
        wp_safe_redirect(add_query_arg('note_saved', 'true'));
        exit;
    }
});



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

add_action('wp', function() {
    if (isset($_POST['wp_job_manager_resumes_apply_with_resume'])) {
        // Procesar el formulario aquí (lo que hace actualmente).
        
        // Redirigir al home.
        wp_redirect(home_url() . '/mis-postulaciones/');
        exit;
    }
});

add_filter('wp_editor_settings', function ($settings) {
    $settings['media_buttons'] = true; // Agregar botones de medios (subir imagen)
    return $settings;
});

add_filter('mce_buttons', function ($buttons) {
    array_push($buttons, 'image'); // Agregar el botón de insertar imagen
    return $buttons;
});

add_filter('mce_external_plugins', function ($plugins) {
    $plugins['image'] = includes_url('/js/tinymce/plugins/image/plugin.min.js');
    return $plugins;
});







// NO FUNCA DEL TODO - CUANDO SE PONE VER CURRICULUM QUE CAMBIE EL ESTADO A EN REVISIÓN
// add_action('template_redirect', function () {
//     // Verificar si los parámetros están presentes en la URL
//     if ($_GET['application_id']) {
//         $application_id = absint($_GET['application_id']);  // Sanear el ID de la aplicación
//         $nonce = sanitize_text_field($_GET['nonce']);  // Sanear el nonce

//         // Verificar si el nonce es válido
//         if (!wp_verify_nonce($nonce, 'change_status_' . $application_id)) {
//             return;  // Si el nonce no es válido, no hacer nada
//         }

//         $application = get_post($application_id);  // Obtener el post de la aplicación

//         // Verificar si la aplicación existe y si su estado es 'new'
//         if ($application && 'new' === $application->post_status) {
//             // Cambiar el estado a 'revision'
//             wp_update_post([
//                 'ID'          => $application_id,
//                 'post_status' => 'revision',
//             ]);
//             error_log('Changed status to revision for application_id: ' . $application_id);  // Verificar que el estado se cambió correctamente
//         } else {
//             error_log('Application not found or not in "new" status. Application ID: ' . $application_id);  // En caso de que no se cumpla la condición
//         }
//     }
// });
