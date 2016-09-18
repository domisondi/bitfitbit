<?php
global $output;
global $database;

if(isset($_REQUEST['user_id']) && isset($_REQUEST['access_token']) && isset($_REQUEST['coll_id']) && isset($_REQUEST['item_id'])){
    $user = new User($_REQUEST['user_id']);
    $item = new Item($_REQUEST['item_id'], $_REQUEST['coll_id']);
    $output['step_count'] = $user->complete_item($item);
    
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