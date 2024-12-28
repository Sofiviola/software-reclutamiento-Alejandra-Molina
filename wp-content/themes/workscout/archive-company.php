<?php

get_header();

$header_image = Kirki::get_option( 'workscout', 'pp_jobs_header_upload', '' );  
    
$layout = Kirki::get_option( 'workscout', 'pp_blog_layout' );
if(empty($layout)) { $layout = 'right-sidebar'; }
?>


    <div id="titlebar" class="single">


    <div class="container">
        <div class="sixteen columns">
            <div class="ten columns">
                <?php $count_jobs = wp_count_posts( 'company', 'readable' ); ?>
                    <span><?php printf( esc_html__( 'We have %s companies in our database', 'workscout' ), $count_jobs->publish ) ?></span>
                    <h2 class="showing_jobs"><?php esc_html_e('Showing all companies','workscout') ?></h2>
                
               
            </div>
            <!-- <div class="six columns">
                <a href="<?php echo get_permalink(get_option('job_manager_submit_company_form_page_id')); ?>" class="button"><?php esc_html_e('Add Company Profile','workscout'); ?></a>
            </div> -->
        </div>
    </div>
</div>


<div class="container  wpjm-container <?php echo esc_attr($layout); ?>">
    <div class="five columns sidebar"  role="complementary">

                <?php  dynamic_sidebar( 'sidebar-companies' ); ?>
 <form method="get" class="mas-wpjmc-search" action="">
    
  
    <input type="hidden" name="post_type" value="company"/>
    <?php 
      $query_vars = MAS_WPJMC::get_current_page_query_args();
      foreach( $query_vars as $key => $value ) {
        if( $key !== 's' ) {
            echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"/>';
        }
    } ?>

      
        <?php 
        if ( ! empty( $_GET['search_keywords'] ) ) {
            $keywords = sanitize_text_field( $_GET['search_keywords'] );
        } else {
            $keywords = '';
        }
    ?>
     <div class="widget job-widget-keywords">
              <h4><?php esc_html_e('Search keywords','workscout') ?></h4>
              <input type="text" id="search_keywords" class="search-field" placeholder="<?php echo esc_attr__( 'Company title or keywords', 'workscout' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
  
        </div>
        <?php $taxonomies_args = apply_filters( 'mas_company_taxonomies_list', array(
            'company_category'  => array(
                'singular'                  => esc_html__( 'Industry', 'workscout' ),
                'plural'                    => esc_html__( 'Industries', 'workscout' ),
                'slug'                      => esc_html_x( 'company-category', 'Company category permalink - resave permalinks after changing this', 'workscout' ),
                'enable'                    => get_option('job_manager_company_enable_company_category', true)
            ),
            'company_strength' => array(
                'singular'                  => esc_html__( 'Employee Strength', 'workscout' ),
                'plural'                    => esc_html__( 'Employees Strength', 'workscout' ),
                'slug'                      => esc_html_x( 'company-employees-strength', 'Company employees strength permalink - resave permalinks after changing this', 'workscout' ),
                'enable'                    => get_option('job_manager_company_enable_company_strength', true)
            ),
            'company_average_salary'    => array(
                'singular'                  => esc_html__( 'Avg. Salary', 'workscout' ),
                'plural'                    => esc_html__( 'Avg. Salary', 'workscout' ),
                'slug'                      => esc_html_x( 'company-average-salary', 'Company avarage salary permalink - resave permalinks after changing this', 'workscout' ),
                'enable'                    => get_option('job_manager_company_enable_average_salary', true)
            ),
            'company_revenue'    => array(
                'singular'                  => esc_html__( 'Revenue', 'workscout' ),
                'plural'                    => esc_html__( 'Revenue', 'workscout' ),
                'slug'                      => esc_html_x( 'company-revenue', 'Company revenue permalink - resave permalinks after changing this', 'workscout' ),
                'enable'                    => get_option('job_manager_company_enable_company_revenue', true)
            ),
        ) ); 

        foreach ( $taxonomies_args as $taxonomy_name => $taxonomy_args ) {
            if( $taxonomy_args['enable'] ) {

                $singular  = $taxonomy_args['singular'];
                $plural    = $taxonomy_args['plural'];
                $slug      = $taxonomy_args['slug']; 

            if ( !empty( $_GET['filter_'.$taxonomy_name] ) ) {
                    $selected = sanitize_text_field( $_GET['filter_'.$taxonomy_name] );
                } else {
                    $selected = "";
                }
                
                ?>
                    <div class="widget job-widget-<?php echo $slug; ?>">
                                <h4><?php echo $taxonomy_args['singular']; ?></h4>
                                    <?php
                                        $dropdown = wp_dropdown_categories( array(
                                            'show_option_all'           => $singular,
                                            'hierarchical'              => true,
                                            'orderby'                   => 'name',
                                            'taxonomy'                  => $taxonomy_name,
                                            'name'                      => 'filter_'.$taxonomy_name,
                                            'id'                        => $taxonomy_name,
                                            'class'                     => 'select2-single job-manager-category-dropdown',
                                            'hide_empty'                => 1,
                                            'value_field'      => 'slug',
                                            'selected'                  => $selected,
                                            'echo'                      => true,
                                        )  );
                                       // $fixed_dropdown = str_replace("&nbsp;", "", $dropdown); echo $fixed_dropdown; 

                                    ?>
                            </div>
                

                <?php
            }
        }

        ?>
        
        <div class="widget job-widget-regions">
           <input type="submit" class="button" value="<?php esc_attr_e('Search','workscout') ?>">
        </div>
       </form>
 
    </div>
    <article id="post-<?php the_ID(); ?>" <?php post_class('eleven columns'); ?>>
        <div class="padding-right">
            <?php 
 
                ?>
            <?php if ( have_posts() ) : ?>
                <ul class="wpjmc-companies">
                           <?php while ( have_posts() ) : the_post();

                        do_action( 'company_loop' );

                        get_job_manager_template_part( 'content', 'company', 'mas-wp-job-manager-company', mas_wpjmc()->plugin_dir . 'templates/' );

                    endwhile; // End of the loop. ?>
                </ul>
                <?php mas_wpjmc_pagination(); ?>
                <?php else : ?>

            <article class="post">

 <section class="post-content">

    <h3><?php esc_html_e( 'Nothing Found', 'workscout' ); ?></h3>
   
    
<div class="page-content">
        <?php
        if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'workscout' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

        <?php elseif ( is_search() ) : ?>

            <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'workscout' ); ?></p>
            <?php
                
            else : ?>

            <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'workscout' ); ?></p>
            
        <?php endif; ?>
    </div><!-- .page-content -->



  </section>

</article>

        <?php endif; ?>
            <footer class="entry-footer">
                <?php edit_post_link( esc_html__( 'Edit', 'workscout' ), '<span class="edit-link">', '</span>' ); ?>
            </footer><!-- .entry-footer -->
        </div>
    </article>
    

</div>
<?php

get_footer();