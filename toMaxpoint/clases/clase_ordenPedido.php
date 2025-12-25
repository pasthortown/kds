<?php

////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////
///////Fecha Creacion: 06/02/2016 //////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

class menuPedido extends sql {

    function _construct() {
        parent::_construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case 'mostrarBotonCobrarEnEstacionTomaPedido':
                $lc_sql = "EXEC [config].[USP_mostrarBotonCobrarEnEstacionTomaPedido] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mostrarBoton" => $row['mostrarBoton'],
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            case "verificarSiLaOrdenEsDeAgregador":
                $lc_sql = "EXEC dbo.verificarSiLaOrdenEsDeAgregador '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "es_agregador" => $row['es_agregador'],
                        );
                    }
                }
                return json_encode($this->lc_regs);
            case 'obtieneInformacionFormulario':
                $lc_sql = "EXEC [config].[USP_obtieneInformacionFormulario] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDCliente" => $row['IDCliente'],
                            "documento" => $row['documento'],
                            "descripcion" => utf8_encode($row['descripcion']),
                            "direccion" => utf8_encode($row['direccion']),
                            "telefono" => $row['telefono'],
                            "email" => utf8_encode($row['email']),
                            "jsonDatosAdicionales" => utf8_encode($row['jsonDatosAdicionales']),
                            "datos" => ($row['datos'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                case 'insertarOrdenPedidoCupon':
                    $lc_sql = "EXEC [dbo].[IngresarOrdenPedidoCupon] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]'";
                  
                    if ($this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array(
                                "IDCabeceraOrdenPedido" => $row['IDCabeceraOrdenPedido'],
                                "respuesta" => $row['respuesta']
                            
                            );
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);


            case 'actualizarOrdenPedidoApp':

                $new_lc_datos[0] = isset($lc_datos[0]) ? $lc_datos[0] : '';
                $new_lc_datos[1] = isset($lc_datos[1]) ? $lc_datos[1] : '';
                $new_lc_datos[2] = isset($lc_datos[2]) ? $lc_datos[2] : '';
                $new_lc_datos[3] = isset($lc_datos[3]) ? $lc_datos[3] : '';
                $new_lc_datos[4] = isset($lc_datos[4]) ? $lc_datos[4] : '';
                $new_lc_datos[5] = isset($lc_datos[5]) ? $lc_datos[5] : '';
                $new_lc_datos[6] = isset($lc_datos[6]) ? $lc_datos[6] : '';
                $new_lc_datos[7] = isset($lc_datos[7]) ? $lc_datos[7] : '';
                $new_lc_datos[8] = isset($lc_datos[8]) ? $lc_datos[8] : '';
                $new_lc_datos[9] = isset($lc_datos[9]) ? $lc_datos[9] : '';
                $new_lc_datos[10] = isset($lc_datos[10]) ? $lc_datos[10] : '';
                $new_lc_datos[11] = isset($lc_datos[11]) ? $lc_datos[11] : '';
                $new_lc_datos[12] = isset($lc_datos[12]) ? $lc_datos[12] : '';
                $new_lc_datos[13] = isset($lc_datos[13]) ? $lc_datos[13] : '';
                $new_lc_datos[14] = isset($lc_datos[14]) ? $lc_datos[14] : '';
                $new_lc_datos[15] = isset($lc_datos[15]) ? $lc_datos[15] : '';
                $new_lc_datos[16] = isset($lc_datos[16]) ? $lc_datos[16] : '';
                $new_lc_datos[17] = isset($lc_datos[17]) ? $lc_datos[17] : '';

                $lc_sql = "EXEC [facturacion].[USPactualizarOrdenPedidoApp] '$new_lc_datos[0]','".utf8_decode($new_lc_datos[1])."','$new_lc_datos[2]','$new_lc_datos[3]','$new_lc_datos[4]','$new_lc_datos[5]','$new_lc_datos[6]', '$new_lc_datos[7]', '$new_lc_datos[8]', '$new_lc_datos[9]', '$new_lc_datos[10]', '$new_lc_datos[11]', '$new_lc_datos[12]', '$new_lc_datos[13]', '$new_lc_datos[14]', '$new_lc_datos[15]', '$new_lc_datos[17]'";

                try {
                    $this->fn_ejecutarquery($lc_sql);
                } catch (Exception $e) {
                    return json_encode($e->getMessage());
                }
                $cdn_id = $_SESSION["cadenaId"];
                $restaurante = $_SESSION["rstId"];

                $cedula = $new_lc_datos[5];

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => ($row['mensaje'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();

                $ObtenerGuardarCliente = '';

                if (isset($new_lc_datos[16])){
                    $_SESSION['acepta_beneficio'] = $new_lc_datos[16];
                    $ObtenerGuardarCliente = $this->ObtenerGuardarCliente( $cdn_id, $restaurante, $cedula, 'editar', $new_lc_datos, $new_lc_datos[9], $new_lc_datos[10], $new_lc_datos[0] );
                }else if (isset($_SESSION['acepta_beneficio'])){
                    $ObtenerGuardarCliente = $this->ObtenerGuardarCliente( $cdn_id, $restaurante, $cedula, 'editar', $new_lc_datos, $new_lc_datos[9], $new_lc_datos[10], $new_lc_datos[0] );
                }

                if ($ObtenerGuardarCliente != ''){
                    return $ObtenerGuardarCliente;
                }else{
                    return json_encode($this->lc_regs);
                }
            
            case 'actualizarCabOrdPedLatLon':
                $lc_sql = "EXEC [pedido].[IAE_actualiza_latitud_longitud] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                $this->fn_ejecutarquery($lc_sql);
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case 'PayPhoneCargarTipoInmueble':
                $lc_sql = "EXEC [facturacion].[USP_PayPhoneTipoInmuebles] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "value" => ($row['value']),
                            "descripcion" => ($row['descripcion']),
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'PayPhoneFormularioEnOrdenPedido':
                $lc_sql = "EXEC [facturacion].[USP_PayPhoneFormularioEnOrdenPedido] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tienda" => ($row['tienda']),
                            "estado" => (int) ($row['estado'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            case 'cancelarOrdenPedido':
                $lc_sql = "EXEC [fidelizacion].[cancelarOrdenPedido] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "estado" => utf8_decode($row['estado']),
                            "mensaje" => utf8_decode($row['mensaje'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'limite_min_max':
                $lc_sql = "EXEC [config].[USP_ObtenerLimitesMinimoYMaximo] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "minimo" => $row['minimo'],
                            "maximo" => $row['maximo']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'VisualizaBoton':
                $lc_sql = "EXEC [config].[VisualizaBoton] $lc_datos[0] , '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Activo" => $this->ifNum($row['Activo']),
                            "tomaPedido" => $row['tomaPedido']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'obtenerDatosRegresar':
                //$condicion = $lc_datos[2] !== '' ? ",'$lc_datos[2]'" : '';
                $lc_sql = "EXEC [config].[Devuelve_url_FS] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "url" => $row['url']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'verificarPoliticaCodigo':
                $lc_sql = "EXEC [config].[verificarPoliticaCodigoFacturacion] $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Activo" => $this->ifNum($row['Activo'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'ValidaSecuencial':
                $lc_sql = "EXEC [config].[USP_ValidarSecuencialFacturacion] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Respuesta" => $row['Respuesta']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

/*             case 'obtenerDatosRegresar':
                $lc_sql = "EXEC [config].[Devuelve_url_FS] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "url" => $row['url']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs); */

            case 'obtenerDatosRegresarG':
                $lc_sql = "EXEC [config].[Devuelve_url_FS_guardarCuenta] '$lc_datos[0]','','$lc_datos[2]',$lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDPisos" => $row['IDPisos'],
                            "IDAreaPiso" => $row['IDAreaPiso']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'guardarCuenta':
                $lc_sql = "EXEC [config].[Devuelve_url_FS_guardarCuenta] '$lc_datos[0]' ,'$lc_datos[1]','$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDPisos" => $row['IDPisos'],
                            "IDAreaPiso" => $row['IDAreaPiso']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'validacionGuardanOrden':
                $lc_sql = "EXEC [config].[ColeccionRestaurante_ProhibeGuardarOrdenPedido] $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Resultado" => $row['Resultado']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'cargarConfiguracionRestaurante':
                $lc_sql = "EXEC facturacion.TRN_cargar_configuracion_restaurante $lc_datos[0], '$lc_datos[1]','$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tpsrv_descripcion" => $row['tpsrv_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarMenuEstacionDinamico":
                $lc_sql = "EXEC pedido.ORD_cargar_menu_estacion_dinamico " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "'," . $lc_datos[3] . "," . $lc_datos[4] . "";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "menu_id" => $row['menu_id'],
                            "Descripcion" => utf8_decode($row['Descripcion']),
                            "cla_id" => $row['cla_id'],
                            "es_agregador" => $row['es_agregador']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'ObtieneDatosDestino':
                $lc_sql = "EXECUTE facturacion.USP_TRANSFERENCIAVENTA_DatosRestaurante $lc_datos[0],$lc_datos[1],$lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "NombreBdd" => $row['NombreBdd'],
                            "cdn_id" => $row['cdn_id'],
                            "rst_id" => $row['rst_id']);
                    }
                    return $this->lc_regs;
                }
                break;

            case "agregarComentarioOrdenPedido":
                $lc_sql = "EXEC pedido.ORD_agregar_comentario_ordenpedido '" . $lc_datos[0] . "', '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Confirmar'] = 1;
                } else {
                    $this->lc_regs['Confirmar'] = 0;
                }
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Carga Categoría asignada a la estación
              ----------------------------------------------------------------------------------------------------- */
            case "cargarMenuCategoria":
                $lc_sql = "EXEC pedido.ORD_carga_menu_estacion " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mag_id" => $row['mag_id'],
                            "mag_descripcion" => utf8_decode($row['mag_descripcion']),
                            "mag_colortexto" => $row['mag_colortexto'],
                            "mag_color" => $row['mag_color'],
                            "mag_orden" => $this->ifNum($row['mag_orden'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs,JSON_INVALID_UTF8_IGNORE);

            /* ----------------------------------------------------------------------------------------------------
              Funcion Agregar Comentario
              ----------------------------------------------------------------------------------------------------- */
            case "insertarComentario":
                $lc_sql = "EXEC pedido.ORD_IAE_comentario_ordenpedido 'i', '" . $lc_datos[0] . "', 0, '" . $lc_datos[2] . "', '" . $lc_datos[1] . "', '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $this->ifNum($row['plu_id']),
                            "dop_cantidad" => $this->ifNum($row['dop_cantidad']),
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => $this->ifNum($row['dop_iva']),
                            "dop_total" => $this->ifNum($row['dop_total']),
                            "dop_precio_unitario" => $this->ifNum($row['dop_precio_unitario']),
                            "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                            "plu_anulacion" => $this->ifNum($row['plu_anulacion']),
                            "plu_gramo" => $this->ifNum($row['plu_gramo']),
                            "tipo" => $this->ifNum($row['tipo']),
                            "ancestro" => $row['ancestro'],
                            "plus_puntos" => $this->ifNum($row['Detalle_Orden_PedidoVarchar1']),
                            "puntos" => $this->ifNum($row['puntos']),
                            "tipoBeneficioCupon" => $this->ifNum($row['tipoBeneficioCupon']),
                            "colorBeneficioCupon" => $row['colorBeneficioCupon']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Activa la grilla de productos en pantalla
              Función de llamada: fn_activarProductos(id)
              ----------------------------------------------------------------------------------------------------- */
            case "cargarProducto":
                $lc_sql = "EXEC pedido.ORD_cargarplusporcategoria " . $lc_datos[4] . ", '" . $lc_datos[0] . "', '" . $lc_datos[3] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            'plu_descripcion' => utf8_encode(trim($row['plu_descripcion'])),
                            'magp_desc_impresion' => utf8_encode(trim($row['magp_desc_impresion'])),
                            'magp_colortexto' => $row['magp_colortexto'],
                            'magp_color' => $row['magp_color'],
                            'plu_id' =>(int) $row['plu_id'],
                            'magp_id' => $row['magp_id'],
                            'mag_id' => $row['mag_id'],
                            'magp_orden' => $row['magp_orden'],
                            'plu_gramo' =>(float) $row['plu_gramo'],
                            'std_fecha' =>(int) $row['std_fecha'],
                            'validador' =>(int) $row['validador'],
                            'puntos' =>(int) $row['puntos'],
                            'pvp' =>(float) $row['pvp'],
                            'productoUpSelling' => $row['productoUpSelling'],
                            'jsonMejoraProducto' => ($row['jsonMejoraProducto'] == '') ? 0 : utf8_encode(trim($row['jsonMejoraProducto'])),
                            'pluAplicaPicada' => $row['pluAplicaPicada']
                        );
                    }
                }

                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Activa la grilla de productos en pantalla
              ----------------------------------------------------------------------------------------------------- */
            case "cargarProductoBuscador":
                $lc_sql = "EXEC pedido.ORD_cargar_plusbuscador " . $lc_datos[5] . ", '" . $lc_datos[0] . "', '" . $lc_datos[4] . "', " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "plu_descripcion" => $row['plu_descripcion'],
                            "magp_desc_impresion" => utf8_decode(trim($row['magp_desc_impresion'])),
                            "magp_colortexto" => $row['magp_colortexto'],
                            "magp_color" => $row['magp_color'],
                            "plu_id" =>(int) $row['plu_id'],
                            "magp_id" => $row['magp_id'],
                            "mag_id" => $row['mag_id'],
                            "magp_orden" => $row['magp_orden'],
                            "plu_gramo" =>(float) $row['plu_gramo'],
                            "std_fecha" =>(int) $row['std_fecha'],
                            "validador" =>(int) $row['validador'],
                            "puntos" =>(int) $row['puntos'],
                            "pvp" =>(float) $row['pvp'],
                            "productoUpSelling" => $row['productoUpSelling'],
                            "jsonMejoraProducto" => ($row['jsonMejoraProducto'] == '') ? 0 : $row['jsonMejoraProducto']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica si existe un elemento ya agregado a la lista
              ----------------------------------------------------------------------------------------------------- */
            case "agregarPlusOrdenPedido":
                $lc_sql = "EXEC pedido.ORD_agregar_pluordenpedido '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],$lc_datos[3],'$lc_datos[4]','$lc_datos[6]', '$lc_datos[7]','$lc_datos[5]'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $this->ifNum($row['plu_id']),
                            "dop_cantidad" => $this->ifNum($row['dop_cantidad']),
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => ROUND($row['dop_iva'], 2),
                            "dop_total" => ROUND($row['dop_total'], 2),
                            "dop_precio_unitario" => ROUND($row['dop_precio_unitario'], 2),
                            "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                            "plu_anulacion" => $this->ifNum($row['plu_anulacion']),
                            "plu_gramo" => $this->ifNum($row['plu_gramo']),
                            "tipo" => $this->ifNum($row['tipo']),
                            "ancestro" => $row['ancestro'],
                            "plus_puntos" => $row['Detalle_Orden_PedidoVarchar1'],
                            "puntos" => $row['puntos'],
                            "tipoBeneficioCupon" => $row['tipoBeneficioCupon'],
                            "colorBeneficioCupon" => $row['colorBeneficioCupon']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica si existe un elemento ya agregado a la lista Full services
              ----------------------------------------------------------------------------------------------------- */
            case "agregarPlusOrdenPedido_FS":
                $lc_sql = "EXEC pedido.ORD_agregar_pluordenpedido_FS '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], $lc_datos[3],'$lc_datos[4]',$lc_datos[5]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_desc_impresion" => utf8_decode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $row['plu_id'],
                            "dop_cantidad" => $row['dop_cantidad'],
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => ROUND($row['dop_iva'], 2),
                            "dop_total" => ROUND($row['dop_total'], 2),
                            "dop_precio_unitario" => ROUND($row['dop_precio'], 2),
                            "plu_impuesto" => $row['plu_impuesto'],
                            "plu_anulacion" => $row['plu_anulacion'],
                            "plu_gramo" => $row['plu_gramo'],
                            "tipo" => $row['tipo'],
                            "ancestro" => $row['ancestro']/* ,
                                  "validador" => $row['validador'] */);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica si el plu tiene atado una pregunta sugerida
              ----------------------------------------------------------------------------------------------------- */
            case "verificarPreguntasSugerida":
                $lc_sql = "EXEC pedido.ORD_verificar_preguntassugeridas '$lc_datos[0]',$lc_datos[1],'$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("psug_id" => $row['psug_id'],
                            "pre_sug_descripcion" => mb_convert_encoding(trim($row['pre_sug_descripcion']), 'UTF-8', mb_list_encodings()),
                            "psug_resp_minima" => $row['psug_resp_minima'],
                            "psug_resp_maxima" => $row['psug_resp_maxima']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs,JSON_INVALID_UTF8_IGNORE);

            /* ----------------------------------------------------------------------------------------------------
              Para validar el Plu Preferido para la Separación de Presas.
              ----------------------------------------------------------------------------------------------------- */
            case "pluSeparadorPresas":
                $lc_sql = "EXEC pedido.ORD_separacionpresas_separador";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "distri" => $row['variableV'],
                            "base" => $row['variableI'],
                            "percentA" => $row['min'],
                            "percentB" => $row['max']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Para obtener los plu_id para la Separación de Presas a partes iguales y su distribución.
              ----------------------------------------------------------------------------------------------------- */
            case "pluIdSeparadorPresas":
                $lc_sql = "EXEC pedido.ORD_separacionpresas_pluseparador '" . $lc_datos[0] . "', ". $lc_datos[1];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "plupre" => $row['variableI'],
                            "cntP"   => $row['NumP']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              inserta en la canal movimiento el requerimiento para el Qsr
              ----------------------------------------------------------------------------------------------------- */
            case "insertaQsr":
                $lc_sql = "EXEC pedido.ORD_inserta_ordenpedido_dispositivo_qsr " . $lc_datos[0] . ",'" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            /* ----------------------------------------------------------------------------------------------------
              Devuelve las opciones de la pregunta sugerida
              ----------------------------------------------------------------------------------------------------- */
            case "verificarRespuestaPreguntasSugeridas":
                $lc_sql = "EXEC pedido.ORD_cargar_respuestaspreguntassugeridas '" . $lc_datos[0] . "', '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "plu_respuesta" => $row['plu_id'],
                            "res_descripcion" => utf8_decode($row['res_descripcion']),
                            "IDRespuestas" => $row['IDRespuestas'],
                            "puntos" => $row['puntos']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Impresion Orden Pedido Cupon
              ----------------------------------------------------------------------------------------------------- */
              case "impresionDetalleCuponOrdenPedido":
                if ($lc_datos[20] == 1): // verifica si el api de impresion esta activo
                   $lc_sql = " EXEC pedido.ORD_registrar_cupon_canjeado '" . $lc_datos[2] . "', '" . $lc_datos[0] . "', '" . $lc_datos[5] . "', " . $lc_datos[6] . ", " . $lc_datos[7] . ", " . $lc_datos[8] . ", " . $lc_datos[9] . ", " . $lc_datos[10] . ", '" . $lc_datos[11] . "', " . $lc_datos[12] .", '" . $lc_datos[14] . "', '" .$lc_datos[15]. "', '" .$lc_datos[16]. "', '" .$lc_datos[17]. "', '" .$lc_datos[18]. "', '" .$lc_datos[19]. "', '" .$lc_datos[21]. "'";
                   $this->fn_ejecutarquery($lc_sql);
                   $this->lc_regs[] = array("Confirmar" => 1);
                else:
                    $lc_sql = " EXEC pedido.ORD_registrar_cupon_canjeado '" . $lc_datos[2] . "', '" . $lc_datos[0] . "', '" . $lc_datos[5] . "', " . $lc_datos[6] . ", " . $lc_datos[7] . ", " . $lc_datos[8] . ", " . $lc_datos[9] . ", " . $lc_datos[10] . ", '" . $lc_datos[11] . "', " . $lc_datos[12] .", '" . $lc_datos[14] . "', '" .$lc_datos[15]. "', '" .$lc_datos[16]. "', '" .$lc_datos[17]. "', '" .$lc_datos[18]. "', '" .$lc_datos[19]. "', '" .$lc_datos[21]. "';
                    EXEC pedido.ORD_impresion_ordenpedido_cupon '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[13] . "', " . $lc_datos[3] . ", '" . $lc_datos[4] . "', " . $lc_datos[22] . ";";                            
                    if ($this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                        }
                    }
                $this->lc_regs['str'] = $this->fn_numregistro();
                endif;

                return json_encode($this->lc_regs);
            /* ----------------------------------------------------------------------------------------------------
              Impresion Orden Pedido
              ----------------------------------------------------------------------------------------------------- */
            case "impresionOrdenPedido":
                $lc_sql = " EXEC pedido.ORD_impresion_ordenpedido  '$lc_datos[1]','$lc_datos[2]',$lc_datos[3],$lc_datos[4],$lc_datos[5]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $this->ifNum($row['Confirmar']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Impresion Todas las Orden Pedido
              ----------------------------------------------------------------------------------------------------- */
            case "impresionOrdenPedidoTodas":
                $lc_sql = " EXEC pedido.ORD_impresion_ordenpedido  '$lc_datos[1]','$lc_datos[2]',$lc_datos[3],$lc_datos[4],$lc_datos[5],$lc_datos[6]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Impresion Precuenta
              ----------------------------------------------------------------------------------------------------- */
            case "impresionPrecuenta":
                $lc_sql = " EXEC pedido.ORD_impresion_precuenta '$lc_datos[1]','$lc_datos[2]','$lc_datos[0]',$lc_datos[3],$lc_datos[4],'$lc_datos[5]', $lc_datos[6]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $this->ifNum($row['Confirmar']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica si el producto a eliminar es el último
              Función de llamada: fn_eliminarElemento()
              ----------------------------------------------------------------------------------------------------- */
            case "verificarUltimoElemento":
                $lc_sql = "EXEC pedido.ORD_verificar_ultimo_elemento_eliminar '" . $lc_datos[0] . "', " . $lc_datos[1];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                if (isset($lc_datos[2]) && $lc_datos[2] != ''){
                    $_SESSION['acepta_beneficio'] = 0;
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Elimina el último producto de la orden
              Función de llamada: fn_eliminarElemento()
              ----------------------------------------------------------------------------------------------------- */
            case "eliminarUltimoElemento":
                $lc_sql = "EXEC pedido.ORD_elimina_pluordenpedido 1, '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', " . $lc_datos[2] . ", " . $lc_datos[3] . ", " . $lc_datos[4] . ", '" . $lc_datos[5] . "', " . $lc_datos[6];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "plu_id" => $row['plu_id'], "dop_cantidad" => $row['dop_cantidad'], "dop_id" => $row['dop_id'], "dop_iva" => ROUND($row['dop_iva'], 2), "dop_total" => ROUND($row['dop_total'], 2), "dop_precio_unitario" => ROUND($row['dop_precio'], 2), "plu_impuesto" => $row['plu_impuesto']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "estaImpreso":
                $lc_sql = "EXEC pedido.EstaImpreso  '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("respuesta" => $row['respuesta']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Elimina un producto de la orden
              Función de llamada: fn_eliminarElemento()
              ----------------------------------------------------------------------------------------------------- */
            case "eliminarunElemento":
                $lc_sql = "EXEC pedido.ORD_elimina_pluordenpedido 2, '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', " . $lc_datos[2] . ", " . $lc_datos[3] . ", " . $lc_datos[4] . ", '" . $lc_datos[5] . "', " . $lc_datos[6];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Resultado" => $this->ifNum($row['Resultado']),
                            "magp_desc_impresion" => utf8_decode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $this->ifNum($row['plu_id']),
                            "dop_cantidad" => $this->ifNum($row['dop_cantidad']),
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => ROUND($row['dop_iva'], 2),
                            "dop_total" => ROUND($row['dop_total'], 2),
                            "dop_precio_unitario" => ROUND($row['dop_precio'], 2),
                            "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                            "plus_puntos" => $this->ifNum($row['Detalle_Orden_PedidoVarchar1']),
                            "puntos" => $this->ifNum($row['puntos'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Carga inicial de la pantalla con la orden de pedido
              Función de llamada: $(document).ready(function()
              ----------------------------------------------------------------------------------------------------- */
            case "configuracionOrdenPedido":
                $lc_sql = " EXEC pedido.ORD_configuracion_proceso_ordenpedido " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', " . $lc_datos[4] . "," . $lc_datos[5];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "existe" => (int) $row['existe'],
                            "tipo_impuesto" => $row['tipo_impuesto'],
                            "tipo_cantidad" => (int) $row['tipo_cantidad'],
                            "odp_id" => $row['odp_id'],
                            "rst_tiempopedido" => (int) $row['rst_tiempopedido'],
                            "cat_id" => $row['cat_id'],
                            "fecha_periodo" => $row['fecha_periodo'],
                            "fecha" => $row['fecha'],
                            "horaServidor" => $row['horaServidor'],
                            "observacion" => utf8_encode($row['observacion']),
                            "nombreMesa" => $row['nombreMesa'],
                            "palabraDefault" => $row['palabraDefault'],
                            "solicitarInicio" => utf8_encode(trim($row['solicitarInicio']))
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Carga la lista de productos pendiente
              Función de llamada: fn_listaPendiente()
              ----------------------------------------------------------------------------------------------------- */

            case "cargar_ordenPedidoPendiente":
                $lc_sql = "EXEC pedido.ORD_cargar_ordenpedido_pendiente  $lc_datos[0], '$lc_datos[1]','$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $this->ifNum($row['plu_id']),
                            "dop_cantidad" => $this->ifNum($row['dop_cantidad']),
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => $this->ifNum($row['dop_iva']),
                            "dop_total" => $this->ifNum($row['dop_total']),
                            "dop_precio_unitario" => $this->ifNum($row['dop_precio_unitario']),
                            "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                            "plu_anulacion" => $this->ifNum($row['plu_anulacion']),
                            "plu_gramo" => $this->ifNum($row['plu_gramo']),
                            "tipo" => $this->ifNum($row['tipo']),
                            "ancestro" => $row['ancestro'],
                            "plus_puntos" => $this->ifNum($row['Detalle_Orden_PedidoVarchar1']),
                            "puntos" => $this->ifNum($row['puntos']),
                            "tipoBeneficioCupon" => $this->ifNum($row['tipoBeneficioCupon']),
                            "colorBeneficioCupon" => $row['colorBeneficioCupon'],
                            "notasKDS" => isset($row['notasKDS']) ? utf8_encode(trim($row['notasKDS'])) : ''
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Carga la lista de productos pendiente full services
              ----------------------------------------------------------------------------------------------------- */

            case "cargar_ordenPedidoPendiente_FS":
                $lc_sql = "EXEC pedido.ORD_cargar_ordenpedido_pendiente_FS " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'," . $lc_datos[3];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $row['plu_id'],
                            "dop_cantidad" => $row['dop_cantidad'],
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => $row['dop_iva'],
                            "dop_total" => $row['dop_total'],
                            "dop_precio_unitario" => $row['dop_precio_unitario'],
                            "plu_impuesto" => $row['plu_impuesto'],
                            "plu_anulacion" => $row['plu_anulacion'],
                            "plu_gramo" => $row['plu_gramo'],
                            "tipo" => $row['tipo'],
                            "ancestro" => $row['ancestro'],
                            "dop_cuenta" => $row['dop_cuenta'],
                            "notasKDS" => isset($row['notasKDS']) ? utf8_encode(trim($row['notasKDS'])) : ''
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Funcion para eliminar un texto ingresado
              ----------------------------------------------------------------------------------------------------- */
            case "eliminarTextoPlu":
                $lc_sql = "EXEC pedido.ORD_IAE_comentario_ordenpedido 'e', '$lc_datos[1]', 0, '', '$lc_datos[0]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Confirmar'] = 1;
                } else {
                    $this->lc_regs['Confirmar'] = 0;
                }
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Valida las credenciales del usuario administrador para la anulación de un producto
              Función de llamada: fn_validarUsuario()
              ----------------------------------------------------------------------------------------------------- */
            case "validarUsuario":
                $lc_sql = "EXEC pedido.ORD_validar_usuario_administrador " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("usr_id" => $row['usr_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Valida el tiempo del producto para la anulación
              Función de llamada: fn_validarTiempo(plu_id)
              ----------------------------------------------------------------------------------------------------- */
            case "validarTiempoPlu":
                $lc_sql = "EXEC pedido.ORD_cargar_tiempo_ordenpedido_plu '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_creacionfecha" => $this->ifNum($row['plu_creacionfecha']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Carga los productos desde el lector de barras
              Función de llamada: fn_lectorBarras()
              ----------------------------------------------------------------------------------------------------- */
            case "lectorBarras":
                $lc_sql = "EXEC pedido.ORD_cargar_plu_lectorbarras $lc_datos[0], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "bandera" => $row['bandera'],
                            "mensaje" => $row['mensaje'],
                            "plu_id" => intval($row['plu_id']),
                            "magp_id" => $row['magp_id'],
                            "validador" => intval($row['validador']),
                            "cantidad" => floatval($row['cantidad']),
                            "ProductoAlPeso" => floatval($row['ProductoAlPeso']),
                            "ventaAlPeso" => floatval($row['ventaAlPeso']),
                            "producto" => utf8_encode(trim($row['producto'])),
                            "pvp" => floatval($row['pvp']),
                            "productoUpSelling" => $row['productoUpSelling'],
                            "jsonMejoraProducto" => ($row['jsonMejoraProducto'] == '') ? 0 : $row['jsonMejoraProducto']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Obtiene una mesa en caso de que el servicio sea Fast Food
              Función de llamada: fn_obtenerMesa(rst_id)
              Para servicio FastFood se verifican que existan mesas y de no existir crea una nueva y la asigna.
              ----------------------------------------------------------------------------------------------------- */
            case "obtenerMesa":
                $estacion = $lc_datos[1];

                $lc_sql = "EXEC  [pedido].[ORD_asignar_mesa]" . $lc_datos[0] . ", $estacion,''";
                //$lc_sql = "EXEC  [pedido].[ORD_asignar_mesa]" . $lc_datos[0] . ", '" . $lc_datos[1] . "',''";


                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mesa_asignada" => $row['mesa_asignada']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();

                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Obtiene la ip de la estación
              Función de llamada: fn_ipEstacion()
              ----------------------------------------------------------------------------------------------------- */
            case "ipEstacion":
                $lc_sql = "SELECT est_ip FROM Estacion WHERE est_id=$lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("est_ip" => $row['est_ip']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /*
              valida si la cadena tiene productos para llevar
             */
            case "validaLlevarProductos":
                $lc_sql = "EXEC ORD_Valida_Configuracion " . $lc_datos[0];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "existe" => $row["existe"],
                            "Tipo_Cantidad" => $row["Tipo_Cantidad"]
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica los canales de impresión de la orden de pedido
              Función de llamada: fn_canalesImpresion()
              ----------------------------------------------------------------------------------------------------- */
            case "canalesImpresion":
                $lc_sql = "EXEC pedido.ORD_verificar_canalimpresion_ordenpedido '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cprn_id" => $row['cprn_id'],
                            "cprn_descripcion" => $row['cprn_descripcion']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Envia la impresión de la orden de pedido
              Función de llamada: fn_canalesImpresion()
              ----------------------------------------------------------------------------------------------------- */
            case "enviarImpresion":
                $lc_sql = "INSERT INTO Canal_Movimiento (imp_ip_estacion, imp_fecha, imp_url, imp_impresora, usr_id, tca_codigo, std_id) VALUES ('$lc_datos[0]', SYSDATETIME(), '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', 200, 41);
						SELECT MAX(imp_id) AS impresion FROM Canal_Movimiento WHERE imp_ip_estacion = '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("impresion" => $row['impresion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* ----------------------------------------------------------------------------------------------------
              Verifica el perfil del usuario.
              ----------------------------------------------------------------------------------------------------- */
            case "validaAdministradorBuscar":
                $lc_sql = "EXEC pedido.USP_permisosBuscador '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs["valida"] = $row["valida"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarAccesosPerfil":
                $lc_sql = "EXEC config.USP_verificanivelacceso '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "acc_id" => $row['acc_id'],
                            "acc_descripcion" => trim($row['acc_descripcion'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "agregarPreguntaSugerida":
                $lc_sql = "EXEC pedido.ORD_agregar_plus_preguntassugeridas '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', " . $lc_datos[3] . ', ' . $lc_datos[4] . ', ' . $lc_datos[5] . ", " . $lc_datos[6] . ", '" . $lc_datos[7] . "', '" . $lc_datos[8] . "' ";
                if ($this->fn_ejecutarquery($lc_sql)) {

                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $this->ifNum($row['plu_id']),
                            "dop_cantidad" => $this->ifNum($row['dop_cantidad']),
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => $this->ifNum($row['dop_iva']),
                            "dop_total" => $this->ifNum($row['dop_total']),
                            "dop_precio_unitario" => $this->ifNum($row['dop_precio_unitario']),
                            "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                            "plu_anulacion" => $this->ifNum($row['plu_anulacion']),
                            "plu_gramo" => $this->ifNum($this->ifNum($row['plu_gramo'])),
                            "tipo" => $this->ifNum($row['tipo']),
                            "ancestro" => $row['ancestro'],
                            "plus_puntos" => $this->ifNum($row['Detalle_Orden_PedidoVarchar1']),
                            "puntos" => $this->ifNum($row['puntos']),
                            "tipoBeneficioCupon" => $row['tipoBeneficioCupon'],
                            "colorBeneficioCupon" => $row['colorBeneficioCupon']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "agregarPreguntaSugerida_FS":
                $lc_sql = "EXEC pedido.ORD_agregar_plus_preguntassugeridas '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "' , " . $lc_datos[3] . ',' . $lc_datos[4] . ',' . $lc_datos[5] . ", " . $lc_datos[6] . " ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_id" => $row['plu_id'],
                            "dop_cantidad" => $row['dop_cantidad'],
                            "dop_id" => $row['dop_id'],
                            "dop_iva" => $row['dop_iva'],
                            "dop_total" => $row['dop_total'],
                            "dop_precio_unitario" => $row['dop_precio'],
                            "plu_impuesto" => $row['plu_impuesto'],
                            "plu_anulacion" => $row['plu_anulacion'],
                            "plu_gramo" => $row['plu_gramo'],
                            "tipo" => $row['tipo'],
                            "ancestro" => $row['ancestro']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "buscarValidacionesPLU":
                $validaciones = array();
                $valorMinimo = 0;
                $aplica = 0;
                //$lc_datos[0] es el ID del producto
                //$lc_datos[1] es el ID de la cadena
                $lc_sql = "EXEC pedido.USP_DatosValidacionesPlus " . $lc_datos[0] . ", " . $lc_datos[1];
                //echo($lc_sql);
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $descripciones = array();
                    while ($row = $this->fn_leerarreglo()) {
                        $descripciones[] = utf8_decode($row["descripcion"]);
                        if ("GO TRADE" == $row["descripcion"]) {
                            $valorMinimo = $row["valorMinimo"];
                        }
//                        $validaciones[]=$row;
                        if ( "CUPONES DESCUENTO" == $row["descripcion"] ) {
                            $aplica = $row["aplica"];
                        }
                    }
                }
                $this->lc_regs['valorMinimo'] = $valorMinimo;
                $this->lc_regs['validaciones'] = $descripciones;
                $this->lc_regs['aplica'] = $aplica;
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "split_cuentas":
                $lc_sql = "EXEC config.IAE_estadosMesa '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {

                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "informacionMesa":
                $lc_sql = "EXEC config.USP_InformacionMesas '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "' ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cantidad_splits" => $row['cantidad_splits'],
                            "numero_personas" => $row['numero_personas'],
                            "tipo_mesa" => $row['tipo_mesa'],
                            "mi_mesa" => $row['mi_mesa'],
                            "estado" => $row['estado']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "informacionMesaAll":
                $lc_sql = "EXEC config.USP_CargaInformacionPorMesa '" . $lc_datos[0] . "'  ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "numero_transaccion" => utf8_decode($row['numero_transaccion']),
                            "transaccion_info" => utf8_decode($row['transaccion_info']),
                            "last_order" => utf8_decode($row['last_order']),
                            "final_total" => $row['final_total'],
                            "observacion" => utf8_decode($row['observacion'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "dividirProductos":
                $lc_sql = "EXEC [config].[IAE_separacion_cuentas_por_personas]    $lc_datos[0],'$lc_datos[1]','$lc_datos[2]',$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    return 1;
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);


            case "politicaPayCard":
                $usuarioId = $_SESSION['usuarioId'];
                $lc_sql_politica = "EXEC [config].[USP_PayCard] 1, $lc_datos[0],'$usuarioId','$lc_datos[1]'";
                //print_r($lc_sql_politica);
                if ($this->fn_ejecutarquery($lc_sql_politica)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs_politica['mensaje']  = utf8_encode($row['mensaje']);
                    }
                }
                $this->lc_regs_politica['str'] = $this->fn_numregistro();
                if($this->lc_regs_politica['str'] > 0){
                    $descripcion = $this->lc_regs_politica['mensaje'];
                    $consulta = "EXEC [seguridad].[IAE_Audit_registro] 'i','$usuarioId',0,'MENU','$descripcion','PAYCARD'";
                    $ejecucion = $this->fn_ejecutarquery($consulta);
                }
                //print_r($this->lc_regs_politica);
                return json_encode($this->lc_regs_politica);
                //print_r($this->lc_regs_politica);
                break;
            case "politicaPayCardRestaurante":
                    $usuarioId = $_SESSION['usuarioId'];
                    $lc_sql_politica = "EXEC [config].[USP_PayCard_Restaurante] 1, $lc_datos[0],'$usuarioId','$lc_datos[1]'";
                    //print_r($lc_sql_politica);
                    if ($this->fn_ejecutarquery($lc_sql_politica)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs_politica['mensaje']  = utf8_encode($row['mensaje']);
                        }
                    }
                    $this->lc_regs_politica['str'] = $this->fn_numregistro();
                    if($this->lc_regs_politica['str'] > 0){
                        $descripcion = $this->lc_regs_politica['mensaje'];
                        $consulta = "EXEC [seguridad].[IAE_Audit_registro] 'i','$usuarioId',0,'MENU','$descripcion','PAYCARD_RESTAURANTE'";
                        $ejecucion = $this->fn_ejecutarquery($consulta);
                    }
                    //print_r($this->lc_regs_politica);
                    return json_encode($this->lc_regs_politica);
                    //print_r($this->lc_regs_politica);
                    break;
            
            
            case "obtenerProductoPaycard":
                $usuarioId = $_SESSION['usuarioId'];
                $lc_sql = "EXEC [config].[IAE_PayCard] 2, $lc_datos[0],'$usuarioId','$lc_datos[1]'";
                //print_r($lc_sql);
                //todo: Guillermo pendiente revisar query EXEC [config].[IAE_PayCard] 2, 8,'7044D0A5-5CAE-E911-80E2-000D3A019254','false'
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $row = $this->fn_leerarreglo();
                    $this->lc_regs['mensaje'] = utf8_encode($row['mensaje']);
                    //$this->lc_regs['menu_Nombre'] = utf8_encode($row['nombreMenu']);
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                //print_r($this->lc_regs);
                break;
            case "insertaAuditoria":
                $lc_sql = "EXEC [seguridad].[IAE_Audit_registro] 'i','" . $lc_datos[0] . "',0,'FACTURACION','" . $lc_datos[1] . "','INFO'";
                $ejecucion = $this->fn_ejecutarquery($lc_sql);
                return $ejecucion;  
            case "retorna_WS_URL_Cashless":
                $lc_sql = "EXEC config.USP_PayCard_Restaurante 3," . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $row = $this->fn_leerarreglo();
                    $this->lc_regs["datos"] = [
                        "urlwebservice" => utf8_decode($row[0])
                    ];
                }
                return $this->lc_regs["datos"];
            
            case "retorna_cedula_user":
                $lc_sql = "EXEC config.USP_PayCard_Restaurante 4," . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $row = $this->fn_leerarreglo();
                    $this->lc_regs["datos"] = [
                    "cedula_user" => utf8_decode($row[0])
                    ];
                }
                return $this->lc_regs["datos"];


        }
    }

    function fn_validaFechaAtencionPeriodo($lc_datos) {
        $lc_sql = " EXEC seguridad.USP_validaAperturaPeriodo  '$lc_datos[0] ', '$lc_datos[1]', '$lc_datos[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "respuesta" => $this->ifNum($row['respuesta']),
                    "cierrePeriodo" => $this->ifNum($row['cierrePeriodo']),
                    "fechaAperturaPeriodo" => $row['fechaAperturaPeriodo']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_ActualizaEstadosDop($opcion, $lc_datos) {
        switch ($opcion) {

            case "insertaOrdenPedidoFidelizacion":
                $lc_sql = "EXEC [fidelizacion].actualizaOrdenPedidoFidelizcionActiva  '" . $lc_datos[0] . "','" . $lc_datos[1] . "'   ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => ($row['mensaje']),
                            "estado" => $row['estado']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "FiltroQueMeAlcanza":
                $lc_sql = "EXEC [pedido].FIDELIZACION_buscarProductosPuntosCliente_listado  " . $lc_datos[0] . ",'" . $lc_datos[1] . "'  , " . $lc_datos[2] . " ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_descripcion" => utf8_decode($row['plu_descripcion']),
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "magp_colortexto" => $row['magp_colortexto'],
                            "magp_color" => $row['magp_color'],
                            "plu_id" => $row['plu_id'],
                            "magp_id" => $row['magp_id'],
                            "mag_id" => $row['mag_id'],
                            "magp_orden" => $row['magp_orden'],
                            "plu_gramo" => $row['plu_gramo'],
                            "std_fecha" => $row['std_fecha'],
                            "validador" => $row['validador'],
                            "puntos" => $row['puntos']
                        );
                    }
                }

                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "queAlcanza":
                $lc_sql = "EXEC config.USP_PLUS_CONFIGURACION_FIDELIZACION_CLIENTES  " . $lc_datos[0] . " ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "ID_ColeccionDeDatosPlus" => ($row['ID_ColeccionDeDatosPlus']),
                            "plu_id" => ($row['plu_id']),
                            "puntos" => $row['puntos']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'ActualizaEstadosDop':
                $lc_sql = "EXEC [pedido].[IAE_Actualiza_Consulta_Orden] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
                $this->fn_ejecutarquery($lc_sql);
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'CargaNumPersonas':
                $lc_sql = "EXEC [pedido].[IAE_Actualiza_Consulta_Orden] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("odp_num_personas" => $row['odp_num_personas']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'obtienePatronesVocuher':
                $lc_sql = "EXEC [config].[USP_OBTIENE_INFORMACION_VOUCHER] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDColeccionDeDatosPais" => $row['IDColeccionDeDatosPais'],
                            "IDColeccionPais" => $row['IDColeccionPais'],
                            "pais_id" => $row['pais_id'],
                            "caracter" => $row['variableV'],
                            "incidencias" => $row['variableI']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'obtieneClienteSegunPatronesVocuher':
                $lc_sql = "EXEC [config].[USP_OBTIENE_INFORMACION_VOUCHER] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDCliente" => $row['IDCliente'],
                            "cli_nombres" => utf8_encode($row['cli_nombres']),
                            "IDTipoDocumento" => utf8_encode($row['IDTipoDocumento']),
                            "cli_documento" => utf8_encode($row['cli_documento']),
                            "cli_direccion" => utf8_encode($row['cli_direccion']),
                            "cli_email" => utf8_encode($row['cli_email']),
                            "cli_telefono" => utf8_encode($row['cli_telefono']),
                            "cli_tipo_cliente" => utf8_encode($row['cli_tipo_cliente'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'obtenerTiposdeCupo':
                $lc_sql = "EXEC [config].[USP_Voucher_obtieneTipoCupo] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDColeccionDeDatosPais" => $row['IDColeccionDeDatosPais'],
                            "IDColeccionPais" => utf8_encode($row['IDColeccionPais']),
                            "tipo_cupo" => utf8_encode($row['tipo_cupo']),
                            "cupoVip" => utf8_encode($row['cupoVip']),
                            "maxCupo" => utf8_encode($row['maxCupo'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'guadarDescripcionVoucher':
                $lc_sql = "[config].[IAE_guardarInformacionVoucher] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => utf8_encode($row['mensaje'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function fn_ValidarPromocion($opcion, $lc_datos) {
        switch ($opcion) {
            case 'ValidarPromocion':
                $lc_sql = "EXEC [promociones].[USP_ValidacionPromocionOrdenFactura] '$lc_datos[0]','$lc_datos[1]',$lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "estado" => ($row['estado']),
                            "nombre" => utf8_decode($row['nombre']),
                            "mensaje" => utf8_encode($row['mensaje'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function fn_transferirMesas($opcion, $lc_datos) {
        switch ($opcion) {
            case 'transferirMesas':
                $lc_sql = "EXEC [config].[IAE_TransferenciaMesas] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => utf8_encode($row['mensaje'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function visualizarCantidadProducto($lc_datos) {
        $lc_sql = "EXEC pedido.ORD_USP_VisualizaCantidadProductos '$lc_datos[0]', '$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["visualizarCantidad"] = $row["visualizarCantidad"];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_VerificarEstadoTipoVenta($restaurante) {
        $lc_sql = "EXEC  [config].[VisualizaTipoVenta] $restaurante";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["Activo"] =(int) $row["Activo"];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_ConsultarCanalVenta($restaurante) {
        $lc_sql = "EXEC  [config].[USP_ConsultarCanalTipoPedido] $restaurante";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idIntegracion" => $row['idIntegracion'],
                    "Descripcion" => utf8_encode($row['Descripcion'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_insertarTipoPedido($restaurante, $idcabecerapedido, $idTipoPedido) {
        $lc_sql = "[config].[IAE_InsertarCanalTipoPedido] $restaurante,'$idcabecerapedido','$idTipoPedido'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["Respuesta"] = $row["Respuesta"];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }


    function fn_insertarCodigoBarras($idcabecerapedido,$codigoBarras) {
        $lc_sql = "[config].[IAE_InsertarCodigoBarras] '$idcabecerapedido','$codigoBarras'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["Respuesta"] = $row["Respuesta"];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    function fn_borrarCodigoBarras($idcabecerapedido,$codigoBarras) {
        $lc_sql = "[config].[IAE_EliminarCodigoBarras] '$idcabecerapedido','$codigoBarras'";
        try {
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs["Respuesta"] = $row["Respuesta"];
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }catch (Exception $e){
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }

    function solicitudHorneado($accion, $idCadena, $plu_id, $idControlEstacion, $idEstacion) {
        $lc_sql = "EXEC [pedido].[UPS_SolicitudProductosHorneados]  $accion, $idCadena, $plu_id, '$idControlEstacion', '$idEstacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "plu_id" => $this->ifNum($row['plu_id']),
                    "aplica" => $this->ifNum($row['aplica']),
                    "ruta" => $row['ruta'],
                    "idRestaurante" => $this->ifNum($row['idRestaurante']),
                    "ipEstacion" => $row['ipEstacion'],
                    "impresora" => $row['impresora'],
                    "cajero" => $row['cajero'],
                    "tienda" => $row['tienda'],
                    "servidor" => $row['servidor'],
                    "IDUsersPos" => $row['IDUsersPos'],
                    "nameCadena" => $_SESSION['cadenaNombre']
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function obtenerTotalOrdenPedido($idOrdenPedido) {
        $lc_sql = "EXEC creditos.USP_obtenerTotalOrdenPedido '$idOrdenPedido'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs["total"] = $row["total"];
        }
        return json_encode($this->lc_regs);
    }

    function insertarBeneficiosOrdenPedido($param) {
        $lc_sql = "EXEC [pedido].[IAE_OrdenPedidoCanjeCupon] $param[0], '$param[1]', '$param[2]' ,'$param[3]', $param[4], '$param[5]' ,$param[6] ,'".json_encode($param[7])."'";
        $this->fn_ejecutarquery($lc_sql);
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function habilitarBotonQR($idCadena, $idRestaurante) {
        $boton = 0;
        $lc_sql = "EXEC [pedido].[USP_OrdenPedidoCanjeCupon] 1, '$idCadena', '$idRestaurante'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $boton = $row["habilitarBoton"];
            }
        } else {
            $boton = 0;
        }
        return $boton;
    }

    function fn_ValidarControlEstacionActivo($post) {
        $lc_sql = "EXECUTE  [pedido].[USP_ValidarControlEstacionActivo] @idEstacion = '$post[est_id]', @idControlEstacion = '$post[idControlEstacion]', @idCadena = '$post[cdn_id]', @idPeriodo = '$post[IDPeriodo]'";
        $continuar=0;
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $continuar = $row['continuar'];
            }
        }
        unset($lc_sql,$row);
        return $continuar;
    }
    
    function fn_retomarOrdenKiosko($Factura, $ip, $usuario, $tipoOrden) {
        if ($tipoOrden == 'PICKUP') {
            $lc_sql = "EXEC [dbo].[kiosko_Integracion_OrdenPedido_Factura] '$Factura', '$ip', '$usuario', '$tipoOrden'";
        } else {
            $lc_sql = "EXEC [dbo].[kiosko_ObtenerId_OrdenPedido_Factura] '$Factura'";
        }

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "odp_id" => $row['odp_id'],
                    "dop_cuenta" => $row['dop_cuenta'],
                    "mesa_id" => $row['mesa_id'],
                    "mensajes" => utf8_encode($row['mensaje'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_anularOrdenKioskoMaxpoint($Factura, $usuario) {
        $lc_sql = "EXEC [dbo].[Kiosko_Eliminar_CabeceraFactura_DetalleFactura] '$Factura', '$usuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "mensaje" => $row['mensaje']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_configuracionKiosko($idCadena, $idRestaurante) {
        $this->lc_regs["respuesta"] = array("estado" => 0,
            "mensaje" => "Error al buscar configuración kiosko",
            "url" => "",
            "activo" => "0");

        $lc_sql = "EXEC [config].[configuracionKiosko] $idCadena, $idRestaurante";
        $ejecutado = $this->fn_ejecutarquery($lc_sql);
        if ($ejecutado && ($this->fn_numregistro() > 0)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs["respuesta"]["estado"] = 1;
            $this->lc_regs["respuesta"]["mensaje"] = "OK";
            $this->lc_regs["respuesta"]["url"] = $row["url_socket"];
            $this->lc_regs["respuesta"]["url_http"] = $row["url"];
            $this->lc_regs["respuesta"]["activo"] = $row["activo"];
        } else {
            $this->lc_regs["respuesta"]["mensaje"] = "No se encontró configuración de Kiosko";
        }
        return $this->lc_regs;
    }

    function fn_cargarPedidosEfectivo($idCadena, $idRestaurante) {
        $lc_sql = "EXEC [pedido].[TRANSACCIONES_cargar_ordenes_efectivo] $idCadena, $idRestaurante";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "transaccion" => $row["transaccion"],
                    "cliente" => utf8_encode($row["cliente"]),
					"orden" => $row["orden"],
                    "codigo_app" => $row["codigo_app"],
					"tipo" => $row["tipo"]
                );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_verificarCodigoOrdenApp($idCadena) {
        $lc_sql = "EXEC [dbo].[verificarPoliticaCodigoOrdenApp] $idCadena";
        $codApp = $this->fn_ejecutarquery($lc_sql);
        if ($codApp) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs = $row["CodAppRest"];
        } else {
            $this->lc_regs["mensaje"] = "No se encontró configuración de Política";
        }
        return $this->lc_regs;
    }

    function fn_verificarLongitudCodigoOrdenApp($idCadena) {
        $lc_sql = "EXEC [dbo].[verificarPoliticaLongitudCodigoOrdenApp] $idCadena";
        $codApp = $this->fn_ejecutarquery($lc_sql);
        if ($codApp) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs = $row["LongCodApp"];
        } else {
            $this->lc_regs["mensaje"] = "No se encontró configuración de Política";
        }
        return $this->lc_regs;
    }

    function fn_verificarlecturaCodigosManualPickup($idCadena, $idRest) {
        $lc_sql = "EXEC [dbo].[verificarlecturaCodigosManualPickup] $idCadena, $idRest";
        $statusCodApp = $this->fn_ejecutarquery($lc_sql);
        if ($statusCodApp) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs = $row;
        } else {
            $this->lc_regs["mensaje"] = "No se encontró configuración de Política";
        }
        return $this->lc_regs;
    }

    function fn_cancelarOrdenKiosko($ordenPedido) {
        $lc_sql = "EXEC [pedido].[cancelarOrdenKiosko] '$ordenPedido'";
        $this->fn_ejecutarquery($lc_sql);
    }

    function fn_consultarPedidoApp($codigo_app){
        $lc_sql="SELECT  identificacion_cliente,nombres_cliente  FROM Cabecera_App WHERE codigo_app = '$codigo_app'";
        if($this->fn_ejecutarquery($lc_sql)) 
            { 
               
                while($row = $this->fn_leerarreglo()) 
                {		
                    $this->lc_regs['identificacion_cliente'] = $row['identificacion_cliente'];
                    $this->lc_regs['nombres_cliente'] = utf8_encode($row['nombres_cliente']);		
                }
            }	
        $this->lc_regs['str'] = $this->fn_numregistro();  
        return json_encode($this->lc_regs);	
    }
    function fn_parametrosFifteam($idCadena, $idRestaurante, $codigo) {
        $lc_sql = "EXEC [pedido].[USP_Configuraciones_Fifteam] '$idCadena', '$idRestaurante', '$codigo'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "parametros_url" => $row["parametros_url"],
                    "username" => $row["username"],
                    "user_password" => $row["user_password"]
                );
            }
        }
        return $this->lc_regs;
	}
	
	function fn_configuracionPickup($idCadena, $idRestaurante) {
		$lc_sql = "EXEC config.PICKUP_activo $idCadena, $idRestaurante";
		if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                if ($row["activo"] !== null){
                    $this->lc_regs[] = array(
                        "activo" => $row["activo"]
                    );
                }
            }
            if ($row !== null){
    			return $this->lc_regs;
            }
        }
	}

    function fn_filtrarPedidosMedios($lc_condiciones) {
        $lc_sql = "EXEC config.USP_ObtenerMediosPorEstacion '$lc_condiciones[0]','$lc_condiciones[1]','$lc_condiciones[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "id_estacion" => $row["id_estacion"],
                    "medios" => $row["medios"]
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }


    function fn_cargarUrlApiValidaPlugthem($idRestaurante) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [config].[USP_Retorna_Direccion_Webservice] '$idRestaurante', 'PLUG THEM', 'VALIDAR CORREO', 0 ";

        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "url" => utf8_decode($row['direccionws']) );
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function fn_eliminar_Cabecera_Orden_Pedido($lc_condiciones) {
        $estado = 0;
        $lc_sql = "EXEC [dbo].[IAE_Eliminar_Cabecera_Orden_Pedido] @IDCabeceraOrdenPedido='$lc_condiciones[IDCabeceraOrdenPedido]' ";        
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $estado = array( "estado" => $row['estado'] );
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $estado;
    }

    public function fn_ValidarFacturaTarjeta($cabecera) {
        $lc_sql = "EXEC [pedido].[USP_ValidarFacturaTarjeta] '$cabecera' ";
        if($this->fn_ejecutarquery($lc_sql)){
            $row = $this->fn_leerarreglo();
            if($row['TipoPago']=='TARJETA'){
                unset($row,$lc_sql,$cabecera);
                return json_encode(array('estado'=>1));
            }else {
                unset($row,$lc_sql,$cabecera);
                return json_encode(array('estado'=>0));
            }            
        }
    }
    
    function condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[pedido].[condicionFacturacionOrdenPedido] '$IDCabeceraOrdenPedido';";
        //        todo: guillermo revision sp EXEC	[pedido].[condicionFacturacionOrdenPedido] '1967101C-0475-EE11-A4BE-70106F3EFF7D';

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();

            $this->lc_regs["condicionFOP"] =  array(
                    "error"                 =>(int) $row["error"],
                    "errorDescripcion"      => utf8_encode( $row["errorDescripcion"] ),
                    "condicion"             =>(int) $row["condicion"],
                    "condicionDescripcion"  => utf8_encode( $row["condicionDescripcion"] ),
                    "promesaPendiente"      => (int) $row["promesaPendiente"],
                    "IDFormapagoPromPend"   => $row["IDFormapagoPromPend"],
                    "montoPagadoPromPend"   =>(float) $row["montoPagadoPromPend"]
                );

            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return json_encode( $this->lc_regs );
    }

    function rutaBinarioPHP( $ID_Restaurante )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[config].[USP_ObtenerRutaEjecutablePHP] '$ID_Restaurante';";        

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();

            if ($row !== null){
                $this->lc_regs["rutaBinarioPHP"] = $row["rutaEjecutablePHP"];
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return $this->lc_regs;
    }

    function iAuditoriaTransaccionCuponDescuento( $atran_descripcion, $IDUsersPos, $IDCabeceraOrdenPedido, $pluIdCuponDescuento )
    {
        $lc_sql = "EXEC	[seguridad].[I_Auditoria_Transaccion_Cupon_Descuento] '$atran_descripcion', '$IDUsersPos', '$IDCabeceraOrdenPedido', '$pluIdCuponDescuento';";        

        if( $this->fn_ejecutarquery( $lc_sql ) ) { return 1; }
        else { return 0; }
    }

    function verificaPluCuponDescuento( $IDCabeceraOrdenPedido, $IDDetalleOrdenPedido )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[pedido].[verifica_plu_cupon_descuento] '$IDCabeceraOrdenPedido', '$IDDetalleOrdenPedido';";

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();

            $this->lc_regs["pluDerivadoCanjeCD"] = intval($row["pluDerivadoCanjeCD"]);
                                                    
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return $this->lc_regs;
    }

    function activarEsDomicilio( $cadena )
    {

        $this->lc_regs = [];
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableB] ($cadena, 'CONFIGURACION DOMICILIO', 'ES DOMICILIO') AS estado";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( 
                    "estado" => $row['estado']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function ObtenerGuardarCliente( $cadena, $restaurante, $cedula, $accion = '', $datos = '', $usuario = '', $tipoInmueble = '', $IDCabeceraOrdenPedido= '' )
    {
        $intentos = 3;
        $mensaje = array();
        while ($intentos > 0){
            $result = 'No se pudo conectar';
            $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($cadena, 'DATOS PERSONALES', 'URL') AS url";
            try {
                $this->fn_ejecutarquery( $lc_sql );
                if ( $this->fn_numregistro() > 0 ) {
                    $row = $this->fn_leerarreglo();

                    $urlServicioWeb = $row['url'];
                    $token = $this->validarTokenApiCliente();

                    $obtenerClientes = $this->ObtenerCliente( $cadena, $restaurante, $cedula, $urlServicioWeb, $token );

                    if (isset($datos[2])){
                        $datos["telefono"] = $datos[2];
                    }

                    if (isset($datos["clienteFono"])){
                        $datos["telefono"] = $datos["clienteFono"];
                    }

                    if (isset($datos["clienteDescripcion"])){
                        $datos[1] = $datos["clienteDescripcion"];
                    }

                    if (isset($datos["clienteCorreo"])){
                        $datos[17] = $datos["clienteCorreo"];
                    }

                    if ((!empty($datos)) && ($datos != '')){
                        $tipoAccion = 'U';
                        $telefono = $datos["telefono"];
                    }else{
                        $tipoAccion = 'I';
                        $telefono = '';
                    }

                    if ($obtenerClientes) {

                        if (!empty($obtenerClientes["data"]["revocado"])) {
                            if($obtenerClientes["data"]["revocado"]) {
                                $revocado = [
                                    'success' => false
                                ];

                                return json_encode($revocado);
                            }
                        }

                        if (!empty($obtenerClientes["data"]["cliente"])){
                            $obtenerCliente = $obtenerClientes["data"]["cliente"];
                        }else{
                            $obtenerCliente = $obtenerClientes;
                        }

                        if (($telefono == '') && (!empty($obtenerCliente["telefono"]))){
                            $telefono = $obtenerCliente["telefono"];
                        }

                        if (!empty($obtenerCliente["documento"])){
                            $beneficiosCliente =  '';
                            $_SESSION['aplicarBeneficio'] = 0;
                            if (isset($obtenerClientes["data"]["infoAdicional"])) {
                                $infoBeneficio = $obtenerClientes["data"]["infoAdicional"]["infoBeneficio"];
                                if (isset($infoBeneficio["redimioBeneficio"])){
                                    $beneficiosCliente = $infoBeneficio["redimioBeneficio"];
                                }else{
                                    $beneficiosCliente = $infoBeneficio["remidioBeneficio"];
                                }
                                $_SESSION['aplicarBeneficio'] = 1;
                            }
                        }
                        if ((!empty($obtenerCliente["documento"])) && ($accion == 'editar')){
                            $DateTime = new DateTime();
                            $formattedDateTime = $DateTime->format('Y-m-d\TH:i:s.u\Z');

                            $nombre = utf8_decode($datos[1]);
                            $nombres = explode(' ', $nombre);
                            $count = count($nombres) / 2;

                            $nombre = array_slice($nombres, 0, $count);
                            $apellido = array_slice($nombres, $count);

                            $datosCliente = array(
                               "_id" => $obtenerCliente['_id'],
                               "cdn_id" => $cadena,
                               "fechaActualizacion" => $formattedDateTime,
                               "sistemaOrigen" => $obtenerCliente["sistemaOrigen"],
                               "aceptacionPoliticas" => $obtenerCliente["aceptacionPoliticas"],
                               "fechaAceptoPrivacidad" => $obtenerCliente["fechaAceptoPrivacidad"],
                               "analisisDeDatosPerfiles" => $obtenerCliente["analisisDeDatosPerfiles"],
                               "cesionDatosATercerosNacionales" => $obtenerCliente["cesionDatosATercerosNacionales"],
                               "cesionDatosATercerosInternacionales" => $obtenerCliente["cesionDatosATercerosInternacionales"],
                               "autenticacion" => $obtenerCliente["autenticacion"],
                               "envioComunicacionesComerciales" => $obtenerCliente["envioComunicacionesComerciales"],
                               "envioComunicacionesComercialesPush" => $obtenerCliente["envioComunicacionesComercialesPush"],
                               "primerNombre" => implode(' ', $nombre),
                               "apellidos" => implode(' ', $apellido),
                               "email" => $datos[17],
                               "telefono" => $telefono
                            );

                            $header_array = array(
                                'Content-Type: application/json; charset=UTF-8',
                                'Accept: application/json',
                                'Authorization: Bearer '.$token["token"]
                            );

                            $url = $urlServicioWeb.'/api/client';
                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosCliente));
                            //curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem');
                            $result = curl_exec($ch);
                            $result = json_decode($result, true);

                            if ($result === false) {
                                $result = 'cURL Error: ' . curl_error($ch);
                            }

                            curl_close($ch);
                        }else if ((!empty($obtenerCliente["documento"])) && ($beneficiosCliente === false)){
                            $guardarActualizar = $this->GuardarClienteBaseLocal($obtenerCliente, $usuario, $tipoInmueble, $tipoAccion, $telefono);
                            if ($guardarActualizar == true){
                                $result = $obtenerClientes["data"];
                            }
                        }else if ((!empty($obtenerCliente["documento"])) && ($beneficiosCliente === true)){

                            $datos_mensaje = $obtenerCliente["beneficiosPorCadena"][0]["infoBeneficio"];
                            $middlename = ' ';
                            if ($obtenerCliente["segundoNombre"] != ''){
                                $middlename = ' '.$obtenerCliente["segundoNombre"].' ';
                            }
                            $nombre = $obtenerCliente["primerNombre"].$middlename.$obtenerCliente["apellidos"];

                            $mensaje = array(
                                "Cadena" => $datos_mensaje["infoRestaurante"]["nombre_cadena"],
                                "Tienda" => 'local '.$datos_mensaje["infoRestaurante"]["local"],
                                "Fecha de canje" => $datos_mensaje["fechaCanje"],
                                "No. doc" => $obtenerCliente["documento"],
                                "Nombre" => $nombre
                            );

                            $mensaje = array('mensaje' => $mensaje);

                            $result = array_merge($obtenerClientes["data"], $mensaje);
                        }else{
                            $result = $obtenerClientes;
                        }
                    }else{
                        $result = array($datos, 'conexion' => $result);
                    }
                    $intentos--;
                }

            } catch (Exception $e) {
                return $e;
            }
            return json_encode($result);
        }
    }

    function GuardarClienteBaseLocal($datos, $usuario, $tipoInmueble, $tipoAccion, $telefono = ''){

        $lc_datos[0] = $tipoAccion;
        $lc_datos[1] = "W";
        //$lc_datos[2] = "CEDULA";
        $lc_datos[2] = $datos['tipoDocumento'];
        $lc_datos[3] = $datos["documento"];
        $lc_datos[4] = $datos["primerNombre"].' '.$datos["apellidos"];
        $lc_datos[5] = '';
        $lc_datos[6] = $telefono;
        $lc_datos[7] = $datos["email"];
        $lc_datos[8] = $usuario;
        $lc_datos[9] = 0;
        $lc_datos[10] = "NORMA";       

        $json = '{"TiposInmuebles":"'.$tipoInmueble.'","numeroCallePrincipal":"","calleSecundaria":"","referenciaTipoInmueble":"","referencia":"","latitud":"","longitud":""}';

        $lc_sql = "EXEC [config].[IAE_ClientePayphone] '$lc_datos[0]',  '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$json'";
        return $this->fn_ejecutarquery($lc_sql);
    }

    function ObtenerCliente( $cadena, $restaurante, $cedula, $urlServicioWeb, $token )
    {
        $header_array = array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json',
            'Authorization: Bearer '.$token["token"]
        );

        $url = $urlServicioWeb.'/api/Client/'.$cadena.'/'.$cedula;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem');
        $result = curl_exec($ch);
        $result = json_decode($result, true);

        if ($result === false) {
            $result = 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);
        return $result;
    }

    function validarTokenApiCliente() {
        $arrayToken = array();
        $mensaje = '';
        $token = '';
        $path_json = '';

        $fileName = 'tokenApiMdmCliente.json';
        $folderName = 'tokens';
        $permisos = '0777';
        $base_dir = realpath(__DIR__  . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filePath = $base_dir . $folderName . DIRECTORY_SEPARATOR . $fileName;
        $filePathToken = $base_dir . $folderName;
        //Si no existe la carpeta tokens la crea
        if (!file_exists($filePathToken)) {
            mkdir($filePathToken, $permisos, true);
        }
        if (!file_exists($filePath)) {
          $tokenApiMDMCliente = $this->generarTokenApiMDMCliente();
          file_put_contents($filePath,$tokenApiMDMCliente);
        }

        $configContents =  file_get_contents($filePath);

        if ($configContents !== false) {
            $config = json_decode($configContents, true);

            if (isset($config['token'])) {
                $token = $config['token'];
                $tokenParts = explode('.', $token);
                $tokenPayload = base64_decode($tokenParts[1]);
                $payload = json_decode($tokenPayload, true);

                $tokenExpirationTime = $payload['exp'];
                $currentTimestamp = time();

                if ($currentTimestamp <= $tokenExpirationTime) {
                    $mensaje = utf8_decode("El token API MDM CLIENTE es válido y no ha caducado.");
                } else {
                    $tokenData = $this->generarTokenApiMDMCliente();

                    if (isset($tokenData["token"])) {
                        $token = $tokenData["token"];
                        $jsonDatos = json_encode($tokenData);
                        file_put_contents($filePath, $jsonDatos);
                    } else {
                        $mensaje = 'No se pudo generar un nuevo token.';
                    }
                }
            } else {
                $tokenData = $this->generarTokenApiMDMCliente();

                if (isset($tokenData["token"])) {
                    $token = $tokenData["token"];

                    $jsonDatos = json_encode($tokenData);
                    file_put_contents($filePath, $jsonDatos);

                } else {
                    $mensaje = 'No se pudo generar un nuevo token.';
                }
            }
        } else {
            $mensaje = "No se pudo leer el archivo JSON.";
        }

        $arrayToken = array('token' => $token, 'mensaje' => utf8_encode($mensaje));
        return $arrayToken;
    }

    function generarTokenApiMDMCliente() {
        $idRestaurante     = $_SESSION['rstId'];
        $idCadena          = $_SESSION['cadenaId'];

        $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'URL') AS url;";
        $this->fn_ejecutarquery($sql_url_base);
        $arreglo_url_base = $this->fn_leerarreglo();
        $urlAPIMDM = $arreglo_url_base['url'];

        $sql_idCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTID') AS idApi;";
        $this->fn_ejecutarquery($sql_idCliente);
        $arreglo_idCliente = $this->fn_leerarreglo();
        $api_clientID = $arreglo_idCliente['idApi'];

        $sql_secretCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTSECRET') AS secretApi;";
        $this->fn_ejecutarquery($sql_secretCliente);
        $arreglo_secretCliente  = $this->fn_leerarreglo();
        $api_secretCliente = $arreglo_secretCliente['secretApi'];

        $header_array = array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json'
        );

        $credentials = array(
            'clientID' => $api_clientID,
            'clientSecret' => $api_secretCliente
        );
        $url = $urlAPIMDM . '/api/Auth/token';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));

        $result = curl_exec($ch);

        if ($result === false) {
            $result = 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result, true);

    }

    function confirmacionClienteBeneficio( $cadena )
    {

        $this->lc_regs = [];
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableB] ($cadena, 'DATOS PERSONALES', 'BENEFICIO CLIENTE') AS beneficio";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( 
                    "beneficio" => $row['beneficio']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerBeneficioCliente($cadena, $lc_datos){

        $producto = $this->agregarBeneficioCliente( $cadena );
        $producto = $producto["producto"];
        $cantidad = $this->agregarCantidadBeneficioCliente( $cadena );
        $cantidad = $cantidad["cantidad"];

        $lc_sql = "EXEC pedido.ORD_agregar_pluordenpedido '$lc_datos[0]','$lc_datos[1]',$producto,$cantidad,'$lc_datos[4]', '0','$lc_datos[6]', '$lc_datos[9]', '$lc_datos[8]'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                if ($row['plu_id'] == $producto){
                    $this->lc_regs[] = array(
                        "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                        "plu_id" => $row['plu_id'],
                        "dop_cantidad" => $row['dop_cantidad'],
                        "dop_id" => $row['dop_id'],
                        "dop_iva" => ROUND($row['dop_iva'], 2),
                        "dop_total" => ROUND($row['dop_total'], 2),
                        "dop_precio_unitario" => ROUND($row['dop_precio_unitario'], 2),
                        "plu_impuesto" => $row['plu_impuesto'],
                        "plu_anulacion" => $row['plu_anulacion'],
                        "plu_gramo" => $row['plu_gramo'],
                        "tipo" => $row['tipo'],
                        "ancestro" => $row['ancestro'],
                        "plus_puntos" => $row['Detalle_Orden_PedidoVarchar1'],
                        "puntos" => $row['puntos']
                    );
                }
            }
        }
        $this->lc_regs['str'] = 1;
        return json_encode($this->lc_regs);
    }

    function agregarBeneficioCliente( $cadena )
    {
        $this->lc_regs = [];
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($cadena, 'DATOS PERSONALES', 'PRODUCTO') AS producto";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( 
                    "producto" => $row['producto']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function agregarCantidadBeneficioCliente( $cadena )
    {
        $this->lc_regs = [];
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableI] ($cadena, 'DATOS PERSONALES', 'CANTIDAD') AS cantidad";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( 
                    "cantidad" => $row['cantidad']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }
    
    function guardarVariables($lc_datos){
        $lc_sql = "EXEC [pedido].[guardarVariableslocales] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "value" => ($row['value']),
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function obtenerVariables($lc_datos){
        $this->lc_regs = [];
        $lc_sql = "SELECT Detalle_Orden_PedidoVarchar3 as value FROM Detalle_Orden_Pedido WHERE IDCabeceraOrdenPedido = '$lc_datos[0]';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                if ($row['value'] !== null){
                    $this->lc_regs[] = array(
                        "value" => json_decode($row['value']),
                    );
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }else{
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
            }
        }
        return json_encode($this->lc_regs);
    }

    function validarRevocatoria($cadena, $restaurante, $cliente) 
    {
        try {
            $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($cadena, 'DATOS PERSONALES', 'URL') AS url";
            
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();

                $urlServicioWeb = $row['url'];
                $token = $this->validarTokenApiCliente();

                $obtenerClientes = $this->ObtenerCliente( $cadena, $restaurante, $cliente, $urlServicioWeb, $token );

                if ($obtenerClientes) {
                    
                    if (isset($obtenerClientes["data"]["revocado"])) {
                        if($obtenerClientes["data"]["revocado"]) {
                            $estadoRevocatoria = 'REVOCADO';
                        }else{
                            $estadoRevocatoria = 'ACTIVO';
                        }

                        //actualizar estado cliente
                        $lc_sql = "EXEC [dbo].[revocatoria_cliente_estado] '$cliente', '$estadoRevocatoria'";
                        $this->fn_ejecutarquery($lc_sql);
                    }
                }
            }
        }catch(Exception $e) {
            return $e;
        }

        return json_encode(['estado' => true]);
    }

    // masivo

    function fn_obtenerCandena(){
        $sql = "SELECT * FROM cadena";

        $cadena=0;

        if($this->fn_ejecutarquery($sql)){
            while ($row = $this->fn_leerarreglo()) {
                $cadena = $row['cdn_id'] * 1;
            }
        }

        return $cadena;
    }
    
    function fn_politicUrlValidarCodigoMasivo($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO VALIDAR CODIGO') AS evento";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        if(isset($row['evento']) || !empty($row['evento'])){
            $data = (object)[
                "url" => $row['evento']
            ];
            return $data;
        }

        return false;
    }

    function nombrarPicada($accion, $IDCabeceraOrdenPedido, $nombrePicada) {
        $error = 1;
        $transactSQL = "EXEC [pedido].[nombrarPicada] $accion, '$IDCabeceraOrdenPedido', '$nombrePicada';";        

        if($this->fn_ejecutarquery($transactSQL) AND $this->fn_numregistro() > 0) {
            $registro = $this->fn_leerarreglo();
            $error = $registro['error'];
        }

        return json_encode(['error' => $error]);
    }
    
    function pedidoAplicaPicada(): int {
        $aplica = 0;
        $transactSQL = 'EXEC [pedido].[aplicaPicada];';

        if($this->fn_ejecutarquery($transactSQL) AND $this->fn_numregistro() > 0) {
            $registro = $this->fn_leerarreglo();
            $aplica = (int) $registro['aplica'];
        }

        return $aplica;
    }
	
    function fn_politicUrlObtenerClienteMasivo($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO OBTENER CLIENTE') AS evento";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        if(isset($row['evento']) || !empty($row['evento'])){
            $data = (object)[
                "url" => $row['evento']
            ];
            return $data;
        }

        return false;
    }

    function fn_ejecutaLlamadoMasivo($cadena, $codigo, $authorization){
        $data = $this->fn_politicUrlValidarCodigoMasivo($cadena);
        if($data){
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $data->url.$codigo,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $authorization"
                ],
            ]);

            $respuesta=curl_exec($curl);
            $response = json_decode($respuesta);
            $err = curl_error($curl);

            curl_close($curl);
            if(!$err && isset($response->data)){
                //activamos todo lo correspondiente a fidelizacion  clienteWSClientes.php
                $_SESSION['fidelizacionActiva']=1;
                $_SESSION["claveConexionMasivo"]=$authorization;
                //numero de puntos
                $idCliente=$response->data->customer->id;
                $_SESSION['fb_mensaje_puntos']="";
                $_SESSION['fb_customer_id']=$idCliente;
                $puntos=$this->fn_consultaPuntosMasivoRespuesta(json_decode($respuesta,true));//para pruebas//$this->fn_consultaPuntosMasivoRespuesta($response);
                $_SESSION['fb_points']=$puntos;
                //if($puntos==0) $_SESSION['fidelizacionActiva']=0;
                $_SESSION['fdznNombres'] =$response->data->customer->name;
                
                // Validar y guardar el documento del cliente
                if (isset($response->data->customer->metadata) && 
                    isset($response->data->customer->metadata->document) && 
                    !empty($response->data->customer->metadata->document)) {
                    $_SESSION['fdznDocumento'] = $response->data->customer->metadata->document;
                } else {
                    $_SESSION['fdznDocumento'] = $_SESSION['documentoCliente']==''?"9999999999999":$_SESSION['documentoCliente'];
                }
                //$_SESSION['fdznDocumento']="1311573586"; //para pruebas quitar
                $_SESSION['fb_name']=$response->data->customer->name;
                $_SESSION['fb_econtroDatos'] = 1;
                $_SESSION['fb_status'] = 'REGISTERED'; //BLOCKED
                //se supone que es la cedula
                $_SESSION['fb_document']=$response->data->customer->id;
                $_SESSION['fb_money']=0; //revisar
                $_SESSION['fb_security']=1;
                $_SESSION['fb_authorization']=$authorization;

                return json_encode([
                    "error"=>false,
                    "data"=>[
                        "name"=>$response->data->customer->name,
                        "uid"=>$response->data->customer->id,
                        "auth"=>$authorization,
                        "points"=>$puntos,
                        "mensaje_puntos" => $_SESSION['fb_mensaje_puntos']
                    ]
                ]);
            }else{
                $message_masivo = "Código no valido por masivo";
                if(is_string($response->details)){
                    $message_masivo = $response->details;
                }
                return json_encode([
                    "error"=>true,
                    "data"=>"Masivo: $message_masivo",
                    "detalles"=>[
                        "auth"=>$authorization,
                        "peticion"=>$data->url.$codigo,
                        "respuesta"=>json_encode($response)
                    ]
                ]);
            }
        }else{
            return json_encode([
                "error"=>true,
                "data"=>"Política de masivo mal configurada"
            ]);
        }
    }

    function fn_consultaPuntosMasivoRespuesta($response){
        if(isset($response['data'])){
            if (isset($response['data']['wallet']['totals']) && is_array($response['data']['wallet']['totals'])) {
                $total_final=0;
                foreach ($response['data']['wallet']['totals'] as $total) {
                    if (isset($total['reward']['type']) && $total['reward']['type'] === 'POINTS') {
                        $total_final=$total['total'];
                        // Termina el loop después de validar el primer reward tipo POINTS
                        break;
                    }
                }
                if($total_final==0)
                    $_SESSION['fb_mensaje_puntos']=json_encode($response);
                return $total_final;
            }else{
                $_SESSION['fb_mensaje_puntos']="TOTALS NO EXISTE";
                return -3;
            }
        }else{
            $_SESSION['fb_mensaje_puntos']="RESPUESTA ERRONEA: ".json_encode($response);
            return -2;
        }
    }

    function fn_consultaPuntosMasivo($cadena, $idCliente, $authorization){
        $data = $this->fn_politicUrlObtenerClienteMasivo($cadena);
        if($data){
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $data->url.$idCliente,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $authorization"
                ],
            ]);

            $response=curl_exec($curl);
            $response = json_decode($response,true);
            $err = curl_error($curl);

            curl_close($curl);
            if(!$err && isset($response['data'])){
                if (isset($response['data']['wallet']['totals']) && is_array($response['data']['wallet']['totals'])) {
                    $total_final=0;
                    foreach ($response['data']['wallet']['totals'] as $total) {
                        if (isset($total['reward']['type']) && $total['reward']['type'] === 'POINTS') {
                            $total_final=$total['total'];
                            // Termina el loop después de validar el primer reward tipo POINTS
                            break;
                        }
                    }
                    if($total_final==0)
                        $_SESSION['fb_mensaje_puntos']=json_encode($response);
                    return $total_final;
                }else{
                    $_SESSION['fb_mensaje_puntos']="TOTALS NO EXISTE";
                    return -3;
                }
            }else{
                $_SESSION['fb_mensaje_puntos']="RESPUESTA ERRONEA: ".json_encode($response);
                return -2;
            }
        }else{
            $_SESSION['fb_mensaje_puntos']="POLITICA URL MASIVO CLIENTE NO CONFIGURADA";
            return -1;
        }
    }
}