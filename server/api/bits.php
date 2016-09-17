<?php
global $output;

if(isset($_REQUEST['access_token']) && isset($_REQUEST['user_id'])){
    global $user;
    $user = new User($_REQUEST['user_id']);
    $user->set_access_token($_REQUEST['access_token']);
    $output['bits'] = $user->refresh_bits_count();
    
    function action_after_api() {
        global $user;
        $user->update_in_database();
    }
}
else throw new Exception('Unrecognized request.');