<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 */

use JetBrains\PhpStorm\NoReturn;
use Hupa\RegisterApiEditorLicense\RegisterHupaApiEditor;
use HupaApiEditorAPIExec\EXEC\HupaApiEditorLicenseExecAPI;
use Hupa\ApiEditorPluginLicense\HupaApiPluginApiEditorServerHandle;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Api_Editor
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Hupa_Api_Editor_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected Hupa_Api_Editor_Loader $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected string $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected string $version;

    /**
     * Store plugin admin class to allow public access.
     *
     * @since    1.0.0
     * @var object      The admin class.
     */
    public $admin;

    /**
     * Store plugin public class to allow public access.
     *
     * @since    1.0.0
     * @var object      The public class.
     */
    public $public;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @var object      The main class.
     */
    public $main;


    /**
     * Activate Plugin License.
     *
     * @since    1.0.0
     * @var object      The license class.
     */
    public $license;

    /**
     * WP-Remote Plugin License.
     *
     * @since    1.0.0
     * @var object      The Remote class.
     */
    public $remote;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */

    public function __construct()
    {
        if (defined('HUPA_API_EDITOR_VERSION')) {
            $this->version = HUPA_API_EDITOR_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'hupa-api-editor';

        $this->main = $this;

        $this->check_dependencies();
        $this->load_dependencies();
        $this->set_locale();
        $this->register_hupa_api_edit_license();
        if (get_option('hupa_api_editor_product_install_authorize')) {
            $this->define_admin_hooks();
        }
        $this->define_public_hooks();

    }

    /**
     * Validate Hupa Api Edit License.
     *
     * Include the following files that make up the plugin:
     *
     * - Hupa_Api_Editor_Loader. Orchestrates the hooks of the plugin.
     * - Hupa_Api_Editor_i18n. Defines internationalization functionality.
     * - RegisterHupaApiEditor. Defines all hooks for the License.
     * - HupaApiEditorLicenseExecAPI. Defines all hooks for the License Exec.
     * - HupaApiPluginApiEditorServerHandle. Defines all hooks for the License API WP-Remote.
     * - Hupa_Api_Editor_Admin. Defines all hooks for the admin area.
     * - Hupa_Api_Editor_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class, Registers and activates the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/license/class-register-hupa-plugin.php';

        /**
         * The class API EXEC.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/license/api-exec-class.php';

        /**
         * The class API WP-Remote Class.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/license/hupa_client_api_wp_remote.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hupa-api-editor-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hupa-api-editor-i18n.php';


        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-hupa-api-editor-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-hupa-api-editor-public.php';

        /**
         * The class responsible for defining all actions for ADMIN AJAX
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-hupa-api-editor-ajax.php';

        /**
         * The class responsible for defining all actions for PUBLIC AJAX
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-hupa-api-editor-ajax.php';

        $this->loader = new Hupa_Api_Editor_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Hupa_Api_Editor_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Hupa_Api_Editor_i18n();
        $this->loader->add_action('init', $plugin_i18n, 'load_api_editor_textdomain');
    }

    /**
     * Validate Hupa Api Edit License.
     *
     * Uses the class RegisterHupaApiEditor to register the licence for the plugin
     * @since    1.0.0
     * @access   private
     */
    private function register_hupa_api_edit_license()
    {
        $this->license = RegisterHupaApiEditor::instance();

        /** Register License Admin Menu
         * @since    1.0.0
         */

        // TODO REGISTER LICENSE MENU
        if (!get_option('hupa_api_editor_install_authorize')) {
            $this->loader->add_action('admin_menu', $this->license, 'register_license_hupa_api_editor_plugin');
        }

        $this->loader->add_action('wp_ajax_HupaApiEditorLicenceHandle', $this->license, 'prefix_ajax_HupaApiEditorLicenceHandle');
        $this->loader->add_action('init', $this->license, 'hupa_api_editor_license_site_trigger_check');
        $this->loader->add_action('template_redirect', $this->license, 'hupa_api_editor_license_callback_trigger_check');

        /** Register License API EXEC CLASS
         * @since    1.0.0
         */
        global $hupa_api_editor_license_exec;
        $hupa_api_editor_license_exec = HupaApiEditorLicenseExecAPI::instance();


        /** Register License API WP-REMOTE CLASS
         * @since    1.0.0
         */

        global $hupa_api_editor_license_wp_remote;
        $hupa_api_editor_license_wp_remote = HupaApiPluginApiEditorServerHandle::instance();
        $this->remote = $hupa_api_editor_license_wp_remote;

        $this->loader->add_action('plugin_loaded', $this->remote, 'wp_loaded_api_editor_remote');

    }

    /**
     * Register all the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $this->admin = new Hupa_Api_Editor_Admin($this->get_plugin_name(), $this->get_version(), $this->main);

        /** Register Admin Menu
         * @since    1.0.0
         */
        $this->loader->add_action('admin_menu', $this->admin, 'create_menu', 0);

        /** Register Plugin Settings Menu
         * @since    1.0.0
         */
        $this->loader->add_filter('plugin_action_links_' . HUPA_API_EDITOR_SLUG_PATH, $this->admin, 'api_editor_plugin_add_action_link');

        /** Register Ajax Prefix ADMIN Action
         * @since    1.0.0
         */
        $this->loader->add_action('wp_ajax_HupaApiEditorHandle', $this->admin, 'prefix_ajax_HupaApiEditorHandle');

        /** Register ADMIN Enqueue Style Action
         * @since    1.0.0
         */
        $this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_styles');
        //$this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_scripts');

    }

    /**
     * Check PHP and WordPress Version
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function check_dependencies(): void
    {
        global $wp_version;
        if (version_compare(PHP_VERSION, HUPA_API_EDITOR_MIN_PHP_VERSION, '<') || $wp_version < HUPA_API_EDITOR_MIN_WP_VERSION) {
            $this->maybe_self_deactivate();
        }
    }

    /**
     * Self-Deactivate
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function maybe_self_deactivate(): void
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins(HUPA_API_EDITOR_SLUG_PATH);
        add_action('admin_notices', array($this, 'self_deactivate_notice'));
    }

    /**
     * Self-Deactivate Admin Notiz
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    #[NoReturn] public function self_deactivate_notice(): void
    {
        echo sprintf('<div class="error" style="margin-top:5rem"><p>' . __('This plugin has been disabled because it requires a PHP version greater than %s and a WordPress version greater than %s. Your PHP version can be updated by your hosting provider.', 'hupa-api-editor') . '</p></div>', HUPA_API_EDITOR_MIN_PHP_VERSION, HUPA_API_EDITOR_MIN_WP_VERSION);
        exit();
    }

    /**
     * Register all the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $this->public = new Hupa_Api_Editor_Public($this->get_plugin_name(), $this->get_version(), $this->main);

        /** Register Ajax Prefix PUBLIC Action
         * @since    1.0.0
         */
        $this->loader->add_action('wp_ajax_nopriv_HupaApiEditorNoAdmin', $this->public, 'prefix_ajax_HupaApiEditorNoAdmin');
        $this->loader->add_action('wp_ajax_HupaApiEditorNoAdmin', $this->public, 'prefix_ajax_HupaApiEditorNoAdmin');


        $this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name(): string
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Hupa_Api_Editor_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader(): Hupa_Api_Editor_Loader
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version(): string
    {
        return $this->version;
    }

}
