<?php

//-- =================================================================
//--FECHA ULTIMA MODIFICACION	: 11:18 6/1/2017
//--USUARIO QUE MODIFICO	: Mychael Castro
//--DECRIPCION ULTIMO CAMBIO	: Adición de "json_encode" en el resultado final de retorno de la funcion "cargarCategoriasPreciosTodos"
//-- =================================================================
class Categoria extends sql {

    function cargarCategoriasPreciosActivos($idCadena) {
        $result = array();
        $query = "EXEC config.CATEGORIASPRECIOS_configuraciones 1, '', " . $idCadena . ", 'Activo'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "abreviatura" => utf8_encode($row['abreviatura']), "nivel" => $row['nivel'], "idIntegracion" => $row['idIntegracion'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarCategoriasPreciosInactivos($idCadena) {
        $result = array();
        $query = "EXEC config.CATEGORIASPRECIOS_configuraciones 1, '', " . $idCadena . ", 'Inactivo'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "abreviatura" => utf8_encode($row['abreviatura']), "nivel" => $row['nivel'], "idIntegracion" => $row['idIntegracion'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    function cargarCategoriasPreciosTodos($idCadena) {
        $result = array();
        $query = "EXEC config.CATEGORIASPRECIOS_configuraciones 0, '', " . $idCadena . ", ''";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "abreviatura" => utf8_encode($row['abreviatura']), "nivel" => $row['nivel'], "idIntegracion" => $row['idIntegracion'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function guardarCategoriaPrecios($accion, $idCategoria, $descripcion, $abreviatura, $nivel, $idIntegracion, $estado, $idCadena, $integracion, $idUsr)
    {
        $result = array();
        $result["estado"] = 0;
        $query = "EXEC config.CATEGORIASPRECIOS_IA_categorias " . $accion . ", '" . $idCategoria . "', '" . $descripcion . "', '" . $abreviatura . "', " . $nivel . ", " . $idIntegracion . ", '" . $estado . "', " . $idCadena . ", '" . $integracion . "', '" . $idUsr . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $datos = array(
                    "idCategoria" => $row['idCategoria']
                , "descripcion" => utf8_decode($row['descripcion'])
                , "abreviatura" => utf8_decode($row['abreviatura'])
                    , "nivel" => $row['nivel']
                    , "idIntegracion" => $row['idIntegracion']
                    , "estado" => $row['estado']);
            }
            $result["estado"] = 1;
            $result["datos"] = $datos;
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarCategoriasPreciosPorEstado($idCadena, $estado) {
        $query = "EXEC config.CATEGORIASPRECIOS_configuraciones 1, '', " . $idCadena . ", '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idCategoria" => $row['idCategoria'], "descripcion" => utf8_encode($row['descripcion']), "abreviatura" => utf8_encode($row['abreviatura']), "nivel" => $row['nivel'], "idIntegracion" => $row['idIntegracion'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

}
