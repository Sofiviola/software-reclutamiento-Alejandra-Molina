<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * WorkScout_Freelancer_Task class
 */
class WorkScout_Freelancer_Task
{

    private static $_instance = null;

    public function __construct()
    {

        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('pre_get_posts', array($this, 'pre_get_posts_tasks'), 0);

        add_action('wp_ajax_nopriv_workscout_get_tasks', array($this, 'ajax_get_tasks'));
        add_action('wp_ajax_workscout_get_tasks', array($this, 'ajax_get_tasks'));
        add_action('wp_ajax_workscout_incremental_skills_suggest', array($this, 'wp_ajax_workscout_incremental_skills_suggest'));
        add_action('wp_ajax_nopriv_workscout_incremental_skills_suggest', array($this, 'wp_ajax_workscout_incremental_skills_suggest'));
        add_action('wp', array($this, 'bookmark_handler'));
    }

    function wp_ajax_workscout_incremental_skills_suggest(){
        $suggestions = array();
        $terms = get_terms(array(
            
            'taxonomy'      => array('task_skill'), // taxonomy name
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'fields'        => 'all',
            'name__like'    => $_REQUEST['term']
        ));
        
        $count = count($terms);
        if ($count > 0) {
            foreach ($terms as $term) {
                
                $suggestion = array();
                $suggestion['label'] =  html_entity_decode($term->name, ENT_QUOTES, 'UTF-8');
                $suggestion['link'] = get_term_link($term);

                $suggestions[] = $suggestion;
            }
        }
      
        // JSON encode and echo
        $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
        echo $response;
        // Don't forget to exit!
        exit;
    }

