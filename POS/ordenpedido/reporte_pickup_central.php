<!DOCTYPE html> 
<html lang="es">

<head>
    <script type="text/javascript" src="../js/ajax_reporte_pickup_central.js"></script>
</head>

<body>

    <input inputmode="none"  type="hidden" id="urlServidorCentral"  />


    <div id="pnl_principal_central" style="display: block; position: relative; height: 565px; overflow: initial;" class="card">
        <div class="card-header rp_cabecera_principal">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        Listado de Pedidos NO CONCLUIDOS
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body rp_cuerpo_tabla_principal">
            <div class="container">
                <div id="div_filtros" class="row">
                    <div class="col-sm-4">
                        <!-- <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Estado Pedido</th>
                                    <th scope="col">Mostrar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_amarillo">
                                        <div>
                                            <p class="rp_div_alinear_izquierda">Ingresado</p>
                                            <p class="rp_div_alinear_derecha"><label class="rp_label_numero_pedidos" id="lbl_pedidos_ingresados"></label></p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none" id="chk_ingresado" checked type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_naranja">
                                        <div>
                                            <p class="rp_div_alinear_izquierda">Preparando</p>
                                            <p class="rp_div_alinear_derecha"><label class="rp_label_numero_pedidos" id="lbl_pedidos_preparando"></label></p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none" id="chk_preparando" checked type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_verde">
                                        <div>
                                            <p class="rp_div_alinear_izquierda">Listo</p>
                                            <p class="rp_div_alinear_derecha"><label class="rp_label_numero_pedidos" id="lbl_pedidos_listo"></label></p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none" id="chk_listo" type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_azul">
                                        <div>
                                            <p class="rp_div_alinear_izquierda">Entregado</p>
                                            <p class="rp_div_alinear_derecha"><label class="rp_label_numero_pedidos" id="lbl_pedidos_entregado"></label></p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none" id="chk_entregado" type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                </tr>
                            </tbody>
                        </table> -->
                    </div>
                    <div class="col-sm-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2">Búsqueda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Identificación Cliente</td>
                                    <td><input inputmode="none" id="inp_identificacion_central" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Código app</td>
                                    <td><input inputmode="none" id="inp_codigo_app_central" autocomplete="off" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center"> <button id="btn_buscar_central" type="button" class="btn btn-success rp_boton_busqueda" onclick="buscarCentral()">Buscar</button> </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-9">
                        <div class="table-responsive" style="overflow-x: unset; height: 300px;">
                            <table id="tbl_pedidos_central" class=" table table-bordered table-striped table-hover datatable datatable-Conciliation">
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <div id="pnl_detalle_central" style="display: block; position: relative; height: 565px; overflow: initial;">

        <div class="card">
            <div class="card-body rp_cuerpo_principal">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-2">
                            <button id="btn_buscar_central" type="button" class="btn btn-success" onclick="regresarAPanelPrincipalCentral()"><i class="fa fa-arrow-circle-left fa-2x"> </i> Regresar</button>
                        </div>
                        <div class="col-sm-3">
                            <label class="rp_titulo_estado" style="font-size: large !important;" id="lbl_detalle_cabecera_estado_central"></label>
                        </div>
                        <div class="col-sm-4">
                            <br>
                            <label class="rp_titulo_estado" style="font-size: small !important;" id="lbl_detalle_cabecera_estado_mensaje_central"></label>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        </br>


        <table id="tbl_detalle_principal_central" style="width:100%; font-size: x-small;">
            <tr>
                <th width="44%" valign="top" style="max-width: 30px; padding: 8px;">
                    <div class="card">
                        <div class="card-header rp_encabezado_detalle">
                            <b>Pedido:</b> <label id="detalle_encabezado_orden_pedido_id_central"></label>
                        </div>
                        <div class="card-body">
                            <div class="mb-1">
                                <div class="list-group-item">
                                    <h5>Datos de pedido</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td><b>Código App: </b></td>
                                                <td><label id="detalle_codigo_app_central"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Restaurante: </b></td>
                                                <td><label id="detalle_restaurante_central"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Tipo de servicio: </b></td>
                                                <td><label id="detalle_tipo_servicio_central"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Tipo pickup: </b></td>
                                                <td><label id="detalle_tipo_pickup_central"></label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="list-group-item">
                                    <h5>Detalle</h5>
                                    <table id="tbl_detalle_pedido_producto_central" class="table table-sm" style="max-width: 350px;">
                                    </table>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="list-group-item">
                                    <h5>Datos de cliente</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td><b>Nombre: </b></td>
                                                <td><label id="detalle_cliente_nombre_central"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Dirección: </b></td>
                                                <td><label id="detalle_cliente_direccion_central"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Email: </b></td>
                                                <td><label id="detalle_cliente_email_central"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Teléfono: </b></td>
                                                <td><label id="detalle_cliente_telefono_central"></label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-1">
                                <li class="list-group-item">
                                    <h5>Pago: </h5>
                                    <table class="table table-sm" id="tbl_detalle_formas_pago_central">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </li>
                            </div>

                            <div class="mb-1">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <h5>Autorizaciones de pago: </h5>
                                    </li>
                                    <li class="list-group-item">
                                        <table class="table table-sm" id="tbl_detalle_autorizaciones_pago_central">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </th>

                 <th width="42%" valign="top" style="max-width: 30px; padding: 8px;">
                    <div class="card">
                        <div class="card-header rp_encabezado_detalle">
                            <div style="float:left; "><b>Factura: </b> <label id="detalle_encabezado_factura_id_central"></label></div>
                            <div style="float:right; "><b>Hora Pickup: </b><label id="detalle_encabezado_hora_pickup_central"></div>
                        </div>
                        <div class="card-body">
                            <div class="mb-1">
                                <div class="list-group" style="margin-bottom: 0px !important;">
                                    <li class="list-group-item">
                                        <h5>Impresiones de factura</h5>
                                        <table id="tbl_detalle_impresion_factura_central" class="table table-sm font-sm">
                                        </table>
                                    </li>

                                    <li class="list-group-item">
                                        <h5>Impresiones de Orden de pedido</h5>
                                        <table id="tbl_detalle_impresion_orden_pedido_central" class="table table-sm font-sm">
                                        </table>
                                    </li>
                                </div>
                            </div>

                            <div class="mb-1">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <h5>Factura generada</h5>
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr id="df_id_central">
                                                    <td><b>ID: </b></td>
                                                    <td><label id="detalle_factura_id_central"></label></td>
                                                </tr>
                                                <tr id="df_fecha_insercion_central">
                                                    <td><b>Fecha inserción: </b></td>
                                                    <td><label id="detalle_factura_fecha_central"></label></td>
                                                </tr>
                                                <tr id="df_valor_total_central">
                                                    <td><b>Valor Total: </b></td>
                                                    <td><label id="detalle_factura_valor_total_central"></label></td>
                                                </tr>
                                                <tr id="df_valor_neto_central">
                                                    <td><b>Valor Neto: </b></td>
                                                    <td><label id="detalle_factura_valor_neto_central"></label></td>
                                                </tr>
                                                <tr id="df_valor_iva_central">
                                                    <td><b>Valor IVA: </b></td>
                                                    <td><label id="detalle_factura_valor_iva_central"></label></td>
                                                </tr>
                                                <tr id="df_valor_descuento_central">
                                                    <td><b>Valor Descuento: </b></td>
                                                    <td><label id="detalle_factura_valor_descuento_central"></label></td>
                                                </tr>

                                                <tr id="df_rechazado_central" class="rp_color_rechazado">
                                                    <td colspan="2" class="rp_color_letra_blanco" >NO SE ENCONTRÓ CABECERA DE FACTURA</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </li>
                                    <li class="list-group-item">
                                        <table id="tbl_detalle_factura_pickup_central" class="table table-sm">
                                        </table>
                                    </li>
                                </ul>
                            </div> 

                        </div>

                    </div>

                </th> 
            </tr>
        </table>
    </div>



</body>

</html>