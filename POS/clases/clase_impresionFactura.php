<?php

//////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: JOSE FERNANDEZ///////////////////////////////////////////
///////DESCRIPCION	   : Clase de impresion de Factura con promocion//////////////
////////TABLAS		   : CABECERA FACTURA,DETALLE_FACTURA,PLUS,PROMOCIONES////////
//////////////////////////CEDENA,EMPRESA//////////////////////////////////////////
///////FECHA CREACION  : 20/12/2014///////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:21/07/2014////////////////////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez////////////////////////////////////
////////DECRIPCION ULTIMO CAMBIO: E-fact////////////////////////////////////////

class impresion_factura extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'impresion_factura':
//                $nombreSP = "USP_impresiondinamica_factura" . $lc_datos[2];
//                $lc_query = "exec [facturacion].[$nombreSP] '$lc_datos[0]', '$lc_datos[1]'";
                $lc_query = "exec [facturacion].[USP_impresiondinamica_factura] '$lc_datos[0]', '$lc_datos[1]'";
                
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'impresion_notacredito':
//                $nombreSP = "USP_impresiondinamica_NotaCredito" . $lc_datos[2];
//                $lc_query = "exec [facturacion].[$nombreSP] '$lc_datos[0]', '$lc_datos[1]'";
                $lc_query = "exec [facturacion].[USP_impresiondinamica_NotaCredito] '$lc_datos[0]', '$lc_datos[1]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
        }
    }

}