    function bookmark_handler(){
        global $wpdb;

        if (!is_user_logged_in()) {
            return;
        }
        //$wpjm_bookmark =  WP_Job_Manager_Bookmarks();
        $action_data = null;

        if (!empty($_POST['submit_bookmark'])) {
            $post_id = absint($_POST['bookmark_post_id']);
            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'update_bookmark')) {
                $action_data = array(
                    'error_code' => 400,
                    'error' => __('Bad request', 'wp-job-manager-bookmarks'),
                );
            } else {
                $note    = wp_kses_post(stripslashes($_POST['bookmark_notes']));

                if (
                    $post_id && in_array(get_post_type($post_id), array('task'))
                ) {
                    if (!$this->is_bookmarked($post_id)) {
                        $wpdb->insert(
                            "{$wpdb->prefix}job_manager_bookmarks",
                            array(
                                'user_id'       => get_current_user_id(),
                                'post_id'       => $post_id,
                                'bookmark_note' => $note,
                                'date_created'  => current_time('mysql')
                            )
                        );
                    } else {
                        $wpdb->update(
                            "{$wpdb->prefix}job_manager_bookmarks",
                            array(
                                'bookmark_note' => $note
                            ),
                            array(
                                'post_id' => $post_id,
                                'user_id' => get_current_user_id()
                            )
                        );
                    }

                    delete_transient('bookmark_count_' . $post_id);
                    $action_data = array('success' => true, 'note' =>  $note);
                }
            }
        }

   
        if (null === $action_data) {
            return;
        }
        if (!empty($_REQUEST['wpjm-ajax']) && !defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        if (wp_doing_ajax()) {
            wp_send_json($action_data, !empty($action_data['error_code']) ? $action_data['error_code'] : 200);
        } else {
            wp_redirect(remove_query_arg(array('submit_bookmark', 'remove_bookmark', '_wpnonce', 'wpjm-ajax')));
        }
    }

    /**
     * See if a post is bookmarked by ID
     * @param  int post ID
     * @return boolean
     */
    public function is_bookmarked($post_id)
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}job_manager_bookmarks WHERE post_id = %d AND user_id = %d;", $post_id, get_current_user_id())) ? true : false;
    }


    public function add_query_vars($vars)
    {

        $new_vars = array();
        $taxonomy_objects = get_object_taxonomies('task', 'objects');
        foreach ($taxonomy_objects as $tax) {
            array_push($new_vars, 'tax-' . $tax->name);
        }
        array_push($new_vars, 'search_keywords', 'location_search', 'workscout_freelancer_order');

        $vars = array_merge($new_vars, $vars);
        return $vars;
    }

    public static function build_available_query_vars()
    {
        $query_vars = array();
        $taxonomy_objects = get_object_taxonomies('task', 'objects');
        foreach ($taxonomy_objects as $tax) {
            array_push($query_vars, 'tax-' . $tax->name);
        }
        
        

        // $custom = Workscout_Freelancer_Meta_Boxes::meta_boxes_custom();
        // foreach ($custom['fields']  as $key => $field) {
        //     array_push($query_vars, $field['id']);
        // }
        // array_push($query_vars, '_hourl');

        return $query_vars;
    }

    public function pre_get_posts_tasks($query)
    {

        if (is_admin() || !$query->is_main_query()) {
            return $query;
        }
        if(class_exists('Kirki')){
            $per_page = Kirki::get_option('workscout', 'tasks_per_page');
        } else {
            $per_page = get_option('workscout_tasks_per_page', 10);
        }
        
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('task')) {
            
            $query->set('posts_per_page', $per_page);
            $query->set('post_type', 'task');
            $query->set('post_status', 'publish');
        }

        if (is_tax('task_category')  || is_tax('task_skill')) {

            
            $query->set('posts_per_page', $per_page);
        }

        if (is_post_type_archive('task') || is_author() || is_tax('task_category') || is_tax('task_skill')) {

            $ordering_args = WorkScout_Freelancer_Task::get_task_ordering_args();

            if (isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured') {
                $query->set('meta_key', $ordering_args['meta_key']);
            }

            $query->set('orderby', $ordering_args['orderby']);
            $query->set('order', $ordering_args['order']);

            $keyword = get_query_var('search_keywords');

            $keyword_search = get_option('workscout_freelancer_keyword_search', 'search_title');
            $search_mode = get_option('workscout_freelancer_search_mode', 'exact');

            $keywords_post_ids = array();
            $location_post_ids = array();
            if ($keyword) {
                global $wpdb;
                // Trim and explode keywords
                if ($search_mode == 'exact') {
                    $keywords = array_map('trim', explode('+', $keyword));
                } else {
                    $keywords = array_map('trim', explode(' ', $keyword));
                }


                // Setup SQL
                $posts_keywords_sql    = array();
                $postmeta_keywords_sql = array();
                // Loop through keywords and create SQL snippets
                foreach ($keywords as $keyword) {
                    # code...
                    if (strlen($keyword) > 2) {


                        // Create post meta SQL
                        if ($keyword_search == 'search_title') {
                            $postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%' AND meta_key IN ('task_subtitle','task_title','task_description','keywords') ";
                        } else {
                            $postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%'";
                        }

                        // Create post title and content SQL
                        $posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql($keyword) . "%' OR post_content LIKE '%" . esc_sql($keyword) . "%' ";
                    }
                }

                if (!empty($postmeta_keywords_sql)) {
                    // Get post IDs from post meta search

                    $post_ids = $wpdb->get_col("
					    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					    WHERE " . implode(' OR ', $postmeta_keywords_sql) . "
					");
                } else {
                    $post_ids = array();
                }


                // Merge with post IDs from post title and content search

                $keywords_post_ids = array_merge($post_ids, $wpdb->get_col("
					    SELECT ID FROM {$wpdb->posts}
					    WHERE ( " . implode(' OR ', $posts_keywords_sql) . " )
					    AND post_type = 'task'
					   
					"), array(0));
            }
            $location = get_query_var('location_search');

            if ($location) {

                $radius = get_query_var('search_radius');
                if (empty($radius) && get_option('workscout_radius_state') == 'enabled') {
                    $radius = get_option('workscout_maps_default_radius');
                }
                $radius_type = get_option('workscout_radius_unit', 'km');
                $geocoding_provider = get_option('workscout_geocoding_provider', 'google');
                if ($geocoding_provider == 'google') {
                    $radius_api_key = get_option('workscout_maps_api_server');
                } else {
                    $radius_api_key = get_option('workscout_geoapify_maps_api_server');
                }

                if (!empty($location) && !empty($radius) && !empty($radius_api_key)) {

                    //search by google
                    $latlng = workscout_geocode($address);
                    $nearbyposts = workscout_get_nearby_jobs($latlng[0], $latlng[1], $radius, $radius_type);
                    workscout_array_sort_by_column($nearbyposts, 'distance');

                    $location_post_ids = array_unique(array_column($nearbyposts, 'post_id'));

                    if (empty($location_post_ids)) {
                        $location_post_ids = array(0);
                    }
                } else {

                    //search by text
                    global $wpdb;
                    // Trim and explode keywords
                    $locations = array_map('trim', explode(',', $location));

                    // Setup SQL
                    $posts_locations_sql    = array();
                    $postmeta_locations_sql = array();
                    // Loop through keywords and create SQL snippets

                    if (get_option('workscout_search_only_address', 'off') == 'on') {
                        $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%'  AND meta_key = '_address'";
                        $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%'  AND meta_key = '_friendly_address'";
                    } else {
                        // Create post meta SQL
                        $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%' ";
                        // Create post title and content SQL
                        $posts_locations_sql[]    = " post_title LIKE '%" . esc_sql($locations[0]) . "%' OR post_content LIKE '%" . esc_sql($locations[0]) . "%' ";
                    }

                    // Get post IDs from post meta search
                    $post_ids = $wpdb->get_col("
					    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					    WHERE " . implode(' OR ', $postmeta_locations_sql) . "

					");

                    // Merge with post IDs from post title and content search
                    if (get_option('workscout_search_only_address', 'off') == 'on') {
                        $location_post_ids = array_merge($post_ids, array(0));
                    } else {
                        $location_post_ids = array_merge($post_ids, $wpdb->get_col("
						    SELECT ID FROM {$wpdb->posts}
						    WHERE ( " . implode(' OR ', $posts_locations_sql) . " )
						    AND post_type = 'task'
					    	AND post_status = 'publish'
						   
						"), array(0));
                    }
                }
            }

            if (sizeof($keywords_post_ids) != 0 && sizeof($location_post_ids) != 0) {
                $post_ids = array_intersect($keywords_post_ids, $location_post_ids);
                $query->set('post__in', $post_ids);
            } else if (sizeof($keywords_post_ids) != 0 && sizeof($location_post_ids) == 0) {
                $query->set('post__in', $keywords_post_ids);
            } else if (sizeof($keywords_post_ids) == 0 && sizeof($location_post_ids) != 0) {

                $query->set('post__in', $location_post_ids);
            }


            // if ( ! empty( $post_ids ) ) {
            //        $query->set( 'post__in', $post_ids );
            //    }

            $query->set('post_type', 'task');
            $args = array();
       
            $tax_query = array(
                'relation' => get_option('workscout_taxonomy_or_and', 'AND')
            );
            $taxonomy_objects = get_object_taxonomies('task', 'objects');

            foreach ($taxonomy_objects as $tax) {
               
                $get_tax = get_query_var('tax-' . $tax->name);

                if (is_array($get_tax)) {

                    $tax_query[$tax->name] = array('relation' => get_option('workscout_' . $tax->name . 'search_mode', 'OR'));

                    foreach ($get_tax as $key => $value) {
                        array_push($tax_query[$tax->name], array(
                            'taxonomy' =>   $tax->name,
                            'field'    =>   'slug',
                            'terms'    =>   $value,

                        ));
                    }
                } else {

                    if ($get_tax) {
                        if(is_numeric($get_tax)){
                            $term = get_term_by('id', $get_tax, $tax->name);
                            if ($term) {
                                array_push($tax_query, array(
                                    'taxonomy' =>  $tax->name,
                                    'field'    =>  'term_id',
                                    'terms'    =>  $term->term_id,
                                    'operator' =>  'IN'
                                ));
                            }
                        }else{
                            $term = get_term_by('slug', $get_tax, $tax->name);
                            if ($term) {
                                array_push($tax_query, array(
                                    'taxonomy' =>  $tax->name,
                                    'field'    =>  'slug',
                                    'terms'    =>  $term->slug,
                                    'operator' =>  'IN'
                                ));
                            }
                        }
                    }
                }
            }


            $query->set('tax_query', $tax_query);

            $available_query_vars = $this->build_available_query_vars();

            $meta_queries = array();


            // $selected_range = sanitize_text_field($form_data['filter_by_rate']);

            // $query_args['meta_query'][] = array(
            //     'key'     => '_rate_min',
            //     'value'   => array_map('absint', explode(',', $selected_range)),
            //     'compare' => 'BETWEEN',
            //     'type'    => 'NUMERIC'
            // );
				
            foreach ($available_query_vars as $key => $meta_key) {

                if (substr($meta_key, 0, 4) == "tax-") {
                    continue;
                }
                if ($meta_key == '_price_range') {
                    continue;
                }




                if (!empty($meta_min) && !empty($meta_max)) {

                    $meta_queries[] = array(
                        'key' =>  substr($meta_key, 0, -4),
                        'value' => array($meta_min, $meta_max),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC'
                    );
                    $meta_max = false;
                    $meta_min = false;
                } else if (!empty($meta_min) && empty($meta_max)) {
                    $meta_queries[] = array(
                        'key' =>  substr($meta_key, 0, -4),
                        'value' => $meta_min,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    );
                    $meta_max = false;
                    $meta_min = false;
                } else if (empty($meta_min) && !empty($meta_max)) {
                    $meta_queries[] = array(
                        'key' =>  substr($meta_key, 0, -4),
                        'value' => $meta_max,
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    );
                    $meta_max = false;
                    $meta_min = false;
                }

                if ($meta_key == '_price') {
                    $meta = get_query_var('_price_range');
                    if (!empty($meta) && $meta != -1) {

                        $range = array_map('absint', explode(',', $meta));

                        $meta_queries[] = array(
                            'relation' => 'OR',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => '_price_min',
                                    'value' => $range,
                                    'compare' => 'BETWEEN',
                                    'type' => 'NUMERIC',
                                ),
                                array(
                                    'key' => '_price_max',
                                    'value' => $range,
                                    'compare' => 'BETWEEN',
                                    'type' => 'NUMERIC',
                                ),
                              

                            ),
                            array(
                                'relation' => 'AND',
                                array(
                                    'key' => '_price_min',
                                    'value' => $range[0],
                                    'compare' => '<=',
                                    'type' => 'NUMERIC',
                                ),
                                array(
                                    'key' => '_price_max',
                                    'value' => $range[1],
                                    'compare' => '>=',
                                    'type' => 'NUMERIC',
                                ),

                            ),
                        );
                    }
                } else {
                    if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") {
                        continue;
                    }


                        $meta = get_query_var($meta_key);

                        if ($meta && $meta != -1) {
                            if (is_array($meta)) {
                                $meta_queries[] = array(
                                    'key'     => $meta_key,
                                    'value'   => array_keys($meta),
                                );
                            } else {
                                $meta_queries[] = array(
                                    'key'     => $meta_key,
                                    'value'   => $meta,
                                );
                            }
                        }
                    
                }
            }


            // var_dump($meta_queries);
            if (isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured') {


                $query->set('order', 'ASC DESC');
                $query->set('orderby', 'meta_value date');
                $query->set('meta_key', '_featured');
            }

            if (!empty($meta_queries)) {
                $query->set('meta_query', array(
                    'relation' => 'AND',
                    $meta_queries
                ));
            }
        }
    

        return $query;
    } /*eof function*/



    public static function get_task_ordering_args($orderby = '', $order = '')
    {

        // Get ordering from query string unless defined
        if ($orderby) {
            $orderby_value = $orderby;
        } else {
            $orderby_value = isset($_GET['workscout_freelancer_order']) ? (string) $_GET['workscout_freelancer_order']  : get_option('workscout_freelancer_sort_by', 'date');
        }

        // Get order + orderby args from string
        $orderby_value = explode('-', $orderby_value);
        $orderby       = esc_attr($orderby_value[0]);
        $order         = !empty($orderby_value[1]) ? $orderby_value[1] : $order;

        $args    = array();

        // default - menu_order
        $args['orderby']  = 'date ID'; //featured
        $args['order']    = ('desc' === $order) ? 'DESC' : 'ASC';
        $args['meta_key'] = '';

        switch ($orderby) {
            case 'rand':
                $args['orderby']  = 'rand';
                break;
            case 'featured':
                $args['orderby']  = 'meta_value_num date';
                $args['meta_key']  = '_featured';

                break;
            case 'verified':
                $args['orderby']  = 'meta_value_num';
                $args['meta_key']  = '_verified';

                break;
            case 'date':
                $args['orderby']  = 'date';
                $args['order']    = ('asc' === $order) ? 'ASC' : 'DESC';
                break;

         
            case 'views':
                $args['orderby']  = 'meta_value_num';
                $args['order']  = 'DESC';
                $args['meta_type'] = 'NUMERIC';
                $args['meta_key']  = '_task_views_count';
                break;
     

            case 'title':
                $args['orderby'] = 'title';
                $args['order']   = ('desc' === $order) ? 'DESC' : 'ASC';
                break;
            default:
                $args['orderby']  = 'date ID';
                $args['order']    = ('ASC' === $order) ? 'ASC' : 'DESC';
                break;
        }

        return apply_filters('workscout_freelancer_get_tasks_ordering_args', $args);
    }



    public static function get_tasks($args)
    {

        global $wpdb;

        global $paged;

        if (isset($args['workscout_orderby'])) {
            $ordering_args = WorkScout_Freelancer_Task::get_task_ordering_args($args['workscout_orderby']);
        } else {
            $ordering_args = WorkScout_Freelancer_Task::get_task_ordering_args();
        }



        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        $search_radius_var = get_query_var('search_radius');
        if (!empty($search_radius_var)) {
            $args['search_radius'] = $search_radius_var;
        }

        $radius_type_var = get_query_var('radius_type');
        if (!empty($radius_type_var)) {
            $args['radius_type'] = $radius_type_var;
        }

        $keyword_var = get_query_var('search_keywords');

        if (!empty($keyword_var)) {
            $args['keyword'] = $keyword_var;
        }


        $location_var = get_query_var('location_search');
        if (!empty($location_var)) {
            $args['location'] = $location_var;
        }

        $query_args = array(
            'query_label'              => 'workscout_get_task_query',
            'post_type'              => 'task',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => 1,
            'paged'                   => $paged,
            'posts_per_page'         => intval($args['posts_per_page']),
            'orderby'                => $ordering_args['orderby'],
            'order'                  => $ordering_args['order'],
            'tax_query'              => array(),
            'meta_query'             => array(),
        );


        if (isset($args['offset'])) {
            $query_args['offset'] = $args['offset'];
        }
        if (isset($ordering_args['meta_type'])) {
            $query_args['meta_type'] = $ordering_args['meta_type'];
        }
        if (isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured') {
            $query_args['meta_key'] = $ordering_args['meta_key'];
        }
        $keywords_post_ids = array();
        $location_post_ids = array();
        $keyword_search = get_option('workscout_keyword_search', 'search_title');
        $search_mode = get_option('workscout_search_mode', 'exact');

        if (isset($args['keyword']) && !empty($args['keyword'])) {


            if ($search_mode == 'exact') {
                $keywords = array_map('trim', explode('+', $args['keyword']));
            } else {
                $keywords = array_map('trim', explode(' ', $args['keyword']));
            }
            // Setup SQL

            $posts_keywords_sql    = array();
            $postmeta_keywords_sql = array();

            // $postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql( $keywords[0] ) . "%' ";
            // // Create post title and content SQL
            // $posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keywords[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $keywords[0] ) . "%' ";


            foreach ($keywords as $keyword) {
                # code...
                if (strlen($keyword) > 2) {
                    // Create post meta SQL

                    if ($keyword_search == 'search_title') {
                        $postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%' AND meta_key IN ('workscout_subtitle','task_title','task_description','keywords') ";
                    } else {
                        $postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%'";
                    }

                    // Create post title and content SQL
                    $posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql($keyword) . "%' OR post_content LIKE '%" . esc_sql($keyword) . "%' ";
                }
            }

            // Get post IDs from post meta search

            $post_ids = $wpdb->get_col("
				    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
				    WHERE " . implode(' OR ', $postmeta_keywords_sql) . "
				");

            // Merge with post IDs from post title and content search

            $keywords_post_ids = array_merge($post_ids, $wpdb->get_col("
				    SELECT ID FROM {$wpdb->posts}
				    WHERE ( " . implode(' OR ', $posts_keywords_sql) . " )
				    AND post_type = 'task'
				   
				"), array(0));
            /* array( 0 ) is set to return no result when no keyword was found */
        }

        if (isset($args['location']) && !empty($args['location'])) {
            $radius = $args['search_radius'];

            if (empty($radius)) {
                $radius =  get_option('workscout_maps_default_radius');
            }
            $radius_type = get_option('workscout_radius_unit', 'km');
            $radius_api_key = get_option('workscout_maps_api_server');
            $geocoding_provider = get_option('workscout_geocoding_provider', 'google');
            if ($geocoding_provider == 'google') {
                $radius_api_key = get_option('workscout_maps_api_server');
            } else {
                $radius_api_key = get_option('workscout_geoapify_maps_api_server');
            }

            if (!empty($args['location']) && !empty($radius) && !empty($radius_api_key)) {
                //search by google

                $latlng = workscout_geocode($args['location']);

                $nearbyposts = workscout_get_nearby_jobs($latlng[0], $latlng[1], $radius, $radius_type);

                workscout_array_sort_by_column($nearbyposts, 'distance');
                $location_post_ids = array_unique(array_column($nearbyposts, 'post_id'));

                if (empty($location_post_ids)) {
                    $location_post_ids = array(0);
                }
            } else {

                $locations = array_map('trim', explode(',', $args['location']));

                // Setup SQL

                $posts_locations_sql    = array();
                $postmeta_locations_sql = array();

                if (get_option('workscout_search_only_address', 'off') == 'on') {
                    $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%'  AND meta_key = '_address'";
                    $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%'  AND meta_key = '_friendly_address'";
                } else {
                    $postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql($locations[0]) . "%' ";
                    // Create post title and content SQL
                    $posts_locations_sql[]    = " post_title LIKE '%" . esc_sql($locations[0]) . "%' OR post_content LIKE '%" . esc_sql($locations[0]) . "%' ";
                }

                // Get post IDs from post meta search

                $post_ids = $wpdb->get_col("
				    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
				    WHERE " . implode(' OR ', $postmeta_locations_sql) . "

				");

                // Merge with post IDs from post title and content search
                if (get_option('workscout_search_only_address', 'off') == 'on') {
                    $location_post_ids = array_merge($post_ids, array(0));
                } else {
                    $location_post_ids = array_merge($post_ids, $wpdb->get_col("
					    SELECT ID FROM {$wpdb->posts}
					    WHERE ( " . implode(' OR ', $posts_locations_sql) . " )
					    AND post_type = 'task'
					    AND post_status = 'publish'
					   
					"), array(0));
                }
            }
        }

        if (sizeof($keywords_post_ids) != 0 && sizeof($location_post_ids) != 0) {
            $post_ids = array_intersect($keywords_post_ids, $location_post_ids);
            if (!empty($post_ids)) {
                $query_args['post__in'] = $post_ids;
            } else {

                $query_args['post__in'] = array(0);
            }
        } else if (sizeof($keywords_post_ids) != 0 && sizeof($location_post_ids) == 0) {
            $query_args['post__in'] = $keywords_post_ids;
        } else if (sizeof($keywords_post_ids) == 0 && sizeof($location_post_ids) != 0) {
            $query_args['post__in'] = $location_post_ids;
        }
        if (isset($query_args['post__in'])) {
            $posts_in_array = $query_args['post__in'];
        } else {
            $posts_in_array = array();
        }

        $posts_not_ids = array();

       

        $query_args['post__in'] = array_diff($posts_in_array, $posts_not_ids);
        $query_args['tax_query'] = array(
            'relation' => 'AND',
        );
        $taxonomy_objects = get_object_taxonomies('task', 'objects');


        foreach ($taxonomy_objects as $tax) {


            $get_tax = false;
            if ((isset($_GET['tax-' . $tax->name]) && !empty($_GET['tax-' . $tax->name]))) {
                $get_tax = $_GET['tax-' . $tax->name];
            } else {
                if (isset($args['tax-' . $tax->name])) {
                    $get_tax = $args['tax-' . $tax->name];
                }
            }

            if (is_array($get_tax)) {

                $query_args['tax_query'][$tax->name] =
                array('relation' => get_option('workscout_' . $tax->name . 'search_mode', 'OR'));
                foreach ($get_tax as $key => $value) {
                    array_push($query_args['tax_query'][$tax->name], array(
                        'taxonomy' =>   $tax->name,
                        'field'    =>   'ID',
                        'terms'    =>   $value,

                    ));
                }
            } else {

                if ($get_tax) {
                    if (is_numeric($get_tax)) {
                        $term = get_term_by('slug', $get_tax, $tax->name);
                        if ($term) {
                            array_push($query_args['tax_query'], array(
                                'taxonomy' =>  $tax->name,
                                'field'    =>  'ID',
                                'terms'    =>  $term->slug,
                                'operator' =>  'IN'
                            ));
                        }
                    } else {
                        $get_tax_array = explode(',', $get_tax);
                        //$query_args['tax_query'][$tax->name] = array('relation'=> 'OR');
                        array_push($query_args['tax_query'], array(
                            'taxonomy' =>  $tax->name,
                            'field'    =>  'ID',
                            'terms'    =>  $get_tax_array,

                        ));
                    }
                }
            }
        }
        
        $available_query_vars = WorkScout_Freelancer_Task::build_available_query_vars();
        $meta_queries = array();
        if (isset($args['featured'])  && !$args['featured']) {
            $available_query_vars[] = 'featured';
        }


        foreach ($available_query_vars as $key => $meta_key) {

            if (substr($meta_key, 0, 4) == "tax-") {
                continue;
            }
            if ($meta_key == '_price_range') {
                continue;
            }


            if ($meta_key == '_price') {

                $meta = false;
                if (!empty(get_query_var('_price_range'))) {
                    $meta = get_query_var('_price_range');
                } else if (isset($args['_price_range'])) {
                    $meta = $args['_price_range'];
                }
                if (!empty($meta)) {

                    $range = array_map('absint', explode(',', $meta));

                    $query_args['meta_query'][] = array(
                        'relation' => 'OR',
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => '_price_min',
                                'value' => $range,
                                'compare' => 'BETWEEN',
                                'type' => 'NUMERIC',
                            ),
                            array(
                                'key' => '_price_max',
                                'value' => $range,
                                'compare' => 'BETWEEN',
                                'type' => 'NUMERIC',
                            ),
                            array(
                                'key' => '_classifieds_price',
                                'value' => $range,
                                'compare' => 'BETWEEN',
                                'type' => 'NUMERIC',
                            ),

                        ),
                        // array(
                        //     'relation' => 'AND',
                        //     array(
                        //                     'key' => '_price_min',
                        //                     'value' => $range[0],
                        //                      'compare' => '>=',
                        //                     'type' => 'NUMERIC',
                        //                 ),
                        //                 array(
                        //                     'key' => '_price_max',
                        //                     'value' => $range[1],
                        //                     'compare' => '>=',
                        //                     'type' => 'NUMERIC',
                        //                 ),

                        // ),
                    );
                }
            } else {
                if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") {
                    continue;
                }
                $meta = false;



                if (!empty(get_query_var($meta_key))) {
                    $meta = get_query_var($meta_key);
                } else if (isset($args[$meta_key])) {

                    $meta = $args[$meta_key];
                }

                if ($meta) {

                    if ($meta === 'featured') {
                        $query_args['meta_query'][] = array(
                            'key'     => '_featured',
                            'value'   => 'on',
                            'compare' => '='
                        );
                    } else {
                   
                            if (is_array($meta)) {

                                $query_args['meta_query'][] = array(
                                    'key'     => $meta_key,
                                    'value'   => array_keys($meta),
                                    'compare' => 'IN'
                                );
                            } else {

                                $query_args['meta_query'][] = array(
                                    'key'     => $meta_key,
                                    'value'   => $meta,
                                );
                            }
                        
                    }
                }
            }
        }
        if (isset($args['filter_by_fixed_check']) && $args['filter_by_fixed_check'] == 'on') {
            if (isset($args['filter_by_fixed']) && !empty($args['filter_by_fixed'])) {
                $selected_range = sanitize_text_field($args['filter_by_fixed']);
                
                $range = array_map('absint', explode(',', $selected_range));
                $query_args['meta_query'][] = array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_budget_min',
                            'value' => $range,
                            'compare' => 'BETWEEN',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => '_budget_max',
                            'value' => $range,
                            'compare' => 'BETWEEN',
                            'type' => 'NUMERIC',
                        ),
                

                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_budget_min',
                            'value' => $range[0],
                            'compare' => '<=',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => '_budget_max',
                            'value' => $range[1],
                            'compare' => '>=',
                            'type' => 'NUMERIC',
                        ),


                    ),
                  
                );
                $query_args['meta_query'][] = array(
                    'key'     => '_task_type',
                    'value'   => 'fixed',
                    'compare' => '='
                );

            }
        }
        if (isset($args['filter_by_hourly_rate_check']) && $args['filter_by_hourly_rate_check'] == 'on') {

            if (isset($args['filter_by_hourly_rate']) && !empty($args['filter_by_hourly_rate'])) {
                $selected_range = sanitize_text_field($args['filter_by_hourly_rate']);
        
                
                $range = array_map('absint', explode(',', $selected_range));
                $query_args['meta_query'][] = array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_hourly_min',
                            'value' => $range,
                            'compare' => 'BETWEEN',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => '_hourly_max',
                            'value' => $range,
                            'compare' => 'BETWEEN',
                            'type' => 'NUMERIC',
                        ),
                

                    ),
                    // array(
                    //     'key'     => '_task_type',
                    //     'value'   => 'hourly',
                    //     'compare' => '='
                    // )
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_hourly_min',
                            'value' => $range[0],
                            'compare' => '<=',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => '_hourly_max',
                            'value' => $range[1],
                            'compare' => '>=',
                            'type' => 'NUMERIC',
                        ),


                    ),
                   
                );
                $query_args['meta_query'][] = array(
                    'key'     => '_task_type',
                    'value'   => 'hourly',
                    'compare' => '='
                );
            }
        }

        if (isset($args['featured']) && $args['featured'] !== 'null') {
            if ($args['featured'] == 'true' || $args['featured'] == true) {

                $query_args['meta_query'][] = array(
                    'key'     => '_featured',
                    'value'   => 'on',
                    'compare' => '='
                );
            }
        }

        if (isset($args['featured']) && $args['featured'] === 'null') {

            $query_args['meta_query'][] = array(
                'key'     => '_featured',
                'value'   => 'on',
                'compare' => '!='
            );
        }

