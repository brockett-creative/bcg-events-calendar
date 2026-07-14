<?php
/**
 * Plugin Name: BCG Events Calendar
 * Description: A handcrafted events calendar system for BCG websites. Requires ACF Pro or ACF Free.
 * Author: BCGSam
 * Version: 2.0.1
 * Text Domain: bcg-events-calendar
 * GitHub Plugin URI: https://github.com/brockett-creative/bcg-events-calendar
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BCG_EVENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'BCG_EVENTS_URL',  plugin_dir_url( __FILE__ ) );

require_once BCG_EVENTS_PATH . 'includes/post-type.php';
require_once BCG_EVENTS_PATH . 'includes/acf-fields.php';
require_once BCG_EVENTS_PATH . 'includes/settings.php';
require_once BCG_EVENTS_PATH . 'includes/shortcode.php';

// Enqueue front-end styles
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'bcg-events', BCG_EVENTS_URL . 'css/style.css', array(), '2.0.0' );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );
});
