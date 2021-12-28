<?php

namespace Hupa\ApiEditorPluginLicense;

use Exception;
use stdClass;

defined('ABSPATH') or die();

/**
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */

    class HupaApiPluginApiEditorServerHandle
    {
        private static $api_filter_instance;

        /**
         * @return static
         */
        public static function init(): self
        {
            if (is_null(self::$api_filter_instance)) {
                self::$api_filter_instance = new self;
            }
            return self::$api_filter_instance;
        }

        public function __construct()
        {

        }

        public function HupaApiEditorUpdateServerUrl($url)
        {
            update_option('hupa_server_url', $url);
        }

        public function HupaApiEditorGetApiUrl($scope): string
        {
            $client_id = get_option('hupa_api_editor_client_id');
            return match ($scope) {
                'authorize_url' => get_option('hupa_server_url') . 'authorize?response_type=code&client_id=' . $client_id,
                default => '',
            };
        }

        public function HupaApiEditorInstallByAuthorizationCode($authorization_code): object
        {
            $error = new stdClass();
            $error->status = false;
            $client_id = get_option('hupa_api_editor_client_id');
            $client_secret = get_option('hupa_api_editor_client_secret');
            $token_url = get_option('hupa_server_url') . 'token';
            $authorization = base64_encode("$client_id:$client_secret");

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => "Basic {$authorization}"
                ),
                'body' => [
                    'grant_type' => "authorization_code",
                    'code' => $authorization_code
                ]
            );

            $response = wp_remote_post($token_url, $args);
            if (is_wp_error($response)) {
                $error->message = $response->get_error_message();
                return $error;
            }

            $apiData = json_decode($response['body']);
            if ($apiData->error) {
                $apiData->status = false;
                return $apiData;
            }

            update_option('hupa_api_editor_access_token', $apiData->access_token);
            $body = [
                'version' => HUPA_API_EDITOR_VERSION,
            ];

            return $this->hupaApiEditorPOSTApiResource('install', $body);
        }

        public function hupaApiEditorPOSTApiResource($scope, $body = false)
        {
            $response = wp_remote_post(get_option('hupa_server_url') . $scope, $this->HupaApiEditorApiPostArgs($body));
            if (is_wp_error($response)) {
                return $response->get_error_message();
            }
            if (is_array($response)) {
                $query = json_decode($response['body']);
                if (isset($query->error)) {
                    if ($this->get_error_message($query)) {
                        $this->HupaApiEditorGetApiClientCredentials();
                    }
                    $response = wp_remote_post(get_option('hupa_server_url') . $scope, $this->HupaApiEditorApiPostArgs($body));
                    if (is_array($response)) {
                        return json_decode($response['body']);
                    }
                } else {
                    return $query;
                }
            }
            return false;
        }

        public function hupaApiEditorServerPOSTApiResource($scope, $body = false)
        {
            $response = wp_remote_post(get_option('hupa_server_url') . $scope, $this->HupaApiEditorApiPostArgs($body));
            if (is_wp_error($response)) {
                return $response->get_error_message();
            }
            if (is_array($response)) {
                $query = json_decode($response['body']);
                if (isset($query->error)) {
                    if ($this->get_error_message($query)) {
                        $this->HupaApiEditorGetApiClientCredentials();
                    }
                    $response = wp_remote_post(get_option('hupa_server_url') . $scope, $this->HupaApiEditorApiPostArgs($body));
                    if (is_array($response)) {
                        return $response['body'];
                    }
                } else {
                    return $response['body'];
                }
            }
            return false;
        }

        public function HupaApiEditorGETApiResource($scope, $body = false)
        {
            $response = wp_remote_get(get_option('hupa_server_url') . $scope, $this->HupaApiEditorGETApiArgs($body));
            if (is_wp_error($response)) {
                return $response->get_error_message();
            }
            if (is_array($response)) {
                $query = json_decode($response['body']);
                if (isset($query->error)) {
                    if ($this->get_error_message($query)) {
                        $this->HupaApiEditorGetApiClientCredentials();
                    }
                    $response = wp_remote_get(get_option('hupa_server_url') . $scope, $this->HupaApiEditorGETApiArgs($body));
                    if (is_array($response)) {
                        return json_decode($response['body']);
                    }
                } else {
                    return $query;
                }
            }
            return false;
        }

        public function HupaApiEditorGETApiResourceOld($scope, $get = [])
        {
            $error = new stdClass();
            $error->status = false;

            $getUrl = '';
            if ($get) {
                $getUrl = implode('&', $get);
                $getUrl = '?' . $getUrl;
            }

            $url = get_option('hupa_server_url') . $scope . $getUrl;
            $args = $this->HupaApiEditorGETApiArgs();

            $response = wp_remote_get($url, $args);
            if (is_wp_error($response)) {
                $error->message = $response->get_error_message();
                return $error;
            }

            $apiData = json_decode($response['body']);
            if ($apiData->error) {
                $errType = $this->get_error_message($apiData);
                if ($errType) {
                    $this->HupaApiEditorGetApiClientCredentials();
                }
            }

            $response = wp_remote_get(get_option('hupa_server_url'), $this->HupaApiEditorGETApiArgs());
            if (is_wp_error($response)) {
                $error->message = $response->get_error_message();
                return $error;
            }
            $apiData = json_decode($response['body']);
            if (!$apiData->error) {
                $apiData->status = true;
                return $apiData;
            }

            $error->error = $apiData->error;
            $error->error_description = $apiData->error_description;
            return $error;
        }

        public function HupaApiEditorApiPostArgs($body = []): array
        {
            $bearerToken = get_option('hupa_api_editor_access_token');
            return [
                'method'        => 'POST',
                'timeout'       => 45,
                'redirection'   => 5,
                'httpversion'   => '1.0',
                'blocking'      => true,
                'sslverify'     => true,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => "Bearer $bearerToken"
                ],
                'body'          => $body
            ];
        }

        private function HupaApiEditorGETApiArgs($body = []): array
        {
            $bearerToken = get_option('hupa_api_editor_access_token');
            return [
                'method' => 'GET',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'sslverify' => true,
                'blocking' => true,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => "Bearer $bearerToken"
                ],
                'body'          => $body
            ];
        }

        private function HupaApiEditorGetApiClientCredentials(): void
        {
            $token_url = get_option('hupa_server_url') . 'token';
            $client_id = get_option('hupa_api_editor_client_id');
            $client_secret = get_option('hupa_api_editor_client_secret');
            $authorization = base64_encode("$client_id:$client_secret");
            $error = new stdClass();
            $error->status = false;
            $args = [
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'sslverify' => true,
                'blocking' => true,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => "Basic $authorization"
                ],
                'body' => [
                    'grant_type' => 'client_credentials'
                ]
            ];
            $response = wp_remote_post($token_url, $args);
            if (!is_wp_error($response)) {
                $apiData = json_decode($response['body']);
                update_option('hupa_api_editor_access_token', $apiData->access_token);
            }
        }

        public function HupaApiEditorApiDownloadFile($url, $body = []) {

            $bearerToken = get_option('hupa_api_editor_access_token');
            $args = [
                'method'        => 'POST',
                'timeout'       => 45,
                'redirection'   => 5,
                'httpversion'   => '1.0',
                'blocking'      => true,
                'sslverify'     => true,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => "Bearer $bearerToken"
                ],
                'body'          => $body
            ];

            $response = wp_remote_post( $url, $args );

            if (is_wp_error($response)) {
                $this->HupaApiEditorGetApiClientCredentials();
            }

            $response = wp_remote_post( $url, $args );

            if (is_wp_error($response)) {
                print_r($response->get_error_message());
                exit();
            }

            if( !is_array( $response ) ) {
                exit('Download Fehlgeschlagen!');
            }
            return $response['body'];
        }

        private function get_error_message($error): bool
        {
            $return = false;
            switch ($error->error) {
                case 'invalid_grant':
                case 'insufficient_scope':
                case 'invalid_request':
                    $return = false;
                    break;
                case'invalid_token':
                    $return = true;
                    break;
            }
            return $return;
        }

    }
