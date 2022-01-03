<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'hupa_api_editor';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

delete_option('hupa_api_editor_database');
delete_option('hupa_api_editor_license_url');
delete_option('hupa_api_editor_server_api');
delete_option("hupa_api_editor_client_id");
delete_option("hupa_api_editor_client_secret");
delete_option('hupa_api_editor_product_install_authorize');
delete_option("hupa_api_editor_message");

delete_transient('show_api_editor_lizenz_info');
