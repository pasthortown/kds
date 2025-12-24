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
                $this->lc_regs[] = array("idClasificacion" => $row['idClasificacion'], "clasificacion" => utf8_encode($row['clasificacion']));
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
                $this->lc_regs[] = array("idProducto" => $row['idProducto'], "numPlu" => $row['numPlu'], "idClasificacion" => $row['idClasificacion'], "masterPlu" => $row['masterPlu'], "impuesto1" => $row['impuesto1'], "impuesto2" => $row['impuesto2'], "impuesto3" => $row['impuesto3'], "impuesto4" => $row['impuesto4'], "impuesto5" => $row['impuesto5'], "descripcion" => utf8_encode($row['descripcion']), "estado" => utf8_encode($row['estado']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarProductosPorCadena($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_plus 0, " . $idCadena . ", 0, '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idProducto" => $row['idProducto'], "numPlu" => $row['numPlu'], "idClasificacion" => $row['idClasificacion'], "masterPlu" => $row['masterPlu'], "impuesto1" => $row['impuesto1'], "impuesto2" => $row['impuesto2'], "impuesto3" => $row['impuesto3'], "impuesto4" => $row['impuesto4'], "impuesto5" => $row['impuesto5'], "descripcion" => utf8_encode($row['descripcion']), "estado" => utf8_encode($row['estado']), "clasificacion" => utf8_encode($row['clasificacion']));
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
                "impueto1" => $row['impueto1'],
                "impueto2" => $row['impueto2'],
                "impueto3" => $row['impueto3'],
                "impueto4" => $row['impueto4'],
                "impueto5" => $row['impueto5'],
                "masterPlu" => $row['masterPlu'],
                "masterDescripcion" => utf8_encode($row['masterDescripcion']),
                "idClasificacion" => $row['idClasificacion'],
                "descripcion" => utf8_encode($row['descripcion']),
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
                $this->lc_regs[] = array("idTipoProducto" => $row['idTipoProducto'], "tipoProducto" => utf8_encode($row['tipoProducto']));
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
                $this->lc_regs[] = array("idImpuesto" => $row['idImpuesto'], "ordenImpuesto" => $row['ordenImpuesto'], "impuesto" => utf8_encode($row['impuesto']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function guardarProducto($idCadena, $accion, $idProducto, $descripcion, $preparacion, $idTipoProducto, $idClasificacion, $codigoBarras, $anulacion, $gramo, $qsr, $cantidad, $impuesto1, $impuesto2, $impuesto3, $impuesto4, $impuesto5, $masterPlu, $preciosPorCategoria, $canales, $preguntas, $estado) {
        $lc_sql = "EXEC config.PRODUCTOS_IA_producto " . $accion . ", " . $idCadena . ", " . $idProducto . ", '" . utf8_decode($descripcion) . "', " . $preparacion . ", " . $idTipoProducto . ", '" . $idClasificacion . "', '" . $codigoBarras . "', " . $anulacion . ", " . $gramo . ", " . $qsr . ", " . $cantidad . ", " . $impuesto1 . ", " . $impuesto2 . ", " . $impuesto3 . ", " . $impuesto4 . ", " . $impuesto5 . ", " . $masterPlu . ", '" . $preciosPorCategoria . "', '" . $canales . "', '" . $preguntas . "', '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idProducto" => $row['idProducto'], "numPlu" => $row['numPlu'], "idClasificacion" => $row['idClasificacion'], "masterPlu" => $row['masterPlu'], "impuesto1" => $row['impuesto1'], "impuesto2" => $row['impuesto2'], "impuesto3" => $row['impuesto3'], "impuesto4" => $row['impuesto4'], "impuesto5" => $row['impuesto5'], "descripcion" => utf8_encode($row['descripcion']), "estado" => utf8_encode($row['estado']), "clasificacion" => utf8_encode($row['clasificacion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarCategoriasPorCadena($idCadena) {
        $lc_sql = "EXEC config.PRODUCTOS_configuracion 3, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idCategoria" => $row['idCategoria'], "abreviatura" => utf8_encode($row['abreviatura']), "categoria" => utf8_encode($row['categoria']));
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
                $this->lc_regs[] = array("idCategoria" => $row['idCategoria'], "precioBase" => $row['precioBase']);
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
                $this->lc_regs[] = array("idCanal" => $row['idCanal'], "canal" => utf8_encode($row['canal']));
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
                $this->lc_regs[] = array("numPlu" => $row['numPlu'], "producto" => utf8_encode($row['producto']));
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
                $this->lc_regs[] = array("idPregunta" => $row['idPregunta'], "orden" => $row['orden'], "pregunta" => utf8_encode($row['pregunta']));
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
                $this->lc_regs[] = array("idPregunta" => $row['idPregunta'], "pregunta" => utf8_encode($row['pregunta']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

}

?>			