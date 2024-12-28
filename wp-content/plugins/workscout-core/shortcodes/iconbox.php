<?php

/**
* Headline shortcode
* Usage: [iconbox title="Service Title" url="#" icon="37"] test [/headline]
*/
    function workscout_iconbox( $atts, $content ) {
      extract(shortcode_atts(array(
            'title'         => 'Search For Jobs',
            'url'           => '',
            'icon'          => 'ln ln-icon-Search-onCloud',
            'image'          => '',
            'type'          => 'rounded', // 'standard' // image
            'from_vs'       => 'no',
        ), $atts));

        if( $type == 'image' ){ 
            ob_start();
              ?>
            <div class="icon-box-2">
                <?php $file_url = wp_get_attachment_url( $image );
                $filetype = wp_check_filetype( $file_url ); ?>
                    <?php 
                    if($url) {
                    $link = vc_build_link( $url );
                    $a_href = $link['url'];
                    $a_title = $link['title'];
                    $a_target = $link['target'];
                    echo '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr($a_target).'">';
                }
                    if($filetype['ext'] == 'svg')  {
                        echo workscout_render_svg_icon($image); 
                    } else {
                        echo "<img src=".$file_url.">";
                    }
                    if($url) { echo "</a>"; } ?>

                    <h3><?php echo esc_html( $title ); ?></h3>
                    <p><?php echo do_shortcode( $content ); ?></p>
            </div>
        
        <?php
           $output =  ob_get_clean() ;
        } else {

            if( $type == 'rounded' ){
                $output = '<div class="icon-box rounded alt">';
            } else {
                $output = '<div class="icon-box alt">';
            }

            if($from_vs === "yes") { 
     
                if($url) {
                    $link = vc_build_link( $url );
                    $a_href = $link['url'];
                    $a_title = $link['title'];
                    $a_target = $link['target'];
                    $output .= '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr($a_target).'">';
                }
                $output .= '<i class="'.esc_attr($icon).'"></i>';
                if($url) {
                        $output .= '</a>';
                    }
                } else {
                    if($url) {
                        $output .= '<a href="'.$url.'">';
                    }
                    $output .= '<i class="'.esc_attr($icon).'"></i>';
                    if($url) {
                        $output .= '</a>';
                    }
                }
                if($url) {
                    if($from_vs === 'yes') { 

                        $link = vc_build_link( $url );
                        $a_href = $link['url'];
                        $a_title = $link['title'];
                        $a_target = $link['target'];
                        
                        $output .= '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr($a_target).'"><h4>'.$title.'</h4></a>';
                    } else {
                        $output .= '<a href="'.$url.'"><h4>'.$title.'</h4></a>';
                    }
                } else {
                    $output .= '<h4>'.$title.'</h4>';
                }
                $output .= '<p>'.do_shortcode( $content ).'</p>';
                $output .= '</div>';
        }

        return $output;
    }
    add_shortcode('iconbox', 'workscout_iconbox');
?>