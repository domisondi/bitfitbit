<?php
global $output;
global $database;
global $fitbit;

if(isset($_REQUEST['access_token']) && isset($_REQUEST['user_id'])){
    $user = new User($_REQUEST['user_id']);
}
else throw new Exception('Unrecognized request.');