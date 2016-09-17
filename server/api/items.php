<?php
global $output;
global $database;

if(isset($_REQUEST['access_token']) && isset($_REQUEST['user_id'])){
    $user = new User($_REQUEST['user_id']);
    $collections = get_collections();
    foreach($collections as $coll){
        $items = $coll->get_items();
        foreach($items as $item){
            $item->completed = $item->is_done_by_user($user);
        }
        $coll->items = $items;
    }
    $output['collections'] = $collections;
}
else throw new Exception('Unrecognized request.');