<?php


class impresion_cashless extends sql{
    //Constructor de la Clase
    function _construct(){
            parent ::_construct();
    }
	
    //Función que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos){
        switch($lc_sqlQuery){
            case 'Cabecera_Orden_Pedido':
                $lc_query="EXEC pedido.ORD_impresion_cabecera_ordenpedido '".$lc_datos[0]."'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
            break;
            case 'Detalle_Orden_Pedido':
                $lc_query="EXEC pedido.ORD_impresion_detalle_ordenpedido '".$lc_datos[0]."'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
            break;                    
        }
    }
    
    function fn_impresionDinamicaOrdenPedidoCashless($lc_datos){
        $lc_query = "EXEC pedido.ORD_impresion_dinamica_cashless_ordenpedido'".$lc_datos[0]."', '".$lc_datos[1]."','".$lc_datos[2]."'";
        
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }
}

?>