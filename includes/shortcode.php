<?php
/**
 * [events_archive]    — upcoming standard events grid.
 * [recurring_events]  — recurring events grid (always shown).
 * [single_event]      — single event detail view for Elementor templates.
 */


/* ── Shared helper: render an event card ──────────────────────────────────── */
function bcg_render_event_card( $post, $fallback_image, $type = 'standard' ) {
    $image      = get_field( 'event_image', $post->ID ) ?: $fallback_image;
    $address    = get_field( 'event_address', $post->ID );
    $start_date = get_field( 'event_start_date', $post->ID );
    $recurrence = get_field( 'event_recurrence', $post->ID );
    ?>
	<a class="event-card" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
		<div class="ec-top">
			<?php if ( $image ) : ?>
				<img class="ec-image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>" />
			<?php endif; ?>
		</div>
		<div class="ec-content">
			<h2 class="ec-title"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h2>
			<div class="ec-details">
				<?php if ( $type === 'recurring' && $recurrence ) : ?>
					<p class="ec-recurrence"><?php echo esc_html( $recurrence ); ?></p>
				<?php elseif ( $start_date ) : ?>
					<p class="ec-date"><?php echo esc_html( date( 'F j, Y', strtotime( $start_date ) ) ); ?></p>
				<?php endif; ?>
				<?php if ( $address ) : ?>
					<p class="ec-address"><?php echo esc_html( $address ); ?></p>
				<?php endif; ?>
			</div>
			<div class="ec-button-container">
				<span class="ec-button">View details</span>
			</div>
		</div>
	</a>
    <?php
}

/* ── Shared helper: render a FULL-WIDTH featured event card ───────────────── */
function bcg_render_featured_event_card( $post, $fallback_image, $type = 'standard' ) {
    $image      = get_field( 'event_image', $post->ID ) ?: $fallback_image;
    $start_date = get_field( 'event_start_date', $post->ID );
    $start_time = get_field( 'event_start_time', $post->ID );
    $location   = get_field( 'event_location', $post->ID );
    $recurrence = get_field( 'event_recurrence', $post->ID );
    ?>
    <div class="featured-event-card">
        <div class="fec-left">
            <?php if ( $image ) : ?>
                <img class="ec-image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>" />
            <?php endif; ?>
        </div>
        <div class="fec-right">
            <div>
                <span class="ec-tag">Featured</span>
                <h2 class="ec-title"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h2>
                <div class="ec-details">
                    <?php if ( $type === 'recurring' && $recurrence ) : ?>
                        <p class="ec-recurrence"><?php echo esc_html( $recurrence ); ?></p>
                    <?php elseif ( $start_date ) : ?>
                        <p class="ec-date"><?php echo esc_html( date( 'F j, Y', strtotime( $start_date ) ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( $start_time ) : ?>
                        <p class="ec-time"><?php echo esc_html( date( 'g:i a', strtotime( $start_time ) ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( $location ) : ?>
                        <p class="ec-location"><?php echo esc_html( $location ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="ec-button-container">
                <a class="ec-button" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">View details</a>
            </div>
        </div>
    </div>
    <?php
}


/* ================================================
   STANDARD EVENTS ARCHIVE
   [events_archive]
   ================================================ */
add_shortcode( 'events_archive', function() {

    $fallback_image = get_field( 'event_fallback_image', 'option' );

    // Shared: upcoming standard events only
    $upcoming_clause = array(
        'relation' => 'AND',
        array(
            'key'     => 'event_start_date',
            'value'   => date( 'Ymd' ),
            'compare' => '>=',
        ),
        array(
            'relation' => 'OR',
            array(
                'key'     => 'event_type',
                'value'   => 'standard',
                'compare' => '=',
            ),
            array(
                'key'     => 'event_type',
                'compare' => 'NOT EXISTS',
            ),
        ),
    );

    ob_start();
    echo '<div class="bcg-events-wrap">';

    // --- Featured events (full-width, manually flagged) ---
    $featured_query = new WP_Query( array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_start_date',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            $upcoming_clause,
            array(
                'key'     => 'featured_event',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
    ) );

    if ( $featured_query->have_posts() ) :
        echo '<p class="events-section-heading">Featured</p>';
        echo '<div class="featured-events">';
        while ( $featured_query->have_posts() ) : $featured_query->the_post();
            global $post;
            bcg_render_featured_event_card( $post, $fallback_image, 'standard' );
        endwhile;
        echo '</div>';
    endif;
    wp_reset_postdata();

    // --- Main grid (everything NOT featured) ---
    $query = new WP_Query( array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_start_date',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            $upcoming_clause,
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'featured_event',
                    'value'   => '1',
                    'compare' => '!=',
                ),
                array(
                    'key'     => 'featured_event',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ),
    ) );

    if ( $query->have_posts() ) :
        echo '<p class="events-section-heading">Upcoming events</p>';
        echo '<div class="events-grid">';
        while ( $query->have_posts() ) : $query->the_post();
            global $post;
            bcg_render_event_card( $post, $fallback_image, 'standard' );
        endwhile;
        echo '</div>';
    else :
        echo '<p class="bcg-no-events">No upcoming events found.</p>';
    endif;

    wp_reset_postdata();
    echo '</div>';

    return ob_get_clean();
});


/* ================================================
   RECURRING EVENTS
   [recurring_events]
   ================================================ */
add_shortcode( 'recurring_events', function() {

    $fallback_image = get_field( 'event_fallback_image', 'option' );

    ob_start();
    echo '<div class="bcg-events-wrap">';

    // --- Featured recurring events (full-width) ---
    $featured_query = new WP_Query( array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'event_type',
                'value'   => 'recurring',
                'compare' => '=',
            ),
            array(
                'key'     => 'featured_event',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
    ) );

    if ( $featured_query->have_posts() ) :
        echo '<p class="events-section-heading">Featured</p>';
        echo '<div class="featured-events">';
        while ( $featured_query->have_posts() ) : $featured_query->the_post();
            global $post;
            bcg_render_featured_event_card( $post, $fallback_image, 'recurring' );
        endwhile;
        echo '</div>';
    endif;
    wp_reset_postdata();

    // --- Main recurring grid (NOT featured) ---
    $query = new WP_Query( array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'event_type',
                'value'   => 'recurring',
                'compare' => '=',
            ),
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'featured_event',
                    'value'   => '1',
                    'compare' => '!=',
                ),
                array(
                    'key'     => 'featured_event',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ),
    ) );

    if ( $query->have_posts() ) :
        echo '<div class="events-grid">';
        while ( $query->have_posts() ) : $query->the_post();
            global $post;
            bcg_render_event_card( $post, $fallback_image, 'recurring' );
        endwhile;
        echo '</div>';
    else :
        echo '<p class="bcg-no-events">No recurring events found.</p>';
    endif;

    wp_reset_postdata();
    echo '</div>';

    return ob_get_clean();
});


