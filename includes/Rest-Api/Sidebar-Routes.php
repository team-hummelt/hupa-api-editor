<?php
defined('ABSPATH') or die();
/**
 * The API Sidebar WP-Rest Routes class.
 *
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hupa_Api_Editor
 * @subpackage Hupa_Api_Editor/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */

class Sidebar extends WP_REST_Controller
{

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version   = '2';
        $namespace = 'wp/v' . $version;
        $base      = '/sidebar';

        register_rest_route(
            $namespace,
            $base,
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                //'permission_callback' => array( $this, 'permissions_check' )
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $namespace,
            $base . '/(?P<sidebar_id>[^/]+)',
            array(
                'methods'              => WP_REST_Server::READABLE,
                'callback'             => array( $this, 'get_item' ),
                'permissions_callback' => array( $this, 'permissions_check' )
                //'permission_callback' => '__return_true',
            )
        );
    }

    /**
     * Get a collection of items.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_items( $request ): WP_Error|WP_REST_Response
    {
        global $wp_registered_sidebars, $sidebars_widgets;

        $data = [ ];
        foreach ( $wp_registered_sidebars as $item ) {
            $itemdata = new WP_REST_Response( $item );

            $itemdata->add_link(
                'self',
                rest_url( 'wp/v2/sidebar/' . $item['id'] )
            );

            foreach ( $sidebars_widgets[ $item['id'] ] as $widget ) {
                preg_match( '/^(.+)-(\d+)$/', $widget, $matches );
                $itemdata->add_link(
                    'widgets',
                    rest_url( 'wp/v2/widget/' . $matches[1] . '/' . $matches[2] ),
                    array( 'embeddable' => true )
                );
            }

            $data[] = $this->prepare_response_for_collection( $itemdata );
        }

        return rest_ensure_response( $data );
    }

    /**
     * Get one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ): WP_Error|WP_REST_Response
    {
        global $wp_registered_sidebars, $sidebars_widgets;

        $sidebar_id = $request->get_param( 'sidebar_id' );

        if ( ! isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
            return new WP_Error( 404 );
        }

        $itemdata = new WP_REST_Response( $wp_registered_sidebars[ $sidebar_id ] );

        $itemdata->add_link(
            'self',
            rest_url( 'wp/v2/sidebar/' . $sidebar_id )
        );

        foreach ( $sidebars_widgets[ $sidebar_id ] as $widget ) {
            preg_match( '/^(.+)-(\d+)$/', $widget, $matches );
            $itemdata->add_link(
                'widgets',
                rest_url( 'wp/v2/widget/' . $matches[1] . '/' . $matches[2] ),
                array( 'embeddable' => true )
            );
        }

        return $itemdata;
    }

    /**
     * Check if a given request has access.
     *
     * @return WP_Error|bool
     */
    public function permissions_check(): WP_Error|bool
    {
        return current_user_can( 'edit_theme_options' );
    }

}