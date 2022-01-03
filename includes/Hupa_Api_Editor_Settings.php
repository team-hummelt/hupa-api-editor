<?php

namespace Hupa\ApiEditorDatabase;

defined('ABSPATH') or die();

/**
 * ADMIN Settings TRAIT
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2022, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 *
 * @Since 1.0.0
 */
trait Hupa_Api_Editor_Settings
{
    //DATABASE TABLES
    protected string $table_editor = 'hupa_api_editor';
    protected string $table_api_settings = 'hupa_api_settings';

    //SETTINGS DEFAULT OBJECT
    protected array $api_editor_default_values;
    //Default CSS Sections
    protected int $posts_aktiv = 1;
    protected int $pages_aktiv = 1;

    //Default EditorJs Settings
    protected string $textarea_form_wrapper = 'api-edit-wrapper';
    protected string $textarea_form_class = 'w-100';
    protected string $textarea_submit_class = 'btn btn-outline-success btn-sm mt-2 me-1';
    protected string $textarea_cancel_class = 'btn btn-outline-danger btn-sm mt-2 me-1';
    protected int $textarea_show_button = 1;
    protected int $textarea_symbol = 1;
    protected int $textarea_label = 1;
    protected int $textarea_auto_height = 1;
    //Default EditorJs Inputs
    protected string $input_form_wrapper = 'api-edit-wrapper';
    protected string $input_form_class = 'w-100';
    protected string $input_submit_class = 'btn btn-outline-success btn-sm mt-2 me-1';
    protected string $input_cancel_class = 'btn btn-outline-danger btn-sm mt-2 me-1';
    protected int $input_show_button = 1;
    protected int $input_symbol = 1;
    protected int $input_label = 1;
    protected int $input_auto_height = 0;
    //Default EditorJs Inline
    protected string $inline_form_wrapper = 'api-edit-inline';
    protected string $inline_form_class = 'form-control';
    protected string $inline_submit_class = '';
    protected string $inline_cancel_class = '';
    protected int $inline_symbol = 1;
    protected int $inline_show_button = 0;
    protected int $inline_label = 0;
    protected int $inline_auto_height = 0;

    protected function get_theme_default_settings(): array
    {
        return $this->api_editor_default_values = [

            'editor_sections_default' => [
                '0' => [
                    'css_selector' => 'entry-title',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'post',
                    'output_type' => 'text',
                    'section_type' => 'title',
                ],
                '1' => [
                    'css_selector' => 'has-excerpt',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'post',
                    'output_type' => 'textarea',
                    'section_type' => 'excerpt',
                ],
               /* '2' => [
                    'css_selector' => 'widget-title',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'text_widget',
                    'output_type' => 'text',
                    'section_type' => 'title',
                ],
                '3' => [
                    'css_selector' => 'textwidget',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'text_widget',
                    'output_type' => 'textarea',
                    'section_type' => 'text',
                ],
                '4' => [
                    'css_selector' => 'widget-title',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'html_widget',
                    'output_type' => 'text',
                    'section_type' => 'title',
                ],
                '5' => [
                    'css_selector' => 'textwidget',
                    'posts_aktiv' => $this->posts_aktiv,
                    'pages_aktiv' => $this->pages_aktiv,
                    'content_type' => 'html_widget',
                    'output_type' => 'textarea',
                    'section_type' => 'content',
                ]*/
            ],

            'input_edit_form_default' => [
                'textarea_form_wrapper' => $this->textarea_form_wrapper,
                'textarea_form_class' => $this->textarea_form_class,
                'textarea_submit_class' => $this->textarea_submit_class,
                'textarea_cancel_class' => $this->textarea_cancel_class,
                'textarea_show_button' => $this->textarea_show_button,
                'textarea_symbol' => $this->textarea_symbol,
                'textarea_label' => $this->textarea_label,
                'textarea_auto_height' => $this->textarea_auto_height,

                'input_form_wrapper' => $this->input_form_wrapper,
                'input_form_class' => $this->input_form_class,
                'input_submit_class' => $this->input_submit_class,
                'input_cancel_class' => $this->input_cancel_class,
                'input_show_button' => $this->input_show_button,
                'input_symbol' => $this->input_symbol,
                'input_label' => $this->input_label,
                'input_auto_height' => $this->input_auto_height,

                'inline_form_wrapper' => $this->inline_form_wrapper,
                'inline_form_class' => $this->inline_form_class,
                'inline_submit_class' => $this->inline_submit_class,
                'inline_cancel_class' => $this->inline_cancel_class,
                'inline_show_button' => $this->inline_show_button,
                'inline_symbol' => $this->inline_symbol,
                'inline_label' => $this->inline_label,
                'inline_auto_height' => $this->inline_auto_height,
            ]
        ];
    }
}