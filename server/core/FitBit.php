<?php

class FitBit {
    
    static $base_url = 'https://api.fitbit.com/1/';

    var $user_id;
    var $access_token;
    
    public function __construct() {
        
    }
    
    public function init($user_id, $access_token){
        $this->user_id = $user_id;
        $this->access_token = $access_token;
    }
    
    public function get_data($params) {
        if(!$this->user_id || !$this->access_token) throw new Exception ('FitBit not initialized.');
        
        $params = str_replace("[user-id]", $this->user_id, $params);

        $url = FitBit::$base_url . $params;
        $options = array(
          'http'=>array(
            'method'=>"GET",
            'header' => "Authorization: Bearer " . $this->access_token                 
          )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return $response;
    }
}
