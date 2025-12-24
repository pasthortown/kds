<?php
class DragonTailOrderRequest {
    private $storeNo;
    private $fullLoad;
    private $orders;
    private $orderItems;
    private $time;

    function __construct($data) {
        $this->storeNo    = $data['storeNo'];
        $this->fullLoad   = $data['fullLoad'];
        $this->orders     = $data['orders'];
        $this->orderItems = $data['orderItems'];
        $this->time       = $data['time'];
    }

    public static function from($time, $restaurantId, $orders, $orderItems) {
        $data = [
            'time'       => $time,
            'storeNo'    => intval($restaurantId),
            'fullLoad'   => false,
            'orders'     => $orders,
            'orderItems' => $orderItems,
        ];
        $new = new static($data);
        return $new;
    }

    public function orderByPosition() {
        $columns = array_column($this->orderItems, 'position');
         array_multisort($columns, SORT_ASC, $this->orderItems);
    }

    public function toJson() {
        return [
            "time"=>$this->time,
            "storeNo"=>$this->storeNo,
            "fullLoad"=>$this->fullLoad,
            "orders"=>$this->orders,
            "orderItems"=>$this->orderItems,
        ];
    }
}
