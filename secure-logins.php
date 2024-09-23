<?php
/**
 * Plugin Name: secure logins
 * Plugin URI: https://github.com/MearajParsa/secure-logins
 * Description: With the Secure Login plugin, you can change the default WordPress admin URL from wp-login.php and wp-admin to anything you want
 * Version: 1.4
 * Author: MearajParsa
 * Author URI: https://github.com/MearajParsa
 * Text Domain:  secure-logins
 * Domain Path:  /languages
 * License: GPLv2 or later
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


add_action('plugins_loaded', 'sl_load_textdomain');
function sl_load_textdomain() {
    load_plugin_textdomain('secure-logins', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}


define( 'SECURE_LOGINS_VERSION', '1.3' );
define( 'SECURE_LOGINS_BASENAME', plugin_basename( __FILE__ ) );
define( 'SECURE_LOGINS_DIR', plugin_dir_path( __FILE__ ) );


// Check plugin requirements
if ( version_compare(PHP_VERSION, '7.1', '<') ) {
    if (!function_exists('secure_logins_disable_plugin')) {
        /**
         * Disable plugin
         *
         * @return void
         */
        function secure_logins_disable_plugin(){
            if (current_user_can('activate_plugins') && is_plugin_active(SECURE_LOGINS_BASENAME)) {
                deactivate_plugins(__FILE__);
                if(isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    if (!function_exists('secure_logins_show_error')) {
        /**
         * Show error
         *
         * @return void
         */
        function secure_logins_show_error(){

            _e( '<div class="error"><p><strong>Secure Logins</strong> needs at least PHP 7.1 version, please update before installing the plugin.</p></div>' );
        }
    }

    // add actions
    add_action('admin_init', 'secure_logins_disable_plugin');
    add_action('admin_notices', 'secure_logins_show_error');

    // do not load anything more
    return;
}

require_once SECURE_LOGINS_DIR . 'admin/admin.php';
require_once SECURE_LOGINS_DIR . 'inc/functions.php';
