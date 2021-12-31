<?php
defined('ABSPATH') or die();
/**
 * Fired during plugin activation
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 */

use JetBrains\PhpStorm\NoReturn;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Api_Editor_Activator {


    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
	public static function activate()
    {
        $register = HUPA_API_EDITOR_ADMIN_DIR . 'class-hupa-api-editor-admin.php';
        if(!get_option('hupa_api_editor_product_install_authorize')){
            unlink($register);
        }
        delete_option("hupa_api_editor_product_install_authorize");
        delete_option("hupa_api_editor_client_id");
        delete_option("hupa_api_editor_client_secret");
        delete_option("hupa_api_editor_message");
        delete_option("hupa_api_editor_access_token");
        $infoTxt = 'aktiviert am ' . date('d.m.Y H:i:s')."\r\n";
        file_put_contents(HUPA_API_EDITOR_PLUGIN_DIR.'/hupa-api-editor.txt',$infoTxt,  FILE_APPEND | LOCK_EX);
        set_transient('show_api_editor_lizenz_info', true, 5);
	}




}
