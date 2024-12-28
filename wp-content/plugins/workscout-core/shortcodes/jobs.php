<?php


/**
* Headline shortcode
* Usage: [jobs]
* Hacks the default jobs shortcode from wp-job-manager and allows better filtering
*/

function workscout_jobs( $atts ) {
    ob_start();
    wp_enqueue_script( 'workscout-wp-job-manager-ajax-filters' );
    extract( $atts = shortcode_atts( apply_filters( 'job_manager_output_jobs_defaults', array(
        'per_page'                  => get_option( 'job_manager_per_page' ),
        'orderby'                   => 'featured',
        'order'                     => 'DESC',
 

        // Filters + cats
        'show_filters'              => true,
        'show_categories'           => true,
        'show_category_multiselect' => get_option( 'job_manager_enable_default_category_multiselect', false ),
        'show_pagination'           => false,
        'show_more'                 => true,
        'show_link'                 => false,
        'show_link_label'            => esc_html__('More Jobs','workscout_core'),
        'show_description'           => true,

        // Limit what jobs are shown based on category and type
        'categories'                => '',
        'job_types'                 => '',
        'post_status'               => '',
        'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
        'filled'                    => null, // True to show only filled, false to hide filled, leave null to show both/use the settings.
        'remote_position'           => null, // True to show only remote, false to hide remote, leave null to show both.

        // Default values for filters
        'location'                  => '',
        'keywords'                  => '',
        'selected_category'         => '',
        'selected_job_types'        => implode( ',', array_values( get_job_listing_types( 'id=>slug' ) ) ),

        'list_layout'               => Kirki::get_option( 'workscout','jobs_listings_list_layout', 'list' ) //list, grid/ grid-three-in-row
    ) ), $atts ) );

    if ( ! get_option( 'job_manager_enable_categories' ) ) {
        $show_categories = false;
    }
    $atts_categories    = $categories;
    $atts_job_types     = $job_types;
    $atts_featured      = $featured;
    $atts_filled        = $filled;
    $atts_location      = $location;
    $atts_keywords      = $keywords;
    $atts_remote_position      = $remote_position;

    // String and bool handling
    $show_filters              = workscout_string_to_bool( $show_filters );
    $show_categories           = workscout_string_to_bool( $show_categories );
    $show_category_multiselect = workscout_string_to_bool( $show_category_multiselect );
    $show_more                 = workscout_string_to_bool( $show_more );
    $show_pagination           = workscout_string_to_bool( $show_pagination );
    $show_description           = workscout_string_to_bool( $show_description );

    if ( ! is_null( $featured ) ) {
        $featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
    }

    if ( ! is_null( $filled ) ) {
        $filled = ( is_bool( $filled ) && $filled ) || in_array( $filled, array( '1', 'true', 'yes' ) ) ? true : false;
    }
    if (
        !is_null($remote_position)
    ) {
        $remote_position = (is_bool($atts['remote_position']) && $atts['remote_position']) || in_array($atts['remote_position'], [1, '1', 'true', 'yes'], true);
    }
    

    // Array handling
    $categories         = is_array( $categories ) ? $categories : array_filter( array_map( 'trim', explode( ',', $categories ) ) );

    $job_types          = is_array( $job_types ) ? $job_types : array_filter( array_map( 'trim', explode( ',', $job_types ) ) );
    $post_status        = is_array( $post_status ) ? $post_status : array_filter( array_map( 'trim', explode( ',',$post_status ) ) );
    $selected_job_types = is_array( $selected_job_types ) ? $selected_job_types : array_filter( array_map( 'trim', explode( ',', $selected_job_types ) ) );
    $selected_category  = is_array( $selected_category ) ? $selected_category: array_filter( array_map( 'trim', explode( ',', $selected_category ) ) );

    $disable_client_state = false;
    // Get keywords and location from querystring if set
    if ( ! empty( $_GET['search_keywords'] ) ) {
        $keywords = sanitize_text_field( $_GET['search_keywords'] );
        $disable_client_state = true;
    }
    if ( ! empty( $_GET['search_location'] ) ) {
        $location = sanitize_text_field( $_GET['search_location'] );
        $disable_client_state = true;
    }
    if ( ! empty( $_GET['search_category'] ) ) {
        $selected_category = sanitize_text_field( $_GET['search_category'] );
        $disable_client_state = true;
    }
        if ( ! empty( $_GET['search_job_type'] ) ) {
            $selected_job_types = sanitize_text_field( wp_unslash( $_GET['search_job_type'] ) );
            $disable_client_state       = true;
        }
    if (!empty($selected_category) && is_array($selected_category)) {
            foreach ( $selected_category as $cat_index => $category ) {
                if ( ! is_numeric( $category ) ) {
                    $term = get_term_by( 'slug', $category, 'job_listing_category' );

                    if ( $term ) {
                        $selected_category[ $cat_index ] = $term->term_id;
                    }
                }
            }
        }

    if (!job_manager_user_can_browse_job_listings()) {
        get_job_manager_template_part('access-denied', 'browse-job_listings');
        return ob_get_clean();
    }    
    if ( $show_filters ) {

        get_job_manager_template( 'job-filters.php', array( 'per_page' => $per_page, 'orderby' => $orderby, 'order' => $order, 'show_categories' => $show_categories, 'categories' => $categories, 'selected_category' => $selected_category, 'job_types' => $job_types, 'atts' => $atts, 'location' => $location, 'keywords' => $keywords, 'selected_job_types' => $selected_job_types, 'show_category_multiselect' => $show_category_multiselect ) );

        get_job_manager_template( 'job-listings-start.php' );
        get_job_manager_template( 'job-listings-end.php' );

        if ( ! $show_pagination && $show_more ) {
            echo '<a class="load_more_jobs" href="#" style="display:none;"><strong>' . esc_html__( 'Load more listings', 'workscout_core' ) . '</strong></a>';
        }

    } else {

        $jobs = get_job_listings( apply_filters( 'job_manager_output_jobs_args', array(
            'search_location'   => $location,
            'search_keywords'   => $keywords,
            'search_categories' => $categories,
            'job_types'         => $job_types,
            'post_status'       => $post_status,
            'orderby'           => $orderby,
            'order'             => $order,
            'posts_per_page'    => $per_page,
            'featured'          => $featured,
            'filled'            => $filled,
            'remote_position'   => $remote_position,
        ) ) );

        $layout = Kirki::get_option( 'workscout','pp_jobs_old_layout', false );
        $logo_position = Kirki::get_option( 'workscout','pp_job_list_logo_position', 'left' );
      
        if ( $jobs->have_posts() )                                                                                                       : ?>

            <!-- Listings Loader -->
            <div class="listings-loader">
                <div class="spinner">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
            </div>
            
            <ul class="job_listings job-list full <?php if( $list_layout == 'grid-three-in-row') { echo "grid-layout grid-three-in-row"; } ?>  <?php if( $list_layout == 'grid') { echo "grid-layout"; } ?>  <?php if($logo_position == 'right') { echo "logo-to-right ";}  if(!$layout) { echo "new-layout "; } if(!$show_description){ echo "hide-desc";} ?>" >

            <?php while ( $jobs->have_posts() ) : $jobs->the_post(); 
               
                    get_job_manager_template( 'content-job_listing.php',array( 'list_layout' => $list_layout) ); 
              
                
            endwhile; ?>

            <?php get_job_manager_template( 'job-listings-end.php' ); ?>

            <?php if ( $jobs->found_posts > $per_page && $show_more ) : ?>

                <?php wp_enqueue_script( 'wp-job-manager-ajax-filters' ); ?>

                <?php if ( $show_pagination ) : ?>
                    <?php echo get_job_listing_pagination( $jobs->max_num_pages ); ?>
                <?php else : ?>
                    <a class="load_more_jobs button centered" href="#"><i class="fa fa-plus-circle"></i><?php esc_html_e( 'Show More Jobs', 'workscout_core' ); ?></a>
                    <div class="margin-bottom-55"></div>
                <?php endif; ?>

            <?php endif; ?>

            <?php if($show_link) : ?>
                 <a class="link_more_jobs button centered" href="<?php echo esc_url($show_link); ?>"><i class="fa fa-plus-circle"></i><?php echo $show_link_label; ?></a>
                    <div class="margin-bottom-55"></div>
            <?php endif; ?>

        <?php else :
            ?> <ul class="job_listings job-list full <?php if(!$layout) { echo "new-layout "; } if(!$show_description){ echo "hide-desc";} ?>"> <?php 
            do_action( 'job_manager_output_jobs_no_results' );
            ?> </ul> <?php
        endif;

        wp_reset_postdata();
    }

    $data_attributes_string = '';
    $data_attributes        = array(
        'location'        => $location,
        'keywords'        => $keywords,
        'show_filters'    => $show_filters ? 'true' : 'false',
        'show_pagination' => $show_pagination ? 'true' : 'false',
        'job_types'       => $atts_job_types,
        'featured'          => $featured,
        'filled'            => $filled,
        'remote_position'   => $remote_position,
        //'selected_job_types' => $atts_selected_job_types,
        'per_page'        => $per_page,
        'orderby'         => $orderby,
        'order'           => $order,
        'list_layout'     => $list_layout,
        'categories'      => implode( ',', $categories ),
        'disable-form-state-storage' => $disable_client_state,
    );
    if ( ! is_null( $featured ) ) {
        $data_attributes[ 'featured' ] = $featured ? 'true' : 'false';
    }
    if ( ! is_null( $filled ) ) {
        $data_attributes[ 'filled' ]   = $filled ? 'true' : 'false';
    }
    if ( ! empty( $post_status ) ) {
            $data_attributes['post_status'] = implode( ',', $post_status );
        }
    $data_attributes['post_id'] = isset( $GLOBALS['post'] ) ? $GLOBALS['post']->ID : 0;


    $data_attributes = apply_filters( 'job_manager_jobs_shortcode_data_attributes', $data_attributes, $atts );
    
    foreach ( $data_attributes as $key => $value ) {
        $data_attributes_string .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
    }



    $job_listings_output = apply_filters( 'job_manager_job_listings_output', ob_get_clean() );

    return '<div class="job_listings " ' . $data_attributes_string . '>' . $job_listings_output . '</div>';
}

?>