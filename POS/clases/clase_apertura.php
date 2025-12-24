<?php

/*
  DESARROLLADO POR: Darwin Mora
  DESCRIPCION: Clase para web service de interface de producto
  TABLAS INVOLUCRADAS:Detalle_Factura, Cabecera_Factura, Plus
  FECHA CREACION: 23/05/2016
 */

class apertura extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) /**/ {
        switch ($lc_opcion) {

            case 'validaperiodoAbierto':
                $lc_query = "EXECUTE seguridad.USP_validaperiodoabierto $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['Estado'] = $row["Estado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }

                break;
            case 'traerLogoCadena':
                $lc_query = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cdn_logotipo'] = $row["cdn_logotipo"];
                        $this->lc_regs['cdn_id'] = (int) $row["cdn_id"];
                        $this->lc_regs['rst_id'] = (int) $row["rst_id"];
                        $this->lc_regs['rst_tipo_servicio'] = (int) $row["rst_tipo_servicio"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }

                break;

            case 'validaAccesoPerfil':
                $lc_query = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['accesoperfil'] = (int) $row["accesoperfil"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }

                break;

            case 'traerUsuario':
                $lc_query = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row["usr_id"];
                        $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            
            
            case 'ActualizarPrecio':
                $lc_query = "EXECUTE config.PLUS_ActualizaPrecio '$lc_datos[0]',  '$lc_datos[1]' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['estado'] = $row["estado"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
            
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
        }
    }

    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'grabaperiodo':
                $lc_query = "EXECUTE seguridad.IAE_grabaperiodo '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
            break;
        }
    }

    public function fn_fechaPeriodoSecuencial($accion, $ip_estacion) {
        $lc_sql = "[seguridad].[USP_AdministracionPeriodos] $accion, '$ip_estacion'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("fecha_periodo_secuencial" => utf8_encode($row["fecha_periodo_secuencial"]));
        } catch (Exception $e) {
            return $e;
        }

        return $this->lc_regs;
    }
}
?>
	
