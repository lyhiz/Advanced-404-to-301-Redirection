<?php
/**
 * Plugin Name: Advanced 404 to 301 Redirection
 * Plugin URI: https://github.com/lyhiz/Advanced-404-to-301-Redirection
 * Description: Redirects 404 errors to specified URLs using 301 redirects and logs the redirections.
 * Version: 1.2
 * Author: Tarmo Trubetski
 * Author URI: https://www.facebook.com/tarmo.trubetski
 * Requires at least: WordPress 4.7
 * Tested up to: WordPress 5.4
 * Stable tag: 1.2
 * Requires PHP: 7.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

// Add admin menu
function advanced_404_to_301_menu() {
    add_menu_page('404 to 301 Redirection', '404 to 301', 'manage_options', 'advanced-404-to-301', 'advanced_404_to_301_settings_page');
}
add_action('admin_menu', 'advanced_404_to_301_menu');

// Settings page content
function advanced_404_to_301_settings_page() {
    if (isset($_POST['clear_log'])) {
        advanced_404_to_301_clear_log();
    }
    ?>
    <div class="wrap">
        <h2>404 to 301 Redirection Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('advanced-404-to-301-settings-group'); ?>
            <?php do_settings_sections('advanced-404-to-301-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Redirect URL for 404 Errors</th>
                    <td><input type="text" name="advanced_404_to_301_redirect_url" value="<?php echo esc_url(get_option('advanced_404_to_301_redirect_url')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <hr>

        <h2>Support This Plugin</h2>
        <p>If you find this plugin helpful, consider supporting us by making a donation via PayPal:</p>
        <form action="https://www.paypal.com/donate" method="post" target="_blank">
            <input type="hidden" name="business" value="support@gameboss.eu">
            <input type="hidden" name="item_name" value="Support Advanced 404 to 301 Redirection Plugin">
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>

        <form method="post">
            <?php wp_nonce_field('advanced-404-to-301-clear-log'); ?>
            <?php submit_button('Clear Log', 'secondary', 'clear_log', false); ?>
        </form>
    </div>
    <?php
}

// Clear log function
function advanced_404_to_301_clear_log() {
    if (check_admin_referer('advanced-404-to-301-clear-log')) {
        $log_file = plugin_dir_path(__FILE__) . 'redirect_log.txt';
        file_put_contents($log_file, ''); // Clear the log file
    }
}

// Register and sanitize settings
function advanced_404_to_301_settings_init() {
    register_setting('advanced-404-to-301-settings-group', 'advanced_404_to_301_redirect_url', 'esc_url');
}
add_action('admin_init', 'advanced_404_to_301_settings_init');

// Redirect function
function advanced_404_to_301_redirect() {
    if (is_404()) {
        $redirect_url = esc_url(get_option('advanced_404_to_301_redirect_url'));
        if (!empty($redirect_url)) {
            $requested_url = esc_url($_SERVER['REQUEST_URI']);
            $log_entry = "404 Redirect: $requested_url -> $redirect_url";

            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $redirect_url");

            // Append log entry to the log file
            $log_file = plugin_dir_path(__FILE__) . 'redirect_log.txt';
            file_put_contents($log_file, $log_entry . PHP_EOL, FILE_APPEND);

            exit();
        }
    }
}
add_action('template_redirect', 'advanced_404_to_301_redirect');

// Log page content
function advanced_404_to_301_log_page() {
    if (isset($_POST['clear_log'])) {
        advanced_404_to_301_clear_log();
    }

    $log_file = plugin_dir_path(__FILE__) . 'redirect_log.txt';
    if (file_exists($log_file)) {
        $log_content = file_get_contents($log_file);
    } else {
        $log_content = 'Log file is empty.';
    }

    echo '<div class="wrap">';
    echo '<h2>404 to 301 Redirection Log</h2>';
    echo '<form method="post">';
    wp_nonce_field('advanced-404-to-301-clear-log');
    submit_button('Clear Log', 'secondary', 'clear_log', false);
    echo '</form>';
    echo '<pre>' . esc_html($log_content) . '</pre>';
    echo '</div>';
}

// Add submenu for viewing redirection log
function advanced_404_to_301_log_menu() {
    add_submenu_page('advanced-404-to-301', 'Redirection Log', 'View Log', 'manage_options', 'advanced-404-to-301-log', 'advanced_404_to_301_log_page');
}
add_action('admin_menu', 'advanced_404_to_301_log_menu');
