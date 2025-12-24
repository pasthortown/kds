<?php
class OrderStatusException extends Exception {

  protected $_defaultCode = 404;
    public function errorMessage($status,$code=400) {
        http_response_code($code);
        return json_encode(["success"=>false, "message"=>$this->getMessage(), "status"=>$status]);
      }

    
 }
