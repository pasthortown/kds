<?php

class Clase_dragonTailOrders  {
    private $storeNo;
    private $orderId;
    private $clientId;
    private $dailyNo;
    private $saleType;
    private $lat;
    private $lng;
    private $firstName;
    private $street;
    private $addressNo;
    private $postCode;
    private $phone;
    private $orderTotal;
    private $paymentMethod;
    private $isTimedOrder;
    private $email;
    private $source;
    private $isNotPaid;
    private $orderTime;
    private $carrierInstructions;
    private $cookInstructions;
    private $cash;
    private $city;
    private $action;

    function __construct($data) {
        $this->orderTime=$data['orderTime'];
        $this->storeNo=$data['storeNo'];
        $this->orderId=$data['orderId'];
        $this->dailyNo=$data['dailyNo'];
        $this->saleType=$data['saleType'];
        $this->lat=$data['lat'];
        $this->lng=$data['lng'];
        $this->firstName=$data['firstName'];
        $this->street=$data['street'];
        $this->addressNo=$data['addressNo'];
        $this->postCode=$data['postCode'];
        $this->phone=$data['phone'];
        $this->orderTotal=$data['orderTotal'];
        $this->paymentMethod=$data['paymentMethod'];
        $this->isTimedOrder=$data['isTimedOrder'];
        $this->email=$data['email'];
        $this->source=$data['source'];
        $this->isNotPaid=$data['isNotPaid'];
        $this->carrierInstructions=$data['carrierInstructions'];
        $this->cookInstructions=$data['cookInstructions'];
        $this->cash=$data['cash'];
        $this->clientId=$data['clientId'];
        $this->city=$data['city'];
        $this->action=$data['action'];
    }

    public static function from($data, $restaurantId, $cadenaId, $action, $seguimientoPedido, $codApp, $medio) {
        $data = [
            'orderTime'           => self::getOrderTime($data['fecha']),
            'storeNo'             => intval($restaurantId),
            'orderId'             => (float) $seguimientoPedido,
            'dailyNo'             => self::parceNoInvoice($data['cfac_id']),
            'saleType'            => self::getSaleType($cadenaId, $restaurantId, $medio, $codApp),
            'lat'                 => is_string($data['latitud']) ? floatval(trim($data['latitud'], '"')) :
                floatval($data['latitud']),
            'lng'                 => is_string($data['longitud']) ? floatval(trim($data['longitud'], '"')) :
                floatval($data['longitud']),
            'firstName'           => $data['nombres_cliente'],
            'street'              => $data['calle1_domicilio'],
            'addressNo'           => $data['numDireccion_domicilio'],
            'postCode'            => $data['cod_ZipCode'],
            'phone'               => $data['telefono_cliente'],
            'orderTotal'          => $data['total_Factura'],
            'paymentMethod'       => self::getPaymentMethod($data['fmp_descripcion']),
            'cash'                => self::getCash($data['fmp_descripcion'], $data['total_Factura']),
            'isTimedOrder'        => self::getIsTimeOrder(),
            'email'               => $data['email_cliente'],
            'source'              => self::getSource($data['medio'], $restaurantId),
            'isNotPaid'           => self::getIsNotPaid($data['fmp_descripcion']),
            'carrierInstructions' => self::getCarrierInstructions($data['observaciones_domicilio'],
                $data['calle2_domicilio']),
            'cookInstructions'    => $data['observacion_pedido'],
            'clientId'            => (int)$data['identificacion_cliente'],
            'city'                => $data['city'],
            'action'              => $action,
        ];
        $new = new static($data);
        return $new;
    }

    private static function getOrderTime($date) {
        return (new DateTimeImmutable($date))->format('Y-m-d H:i:s');
    }

    private static function getSource($medio, $restaurantId) {
        $source = DragonTailConfig::getAllParamVariableI('DRAGONTAIL SOURCE', strtoupper($medio));
        if (isset($source)) {
            return $source;
        } else {
            throw new GeneralException( 'counld not find source for medio :' . $medio);
        }
    }

    private static function getIsTimeOrder() {
        return 0;
    }

    private static function getPaymentMethod($payment) {
        if ($payment === 'EFECTIVO') {
            $paymentMethod = 0;
        } elseif (strpos($payment, 'TARJETA') === 0) {
            $paymentMethod = 1;
        } else {
            $paymentMethod = 2;
        }
        return $paymentMethod;
    }

    private static function getCash($paymentMethod, $amount) {
        $cash = 0;
        if ($paymentMethod == 'EFECTIVO') {
            $cash = $amount;
        }
        return $cash;
    }

    private static function getIsNotPaid($paymentMethod) {
        $isNotPaid=0;
        if ($paymentMethod == 'EFECTIVO') {
            $isNotPaid=1;
        }
        return $isNotPaid;
    }

    public static function getSaleType($idCadena, $idRestaurante, $medio, $codApp) {
        $medio = strtoupper($medio);
        if ($medio == 'DINEIN') {
            return 3;
        } else {
            $saleType = DragonTailConfig::getRestaurantConfig($idCadena, $idRestaurante, 'LISTA MEDIO ' . $medio,
                'CAMBIO ESTADOS AUTOMATICO', 'variableI');

            if (self::cotizacionUberDirect($idRestaurante, $codApp)) {
                $saleType = 1;
            }

            switch ($saleType) {
                case '1':
                    return 1;
                    break;
                case '2':
                    return 2;
                    break;
                case "not found":
                    return null;
                    break;
                default:
                    return 2;
            }
        }
    }

    private static function getCarrierInstructions($instruccion, $calle2Domicilio) {
        return  $instruccion . ' ' . $calle2Domicilio;
    }

    function getOrderId() {
        return $this->orderId;
    }

    public function toJson() {
        return array(get_object_vars($this));
    }
    function addStyle($data) {
        $items = [];
        foreach($data as $item) {
            $item['style'] = DragonTailConfig::getStyle($item['mainItem']);
            $items[] = $item;
        }
        return $items;
    }
    private static function parceNoInvoice($invoice) {
        $invoice = (explode("F",$invoice));
        return intval($invoice[1]);
    }

    public static function cotizacionUberDirect($idRestaurante, $codigo_app) {
        $sql = new sql();
        $query = "select top 1 cotizacion_id from Cabecera_App where codigo_app = '$codigo_app' and cod_Restaurante = '$idRestaurante' and cotizacion_id is not null";
        try {
            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            if ($sql->fn_numregistro() > 0) {
                return isset($row['cotizacion_id']) ? true : null;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}
