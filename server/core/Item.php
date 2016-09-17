<?php

class Item {
    
    var $id;
    var $coll_id;
    var $name;
    var $nr_steps;
    
    public function __construct($id, $coll_id, $name, $nr_steps) {
        $this->id = $id;
        $this->coll_id = $coll_id;
        $this->name = $name;
        $this->nr_steps = $nr_steps;
    }
    
    public function insert_into_database() {
        global $database;
        $database->beginTransaction();
        $database->query("SELECT MAX(id) as max_id FROM items WHERE coll_id=:coll_id");
        $database->bind('coll_id', $this->coll_id);
        $item_id = $database->get_single()['max_id'] + 1;
        
        $database->query("INSERT INTO items (id, coll_id, name, nr_steps) VALUES (:id, :coll_id, :name, :nr_steps)");
        $database->bind('id', $item_id);
        $database->bind('coll_id', $this->coll_id);
        $database->bind('name', $this->name);
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
}