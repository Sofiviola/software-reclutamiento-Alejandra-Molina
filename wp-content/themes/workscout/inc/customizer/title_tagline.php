<?php 


	Kirki::add_field( 'workscout', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_logo_upload',
	    'label'       => esc_html__( 'Logo image', 'workscout' ),
	    'description' => esc_html__( 'Upload logo for your website', 'workscout' ),
	    'section'     => 'title_tagline',
	    'default'     => '',
	    'priority'    => 10,
	) );	



	Kirki::add_field( 'workscout', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_transparent_logo_upload',
	    'label'       => esc_html__( 'Mobile Logo / Logo for transparent header', 'workscout' ),
	    'description' => esc_html__( 'If you need to use different logo in case yours not readable on transparent header', 'workscout' ),
	    'section'     => 'title_tagline',
	    'default'     => '',
	    'priority'    => 13,
	) );	


	Kirki::add_field( 'workscout', array(
		'type'        => 'slider',
		'settings'    => 'pp_retina_logo_height',
		'label'       => esc_html__( 'Logo image max height', 'workscout' ),
		'description' => esc_html__( 'Set image height for retina version in case the logo is displayed too big', 'workscout' ),
		'section'     => 'title_tagline',
		'default'     => '36',
		'choices'     => array(
			'min'  => '0',
			'max'  => '400',
			'step' => '1',
		),
		'priority'    => 15,
	) ); 	

	Kirki::add_field( 'workscout', array(
		'type'        => 'slider',
		'settings'    => 'pp_mobile_logo_height',
		'label'       => esc_html__( 'Mobile logo image max height', 'workscout' ),
		'description' => esc_html__( 'Set max image height for mobile version in case the logo is displayed too big', 'workscout' ),
		'section'     => 'title_tagline',
		'default'     => '36',
		'choices'     => array(
			'min'  => '0',
			'max'  => '200',
			'step' => '1',
		),
		'priority'    => 15,
	) ); 	



	?>