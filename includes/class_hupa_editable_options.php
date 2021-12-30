<?php

/**
 * Define the Editable functionality
 *
 * Loads and defines user permissions
 * For the Editable JS functions.
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 */

/**
 * @since      1.0.0
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Editable_Options
{

    private static $instance;

    /**
     * @return static
     */
    public static function instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Load the plugin Editable Options.
     *
     * @since    1.0.0
     */
    public function hupa_set_editable_options()
    {

        $options = get_option('hupa_api_editable_options');
        $defaults = array(
            'show_editable_interfaces' => array(),
            'aktiv' => 1,
            'capability' => 'manage_options',
        );

        $options = wp_parse_args($options, $defaults);
        return apply_filters('hupa-api-editor/get_options', $options);
    }


    /**
     * Load AJAX AND JS LANGUAGE.
     *
     * @param void
     * @since    1.0.0
     * @return array
     */
    public function hupa_api_editor_language(): array
    {
        return [
            'save' => __('save', 'hupa-api-editor'),
            'cancel' => __('cancel', 'hupa-api-editor'),
            'tooltip' => __('Click to edit', 'hupa-api-editor') . '...',
            'edit_txt' => __('Edit text', 'hupa-api-editor'),
            'edit_title' => __('Edit title', 'hupa-api-editor'),
            'saving' => __('Saving', 'hupa-api-editor') . '...',
        ];
    }

    /**
     * GET SELECT SECTIONS TYPE.
     *
     * @param void
     * @since  1.0.0
     * @return object
     */
    public function hupa_api_edit_input_select(): object
    {
        $select = [
            '0' => [
                'id'            => '',
                'bezeichnung'   => __('Input Type', 'hupa-api-editor').' ...'
            ],
            '1' => [
                'id'            => 'text',
                'bezeichnung'   => __('Text', 'hupa-api-editor')
            ],
            '2' => [
                'id'            => 'textarea',
                'bezeichnung'   => __('Textarea', 'hupa-api-editor')
            ],
            '3' => [
                'id'            => 'number',
                'bezeichnung'   => __('Number', 'hupa-api-editor')
            ],
            '4' => [
                'id'            => 'date',
                'bezeichnung'   => __('Date', 'hupa-api-editor')
            ],
            '5' => [
                'id'            => 'email',
                'bezeichnung'   => __('E-Mail', 'hupa-api-editor')
            ],
            '6' => [
                'id'            => 'url',
                'bezeichnung'   => __('URL', 'hupa-api-editor')
            ],
            '7' => [
                'id'            => 'inline',
                'bezeichnung'   => __('Inline Element', 'hupa-api-editor')
            ],
        ];

        $options = $this->hupa_set_editable_options();
        isset($options['show_editable_interfaces']['post']) && $options['show_editable_interfaces']['post'] == 'show' ? $post = true : $post = false;
        isset($options['show_editable_interfaces']['page']) && $options['show_editable_interfaces']['page'] == 'show' ? $page = true : $page = false;
        !$page && !$post ? $aktiv = false : $aktiv = true;
        $return = new stdClass();
        $return->page = $page;
        $return->post = $post;
        $return->aktiv = $aktiv;
        $return->select = $this->hupaApiEditArrayToObject($select);
        return $return;
    }

    public function api_editor_js_admin_language():object {
        $lang = new stdClass();
        $lang->aktiv = __('active', 'hupa-api-editor');
        $lang->posts = __('Posts', 'hupa-api-editor');
        $lang->name = __('Name', 'hupa-api-editor');
        $lang->pages = __('Pages', 'hupa-api-editor');
        $lang->section_ph = __('CSS Section, Class, Element etc...', 'hupa-api-editor');
        $lang->add = __('add', 'hupa-api-editor');
        $lang->edit = __('edit', 'hupa-api-editor');
        $lang->save_changes = __('Save changes', 'hupa-api-editor');
        $lang->delete = __('delete', 'hupa-api-editor');
        return $lang;
    }

    /**
     * @param $array
     * @since 1.0.0
     * @return object
     */
    final public function hupaApiEditArrayToObject($array): object
    {
        foreach ($array as $key => $value)
            if (is_array($value)) $array[$key] = self::hupaApiEditArrayToObject($value);
        return (object)$array;
    }
}


