<?php
defined('ABSPATH') or die();

/**
 * REGISTER HUPA API EDITOR
 * @package Hummelt & Partner HUPA API EDITOR
 * Copyright 2021, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 */

global $hupa_api_editor_license_exec;
$data = json_decode(file_get_contents("php://input"));

$backMsg = [];

if(isset($data) && $data->make_id == 'make_exec'){
    global $hupa_api_editor_license_exec;
	$makeJob = $hupa_api_editor_license_exec->make_api_exec_job($data);
	$backMsg =  [
		'msg' => $makeJob->msg,
		'status' => $makeJob->status,
	];
	echo json_encode($backMsg);
	exit();
}

if(isset($data) && $data->client_id !== get_option('hupa_api_editor_client_id')){
    $backMsg =  [
        'reply' => 'ERROR',
        'status' => false,
    ];
    echo json_encode($backMsg)."<br><br>";
    exit('ERROR');
}

if(isset($data)):
require_once ABSPATH . 'wp-admin/includes/plugin.php';
switch ($data->make_id) {
    case '1':
        $message = json_decode($data->message);
        $backMsg =  [
            'client_id' => get_option('hupa_api_editor_client_id'),
            'reply' => 'Plugin deaktiviert',
            'status' => true,
        ];

        update_option('hupa_api_editor_message',$message->msg);
        delete_option('hupa_api_editor_product_install_authorize');
        delete_option('hupa_api_editor_client_id');
        delete_option('hupa_api_editor_client_secret');
	    deactivate_plugins( HUPA_API_EDITOR_SLUG_PATH );
	    set_transient('show_api_editor_lizenz_info', true, 5);
        break;
    case'send_versions':
        $backMsg = [
            'status' => true,
            'theme_version' => 'v'.HUPA_API_EDITOR_VERSION,
        ];
        break;
    default:
        $backMsg = [
          'status' => false
        ];
}
endif;

$response = new stdClass();
if($data) {
    echo json_encode($backMsg);
}
