<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuración de Restaurante //////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class restaurante extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    function cargarListaRestaurantes($resultado, $rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones " . $resultado . ", " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                    "rst_direccion" => utf8_encode(trim($row['rst_direccion'])),
                    "rst_fono" => utf8_encode(trim($row['rst_fono'])),
                    "rst_localizacion" => utf8_encode(trim($row['rst_localizacion'])),
                    "tpsrv_descripcion" => utf8_encode(trim($row['tpsrv_descripcion'])),
                    "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])),
                    "std_id" => $row['std_id'],
                    "rst_numpiso" => $row['rst_numpiso']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarCiudades($rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones 1, " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ciu_id" => $row['ciu_id'],
                    "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarCategoria($rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones 13, " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDCategoria" => $row['IDCategoria'],
                    "cat_descripcion" => utf8_encode(trim($row['cat_descripcion'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarTipoServicio($rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones 2, " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("tpsrv_id" => $row['tpsrv_id'],
                    "tpsrv_descripcion" => utf8_encode(trim($row['tpsrv_descripcion'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarTipoFacturacion($rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones 3, " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("tf_id" => $row['tf_id'],
                    "tf_descripcion" => utf8_encode(trim($row['tf_descripcion'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarInformacionRestaurante($resultado, $rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones " . $resultado . ", " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                    "rst_direccion" => utf8_encode(trim($row['rst_direccion'])),
                    "rst_fono" => utf8_encode(trim($row['rst_fono'])),
                    "rst_mid" => utf8_encode(trim($row['rst_mid'])),
                    "rst_localizacion" => utf8_encode(trim($row['rst_localizacion'])),
                    "rst_serie" => utf8_encode(trim($row['rst_serie'])),
                    "rst_puntoemision" => utf8_encode(trim($row['rst_puntoemision'])),
                    "ciu_id" => $row['ciu_id'],
                    "rst_tipo_servicio" => $row['rst_tipo_servicio'],
                    "std_id" => $row['std_id'],
                    "rst_tipo_facturacion" => $row['rst_tipo_facturacion'],
                    "rst_tiempopedido" => $row['rst_tiempopedido'],
                    "rst_cancelar_pago" => $row['rst_cancelar_pago'],
                    "rst_num_personas" => $row['rst_num_personas'],
                    "rst_horarioatencion" => $row['rst_horarioatencion'],
                    "rst_servicio" => $row['rst_servicio'],
                    "rst_tipo_cantidad" => $row['rst_tipo_cantidad'],
                    "rst_cajon_fin_transaccion" => $row['rst_cajon_fin_transaccion'],
                    "emp_id" => $row['emp_id'],
                    "emp_ruc" => $row['emp_ruc'],
                    "emp_nombre" => $row['emp_nombre'],
                    "emp_confirmar" => $row['emp_confirmar'],
                    "metodoImpuesto" => $row['metodoImpuesto'],
                    "IDCategoria" => $row['IDCategoria']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarPisos($resultado, $rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones " . $resultado . ", " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("pis_id" => $row['pis_id'],
                    "pis_numero" => $row['pis_numero'],
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarAreas($resultado, $cdn_id, $pis_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones " . $resultado . ", 0, " . $cdn_id . ", '" . $pis_id . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("arp_id" => $row['arp_id'],
                    "arp_descripcion" => utf8_encode(trim($row['arp_descripcion'])),
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function administrarPisoArea($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $idUsuario = null) {
        $lc_sql = "EXEC config.IAE_RST_restaurantepisosareas " . $accion . ", " . $resultado . ", " . $rst_id . ", '" . $pis_id . "', '" . $arp_id . "', '" . $std_id . "', '" . utf8_decode($descripcion) . "','$idUsuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Confirmar'] = 1;
        } else {
            $this->lc_regs['Confirmar'] = 0;
        }
        return json_encode($this->lc_regs);
    }

    function agregarPiso($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $idUsuario = null) {
        $lc_sql = "EXEC config.IAE_RST_restaurantepisosareas " . $accion . ", " . $resultado . ", " . $rst_id . ", " . $pis_id . ", " . $arp_id . ", " . $std_id . ", '" . utf8_decode($descripcion) . "'" . "','$idUsuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("pis_id" => $row['pis_id'],
                    "pis_numero" => $row['pis_numero'],
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function agregarArea($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $idUsuario = null) {
        $lc_sql = "EXEC config.IAE_RST_restaurantepisosareas " . $accion . ", " . $resultado . ", " . $rst_id . ", '" . $pis_id . "', '" . $arp_id . "', '" . $std_id . "', '" . utf8_decode($descripcion) . "','$idUsuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("arp_id" => $row['arp_id'],
                    "arp_descripcion" => utf8_encode(trim($row['arp_descripcion'])),
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function modificarRestaurante($accion, $resultado, $rst_id, $rst_direccion, $rst_fono, $ciu_id, $tpsrv_rst, $rst_mid, $tpfct_rst, $rst_nmr_sr, $rst_pnt_msn, $rst_tmp_pdd, $rst_cnclr_pg, $rst_nmr_prsns, $rst_srvc, $rst_cntd_grms, $rst_br_cjn, $std_id, $cdn_id, $usuarioId, $idMetodoImpuesto, $cat_rst, $horarioatencion) {
        $lc_sql = "EXEC config.IAE_RST_restauranteadministracion " . $accion . ", " . $resultado . ", " . $rst_id . ", '" . utf8_decode($rst_direccion) . "' ,'" . utf8_decode($rst_fono) . "', " . $ciu_id . ", '" . utf8_decode($tpsrv_rst) . "' ,'" . utf8_decode($rst_mid) . "', '" . $tpfct_rst . "', '" . utf8_decode($rst_nmr_sr) . "' ,'" . utf8_decode($rst_pnt_msn) . "', " . $rst_tmp_pdd . ", " . $rst_cnclr_pg . ", " . $rst_nmr_prsns . ", " . $rst_srvc . ", " . $rst_cntd_grms . ", " . $rst_br_cjn . ", '" . $std_id . "', " . $cdn_id . ", '" . $usuarioId . "', '" . $idMetodoImpuesto . "', '" . $cat_rst . "'," . $horarioatencion;
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                    "rst_direccion" => utf8_encode(trim($row['rst_direccion'])),
                    "rst_fono" => utf8_encode(trim($row['rst_fono'])),
                    "rst_localizacion" => utf8_encode(trim($row['rst_localizacion'])),
                    "tpsrv_descripcion" => utf8_encode(trim($row['tpsrv_descripcion'])),
                    "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])),
                    "std_id" => $row['std_id'],
                    "rst_numpiso" => $row['rst_numpiso']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

  /*function modificarImagenArea($lc_datos) {
        $lc_sql = "EXEC usp_rstn_iae_imagen " . $lc_datos[0] . ", '" . $lc_datos[1] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Confirmar'] = 1;
        } else {
            $this->lc_regs['Confirmar'] = 0;
        }
        return json_encode($this->lc_regs);
    }*/

    function cargarAutorizacionesRestaurantes($resultado, $rst_id, $cdn_id) {
        $lc_sql = "EXEC config.USP_RST_restauranteconfiguraciones " . $resultado . ", " . $rst_id . ", " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("aur_id" => $row['aur_id'],
                    "atr_numero_autorizacion" => $row['atr_numero_autorizacion'],
                    "atr_tipo_documento" => $row['atr_tipo_documento'],
                    "std_id" => $row['std_id'],
                    "aur_inicio_secuencia" => $row['aur_inicio_secuencia'],
                    "aur_ultima_secuencia" => $row['aur_ultima_secuencia'],
                    "aur_fecha_inicio" => $row['aur_fecha_inicio'],
                    "aur_fecha_fin" => $row['aur_fecha_fin']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function administrarAutorizacionRestaurante($accion, $rst_id, $cdn_id, $aur_id, $sec_ini, $sec_fin, $fecha_ini, $fecha_fin, $idUsuario = null) {
        $lc_sql = "EXEC config.IAE_RST_autorizacionrestaurante " . $accion . ", " . $rst_id . ", " . $cdn_id . ", " . $aur_id . ", " . $sec_ini . ", " . $sec_fin . ", '" . $fecha_ini . "', '" . $fecha_fin . "','$idUsuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("aur_id" => $row['aur_id'],
                    "atr_numero_autorizacion" => $row['atr_numero_autorizacion'],
                    "atr_tipo_documento" => $row['atr_tipo_documento'],
                    "std_id" => $row['std_id'],
                    "aur_inicio_secuencia" => $row['aur_inicio_secuencia'],
                    "aur_ultima_secuencia" => $row['aur_ultima_secuencia'],
                    "aur_fecha_inicio" => $row['aur_fecha_inicio'],
                    "aur_fecha_fin" => $row['aur_fecha_fin']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarMetodoCalculoImpuesto($cdn_id) {
        $lc_sql = "EXEC [config].[USP_RST_restauranteconfiguraciones] 10, 0, " . $cdn_id . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("id" => $row['id'],
                    "descripcion" => $row['descripcion']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarImpuestosCadena($cadenaId) {
        $lc_sql = "EXEC [config].[USP_RST_restauranteconfiguraciones] 11, 0, " . $cadenaId . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("id" => $row['id'],
                    "descripcion" => $row['descripcion']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarImpuestosRestaurante($rst_id, $cadenaId) {
        $lc_sql = "EXEC [config].[USP_RST_restauranteconfiguraciones] 12, " . $rst_id . ", " . $cadenaId . ", ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("id" => $row['id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function guardarImpuestosRestaurante($accion, $rst_id, $cadenaId, $usuarioId, $impuestos) {
        $lc_sql = "EXEC [config].[USP_RST_restauranteconfiguracionimpuestos] " . $accion . ", " . $rst_id . ", " . $cadenaId . ", '" . $usuarioId . "', '" . $impuestos . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function validarImpuestosRestaurante($accion, $rst_id, $cadenaId, $usuarioId, $impuestos) {
        $lc_sql = "EXEC [config].[USP_RST_restauranteconfiguracionimpuestos] " . $accion . ", " . $rst_id . ", " . $cadenaId . ", '" . $usuarioId . "', '" . $impuestos . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function administrarColeccionRestaurante($accion, $rst_id) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos] " . $accion . ", " . $rst_id . ", '0', '0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                    "ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
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
                    "min" => $row['min'],
                    "max" => $row['max'],
                    "isActive" => intval($row['isActive']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function detalleColeccionRestaurante($accion, $rst_id) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos] " . $accion . ", " . $rst_id . ", '0', '0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                    "Descripcion" => utf8_encode($row['Descripcion']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function datosColeccionRestaurante($accion, $rst_id, $IDColeccionRestaurante) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos] " . $accion . ", " . $rst_id . ", '" . $IDColeccionRestaurante . "', '0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                    "Descripcion" => utf8_encode($row['Descripcion']),
                    "ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
                    "datos" => utf8_encode($row['datos']),
                    "especificarValor" => utf8_encode($row['especificarValor']),
                    "obligatorio" => utf8_encode($row['obligatorio']),
                    "tipodedato" => utf8_encode($row['tipodedato']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function guardarRestauranteColeccion($accion, $IDColecciondeDatosRestaurante, $IDColeccionRestaurante, $IDRestaurante, $varchar, $entero, $fecha, $seleccion, $numerico, $fecha_inicio, $fecha_fin, $minimo, $maximo, $IDUsuario, $estado) {
        $lc_sql = "EXEC [config].[IAE_RestauranteColecciondeDatos] " . $accion . ", '" . $IDColecciondeDatosRestaurante . "', '" . $IDColeccionRestaurante . "', " . $IDRestaurante . ", '" . $varchar . "', " . $entero . ", '" . $fecha . "', " . $seleccion . ", " . $numerico . ", '" . $fecha_inicio . "', '" . $fecha_fin . "', " . $minimo . ", " . $maximo . ", '" . $IDUsuario . "', " . $estado;
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

    function cargaRestauranteColeccion_edit($accion, $rst_id, $IDColeccionRestaurante, $IDColecciondeDatosRestaurante) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos] " . $accion . ", " . $rst_id . ", '" . $IDColeccionRestaurante . "', '" . $IDColecciondeDatosRestaurante . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                    "Descripcion" => utf8_encode($row['Descripcion']),
                    "ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
                    "datos" => utf8_encode($row['datos']),
                    "especificarValor" => intval($row['especificarValor']),
                    "obligatorio" => intval($row['obligatorio']),
                    "tipodedato" => $row['tipodedato'],
                    "caracter" => utf8_encode($row['caracter']),
                    "entero" => $row['entero'],
                    "fecha" => $row['fecha'],
                    "seleccion" => intval($row['seleccion']),
                    "numerico" => $row['numerico'],
                    "fechaInicio" => $row['fechaInicio'],
                    "fechaFin" => $row['fechaFin'],
                    "minimo" => $row['minimo'],
                    "maximo" => $row['maximo'],
                    "estado" => intval($row['estado']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function CanalesImpresion($accion, $rst_id, $cadena) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos_ImpresionDocumentos] " . $accion . ", " . $rst_id . ", " . $cadena . ", '0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDCanalImpresion" => $row['IDCanalImpresion'],
                    "canal_impresion" => utf8_encode($row['canal_impresion']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarTipoDocumento($accion, $rst_id, $cadena) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos_ImpresionDocumentos] " . $accion . ", " . $rst_id . ", " . $cadena . ", '0'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
                    "ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                    "Tipo_Documento" => utf8_encode($row['Tipo_Documento']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargarTipoDocumentoRestaurante($accion, $rst_id, $cadena, $idColeccionDeDatosRestaurante) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos_ImpresionDocumentos] " . $accion . ", " . $rst_id . ", " . $cadena . ", '" . $idColeccionDeDatosRestaurante . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDTipoDocumento" => utf8_encode($row['IDTipoDocumento']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function guardarImpresionTipoDocumentos($accion, $rst_id, $cadena, $factura, $voucher, $linea, $usuario) {
        $lc_sql = "EXEC [config].[IAE_RestauranteColecciondeDatos_ImpresionDocumentos] " . $accion . ", " . $rst_id . ", " . $cadena . ", '" . $factura . "', '" . $voucher . "', '" . $linea . "', '" . $usuario . "'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result) {
            return true;
        }else{
            return false;
        };
    }

    function validaTipoDocumentoCanalImpresion($accion, $rst_id, $cadena, $tipodocumentoV) {
        $lc_sql = "EXEC [config].[USP_RestauranteColecciondeDatos_ImpresionDocumentos] " . $accion . ", " . $rst_id . ", " . $cadena . ", '" . $tipodocumentoV . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ExisteTipoDocumento" => utf8_encode($row['ExisteTipoDocumento']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function sincronizarRestaurantes($restaurantes) {
        foreach ($restaurantes as $restaurante) {

            if (isset($restaurante->telefono)) {
                $telefonoIngreso = $restaurante->telefono;
            } else {
                $telefonoIngreso = '';
            }

            if (isset($restaurante->direccion)) {
                $direccionIngreso = $restaurante->direccion;
            } else {
                $direccionIngreso = '';
            }

            $lc_sql = "EXECUTE config.RESTAURANTE_I_restaurante " . $restaurante->codRestaurante . ", " . $restaurante->codCiudad . ", " . $restaurante->codCadena . ", '" . utf8_encode($restaurante->codTienda) . "', '" . utf8_encode($restaurante->descripcion) . "', '" . utf8_decode($direccionIngreso) . "', '" . utf8_decode($telefonoIngreso) . "', '" . utf8_encode($restaurante->localizacion) . "', " . $restaurante->estado;
            $this->fn_ejecutarquery($lc_sql);
        }
        return json_encode("Realizado");
    }

}
