<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Api_Editor_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        delete_option("hupa_api_editor_product_install_authorize");
        delete_option("hupa_api_editor_client_id");
        delete_option("hupa_api_editor_client_secret");
        delete_option("hupa_api_editor_message");
        delete_option("hupa_api_editor_access_token");
        $infoTxt = 'deaktiviert am ' . date('d.m.Y H:i:s')."\r\n";
        file_put_contents(HUPA_API_EDITOR_PLUGIN_DIR.'/hupa-api-editor.txt',$infoTxt,  FILE_APPEND | LOCK_EX);
	}

}
