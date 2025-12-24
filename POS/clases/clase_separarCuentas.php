<?php

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 28-02-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 29-04-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Notas de Crédito///////
///////FECHA ULTIMA MODIFICACION: 09/09/2014////////////////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Validacion de productos para///////////
/////////////////////////////////Salon y llevar/////////////////////////
///////////////////////////////////////////////////////////
//-- =================================================================
//--FECHA ULTIMA MODIFICACION	: 13:35 20/4/2017
//--USUARIO QUE MODIFICO	: Mychael Castro
//--DECRIPCION ULTIMO CAMBIO	:  
//-- =================================================================

class menuPedido extends sql {

    //Constructor de la Clase
    function _construct() {
        parent ::_construct();
    }

    //Función que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case "guardarCop":

                $lc_sql = "EXEC [config].[IAE_actualiza_division_cuenta] '$lc_datos[0]','$lc_datos[1]'";

                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "odp_id" => $row['IDCabeceraOrdenPedido'],
                            "dop_cuenta" => $this->ifNum($row['dop_cuenta'])
                                );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "listaPendiente":
                /* $lc_sql = "SELECT distinct(plu.plu_id) as plu_id,
                  map.magp_desc_impresion +' '+
                  case when cla.idIntegracion =2 then '[LLEVAR]' ELSE '' end as magp_desc_impresion,

                  dop.dop_cantidad ,dop.IDDetalleOrdenPedido,dop.dop_iva,dop.dop_total,
                  dop.dop_precio,plu.plu_impuesto,plu.IDClasificacion
                  FROM Cabecera_Orden_Pedido odp
                  inner join Detalle_Orden_Pedido dop on odp.IDCabeceraOrdenPedido=dop.IDCabeceraOrdenPedido
                  inner join Menu_Agrupacionproducto map on map.plu_id=dop.plu_id
                  inner join Plus plu on plu.plu_id=dop.plu_id
                  inner join Clasificacion cla on cla.IDClasificacion = plu.IDClasificacion

                  WHERE dop.IDCabeceraOrdenPedido='" . $lc_datos[1] . "' AND dop.dop_estado=1 AND
                  dop.dop_anulacion=1 AND dop.dop_cuenta=" . $lc_datos[2] . " ORDER BY dop.IDDetalleOrdenPedido ASC";
                 */
                $lc_sql = " EXEC pedido.ORD_cargar_ordenpedido_pendiente_FS " . $lc_datos[0] . ",'$lc_datos[1]','" . $lc_datos[2] . "'," . $lc_datos[3];


                try {

                    if ($result = $this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion']))
                            , "plu_id" => $row['plu_id']
                            , "dop_cantidad" => $this->ifNum($row['dop_cantidad'])
                            , "dop_id" => $row['dop_id']
                            , "dop_iva" => ROUND($row['dop_iva'], 2)
                            , "dop_total" => ROUND($row['dop_total'], 2)
                            , "dop_precio_unitario" => ROUND($row['dop_precio_unitario'], 2)
                            , "plu_impuesto" => $this->ifNum($row['plu_impuesto']),
                                "ancestro" => $row['ancestro']
                            , "IDDetalleOrdenPedidoPadre" => $row['IDDetalleOrdenPedidoPadre']
                            );
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }catch (Exception $e){
                    $this->lc_regs['str'] = false;
                    return json_encode($this->lc_regs);
                }


            case 'obtieneParametrosVoucher':
                $lc_sql = " EXEC config.obtieneParametrosVoucher  '$lc_datos[0]','$lc_datos[1]' ";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "parametros" => $row['parametros']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;



            case 'esFullService':
                $lc_sql = "select  [config].[fn_restaurante_full_service] ($lc_datos[0]) as respuesta  ";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "respuesta" => $row['respuesta']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case 'listaPendiente_impuesto':
                $lc_sql = "EXEC CONFIG.UPS_IMPUESTOS_SEPARACION_CUENTAS '$lc_datos[0]',$lc_datos[1]";
                try {

                    if ($result = $this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array(
                                "SUBTOTAL" => $this->ifNum($row['SUBTOTAL'])
                            , "IVA" => $this->ifNum($row['IVA'])
                            , "TOTAL" => $this->ifNum($row['TOTAL']));
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }catch (Exception $e){
                    $this->lc_regs['str'] = false;
                    return json_encode($this->lc_regs);
                }
                break;


            case 'estaDividido':
                $lc_sql = "EXEC CONFIG.estaDividido '$lc_datos[0]' ";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "respuesta" => $row['respuesta']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "numeroCuentas":
                $lc_sql = "EXEC  config.USP_obtiene_min_max_odp '$lc_datos[0]' "; // "SELECT max(dop_cuenta) as acum_split_max, min(dop_cuenta) as acum_split_min
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "acum_split_max" => $this->ifNum($row['acum_split_max']),
                            "acum_split_min" => $this->ifNum($row['acum_split_min'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;










            case "listacompleta":
                $lc_sql = "SELECT (plu.plu_id) as plu_id,
                                                            map.magp_desc_impresion +' '+ 
                                                           case when cla.idIntegracion =2 then '[LLEVAR]' ELSE '' end as magp_desc_impresion,

                                                          dop.dop_cantidad ,dop.IDDetalleOrdenPedido,dop.dop_iva,dop.dop_total,
                                                          dop.dop_precio,plu.plu_impuesto,plu.IDClasificacion
                                                          FROM Cabecera_Orden_Pedido odp
                                                          inner join Detalle_Orden_Pedido dop on odp.IDCabeceraOrdenPedido=dop.IDCabeceraOrdenPedido
                                                          inner join Menu_Agrupacionproducto map on map.plu_id=dop.plu_id
                                                          inner join Plus plu on plu.plu_id=dop.plu_id
                                                          inner join Clasificacion cla on cla.IDClasificacion = plu.IDClasificacion

                                                          WHERE dop.IDCabeceraOrdenPedido='" . $lc_datos[1] . "' AND dop.dop_estado>=1 AND
                                                           dop.dop_anulacion=1 ORDER BY dop.IDDetalleOrdenPedido ASC";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion']))
                            , "plu_id" => $row['plu_id']
                            , "dop_cantidad" => $row['dop_cantidad']
                            , "dop_id" => $row['IDDetalleOrdenPedido']
                            , "dop_iva" => ROUND($row['dop_iva'], 2)
                            , "dop_total" => ROUND($row['dop_total'], 2)
                            , "dop_precio_unitario" => ROUND($row['dop_precio'], 2)
                            , "plu_impuesto" => $row['plu_impuesto']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;




            case "verificarDop":
                $lc_sql = "					
		SELECT * FROM Detalle_Orden_Pedido WHERE IDCabeceraOrdenPedido='$lc_datos[0]' AND IDDetalleOrdenPedido='$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dop_id" => $row['IDDetalleOrdenPedido']
                            , "plu_id" => $row['plu_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "verificarPlu":
                $lc_sql = "EXEC config.USP_administracion_separacion_cuentas 1, '" . $lc_datos[0] . "','" . $lc_datos[1] . "', '" . $lc_datos[2] . "','$lc_datos[3]','" . $lc_datos[5] . "' , '" . $lc_datos[4] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dop_id" => $row['IDDetalleOrdenPedido']
                            , "plu_id" => $row['plu_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

                break;

            case "verificarCantidad":
                $lc_sql = "
							SELECT * FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido='$lc_datos[1]' AND IDCabeceraOrdenPedido='$lc_datos[0]' AND dop_cantidad>1";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dop_id" => $row['IDDetalleOrdenPedido']
                            , "odp_id" => $row['IDCabeceraOrdenPedido']
                            , "plu_id" => $row['plu_id']
                            , "dop_cantidad" => $row['dop_cantidad']
                            , "dop_iva" => $row['dop_iva']
                            , "dop_precio_unitario" => $row['dop_precio']
                            , "dop_total" => $row['dop_total']
                            , "dop_cuenta" => $row['dop_cuenta']
                            , "dop_estado" => $row['dop_estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "verificarCantidadPlu":
                $lc_sql = " SELECT * FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido='$lc_datos[2]' AND IDCabeceraOrdenPedido='$lc_datos[0]' AND plu_id='$lc_datos[1]' AND dop_cantidad>0";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dop_id" => $row['IDDetalleOrdenPedido']
                            , "odp_id" => $row['IDCabeceraOrdenPedido']
                            , "plu_id" => $row['plu_id']
                            , "dop_cantidad" => $row['dop_cantidad']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            // Actualizar Cantidad?
            case "incrementarPlu":
                $lc_sql = "EXEC config.USP_administracion_separacion_cuentas 2, '" . $lc_datos[1] . "', '" . $lc_datos[0] . "', '" . $lc_datos[2] . "', '', " . $lc_datos[3] . " , ''";

                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break; //

            case "agregarPlu":
                // $lc_sql = "INSERT into Detalle_Orden_Pedido (IDCabeceraOrdenPedido,plu_id,dop_cantidad,dop_iva,dop_precio,dop_total,dop_creacionfecha,dop_cuenta,dop_estado,dop_anulacion,dop_impresion)values('$lc_datos[1]','$lc_datos[2]',1,'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]',SYSDATETIME(),'$lc_datos[9]','$lc_datos[8]',1,1)					
                //$lc_sql="UPDATE Detalle_Orden_Pedido SET dop_cantidad='$lc_datos[3]' WHERE IDCabeceraOrdenPedido='$lc_datos[1]' AND plu_id='$lc_datos[2]' AND IDDetalleOrdenPedido='$lc_datos[0]'

                $lc_sql = "SELECT * FROM Detalle_Orden_Pedido WHERE IDCabeceraOrdenPedido='$lc_datos[1]' AND IDDetalleOrdenPedido='$lc_datos[0]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "actualizarPlu":
                // $lc_sql = " UPDATE Detalle_Orden_Pedido set dop_cuenta='$lc_datos[1]' WHERE IDDetalleOrdenPedido='$lc_datos[2]' AND IDCabeceraOrdenPedido='$lc_datos[0]'
                $lc_sql = " SELECT * FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido='$lc_datos[2]' AND IDCabeceraOrdenPedido='$lc_datos[0]' AND dop_cuenta='$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "actualizarCuenta":
                $lc_sql = "EXEC config.USP_administracion_separacion_cuentas 3, '" . $lc_datos[0] . "', '', '', '" . $lc_datos[1] . "', '0',''";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion']))
                            , "plu_id" => $row['plu_id']
                            , "dop_cantidad" => $row['dop_cantidad']
                            , "dop_id" => $row['IDDetalleOrdenPedido']
                            , "dop_iva" => ROUND($row['dop_iva'], 2)
                            , "dop_total" => ROUND($row['dop_total'], 2)
                            , "dop_precio_unitario" => ROUND($row['dop_precio'], 2)
                            , "plu_impuesto" => $row['plu_impuesto']
                            , "ancestro" => $row['ancestro']
                            , "IDDetalleOrdenPedidoPadre" => $row['IDDetalleOrdenPedidoPadre']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "canalesImpresion":
                $lc_sql = "SELECT DISTINCT plu.cprn_id, imp.cprn_descripcion FROM Cabecera_Orden_Pedido cop
						inner join Detalle_Orden_Pedido det on det.IDCabeceraOrdenPedido=cop.IDCabeceraOrdenPedido
						inner join Plus plu on plu.plu_id=det.plu_id
						inner join Impresora_Plu imp on imp.cprn_id=plu.cprn_id
						WHERE cop.IDCabeceraOrdenPedido=$lc_datos[0] AND det.dop_estado=1 AND plu.cprn_id IS NOT NULL";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cprn_id" => $row['cprn_id']
                            , "cprn_descripcion" => $row['cprn_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "enviarImpresion":
                $lc_sql = "INSERT into Canal_Movimiento (imp_ip_estacion,imp_fecha,imp_url,imp_impresora,usr_id,tca_codigo,std_id)values('$lc_datos[0]',SYSDATETIME(),'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',200,41)
				
						SELECT MAX(imp_id) AS impresion FROM Canal_Movimiento WHERE imp_ip_estacion='$lc_datos[0]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("impresion" => $row['impresion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "finalizarSplits":
                $lc_sql = "EXEC  config.UPS_dividirProductos   '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "' ";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("direccion" => $row['direccion'],
                            "url_cuenta" => $row['url_cuenta']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "recuperar_cuenta_dividida":
                $lc_sql = "EXEC [config].[IAE_separacion_cuentas_por_personas]  $lc_datos[0],'$lc_datos[1]','',''";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "opcion" => $row['opcion'],
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
                break;
        }
    }

    public function validarCuentasEnCero(
        $numeroDeCuenta, $detalleOrdenPedido
    ){
        $numeroDeCuenta = ($numeroDeCuenta != '') ? $numeroDeCuenta : "(SELECT dop_cuenta FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido =  '$detalleOrdenPedido')";
        $lc_sql =  "DECLARE @IDDetallePadre VARCHAR(40)
                    DECLARE @TotalCuenta FLOAT = 0
                    DECLARE @numCuenta INT = 0
                    SET @numCuenta = $numeroDeCuenta
                    DECLARE @IDetalleOrdePedido VARCHAR(40) = '$detalleOrdenPedido'
                    DECLARE @IDDetalleOrdenPedidoPadre VARCHAR(40) = (SELECT IDDetalleOrdenPedidoPadre FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido =  @IDetalleOrdePedido)
                    DECLARE @IDCabeceraOrdenPedido VARCHAR(40) = (SELECT IDCabeceraOrdenPedido FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido =  @IDetalleOrdePedido)
                    SET @IDDetallePadre = (SELECT ISNULL(IDDetalleOrdenPedidoPadre, IDDetalleOrdenPedido) FROM Detalle_Orden_Pedido WHERE IDDetalleOrdenPedido = @IDetalleOrdePedido)
                    IF @IDDetalleOrdenPedidoPadre IS NOT NULL BEGIN
                        SET @TotalCuenta = (SELECT ISNULL(SUM(dop_total),0) FROM Detalle_Orden_Pedido where IDDetalleOrdenPedidoPadre = @IDDetalleOrdenPedidoPadre AND dop_estado IN (-1,1) )
                        SET @TotalCuenta = (SELECT dop_total FROM Detalle_Orden_Pedido where IDDetalleOrdenPedido = @IDDetalleOrdenPedidoPadre AND dop_estado IN (-1,1) ) + @TotalCuenta
                    END ELSE BEGIN
                        SET @TotalCuenta = (SELECT ISNULL(SUM(dop_total),0) FROM Detalle_Orden_Pedido where IDDetalleOrdenPedidoPadre = @IDetalleOrdePedido AND dop_estado IN (-1,1) )
                        SET @TotalCuenta = (SELECT dop_total FROM Detalle_Orden_Pedido where IDDetalleOrdenPedido = @IDetalleOrdePedido AND dop_estado IN (-1,1) ) + @TotalCuenta
                    END
                    DECLARE @totalCuentaOrigen FLOAT = (SELECT ISNULL(SUM(dop_total), 0) FROM Detalle_Orden_Pedido WHERE dop_cuenta = $numeroDeCuenta AND IDCabeceraOrdenPedido = @IDCabeceraOrdenPedido)
                    SELECT ISNULL(@TotalCuenta, 0) AS total , @totalCuentaOrigen AS total_detino
                    ";
        if ($result = $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("total" => $row['total'], "total_detino" => $row['total_detino']);

            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);

    }

}

?>