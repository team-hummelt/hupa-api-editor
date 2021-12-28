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
class Hupa_Api_Editor_Public {

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
	 * @param    string    $plugin_name The name of the plugin.
	 * @param    string    $version     The version of this plugin.
	 */
	public function __construct(string $plugin_name, string $version ,$plugin_main ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->main = $plugin_main;

	}

    /** Register Ajax Prefix PUBLIC Action
     * @since    1.0.0
     */
    public function prefix_ajax_HupaApiEditorNoAdmin(): void {
        $responseJson = null;
        check_ajax_referer( 'hupa_api_editor_public_handle' );
        wp_send_json( $responseJson );
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hupa-api-editor-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hupa-api-editor-public.js', array( 'jquery' ), $this->version, true );

        global $post;
        $title_nonce = wp_create_nonce('hupa_api_editor_public_handle');
        wp_register_script('api-editor-public-ajax-script', '', [], '', true);
        wp_enqueue_script('api-editor-public-ajax-script');
        wp_localize_script('api-editor-public-ajax-script', 'api_editor_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $title_nonce,
            'post_id' => $post->ID,
            'plugin_url' => HUPA_API_EDITOR_PLUGIN_URL,
            'site_url' => site_url(),
        ));
	}

}
