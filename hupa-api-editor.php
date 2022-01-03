<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jenswiecker.de
 * @since             1.0.0
 * @package           Hupa_Api_Editor
 *
 * @wordpress-plugin
 * Plugin Name:       Hupa API Editor
 * Plugin URI:        https://www.hummelt-werbeagentur.de/leistungen/
 * Description:       Inline Editor für Seiten, Beiträge und Archive.
 * Version:           1.0.0
 * Author:            Jens Wiecker
 * Author URI:        http://jenswiecker.de
 * License:           MIT License
 * Tested up to:      5.8
 * Stable tag:        1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//PLUGIN VERSION
$plugin_data = get_file_data(dirname(__FILE__) . '/hupa-api-editor.php', array('Version' => 'Version'), false);
define("HUPA_API_EDITOR_VERSION", $plugin_data['Version']);
//DEFINE DATENBANK VERSION:
const HUPA_API_EDITOR_DB_VERSION = '1.0.0';
//DEFINE MIN PHP VERSION
const HUPA_API_EDITOR_MIN_PHP_VERSION = '8.0';
//DEFINE MIN WordPress VERSION
const HUPA_API_EDITOR_MIN_WP_VERSION = '5.7';
//DEFINE PLUGIN ROOT PATH
define('HUPA_API_EDITOR_PLUGIN_DIR', dirname(__FILE__));
//DEFINE PLUGIN ADMIN DIR
define('HUPA_API_EDITOR_ADMIN_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR);
//DEFINE PLUGIN SLUG
define('HUPA_API_EDITOR_SLUG_PATH', plugin_basename(__FILE__));
//DEFINE PLUGIN BASENAME
define('HUPA_API_EDITOR_BASENAME', plugin_basename(__DIR__));
//DEFINE PLUGIN URL
define('HUPA_API_EDITOR_PLUGIN_URL', plugins_url('hupa-api-editor'));


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hupa-api-editor-activator.php
 */
function activate_hupa_api_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hupa-api-editor-activator.php';
	Hupa_Api_Editor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hupa-api-editor-deactivator.php
 */
function deactivate_hupa_api_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hupa-api-editor-deactivator.php';
	Hupa_Api_Editor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hupa_api_editor' );
register_deactivation_hook( __FILE__, 'deactivate_hupa_api_editor' );


if ( is_admin() ) {
    /**
     * @link http://w-shadow.com/blog/2011/06/02/automatic-updates-for-commercial-themes/
     * @link https://github.com/YahnisElsts/plugin-update-checker
     * @link https://github.com/YahnisElsts/wp-update-server
     */


    if( ! class_exists( 'Puc_v4_Factory' ) ) {
        require_once 'vendor/autoload.php';
    }

    if ( get_option( 'hupa_api_editor_product_install_authorize' ) ) {
        delete_transient('show_api_editor_lizenz_info');
        if ( get_option( 'hupa_api_editor_server_api' )['update_aktiv'] == '1' ) {
            $hupaApiEditorUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                get_option( 'hupa_api_editor_server_api' )['update_url'],
                __FILE__,
                HUPA_API_EDITOR_BASENAME
            );
            if ( get_option( 'hupa_api_editor_server_api' )['update_type'] == '1' ) {
                $hupaApiEditorUpdateChecker->getVcsApi()->enableReleaseAssets();
            }
        }
    }

    /**
     * add plugin upgrade notification
     */
    add_action( 'in_plugin_update_message-' . HUPA_API_EDITOR_SLUG_PATH . '/' . HUPA_API_EDITOR_SLUG_PATH .'.php', 'hupa_api_editor_show_upgrade_notification', 10, 2 );
    function hupa_api_editor_show_upgrade_notification( $current_plugin_metadata, $new_plugin_metadata ) {

        /**
         *
         * @since    1.0.0
         * Notice	<- message
         */
        if ( isset( $new_plugin_metadata->upgrade_notice ) && strlen( trim( $new_plugin_metadata->upgrade_notice ) ) > 0 ) {

            // Display "upgrade_notice".
            echo sprintf( '<span style="background-color:#d54e21;padding:10px;color:#f9f9f9;margin-top:10px;display:block;"><strong>%1$s: </strong>%2$s</span>', esc_attr( 'Important Upgrade Notice', 'exopite-multifilter' ), esc_html( rtrim( $new_plugin_metadata->upgrade_notice ) ) );

        }
    }
}


function showWPHupaApiEditorInfo() {
    if ( get_transient( 'show_api_editor_lizenz_info' ) ) {
        echo '<div class="error"><p>' .
            'Hupa Api Editor ungültige Lizenz: Zum Aktivieren geben Sie Ihre Zugangsdaten ein.' .
            '</p></div>';
    }
}

add_action( 'admin_notices', 'showWPHupaApiEditorInfo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hupa-api-editor.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

global $pbt_hupa_api_editor;
$pbt_hupa_api_editor = new Hupa_Api_Editor();
$pbt_hupa_api_editor->run();