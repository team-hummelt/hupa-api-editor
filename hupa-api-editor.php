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
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Jens Wiecker
 * Author URI:        http://jenswiecker.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hupa-api-editor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const HUPA_API_EDITOR_VERSION = '1.0.0';

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
function run_hupa_api_editor() {

	$plugin = new Hupa_Api_Editor();
	$plugin->run();

}
run_hupa_api_editor();
