<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez///////////////////////////////////////////////////////////////
///////////DESCRIPCION: Clase que maneja la pantalla de la cadena///////////////////////////////////////
////////////////TABLAS: Cadena ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 25-05-2016//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

class adminCadena extends sql {

    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargaCadena":
                $lc_sql = "EXECUTE [config].[USP_CadenaColecciondeDatos]  $lc_datos[0],$lc_datos[1],'',''";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionCadena" => utf8_encode($row["ID_ColeccionCadena"]),
                            "ID_ColeccionDeDatosCadena" => utf8_encode($row["ID_ColeccionDeDatosCadena"]),
                            "descripcion_coleccion" => utf8_encode($row["descripcion_coleccion"]),
                            "descripcion_dato" => utf8_encode($row["descripcion_dato"]),
                            "especificarValor" => intval($row["especificarValor"]),
                            "obligatorio" => intval($row["obligatorio"]),
                            "tipodedato" => $row["tipodedato"],
                            "caracter" => utf8_encode($row["caracter"]),
                            "entero" => $row["entero"],
                            "fecha" => $row["fecha"],
                            "seleccion" => $row["seleccion"],
                            "numerico" => $row["numerico"],
                            "fechaIni" => $row["fechaIni"],
                            "fechaFin" => $row["fechaFin"],
                            "min" => $row["min"],
                            "max" => $row["max"],
                            "isActive" => intval($row["isActive"]));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaColeccionDeDatos":
                $lc_sql = "EXECUTE [config].[USP_CadenaColecciondeDatos]  $lc_datos[0],$lc_datos[1],'$lc_datos[2]',''";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionCadena" => $row["ID_ColeccionCadena"],
                            "Descripcion" => utf8_encode($row["Descripcion"]));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaColeccionDatosC":
                $lc_sql = "EXECUTE [config].[USP_CadenaColecciondeDatos] $lc_datos[0],$lc_datos[1],'$lc_datos[2]',''";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionCadena" => $row["ID_ColeccionCadena"],
                            "ID_ColeccionDeDatosCadena" => utf8_encode($row["ID_ColeccionDeDatosCadena"]),
                            "Descripcion" => utf8_encode($row["Descripcion"]),
                            "dato" => utf8_encode($row["dato"]),
                            "especificarValor" => utf8_encode($row["especificarValor"]),
                            "obligatorio" => utf8_encode($row["obligatorio"]),
                            "tipodedato" => utf8_encode($row["tipodedato"]));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "grabaCadenaColeccionDatos":
                $lc_sql = "EXECUTE [config].[IAE_CadenaColecciondeDatos] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors() ) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);

            case "actualizaCadenaColeccionDatos":
                $lc_sql = "EXECUTE [config].[IAE_CadenaColecciondeDatos]  '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors() ) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);

            case "editarColeccionDeDatos":
                $lc_sql = "EXECUTE [config].[USP_CadenaColecciondeDatos]  $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs["ID_ColeccionCadena"] = utf8_encode($row["ID_ColeccionCadena"]);
                        $this->lc_regs["ID_ColeccionDeDatosCadena"] = utf8_encode($row["ID_ColeccionDeDatosCadena"]);
                        $this->lc_regs["Descripcion"] = utf8_encode($row["Descripcion"]);
                        $this->lc_regs["dato"] = utf8_encode($row["dato"]);
                        $this->lc_regs["obligatorio"] = $row["obligatorio"];
                        $this->lc_regs["especificarValor"] = $row["especificarValor"];
                        $this->lc_regs["entero"] = $row["entero"];
                        $this->lc_regs["tipodedato"] = utf8_encode($row["tipodedato"]);
                        $this->lc_regs["caracter"] = utf8_encode($row["caracter"]);
                        $this->lc_regs["entero"] = $row["entero"];
                        $this->lc_regs["fecha"] = utf8_encode($row["fecha"]);
                        $this->lc_regs["seleccion"] = $row["seleccion"];
                        $this->lc_regs["fechaFin"] = utf8_encode($row["fechaFin"]);
                        $this->lc_regs["numerico"] = $row["numerico"];
                        $this->lc_regs["fechaInicio"] = utf8_encode($row["fechaInicio"]);
                        $this->lc_regs["minimo"] = $row["minimo"];
                        $this->lc_regs["maximo"] = $row["maximo"];
                        $this->lc_regs["activo"] = $row["activo"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function sincronizarCadenas($cadenas) {
        foreach ($cadenas as $cadena) {
            $lc_sql = "EXECUTE config.CADENA_I_cadena " . $cadena->codCadena . ", " . $cadena->codempresa . ", '" . utf8_decode($cadena->descripcion) . "', '" . $cadena->logo . "', '" . $cadena->tipoImpuesto . "', '" . utf8_decode($cadena->nombreComercial) . "'";
            $this->fn_ejecutarquery($lc_sql);
        }
        return json_encode("Realizado");
    }

    function fn_consultaColeccionDeDatosTrasferenciaVentas($lc_datos) {
        $lc_sql = "EXECUTE [config].[CADENAS_USP_transferenciaVentas]  '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionCadena" => utf8_encode($row["ID_ColeccionCadena"]),
                    "ID_ColeccionDeDatosCadena" => utf8_encode($row["ID_ColeccionDeDatosCadena"]),
                    "descripcion_coleccion" => utf8_encode($row["descripcion_coleccion"]),
                    "origen" => utf8_encode($row["origen"]),
                    "destino" => utf8_encode($row["destino"]),
                    "isActive" => utf8_encode($row["isActive"]));
            }
        }
        $this->fn_ejecutarquery($lc_sql);
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_consultaCadenas($lc_datos) {
        $lc_sql = "EXECUTE [config].[CADENAS_USP_transferenciaVentas]  '$lc_datos[0]', $lc_datos[1], $lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("cdn_id" => utf8_encode($row["cdn_id"]),
                    "cdn_descripcion" => utf8_encode($row["cdn_descripcion"]));
            }
        }
        $this->fn_ejecutarquery($lc_sql);
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_grabaTransferenciaVentas($lc_datos) {
        $lc_sql = "EXECUTE [config].[CADENAS_IAE_transferenciaVentas]  '$lc_datos[0]', $lc_datos[1], $lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]',$lc_datos[6]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("respuesta" => utf8_encode($row["respuesta"]),
                    "mensaje" => utf8_encode($row["mensaje"]));
                $this->lc_regs['str'] = $this->fn_numregistro();
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }
        // $this->fn_ejecutarquery($lc_sql);
        //   $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_inactivarTransferencia($lc_datos) {
        $lc_sql = "EXECUTE [config].[CADENAS_IAE_transferenciaVentas]  '$lc_datos[0]', $lc_datos[1], $lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]',$lc_datos[6]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("respuesta" => utf8_encode($row["respuesta"]),
                    "mensaje" => utf8_encode($row["mensaje"]));
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }
        // $this->fn_ejecutarquery($lc_sql);
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_guardarClienteExternoVoucher($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_creacion_politica_voucher]  $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]',$lc_datos[5],'$lc_datos[6]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "mensaje" => utf8_encode($row["mensaje"]));
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_traerListaVoucherAerolineas($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_creacion_politica_voucher]  $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]',$lc_datos[5],'$lc_datos[6]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "ID_ColeccionCadena" => utf8_encode($row["ID_ColeccionCadena"]),
                    "ID_ColeccionDeDatosCadena" => utf8_encode($row["ID_ColeccionDeDatosCadena"]),
                    "Datos" => utf8_encode($row["datos"]),
                    "isActive" => $row["isActive"]
                );
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_buscarMontoVoucher($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_Monto_Voucher_Aerolineas]  $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "valor" => utf8_encode($row["valor"])
                );
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_guardarMontoVoucher($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_Monto_Voucher_Aerolineas]  $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "mensaje" => utf8_encode($row["mensaje"])
                );
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_actualizarStatusCliente($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_Monto_Voucher_Aerolineas]  $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]',$lc_datos[6]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "mensaje" => utf8_encode($row["mensaje"])
                );
            }
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

}
