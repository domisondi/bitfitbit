<?php

class Item {
    
    var $id;
    var $coll_id;
    var $name;
    var $description;
    var $nr_steps;
    
    public function __construct($id, $coll_id, $data = null) {
        $this->id = $id;
        $this->coll_id = $coll_id;
        
        global $database;
        if(!$data || !is_array($data)){
            $database->query("SELECT * FROM items WHERE id=:id AND coll_id=:coll_id");
            $database->bind('id', $id);
            $database->bind('coll_id', $coll_id);
            $data = $database->get_single();
        }
        
        if($data) {
            foreach($data as $dat_name => $dat){
                if($dat_name == 'id') continue;
                else if($dat_name == 'coll_id') continue;
                
                $this->$dat_name = $dat;
            }
        }
        else throw new Exception ('Item not found!');
    }
    
    public function insert_into_database() {
        global $database;
        $database->beginTransaction();
        $database->query("SELECT MAX(id) as max_id FROM items WHERE coll_id=:coll_id");
        $database->bind('coll_id', $this->coll_id);
        $item_id = $database->get_single()['max_id'] + 1;
        
        $database->query("INSERT INTO items (id, coll_id, name, description, nr_steps) VALUES (:id, :coll_id, :name, :description, :nr_steps)");
        $database->bind('id', $item_id);
        $database->bind('coll_id', $this->coll_id);
        $database->bind('name', $this->name);
        $database->bind('description', $this->description);
        $database->bind('nr_steps', $this->nr_steps);
        $res = $database->execute();
        if($res===false) {
            $database->cancelTransaction();
            return false;
        }
        else {
            $database->endTransaction();
            return true;
        }
    }
    
    public function delete_from_database() {
        global $database;
        $database->query("DELETE FROM items WHERE id=:id AND coll_id=:coll_id");
        $database->bind('id', $this->id);
        $database->bind('coll_id', $this->coll_id);
        $database->execute();
    }
    
    public function get_done_by_users() {
        global $database;
        $database->query("SELECT * FROM items_done WHERE coll_id=:coll_id AND item_id=:item_id");
        $database->bind('coll_id', $this->coll_id);
        $database->bind('item_id', $this->id);
        
        $result = $database->get_results();
        
        $users = array();
        if($result){
            foreach($result as $res){
                $users[$res['user_id']] = new User($res['user_id']);
            }
        }
        return $users;
    }
    
    public function is_done_by_user($user){
        $users = $this->get_done_by_users();
        if(in_array($user, $users)) return true;
        else return false;
    }
}