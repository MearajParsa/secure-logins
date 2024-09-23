<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Secure Logins
 * @author    MearajParsa
 * @link      https://github.com/MearajParsa
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sl_page' );
delete_option( 'sl_redirect_admin' );

flush_rewrite_rules();

//info: optimize table
$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "options`" );
