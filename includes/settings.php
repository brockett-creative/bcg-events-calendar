<?php
/**
 * Plugin settings page and color injection.
 * Registers an ACF options page under the Events CPT menu.
 */

// Register the options page under Events
add_action( 'acf/init', function() {

    if ( ! function_exists( 'acf_add_options_page' ) ) return;

    acf_add_options_sub_page( array(
        'page_title'  => 'Events Settings',
        'menu_title'  => 'Settings',
        'parent_slug' => 'edit.php?post_type=events',
        'capability'  => 'manage_options',
        'option_key'  => 'bcg_events_settings',
    ));

    acf_add_local_field_group( array(
        'key'      => 'group_bcg_events_settings',
        'title'    => 'Events Settings',
        'location' => array( array( array(
            'param'    => 'options_page',
            'operator' => '==',
            'value'    => 'acf-options-settings',
        ))),
        'fields' => array(

            // Featured event toggle
            array(
                'key'          => 'field_bcg_use_featured',
                'label'        => 'Show Featured Event at Top of Grid',
                'name'         => 'use_featured_event',
                'type'         => 'true_false',
                'ui'           => 1,
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'When enabled, the next upcoming event is displayed as a featured card above the main grid.',
            ),

            // Fallback image
            array(
                'key'           => 'field_bcg_fallback_image',
                'label'         => 'Fallback Image for Event Cards',
                'name'          => 'event_fallback_image',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => array( 'width' => '50' ),
                'instructions'  => 'Used as the card image when an individual event has no image set.',
            ),

            // Primary color
            array(
                'key'          => 'field_bcg_color_primary',
                'label'        => 'Primary Color',
                'name'         => 'color_primary',
                'type'         => 'color_picker',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Used on buttons, tags, and accent elements. Defaults to #1e1c1c if not set.',
            ),

            // Text color
            array(
                'key'          => 'field_bcg_color_text',
                'label'        => 'Text Color',
                'name'         => 'color_text',
                'type'         => 'color_picker',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Used for text on colored elements (e.g. button text). Defaults to #ffffff if not set.',
            ),

        ),
    ));

});

// Inject plugin color settings as CSS variables into <head>
add_action( 'wp_head', function() {

    $primary = get_field( 'color_primary', 'option' );
    $primary = ! empty( $primary ) ? esc_attr( $primary ) : '#1a1a1a';

    $text = get_field( 'color_text', 'option' );
    $text = ! empty( $text ) ? esc_attr( $text ) : '#ffffff';

    ?>
    <style>
        :root {
            --bcg-clr-primary: <?php echo $primary; ?>;
            --bcg-clr-text:    <?php echo $text; ?>;
        }
    </style>
    <?php
});
