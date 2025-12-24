<?php
class BillException extends Exception {
  protected $BillID;

  public function __construct($message,$code,$BillID) {
      $this->BillID = $BillID;
      parent::__construct($message,$code);
      http_response_code($code);
  }

  public function __toString() {
    return json_encode(
            [
                "mensaje" => $this->getMessage(),
                "idFactura" => $this->BillID,
                "codigo" => $this->getCode()
            ]
        );

    return $this->getMessage() . $this->BillID;
  }
}