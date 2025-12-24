<?php
class DragonTailOrderItems {
    private $storeNo;
    private $orderId;
    private $position;
    private $quantity;
    private $itemNo;
    private $description;
    private $side;
    private $kdsList;
    private $style;
    private $action;

    function __construct($data) {
        $this->storeNo=$data['storeNo'];
        $this->orderId=$data['orderId'];
        $this->position=$data['position'];
        $this->quantity=$data['quantity'];
        $this->itemNo=$data['itemNo'];
        $this->description=$data['description'];
        $this->side=$data['side'];
        $this->kdsList=$data['kdsList'];
        $this->style=$data['style'];
        $this->action=$data['action'];

    }

    public static function from($item, $restaurantId, $accion, $seguimientoPedido) {
        $data = [
            'storeNo'     => intval($restaurantId),
            'orderId'     => (float) $seguimientoPedido,
            'position'    => $item['position'],
            'quantity'    => $item['cantidad'],
            'itemNo'      => strval($item['pluId']),
            'description' => $item['plu_descripcion'],
            'side'        => self::getSide($item['mainItem']),
            'kdsList'     => 'Pack-KDS',
            'style'       => self::getStyle($item['mainItem']),
            'action'      => intval($accion),
        ];
        $new = new static($data);
        return $new;
    }

    static function getSide($type) {
        switch ($type) {
            case 'PRINCIPAL':
                return 3;
                break;
            case 'MODIFICADOR':
                return 0;
                break;
            case 'DESCRIPCION':
                return -1;
                break;
        }
    }

    static function getStyle($type) {
        return DragonTailConfig::getStyle($type);
    }

    function setItemNo($itemNo) {
        $this->itemNo = $itemNo;
    }

    function setPosition($position) {
        $this->position = $position;
    }

    public function toJson() {
        return get_object_vars($this);
    }
}
