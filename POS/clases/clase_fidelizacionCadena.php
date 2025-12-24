<?php

class Cadena extends sql {

    function cargarConfiguracionPoliticas($idCadena) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 2, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("formatoRide" => html_entity_decode(utf8_encode($row['formatoRide']))
                , "formatoVoucher" => html_entity_decode(utf8_encode($row['formatoVoucher']))
                , "nombrePlan" => utf8_encode($row['nombrePlan'])
                , "bienvenida" => $row['bienvenida']
                , "tituloVoucher" => utf8_encode($row['tituloVoucher'])
                , "urlWeb" => utf8_encode($row['urlWeb'])
                , "despedida" => utf8_encode($row['despedida'])
                , "app" => utf8_encode($row['app'])
                , "seguridad" => $row['seguridad']
                , "tituloRide" => $row['tituloRide']
                , "autoconsumoRuc" => utf8_encode($row['autoconsumoRuc'])
                , "autoconsumoRazonSocial" => utf8_encode($row['autoconsumoRazonSocial'])
                , "preguntaRegistro" => utf8_encode($row['preguntaRegistro'])
                , "interfaceRuc" => utf8_encode($row['interfaceRuc'])
                , "intefaceRazonSocial" => utf8_encode($row['intefaceRazonSocial'])
                , "ContrasenaWebServicesFidelizacion" => utf8_encode($row['ContrasenaWebServicesFidelizacion'])
                );
            } else {
                $this->lc_regs = array("formatoRide" => ""
                , "formatoVoucher" => ""
                , "nombrePlan" => ""
                , "bienvenida" => ""
                , "tituloVoucher" => ""
                , "urlWeb" => ""
                , "despedida" => ""
                , "app" => ""
                , "seguridad" => ""
                , "tituloRide" => ""
                , "autoconsumoRuc" => ""
                , "autoconsumoRazonSocial" => ""
                , "preguntaRegistro" => ""
                , "interfaceRuc" => ""
                , "intefaceRazonSocial" => ""
                , "ContrasenaWebServicesFidelizacion" => ""
                );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cargarConfiguracionPoliticasToken( $idCadena, $app) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 6, " . $idCadena .", 0, '" . $app . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array(
                    "client_id" => html_entity_decode(utf8_encode($row['client_id'])),
                    "client_secret" => html_entity_decode(utf8_encode($row['client_secret'])),
                    "grant_type" => utf8_encode($row['grant_type']),
                    "key_client_jwt" => utf8_encode($row['key_client_jwt'])
                );
            } else {
                $this->lc_regs = array(
                    "client_id" => null,
                    "client_secret" => null,
                    "grant_type" => null,
                    "key_client_jwt" => null

                );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function guardarConfiguracionPoliticas($idUsuario, $idCadena, $parametro, $valor) {
        $lc_sql = "EXEC fidelizacion.IAE_Cadena '$idUsuario', $idCadena, '$parametro', '$valor';";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("mensaje" => utf8_encode($row['mensaje']), "estado" => $row['estado']);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function guardarConfiguracionPoliticaAplicaCadenaObjeto($idCadena) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 3, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("aplicaConfiguracion" => $row['aplicaConfiguracion']
            , "claveSeguridad" => $row['claveSeguridad']);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function guardarConfiguracionPoliticaAplicaCadenaObjetoPuntosRide($idCadena, $idRestaurante) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 4, " . $idCadena . ", $idRestaurante";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("imprimePuntosRide" => $row['imprimePuntosRide']);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function desactivarPlanFidelizacion($idCadena, $idUsuario) {
        $lc_sql = "EXEC fidelizacion.desactivarPlanFidelizacion " . $idCadena . ", '" . $idUsuario . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("confirmado" => 1, "mensaje" => "El módulo de fidelización ha sido desactivado correctamente.");
            $result = $this->lc_regs;
        } catch (Exception $e) {
            $this->lc_regs = array("confirmado" => 0, "mensaje" => $e->getMessage());
        }
        return json_encode($result);
    }

    function activarPlanFidelizacion($idCadena, $idUsuario) {
        $lc_sql = "EXEC fidelizacion.activarPlanFidelizacion " . $idCadena . ", '" . $idUsuario . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("confirmado" => 1, "mensaje" => "El módulo de fidelización ha sido activado correctamente.");
            $result = $this->lc_regs;
        } catch (Exception $e) {
            $this->lc_regs = array("confirmado" => 0, "mensaje" => $e->getMessage());
        }
        return json_encode($result);
    }

    function cargarConfiguracionFormaPago($idRestaurante) {
        $lc_sql = "EXEC fidelizacion.cargarFormaPago  " . $idRestaurante;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDFormapago" => $row['IDFormapago']
                , "codigo" => $row['codigo']
                , "descripcion" => $row['descripcion']
                , "formaPago" => $row['formaPago']
                , "aplica" => $row['aplica']
                , "aplicaRestriccion" => $row['aplicaRestriccion']
                );
            }
            if ($this->fn_numregistro() > 0) {
                $result['data'] = $this->lc_regs;
            } else {
                $result['data'] = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function guardarConfiguracionFormaPago($idFormaPago, $idUsuario, $idCadena, $estado) {
        $lc_sql = "EXEC fidelizacion.politica_formapagoNOAplicantes  '$idFormaPago', '$idUsuario' ,$idCadena , $estado ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("mensaje" => $row['mensaje']
                );
            }
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cargarTokenSeguridad($idCadena) {
        $lc_sql = "EXEC fidelizacion.SEGURIDAD_consultarToken " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("claveSeguridad" => $row['claveSeguridad']);
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }


}
