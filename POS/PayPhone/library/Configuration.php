<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConfigurationManager{
    
    private static $instance;
    
    private function __construct() {}
    
    public static function Instance(){
        if (!isset(self::$instance)){
            $myClass = __CLASS__;
            self::$instance = new $myClass;
        }
        return self::$instance;
    }
    
    public function __clone() {
        trigger_error("La clonacion de este objeto no esta permitida");
    }
    
    public $PrivateKey = null;
    
    public $ApplicationPublicKey = null;
    
    public $Token = null;
    
    public $ApplicationId = null;
    
    public $RefreshToken = null;
    
    public $ClientId = null;
    
    public $ClientSecret = null;
    
    public $ApiPath = null;
    
}