workscout_write_log($query_args['meta_query']);

        if (isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured') {


            $query_args['order'] = 'ASC DESC';
            $query_args['orderby'] = 'meta_value date';
            $query_args['meta_key'] = '_featured';
        }


        //workscout_write_log($query_args['meta_query']);
        if (empty($query_args['meta_query']))
        unset($query_args['meta_query']);


  
        $query_args = apply_filters('workscout_freelancer_get_tasks', $query_args, $args);

        $result = new WP_Query($query_args);

        return $result;
    }



    public function ajax_get_tasks()
    {


        global $wp_post_types;

        $template_loader = new WorkScout_Freelancer_Template_Loader;

        $location      = (isset($_REQUEST['search_location'])) ? sanitize_text_field(stripslashes($_REQUEST['search_location'])) : '';
        $keyword       = (isset($_REQUEST['search_keywords'])) ? sanitize_text_field(stripslashes($_REQUEST['search_keywords'])) : '';
        $radius       = (isset($_REQUEST['search_radius'])) ?  sanitize_text_field(stripslashes($_REQUEST['search_radius'])) : '';


        $orderby       = (isset($_REQUEST['orderby'])) ?  sanitize_text_field(stripslashes($_REQUEST['orderby'])) : '';
        $order       = (isset($_REQUEST['order'])) ?  sanitize_text_field(stripslashes($_REQUEST['order'])) : '';

        $style       = sanitize_text_field(stripslashes($_REQUEST['style']));
        
        $per_page   = sanitize_text_field(stripslashes($_REQUEST['per_page']));
        


        $region         = (isset($_REQUEST['tax-region'])) ?  ($_REQUEST['tax-region']) : '';
        $category       = (isset($_REQUEST['tax-task_category'])) ?  ($_REQUEST['tax-task_category']) : '';
        $skill          = (isset($_REQUEST['tax-task_skill'])) ?  ($_REQUEST['tax-task_skill']) : '';

        $filter_by_hourly_rate_check = (isset($_REQUEST['filter_by_hourly_rate_check'])) ?  ($_REQUEST['filter_by_hourly_rate_check']) : '';
        $filter_by_hourly_rate = (isset($_REQUEST['filter_by_hourly_rate'])) ?  ($_REQUEST['filter_by_hourly_rate']) : '';
        $filter_by_fixed_check = (isset($_REQUEST['filter_by_fixed_check'])) ?  ($_REQUEST['filter_by_fixed_check']) : '';
        $filter_by_fixed = (isset($_REQUEST['filter_by_fixed'])) ?  ($_REQUEST['filter_by_fixed']) : '';
        $date_start = '';
        $date_end = '';

     

        if (empty($per_page)) {
            $per_page = Kirki::get_option('workscout', 'tasks_per_page');
        }

        $query_args = array(
            'ignore_sticky_posts'    => 1,
            'post_type'         => 'task',
            'orderby'           => $orderby,
            'order'             =>  $order,
            'offset'            => (absint($_REQUEST['page']) - 1) * absint($per_page),
            'location'           => $location,
            'keyword'           => $keyword,
            'search_radius'       => $radius,
            'posts_per_page'    => $per_page,
            'tax-task_skill'   => $skill,
            'filter_by_hourly_rate_check'  => $filter_by_hourly_rate_check,
            'filter_by_hourly_rate'  => $filter_by_hourly_rate,
            'filter_by_fixed_check'  => $filter_by_fixed_check,
            'filter_by_fixed'  => $filter_by_fixed,

        );

        $query_args['workscout_orderby'] = (isset($_REQUEST['workscout_core_order'])) ? sanitize_text_field($_REQUEST['workscout_core_order']) : false;

        $taxonomy_objects = get_object_taxonomies('task', 'objects');
        foreach ($taxonomy_objects as $tax) {
            if (isset($_REQUEST['tax-' . $tax->name])) {
                $query_args['tax-' . $tax->name] = $_REQUEST['tax-' . $tax->name];
            }
        }

        $available_query_vars = $this->build_available_query_vars();
        foreach ($available_query_vars as $key => $meta_key) {

            if (isset($_REQUEST[$meta_key]) && $_REQUEST[$meta_key] != -1) {

                $query_args[$meta_key] = $_REQUEST[$meta_key];
            }
        }


        // add meta boxes support

        $orderby = isset($_REQUEST['workscout_core_order']) ? $_REQUEST['workscout_core_order'] : 'date';


        // if ( ! is_null( $featured ) ) {
        // 	$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
        // }


        $tasks = WorkScout_Freelancer_Task::get_tasks(apply_filters('workscout_core_output_defaults_args', $query_args));
        $result = array(
            'found_tasks'    => $tasks->have_posts(),
            'max_num_pages' => $tasks->max_num_pages,
        );

        ob_start();
        if ($result['found_tasks']) {
            $style_data = array(
                'style'         => $style,
                //				'class' 		=> $custom_class, 
                //'in_rows' 		=> $in_rows, 
                
                'max_num_pages'    => $tasks->max_num_pages,
                'counter'        => $tasks->found_posts
            );
            //$template_loader->set_template_data( $style_data )->get_template_part( 'tasks-start' ); 
?>
            <div class="loader-ajax-container">
                <div class="loader-ajax"></div>
            </div>
            
            <?php
           
           
                while ($tasks->have_posts()) {
                    $tasks->the_post();
                 
                  
                    $template_loader->set_template_data($style_data)->get_template_part('content-task', $style);
                }
           
            ?>
            <div class="clearfix"></div>
            </div>
        <?php
            //$template_loader->set_template_data( $style_data )->get_template_part( 'tasks-end' ); 
        } else {
        ?>
            <div class="loader-ajax-container">
                <div class="loader-ajax"></div>
            </div>
            <?php
            $template_loader->get_template_part('no-task-found');
            ?><div class="clearfix"></div>
<?php
        }

        $result['html'] = ob_get_clean();
        $result['counter'] = $tasks->found_posts;
        $result['pagination'] = workscout_freelancer_ajax_pagination($tasks->max_num_pages, absint($_REQUEST['page']));

        wp_send_json($result);
    }
}
