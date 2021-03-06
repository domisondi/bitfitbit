<?php

class User {
    
    var $id;
    var $name;
    var $bits;
    var $steps_used;
    
    private $fitbit;
    private $access_token;
    
    public function __construct($id, $data = null) {
        $this->id = $id;
        $this->fitbit = new FitBit();
        
        global $database;
        if(!$data || !is_array($data)){
            $database->query("SELECT * FROM users WHERE id=:id");
            $database->bind('id', $id);
            $data = $database->get_single();
        }
        
        if($data) {
            foreach($data as $dat_name => $dat){
                if($dat_name == 'id') continue;
                
                $this->$dat_name = $dat;
            }
        }
        else {
            $this->name = $id;
            $this->bits = 0;
            $this->steps_used = $this->get_fitbit_lifetime_stepcount();
            $this->insert_into_database();
        }
    }
    
    public function insert_into_database() {
        global $database;
        $database->query("INSERT INTO users (id, name, bits) VALUES (:id, :name, :bits)");
        $database->bind('id', $this->id);
        $database->bind('name', $this->name);
        $database->bind('bits', $this->bits);
        $database->execute();
    }
    
    public function update_in_database() {
        global $database;
        $database->query("UPDATE users SET name=:name, bits=:bits, steps_used=:steps_used WHERE id=:id");
        $database->bind('id', $this->id);
        $database->bind('name', $this->name);
        $database->bind('bits', $this->bits);
        $database->bind('steps_used', $this->steps_used);
        $database->execute();
    }
    
    public function set_access_token($token){
        $this->token = $token;
    }
    
    public function get_access_token(){
        if($this->access_token) return $this->access_token;
        else if(isset ($_REQUEST['access_token'])) return $_REQUEST['access_token'];
        else throw new Exception ('No access token found!');
    }
    
    public function refresh_bits_count() {
        $this->fitbit->init($this->id, $this->get_access_token());
        $data = $this->fitbit->get_data('user/[user-id]/activities/date/2016-09-17.json');
        
        return $data;
    }
    
    public function has_item_completed($item){
        return $item->is_done_by_user($this);
    }
    
    public function get_current_step_count() {
        return $this->get_fitbit_lifetime_stepcount() - $this->steps_used;
    }
    
    public function get_fitbit_lifetime_stepcount() {
        $this->fitbit->init($this->id, $this->get_access_token());
        $raw_data = $this->fitbit->get_data('user/[user-id]/activities.json');
        $data = json_decode($raw_data);
        
        $stepcount = $data->lifetime->total->steps;
        
        return $stepcount;
    }
    
    public function get_stats() {
        $stats = array();
        $stats['step_count'] = $this->get_current_step_count();
        
        return $stats;
    }
    
    public function complete_item($item){
        // check if not already completed
        if($this->has_item_completed($item)) throw new Exception('Item already completed...');
        
        $avail_steps = $this->get_current_step_count();
        if($item->nr_steps > $avail_steps) throw new Exception ('You have too few steps...');
        
        // enough steps here
        $this->steps_used += $item->nr_steps;
        $this->update_in_database();
        
        global $database;
        $database->query("INSERT INTO items_done (user_id, coll_id, item_id) VALUES (:user_id, :coll_id, :item_id)");
        $database->bind('user_id', $this->id);
        $database->bind('coll_id', $item->coll_id);
        $database->bind('item_id', $item->id);
        $database->execute();
        
        return $avail_steps - $item->nr_steps;
    }
    
    public function get_rewards() {
        global $database;
        
        $rewards = array();
        $database->query("SELECT reward FROM items WHERE EXISTS (SELECT * FROM items_done WHERE item_id=items.id AND items.coll_id=items_done.coll_id AND user_id=:user_id)");
        $database->bind('user_id', $this->id);
        $results = $database->get_results();
        
        foreach($results as $result){
            array_push($rewards, $result['reward']);
        }
        
        return $rewards;
    }
}
