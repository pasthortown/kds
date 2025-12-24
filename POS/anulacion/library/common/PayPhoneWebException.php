<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PayPhoneWebException extends Exception{
    // Redefine the exception so message isn't optional
    public function __construct($message, $statusCode, $errorList, $code = 0, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        
        $this->StatusCode = $statusCode;
        $this->ErrorList = $errorList;
    }
    
    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public $StatusCode;
    
    public $ErrorList;
}