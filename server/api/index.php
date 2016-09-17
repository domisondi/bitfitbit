<?php

require_once '../core/core.php';

global $output;
$output = array('status' => 0, 'err_msg' => '');

$valid_requests = array('items', 'bits', 'stats', 'complete');

ignore_user_abort(true);

ob_start();

try {
    if(in_array($_REQUEST['request'], $valid_requests)){
        require_once $_REQUEST['request'].'.php';
    }
    else {
        throw new Exception('Unrecognized request.');
    }
} catch(Exception $ex){
    $output['status'] = -1;
    $output['err_msg'] = $ex->getMessage();
}
echo json_encode($output);

header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();

// Do further actions here
if(function_exists('action_after_api')){
    action_after_api();
}