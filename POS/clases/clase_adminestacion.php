<?php

//////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE ESTACION CON////
/////////////////////// TABLA MODAL //////////////////////////////////////////////
////////////////TABLAS: Estacion,SWT_Tipo_Envio///////////////////////////////////
////////FECHA CREACION: 01/06/2015////////////////////////////////////////////////
///////////////////////////////////////////cargarTipoCobro///////////////////////////////////////

class estacion extends sql {

    public function fn_cargar_Restaurante($lc_datos) {
        $lc_query = "EXECUTE config.USP_restaurantesporcadena $lc_datos[0],$lc_datos[1],$lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "Descripcion" => htmlentities(trim($row['Descripcion'])),
                    "FastFood" => $row['FastFood']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case "cargarmenu":
                $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],'$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("menu_id" => $row['menu_id'],
                            "menu_nombre" => htmlentities(trim($row['menu_nombre'])));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargarTipoCobro":
                $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tpenv_id" => $row['tpenv_id'],
                            "tpenv_descripcion" => htmlentities(trim($row['tpenv_descripcion'])));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargarDetalleInactivos":
                $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("est_id" => $row['est_id'],
                            "est_ip" => $row['est_ip'],
                            "est_nombre" => $row['est_nombre'],
                            "Estado" => $row['Estado'],
                            "menu_Nombre" => utf8_encode($row['menu_Nombre']),
                            "menu_id" => $row['menu_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargarNumeroNombreCaja":
                $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs['MaximaCaja'] = $row["MaximaCaja"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargarestacionModifica":
                $lc_query = "EXEC config.USP_datosinsertamodificaestacion " . $lc_datos[2] . ", " . $lc_datos[3] . ", '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Restaurante" => $row["Restaurante"],
                            "primer_octeto_ip" => $row["primer_octeto_ip"],
                            "segundo_octeto_ip" => $row["segundo_octeto_ip"],
                            "tercer_octeto_ip" => $row["tercer_octeto_ip"],
                            "cuarto_octeto_ip" => $row["cuarto_octeto_ip"],
                            "numero_estacion" => $row["numero_estacion"],
                            "menu_id" => $row["menu_id"],
                            "tid" => utf8_encode($row["tid"]),
                            "tpenv_id" => $row["tpenv_id"],
                            "std_id" => $row["std_id"],
                            "est_punto_emision" => $row["est_punto_emision"]);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarselmenu":
                $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],'$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("menu_id" => $row['menu_id'],
                            "menu_Nombre" => htmlentities(trim($row['menu_Nombre'])));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargaDesasignarEstacion":
                $lc_query = "EXECUTE [config].[USP_carga_desasignarEnEstacion] $lc_datos[0],'$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['variableB'] = $row["variableB"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargaTipoEnvio":
                $lc_query = "EXECUTE [config].[USP_carga_tipoEnvio] $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['variableI'] = $row["variableI"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
        }
    }

    public function fn_consultaColeccionPagoPredeterminado($lc_datos) {
        $lc_query = "EXECUTE [config].[USP_cargaColeccionPagoPredeterminado] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['idIntegracion'] = utf8_encode($row["idIntegracion"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }
    
    ////////FUNCION QUE PERMITE MOSTRAR LOS MEDIOS AUTORIZADOS QUE YA FUERON AGREGADOS AL LOCAL//////////
    public function fn_consultaColeccionMediosAutorizadores($lc_datos) {
        $lc_query = "EXECUTE [config].[USP_cargaColeccionMediosAutorizadores]  $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idIntegracion" => utf8_encode(trim($row['idIntegracion'])));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_insertaColeccionPagoPredeterminado($lc_datos) {
        $lc_query = "EXEC [config].[IAE_coleccionPagoPredeterminado]  '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_query)) {
            $this->lc_regs['str'] = 1;
        } else {
            if (($errors = sqlsrv_errors() ) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs['str'] = utf8_encode($error['message']);
                }
            }
        }
        return json_encode($this->lc_regs);
    }

    public function fn_editarColeccionDeDatos($lc_datos) {
        $lc_sql = "EXECUTE [config].[USP_EstacionColecciondeDatos]  $lc_datos[0],$lc_datos[1],'$lc_datos[4]','$lc_datos[2]','$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["ID_ColeccionEstacion"] = utf8_encode($row["ID_ColeccionEstacion"]);
                $this->lc_regs["ID_ColeccionDeDatosEstacion"] = utf8_encode($row["ID_ColeccionDeDatosEstacion"]);
                $this->lc_regs["Descripcion"] = utf8_encode($row["Descripcion"]);
                $this->lc_regs["dato"] = utf8_encode($row["dato"]);
                $this->lc_regs["especificarValor"] = intval($row["especificarValor"]);
                $this->lc_regs["obligatorio"] = intval($row["obligatorio"]);
                $this->lc_regs["tipodedato"] = utf8_encode($row["tipodedato"]);
                $this->lc_regs["caracter"] = utf8_encode($row["caracter"]);
                $this->lc_regs["entero"] = $row["entero"];
                $this->lc_regs["fecha"] = utf8_encode($row["fecha"]);
                $this->lc_regs["seleccion"] = $row["seleccion"];
                $this->lc_regs["numerico"] = $row["numerico"];
                $this->lc_regs["fechaInicio"] = utf8_encode($row["fechaInicio"]);
                $this->lc_regs["fechaFin"] = utf8_encode($row["fechaFin"]);
                $this->lc_regs["minimo"] = $row["minimo"];
                $this->lc_regs["maximo"] = $row["maximo"];
                $this->lc_regs["activo"] = intval($row["activo"]);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function fn_cargaColeccionDeDatos($lc_datos) {
        $lc_sql = "EXECUTE [config].[USP_EstacionColecciondeDatos]  $lc_datos[0],$lc_datos[1],'0','0','0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionEstacion" => $row["ID_ColeccionEstacion"],
                    "Descripcion" => utf8_encode($row["Descripcion"]));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function fn_IAEColeccionMedioAutorizador($lc_datos) {
        $lc_query = "EXEC [config].[IAE_coleccionMedioAutorizador]  '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result) {
            return true;
        }else{
            return false;
        };
    }

    public function fn_actualizaCadenaColeccionDatos($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_EstacionColecciondeDatos]  '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]'";
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
    }

    public function fn_cargaConfiguracionPoliticasModificar($lc_datos) {
        $lc_query = "EXEC [config].[USP_EstacionColecciondeDatos] '$lc_datos[0]',$lc_datos[2],'$lc_datos[1]','0','0'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionEstacion" => $row["ID_ColeccionEstacion"], "ID_ColeccionDeDatosEstacion" => $row["ID_ColeccionDeDatosEstacion"],
                    "descripcion_coleccion" => utf8_encode($row["descripcion_coleccion"]), "descripcion_dato" => utf8_encode($row["descripcion_dato"]),
                    "especificarValor" => intval($row["especificarValor"]), "obligatorio" => intval($row["obligatorio"]),
                    "tipodedato" => $row["tipodedato"], "caracter" => utf8_encode($row["caracter"]),
                    "entero" => $row["entero"], "seleccion" => $row["seleccion"],
                    "fecha" => $row["fecha"], "numerico" => $row["numerico"],
                    "fechaIni" => $row["fechaIni"], "fechaFin" => $row["fechaFin"],
                    "min" => $row["min"], "max" => $row["max"],
                    "isActive" => intval($row["isActive"]));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function fn_cargaColeccionDatosC($lc_datos) {
        $lc_sql = "EXECUTE [config].[USP_EstacionColecciondeDatos]  $lc_datos[0],$lc_datos[1],'$lc_datos[3]','$lc_datos[2]','0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionEstacion" => $row["ID_ColeccionEstacion"],
                    "ID_ColeccionDeDatosEstacion" => utf8_encode($row["ID_ColeccionDeDatosEstacion"]),
                    "Descripcion" => utf8_encode($row["Descripcion"]),
                    "dato" => utf8_encode($row["dato"]),
                    "especificarValor" => utf8_encode($row["especificarValor"]),
                    "obligatorio" => utf8_encode($row["obligatorio"]),
                    "tipodedato" => utf8_encode($row["tipodedato"]));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    //////////////FUNCION QUE LISTA TODOS LOS MEDIOS AUTORIZADOS
    public function fn_cargaMedioAutorizador($lc_datos) {
        $lc_query = "EXEC [config].[USP_cargaColeccionMediosAutorizadores] '$lc_datos[0]','$lc_datos[1]',$lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idIntegracion" => utf8_encode($row["idIntegracion"]),
                    "Descripcion" => utf8_encode($row["Descripcion"]));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function fn_grabaCadenaColeccionDatos($lc_datos) {
        $lc_sql = "EXECUTE [config].[IAE_EstacionColecciondeDatos]  '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]'";
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
    }

    public function fn_cargaPagoPredeterminado($lc_datos) {
        $lc_query = "EXEC [config].[USP_cargaColeccionPagoPredeterminado]  '$lc_datos[0]','$lc_datos[1]',$lc_datos[2]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idIntegracion" => utf8_encode($row["idIntegracion"]),
                    "fmp_descripcion" => utf8_encode($row["fmp_descripcion"]));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function fn_cargarDetalle($lc_datos) {
        $lc_query = "EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("est_id" => $row['est_id'],
                    "est_ip" => $row['est_ip'],
                    "est_nombre" => $row['est_nombre'],
                    "Estado" => $row['Estado'],
                    "menu_Nombre" => utf8_encode($row['menu_Nombre']),
                    "menu_id" => $row['menu_id']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_cargarNuevo($lc_datos) {
        $lc_query = "EXECUTE config.USP_iprestaurante $lc_datos[0],$lc_datos[1]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['Restaurante'] = $row["Restaurante"];
                $this->lc_regs['primer_octeto_ip'] = $row["primer_octeto_ip"];
                $this->lc_regs['segundo_octeto_ip'] = $row["segundo_octeto_ip"];
                $this->lc_regs['tercer_octeto_ip'] = $row["tercer_octeto_ip"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case "grabamodificaestacion":
                $lc_query = "EXECUTE config.IAE_insertamodificaestacion '$lc_datos[12]','$lc_datos[10]',$lc_datos[11],$lc_datos[5],'$lc_datos[0].$lc_datos[1].$lc_datos[2].$lc_datos[3]','$lc_datos[4]','$lc_datos[6]','$lc_datos[8]',$lc_datos[7],$lc_datos[9], '$lc_datos[13]', '$lc_datos[14]','$lc_datos[15]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['Existe'] = $row["Existe"];
                        $this->lc_regs['idestacioninsert'] = $row["idestacioninsert"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "administracionCanalesImpresion":
                $lc_query = "EXECUTE config.USP_administracioncanalimpresionestacion $lc_datos[0] ,$lc_datos[1] ,$lc_datos[2] , '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cimp_id" => $row['cimp_id'],
                            "cimp_descripcion" => utf8_encode($row['cimp_descripcion']),
                            "imp_id" => $row['imp_id'],
                            "imp_nombre" => utf8_encode($row['imp_nombre']),
                            "est_id" => $row['est_id'],
                            "pto_id" => $row['pto_id'],
                            "pto_descripcion" => $row['pto_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "guardaDesasignarEstacion":
                $lc_query = "EXECUTE [config].[IAE_insertamodifica_desasignarEnEstacion] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], $lc_datos[3],'$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result) {
                    return true;
                }else{
                    return false;
                }

            case "guardaTipoEnvio":
                $lc_query = "EXECUTE [config].[IAE_insertamodifica_tipoEnvio] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], $lc_datos[3],'$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result) {
                    return true;
                }else{
                    return false;
                }
        }
    }

    function fn_eliminarCanalImpresoraEstacion($lc_condiciones) {
        $lc_query = "EXECUTE config.ESTACION_E_CanalImpresoraEstacion '" . $lc_condiciones[0] . "', '" . $lc_condiciones[1] . "'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result) {
            return true;
        }else{
            return false;
        }
    }

    function fn_consultaColeccionMesa($lc_condiciones) {
        $lc_query = "EXECUTE config.ESTACION_configuracion_colecciones 0, " . $lc_condiciones[0] . ", " . $lc_condiciones[1] . ", '" . $lc_condiciones[2] . "'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idMesa" => $row['idMesa'],
                    "descripcion" => utf8_encode($row['descripcion']),
                    "mesaActual" => $row['mesaActual']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

}