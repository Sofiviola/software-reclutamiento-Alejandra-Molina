<?php




/*
    Shortcode prints grid of categories with icon boxes
    TODO: images instead of font icons
*/
function workscout_jobs_categories( $atts ) {
    extract(shortcode_atts(array(
        'title' => "Custom Title",
        'orderby' => 'count',
        'order' => 'ASC',
        'number' => '99',
        'hide_empty' => 0,
        'full_width' => 'yes',
        'jobs_counter' => 'no',
        'type' => 'all',  /* type: group_by_parent/ all / parent */
        'parent_id' => '',
        'from_vs' => ''
    ), $atts));

    $output = '';
    if($full_width == 'yes') {
       if($from_vs === 'yes') {
        $output =  '    </div> <!-- eof wpb_wrapper -->
                    </div> <!-- eof vc_column-inner -->
                </div> <!-- eof vc_column_container -->
            </div> <!-- eof vc_row-fluid -->
        </article>
    </div> <!-- eof container -->';

        } else {
             $output = '</article>
            </div>';
        }
    }

    
    if($type == 'all') {
     
        $categories = get_terms( 'job_listing_category', array(
            'orderby'    => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
            'order'      => $order,
            'hide_empty' => $hide_empty,
            'number'     => $number,
         ) );
        if ( !is_wp_error( $categories ) ) {
            $output .= '<div class="categories-group">
                <div class="container">
                    <div class="four columns"><h4 class="parent-jobs-category">'.$title.'</h4></div>';
            $chunks = workscout_partition($categories, 3);
            foreach ($chunks as $chunk) {
                $output .= '<div class="four columns">
                        <ul>';
                        foreach ($chunk as $term) {
                            $count = workscout_get_term_post_count('job_listing_category',$term->term_id);
                            $output .= ' <li><a href="' . get_term_link( $term ) . '">' . $term->name;
                            if($jobs_counter=='yes'){
                                $output .= ' ('.$count.')';
                            }
                            $output .= '</a></li>';
                        }
                $output .= '</ul>
                    </div>';
            }
            $output .= '</div>
            </div>';
        }
    }  

    if($type == 'only_parents') {
     
        $categories = get_terms( 'job_listing_category', array(
            'orderby'    => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
            'order'      => $order,
            'hide_empty' => $hide_empty,
            'number'     => $number,
            'parent'     => 0
         ) );
        if ( !is_wp_error( $categories ) ) {
            $output .= '<div class="categories-group">
                <div class="container">
                    <div class="four columns"><h4 class="parent-jobs-category">'.$title.'</h4></div>';
            $chunks = workscout_partition($categories, 3);
            foreach ($chunks as $chunk) {
                $output .= '<div class="four columns">
                        <ul>';
                        foreach ($chunk as $term) {
                            $count = workscout_get_term_post_count('job_listing_category',$term->term_id);
                            $output .= ' <li><a href="' . get_term_link( $term ) . '">' . $term->name;
                            if($jobs_counter=='yes'){
                                $output .= ' ('.$count.')';
                            }
                            $output .= '</a></li>';
                        }
                $output .= '</ul>
                    </div>';
            }
            $output .= '</div>
            </div>';
        }
    }

    if($type == 'group_by_parents') {

        $parents =  get_terms("job_listing_category", array(
            'orderby'    => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
            'order'      => $order,
            'hide_empty' => $hide_empty,
            'number'     => $number,
            'parent'     => 0
            ));
        if ( !is_wp_error( $parents ) ) {
            foreach($parents as $key => $term) :
                $subterms = get_terms("job_listing_category", array("orderby" => $orderby, "parent" => $term->term_id, 'hide_empty' => $hide_empty));
                if($subterms) :
                    $output .= '<div class="categories-group">
                    <div class="container">
                        <div class="four columns"><h4 class="parent-jobs-category"><a href="' . get_term_link( $term ) . '">'. $term->name .'</a></h4></div>';
                           
                            $chunks = workscout_partition($subterms, 3);
                            foreach ($chunks as $chunk) {
                                $output .= '<div class="four columns">
                                        <ul>';
                                        foreach ($chunk as $subterms) {
                                            $count = workscout_get_term_post_count('job_listing_category',$subterms->term_id);
                                            $output .= ' <li><a href="' . get_term_link( $subterms ) . '">' . $subterms->name;
                                            if($jobs_counter=='yes'){
                                                $output .= ' ('.$count.')';
                                            }
                                            $output .= '</a></li>';
                                        }
                                $output .= '</ul>
                                    </div>';
                            }
                           
                    $output .= '</div>
                    </div>';
                 endif;
            endforeach;
        }
    }

    if($type == 'parent') {
        $categories = get_terms( 'job_listing_category', array(
            'orderby'    => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
            'order'      => $order,
            'hide_empty' => $hide_empty,
            'number'     => $number,
         ) );
        if ( !is_wp_error( $categories ) ) {
            $subterms =  get_terms("job_listing_category", array(
                'orderby'    => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
                'order'      => $order,
                'hide_empty' => $hide_empty,
                'number'     => $number,
                'parent'     => $parent_id,
                ));
            $term = get_term( $parent_id, "job_listing_category" );
            if($subterms) :
                    $output .= '<div class="categories-group">
                    <div class="container">
                        <div class="four columns"><h4 class="parent-jobs-category"><a href="' . get_term_link( $term ) . '">'. $term->name .'</a></h4></div>';
                           
                            $chunks = workscout_partition($subterms, 3);
                            foreach ($chunks as $chunk) {
                                $output .= '<div class="four columns">
                                        <ul>';
                                        foreach ($chunk as $subterms) {
                                            $count = workscout_get_term_post_count('job_listing_category',$subterms->term_id);
                                           $output .= ' <li><a href="' . get_term_link( $subterms ) . '">' . $subterms->name;
                                           if($jobs_counter=='yes'){
                                             $output .= ' ('.$count.')';
                                           }
                                           $output .= '</a></li>';
                                        }
                                $output .= '</ul>
                                    </div>';
                            }
                           
                    $output .= '</div>
                    </div>';
                 endif;
         }
        
    }

    if($full_width == 'yes') {
       if($from_vs === 'yes') {
              $output .= '
    <div class="container">
        <article class="sixteen columns">
             <div class="vc_row wpb_row vc_row-fluid">
                <div class="wpb_column vc_column_container vc_col-sm-12">
                    <div class="vc_column-inner ">
                        <div class="wpb_wrapper">';
        } else {
            $output .= ' <div class="container">
                <article class="sixteen columns">';
        }
    }
    return $output;
}
?>