<?php 
// ----------- FOOTER OPTIONS ----------

Kirki::add_section( 'footer', array(
    'title'          => esc_html__( 'Footer Options', 'workscout'  ),
    'description'    => esc_html__( 'Footer related options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 160,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

    Kirki::add_field( 'workscout', array(
        'type'        => 'upload',
        'settings'     => 'pp_footer_logo_upload',
        'label'       => esc_html__( 'Footer Logo image', 'workscout' ),
        'description' => esc_html__( 'Upload logo for footer top area', 'workscout' ),
        'section'     => 'footer',
        'default'     => '',
        'priority'    => 10,
    ) );


Kirki::add_field('workscout', array(
    'type'        => 'radio',
    'settings'    => 'workscout_custom_footer',
    'label'       => esc_html__('Enable custom footer colors', 'workscout'),
    'section'     => 'footer',
    'choices'     => array(
       
        'disable' => esc_attr__('Disable', 'workscout'),
        'enable'  => esc_attr__('Enable', 'workscout'),
    ),
    'priority'    => 10,
    'default'     => 'disable',
));

Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'workscout_footer_bg_color',
    'label'       => esc_html__('Select Footer color', 'workscout'),
    'section'     => 'footer',
    'default'     => '#fff',
    'priority'    => 10,
    'active_callback'  => array(
        array(
            'setting'  => 'workscout_custom_footer',
            'operator' => '==',
            'value'    => 'enable',
        ),
    )
));
Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'workscout_footer_text_color',
    'label'       => esc_html__('Select Footer text color', 'workscout'),
    'section'     => 'footer',
    'default'     => '#fff',
    'priority'    => 10,
    'active_callback'  => array(
        array(
            'setting'  => 'workscout_custom_footer',
            'operator' => '==',
            'value'    => 'enable',
        ),
    )
));
Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'workscout_footer_border_color',
    'label'       => esc_html__('Select Footer border color', 'workscout'),
    'section'     => 'footer',
    'default'     => '#fff',
    'priority'    => 10,
    'active_callback'  => array(
        array(
            'setting'  => 'workscout_custom_footer',
            'operator' => '==',
            'value'    => 'enable',
        ),
    )
));


$footer_icons = array();  
$footer_brand_icons = array();  
$faicons = workscout_icons_list();
foreach ($faicons as $key => $value) {
    $footer_icons['fa fa-'.$key] = $key.' (Font Awesome)';;
}
$brandicons = workscoutBrandIcons();

foreach ($brandicons as $key => $value) {
    // uppercase first letter of key
    $footer_icons['icon-brand-'.$key] = $value;
    $footer_brand_icons[$key] = $value;
}
$imicons = workscout_material_icons();

foreach ($imicons as $key) {
    // uppercase first letter of key
    $footer_icons[$key] = $key;
}
$feather_icons = workscout_feather_icons();
foreach ($feather_icons as $key => $value) {
    // remove from key beginning string "icon-feather-' and make first letter uppercase
    
    $footer_icons[$value] = ucfirst(str_replace('icon-feather-', '', $value)) . ' (Feather)';
}
    Kirki::add_field( 'workscout', array(
            'type'        => 'repeater',
            'label'       => esc_html__( 'Stats counter', 'kirki' ),
            'section'     => 'footer',
            'priority'    => 10,
            'row_label' => array(
                'type'  => 'text',
                'value' => esc_html__( 'Counter', 'kirki' ),
            ),
            'button_label' => esc_html__('"Add new" counter ', 'kirki' ),
            'settings'     => 'footer_stat_counters',
            
            'fields' => array(
                'icons' => array(
                    'type'        => 'select',
                    'label'       => esc_html__( 'Icon', 'kirki' ),
                    //'description' => esc_html__( 'This will be the label for your link', 'kirki' ),
                    'default'     => '',
                    'choices'     => $footer_icons
                ),
                 //'type' => '', //jobs, resumes, posts, members, candidates, employers, 
                'number'  => array(
                    'type'        => 'number',
                    'label'       => esc_html__( 'Number', 'kirki' ),
                    //'description' => esc_html__( 'This will be the link URL', 'kirki' ),
                    'default'     => '',
                ),
                'type' => array(
                    'type'        => 'select',
                    'label'       => esc_html__('Get value automatically from:', 'kirki'),
                    //'description' => esc_html__( 'This will be the label for your link', 'kirki' ),
                    'default'     => '',
                    'choices'     => array(
                        'manual' => 'Manual number',
                        'jobs' => 'Jobs',
                        'resumes' => 'Resumes',
                        'posts' => 'Posts',
                        'members' => 'Members',
                        'candidates' => 'Candidates',
                        'employers' => 'Employers',
                        'companies' => 'Companies',
                    ),
                ),
                'label'  => array(
                    'type'        => 'text',
                    'label'       => esc_html__( 'Label', 'kirki' ),
                    //'description' => esc_html__( 'This will be the link URL', 'kirki' ),
                    'default'     => '',
                ),
            )
        ) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'textarea',
	    'settings'    => 'pp_copyrights',
	    'label'       => esc_html__( 'Copyrights text', 'workscout' ),
	    'default'     => '&copy; Theme by Purethemes.net. All Rights Reserved.',
	    'section'     => 'footer',
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
        'type'        => 'text',
        'settings'    => 'pp_new_footer_widgets',
        'label'       => esc_html__( 'Footer widgets layout', 'workscout' ),
        'description' => esc_html__( 'Total width of footer is 12 columns, here you can decide layout based on columns number for each widget area in footer', 'workscout' ),
        'section'     => 'footer',
        'default'     => '2,2,2,4',
        'priority'    => 10,
       
	) );

    Kirki::add_field( 'workscout', array(
            'type'        => 'repeater',
            'label'       => esc_html__( 'Social Icons', 'kirki' ),
            'section'     => 'footer',
            'priority'    => 10,
            'row_label' => array(
                'type'  => 'text',
                'value' => esc_html__( 'Icon', 'kirki' ),
            ),
            'button_label' => esc_html__('"Add new" button label (optional) ', 'kirki' ),
            'settings'     => 'pp_footericons',
            
            'fields' => array(
                'icons_service' => array(
                    'type'        => 'select',
                    'label'       => esc_html__( 'Address of marker on map', 'kirki' ),
                    //'description' => esc_html__( 'This will be the label for your link', 'kirki' ),
                    'default'     => '',
                    'choices'     => $footer_brand_icons
                ),
                'icons_url'  => array(
                    'type'        => 'text',
                    'label'       => esc_html__( 'URL to profile page', 'kirki' ),
                    //'description' => esc_html__( 'This will be the link URL', 'kirki' ),
                    'default'     => '',
                ),
            )
        ) );
 ?>