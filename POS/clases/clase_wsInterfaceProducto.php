<?php

/*
DESARROLLADO POR: Darwin Mora
DESCRIPCION: Clase para web service de interface de producto
TABLAS INVOLUCRADAS:Detalle_Factura, Cabecera_Factura, Plus
FECHA CREACION: 23/05/2016
*/
class ws_interface_producto extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) {
        
        switch($lc_opcion) {					
        case 'interfaceProducto':
            $lc_query="SET DATEFORMAT DMY EXEC [interface].[USP_InterfaceSistemaGerenteProductos] '".$lc_datos[0]."', '".$lc_datos[1]."'";
            
            if ($this->fn_ejecutarquery($lc_query)) {
                while($row = $this->fn_leerarreglo()) {    
                    $this->lc_regs["Respuesta"] = "OK";
                    $this->lc_regs["Mensaje"] = "OK";
                    $this->lc_regs["DatosAdicionales"] = utf8_encode($row["DatosAdicionales"]);
                    $this->lc_regs["PlusTrans"] = utf8_encode($row["PlusTrans"]);
                }
                
                $this->lc_regs["str"] = $this->fn_numregistro();
                return json_encode($this->lc_regs);	
            }
            break;	
        }
    }
}