<?php
/**
 * Register ACF fields for individual Events.
 */

add_action( 'acf/init', function() {

    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    // ── Event Details (all events) ────────────────────────────────────────────
    acf_add_local_field_group( array(
        'key'      => 'group_bcg_event_details',
        'title'    => 'Event Details',
        'location' => array( array( array(
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'events',
        ))),
        'fields' => array(

            // Event Type
            array(
                'key'           => 'field_bcg_event_type',
                'label'         => 'Event Type',
                'name'          => 'event_type',
                'type'          => 'select',
                'choices'       => array(
                    'standard'  => 'Standard (one-time)',
                    'recurring' => 'Recurring',
                ),
                'default_value' => 'standard',
                'wrapper'       => array( 'width' => '33' ),
                'instructions'  => 'Standard events expire after their date passes. Recurring events always show.',
            ),

            // Featured Event
            array(
                'key'           => 'field_bcg_event_featured',
                'label'         => 'Featured Event',
                'name'          => 'featured_event',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 0,
                'wrapper'       => array( 'width' => '33' ),
                'instructions'  => 'Promote this event to a full-width featured card above the main grid.',
            ),

            // Event Image
            array(
                'key'           => 'field_bcg_event_image',
                'label'         => 'Event Image',
                'name'          => 'event_image',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => array( 'width' => '33' ),
                'instructions'  => 'The image used on the event card. If nothing is set, it will use the fallback image from Settings.',
            ),
			 
			// Recurrence Description (recurring only)
            array(
                'key'          => 'field_bcg_event_recurrence',
                'label'        => 'Recurrence Description',
                'name'         => 'event_recurrence',
                'type'         => 'text',
                'wrapper'      => array( 'width' => '100' ),
                'instructions' => 'Describe when this event recurs, e.g. "Every Thursday, 6–8pm" or "First Saturday of each month". This is displayed on the event card.',
                'conditional_logic' => array( array( array(
                    'field'    => 'field_bcg_event_type',
                    'operator' => '==',
                    'value'    => 'recurring',
                ))),
            ),

            // Start Date
            array(
                'key'          => 'field_bcg_event_start_date',
                'label'        => 'Event Start Date',
                'name'         => 'event_start_date',
                'type'         => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format'  => 'Y-m-d',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'The start date of a multi-day event, or the date of a single-day event. Not used for recurring events.',
                'conditional_logic' => array( array( array(
                    'field'    => 'field_bcg_event_type',
                    'operator' => '==',
                    'value'    => 'standard',
                ))),
            ),

            // End Date
            array(
                'key'          => 'field_bcg_event_end_date',
                'label'        => 'Event End Date',
                'name'         => 'event_end_date',
                'type'         => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format'  => 'Y-m-d',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'If needed, set the end date of the event. If not set, nothing will show.',
                'conditional_logic' => array( array( array(
                    'field'    => 'field_bcg_event_type',
                    'operator' => '==',
                    'value'    => 'standard',
                ))),
            ),

            // Start Time
            array(
                'key'          => 'field_bcg_event_start_time',
                'label'        => 'Start Time',
                'name'         => 'event_start_time',
                'type'         => 'time_picker',
                'display_format' => 'g:i a',
                'return_format'  => 'H:i:s',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'The time the event starts. Leave blank if there is no specific time.',
            ),

            // End Time
            array(
                'key'          => 'field_bcg_event_end_time',
                'label'        => 'End Time',
                'name'         => 'event_end_time',
                'type'         => 'time_picker',
                'display_format' => 'g:i a',
                'return_format'  => 'H:i:s',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'The time the event ends. Leave blank if there is no specific time.',
            ),

            // Address
            array(
                'key'          => 'field_bcg_event_address',
                'label'        => 'Event Address',
                'name'         => 'event_address',
                'type'         => 'text',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Street address of the event. Will link to Google Maps in a new tab.',
            ),

            // Location
            array(
                'key'          => 'field_bcg_event_location',
                'label'        => 'Event Location',
                'name'         => 'event_location',
                'type'         => 'text',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Optional sub-location within the address, e.g. "Building A" or "Stage 3". If not set, nothing will show.',
            ),

            // Ticket Link
            array(
                'key'          => 'field_bcg_event_ticket_link',
                'label'        => 'Event Ticket Link',
                'name'         => 'event_ticket_link',
                'type'         => 'url',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Link to purchase tickets or an external event page.',
            ),

            // Ticket Link Text
            array(
                'key'          => 'field_bcg_event_ticket_link_text',
                'label'        => 'Ticket Button Text',
                'name'         => 'event_ticket_link_text',
                'type'         => 'text',
                'wrapper'      => array( 'width' => '50' ),
                'instructions' => 'Text for the ticket button. Defaults to "Tickets" if left blank.',
            ),

            // Description
            array(
                'key'          => 'field_bcg_event_description',
                'label'        => 'Event Description',
                'name'         => 'event_description',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 0,
            ),

        ),
    ));

});