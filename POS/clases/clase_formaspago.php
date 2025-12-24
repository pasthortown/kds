<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION ULTIMO CAMBIO: NUEVOS ESTILOS INGRESO Y ACTUALIZACION FORMAS DE PAGO/////
///////////////////////////////// TABLA MODAL, COLECCION DE DATOS ATRIBUTO FORMA PAGO///////
////////TABLAS INVOLUCRADAS: Formapago,Cadena///////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION: 09/06/2015///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

class categoria extends sql {
    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }
    //funcion que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "administrarFormasPago":
                $lc_sql = "EXEC config.USP_administracionformaspago " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', '" . $lc_datos[6] . "', " . $lc_datos[7] . ", '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', " . $lc_datos[10] . ",'" . $lc_datos[11] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_orden" => $row['fmp_orden'],
                            "fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => htmlentities(utf8_decode(trim($row['fmp_descripcion']))),
                            "std_id" => $row['std_id'],
                            "fpf_codigo" => $row['fpf_codigo'],
                            "tfp_id" => $row['tfp_id'],
                            "tfp_descripcion" => htmlentities(utf8_decode(trim($row['tfp_descripcion']))),
                            "rda_descripcion" => htmlentities(utf8_decode(trim($row['rda_descripcion']))),
                            "rda_id" => $row['rda_id'],
                            "fmp_varchar1" => htmlentities(utf8_decode(trim($row['fmp_varchar1']))));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "agregarFormasPago":
                $lc_sql = "EXEC config.USP_administracionformaspago " . $lc_datos[0] . ", " . $lc_datos[1] . ", " . $lc_datos[2] . ", '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', '" . $lc_datos[6] . "', " . $lc_datos[7] . ", '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', " . $lc_datos[10] . ",'" . $lc_datos[11] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['fmp_id'] = $row["fmp_id"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarTipoFormaPago":
                $lc_sql = "EXECUTE config.USP_informaciontipoformaspagoredadquiriente $lc_datos[0]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tfp_id" => $row['tfp_id'],
                            "tfp_descripcion" => $row['tfp_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case "cargarTipoAdquiriente":
                $lc_sql = "EXECUTE config.USP_informaciontipoformaspagoredadquiriente $lc_datos[0]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rda_id" => $row['rda_id'],
                            "rda_descripcion" => $row['rda_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case "cargaCadena":
                $lc_sql = "EXECUTE config.USP_informaciontipoformaspagoredadquiriente $lc_datos[0]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cdn_id" => $row['cdn_id'],
                            "cdn_descripcion" => utf8_encode(trim($row['cdn_descripcion'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerRestaurantes":
                $lc_sql = "EXEC config.USP_cnf_restauranteformapagos " . $lc_datos[0] . ",'" . $lc_datos[1] . "'," . $lc_datos[2] . ",'" . $lc_datos[3] . "','" . $lc_datos[4] . "','" . $lc_datos[5] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado'],
                            "rsat_bit" => $row['rsat_bit']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "configuracionformaspagocoleccion":
                $lc_sql = "EXEC config.USP_configuracionformasdepagocoleccion " . $lc_datos[0] . ",'" . $lc_datos[1] . "','" . $lc_datos[2] . "'," . $lc_datos[3] . ",'" . $lc_datos[4] . "'," . $lc_datos[5] . ",'" . $lc_datos[6] . "'," . $lc_datos[7];
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fila" => $row['fila'],
                            "cfpa_id" => $row['cfpa_id'],
                            "cfp_id" => $row['cfp_id'],
                            "cfre_descripcion" => $row['cfre_descripcion'],
                            "fpat_bit" => $row['fpat_bit'],
                            "fpat_float" => $row['fpat_float'],
                            "orden" => $row['orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarPerfilesNivelSeguridad":
                $lc_sql = "EXEC config.USP_niveldeseguridadformasdepago $lc_datos[0],$lc_datos[1]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prf_id" => $row['prf_id'],
                            "prf_descripcion" => $row['prf_descripcion'],
                            "prf_id_coleccion" => $row['prf_id_coleccion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "agregarNivelSeguridad":
                $lc_sql = "EXEC config.IAE_niveldeseguridadformasdepago $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',$lc_datos[4]";       //
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarPerfilesNivelSeguridadModificar":
                $lc_sql = "EXEC config.USP_niveldeseguridadformasdepago $lc_datos[0],'$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prf_id" => $row['prf_id'],
                            "prf_descripcion" => $row['prf_descripcion'],
                            "prf_id_coleccion" => $row['prf_id_coleccion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "agregarNivelSeguridadModificar":
                $lc_sql = "EXEC config.IAE_niveldeseguridadformasdepago $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',$lc_datos[4]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarSimboloMoneda":
                $lc_sql = "EXEC config.USP_paissimbolomoneda $lc_datos[0], $lc_datos[1]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pais_id" => $row['pais_id'],
                            "pais_descripcion" => utf8_encode(trim($row['pais_descripcion'])),
                            "pais_codigo" => $row['pais_codigo'],
                            "pais_moneda" => $row['pais_moneda'],
                            "pais_desc_modeda" => utf8_encode(trim($row['pais_desc_modeda'])),
                            "tipo_cambio" => $row['tipo_cambio'],
                            "pais_base_factura" => $row['pais_base_factura'],
                            "pais_moneda_simbolo" => $row['pais_moneda_simbolo']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarSimboloMoneda":
                $lc_sql = "EXEC config.IAE_paissimbolomoneda $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', $lc_datos[6], $lc_datos[7], $lc_datos[8]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerModificaSimboloMoneda":
                $lc_sql = "EXEC config.USP_paissimbolomoneda $lc_datos[0], $lc_datos[1]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pais_id" => $row['pais_id'],
                            "pais_descripcion" => utf8_encode(trim($row['pais_descripcion'])),
                            "pais_codigo" => $row['pais_codigo'],
                            "pais_moneda" => $row['pais_moneda'],
                            "pais_desc_modeda" => utf8_encode(trim($row['pais_desc_modeda'])),
                            "tipo_cambio" => $row['tipo_cambio'],
                            "pais_base_factura" => $row['pais_base_factura'],
                            "pais_moneda_simbolo" => $row['pais_moneda_simbolo']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarModificaSimboloMoneda":
                $lc_sql = "EXEC config.IAE_paissimbolomoneda $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', $lc_datos[6], '$lc_datos[7]', $lc_datos[8]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarClientes":
                $lc_sql = "EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cli_id" => $row['cli_id'],
                            "cli_apellidos" => utf8_encode(trim($row['cli_apellidos'])),
                            "cli_nombres" => utf8_encode(trim($row['cli_nombres'])),
                            "cli_documento" => $row['cli_documento'],
                            "cli_telefono" => $row['cli_telefono'],
                            "cli_direccion" => utf8_encode(trim($row['cli_direccion'])),
                            "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])),
                            "cli_email" => $row['cli_email'],
                            "fmp_descripcion" => $row['fmp_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerTipoDocumento":
                $lc_sql = "EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tpdoc_id" => $row['tpdoc_id'],
                            "tpdoc_descripcion" => utf8_encode(trim($row['tpdoc_descripcion'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerCiudad":
                $lc_sql = "EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ciu_id" => $row['ciu_id'],
                            "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerAplicaFormasPago":
                $lc_sql = "EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarClienteFormasPago":
                $lc_sql = "EXEC config.IAE_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], $lc_datos[3], $lc_datos[4], $lc_datos[5], '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], $lc_datos[13]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarClienteAplicaFormasPago":
                $lc_sql = "EXEC config.IAE_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], $lc_datos[3], $lc_datos[4], $lc_datos[5], '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], $lc_datos[13]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarClientesAplicaFormaPago":
                $lc_sql = "EXEC config.USP_aplicaclientesformaspago " . $lc_datos[0] . "," . $lc_datos[1] . ",'" . $lc_datos[2] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cli_id" => $row['cli_id'],
                            "cli_apellidos" => utf8_encode(trim($row['cli_apellidos'])),
                            "cli_nombres" => utf8_encode(trim($row['cli_nombres'])),
                            "clienteagregado" => $row['clienteagregado'],
                            "fpat_bit" => $row['fpat_bit']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "aplica_cliente":
                $lc_sql = "EXEC config.IAE_aplicaclientesformaspago $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]', $lc_datos[5]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("AccionFormaPago" => $row['AccionFormaPago']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarTipoFacturacion":
                $lc_sql = "EXEC config.USP_FormasPagoTipoFacturacion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', $lc_datos[3]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDTipoFacturacion" => $row['IDTipoFacturacion'],
                            "tf_descripcion" => $row['tf_descripcion'],
                            "IDStatus" => $row['IDStatus']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarTipoFacturacionColeccion":
                $lc_sql = "EXEC config.IAE_FormasPagoTipoFacturacion $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "cargarTipoFacturacionModifica":
                $lc_sql = "EXEC config.USP_FormasPagoTipoFacturacion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', $lc_datos[3]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDTipoFacturacion" => $row['IDTipoFacturacion'],
                            "tf_descripcion" => $row['tf_descripcion'],
                            "IDStatus" => $row['IDStatus']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarUrlImprieTicket":
                $lc_sql = "EXEC config.IAE_FormasPagoTipoFacturacion $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "cargarUrlImprimeTicket":
                $lc_sql = "EXEC config.USP_FormasPagoTipoFacturacion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', $lc_datos[3]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("url_imprime_ticket" => $row['url_imprime_ticket'],
                            "imprimevoucher" => $row['imprimevoucher']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            //COLECCION FORMAS DE PAGO
            case "administrarColeccionFormasPago":
                $lc_sql = "EXEC [config].[USP_FormasPagoColecciondeDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionFormapago" => $row['ID_ColeccionFormapago'],
                            "ID_ColeccionDeDatosFormapago" => $row['ID_ColeccionDeDatosFormapago'],
                            "descripcion_coleccion" => utf8_encode($row['descripcion_coleccion']),
                            "descripcion_dato" => utf8_encode($row['descripcion_dato']),
                            "especificarValor" => intval($row['especificarValor']),
                            "obligatorio" => intval($row['obligatorio']),
                            "tipodedato" => $row['tipodedato'],
                            "caracter" => utf8_encode($row['caracter']),
                            "entero" => $row['entero'],
                            "fecha" => $row['fecha'],
                            "bitt" => $row['bitt'],
                            "numerico" => $row['numerico'],
                            "fechaIni" => $row['fechaIni'],
                            "fechaFin" => $row['fechaFin'],
                            "min" => intval($row['min']),
                            "max" => intval($row['max']),
                            "isActive" => intval($row['isActive']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "detalleColeccionFormasPago":
                $lc_sql = "EXEC [config].[USP_FormasPagoColecciondeDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionFormapago" => $row['ID_ColeccionFormapago'],
                            "Descripcion" => utf8_encode($row['Descripcion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "datosColeccionFormasPago":
                $lc_sql = "EXEC [config].[USP_FormasPagoColecciondeDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionFormapago" => $row['ID_ColeccionFormapago'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "ID_ColeccionDeDatosFormapago" => $row['ID_ColeccionDeDatosFormapago'],
                            "datos" => utf8_encode($row['datos']),
                            "especificarValor" => $row['especificarValor'],
                            "obligatorio" => $row['obligatorio'],
                            "tipodedato" => utf8_encode($row['tipodedato']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "guardarFormasPagoColeccion":
                $lc_sql = "EXEC [config].[IAE_FormasPagoColecciondeDatos] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', " . $lc_datos[5] . ", '" . $lc_datos[6] . "', " . $lc_datos[7] . ", " . $lc_datos[8] . ", '" . $lc_datos[9] . "', '" . $lc_datos[10] . "', " . $lc_datos[11] . ", " . $lc_datos[12] . ", '" . $lc_datos[13] . "', " . $lc_datos[14];
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors() ) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);

            case "cargaFormaPagoColeccion_edit":
                $lc_sql = "EXEC [config].[USP_FormasPagoColecciondeDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionFormapago" => $row['ID_ColeccionFormapago'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "ID_ColeccionDeDatosFormapago" => $row['ID_ColeccionDeDatosFormapago'],
                            "datos" => utf8_encode($row['datos']),
                            "especificarValor" => $row['especificarValor'],
                            "obligatorio" => $row['obligatorio'],
                            "tipodedato" => $row['tipodedato'],
                            "caracter" => utf8_encode($row['caracter']),
                            "entero" => $row['entero'],
                            "fecha" => $row['fecha'],
                            "seleccion" => $row['seleccion'],
                            "numerico" => $row['numerico'],
                            "fechaInicio" => $row['fechaInicio'],
                            "fechaFin" => $row['fechaFin'],
                            "minimo" => $row['minimo'],
                            "maximo" => $row['maximo'],
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }
    
}