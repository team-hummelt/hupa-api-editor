<?php

namespace Hupa\ApiRestRoutes;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') or die();

/**
 * The API Meta-Daten WP-Rest Routes class.
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
class Hupa_Meta_Routes extends WP_REST_Controller
{
    /**
     * The current Post ID.
     *
     * @since    1.0.0
     * @access   protected
     * @var      int|null $post_id The current Post ID.
     */
    protected ?int $post_id;

    public function __construct($postId)
    {
        $this->post_id = $postId;
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $base = '/hupa-meta';

        register_rest_route(
            $namespace,
            $base,
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_registered_items'),
                'permission_callback' => array($this, 'permissions_check')
            )
        );

        register_rest_route(
            $namespace,
            $base . '/(?P<post_id>[\d^/]+)',
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'permissions_callback' => array($this, 'permissions_check'),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permissions_callback' => array($this, 'permissions_check'),
                    'args' => array(
                        'post_id' => array(
                            'required' => true
                        )
                    )
                )
            )
        );

        register_rest_route(
            $namespace,
            $base . '/(?P<post_id>[\d^/]+)/(?P<meta_field>[^/]+)',
            array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permissions_callback' => array($this, 'permissions_check'),
                    //'permission_callback' => '__return_true',
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update_item'),
                    'permissions_callback' => array($this, 'permissions_check')
                    //'permission_callback' => '__return_true',
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array($this, 'delete_item'),
                    'permissions_callback' => array($this, 'permissions_check')
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
    public function get_registered_items(WP_REST_Request $request): WP_Error|WP_REST_Response
    {
        $data = [];
        return rest_ensure_response($data);
    }

    /**
     * Get subcollection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request): WP_Error|WP_REST_Response
    {
        $post_id = (int)$request->get_param('post_id');

        if (!$post_id) {
            return new WP_Error(404, ' POST ID');
        }

        $hupaMeta = get_post_meta($post_id);

        if (!$hupaMeta) {
            return new WP_Error(404, 'No meta data found 12');
        }

        $data = [];
        foreach ($hupaMeta as $key => $val) {
            preg_match('/^_?/', $key) ? $protect = true : $protect = false;
            $key = preg_replace('/^_?/', '', $key);
            is_numeric($val[0]) ? $value = (int)$val[0] : $value = esc_html(trim($val[0]));
            $iArr = [
                'meta' => $key,
                'value' => $value,
                'protected' => $protect
            ];

            $itemdata = new WP_REST_Response($iArr);
            $itemdata->add_link(
                'self',
                rest_url('wp/v2/hupa-meta/' . $post_id . '/' . $key)
            );
            $data[] = $this->prepare_response_for_collection($itemdata);
        }
        if (!$data) {

            return new WP_Error(404, 'No meta data found');
        }
        return rest_ensure_response($data);
    }

    /**
     * Get one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request): WP_Error|WP_REST_Response
    {
        $post_id = (int)$request->get_param('post_id');
        $meta_name = (string)$request->get_param('meta_field');
        if (!$post_id || !$meta_name) {
            return new WP_Error(404);
        }

        return $this->get_meta_item($post_id, $meta_name);

    }

    /**
     * GET Post Meta BY ID AND Field
     * @param $post_id
     * @param $meta_field
     * @return WP_Error|WP_REST_Response
     */
    public function get_meta_item($post_id, $meta_field): WP_Error|WP_REST_Response
    {
        $post_id = (int)$post_id;
        $meta_name = (string)$meta_field;
        if (!$post_id || !$meta_name) {
            return new WP_Error(404);
        }

        $hupaMeta = get_post_meta($post_id);
        if (!$hupaMeta) {
            return new WP_Error(404, 'No meta data found');
        }

        $data = [];
        foreach ($hupaMeta as $key => $val) {
            preg_match('/^_?/', $key) ? $protect = true : $protect = false;
            $protect ? $s = '_' . $meta_name : $s = $meta_name;
            if ($s === $key) {
                is_numeric($val[0]) ? $value = $val[0] : $value = esc_html(trim($val[0]));
                $data = [
                    'meta' => esc_html(trim($key)),
                    'value' => $value,
                    'protected' => $protect
                ];
            }
        }

        if (!$data) {
            $data = [
                'meta' => false,
                'value' => false,
                'protected' => false
            ];
        }
        $itemdata = new WP_REST_Response($data, 200);
        $itemdata->add_link(
            'self',
            rest_url('wp/v2/hupa-meta/' . $post_id . '/' . $meta_name)
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
    public function update_item($request): WP_Error|bool|WP_REST_Response
    {

        $post_id = (int)$request->get_param('post_id');
        $meta_name = (string)$request->get_param('meta_field');
        if (!$post_id || !$meta_name) {
            return new WP_Error(404);
        }

        $params = $request->get_body_params();
        if(!$params){
            return new WP_Error(404, 'No params data found');
        }

        if(!isset($params['value'])){
            return new WP_Error(404, 'No params data found');
        }

        $updateValue = $params['value'];
        $hupaMeta = get_post_meta($post_id);

        foreach ($hupaMeta as $key => $val) {
            preg_match('/^_?/', $key) ? $protect = true : $protect = false;
            $protect ? $s = '_' . $meta_name : $s = $meta_name;
            if ($s === $key) {
                update_post_meta($post_id, $key, $updateValue);
            }
        }

        return $this->get_meta_item($post_id, $meta_name);
    }

    /**
     * Check if a given request has access.
     *
     * @return WP_Error|bool
     */
    public function permissions_check(): WP_Error|bool
    {
        return current_user_can('edit_theme_options');
    }
}