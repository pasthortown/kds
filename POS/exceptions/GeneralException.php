<?php
class GeneralException extends Exception {

  protected $_defaultCode = 404;
    public function errorMessage() {
        http_response_code(400);
        return json_encode(["status"=>"error", "message"=>$this->getMessage()]);
      }

    public function customMessage($messge,$code=400) {
        http_response_code($code);
        return json_encode(["status"=>"error", "message"=>$messge]);
      }
    
 }
