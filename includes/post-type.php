<?php
/**
 * Register the Events custom post type.
 */

add_action( 'init', function() {
    register_post_type( 'events', array(
        'labels' => array(
            'name'               => __( 'Events', 'bcg-events-calendar' ),
            'singular_name'      => __( 'Event', 'bcg-events-calendar' ),
            'menu_name'          => __( 'Events', 'bcg-events-calendar' ),
            'all_items'          => __( 'All Events', 'bcg-events-calendar' ),
            'add_new_item'       => __( 'Add New Event', 'bcg-events-calendar' ),
            'edit_item'          => __( 'Edit Event', 'bcg-events-calendar' ),
            'view_item'          => __( 'View Event', 'bcg-events-calendar' ),
            'not_found'          => __( 'No events found', 'bcg-events-calendar' ),
            'not_found_in_trash' => __( 'No events found in Trash', 'bcg-events-calendar' ),
        ),
        'public'             => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-calendar-alt',
        'menu_position'      => 10,
        'supports'           => array( 'title' ),
        'has_archive'        => true,
        'capability_type'    => 'post',
    ));
});
