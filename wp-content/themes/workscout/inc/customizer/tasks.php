<?php 


Kirki::add_panel( 'tasks_panel', array(
    'priority'    => 21,
    'title'       => __( 'Tasks Options', 'sphene' ),
    'description' => __( 'Tasks related options', 'sphene' ),
) );


Kirki::add_section( 'tasks', array(
    'title'          => esc_html__( 'Tasks Options', 'workscout'  ),
    'description'    => esc_html__( 'Tasks related options', 'workscout'  ),
    //'panel'          => 'tasks_panel', // Not typically needed.
    'priority'       => 23,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

Kirki::add_field('workscout', array(
	'type'        => 'number',
	'settings'    => 'tasks_per_page',
	'label'       => esc_attr__('Tasks per page', 'workscout'),
	'section'     => 'tasks',
	'default'     => 10,
	'choices'     => array(
		'min'  => 1,
		'max'  => 50,
		'step' => 1,
	),
));	

Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'tasks_archive_layout',
	    'label'       => esc_html__( 'Tasks Page layout', 'workscout' ),
	    'section'     => 'tasks',
	    'description' => '',
	    'default'     => 'standard',
	    'priority'    => 10,
	    'choices'     => array(
	        'standard-list'	=> __( 'Classic List', 'workscout' ),
	        'standard-grid'	=> __( 'Classic Grid', 'workscout' ),
	        'full-page'	=> __( 'Full Page Grid', 'workscout' ),
	    ),
	) );

// Kirki::add_field('workscout', array(
// 	'type'        => 'select',
// 	'settings'    => 'tasks_list_layout',
// 	'label'       => esc_html__('Tasks list layout', 'workscout'),
// 	'section'     => 'tasks',
// 	'description' => '',
// 	'default'     => 'list',
// 	'priority'    => 10,
// 	'choices'     => array(
// 		'list'	=> __('List', 'workscout'),
// 		'grid'		=> __('Grid', 'workscout'),

// 	),
// ));

// Kirki::add_field( 'workscout', array(
// 	    'type'        => 'switch',
// 	    'settings'    => 'pp_tasks_taxonomies_description',
// 	    'label'       => esc_html__( 'Show taxonomies description on archives', 'workscout' ),
// 	    'section'     => 'tasks',
// 	    'description' => esc_html__( 'Set to ON to show category title and description', 'workscout' ),
// 	    'default'     => false,
// 	    'priority'    => 10,
	
// 	) );


Kirki::add_field('workscout', array(
	'type'        => 'radio-image',
	'settings'     => 'tasks_sidebar_layout',
	'label'       => esc_html__('Standard layout sidebar side', 'workscout'),
	'description' => esc_html__('Choose the sidebar side for tasks', 'workscout'),
	'section'     => 'tasks',
	'default'     => 'right',
	'priority'    => 10,
	'choices'     => array(
		'left' => trailingslashit(trailingslashit(get_template_directory_uri())) . '/images/left-sidebar.png',
		'right' => trailingslashit(trailingslashit(get_template_directory_uri())) . '/images/right-sidebar.png',
	),
));	
	// Kirki::add_field( 'workscout', array(
	//     'type'        => 'upload',
	//     'settings'     => 'pp_tasks_header_upload',
	//     'label'       => esc_html__( 'Tasks header image', 'workscout' ),
	//     'description' => esc_html__( 'Used on Tasks archive page. Set image for header, should be 1920px wide', 'workscout' ),
	//     'section'     => 'tasks',
	//     'default'     => '',
	//     'priority'    => 10,
	// ) );

	// Kirki::add_field( 'workscout', array(
	//     'type'        => 'select',
	//     'settings'    => 'pp_call_to_action_tasks',
	//     'label'       => esc_html__( 'Call to action button in header', 'workscout' ),
	//     'section'     => 'tasks',
	//     'description' => '',
	//     'default'     => 'resume',
	//     'priority'    => 10,
	//     'choices'     => array(
	//         'job'		=> __( 'Post a Job! It\'s Free!', 'workscout' ),
	//         'resume'	=> __( 'Post a Resume! It\'s Free!', 'workscout' ),
	//         'nothing' 	=> esc_html__( 'Show nothing', 'workscout' ),
	//     ),
	// ) );


	// Kirki::add_field( 'workscout', array(
	//     'type'        => 'switch',
	//     'settings'     => 'pp_resume_old_layout',
	//     'label'       => esc_html__( 'Enable old resume list layout', 'workscout' ),
	//     'description' => esc_html__( 'Layout before the 1.5v update', 'workscout' ),
	//     'section'     => 'tasks',
	//     'default'     => 0,
	//     'priority'    => 10,
	// ) );



	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'tasks_default_order',
	    'label'       => esc_html__( 'Tasks Archive order', 'workscout' ),
	    'section'     => 'tasks',
	    'description' => '',
	    'default'     => 'DESC',
	    'priority'    => 10,
	    'choices'     => array(
	    	'ASC' => 'ascending order from lowest to highest values (1, 2, 3; a, b, c).',
			'DESC' => 'descending order from highest to lowest values (3, 2, 1; c, b, a).',
	    ),
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'tasks_orderby',
	    'label'       => esc_html__( 'Resume Archive orderby', 'workscout' ),
	    'section'     => 'tasks',
	    'description' => '',
	    'default'     => 'title',
	    'priority'    => 10,
	    'choices'     => array(
	    	'featured'  => 'Featured.',
	    	'none'  => 'No order.',
			'ID'  => 'Order by post id. ',
			'author'  => 'Order by author.',
			'title'  => 'Order by title.',
			'name'  => 'Order by post name (post slug).',
			'date'  => 'Order by date.',
			'modified'  => 'Order by last modified date.',
			'rand'  => 'Random order.',
			'rand_featured'  => 'Random order with Featured on top.',
	    ),
	) );





	?>