/* ================================================
   SINGLE EVENT DETAIL
   [single_event]
   ================================================ */
add_shortcode( 'single_event', function() {

    if ( ! is_singular( 'events' ) ) return '';

    $event_type  = get_field( 'event_type' ) ?: 'standard';
    $start_date  = get_field( 'event_start_date' );
    $end_date    = get_field( 'event_end_date' );
    $start_time  = get_field( 'event_start_time' );
    $end_time    = get_field( 'event_end_time' );
    $recurrence  = get_field( 'event_recurrence' );
    $image       = get_field( 'event_image' );
    $address     = get_field( 'event_address' );
    $location    = get_field( 'event_location' );
    $description = get_field( 'event_description' );
    $ticket_link = get_field( 'event_ticket_link' );
    $ticket_text = get_field( 'event_ticket_link_text' ) ?: 'Get tickets';

    ob_start();
    ?>
    <div class="bcg-single-event">

        <a class="bcg-single-back" href="/calendar">
            ← Back to events
        </a>

        <div class="bcg-single-layout">

            <div class="bcg-single-main">
	           <?php
					$fallback_image = get_field( 'event_fallback_image', 'option' );
					$display_image  = $image ?: $fallback_image;
				?>
				<?php if ( $display_image ) : ?>
					<img class="bcg-single-image" src="<?php echo esc_url( $display_image ); ?>" alt="<?php the_title_attribute(); ?>" />
				<?php endif; ?>
                <h1 class="bcg-single-title"><?php the_title(); ?></h1>
                <hr class="bcg-single-divider" />
                <?php if ( $description ) : ?>
                    <div class="bcg-single-desc"><?php echo wp_kses_post( $description ); ?></div>
                <?php endif; ?>
            </div>

            <aside class="bcg-single-sidebar">

                <?php if ( $event_type === 'recurring' && $recurrence ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">Recurs</span>
                        <p class="bcg-sidebar-value"><?php echo esc_html( $recurrence ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $event_type === 'standard' && $start_date ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">Start date</span>
                        <p class="bcg-sidebar-value"><?php echo esc_html( date( 'F j, Y', strtotime( $start_date ) ) ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $event_type === 'standard' && $end_date ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">End date</span>
                        <p class="bcg-sidebar-value"><?php echo esc_html( date( 'F j, Y', strtotime( $end_date ) ) ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $start_time || $end_time ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">Time</span>
                        <p class="bcg-sidebar-value">
                            <?php
                            if ( $start_time ) echo esc_html( date( 'g:i a', strtotime( $start_time ) ) );
                            if ( $start_time && $end_time ) echo ' – ';
                            if ( $end_time ) echo esc_html( date( 'g:i a', strtotime( $end_time ) ) );
                            ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ( $address ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">Address</span>
                        <p class="bcg-sidebar-value">
                            <a href="https://maps.google.com/?q=<?php echo urlencode( $address ); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html( $address ); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ( $location ) : ?>
                    <div class="bcg-sidebar-item">
                        <span class="bcg-sidebar-label">Location</span>
                        <p class="bcg-sidebar-value"><?php echo esc_html( $location ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $ticket_link ) : ?>
                    <a class="bcg-sidebar-btn" href="<?php echo esc_url( $ticket_link ); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html( $ticket_text ); ?>
                    </a>
                <?php endif; ?>

            </aside>

        </div>
    </div>
    <?php
    return ob_get_clean();
});

/* ================================================
   RECURRING EVENTS LIST
   [recurring_events_list]
   ================================================ */
add_shortcode( 'recurring_events_list', function() {

    // $accent_color = get_field( 'event_accent_color', 'option' );

    $args = array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array( array(
            'key'     => 'event_type',
            'value'   => 'recurring',
            'compare' => '=',
        )),
    );

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) :
		echo '<p class="events-section-heading">Recurring events</p>';
        echo '<ul class="bcg-events-list">';
        while ( $query->have_posts() ) : $query->the_post();
            $recurrence = get_field( 'event_recurrence' );
            // $style = $accent_color ? ' style="color: ' . esc_attr( $accent_color ) . ';"' : '';
            ?>
            <li class="bcg-events-list-item">
                <a class="bcg-events-list-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php if ( $recurrence ) : ?>
                    <p class="bcg-events-list-recurrence"><?php echo esc_html( $recurrence ); ?></p>
                <?php endif; ?>
            </li>
            <?php
        endwhile;
        echo '</ul>';
    else :
        echo '<p class="bcg-no-events">No recurring events found.</p>';
    endif;

    wp_reset_postdata();

    return ob_get_clean();
});



/* ================================================
   STANDARD EVENTS LIST
   [standard_events_list]
   ================================================ */
add_shortcode( 'standard_events_list', function() {

    // $accent_color = get_field( 'event_accent_color', 'option' );

    $args = array(
        'post_type'      => 'events',
        'posts_per_page' => 9999,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_start_date',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'event_start_date',
                'value'   => date( 'Ymd' ),
                'compare' => '>=',
            ),
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'event_type',
                    'value'   => 'standard',
                    'compare' => '=',
                ),
                array(
                    'key'     => 'event_type',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ),
    );

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) :
		echo '<p class="events-section-heading">Upcoming events</p>';
        echo '<ul class="bcg-events-list">';
        while ( $query->have_posts() ) : $query->the_post();
            $start_date = get_field( 'event_start_date' );
            // $style = $accent_color ? ' style="color: ' . esc_attr( $accent_color ) . ';"' : '';
            ?>
            <li class="bcg-events-list-item">
                <a class="bcg-events-list-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php if ( $start_date ) : ?>
                    <p class="bcg-events-list-recurrence"><?php echo esc_html( date( 'F j, Y', strtotime( $start_date ) ) ); ?></p>
                <?php endif; ?>
            </li>
            <?php
        endwhile;
        echo '</ul>';
    else :
        echo '<p class="bcg-no-events">No upcoming events found.</p>';
    endif;

    wp_reset_postdata();

    return ob_get_clean();
});


