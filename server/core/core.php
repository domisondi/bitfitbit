<?php

global $fitbit_app_id;
$fitbit_app_id = '227Z6J';

global $fitbit_app_secret;
$fitbit_app_secret = 'e2ffbfc98113a4d0b007eaa5861efa2d';

global $fitbit_app_auth_url;
$fitbit_app_auth_url = 'https://www.fitbit.com/oauth2/authorize';

global $fitbit_app_refresh_url;
$fitbit_app_refresh_url = 'https://api.fitbit.com/oauth2/token';

global $fitbit_app_complete_auth_url;
$fitbit_app_complete_auth_url = 'https://www.fitbit.com/oauth2/authorize?response_type=token&client_id=227Z6J&redirect_uri=http%3A%2F%2F172.31.2.42%2Fbitfit%2Fserver%2F%3Fpage%3Dcallback&scope=activity%20heartrate%20location%20nutrition%20profile%20settings%20sleep%20social%20weight&expires_in=604800';

require_once 'Database.php';
require_once 'Item.php';
require_once 'Collection.php';
require_once 'User.php';
require_once 'FitBit.php';

global $fitbit;
$fitbit = new FitBit();

function get_collections() {
    global $database;
    $database->query("SELECT * FROM collections");
    $results = $database->get_results();
    $collections = array();
    foreach($results as $result){
        $collections[$result['id']] = new Collection($result['id'], $result);
    }
    return $collections;
}