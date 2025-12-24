<?php
class DragontailRiderRequest {

    private $fullLoad;
    private $storeNo;
    private $employees;

    function __construct($data) {
        $this->fullLoad  = $data['fullLoad'];
        $this->storeNo   = $data['storeNo'];
        $this->employees = $data['employees'];
    }

    public static function from($data) {
        $data = [
            'fullLoad'  => false,
            'storeNo'   => $data['storeNo'],
            'employees' => array($data),
        ];
        $new = new static($data);
        return $new;
    }

    public function toJson() {
        return get_object_vars($this);
    }
}