/* ================================================
   UPCOMING EVENTS LIST (homepage widget, etc.)
   [upcoming_events_list count="3"]
   ================================================ */
add_shortcode( 'upcoming_events_list', function( $atts ) {

    $atts = shortcode_atts( array(
        'count' => 3,
    ), $atts, 'upcoming_events_list' );

    $args = array(
        'post_type'      => 'events',
        'posts_per_page' => (int) $atts['count'],
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_start_date',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'event_start_date',
                'value'   => date( 'Ymd' ),
                'compare' => '>=',
            ),
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'event_type',
                    'value'   => 'standard',
                    'compare' => '=',
                ),
                array(
                    'key'     => 'event_type',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ),
    );

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) :
        echo '<ul class="bcg-events-list">';
        while ( $query->have_posts() ) : $query->the_post();
            $start_date = get_field( 'event_start_date' );
            ?>
            <li class="bcg-events-list-item">
                <a class="bcg-events-list-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php if ( $start_date ) : ?>
                    <p class="bcg-events-list-recurrence"><?php echo esc_html( date( 'F j, Y', strtotime( $start_date ) ) ); ?></p>
                <?php endif; ?>
            </li>
            <?php
        endwhile;
        echo '</ul>';
    else :
        echo '<p class="bcg-no-events">No upcoming events found.</p>';
    endif;

    wp_reset_postdata();

    return ob_get_clean();
});