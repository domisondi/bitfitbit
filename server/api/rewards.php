<?php
global $output;
global $database;

if(isset($_REQUEST['user_id'])){
    $user = new User($_REQUEST['user_id']);
    $rewards = $user->get_rewards();
    $output['rewards'] = $rewards;
}
else throw new Exception('Unrecognized request.');