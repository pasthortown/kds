<?php

/*
FECHA CREACION   : 04/10/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

class ConsultaSoporte extends sql {
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }   
        
    function fn_cargarRestaurantes($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDRestaurante" => $row['IDRestaurante'],
                                         "Descripcion" => utf8_encode($row['Descripcion']));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarDetalle($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDPeriodo" => $row['IDPeriodo'],
                                         "FechaApertura" => $row['FechaApertura'],
                                         "FechaCierre" => $row['FechaCierre'],
                                         "CerradoPor" => utf8_encode($row['CerradoPor']),
                                         "Estado" => $row['Estado']);
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarDetalleDesmontado($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDCanalMovimiento" => $row['IDCanalMovimiento'],
                                         "Fecha" => $row['Fecha'],                    
                                         "Cajero" => utf8_encode($row['Cajero']),
                                         "Estacion" => $row['Estacion'],
                                         "Estado" => $row['Estado'],
                                         "IPEstacion" => $row['IPEstacion'],
                                         "IDUsuario" => $row['IDUsuario'],
                                         "IDControlEstacion" => $row['IDControlEstacion']);
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarDetalleFindeDia($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDCanalMovimiento" => $row['IDCanalMovimiento'],
                                         "FechaImpresion" => $row['FechaImpresion'],
                                         "FechaPeriodo" => $row['FechaPeriodo'],
                                         "Administrador" => utf8_encode($row['Administrador']),                                         
                                         "Estado" => $row['Estado'],
                                         "IDPeriodo" => $row['IDPeriodo'],
                                         "IPEstacion" => $row['IPEstacion'],
                                         "IDUsuario" => $row['IDUsuario']);
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_imprimirReporte($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_IAE_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", '".$lc_datos[2]."', '".$lc_datos[3]."', ".$lc_datos[4].", '".$lc_datos[5]."', '".$lc_datos[6]."', '".$lc_datos[7]."', '".$lc_datos[8]."', '".$lc_datos[9]."'";
        
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    function fn_infoAplicaApiImpresionCrearReporte($lc_datos){
        $lc_sql = "EXEC [impresion].[Aplica_RestauranteActivo_CrearReporte] '".$lc_datos[0]."', ".$lc_datos[1]."";
        
        if($this->fn_ejecutarquery($lc_sql)){
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs['aplicaTienda'] = $row['aplicaTienda'];
                $this->lc_regs['servicioImpresion'] = isset($row['servicioImpresion'])? $row['servicioImpresion'] : 0;
                if(isset($row['servicioImpresion'])){
                    $servicioApiImpresion = json_decode($row['servicioImpresion']);
   
                    $this->lc_regs['asignacion_retiro_fondo'] = $servicioApiImpresion->asignacion_retiro_fondo;
                    
                }else{ 
                    $this->lc_regs['asignacion_retiro_fondo'] = 0;
                }
                $this->lc_regs['estacion'] = isset($row['estacion'])? $row['estacion'] : '';
                $this->lc_regs['idcadena'] = isset($row['idcadena'])? $row['idcadena'] : '';
                $this->lc_regs['IDEstacion'] = isset($row['IDEstacion'])? $row['IDEstacion'] : '';
            }
        }

        return json_encode($this->lc_regs);
    }
    
    function fn_cargarSeleccionPeriodo($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDPeriodo" => $row['IDPeriodo'],
                                         "Periodo" => utf8_encode($row['Periodo']));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarSeleccionCajero($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDControlEstacion" => $row['IDControlEstacion'],
                                         "IDUsersPos" => $row['IDUsersPos'],
                                         "Datos" => utf8_encode($row['Datos']));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarSeleccionAdmin($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDUsersPos" => $row['IDUsersPos'],
                                         "Administrador" => utf8_encode($row['Administrador']));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_cargarSeleccionEstacion($lc_datos){
        $lc_sql = "EXEC [config].[CONSULTASOPORTE_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDEstacion" => $row['IDEstacion'],
                                         "Impresora" => utf8_encode($row['Impresora']));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }


       
    // //////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////////

    function fn_cargaFacturas($lc_datos){
        $lc_sql = "EXEC [config].[USP_Facturas_NotasCredito_OrdenesPedido_Reimpresion] $lc_datos[0] ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("factura" => $row['factura'],
                                         "numeroFactura" => $row['numeroFactura'],
                                         "total" => $row['total'],
                                         "cliente" => $row['cliente'],                                         
                                         "fecha" => $row['fecha'],
                                         "estacion" => $row['estacion'],
                                         "medio" => $row['medio'],
                                         //"estado_impresion" => $row['estado_impresion'],
                                         "idcanalmovimiento" => $row['idcanalmovimiento'],
                                         "reimpresion" => $row['reimpresion'],
                                         "imprimeMedio" => $row['imprimeMedio'],
            );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }

    function fn_cargaNotasCredito($lc_datos){
        $lc_sql = "EXEC [config].[USP_Facturas_NotasCredito_OrdenesPedido_Reimpresion] $lc_datos[0] ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("factura" => $row['factura'],
                                         "numeroFactura" => $row['numeroFactura'],
                                         "cfac_id" => $row['cfac_id'],
                                         "total" => $row['total'],
                                         "cliente" => $row['cliente'],                                         
                                         "fecha" => $row['fecha'],
                                         "estacion" => $row['estacion'],
                                         "medio" => $row['medio'],
                                         //"estado_impresion" => $row['estado_impresion'],
                                         "idcanalmovimiento" => $row['idcanalmovimiento'],
                                         "reimpresion" => $row['reimpresion'],
                                         "imprimeMedio" => $row['imprimeMedio'],
            );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }


    function fn_cargaOrdenesPedido($lc_datos){
        $lc_sql = "EXEC [config].[USP_Facturas_NotasCredito_OrdenesPedido_Reimpresion] 3 ";
        try {
            //code...
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) {
                    $result[] = array("IDCabeceraOrdenPedido1" => utf8_encode($row['IDCabeceraOrdenPedido1']),
                                             "fecha" => utf8_encode($row['fecha']),
                                             "numerofactura" => utf8_encode($row['numerofactura']),
                                             "total" => $row['total'],                                         
                                             "cliente" => utf8_encode($row['cliente']),
                                             "medio" => utf8_encode($row['medio']),
                                             "idcanalmovimiento" => utf8_encode($row['idcanalmovimiento']),
                                             "reimpresion" => utf8_encode($row['reimpresion']),
                                             "imprimeMedio" => utf8_encode($row['imprimeMedio']),
                                             "numeroOrden" => utf8_encode($row['numeroOrden']),
                );
                }
            }
            $result["str"] = $this->fn_numregistro();

        } catch (Exception $e) {
            //throw $th;
            return json_encode($e);

            
        }

        
       // $this->lc_regs["str"] = $this->fn_numregistro();
        //echo($lc_sql);
        return json_encode($result);  
    }



    // function fn_cargaOrdenesPedido($lc_datos){
    //     $lc_sql = "EXEC [config].[USP_Facturas_NotasCredito_OrdenesPedido_Reimpresion] $lc_datos[0] ";
    //     // if ($this->fn_ejecutarquery($lc_sql)) {
    //     //     while ($row = $this->fn_leerarreglo()) {
    //     //         $this->lc_regs[] = array("IDCabeceraOrdenPedido1" => $row['IDCabeceraOrdenPedido1'],
    //     //                                  "fecha" => $row['fecha'],
    //     //                                  "numerofactura" => $row['numerofactura'],
    //     //                                  "total" => $row['total'],                                         
    //     //                                  "cliente" => $row['cliente'],
    //     //                                  "medio" => $row['medio'],
    //     //                                  "idcanalmovimiento" => $row['idcanalmovimiento'],
    //     //                                  "reimpresion" => $row['reimpresion'],
    //     //                                  "imprimeMedio" => $row['imprimeMedio'],
    //     //                                  "numeroOrden" => $row['numeroOrden'],
    //     //     );
    //     //     }
    //     // }
    //     var_dump($lc_sql);
    //     $this->lc_regs["str"] = $this->fn_numregistro();
    //     return json_encode($this->lc_regs);  
        
    // }


    function fn_reImprimirDocumento($lc_datos){
        $lc_sql = "EXEC [config].[IAE_Reimpresion_documentos] ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }



}





