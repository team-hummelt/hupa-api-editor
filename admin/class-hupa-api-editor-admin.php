<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://jenswiecker.de
 * @since      1.0.0
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/admin
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Api_Editor_Admin {

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
	public function __construct(string $plugin_name, string $version, $plugin_main) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->main = $plugin_main;
	}

    /**
     * Register the admin menu.
     *
     * @since    1.0.0
     */
    public function create_menu():void {

        add_menu_page(
            __( 'API-Editor', 'hupa-api-editor' ),
            __( 'API-Editor', 'hupa-api-editor' ),
            'manage_options',
            'api-editor',
            '',
            'dashicons-admin-multisite', 9
        );

        $hook_suffix = add_submenu_page(
            'api-editor',
            __( 'API-Editor - Settings', 'hupa-api-editor' ),
            __( 'API-Editor Settings ', 'hupa-api-editor' ),
            'manage_options',
            'api-editor',
            array( $this, 'admin_hupa_api_editor_page' )
        );

        add_action('load-' . $hook_suffix, array($this, 'hupa_api_editor_load_ajax_admin_options_script'));

    }

    /**
     * Register the Plugin Settings Link.
     *
     * @since    1.0.0
     */
    public static function api_editor_plugin_add_action_link( $data ) {
        // check permission
        if ( ! current_user_can( 'manage_options' ) ) {
            return $data;
        }

        return array_merge(
            $data,
            array(
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg(
                        array(
                            'page' => 'api-editor'
                        ),
                        admin_url( 'admin.php' )
                    ),
                    __( "Settings", "api-editor" )
                )
            )
        );
    }


    public function admin_hupa_api_editor_page() {
        require 'partials/hupa-api-editor-startseite.php';
    }

    public function hupa_api_editor_load_ajax_admin_options_script() {
        add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue_scripts' ) );
    }


    /** Register Ajax Prefix ADMIN Action
     * @since    1.0.0
     */
    public function prefix_ajax_HupaApiEditorHandle(): void {
        $responseJson = null;
        check_ajax_referer( 'hupa_api_editor_admin_handle' );
        wp_send_json( $responseJson );
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function load_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hupa_Api_Editor_Loader as all the hooks are defined
		 * in that particular class.
		 *
		 * The Hupa_Api_Editor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style('hupa-starter-admin-bs-style', plugin_dir_url( __FILE__ ) . 'css/bs/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style('hupa-starter-admin-icons', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-bs5-data-tables', plugin_dir_url( __FILE__ ) . 'css/tools/dataTables.bootstrap5.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-admin-dashboard-style', plugin_dir_url( __FILE__ ) . 'css/admin-dashboard-style.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hupa-api-editor-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function load_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hupa_Api_Editor_Loader as all the hooks are defined
		 * in that particular class.
		 *
		 * The Hupa_Api_Editor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


        wp_enqueue_script( $this->plugin_name.'-bs-admin-script', plugin_dir_url( __FILE__ ) . 'js/bs/bootstrap.bundle.min.js', array(), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'-jquery-table', plugin_dir_url( __FILE__ ) . 'js/tools/data-table/jquery.dataTables.min.js', array(), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'-bs5-data-table', plugin_dir_url( __FILE__ ) . 'js/tools/data-table/dataTables.bootstrap5.min.js', array(), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'-api-editor-data-table', plugin_dir_url( __FILE__ ) . 'js/hupa-api-data-tables.js', array(), $this->version, true );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hupa-api-editor-admin.js', array('jquery'), $this->version, true );

        $title_nonce = wp_create_nonce('hupa_api_editor_admin_handle');
        wp_register_script('hupa-api-editor-ajax-script', '', [], '', true);
        wp_enqueue_script('hupa-api-editor-ajax-script');
        wp_localize_script('hupa-api-editor-ajax-script', 'api_editor_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $title_nonce,
        ));
	}

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name.'admin-tools', plugin_dir_url( __FILE__ ) . 'css/tools.css', array(), $this->version, 'all');
    }

}
//ORGINAL