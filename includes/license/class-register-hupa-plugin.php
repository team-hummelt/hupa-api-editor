<?php

namespace Hupa\RegisterApiEditorLicense;

defined('ABSPATH') or die();

/**
 * REGISTER HUPA API Editor
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */
final class RegisterHupaApiEditor
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

    public function __construct(){}

    /**
     * ==================================================
     * =========== REGISTER PLUGIN ADMIN MENU ===========
     * ==================================================
     */
    public function register_license_hupa_api_editor_plugin(): void
    {
        $hook_suffix = add_menu_page(
            __('Editor-API', 'hupa-api-editor'),
            __('Editor-API', 'hupa-api-editor'),
            'manage_options',
            'hupa-api-editor-license',
            array($this, 'hupa_api_editor_license'),
            'dashicons-lock', 2
        );
        add_action('load-' . $hook_suffix, array($this, 'hupa_api_editor_load_ajax_admin_options_script'));
    }


    public function hupa_api_editor_license(): void
    {
        require 'activate-hupa-api-editor-page.php';
    }

    /**
     * =========================================
     * =========== ADMIN AJAX HANDLE ===========
     * =========================================
     */

    public function hupa_api_editor_load_ajax_admin_options_script(): void
    {
        add_action('admin_enqueue_scripts', array($this, 'load_hupa_api_editor_admin_style'));
        $title_nonce = wp_create_nonce('hupa_api_editor_license_handle');
        wp_register_script('hupa-api-editor-ajax-script', '', [], '', true);
        wp_enqueue_script('hupa-api-editor-ajax-script');
        wp_localize_script('hupa-api-editor-ajax-script', 'hupa_api_editor_license_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $title_nonce
        ));
    }

    /**
     * ==================================================
     * =========== THEME AJAX RESPONSE HANDLE ===========
     * ==================================================
     */

    public function prefix_ajax_HupaApiEditorLicenceHandle(): void {
        $responseJson = null;
        check_ajax_referer( 'hupa_api_editor_license_handle' );
        require 'hupa-license-ajax.php';
        wp_send_json( $responseJson );
    }

    /*===============================================
       TODO GENERATE CUSTOM SITES
    =================================================
    */
    public function hupa_api_editor_license_site_trigger_check(): void {
        global $wp;
        $wp->add_query_var( HUPA_API_EDITOR_BASENAME );
    }

    function hupa_api_editor_license_callback_trigger_check(): void {
       if ( get_query_var( HUPA_API_EDITOR_BASENAME ) === HUPA_API_EDITOR_BASENAME) {
            require 'api-request-page.php';
            exit;
        }
    }

    /**
     * ====================================================
     * =========== THEME ADMIN DASHBOARD STYLES ===========
     * ====================================================
     */

    public function load_hupa_api_editor_admin_style(): void
    {
        wp_enqueue_style('hupa-api-editor-license-style',plugins_url('hupa-minify') . '/inc/license/assets/license-backend.css', array(), '');
        wp_enqueue_script('js-hupa-api-editor-license', plugins_url('hupa-minify') . '/inc/license/license-script.js', array(), '', true );
    }
}

/*$register_hupa_minify = RegisterHupaMinify::hupa_minify_instance();
if (!empty($register_hupa_minify)) {
	$register_hupa_minify->init_hupa_minify();
}*/
