<?php

class Producto extends sql {

    function __construct() {
        parent ::__construct();
    }

    function cargarClasificaciones($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 1, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idClasificacion" => $row['idClasificacion'],
                    "idIntegracionClasificacion" => $row['idIntegracionClasificacion'],
                    "clasificacion" => utf8_encode($row['clasificacion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarProductosPorClasificacion($idCadena, $idClasificacion) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 1, " . $idCadena . ", 0, '" . $idClasificacion . "', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idProducto" => $row['idProducto'],
                    "numPlu" => $row['numPlu'],
                    "idClasificacion" => $row['idClasificacion'],
                    "masterPlu" => $row['masterPlu'],
                    "impuesto1" => $row['impuesto1'],
                    "impuesto2" => $row['impuesto2'],
                    "impuesto3" => $row['impuesto3'],
                    "impuesto4" => $row['impuesto4'],
                    "impuesto5" => $row['impuesto5'],
                    "descripcion" => utf8_encode($row['descripcion']),
                    "estado" => utf8_encode($row['estado']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarProductosPorCadena($idCadena) {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.PRODUCTOS_plus 0, " . $idCadena . ", 0, '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idProducto" => $row['idProducto'],
                    "numPlu" => $row['numPlu'],
                    "idClasificacion" => $row['idClasificacion'],
                    "masterPlu" => $row['masterPlu'],
                    "impuesto1" => $row['impuesto1'],
                    "impuesto2" => $row['impuesto2'],
                    "impuesto3" => $row['impuesto3'],
                    "impuesto4" => $row['impuesto4'],
                    "impuesto5" => $row['impuesto5'],
                    "descripcion" => utf8_encode($row['descripcion']),
                    "estado" => utf8_encode($row['estado']),
                    "clasificacion" => utf8_encode($row['clasificacion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarConfiguracionProducto($idCadena, $idProducto) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 2, " . $idCadena . ", " . $idProducto . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array(
                "idProducto" => $row['idProducto'],
                "numPlu" => $row['numPlu'],
                "idTipoProducto" => intval($row['idTipoProducto']),
                "anulacion" => $row['anulacion'],
                "gramo" => $row['gramo'],
                "cantidad" => $row['cantidad'],
                "preparacion" => $row['preparacion'],
                "tipoPlato" => $row['tipoPlato'],
                "codigoBarras" => $row['codigoBarras'],
                "qsr" => $row['qsr'],
                "cantidad" => $row['cantidad'],
                "impueto1" => intval($row['impuesto1']),
                "impueto2" => intval($row['impuesto2']),
                "impueto3" => intval($row['impuesto3']),
                "impueto4" => intval($row['impuesto4']),
                "impueto5" => intval($row['impuesto5']),
                "masterPlu" => $row['masterPlu'],
                "masterDescripcion" => utf8_encode($row['masterDescripcion']),
                "idClasificacion" => $row['idClasificacion'],
                "descripcion" => utf8_encode($row['descripcion']),
                "contenido" => utf8_encode($row['contenido']),
                "idDepartamento" => intval($row['idDepartamento']),
                "idModificador" => intval($row['idModificador']),
                "estado" => utf8_encode($row['estado']));
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarTiposProducto($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 0, " . $idCadena;

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idTipoProducto" => intval($row['idTipoProducto']),
                    "tipoProducto" => utf8_encode($row['tipoProducto']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarImpuestos($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 2, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idImpuesto" => $row['idImpuesto'],
                    "ordenImpuesto" => $row['ordenImpuesto'],
                    "impuesto" => utf8_encode($row['impuesto']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function guardarProducto($idCadena, $accion, $idProducto, $descripcion, $preparacion, $idTipoProducto, $idClasificacion, $codigoBarras, $anulacion, $gramo, $qsr, $cantidad, $impuesto1, $impuesto2, $impuesto3, $impuesto4, $impuesto5, $masterPlu, $preciosPorCategoria, $canales, $preguntas, $contenido, $idDepartamento, $usuario, $idModificador, $estado) {
        $lc_sql = "EXEC config.PRODUCTOS_IA_producto " . $accion . ", " . $idCadena . ", " . $idProducto . ", '" . utf8_decode($descripcion) . "', " . $preparacion . ", " . $idTipoProducto . ", '" . $idClasificacion . "', '" . $codigoBarras . "', " . $anulacion . ", " . $gramo . ", " . $qsr . ", " . $cantidad . ", " . $impuesto1 . ", " . $impuesto2 . ", " . $impuesto3 . ", " . $impuesto4 . ", " . $impuesto5 . ", " . $masterPlu . ", '" . $preciosPorCategoria . "', '" . $canales . "', '" . $preguntas . "', '" . utf8_decode($contenido) . "', '" . $idDepartamento . "', '" . $usuario . "', " . $idModificador . ", '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array(
                "idProducto" => $row['idProducto'],
                "numPlu" => $row['numPlu'],
                "idTipoProducto" => intval($row['idTipoProducto']),
                "anulacion" => $row['anulacion'],
                "gramo" => $row['gramo'],
                "cantidad" => $row['cantidad'],
                "preparacion" => $row['preparacion'],
                "tipoPlato" => $row['tipoPlato'],
                "codigoBarras" => $row['codigoBarras'],
                "qsr" => $row['qsr'],
                "cantidad" => $row['cantidad'],
                "impuesto" => intval($row['impuesto']),
                "impueto1" => intval($row['impuesto1']),
                "impueto2" => intval($row['impuesto2']),
                "impueto3" => intval($row['impuesto3']),
                "impueto4" => intval($row['impuesto4']),
                "impueto5" => intval($row['impuesto5']),
                "masterPlu" => $row['masterPlu'],
                "masterDescripcion" => utf8_encode($row['masterDescripcion']),
                "idClasificacion" => $row['idClasificacion'],
                "descripcion" => utf8_encode($row['descripcion']),
                "estado" => utf8_encode($row['estado']));
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarCategoriasPorCadena($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 3, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idCategoria" => $row['idCategoria'],
                    "idIntegracion" => $row['idIntegracion'],
                    "abreviatura" => utf8_encode($row['abreviatura']),
                    "categoria" => utf8_encode($row['categoria']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPreciosPorCategoriasPorProducto($idCadena, $idProducto) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 3, " . $idCadena . ", " . $idProducto . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idCategoria" => $row['idCategoria'],
                    "precioBase" => $row['precioBase'],
                    "neto" => $row['neto'],
                    "iva" => $row['iva']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarCanalImpresionPorCadena($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 4, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idCanal" => $row['idCanal'],
                    "canal" => utf8_encode($row['canal']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarCanalImpresionPorProducto($idProducto) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 4, 0, " . $idProducto . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idCanal" => $row['idCanal']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarMasterPlus($idCadena, $parametro) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 5, " . $idCadena . ", 0, '', '" . utf8_decode($parametro) . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numPlu" => $row['numPlu'],
                    "producto" => utf8_encode($row['producto']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPreguntasSueridasPorProducto($idCadena, $idProducto) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 6, " . $idCadena . ", " . $idProducto . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idPregunta" => $row['idPregunta'],
                    "orden" => $row['orden'],
                    "pregunta" => utf8_encode($row['pregunta']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPreguntasSueridasPorCadena($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 5, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idPregunta" => $row['idPregunta'],
                    "pregunta" => utf8_encode($row['pregunta']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPreciosPorCategoriasPorProductoObjeto($idCadena, $idProducto) {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.PRODUCTOS_plus 3, " . $idCadena . ", " . $idProducto . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "codProducto" => $idProducto,
                    "bruto" => $row['precioBase'],
                    "iva" => $row['iva'],
                    "neto" => $row['neto'],
                    "codCategoria" => $row['idIntegracion']);
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function actualizarDepartamentosMaxPoint($idCadena, $idUsuario, $descripcion, $idDepartamento, $idNumDepartamento) {
        $lc_sql = "EXEC [config].[PRODUCTOS_IA_coleccion_departamentos] " . $idCadena . ", '" . $idUsuario . "', '" . utf8_decode($descripcion) . "', " . $idDepartamento . ", " . $idNumDepartamento;
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("estado" => $row['estado']);
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarDepartamentosPorCadena($idCadena) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 6, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idDepartamento" => $row['idDepartamento'],
                    "idIntegracionDepartamento" => $row['idIntegracionDepartamento'],
                    "departamento" => utf8_encode($row['departamento']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPlusColeccionDeDatos($idCadena, $idProducto) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.PRODUCTOS_plus_configuracion_colecciones 0, " . $idCadena . ", " . $idProducto . ", ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idColeccionPlus" => $row['idColeccionPlus'],
                    "idColeccionDeDatosPlus" => $row['idColeccionDeDatosPlus'],
                    "idPlu" => $row['idPlu'],
                    "descripcionColeccion" => utf8_encode($row['descripcionColeccion']),
                    "descripcionDato" => utf8_encode($row['descripcionDato']),
                    "especificarValor" => intval($row['especificarValor']),
                    "obligatorio" => intval($row['obligatorio']),
                    "tipoDeDato" => $row['tipoDeDato'],
                    "caracter" => utf8_encode($row['caracter']),
                    "entero" => $row['entero'],
                    "fecha" => $row['fecha'],
                    "seleccion" => $row['seleccion'],
                    "numerico" => $row['numerico'],
                    "fechaIni" => $row['fechaIni'],
                    "fechaFin" => $row['fechaFin'],
                    "min" => $row['min'],
                    "max" => $row['max'],
                    "activo" => intval($row['activo']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function editarColeccionPlu($idCadena, $idColeccionPlus, $idColeccionDeDatosPlus, $idPlu, $varchar, $entero, $fecha, $seleccion, $numerico, $fechaIni, $fechaFin, $min, $max, $estado, $usuario) {
        $this->lc_regs = [];
        $fechaIni = html_entity_decode($fechaIni, ENT_QUOTES);
        $fechaFin = html_entity_decode($fechaFin, ENT_QUOTES);
        $varchar = $this->mssql_escape_string($varchar);
        $lc_sql = "EXEC config.PRODUCTOS_IA_coleccion_plus 0, " . $idCadena . ", '" . $idColeccionPlus . "', '" . $idColeccionDeDatosPlus . "', " . $idPlu . ", '" . utf8_decode($varchar) . "', '" . $entero . "', " . $fecha . ", " . $seleccion . ", '" . $numerico . "', " . $fechaIni . ", " . $fechaFin . ", '" . $min . "', '" . $max . "', " . $estado . ", '" . $usuario . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idColeccionPlus" => $row['idColeccionPlus'],
                    "idColeccionDeDatosPlus" => $row['idColeccionDeDatosPlus'],
                    "idPlu" => $row['idPlu'],
                    "descripcionColeccion" => utf8_encode($row['descripcionColeccion']),
                    "descripcionDato" => utf8_encode($row['descripcionDato']),
                    "especificarValor" => $row['especificarValor'],
                    "obligatorio" => $row['obligatorio'],
                    "tipoDeDato" => $row['tipoDeDato'],
                    "caracter" => $row['caracter'],
                    "entero" => $row['entero'],
                    "fecha" => $row['fecha'],
                    "seleccion" => $row['seleccion'],
                    "numerico" => $row['numerico'],
                    "fechaIni" => $row['fechaIni'],
                    "fechaFin" => $row['fechaFin'],
                    "min" => $row['min'],
                    "max" => $row['max'],
                    "activo" => $row['activo']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPluColeccionDescripcion($idCadena) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.PRODUCTOS_plus_configuracion_colecciones 1, " . $idCadena . ", 0, ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idColeccionPlus" => $row['idColeccionPlus'],
                    "descripcion" => utf8_encode($row['descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarPluColeccionDatos($idColeccionPlus) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.PRODUCTOS_plus_configuracion_colecciones 2, 0, 0, '" . $idColeccionPlus . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idColeccionDatosPlus" => $row['idColeccionDatosPlus'],
                    "descripcion" => utf8_encode($row['descripcion']),
                    "especificarValor" => $row['especificarValor'],
                    "obligatorio" => $row['obligatorio'],
                    "tipoDeDato" => $row['tipoDeDato']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function guardarNuevaColeccionPlus($idCadena, $idColeccionDatosPlus, $idColeccionPlus, $idPlu, $varchar, $entero, $fecha, $seleccion, $numerico, $fechaIni, $fechaFin, $min, $max, $usuario) {
        $this->lc_regs = [];
        $fechaIni = html_entity_decode($fechaIni, ENT_QUOTES);
        $fechaFin = html_entity_decode($fechaFin, ENT_QUOTES);
        $varchar = $this->mssql_escape_string($varchar);
        $lc_sql = "EXEC config.PRODUCTOS_IA_coleccion_plus 1, " . $idCadena . ", '" . $idColeccionPlus . "', '" . $idColeccionDatosPlus . "', " . $idPlu . ", '" . utf8_decode($varchar) . "', " . $entero . ", " . $fecha . ", " . $seleccion . ", " . $numerico . ", " . $fechaIni . ", " . $fechaFin . ", " . $min . ", " . $max . ", 1, '" . $usuario . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idColeccionPlus" => $row['idColeccionPlus'],
                    "idColeccionDeDatosPlus" => $row['idColeccionDeDatosPlus'],
                    "idPlu" => $row['idPlu'],
                    "descripcionColeccion" => utf8_encode($row['descripcionColeccion']),
                    "descripcionDato" => utf8_encode($row['descripcionDato']),
                    "especificarValor" => $row['especificarValor'],
                    "obligatorio" => $row['obligatorio'],
                    "tipoDeDato" => $row['tipoDeDato'],
                    "caracter" => utf8_encode($row['caracter']),
                    "entero" => $row['entero'],
                    "fecha" => $row['fecha'],
                    "seleccion" => $row['seleccion'],
                    "numerico" => $row['numerico'],
                    "fechaIni" => $row['fechaIni'],
                    "fechaFin" => $row['fechaFin'],
                    "min" => $row['min'],
                    "max" => $row['max'],
                    "activo" => $row['activo']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarListaModificadores($accion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC [config].[PRODUCTOS_modificadores] " . $accion;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idModificador" => $row['idModificador'],
                    "Modificador" => utf8_encode($row['Modificador']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function mssql_escape_string($string_to_escape) {
        $replaced_string = str_replace("'", "''", $string_to_escape);
        return $replaced_string;
    }

}