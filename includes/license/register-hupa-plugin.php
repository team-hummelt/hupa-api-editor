<?php

namespace Hupa\EditorLicense;

defined('ABSPATH') or die();

/**
 * REGISTER HUPA CUSTOM THEME
 * @package Hummelt & Partner WordPress Theme
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */
final class RegisterHupaApiEditor
{
    /**
     * The Instance of this plugin.
     *
     * @since    1.0.0
     */
    private static $instance;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private string $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private string $version;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @var object      The main class.
     */
    public $main;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string     $plugin_name    The name of this plugin.
     * @param    string     $version        The version of this plugin.
     */

    /**
     * @return static
     */
    public static function instance(string $plugin_name, string $version, $plugin_main): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($plugin_name, $version, $plugin_main);
        }
        return self::$instance;
    }

    public function __construct(string $plugin_name, string $version, $plugin_main){

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->main = $plugin_main;

    }

    /**
     * =================================================
     * =========== REGISTER THEME ADMIN MENU ===========
     * =================================================
     */

    public function register_license_hupa_api_editor_plugin(): void
    {
        $hook_suffix = add_menu_page(
            __('API Editor v' .$this->version, 'hupa-api-editor'),
            __('API Editor v'.$this->version, 'hupa-api-editor'),
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
        wp_register_script('hupa-api-editor-license-ajax-script', '', [], '', true);
        wp_enqueue_script('hupa-api-editor-license-ajax-script');
        wp_localize_script('hupa-api-editor-license-ajax-script', 'hup_api_editor_license_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $title_nonce
        ));
    }

    /**
     * ==================================================
     * =========== THEME AJAX RESPONSE HANDLE ===========
     * ==================================================
     */

    public function ApiEditorLicenceHandle(): void {
        $responseJson = null;
        check_ajax_referer( 'hupa_api_editor_license_handle' );
        require 'hupa-api-editor-license-ajax.php';
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
        wp_enqueue_style('api-editor-license-style',plugins_url(HUPA_API_EDITOR_BASENAME) . '/includes/license/assets/license-backend.css', array(),$this->version);
        wp_enqueue_script('api-editor-formular-license', plugins_url(HUPA_API_EDITOR_BASENAME) . '/includes/license/license-script.js', array(), $this->version, true );
    }
}

