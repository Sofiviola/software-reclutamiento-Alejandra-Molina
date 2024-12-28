<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;
$company_id = get_post_meta($post->ID, '_company_id', true);
// Ensure visibility.
if ( empty( $post ) ) {
    return;
} ?>
<div class="container full-width">

    <div class="company-contact-details">
        <?php if( ! ( function_exists( 'twentynineteen_can_show_post_thumbnail' ) && twentynineteen_can_show_post_thumbnail() ) ) : ?>
        <div class="company-data">
            <div class="company-logo">
                <?php $logo =  get_the_company_logo( null, 'thumbnail' ) ? get_the_company_logo( null, 'thumbnail' ) : apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' ); ?>
                <img src="<?php echo esc_url( $logo ) ?>" class="company-logo--image" alt="<?php the_title(); ?>">
            </div>
            <div class="company-data__content media-body">
                <?php 
                the_title( '<h1 class="company-title">', '</h1>' );
                endif;

                 if( ! empty ( $company_tagline = mas_wpjmc_get_the_meta_data( '_company_tagline' ) ) ) : ?>
                            <p class="company-data__content--list-item"><?php echo esc_html( $company_tagline ); ?></p>
                        <?php endif; 
                        if(function_exists('mas_wpjmcr_get_reviews_count') && mas_wpjmcr_get_reviews_average()){
                          echo mas_wpjmcr_reviews_get_stars();    
                        } else {
                            ?><span class="minimum_votes_req"><?php esc_html_e('No reviews yet','workscout') ?></span><?php
                        }
                if( ! empty ( mas_wpjmc_get_the_meta_data( '_company_website' ) )  || ! empty ( mas_wpjmc_get_the_meta_data( '_company_email' ) ) || ! empty ( mas_wpjmc_get_the_meta_data( '_company_twitter' ) ) || ! empty ( mas_wpjmc_get_the_meta_data( '_company_facebook' ) ) || ! empty ( mas_wpjmc_get_the_meta_data( '_company_phone' ) ) ) {
                    ?>
                    <div class="company-data__content--list _company_tagline">
                      
                        <?php if( ! empty ( $company_website = mas_wpjmc_get_the_meta_data( '_company_website' ) ) ) : ?>
                            <span class="company-data__content--list-item _company_website"><a class="website" href="<?php echo esc_url( $company_website ); ?>" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e( 'Website', 'workscout' ); ?></a></span>
                        <?php endif; ?>
                        <?php if( ! empty ( $company_email = mas_wpjmc_get_the_meta_data( '_company_email' ) ) ) : ?>
                            <span class="company-data__content--list-item _company_email">
                                <a href="mailto:<?php echo ( $company_email ); ?>" target="_blank"><i class="fa fa-envelope"></i>  <?php echo esc_html( $company_email ); ?></a>
                            </span>
                        <?php endif; ?>
                        <?php if( ! empty ( $company_twitter = mas_wpjmc_get_the_meta_data( '_company_twitter' ) ) ) : ?>
                            <span class="company-data__content--list-item _company_twitter">
                                <a href= "<?php echo get_the_mas_company_twitter(); ?>"> 
                                <i class="fa fa-twitter"></i>
                                @<?php echo get_the_mas_company_twitter() ?>
                            </a></span>
                           
                        <?php endif; ?>
                        <?php if( ! empty ( $company_facebook = mas_wpjmc_get_the_meta_data( '_company_facebook' ) ) ) : ?>
                            <span class="company-data__content--list-item _company_phone" ><a href="<?php echo esc_url( $company_facebook ); ?>">
                                <i class="fa fa-facebook"></i>
                                <?php esc_html_e('Facebook','workscout'); ?>
                            </a></span>

                        <?php endif; ?>
                        <?php if( ! empty ( $company_phone = mas_wpjmc_get_the_meta_data( '_company_phone' ) ) ) : ?>
                            <span class="company-data__content--list-item _company_phone">
                                <a href="tel:<?php echo ( $company_phone ); ?>" target="_blank"><i class="fa fa-phone"></i> 
                                    <?php echo esc_html( $company_phone ); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                if( ! ( function_exists( 'twentynineteen_can_show_post_thumbnail' ) && twentynineteen_can_show_post_thumbnail() ) ) : ?>
            </div>
        </div>
        <?php endif; ?>
    </div> 
    <!-- eof company details -->


    <div class="eleven columns ">
        <div class="padding-right">
            <?php if ( !empty( get_the_content() ) ) { ?>
            <h3><?php  echo esc_html__( 'About Us', 'workscout' ); ?></h3>
            <div id="company_content">
                <?php the_content(); ?>    
            </div>
            
            <?php } ?>

 
        </div>
    </div>
    

    <div class="five columns" id="job-details">
            <?php 
             $args = apply_filters( 'mas_wpjmc_single_company_features_args', array(
            'company_headquarters'  => array(
                'title' => esc_html__( 'Headquarters', 'workscout' ),
                'content' => mas_wpjmc_get_the_meta_data( '_company_headquarters' ),
                'icon' => 'icon-material-outline-business'
            ),
            'company_since'  => array(
                'title' => esc_html__( 'Founded', 'workscout' ),
                'content' => mas_wpjmc_get_the_meta_data( '_company_since' ),
                'icon' => 'icon-material-outline-date-range'
            ),
            'company_strength'  => array(
                'title' => esc_html__( 'Employees', 'workscout' ),
                'content' => mas_wpjmc_get_taxomony_data( 'company_strength' ),
                'icon' => 'icon-material-outline-supervisor-account'
            ),
            'company_category'  => array(
                'title' => esc_html__( 'Industry', 'workscout' ),
                'content' => mas_wpjmc_get_taxomony_data( 'company_category' ),
                'icon' => 'icon-material-outline-speaker-notes'
            ),
            'company_revenue'  => array(
                'title' => esc_html__( 'Revenue', 'workscout' ),
                'content' => mas_wpjmc_get_taxomony_data( 'company_revenue' ),
                'icon' => 'icon-material-outline-local-atm'
            ),
            'company_average_salary'  => array(
                'title' => esc_html__( 'Avg. Salary', 'workscout' ),
                'content' => mas_wpjmc_get_taxomony_data( 'company_average_salary' ),
                'icon' => 'icon-material-outline-local-atm'
            ),
        ) );

        if( is_array( $args ) && count( $args ) > 0 ) {
            $i = 0;
            foreach( $args as $key => $arg ) :
                if( isset( $arg['content'] ) && !empty( $arg['content'] ) ) :
                    $i++;
                    break;
                endif;
            endforeach;
            if( $i > 0 ) :
                ?>
                <div class="widget">
                        <h4><?php esc_html_e('Company Overview','workscout') ?></h4>
                    <div class="job-overview">
                        <ul>
                       

                    <?php
                    foreach( $args as $key => $arg ) :
                        
                        if( isset( $arg['content'] ) && !empty( $arg['content'] ) ) :
                        ?>  
                            <li class=" company-feature-<?php esc_attr_e($key) ?>" id="company-feature-<?php esc_attr_e($key) ?>">
                                <?php if(isset($arg['icon'])): ?><i class="<?php echo $arg['icon'] ?>"></i><?php endif; ?>
                                <div>
                                    <strong><?php echo wp_kses_post( $arg['title'] ); ?>:</strong>
                                    <span class="location" ><?php echo wp_kses_post( $arg['content'] ); ?></span>
                                </div>
                            </li>
                            
                        <?php
                        endif;
                    endforeach;
                ?>         
                        </ul>
                     </div>
                </div><?php
            endif;
        } ?>
        <?php 
        $single_map = Kirki::get_option( 'workscout', 'pp_enable_single_jobs_map' ); 
        $lng = $post->geolocation_long;
        $lng = get_post_meta($post->ID,'_geolocation_long',true);
        $lat = get_post_meta($post->ID,'_geolocation_lat',true);
        if($single_map && !empty($lng)) :
        ?>

            <div class="widget">
                <h4><?php esc_html_e('Location','workscout') ?></h4>
                
                <div id="job_map" data-longitude="<?php echo esc_attr( $lng); ?>" data-latitude="<?php echo esc_attr( $lat ); ?>">
                    
                </div>
            </div>

        <?php 
        endif;?>
        </div>
    
        
        <footer class="entry-footer">
                <?php edit_post_link( esc_html__( 'Edit', 'workscout' ), '<span class="edit-link">', '</span>' ); ?>
            </footer><!-- .entry-footer -->
        
    

    <?php // get_sidebar('jobs');?>

</div>
