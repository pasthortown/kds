<?php

class DTD_Sales_Summary {
    private $SalesSummaryDTD = [
        'Date'=>'',
        'SalesTotal'=> '0.00',
        'Currency'=>'',        
    ];//en este DTD se ignora ChainName y SiteName porque el dispositivo los toma de la ip(son opcionales)
   

    public function validarSalesSummaryDTD($datos)
    {
        $DTD = $this->SalesSummaryDTD;
        $longitudDatos = count($datos, 1);
        $cantidadElementos = 0;

        foreach ($datos as $key => $value) {
            if (array_key_exists($key, $DTD) && gettype($value) === gettype($DTD[$key])) {
                $cantidadElementos++;
            }
        }
        if ($cantidadElementos === $longitudDatos) {
            return true;
        }
        return false;
    }
}
?>