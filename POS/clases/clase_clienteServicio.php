<?php

/*
  DESARROLLADO POR: Darwin Mora
  DESCRIPCION: Clase para el consumo del servicio web
  FECHA CREACION: 10/11/2015
  MODIFICADO POR:
  DESCRIPCION:
  TABLAS:
 */

class cliente_servicio extends sql {    
    public function fn_consultar($lc_opcion, $lc_datos) {
        switch($lc_opcion) {
            case 'CargaParametrosInterfaceGer':
                $lc_sql = "EXECUTE [interface].[USP_InterfaceSistemaGerente] " . $lc_datos[0] . ", " . $lc_datos[1];
                
                if($this->fn_ejecutarquery($lc_sql)) { 
                    while($row = $this->fn_leerarreglo()) {									
                        $this->lc_regs[] = array(
                            "cod_restaurante"       => $row['cod_restaurante']
                            , "lc_fecha"            => $row['lc_fecha']
                            , "CierreCajas"         => $row['CierreCajas']
                            , "DinTrans"            => $row['DinTrans']
                            , "VentaHora"           => $row['VentaHora']
                            , "PlusTrans"           => $row['PlusTrans']
                            , "Depositos"           => $row['Depositos']
                            , "CajaChica"           => $row['CajaChica']
                            , "CxCEmpleado"         => $row['CxCEmpleado']
                            , "ConsumoRecarga"      => $row['ConsumoRecarga']
                            , "CreditoAutoconsumo"  => $row['CreditoAutoconsumo']
                            , "Agregadores"         => $row["Agregadores"]
                        );
                    }
                    return $this->lc_regs;
                }
                break;

            case 'InactivaSesionCajero':
                $lc_sql = "EXECUTE [interface].[USP_InactivaSesionCajero] " . $lc_datos[0];
                $this->fn_ejecutarquery($lc_sql);
                break;
        }
    }

    public function interfaceVenta($id_periodo) {
        $lc_sql = "EXECUTE [interface].[USP_SistemaGerente_VentaPorMedio] '".$id_periodo."'";

        if($this->fn_ejecutarquery($lc_sql)) { 
            while($row = $this->fn_leerarreglo()) {									
                $this->lc_regs[] = array(
                    "id_restaurante"                => $row['id_restaurante']
                    , "fecha_periodo"               => $row['fecha_periodo']
                    , "json_cierre_cajas"           => $row['json_cierre_cajas']
                    , "json_formas_pago"            => $row['json_formas_pago']
                    , "json_venta_por_hora"         => $row['json_venta_por_hora']
                    , "json_plus"                   => $row['json_plus']
                    , "json_depositos"              => $row['json_depositos']
                    , "json_caja_chica"             => $row['json_caja_chica']
                    , "json_cxc_empleado"           => $row['json_cxc_empleado']
                    , "json_credito_autoconsumo"    => $row['json_credito_autoconsumo']
                    , "json_switch_transaccional"   => $row['json_switch_transaccional']
                    , "json_recargas_consumos"      => $row['json_recargas_consumos']
                    , "json_medio_formas_pago"      => $row['json_medio_formas_pago']
                );
            }
            return $this->lc_regs;
        }
    }

    public function metodoInterface($accion, $id_cadena) {
        $lc_sql = "EXECUTE [interface].[USP_Parametros_servicio] $accion, $id_cadena";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("metodo_interface" => $row["metodo_interface"]);
        } catch (Exception $e) {
            return $e;
        }

        return $this->lc_regs;
    }
}