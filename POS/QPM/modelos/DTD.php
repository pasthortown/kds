<?php

class DTD {
    private $ProductEventDTD = [
        'EventID' => '',
        'DeviceID' => '',
        'ServerID' => '',
        'Date' => '',
        'Time' => '',
        'ProductEventType' => '',
        'PosSystemIdentifier' => 1,
        'Stage' => '',
        'NonDepleting' => true,
        'SubTotal' => '0.00',
        'Tax' => '0.00',
        'Total' => '0.00',
        'Currency' => ''
    ];
    private $Product = [
        'ID' => '',
        'Name' => '',
        'Qty' => 0,
        'UnitCost' => '',
        'ItemTotal' => '',
    ];

    public function validarProductEventDTD($datos)
    {
        $DTD = $this->ProductEventDTD;
        $DTDProducto = $this->Product;
        $longitudDatos = count($datos, 1);
        $cantidadElementos = 0;


        foreach ($datos as $key => $value) {
            if (array_key_exists($key, $DTD) && gettype($value) === gettype($DTD[$key])) {
                $cantidadElementos++;
            }
            if (gettype($key) === "integer") {
                $cantidadElementos++; //cada item que posee un array                
                foreach ($datos[$key] as $keyProducto => $valueProducto) {
                    if (gettype($valueProducto) === gettype($DTDProducto[$keyProducto])) {
                        $cantidadElementos++;
                    }
                };
            }
        }
        if ($cantidadElementos === $longitudDatos) {
            return true;
        }
        return false;
    }
}
?>