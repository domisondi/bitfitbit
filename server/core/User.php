<?php

class User {
    
    var $id;
    var $name;
    var $bits;
    
    private $access_token;
    
    public function __construct($id, $data = null) {
        $this->id = $id;
        
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
            
            $database->query("INSERT INTO users (id, name, bits) VALUES (:id, :name, :bits)");
            $database->bind('id', $id);
            $database->bind('name', $this->name);
            $database->bind('bits', $this->bits);
            $database->execute();
        }
    }
    
    public function update_in_database() {
        global $database;
        $database->query("UPDATE users SET name=:name, bits=:bits WHERE id=:id");
        $database->bind('id', $this->id);
        $database->bind('name', $this->name);
        $database->bind('bits', $this->bits);
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
        global $fitbit;
        $fitbit->init($this->id, $this->get_access_token());
        $data = $fitbit->get_data('user/[user-id]/activities/date/2016-09-17.json');
        
        return $data;
    }
}
