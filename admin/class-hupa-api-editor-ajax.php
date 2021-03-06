<?php
defined('ABSPATH') or die();
/**
 * ADMIN AJAX
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */

$responseJson = new stdClass();
$record = new stdClass();
$responseJson->status = false;
$data = '';

$method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

switch ($method) {
    case 'editable_form_options':

        $show_editable_interfaces = array($_POST['show_editable_interfaces']);
        $show_editable_interfaces = array_map('sanitize_key', $show_editable_interfaces[0]);
        $capability = filter_input(INPUT_POST, 'capability', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        isset($_POST['edit_aktiv']) && is_string($_POST['edit_aktiv']) ? $edit_api_aktiv = 1 : $edit_api_aktiv = 0;

        $saveOptions = get_option('hupa_api_editable_options');
        $options = array(
            'show_editable_interfaces' => $show_editable_interfaces,
            'aktiv' => $edit_api_aktiv,
            'capability' => sanitize_key($capability),
            'show_option' => $saveOptions['show_option']
        );

        update_option('hupa_api_editable_options', $options);
        $responseJson->status = true;
        $responseJson->spinner = true;

        $responseJson->msg = date('H:i:s', current_time('timestamp'));
        break;

    case 'get_css_section_data':
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        global $editableOption;
        $selected = $editableOption->hupa_api_edit_input_select();

        $sectArr = [];
        switch ($type) {
            case 'load_data':
                $selected->btn_add = false;
                $selected->btn_change = true;
                $selected->btn_delete = true;
                $sections = apply_filters('get_api_editor_table_editor', 'ORDER BY e.created_at DESC');
                if (!$sections->status) {
                    $responseJson->status = false;
                    $responseJson->msg = false;
                    return $responseJson;
                }
                global $editableOption;
                foreach ($sections->record as $tmp) {

                    $sect_items = [
                        'id' => (int)$tmp->id,
                        'css_selector' => esc_html(trim($tmp->css_selector)),
                        'content_type' => esc_html(trim($tmp->content_type)),
                        'output_type' => esc_html(trim($tmp->output_type)),
                        'section_type' => esc_html(trim($tmp->section_type)),
                        'pages_aktiv' => (bool)$tmp->pages_aktiv == '1' && $selected->page === true,
                        'posts_aktiv' => (bool)$tmp->posts_aktiv == '1' && $selected->post === true,
                        'pages_disabled' => $selected->page === false,
                        'posts_disabled' => $selected->post === false,
                        'section_select' => $editableOption->hupa_api_edit_input_select($tmp->content_type),
                        'output_type_head' => $editableOption->hupa_api_edit_input_select('output_type', $tmp->output_type)->bezeichnung,
                        'content_type_head' => $editableOption->hupa_api_edit_input_select('content_type', $tmp->content_type)->bezeichnung,
                    ];
                    $sectArr[] = $sect_items;
                }

                break;
            case 'add_data':
                $selected->btn_add = true;
                $selected->btn_change = false;
                $selected->btn_delete = true;
                break;
        }

        $selected->lang = $editableOption->api_editor_js_admin_language();
        $responseJson->record = $sectArr;
        $responseJson->select = $selected;
        $responseJson->type = $type;
        $responseJson->status = true;
        break;

    case 'css_sections_db_handle':
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $responseJson->rand = filter_input(INPUT_POST, 'rand', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        $css_selector = filter_input(INPUT_POST, 'css_selector', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        $content_type = filter_input(INPUT_POST, 'content_type', FILTER_SANITIZE_STRING);
        $output_type = filter_input(INPUT_POST, 'output_type', FILTER_SANITIZE_STRING);
        $section_type = filter_input(INPUT_POST, 'section_type', FILTER_SANITIZE_STRING);

        filter_input(INPUT_POST, 'page_aktiv', FILTER_SANITIZE_STRING) ? $record->pages_aktiv = 1 : $record->pages_aktiv = 0;
        filter_input(INPUT_POST, 'post_aktiv', FILTER_SANITIZE_STRING) ? $record->posts_aktiv = 1 : $record->posts_aktiv = 0;

        if (!$content_type) {
            $responseJson->msg = 'Fehler! Input Typ nicht ausgew??hlt!';
            return $responseJson;
        }

        if (!$output_type) {
            $responseJson->msg = 'Fehler! Ausgabe Typ nicht ausgew??hlt!';
            return $responseJson;
        }

        if (!$section_type) {
            $responseJson->msg = 'Fehler! Sektion Post Typ nicht ausgew??hlt!';
            return $responseJson;
        }

        $record->content_type = esc_html(trim($content_type));
        $record->output_type = esc_html(trim($output_type));
        $record->section_type = esc_html(trim($section_type));



        if (!$css_selector) {
            $responseJson->msg = 'Fehler! CSS-Selector darf nicht leer sein!';
            return $responseJson;
        }

        $responseJson->type = $type;
        $record->css_selector = esc_html(trim($css_selector));
        switch ($type) {
            case 'insert':
                $insert = apply_filters('set_api_editor_table_editor', $record);
                $responseJson->status = $insert->status;
                $responseJson->msg = $insert->msg;
                $responseJson->id = $insert->id;
                break;
            case'update':
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                $args = sprintf('WHERE e.css_selector="%s" AND e.content_type="%s" AND e.id!=%d', $css_selector,$record->content_type,$id);
                $getSelector = apply_filters('get_api_editor_table_editor', $args ,false);
                if($getSelector->status) {
                    $responseJson->msg = 'Fehler! CSS-Selector schon vorhanden!';
                    return $responseJson;
                }
                if(!$id) {
                    $responseJson->msg = 'Ajax ??bertragungsfehler!';
                    return $responseJson;
                }
                $record->id = $id;
                do_action('update_api_editor_table_editor', $record);
                $responseJson->status = true;
                $responseJson->msg = '??nderungen gespeichert';
                break;
        }

        global $editableOption;
        $responseJson->output_type_head = $editableOption->hupa_api_edit_input_select('output_type', $record->output_type)->bezeichnung;
        $responseJson->content_type_head = $editableOption->hupa_api_edit_input_select('content_type', $record->content_type)->bezeichnung;
        $responseJson->css_selector = $record->css_selector;
        break;

    case 'delete_css_sections':
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $responseJson->rand = filter_input(INPUT_POST, 'rand', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        if(!$id) {
            $responseJson->msg = 'Ajax ??bertragungsfehler!';
            return $responseJson;
        }

        do_action('delete_api_editor_table_editor', $id);
        $responseJson->type = 'delete_section';
        $responseJson->status = true;
        $responseJson->msg = 'CSS Section gel??scht!';
        break;

    case'update_api_settings_options':
        $textarea_form_wrapper = filter_input(INPUT_POST, 'textarea_form_wrapper', FILTER_SANITIZE_STRING);
        $textarea_form_class = filter_input(INPUT_POST, 'textarea_form_class', FILTER_SANITIZE_STRING);
        $textarea_submit_class = filter_input(INPUT_POST, 'textarea_submit_class', FILTER_SANITIZE_STRING);
        $textarea_cancel_class = filter_input(INPUT_POST, 'textarea_cancel_class', FILTER_SANITIZE_STRING);
        filter_input(INPUT_POST, 'textarea_show_button', FILTER_SANITIZE_STRING) ? $textarea_show_button = 1 : $textarea_show_button = 0;
        filter_input(INPUT_POST, 'textarea_symbol', FILTER_SANITIZE_STRING) ? $textarea_symbol = 1 : $textarea_symbol = 0;
        filter_input(INPUT_POST, 'textarea_label', FILTER_SANITIZE_STRING) ? $textarea_label = 1 : $textarea_label = 0;
        filter_input(INPUT_POST, 'textarea_auto_height', FILTER_SANITIZE_STRING) ? $textarea_auto_height = 1 : $textarea_auto_height = 0;

        $input_form_wrapper = filter_input(INPUT_POST, 'input_form_wrapper', FILTER_SANITIZE_STRING);
        $input_form_class = filter_input(INPUT_POST, 'input_form_class', FILTER_SANITIZE_STRING);
        $input_submit_class = filter_input(INPUT_POST, 'input_submit_class', FILTER_SANITIZE_STRING);
        $input_cancel_class = filter_input(INPUT_POST, 'input_cancel_class', FILTER_SANITIZE_STRING);
        filter_input(INPUT_POST, 'input_show_button', FILTER_SANITIZE_STRING) ? $input_show_button = 1 : $input_show_button = 0;
        filter_input(INPUT_POST, 'input_symbol', FILTER_SANITIZE_STRING) ? $input_symbol = 1 : $input_symbol = 0;
        filter_input(INPUT_POST, 'input_label', FILTER_SANITIZE_STRING) ? $input_label = 1 : $input_label = 0;

        $inline_form_wrapper = filter_input(INPUT_POST, 'inline_form_wrapper', FILTER_SANITIZE_STRING);
        $inline_form_class = filter_input(INPUT_POST, 'inline_form_class', FILTER_SANITIZE_STRING);
        filter_input(INPUT_POST, 'inline_symbol', FILTER_SANITIZE_STRING) ? $inline_symbol = 1 : $inline_symbol = 0;

        $updateOption = [
            'textarea_form_wrapper' => esc_html(trim($textarea_form_wrapper)),
            'textarea_form_class' => esc_html(trim($textarea_form_class)),
            'textarea_submit_class' => esc_html(trim($textarea_submit_class)),
            'textarea_cancel_class' => esc_html(trim($textarea_cancel_class)),
            'textarea_show_button' => $textarea_show_button,
            'textarea_symbol' => $textarea_symbol,
            'textarea_label' => $textarea_label,
            'textarea_auto_height' => $textarea_auto_height,

            'input_form_wrapper' => esc_html(trim($input_form_wrapper)),
            'input_form_class' => esc_html(trim($input_form_class)),
            'input_submit_class' => esc_html(trim($input_submit_class)),
            'input_cancel_class' => esc_html(trim($input_cancel_class)),
            'input_show_button' => $input_show_button,
            'input_symbol' => $input_symbol,
            'input_label' => $input_label,
            'input_auto_height' => 0,

            'inline_form_wrapper' => esc_html(trim($inline_form_wrapper)),
            'inline_form_class' => esc_html(trim($inline_form_class)),
            'inline_symbol' => $inline_symbol,
            'inline_submit_class' => '',
            'inline_cancel_class' => '',
            'inline_show_button' => 0,
            'inline_label' => 0,
            'inline_auto_height' => 0,
        ];

        global $editableOption;
        $option = $editableOption->hupaApiEditArrayToObject($updateOption);
        update_option('hupa_get_editor_settings',$option);

        $responseJson->status = true;
        $responseJson->spinner = true;
        $responseJson->msg = date('H:i:s', current_time('timestamp'));
        break;
    case 'reset_options_settings':
          do_action('set_hupa_api_defaults');
          $responseJson->status = true;
          $responseJson->type = $method;
          $responseJson->msg = __('All settings reset!', 'hupa-api-editor');
        break;
    case'update_su_admin_options':
        filter_input(INPUT_POST, 'show_option_page', FILTER_SANITIZE_STRING) ? $show_option_page = 1 : $show_option_page = 0;
        $saveOptions = get_option('hupa_api_editable_options');

        $saveOptions['show_option'] = $show_option_page;
        update_option('hupa_api_editable_options', $saveOptions);
        $responseJson->status = true;
        $responseJson->type = 'admin_option';
        $responseJson->option_page = (bool) $show_option_page == 1;
        $responseJson->spinner = true;
        $responseJson->msg = date('H:i:s', current_time('timestamp'));
        break;

    case'get_select_data':
        $content_type = filter_input(INPUT_POST, 'content_type', FILTER_SANITIZE_STRING);
        $responseJson->rand = filter_input(INPUT_POST, 'rand', FILTER_SANITIZE_STRING);

        if(!$content_type){
            $responseJson->msg = 'Error: Ajax ??bertragungsfehler!';
        }
        global $editableOption;
        $responseJson->section_select = $editableOption->hupa_api_edit_input_select($content_type);
        $responseJson->output_select = $editableOption->hupa_api_edit_input_select('output_type');
        $responseJson->status = true;
        break;
}