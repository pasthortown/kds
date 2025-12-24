<?php
class DragontailCleanRiders {
    private $employees;
    private $fullLoad;
    private $storeNo;
    private $time;

    function __construct($data) {
        $this->fullLoad  = $data['fullLoad'];
        $this->employees = $data['employees'];
        $this->storeNo   = $data['storeNo'];
        $this->time      = $data['time'];
    }

    static function from($storeNo) {
        $data = [];
        $data['fullLoad']  = true;
        $data['employees'] = null;
        $data['storeNo']   = (int)$storeNo;
        $data['time']      = self::getTime();

        $new = new static($data);
        return $new;
    }

    static function getTime() {
        return date('Y-m-d H:i:s');
    }

    public function toJson() {
        return get_object_vars($this);
    }
}
