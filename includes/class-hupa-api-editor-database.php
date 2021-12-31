<?php

namespace Hupa\ApiEditorDatabase;
defined('ABSPATH') or die();
use stdClass;


/**
 * Database entries for the data types of EditableJS
 *
 * Uses the class Hupa_Api_Editor_Database to create and edit the database.
 * register with WordPress.
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 */

class Hupa_Api_Editor_Database
{

    /**
     * TRAIT of Default Settings.
     *
     * @since    1.0.0
     */
    use Hupa_Api_Editor_Settings;

    /**
     * The current version of the DB-Version.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $dbVersion The current version of the database Version.
     */
    protected string $dbVersion;

    /**
     * The Database Table hupa_api_editor.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $table_editor The current version of the database Version.
     */

    /**
     * @param $db_version
     */
    public function __construct($db_version)
    {
        $this->dbVersion = $db_version;
    }

    /**
     * Insert | Update Table Editor
     * INIT Function
     * @since 1.0.0
     */
    public function update_api_editor_database()
    {

        if ($this->dbVersion !== get_option('hupa_api_editor_database')) {
            $this->create_hupa_api_editor_database();
            update_option('hupa_api_editor_database', $this->dbVersion);
            $this->hupa_set_hupa_api_defaults();
        }
    }

    /**
     * Set Defaults Table Editor
     * ADD-Action set_hupa_api_defaults
     * @return void
     * @param $args
     * @since 1.0.0
     */
    public function hupa_api_editor_reset_settings($args = null):void {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_editor;
        $wpdb->query("TRUNCATE TABLE $table");
        $wpdb->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        $this->hupa_set_hupa_api_defaults();
        $this->hupa_api_set_default_settings();
        global $editableOption;
        $editableOption->hupa_set_editable_options();
    }

    /**
     * Set Defaults Table Editor
     * @return void
     * @since 1.0.0
     */
    private function hupa_set_hupa_api_defaults(): void
    {
        $record = new stdClass();
        $getSection = $this->get_hupa_api_editor_sections(false);
        if (!$getSection->status) {
            $defaults = $this->get_theme_default_settings();
            foreach ($defaults['editor_sections_default'] as $tmp) {
                $record->content_type = $tmp['content_type'];
                $record->output_type = $tmp['output_type'];
                $record->section_type = $tmp['section_type'];
                $record->css_selector = $tmp['css_selector'];
                $record->posts_aktiv = $tmp['posts_aktiv'];
                $record->pages_aktiv = $tmp['pages_aktiv'];
                $this->hupa_api_editor_set_table_editor($record);
            }

            $this->hupa_api_set_default_settings();
        }
    }

    /**
     * Get Table Editor
     * FILTER get_api_editor_table_editor
     * @param $args
     * @param bool $fetchMethod
     * @return object
     * @since 1.0.0
     */
    public function get_hupa_api_editor_sections($args, bool $fetchMethod = true): object
    {
        global $wpdb;
        $return = new stdClass();
        $return->status = false;
        $return->count = 0;
        $fetchMethod ? $fetch = 'get_results' : $fetch = 'get_row';
        $table = $wpdb->prefix . $this->table_editor;
        $result = $wpdb->$fetch("SELECT e.*,DATE_FORMAT(e.created_at, '%d.%m.%Y %H:%i:%s') AS created
   							     FROM {$table} e {$args}");
        if (!$result) {
            return $return;
        }
        $fetchMethod ? $count = count($result) : $count = 1;
        $return->count = $count;
        $return->status = true;
        $return->record = $result;

        return $return;
    }

    /**
     *
     * SET Table Editor
     * FILTER set_api_editor_table_editor
     * @param $record
     * @return object
     * @since 1.0.0
     */
    public function hupa_api_editor_set_table_editor($record): object
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_editor;
        $wpdb->insert(
            $table,
            array(
                'posts_aktiv' => $record->posts_aktiv,
                'pages_aktiv' => $record->pages_aktiv,
                'output_type' => $record->output_type,
                'content_type' => $record->content_type,
                'section_type' => $record->section_type,
                'css_selector' => $record->css_selector,
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s')
        );

        $return = new stdClass();
        if (!$wpdb->insert_id) {
            $return->status = false;
            $return->msg = 'Daten konnten nicht gespeichert werden!';
            $return->id = false;
            return $return;
        }

        $return->status = true;
        $return->msg = 'Daten gespeichert!';
        $return->id = $wpdb->insert_id;

        return $return;
    }

    /**
     *
     * Update Table Editor
     * FILTER update_api_editor_table_editor
     * @param $record
     * @since 1.0.0
     */
    public function hupa_api_editor_update_table_editor($record): void
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_editor;
        $wpdb->update(
            $table,
            array(
                'posts_aktiv' => $record->posts_aktiv,
                'pages_aktiv' => $record->pages_aktiv,
                'output_type' => $record->output_type,
                'content_type' => $record->content_type,
                'section_type' => $record->section_type,
                'css_selector' => $record->css_selector,
            ),
            array('id' => $record->id),
            array('%d', '%d', '%s', '%s', '%s', '%s'),
            array('%d')
        );
    }

    /**
     *
     * Delete Table Editor
     * FILTER delete_api_editor_table_editor
     * @param $id
     * @since 1.0.0
     */
    public function hupa_api_editor_delete_table_editor($id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_editor;
        $wpdb->delete(
            $table,
            array(
                'id' => $id
            ),
            array('%d')
        );
    }

    /**
     *
     * API Editor Default Optionen
     * ADD-ACTION set_api_editor_default_optionen
     *
     * @since 1.0.0
     */
    public function hupa_api_set_default_settings()
    {
        $defaults = $this->get_theme_default_settings();
        global $editableOption;
        $defaultOption = $editableOption->hupaApiEditArrayToObject($defaults['input_edit_form_default']);
        update_option('hupa_get_editor_settings',$defaultOption);
    }

    /**
     *
     * API Editor Optionen
     * FILTER get_hupa_api_settings
     * @since 1.0.0
     * @param $args
     * @return object
     */
    public function hupa_get_api_settings($args = null):object {
      return get_option('hupa_get_editor_settings');
    }

    /**
     *
     * CREATE hupa_api_editor
     * @since 1.0.0
     */
    private function create_hupa_api_editor_database()
    {

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        $table_name = $wpdb->prefix . 'hupa_api_editor';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
        id mediumint(11) NOT NULL AUTO_INCREMENT,
        posts_aktiv tinyint(1) NOT NULL DEFAULT 1,
        pages_aktiv tinyint(1) NOT NULL DEFAULT 1,
        content_type varchar(24) NOT NULL,
        output_type varchar(24) NOT NULL,
        section_type varchar(24) NOT NULL,
        css_selector varchar(255) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (id)
     ) $charset_collate;";
        dbDelta($sql);
    }
}