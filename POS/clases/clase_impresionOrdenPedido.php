<?php
///////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco /////////////////////////////////////////////
///////DESCRIPCION: Impresion orden pedido ////////////////////////////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, //////////////////////////////////////
/////// Menu_Agrupacionproducto ///////////////////////////////////////////////////
/////// Detalle_Orden_Pedido //////////////////////////////////////////////////////
/////// Plus, Precio_Plu, Mesas ///////////////////////////////////////////////////
///////FECHA CREACION: 11-05-2015 /////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

class impresion_pedido extends sql{
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
                if ($result){ return $result; }else{ return false; };
            break;
            case 'Detalle_Orden_Pedido':
                $lc_query="EXEC pedido.ORD_impresion_detalle_ordenpedido '".$lc_datos[0]."'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return $result; }else{ return false; };
            break;                    
        }
    }
    
    function fn_impresionDinamicaOrdenPedido($lc_datos){
        $lc_query = "EXEC pedido.ORD_impresion_dinamica_ordenpedido '".$lc_datos[0]."', ".$lc_datos[1].", '".$lc_datos[2]."', ".$lc_datos[3].", ".$lc_datos[4]." ";
        return $this->fn_ejecutarquery($lc_query);
    }
    function fn_impresionPreOrdenImpresionSonido($codigo_app){
        return $this->fn_consulta_generica_escalar("EXEC [pedido].[USP_impresionPreOrdenPedidoImpresoraSonido] '%s'", $codigo_app);
    }
}

?>