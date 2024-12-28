<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPSight_Meta_Boxes class
 */
class WorkScout_Freelancer_User {

    static function on_load()
    {
        add_action('init', array(__CLASS__, 'init'));
        add_action('wp_insert_post', array(__CLASS__, 'wp_insert_post'), 10, 2);
        add_action('profile_update', array(__CLASS__, 'profile_update'), 10, 2);
        add_action('user_register', array(__CLASS__, 'profile_update'));
        add_filter('author_link', array(__CLASS__, 'author_link'), 10, 2);
        add_filter('get_the_author_url', array(__CLASS__, 'author_link'), 10, 2);
       // add_filter('workscout/my-account/custom-fields', array(__CLASS__, 'custom_fields'));

        add_filter('worscout_core_user_fields', array(__CLASS__, 'user_fields'));
    }

    static function user_fields($fields){
        $fields[] = 'freelancer_rate';
        $fields[] = 'freelancer_tagline';
        $fields[] = 'freelancer_country';
        return $fields;
    }

    static function custom_fields(){
        $template_loader = new WorkScout_Freelancer_Template_Loader;
        $template_loader->get_template_part('account/freelancer-fields');
    }
    static function init()
    {
        register_post_type(
            'workscout-freelancer',
            array(
                'labels'          => array('name' => 'Freelancer', 'singular_name' => 'Freelancer'),
                'public'          => true,
                'show_ui'         => true,
                'rewrite'         => array('slug' => 'freelancer'),
                'hierarchical'    => false,
                //'supports'        => array('title','editor','custom-fields'),
            )
        );
        $singular  = __('User Skill', 'workscout-freelancer');
        $plural    = __('Users Skills', 'workscout-freelancer');
        $rewrite   = array(
            'slug'         => _x('skill', 'Skill slug - resave permalinks after changing this', 'workscout-freelancer'),
            'with_front'   => false,
            'hierarchical' => false
        );
        $public    = true;
        register_taxonomy(
            "skill",
            apply_filters('register_taxonomy_user_types_object_type', array('workscout-freelancer')),
            apply_filters('register_taxonomy_user_types_args', array(
                'hierarchical'             => true,
                /*'update_count_callback' => '_update_post_term_count',*/
                'label'                 => $plural,
                'labels' => array(
                    'name'              => $plural,
                    'singular_name'     => $singular,
                    'menu_name'         => ucwords($plural),
                    'search_items'      => sprintf(__('Search %s', 'workscout-freelancer'), $plural),
                    'all_items'         => sprintf(__('All %s', 'workscout-freelancer'), $plural),
                    'parent_item'       => sprintf(__('Parent %s', 'workscout-freelancer'), $singular),
                    'parent_item_colon' => sprintf(__('Parent %s:', 'workscout-freelancer'), $singular),
                    'edit_item'         => sprintf(__('Edit %s', 'workscout-freelancer'), $singular),
                    'update_item'       => sprintf(__('Update %s', 'workscout-freelancer'), $singular),
                    'add_new_item'      => sprintf(__('Add New %s', 'workscout-freelancer'), $singular),
                    'new_item_name'     => sprintf(__('New %s Name', 'workscout-freelancer'),  $singular)
                ),
                'show_ui'                 => true,
                'show_in_rest' => true,
                'show_tagcloud'            => false,
                'public'                  => $public,
                /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
                'rewrite'                 => $rewrite,
            ))
        );
    }

    static function get_email_key()
    {
        return apply_filters('freelancer_email_key', '_email');
    }

    static function profile_update($user_id, $old_user_data = false)
    {
      
        global $wpdb;
        $is_new_freelancer = false;
        $user = get_userdata($user_id);
        $user_email = ($old_user_data ? $old_user_data->user_email : $user->user_email);
        $email_key = self::get_email_key();
        $freelancer_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='%s' AND meta_value='%s'", $email_key, $user_email));
        if (!is_numeric($freelancer_id)) {
            $freelancer_id = $is_new_freelancer = wp_insert_post(array(
                'post_type' => 'workscout-freelancer',
                'post_status' => 'publish',   // Maybe this should be pending or draft?
                'post_title' => $user->display_name,
            ));
        }
        update_user_meta($user_id, '_freelancer_id', $freelancer_id);
        update_post_meta($freelancer_id, '_user_id', $user_id);
        if ($is_new_freelancer || ($old_user_data && $user->user_email != $old_user_data->user_email)) {
            update_post_meta($freelancer_id, $email_key, $user->user_email);
        }
    }
    static function wp_insert_post($freelancer_id, $freelancer)
    {
        if ($freelancer->post_type == 'workscout-freelancer') {
            $email = get_post_meta($freelancer_id, self::get_email_key(), true);
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = get_user_by('email', $email);
                if ($user) { // Associate the user IF there is an user with the same email address
                    update_user_meta($user->ID, '_freelancer_id', $freelancer_id);
                    update_post_meta($freelancer_id, '_user_id', $user->ID);
                } else {
                    delete_post_meta($freelancer_id, '_user_id');
                }
            }
        }
    }
    static function get_user_id($freelancer_id)
    {
        return get_user_meta($user_id, '_user_id', true);
    }

    static function get_user($freelancer_id)
    {
        $user_id = self::get_user_id($freelancer_id);
        return get_userdata($user_id);
    }

    static function get_freelancer_id($user_id)
    {
        return get_user_meta($user_id, '_freelancer_id', true);
    }

    static function get_freelancer($user_id)
    {
        $freelancer_id = self::get_freelancer_id($user_id);
        return get_post($freelancer_id);
    }

    static function author_link($permalink, $user_id)
    {
        $author_id = get_user_meta($user_id, 'freelancer_profile', true);
        
        if ($author_id) {
            $permalink = get_post_permalink($author_id);
        }
        return $permalink;
    }
}
WorkScout_Freelancer_User::on_load();