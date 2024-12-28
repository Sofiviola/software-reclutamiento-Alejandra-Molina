<?php

Kirki::add_section('dashboard_panel', array(
    'title'       => __('Dashboard Options', 'workscout'),
    'description' => __('Options related to front-end dashboard for users', 'workscout'),
    'panel'          => '', // Not typically needed.
    'priority'       => 24,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
));



Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'dashboard_box_color_1',
    'label'       => esc_html__('Select 1st box color', 'workscout'),
    'section'     => 'dashboard_panel',
    'default'     => '#36bd78',
    'priority'    => 10,
));

Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'dashboard_box_color_2',
    'label'       => esc_html__('Select 2nd box color', 'workscout'),
    'section'     => 'dashboard_panel',
    'default'     => '#b81b7f',
    'priority'    => 10,
));

Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'dashboard_box_color_3',
    'label'       => esc_html__('Select 3rd box color', 'workscout'),
    'section'     => 'dashboard_panel',
    'default'     => '#efa80f',
    'priority'    => 10,
));

Kirki::add_field('workscout', array(
    'type'        => 'color',
    'settings'    => 'dashboard_box_color_4',
    'label'       => esc_html__('Select 4th box color', 'workscout'),
    'section'     => 'dashboard_panel',
    'default'     => '#2a41e6',
    'priority'    => 10,
));


?>