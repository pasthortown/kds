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

class adminPrecios extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {

            case 'cargarDetalleCategorias':
                $lc_query = "exec [config].[USP_administracionPrecios_listaCategorias] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cat_id" => $row['cat_id'],
                            "cat_descripcion" => utf8_encode(trim($row['cat_descripcion'])),
                            "cat_abreviatura" => utf8_encode(trim($row['cat_abreviatura'])),
                            "cdn_id" => $row['cdn_id'],
                            "Estado" => trim($row['Estado']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'cargaProgramaciones':
                $lc_query = "exec [config].[USP_administracionPrecios_listadoProgramacionPrecios] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prp_id" => $row['prp_id'],
                            "fecha" => trim($row['fecha']),
                            "fechaCreacion" => trim($row['fechaCreacion']),
                            "horaCreacion" => trim($row['horaCreacion']),
                            "hora" => $row['hora'],
                            "usr_descripcion" => utf8_encode(trim($row['usr_descripcion'])),
                            "cat_descripcion" => utf8_encode(trim($row['cat_descripcion'])),
                            "std_id" => trim($row['std_id']),
                            "imagenEstado" => $row['imagenEstado']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'cancelaProgramacion':
                $lc_query = "exec [config].[IAE_administracionPrecios_cancelaProgramacionPrecios] '$lc_datos[1]','$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['actualiza'] = $row["actualiza"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'grabaCadenaPrecios':
                $lc_query = "exec [config].[IAE_administracionPrecios_grabaStringPrecios] '$lc_datos[0]','$lc_datos[4]','$lc_datos[2]','$lc_datos[1]','$lc_datos[3]',$lc_datos[5],'$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['prp_id'] = $row["prp_id"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'guardaTemporalmenteTrama':
                $lc_query = "exec [config].[IAE_administracionPrecios_insertaTramaTemporal] $lc_datos[2],'$lc_datos[0]',$lc_datos[1],'$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'cargaPreciosCategorias':
                $lc_query = "exec [config].[USP_administracionPrecios_listadoDePrecios] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]'";
                //return $this->fn_ejecutarquery($lc_query);
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs[] = array("pr_plu_id" => $row['pr_plu_id'],
                            "cat_id" => $row['cat_id'],
                            "pr_plu_id" => $row['pr_plu_id'],
                            "plu_num_plu" => $row['plu_num_plu'],
                            "cat_descripcion" => utf8_encode(trim($row['cat_descripcion'])),
                            "cat_abreviatura" => utf8_encode(trim($row['cat_abreviatura'])),
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "pr_valor_neto" => $row['pr_valor_neto'],
                            "pr_valor_iva" => $row['pr_valor_iva'],
                            "pr_pvp" => $row['pr_pvp'],
                            "plu_id" => $row['plu_id'],
                            "plu_impuesto" => trim($row['plu_impuesto']));
                    }

                    $this->lc_regs['str'] = $this->fn_numregistro();
                } else {
                    //echo "no";
                }
                return json_encode($this->lc_regs);

            case 'cargaPreciosCategoriasPorMasterPlu':
                $lc_query = "exec [config].[USP_administracionPrecios_PreciosPorMasterPlu] $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cat_id" => $row['cat_id'],
                            "pr_plu_id" => $row['pr_plu_id'],
                            "plu_num_plu" => $row['plu_num_plu'],
                            "cat_descripcion" => utf8_encode(trim($row['cat_descripcion'])),
                            "cat_abreviatura" => utf8_encode(trim($row['cat_abreviatura'])),
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "pr_valor_neto" => $row['pr_valor_neto'],
                            "pr_valor_iva" => $row['pr_valor_iva'],
                            "pr_pvp" => $row['pr_pvp'],
                            "plu_id" => $row['plu_id'],
                            "plu_impuesto" => trim($row['plu_impuesto']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'detalleReportePrecios':
                $lc_query = "exec [config].[USP_administracionPrecios_listaPreciosAcambiar] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                //return $this->fn_ejecutarquery($lc_query);
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("num_plu" => $row['num_plu'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "categoria" => utf8_encode(trim($row['categoria'])),
                            "precio_actual" => $row['precio_actual'],
                            "precio_nuevo" => $row['precio_nuevo']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'previewCiudadesYrestaurantes':
                $lc_query = "exec [config].[USP_administracionPrecios_listaRestaurantes] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])),
                            "rst_cod_tienda" => trim($row['rst_cod_tienda']),
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'traerPreciosUnaCategoria':
                $lc_query = "exec [config].[USP_administracionPrecios_traerPrecios] $lc_datos[1],'$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pr_pvp" => $row['pr_pvp'], "plu_id" => $row['plu_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'cargarCanales':
                $lc_query = "exec [config].[USP_administracionPrecios_listadoDeCanales]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cla_id" => $row['cla_id'],
                            "cla_nombre" => utf8_encode(trim($row['cla_nombre'])));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
        }
    }

    public function aplicarPrecios() {
        $lc_query = "EXEC config.IAE_administracionPrecios_actualizacionDePrecios";
        if ($this->fn_ejecutarquery($lc_query)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs[] = array('mensaje' => $row['mensaje']);
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

}
