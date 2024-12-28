<?php

/*section typography*/ 
Kirki::add_section( 'typography', array(
    'title'          => esc_html__( 'Typography', 'workscout'  ),
    'description'    => esc_html__( 'Fonts options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 60,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
		'type'        => 'typography',
		'settings'    => 'pp_body_font',
		'label'       => esc_attr__( 'Body font', 'workscout' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Poppins, sans-serif',
			
			//'letter-spacing' => '0',
			'subsets'        => array( 'latin-ext' ),
			//'color'          => '#666',
			
			//'text-align'     => 'left'
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => 'body, p, a, input, input[type="text"], input[type="password"], input[type="email"],
textarea, select, input[type="button"], input[type="submit"], button, body .pac-container,body .wp-subscribe-wrap input.submit,
body input[type="email"],
.new-header #navigation ul li a,
.button,
body .input-text,
.intro-banner .intro-banner-search-form input',
			),
		),
	) );	



	Kirki::add_field( 'workscout', array(
		'type'        => 'typography',
		'settings'    => 'pp_headers_font',
		'label'       => esc_attr__( 'h1..h6 font', 'workscout' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Arial, sans-serif',
			'variant'        => 'regular',
			'subsets'        => array( 'latin-ext' ),
			
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => 'h1,h2,h3,h4,h5,h6',
			),
		),
	) );

	// Kirki::add_field( 'workscout', array(
	// 	'type'        => 'typography',
	// 	'settings'    => 'pp_menu_font',
	// 	'label'       => esc_attr__( 'Menu font', 'workscout' ),
	// 	'section'     => 'typography',
	// 	'default'     => array(
	// 		'font-family'    => 'Montserrat',
	// 		'variant'        => '600',
	// 		'font-size'      => '13px',
	// 		'line-height'    => '19px',
	// 		'subsets'        => array( 'latin-ext' ),

	// 		'text-transform' => 'none',
	// 		'text-align'     => 'left'
			
	// 	),
	// 	'priority'    => 10,
	// 	'output'      => array(
	// 		array(
	// 			'element' => '#navigation > ul > li > a',
	// 		),
	// 	),
	// ) );

	?>