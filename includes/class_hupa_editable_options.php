<?php
defined('ABSPATH') or die();
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
            'show_option' => 1,
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

    public function hupa_api_editor_labels($lbl): string
    {
        return match ($lbl) {
            'textarea', 'text' => __('Edit text', 'hupa-api-editor'),
            'email' => __('Edit E-Mail', 'hupa-api-editor'),
            'number' => __('Edit number', 'hupa-api-editor'),
            'url' => __('Edit URL', 'hupa-api-editor'),
            'date' => __('Edit Date', 'hupa-api-editor'),
            'save' => __('save', 'hupa-api-editor'),
            'cancel' => __('cancel', 'hupa-api-editor'),
            'edit_title' => __('Edit title', 'hupa-api-editor'),
            'saving' => __('Saving', 'hupa-api-editor') . '...',
            'tooltip' => __('Click to edit', 'hupa-api-editor') . '...',
            default => '',
        };
    }

    /**
     * GET SELECT SECTIONS TYPE.
     *
     * @param string $type
     * @param string $byId
     * @return object
     * @since  1.0.0
     */
    public function hupa_api_edit_input_select(string $type = '', string $byId = ''): object
    {
        $outputSelect = [
            '0' => [
                'aktiv' => 1,
                'id'            => '',
                'bezeichnung'   => __('Output type', 'hupa-api-editor').' ...'
            ],
            '1' => [
                'aktiv' => 1,
                'id'            => 'text',
                'bezeichnung'   => __('Text', 'hupa-api-editor')
            ],
            '2' => [
                'aktiv' => 1,
                'id'            => 'textarea',
                'bezeichnung'   => __('Textarea', 'hupa-api-editor')
            ],
            '3' => [
                'aktiv' => 1,
                'id'            => 'number',
                'bezeichnung'   => __('Number', 'hupa-api-editor')
            ],
            '4' => [
                'aktiv' => 1,
                'id'            => 'date',
                'bezeichnung'   => __('Date', 'hupa-api-editor')
            ],
            '5' => [
                'aktiv' => 1,
                'id'            => 'email',
                'bezeichnung'   => __('E-Mail', 'hupa-api-editor')
            ],
            '6' => [
                'aktiv' => 1,
                'id' => 'url',
                'bezeichnung' => __('URL', 'hupa-api-editor')
            ],
            '7' => [
                'aktiv' => 1,
                'id' => 'inline',
                'bezeichnung' => __('Inline Element', 'hupa-api-editor')
            ],
        ];

        $sectionPostTypes = [
            '0' => [
                'aktiv' => 1,
                'id' => '',
                'bezeichnung' => __('Section type', 'hupa-api-editor') . ' ...'
            ],
            '1' => [
                'aktiv' => 1,
                'id' => 'title',
                'bezeichnung' => 'Title'
            ],
            '2' => [
                'aktiv' => 1,
                'id' => 'content',
                'bezeichnung' => 'Content'
            ],
            '3' => [
                'aktiv' => 1,
                'id' => 'excerpt',
                'bezeichnung' => 'Excerpt'
            ],

            '4' => [
                'aktiv' => 0,
                'id' => 'date',
                'bezeichnung' => 'Date'
            ],
            '5' => [
                'aktiv' => 0,
                'id' => 'author',
                'bezeichnung' => 'Author'
            ],
            '6' => [
                'aktiv' => 0,
                'id' => 'categories',
                'bezeichnung' => 'Categories'
            ],
            '7' => [
                'aktiv' => 0,
                'id' => 'tags',
                'bezeichnung' => 'Tags'
            ],
            '8' => [
                'aktiv' => 0,
                'id' => 'password',
                'bezeichnung' => 'Password'
            ],
            '9' => [
                'aktiv' => 0,
                'id' => 'date_gmt',
                'bezeichnung' => 'Date GMT'
            ],
            '10' => [
                'aktiv' => 0,
                'id' => 'status',
                'bezeichnung' => 'Status'
            ],
            '11' => [
                'aktiv' => 0,
                'id' => 'featured_media',
                'bezeichnung' => 'Featured Media'
            ],
            '12' => [
                'aktiv' => 0,
                'id' => 'comment_status',
                'bezeichnung' => 'Comment Status'
            ],
            '13' => [
                'aktiv' => 0,
                'id' => 'ping_status',
                'bezeichnung' => 'Ping Status'
            ],
            '14' => [
                'aktiv' => 0,
                'id' => 'format',
                'bezeichnung' => 'Format'
            ],
            '15' => [
                'id' => 'meta',
                'bezeichnung' => 'Meta'
            ],
            '16' => [
                'aktiv' => 0,
                'id' => 'sticky',
                'bezeichnung' => 'Sticky'
            ],
            '17' => [
                'aktiv' => 0,
                'id' => 'template',
                'bezeichnung' => 'Template'
            ],
        ];

        $sectionWidgetTextTypes = [
            '0' => [
                'aktiv' => 1,
                'id' => '',
                'bezeichnung' => __('Widget Text type', 'hupa-api-editor') . ' ...'
            ],
            '1' => [
                'aktiv' => 1,
                'id' => 'title',
                'bezeichnung' => 'Title'
            ],
            '2' => [
                'aktiv' => 1,
                'id' => 'text',
                'bezeichnung' => 'Text'
            ],
        ];

        $sectionWidgetHtmlTypes = [
            '0' => [
                'aktiv' => 1,
                'id' => '',
                'bezeichnung' => __('Widget HTML type', 'hupa-api-editor') . ' ...'
            ],
            '1' => [
                'aktiv' => 1,
                'id' => 'title',
                'bezeichnung' => 'Title'
            ],
            '2' => [
                'aktiv' => 1,
                'id' => 'content',
                'bezeichnung' => 'Content'
            ],
        ];

        $ContentTyp = [
            '0' => [
                'aktiv' => 1,
                'id'            => '',
                'bezeichnung'   => __('Content type', 'hupa-api-editor').' ...'
            ],
            '1' => [
                'aktiv' => 1,
                'id'            => 'post',
                'bezeichnung'   => __('Post / Page', 'hupa-api-editor')
            ],
            '2' => [
                'aktiv' => 1,
                'id'            => 'text_widget',
                'bezeichnung'   => __('Text Widget', 'hupa-api-editor')
            ],
            '3' => [
                'aktiv' => 1,
                'id'            => 'html_widget',
                'bezeichnung'   => __('HTML Widget', 'hupa-api-editor')
            ]
        ];

        $return = new stdClass();
        $return->output_select = $this->hupaApiEditArrayToObject($outputSelect);
        $return->section_select = $this->hupaApiEditArrayToObject($sectionPostTypes);
        $return->content_types_select = $this->hupaApiEditArrayToObject($ContentTyp);


        $retArr = [];
        switch ($type) {
            case 'post':
                foreach ($sectionPostTypes as $tmp){
                    if(!$tmp['aktiv']){
                        continue;
                    }
                    if($byId && $tmp['id'] === $byId){
                        return $this->hupaApiEditArrayToObject($tmp);
                    }
                    $retArr[] = $tmp;
                }
                return $this->hupaApiEditArrayToObject($retArr);

            case'text_widget':
                foreach ($sectionWidgetTextTypes as $tmp){
                    if(!$tmp['aktiv']){
                        continue;
                    }
                    if($byId && $tmp['id'] === $byId){
                        return $this->hupaApiEditArrayToObject($tmp);
                    }
                    $retArr[] = $tmp;
                }
                return $this->hupaApiEditArrayToObject($retArr);
            case'html_widget':
                foreach ($sectionWidgetHtmlTypes as $tmp){
                    if(!$tmp['aktiv']){
                        continue;
                    }
                    if($byId && $tmp['id'] === $byId){
                        return $this->hupaApiEditArrayToObject($tmp);
                    }
                    $retArr[] = $tmp;
                }
                return $this->hupaApiEditArrayToObject($retArr);
            case 'output_type':
                foreach ($outputSelect as $tmp){
                    if(!$tmp['aktiv']){
                        continue;
                    }
                    if($byId && $tmp['id'] === $byId){
                        return $this->hupaApiEditArrayToObject($tmp);
                    }
                    $retArr[] = $tmp;
                }
                return $this->hupaApiEditArrayToObject($retArr);
            case 'content_type':
                foreach ($ContentTyp as $tmp){
                    if(!$tmp['aktiv']){
                        continue;
                    }
                    if($byId && $tmp['id'] === $byId){
                        return $this->hupaApiEditArrayToObject($tmp);
                    }
                    $retArr[] = $tmp;
                }
                return $this->hupaApiEditArrayToObject($retArr);
        }

        $options = $this->hupa_set_editable_options();
        isset($options['show_editable_interfaces']['post']) && $options['show_editable_interfaces']['post'] == 'show' ? $post = true : $post = false;
        isset($options['show_editable_interfaces']['page']) && $options['show_editable_interfaces']['page'] == 'show' ? $page = true : $page = false;
        !$page && !$post ? $aktiv = false : $aktiv = true;

        $return->page = $page;
        $return->post = $post;
        $return->aktiv = $aktiv;

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


