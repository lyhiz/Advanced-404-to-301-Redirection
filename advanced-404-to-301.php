<?php
/*
Plugin Name: Advanced 404 to 301 Redirection
Plugin URI: https://github.com/lyhiz/Advanced-404-to-301-Redirection
Description: Advanced 404 to 301 Redirection is a WordPress plugin that redirects 404 errors to specified URLs using 301 redirects and logs the redirections.
Version: 1.2
Author: Tarmo Trubetski
Author URI: https://www.facebook.com/tarmo.trubetski
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Include the main plugin file
require_once plugin_dir_path(__FILE__) . 'includes/advanced-404-to-301-functions.php';

// Function to enqueue styles
function advanced_404_to_301_enqueue_styles() {
    wp_enqueue_style('advanced-404-to-301-styles', plugin_dir_url(__FILE__) . 'css/advanced-404-to-301-styles.css');
}
add_action('wp_enqueue_scripts', 'advanced_404_to_301_enqueue_styles');
?>
