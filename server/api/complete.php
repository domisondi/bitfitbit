<?php
global $output;
global $database;

if(isset($_REQUEST['user_id']) && isset($_REQUEST['coll_id']) && isset($_REQUEST['item_id'])){
    $user = new User($_REQUEST['user_id']);
    $item = new Item($_REQUEST['item_id'], $_REQUEST['coll_id']);
    $user->complete_item($item);
}
else throw new Exception('Unrecognized request.');