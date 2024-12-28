<?php
/**
 * Template Name: Page with Jobs Search Boxed
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0
 */
$header_old = Kirki::get_option('workscout','pp_old_header');
$bg_type = Kirki::get_option('workscout','pp_jobs_search_boxed_bg_type');
$header_type = (Kirki::get_option('workscout','pp_old_header') == true) ? 'old' : '' ;
$header_type = apply_filters('workscout_header_type',$header_type);
get_header($header_type); ?>
<?php $fancy_header = get_post_meta($post->ID, 'pp_transparent_header','true'); ?>



<!-- Intro Banner
================================================== -->
<div class="intro-banner boxed intro-bg-type-<?php echo $bg_type; ?>" <?php  if($bg_type == 'image') echo workscout_get_new_search_header();?>>

    <!-- Transparent Header Spacer -->
    <div class="transparent-header-spacer"></div>
        <div class="container">
        
            <div class="boxed-search-container">
            <!-- Intro Headline -->
            <div class="banner-headline-alt">
                <h3>
                    <strong><?php echo Kirki::get_option( 'workscout','pp_jobs_home_title','Find Job');  ?></strong>
                    <span><?php echo Kirki::get_option( 'workscout','pp_jobs_home_subtitle','Hire experts or be hired in ');  ?> 
                         <?php if(get_option('pp_jobs_home_typed_status','enable') == 'enable') { ?> <div class="typed-words"></div><?php } ?>
                     </span>
                </h3>
            </div>
              
            <?php 
                $search_elements = Kirki::get_option( 'workscout', 'pp_job_search_elements',array('keywords','location')); 
                $el_nr = count($search_elements); 
            ?>
            <form method="GET"  class="workscout_main_search_form" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">
            <!-- Search Bar -->
            <div class="intro-banner-search-form">
            
                <?php if (in_array("keywords", $search_elements)) : ?>
                    <!-- Search Field -->
                    <div class="intro-search-field">
                        <input id="intro-keywords" name="search_keywords" type="text" placeholder="<?php esc_attr_e( 'Job title, Skill, Industry', 'workscout' ); ?>">
                    </div>
                <?php endif; ?>
                
                <?php if (in_array("location", $search_elements)) : ?>
                <!-- Search Field -->
                    <div class="intro-search-field with-autocomplete">
                        <?php if ( class_exists('Astoundify_Job_Manager_Regions') && get_option( 'job_manager_regions_filter' ) || is_tax( 'job_listing_region' ) ) {  ?>
                        <?php
                        $dropdown = wp_dropdown_categories( array(
                            'show_option_all'           => __( 'All Regions', 'workscout' ),
                            'hierarchical'              => true,
                            'orderby'                   => 'name',
                            'taxonomy'                  => 'job_listing_region',
                            'name'                      => 'search_region',
                            'id'                        => 'search_location',
                            'class'                     => 'search_region select-on-home job-manager-category-dropdown',
                            'hide_empty'                => 1,
                            'selected'                  => isset( $_GET[ 'search_region' ] ) ? $_GET[ 'search_region' ] : '',
                            'echo'                      => false,
                        )  );
                        $fixed_dropdown = str_replace("&nbsp;", "", $dropdown); echo $fixed_dropdown;
                        } else { ?>
                            <div class="input-with-icon location">
                                <input id="search_location" name="search_location" type="text" placeholder="<?php esc_attr_e('City, State or Zip','workscout'); ?>">
                                
                                <a href="#"><i title="<?php esc_html_e('Find My Location','workscout') ?>" class="tooltip left la la-map-marked-alt"></i></a>
                                <?php if(get_option('workscout_map_address_provider','osm') == 'osm') : ?><span class="type-and-hit-enter"><?php esc_html_e('type and hit enter','workscout') ?></span> <?php endif; ?>
                            </div>
                        <?php } ?>
                       
                    </div>
                <?php endif; ?>

                
                <?php if (in_array("category", $search_elements)) :   ?>
                <!-- Search Field -->
                    <div class="intro-search-field">
                        <?php 
                        
                 
                            $html =  wp_dropdown_categories( 
                                array(
                                    'taxonomy'          => 'job_listing_category',
                                    'name'              => 'search_category',
                                    'orderby'           => 'name',
                                    'class'             => 'select-on-home',
                                    'hierarchical'      => true,
                                    'hide_empty'        => true ,
                                    'show_option_all'   => esc_html__('All Categories','workscout'),
                                    'echo' => 0
                                    )
                                );
                            echo str_replace( '&nbsp;&nbsp;&nbsp;', '- ', $html );                   
                    ?>
                    </div>
                <?php endif; ?>

                <!-- Button -->
                <div class="intro-search-button">
                    <button class="button ripple-effect">
                        <span><?php esc_html_e('Search','workscout') ?></span>
                        <i></i>
                    </button>
                </div>

            </div>
            </form>

        </div>
    
            <!-- Stats -->
            <?php if(Kirki::get_option('workscout','pp_home_job_advanced')) {  ?>
            <div class="row">
                <div class="sixteen columns">
                    <div class="adv-search-btn">
                        <span><?php esc_html_e('Need more search options?','workscout') ?> </span>
                        <a href="<?php echo get_post_type_archive_link( 'job_listing' ) ?>"><?php esc_html_e('Advanced Search','workscout') ?> <i class="la la-long-arrow-alt-right"></i></a>
                    </div>
                </div>
            </div>
            <?php } ?>
            
    </div>
</div>

<?php
while ( have_posts() ) : the_post(); ?>
<!-- 960 Container -->
<div class="container page-container home-page-container">
    <article <?php post_class("sixteen columns"); ?>>
                <?php the_content(); ?>
    </article>
</div>
<?php endwhile; // end of the loop.

get_footer(); ?>