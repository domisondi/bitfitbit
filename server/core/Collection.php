<?php

class Collection {
    
    var $id;
    var $name;
    var $description;
    
    public function __construct($id, $data = null) {
        $this->id = $id;
        
        global $database;
        if(!$data || !is_array($data)){
            $database->query("SELECT * FROM collections WHERE id=:id");
            $database->bind('id', $id);
            $data = $database->get_single();
        }
        
        if($data) {
            foreach($data as $dat_name => $dat){
                if($dat_name == 'id') continue;
                
                $this->$dat_name = $dat;
            }
        }
        else throw new Exception ('Collection not found!');
    }
    
    public function insert_into_database() {
        global $database;
        $database->beginTransaction();
        $database->query("INSERT INTO collections (name, description) VALUES (:name, :description)");
        $database->bind('name', $this->name);
        $database->bind('description', $this->description);
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
    
    public function get_items() {
        global $database;
        $database->query("SELECT * FROM items WHERE coll_id=:coll_id");
        $database->bind('coll_id', $this->id);
        $results = $database->get_results();
        $items = array();
        foreach($results as $result){
            $items[$result['id']] = new Item($result['id'], $this->id, $result);
        }
        return $items;
    }
    
    public static function delete_collection($id){
        global $database;
        $database->query("DELETE FROM collections WHERE id=:id");
        $database->bind('id', $id);
        return $database->execute();
    }
}
