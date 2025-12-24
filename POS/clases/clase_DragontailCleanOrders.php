<?php
class DragontailCleanOrders {
    private $fullLoad;
    private $orderItems;
    private $orders;
    private $storeNo;
    private $time;
    function __construct($data) {
         $this->fullLoad=$data['fullLoad'];
         $this->orderItems=$data['orderItems'];
         $this->orders=$data['orders'];
         $this->storeNo=$data['storeNo'];
         $this->time=$data['time'];
    }

    static function from($storeNo) {
        $data = [];
        $data['fullLoad']   = true;
        $data['orderItems'] = null;
        $data['orders']     = null;
        $data['storeNo']    = (int)$storeNo;
        $data['time']       = self::getTime();

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
