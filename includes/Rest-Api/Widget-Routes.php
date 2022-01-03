<?php
defined('ABSPATH') or die();
/**
 * The API WIDGET Routes class.
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


class Widget extends WP_REST_Controller
{

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version   = '2';
        $namespace = 'wp/v' . $version;
        $base      = '/widget';

        register_rest_route(
            $namespace,
            $base,
            array(
                'methods'              => WP_REST_Server::READABLE,
                'callback'             => array( $this, 'get_registered_items' ),
                'permission_callback'  => array( $this, 'permissions_check' )
            )
        );

        register_rest_route(
            $namespace,
            $base . '/(?P<id_base>[^/]+)',
            array(
                array(
                    'methods'              => WP_REST_Server::READABLE,
                    'callback'             => array( $this, 'get_items' ),
                    'permissions_callback' => array( $this, 'permissions_check' ),
                ),
                array(
                    'methods'              => WP_REST_Server::CREATABLE,
                    'callback'             => array( $this, 'create_item' ),
                    'permissions_callback' => array( $this, 'permissions_check' ),
                    'args'                 => array(
                        'sidebar_id' => array(
                            'required' => true
                        )
                    )
                )
            )
        );

        register_rest_route(
            $namespace,
            $base . '/(?P<id_base>[^/]+)/(?P<widget_number>[\d]+)',
            array(
                array(
                    'methods'              => WP_REST_Server::READABLE,
                    'callback'             => array( $this, 'get_item' ),
                    'permissions_callback' => array( $this, 'permissions_check' ),
                    //'permission_callback' => '__return_true',
                ),
                array(
                    'methods'              => WP_REST_Server::EDITABLE,
                    'callback'             => array( $this, 'update_item' ),
                    'permissions_callback' => array( $this, 'permissions_check' )
                    //'permission_callback' => '__return_true',
                ),
                array(
                    'methods'              => WP_REST_Server::DELETABLE,
                    'callback'             => array( $this, 'delete_item' ),
                    'permissions_callback' => array( $this, 'permissions_check' )
                    //'permission_callback' => '__return_true',
                )
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
    public function get_registered_items(WP_REST_Request $request ): WP_Error|WP_REST_Response
    {
        global $wp_widget_factory;

        $data = array();
        foreach ( $wp_widget_factory->widgets as $key => $item ) {
            $item->class = $key;
            $itemdata    = new WP_REST_Response( $item );
            $itemdata->add_link(
                'registered-widgets',
                rest_url( 'wp/v2/widget/' . $item->id_base ),
                array( 'embeddable' => true )
            );
            $data[] = $this->prepare_response_for_collection( $itemdata );
        }

        return rest_ensure_response( $data );
    }

    /**
     * Get subcollection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_items( $request ): WP_Error|WP_REST_Response
    {
        $id_base = $request->get_param( 'id_base' );

        $widget_options = get_option( 'widget_' . $id_base );
        unset( $widget_options['_multiwidget'] );

        $data = [ ];
        foreach ( $widget_options as $number => $item ) {
            if ( $number == 1 ) {
                continue;
            }

            $item = $this->get_widget_item( $id_base, $number );
            if ( ! $item ) {
                return new WP_Error( 404 );
            }

            $itemdata = new WP_REST_Response( $item );
            $itemdata->add_link(
                'self',
                rest_url( 'wp/v2/widget/' . $id_base . '/' . $number )
            );
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
        $id_base = $request->get_param( 'id_base' );
        $number  = absint( $request->get_param( 'widget_number' ) );

        $item = $this->get_widget_item( $id_base, $number );
        if ( ! $item ) {
            return new WP_Error( 404 );
        }

        $itemdata = new WP_REST_Response( $item );
        $itemdata->add_link(
            'self',
            rest_url( 'wp/v2/widget/' . $id_base . '/' . $number )
        );

        return $itemdata;
    }

    /**
     * Create one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function create_item( $request ): WP_Error|WP_REST_Response
    {
        global $wp_widget_factory, $wp_registered_widgets;

        $sidebar_id = $request->get_param( 'sidebar_id' );
        $id_base    = $request->get_param( 'id_base' );

        $widget_options = $option_keys = get_option( 'widget_' . $id_base );
        unset( $option_keys['_multiwidget'] );
        $option_keys = array_keys( $option_keys );
        $last_key    = array_pop( $option_keys );
        $number      = $last_key + 1;

        $widget = wp_filter_object_list( $wp_widget_factory->widgets, array( 'id_base' => $id_base ) );
        $widget = array_pop( $widget );

        $widget_options[ $number ] = @$widget->update( array(), array() );
        update_option( 'widget_' . $id_base, $widget_options );

        $sidebars_widgets = get_option( 'sidebars_widgets', array() );
        if ( is_array( $sidebars_widgets ) && isset( $sidebars_widgets['array_version'] ) ) {
            unset( $sidebars_widgets['array_version'] );
        }
        $sidebars_widgets[ $sidebar_id ][] = $id_base . '-' . $number;
        update_option( 'sidebars_widgets', $sidebars_widgets );

        $wp_registered_widgets[ $id_base . '-' . $number ] = array(
            'name'        => $widget->name,
            'id'          => $id_base . '-' . $number,
            'params'      => array(
                array(
                    'number' => $number
                )
            ),
            'classname'   => $widget->widget_options['classname'],
            'description' => $widget->widget_options['description'] ?? '',
        );

        $itemdata = new WP_REST_Response( $this->get_widget_item( $id_base, $number ), 201, array(
            'Location' => rest_url( 'wp/v2/widget/' . $id_base . '/' . $number )
        ) );
        $itemdata->add_link(
            'self',
            rest_url( 'wp/v2/widget/' . $id_base . '/' . $number )
        );

        return $itemdata;
    }

    /**
     * Update one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|bool|WP_REST_Response
     */
    public function update_item( $request ): WP_Error|bool|WP_REST_Response
    {
        global $wp_registered_widgets;

        $id_base = $request->get_param( 'id_base' );
        $number  = absint( $request->get_param( 'widget_number' ) );

        $item = $this->get_widget_item( $id_base, $number );
        if ( ! $item ) {
            return new WP_Error( 404 );
        }

        /** @var WP_Widget $widget */
        $widget = $wp_registered_widgets[ $id_base . '-' . $number ]['callback'][0];

        $widget_options            = get_option( 'widget_' . $id_base );
        $widget_options[ $number ] = $widget->update( $request->get_json_params(), $item['options'] );
        @update_option( 'widget_' . $id_base, $widget_options );

        return $this->get_widget_item( $id_base, $number );
    }

    /**
     * Delete one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function delete_item( $request ): WP_Error|WP_REST_Response
    {
        $id_base   = $request->get_param( 'id_base' );
        $number    =  absint( $request->get_param( 'widget_number' ) );
        $widget_id = $id_base . '-' . $number;

        $widget_options = get_option( 'widget_' . $id_base );
        unset( $widget_options[ $number ] );
        update_option( 'widget_' . $id_base, $widget_options );

        $sidebars_widgets = get_option( 'sidebars_widgets', array() );
        if ( is_array( $sidebars_widgets ) && isset( $sidebars_widgets['array_version'] ) ) {
            unset( $sidebars_widgets['array_version'] );
        }
        foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
            $deleted = false;
            foreach ( $widgets as $key => $id ) {
                if ( $id == $widget_id ) {
                    $deleted = true;
                    unset( $sidebars_widgets[ $sidebar_id ][ $key ] );
                    break;
                }
            }
            if ( $deleted ) {
                break;
            }
        }
        update_option( 'sidebars_widgets', $sidebars_widgets );

        return rest_ensure_response( true );
    }

    protected function get_widget_item( $id_base, $number ) {
        global $wp_registered_widgets;

        if ( ! isset( $wp_registered_widgets[ $id_base . '-' . $number ] ) ) {
            return false;
        }

        $widget_options = get_option( 'widget_' . $id_base );

        $widget            = $wp_registered_widgets[ $id_base . '-' . $number ];
        $widget['id_base'] = $id_base;
        $widget['options'] = $widget_options[ $number ];
        unset( $widget['callback'] );

        return $widget;
    }

    /**
     * Check if a given request has access.
     *
     * @return WP_Error|bool
     */
    public function permissions_check(): WP_Error|bool
    {
       return current_user_can( 'edit_theme_options' );
        //return true;
    }

}