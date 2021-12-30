<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/public
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Api_Editor_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private string $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
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
     * Hupa API Edit Controls Language.
     *
     * @since    1.0.0
     * @var string Language
     */
    public $language;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct(string $plugin_name, string $version, object $plugin_main, string $language = null)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->main = $plugin_main;
        $this->language = $language;

    }

    /** Register Ajax Prefix PUBLIC Action
     * @since    1.0.0
     */
    public function prefix_ajax_HupaApiEditorNoAdmin(): void
    {
        $responseJson = null;
        check_ajax_referer('hupa_api_editor_public_handle');
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-hupa-api-editor-ajax.php';
        wp_send_json($responseJson);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Hupa_Api_Editor_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Hupa_Api_Editor_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (get_option('hupa_api_editor_product_install_authorize')) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hupa-api-editor-public.css', array(), $this->version, 'all');
        }
    }


    public function hupa_api_editor_public_init(): bool
    {
        global $editableOption;
        $options = $editableOption->hupa_set_editable_options();
        $err = '';
        if (isset($options['capability']) && !empty($options['capability'])) {
            if (current_user_can($options['capability'])) {
                $err = '';
            } else {
                $err .= 'error-capability';
            }
        }

        if (is_page()) {
            isset($options['show_editable_interfaces']['page']) && $options['show_editable_interfaces']['page'] == 'show' ? $err .= '' : $err .= 'error-page';
        }

        if (is_single()) {
            isset($options['show_editable_interfaces']['post']) && $options['show_editable_interfaces']['post'] == 'show' ? $err .= '' : $err .= 'error-single';
        }

        strlen($err > 0) ? $return = false : $return = true;
        return $return;
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Hupa_Api_Editor_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Hupa_Api_Editor_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if ($this->hupa_api_editor_public_init() && get_option('hupa_api_editor_product_install_authorize')):

            wp_enqueue_script($this->plugin_name . '-editable', plugin_dir_url(__FILE__) . 'js/tools/jquery.jeditable.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-editable-autogrow', plugin_dir_url(__FILE__) . 'js/tools/jquery.jeditable.autogrow.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name . '-editable-autogrow-textarea', plugin_dir_url(__FILE__) . 'js/tools/jquery.autogrowtextarea.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hupa-api-editor-public.js', array('jquery'), $this->version, true);
            wp_enqueue_script('wp-api');

            global $editableOption;
            $title_nonce = wp_create_nonce('hupa_api_editor_public_handle');
            wp_register_script('api-editor-public-ajax-script', '', [], '', true);
            wp_enqueue_script('api-editor-public-ajax-script');
            wp_localize_script('api-editor-public-ajax-script', 'api_editor_ajax_obj', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => $title_nonce,
                'post_id' => get_the_ID(),
                'is_page' => is_page(),
                'language' => $editableOption->hupa_api_editor_language()
            ));
        endif;
    }

}
