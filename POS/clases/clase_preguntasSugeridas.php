<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: Archivo de sentencias sql para manejo de///////////
///////////////////////administracion de preguntas sugeridas///////////////
////////FECHA CREACION: 20/05/2015/////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 01/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Aplicacion nuevo estilo, configuraciones en //
////////////////////////////////pantalla modal, cambio de etiquetas///////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

class preguntas extends sql {

    function insertaCabeceraPregunta($accion, $cdnCabecera, $descCabecera, $maxCabecera, $minCabecera, $descPos, $lc_rest, $lc_usuarioId, $respID, $preElimina, $plu, $estado, $respuesta) {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas $accion, $cdnCabecera, '$descCabecera', $maxCabecera, $minCabecera, '$descPos', $lc_rest, '$lc_usuarioId', '$respID', '$preElimina', $plu, '$estado', '$respuesta'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['psug_id'] = $row['psug_id'];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function eliminaRespuesta($accion, $cdnCabecera, $descCabecera, $maxCabecera, $minCabecera, $descPos, $lc_rest, $lc_usuarioId, $respID, $preElimina, $plu, $estado, $respuesta) {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas $accion, $cdnCabecera, '$descCabecera', $maxCabecera, $minCabecera, '$descPos', $lc_rest, '$lc_usuarioId', '$respID', $preElimina, $plu, '$estado', '$respuesta'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }

    function grabaRespuesta($accion, $cdnCabecera, $descCabecera, $maxCabecera, $minCabecera, $descPos, $lc_rest, $usuario, $respID, $preID, $pluID, $estado, $respuesta) {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas $accion, $cdnCabecera, '$descCabecera', $maxCabecera, $minCabecera, '$descPos', $lc_rest, '$usuario', '$respID', '$preID', $pluID, '$estado', '$respuesta'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array('plu_num_plu' => utf8_encode(trim($row['plu_num_plu'])),
                    'plu_id' => $row['plu_id'],
                    'res_descripcion' => utf8_encode(trim($row['res_descripcion'])),
                    'res_id' => utf8_encode(trim($row['res_id'])),
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function grabaPluModificado($accion, $cdnCabecera, $descCabecera, $maxCabecera, $minCabecera, $descPos, $lc_rest, $usuario, $respID, $preIDModificado, $pluModificado, $estado, $respuesta) {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas $accion, $cdnCabecera, '$descCabecera', $maxCabecera, $minCabecera, '$descPos', $lc_rest, '$usuario', $respID, '$preIDModificado', $pluModificado, '$estado', '$respuesta'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }

    function actualizaCabeceraPregunta($accion, $cdnCabecera, $cabDes, $cabMax, $cabMin, $desPos, $lc_rest, $lc_usuarioId, $respID, $cabPregunta, $plu, $cabStd, $respuesta)
    {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas $accion, $cdnCabecera, '$cabDes', $cabMax, $cabMin, '$desPos', $lc_rest, '$lc_usuarioId', $respID, '$cabPregunta', $plu, '$cabStd', '$respuesta'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }

    function validaNumeroDerespuestas($cdnNumeroRespuestas, $psugNumero) {
        $lc_query = "exec config.USP_Numero_Respuestas_Preguntas_Sugeridas $cdnNumeroRespuestas, '$psugNumero', 'detalle'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['numero_maximo_respuestas'] = $row['numero_maximo_respuestas'];
                $this->lc_regs['respuestasasociadas'] = $row['respuestasasociadas'];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function validaConfiguracionRespuestasMaximas($cdnvalidaConfiguracionRespuestasMaximas) {
        $lc_query = "exec config.USP_Numero_Respuestas_Preguntas_Sugeridas $cdnvalidaConfiguracionRespuestasMaximas, 1, 'pregunta'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['numero_maximo_respuestas'] = $row['numero_maximo_respuestas'];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function cargaLosDetalles($preIDdetalle) {
        $lc_query = "exec config.USP_Detalle_Respuestas '$preIDdetalle'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'plu_num_plu' => utf8_encode(trim($row['plu_num_plu']))
                    , 'plu_id' => $row['plu_id']
                    , 'numPlu' => $row['numPlu']
                    , 'orden' => $row['orden']
                    , 'descripcion' => utf8_encode(trim($row['descripcion']))
                    , 'res_descripcion' => utf8_encode(trim($row['res_descripcion']))
                    , 'res_id' => utf8_encode(trim($row['res_id'])));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function cargarDetallePreguntasInicio($inicio, $fin, $cadena, $aBuscar, $opcion, $porEstado) {
        if ($porEstado) {
            $lc_query = "exec config.USP_Paginador_Preguntas_Sugeridas_Estado $inicio, $fin, $cadena, '$aBuscar', '$opcion'";
        } else {
            $lc_query = "exec config.USP_Paginador_Preguntas_Sugeridas $cadena, '$aBuscar'";
        }
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array('psug_id' => $row['psug_id'],
                    'pre_sug_descripcion' => utf8_encode(trim($row['pre_sug_descripcion'])),
                    'psug_descripcion_pos' => trim($row['psug_descripcion_pos']),
                    'psug_resp_minima' => trim($row['psug_resp_minima']),
                    'psug_resp_maxima' => trim($row['psug_resp_maxima']),
                    'numeroderespuestas' => trim($row['numeroderespuestas']),
                    'Estado' => trim($row['Estado']),
                    'idGrupo' => $row['idGrupo'],
                    'grupo' => utf8_encode($row['grupo']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function cargaPreguntaModificada($psuModificado) {
        $lc_query = "exec config.USP_Carga_Pregunta_Modificada '$psuModificado'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['psug_id'] = $row['psug_id'];
                $this->lc_regs['pre_sug_descripcion'] = utf8_encode($row['pre_sug_descripcion']);
                $this->lc_regs['psug_descripcion_pos'] = utf8_encode($row['psug_descripcion_pos']);
                $this->lc_regs['psug_resp_minima'] = $row['psug_resp_minima'];
                $this->lc_regs['psug_resp_maxima'] = $row['psug_resp_maxima'];
                $this->lc_regs['Estado'] = $row['Estado'];
                $this->lc_regs['respuestasMaximas'] = $row['respuestasMaximas'];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function eliminaPregunta($preElimina) {
        $lc_query = "exec config.IAE_Elimina_Respuesta $preElimina";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }

    function cargaPlus($cdn, $tipo_producto) {
        $lc_query = "exec config.USP_Carga_Select_Plus_Preguntas_Sugeridas $cdn, '$tipo_producto'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'plu_num_plu' => $row['plu_num_plu'],
                    'plu_id' => $row['plu_id'],
                    'descripcion' => utf8_encode(trim($row['descripcion'])),
                    'plu_descripcion' => utf8_encode(trim($row['plu_descripcion'])));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function cargaPreguntaEliminaPreguntaEliminaProducto($resultado, $accion, $cdn_id, $plu_id, $psug_id, $usr_id, $orden) {
        $lc_query = "exec config.USP_Preguntas_Sugeridas_Plus $resultado, $accion, $cdn_id, $plu_id, '$psug_id', '$usr_id', $orden";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array('plu_id' => $row['plu_id'],
                    'plu_num_plu' => $row['plu_num_plu'],
                    'plu_descripcion' => utf8_encode(trim($row['plu_descripcion'])),
                    'psug_id' => $row['psug_id'],
                    'pre_sug_descripcion' => utf8_encode(trim($row['pre_sug_descripcion'])));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function modificarRespuesta($lc_usuarioId, $res_id, $value) {
        $lc_query = "exec config.IAE_Cabecera_Preguntas_Sugeridas 'MR', 0, 'x', 0, 0, 'x', 0, '$lc_usuarioId', '$res_id', 0, 0, 'x', '$value'";
        if ($this->fn_ejecutarquery($lc_query)) {
            $this->lc_regs['Confirmar'] = 1;
        } else {
            $this->lc_regs['Confirmar'] = 0;
        }
        return json_encode($this->lc_regs);
    }

    function mergePreguntaSugerida($accion, $idCadena, $idPregunta, $descripcion, $minimo, $maximo, $descripcionPos, $nivel, $estado, $respuestas, $lc_usuarioId, $idGrupoPregunta)
    {
        $lc_query = "EXEC config.PREGUNTA_SUGERIDA_IAE_preguntas_sugeridas $accion, $idCadena, '$idPregunta', '" . utf8_decode($descripcion) . "', $minimo, $maximo, '" . utf8_decode($descripcionPos) . "', $nivel, '$estado', '$respuestas','$lc_usuarioId', '$idGrupoPregunta'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array('psug_id' => $row['psug_id'],
                    'pre_sug_descripcion' => utf8_encode(trim($row['pre_sug_descripcion'])),
                    'psug_descripcion_pos' => utf8_encode(trim($row['psug_descripcion_pos'])),
                    'psug_resp_minima' => trim($row['psug_resp_minima']),
                    'psug_resp_maxima' => trim($row['psug_resp_maxima']),
                    'numeroderespuestas' => trim($row['numeroderespuestas']),
                    'Estado' => trim($row['Estado']),
                    'idGrupo' => $row['idGrupo'],
                    'grupo' => utf8_encode($row['grupo']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }
    function cargarGrupoPreguntasSugeridas($idCadena) {
        $lc_query = "EXEC config.PREGUNTASUGERIDAS_cargarGrupoPreguntas ".$idCadena;
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array('idGrupo' => $row['idGrupo'],
                                        'grupo' => utf8_encode($row['grupo']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

}
