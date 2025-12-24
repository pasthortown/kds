<?php

class Reporte extends sql {

    function cargarCategorias($idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_configuraciones 0, '', '',  $idCadena ,' $ambiente',$restaurante";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "estado" => $row['estado'], "ruta" => $row['ruta']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarCategoria($idCategoria, $idCadena) {
        $query = "EXEC config.REPORTES_configuraciones 1, '$idCategoria', '', " . $idCadena." ,'' ,''";
        try {
            $this->fn_ejecutarquery($query);
            $row = $this->fn_leerarreglo();
            $result = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "estado" => $row['estado']);
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }
    
    function cargarComboRutaCarpetaReportes($idCadena)
    {
        $query = "EXEC config.USP_REPORTES_CargaOpcionesRutaCarpeta $idCadena";
        try 
        {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) 
            {
                $result[] = array("descripcion" => $row['descripcion']);
            }
            //$row = $this->fn_leerarreglo();
            
            $result['str'] = $this->fn_numregistro();
        } 
        catch (Exception $e) 
        {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function guardarCategoria($accion, $idCategoria, $descripcion, $estado, $idCadena, $opcionRuta, $tipAmbiente,$idRestaurante) {
        $query = "EXEC config.REPORTES_IAE_categorias " . $accion . ", '" . $idCategoria . "', '" . utf8_decode($descripcion) . "', '" . $estado . "', " . $idCadena .",'$opcionRuta'".",'$tipAmbiente',".$idRestaurante;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "estado" => $row['estado'], "ruta" => $row['ruta']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarReportes($idCategoria, $idCadena) {
        $query = "EXEC config.REPORTES_configuraciones 2, '$idCategoria', '', " . $idCadena." ,'','' ";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idReporte" => $row['idReporte'], "label" => utf8_encode($row['label']), "descripcion" => utf8_encode($row['descripcion']), "url" => utf8_encode($row['url']), "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function guardarReporte($accion, $idReporte, $label, $descripcion, $url, $estado, $idCategoria, $idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_IAE_reportes " . $accion . ", '" . $idReporte . "', '" . utf8_decode($label) . "', '" . utf8_decode($descripcion) . "', '" . utf8_decode($url) . "', '" . $estado . "', '" . $idCategoria . "', " . $idCadena.",'$ambiente',".$restaurante;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idReporte" => $row['idReporte'], "label" => utf8_encode($row['label']), "descripcion" => utf8_encode($row['descripcion']), "url" => utf8_encode($row['url']), "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarParametros($idReporte, $idCadena) {
        $query = "EXEC config.REPORTES_configuraciones 3, '', '" . $idReporte . "', " . $idCadena." ,'','' ";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idParametro" => $row['idParametro'], "tipoDato" => $row['tipoDato'], "variable" => utf8_encode($row['variable']), "etiqueta" => utf8_encode($row['etiqueta']), "orden" => $row['orden'], "obligatorio" => $row['obligatorio'], "tablaIntegracion" => $row['tablaIntegracion'], "columnaIntegracion" => $row['columnaIntegracion'], "query" => utf8_encode($row['query']), "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarVariablesSesion($idCadena) {
        $query = "EXEC config.REPORTES_configuraciones 4, '', '', " . $idCadena." ,'','' ";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("sesion" => $row['sesion'], "descripcion" => utf8_encode($row['descripcion']));
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarTiposDato($idCadena) {
        $query = "EXEC config.REPORTES_configuraciones 5, '', '', " . $idCadena." ,'','' ";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idTipoDato" => $row['idTipoDato'], "descripcion" => utf8_encode($row['descripcion']));
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function guardarParametro($accion, $idParametro, $etiqueta, $variable, $tipoDato, $obligatorio, $tablaIntegracion, $columnaIntegracion, $query, $orden, $estado, $idReporte, $idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_IAE_parametros " . $accion . ", '" . $idParametro . "', '" . utf8_decode($etiqueta) . "', '" . utf8_decode($variable) . "', '" . $tipoDato . "', " . $obligatorio . ", '" . $tablaIntegracion . "', '" . $columnaIntegracion . "', '" . utf8_decode($query) . "', " . $orden . ", '" . $estado . "', '" . $idReporte . "', " . $idCadena.",'$ambiente',".$restaurante;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idParametro" => $row['idParametro'], "tipoDato" => $row['tipoDato'], "variable" => utf8_encode($row['variable']), "etiqueta" => utf8_encode($row['etiqueta']), "orden" => $row['orden'], "obligatorio" => $row['obligatorio'], "tablaIntegracion" => $row['tablaIntegracion'], "columnaIntegracion" => $row['columnaIntegracion'], "query" => utf8_encode($row['query']), "orden" => $row['orden'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function actualizarOrdenParametro($html) {
        $query = "EXEC config.REPORTES_A_ordenParametros '" . $html . "'";
        try {
            $this->fn_ejecutarquery($query);
            $result["Confirmar"] = 1;
        } catch (Exception $e) {
            $result["Confirmar"] = 0;
        }
        return json_encode($result);
    }

    function eliminarCategoria($accion, $idCategoria, $idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_IAE_categorias " . $accion . ", '" . $idCategoria . "', '', '', " . $idCadena.",'','$ambiente',$restaurante";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "estado" => $row['estado'], "ruta" => $row['ruta']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function eliminarReporte($accion, $idCategoria, $idReporte, $idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_IAE_reportes " . $accion . ", '" . $idReporte . "', '', '', '', '', '" . $idCategoria . "', " . $idCadena.",'$ambiente',$restaurante";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idReporte" => $row['idReporte'], "label" => utf8_encode($row['label']), "descripcion" => utf8_encode($row['descripcion']), "url" => utf8_encode($row['url']), "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function eliminarParametro($accion, $idReporte, $idParametro, $idCadena,$ambiente,$restaurante) {
        $query = "EXEC config.REPORTES_IAE_parametros " . $accion . ", '" . $idParametro . "', '', '', '', 0, '', '', '', 0, '', '" . $idReporte . "', " . $idCadena.",'$ambiente',$restaurante";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idParametro" => $row['idParametro'], "tipoDato" => $row['tipoDato'], "variable" => utf8_encode($row['variable']), "etiqueta" => utf8_encode($row['etiqueta']), "orden" => $row['orden'], "obligatorio" => $row['obligatorio'], "tablaIntegracion" => $row['tablaIntegracion'], "columnaIntegracion" => $row['columnaIntegracion'], "query" => utf8_encode($row['query']), "orden" => $row['orden'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

}