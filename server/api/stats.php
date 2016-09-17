<?php
global $output;

if(isset($_REQUEST['access_token']) && isset($_REQUEST['user_id'])){
    global $user;
    $user = new User($_REQUEST['user_id']);
    $user->set_access_token($_REQUEST['access_token']);
    
    $output['stats'] = $user->get_stats();
}
else throw new Exception('Unrecognized request.');