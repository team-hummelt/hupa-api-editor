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
    case 'get_api_sections':
        global $editableOption;
        $options = $editableOption->hupa_set_editable_options();
        if (isset($options['capability']) && !empty($options['capability'])) {
            if (!current_user_can($options['capability'])) {
                $responseJson->msg = 'keine Berechtigung!';
                return $responseJson;
            }
        }

        $is_page = filter_input(INPUT_POST, 'is_page', FILTER_SANITIZE_NUMBER_INT);
        $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);

        global $post;
        get_post($post_id) ? $post = get_post($post_id) : $post = '';
        if (!$post) {
            return $responseJson;
        }
        $types = ['page', 'post'];
        if (!in_array(get_post_type(), $types)) {
            return $responseJson;
        }

        isset($options['show_editable_interfaces']['post']) ? $post_aktiv = $options['show_editable_interfaces']['post'] : $post_aktiv = '';
        isset($options['show_editable_interfaces']['page']) ? $page_aktiv = $options['show_editable_interfaces']['page'] : $page_aktiv = '';

        if (get_post_type() == 'page' && !$page_aktiv) {
            return $responseJson;
        }

        if (get_post_type() == 'post' && !$post_aktiv) {
            return $responseJson;
        }

        $sections = apply_filters('get_api_editor_table_editor', false);
        if (!$sections->status) {
            return $responseJson;
        }

        $settings = apply_filters('get_hupa_api_settings', false);
        $retArr = [];
//print_r($sections->record);
        foreach ($sections->record as $tmp) {
            if (!$tmp->pages_aktiv && get_post_type() == 'page') {
                continue;
            }
            if (!$tmp->posts_aktiv && get_post_type() == 'post') {
                continue;
            }

            $input_type = $tmp->output_type;
            switch ($tmp->output_type) {
                case 'textarea':
                    $type = 'textarea';
                    if ($settings->textarea_show_button) {
                        $submit_class = $settings->textarea_submit_class;
                        $cancel_class = $settings->textarea_cancel_class;
                        $save = $editableOption->hupa_api_editor_labels('save');
                        $cancel = $editableOption->hupa_api_editor_labels('cancel');
                    } else {
                        $submit_class = '';
                        $cancel_class = '';
                        $save = '';
                        $cancel = '';
                    }

                    if ($settings->textarea_label) {
                        $label = $editableOption->hupa_api_editor_labels('textarea');
                    } else {
                        $label = '';
                    }

                    $settings->textarea_auto_height ? $input_type = 'autogrow' : $input_type = 'textarea';
                    $form_wrapper = $settings->textarea_form_wrapper;
                    $form_class = $settings->textarea_form_class;
                    $symbol = (bool)$settings->textarea_symbol == '1';
                    $data_what = 'content';

                    break;
                case 'inline':
                    $type = 'inline';
                    $label = '';
                    $submit_class = '';
                    $cancel_class = '';
                    $save = '';
                    $cancel = '';
                    $form_wrapper = $settings->inline_form_wrapper;
                    $form_class = $settings->inline_form_class;
                    $symbol = (bool)$settings->inline_symbol == '1';
                    $data_what = 'content';
                    break;
                default:
                    if ($settings->input_show_button) {
                        $submit_class = $settings->input_submit_class;
                        $cancel_class = $settings->input_cancel_class;
                        $save = $editableOption->hupa_api_editor_labels('save');
                        $cancel = $editableOption->hupa_api_editor_labels('cancel');
                    } else {
                        $submit_class = '';
                        $cancel_class = '';
                        $save = '';
                        $cancel = '';
                    }
                    $type = $tmp->input_type;
                    $form_class = $settings->input_form_class;
                    $form_wrapper = $settings->input_form_wrapper;
                    if ($settings->input_label) {
                        $label = $editableOption->hupa_api_editor_labels($tmp->input_type);
                    } else {
                        $label = '';
                    }
                    $symbol = (bool)$settings->input_symbol == '1';
            }

            $ret_item = [
                'type' => $tmp->output_type,
                'input_type' => $input_type,
                'css_selector' => $tmp->css_selector,
                'form_wrapper' => $form_wrapper,
                'form_class' => $form_class,
                'submit_class' => $submit_class,
                'cancel_class' => $cancel_class,
                'symbol' => $symbol,
                'label' => $label,
                'tooltip' => $editableOption->hupa_api_editor_labels('tooltip'),
                'save' => $save,
                'cancel' => $cancel,
                'content_type' => $tmp->content_type,
                'indicator'=> $editableOption->hupa_api_editor_labels('saving'),
            ];

            $retArr[] = $ret_item;
        }

        if (!$retArr) {
            return $responseJson;
        }

        $responseJson->status = true;
        $responseJson->record = $retArr;

        break;
}