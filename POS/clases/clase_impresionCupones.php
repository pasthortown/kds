<?php

class ImpresionCupones extends sql{

    public function printCouponDetails($cupon,$nombre){
        $query="EXEC [pedido].[ORD_obtener_html_impresion_cupon]  '$cupon', '$nombre'";
        $result=$this->getFromDb($query);
        $result ? $html=$result['DataCupon']: $html="<h1 style='text-align: center;'> Error obteniendo datos de cupones digitales</h1>";
        $html == null ? $html="<h1 style='text-align: center;' > No se pudo encontrar este cupon '$cupon' </h1>":null;
        return $html;
    }

    private function getFromDb($query){
        if($this->fn_ejecutarquery($query)){
            return $this->fn_leerarreglo();
        }
        return false;
    }
}

?>