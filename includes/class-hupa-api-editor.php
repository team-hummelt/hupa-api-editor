<?php
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

use JetBrains\PhpStorm\NoReturn;
use Hupa\RegisterApiEditorLicense\RegisterHupaApiEditor;
use HupaApiEditorAPIExec\EXEC\HupaApiEditorLicenseExecAPI;
use Hupa\ApiEditorPluginLicense\HupaApiPluginApiEditorServerHandle;
use Hupa\ApiEditorDatabase\Hupa_Api_Editor_Database;


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
     * @var object The admin class.
     */
    public $admin;

    /**
     * Store plugin public class to allow public access.
     *
     * @since    1.0.0
     * @var object The public class.
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
     * Plugin DATABASE.
     *
     * @since    1.0.0
     * @var object      The Database class.
     */
    public $database;

    /**
     * The current version of the DB-Version.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $db_version The current version of the database Version.
     */
    protected $db_version;


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

        if (defined('HUPA_API_EDITOR_DB_VERSION')) {
            $this->db_version = HUPA_API_EDITOR_DB_VERSION;
        } else {
            $this->db_version = '1.0.0';
        }

        $this->plugin_name = 'hupa-api-editor';
        $this->main = $this;

        //Check PHP AND WordPress Version
        $this->check_dependencies();
        // Require dependencies
        $this->load_dependencies();
        //Set Locale "hupa-api-editor"
        $this->set_locale();
        // Register License and Import Data from Server
        $this->register_hupa_api_edit_license();
        // Set Settings Default-Optionen
        $this->set_editable_options();
        // Create OR Update Database
        $this->hupa_api_editor_update_database();
        if (get_option('hupa_api_editor_product_install_authorize')) {
            $this->define_admin_hooks();
            $this->define_public_hooks();
        }
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
         * The Trait of Default Settings
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/Hupa_Api_Editor_Settings.php';

        /**
         * The class that is responsible for the database
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hupa-api-editor-database.php';

        /**
         * The class responsible for the options of the Editable JS
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_hupa_editable_options.php';


        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        if (get_option('hupa_api_editor_product_install_authorize')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-hupa-api-editor-admin.php';
        }

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-hupa-api-editor-public.php';

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
     * Database entries for the data types of EditableJS
     *
     * Uses the class Hupa_Api_Editor_Database to create and edit the database.
     * register with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function hupa_api_editor_update_database()
    {
        global $hupaApiEditorDatabase;
        $this->database = new Hupa_Api_Editor_Database($this->db_version);
        $hupaApiEditorDatabase = $this->database;

        /**
         * Create Table-Editor Section
         * @since    1.0.0
         */
        $this->loader->add_action('init', $this->database, 'update_api_editor_database');

        /**
         * GET Table-Editor Section
         * @since    1.0.0
         */
        $this->loader->add_filter('get_api_editor_table_editor', $this->database, 'get_hupa_api_editor_sections',10, 2);

        /**
         * SET Table-Editor Section
         * @since    1.0.0
         */
        $this->loader->add_filter('set_api_editor_table_editor', $this->database, 'hupa_api_editor_set_table_editor');

        /**
         * Update Table-Editor Section
         * @since    1.0.0
         */
        $this->loader->add_action('update_api_editor_table_editor', $this->database, 'hupa_api_editor_update_table_editor');

        /**
         * Delete Table-Editor Section
         * @since    1.0.0
         */
        $this->loader->add_action('delete_api_editor_table_editor', $this->database, 'hupa_api_editor_delete_table_editor');

        /**
         * Set Default Settings
         * @since    1.0.0
         */
        $this->loader->add_action('set_api_editor_default_optionen', $this->database, 'hupa_api_set_default_settings');


        /**
         * GET Optionen Settings
         * @since    1.0.0
         */
        $this->loader->add_filter('get_hupa_api_settings', $this->database, 'hupa_get_api_settings');


        /**
         * RESET ALL Optionen Settings
         * @since    1.0.0
         */
        $this->loader->add_filter('set_hupa_api_defaults', $this->database, 'hupa_api_editor_reset_settings');
    }


    /**
     * Basic settings Options for Editable JS
     *
     * Uses the Hupa_Editable_Options class to register the options and hook.
     * register with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_editable_options()
    {
        global $editableOption;
        $editableOption = Hupa_Editable_Options::instance();

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

        // JOB REGISTER LICENSE MENU
        if (!get_option('hupa_api_editor_product_install_authorize')) {
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

        /**
         * Register Ajax Prefix PUBLIC Action
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

    public function get_license_activate(): bool
    {
        if (get_option('hupa_api_editor_product_install_authorize')) {
            return true;
        }
        return false;
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
