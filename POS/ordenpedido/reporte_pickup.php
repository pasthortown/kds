<!DOCTYPE html> 
<html lang="es">

<head>
    <link rel="stylesheet" type="text/css" href="../css/reporte_pickup.css?v=1.0" />
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap-toggle.min.css" />

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="../js/asset/fonts/font-awesome.min.css">
    
    <script src="../bootstrap/plugins/forms/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="../bootstrap/js/bootstrap-toggle.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/teclado.js"></script>
    <script type="text/javascript" src="../js/ajax_reporte_pickup.js"></script>

    <!--Scripts para alertas-->
    <link rel="stylesheet" href="../css/alertify.core.css" />
    <link rel="stylesheet" href="../css/alertify.default.css" />
    <script type="text/javascript" src="../js/alertify.js"></script>


</head>

<body>

    <div id="pnl_principal" style="display: block; position: relative; height: 565px; overflow: initial;" class="card">
        <div class="card-header rp_cabecera_principal">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        Listado de Pedidos Pickup
                    </div>
                    <div class="col-sm-3 rp_cabecera_centro">
                        Hay <label id="lbl_cabecera_nuevos_pedidos"></label> nuevo(s) pedido(s) <button type="button" class="btn btn-danger rp_boton_refrescar" onclick="buscar()"><i class="fa fa-refresh"></i></button>
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn rp_div_alinear_derecha rp_boton_filtro"><i class="fa fa-filter" data-toggle="collapse" data-target="#div_filtros"></i></button>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body rp_cuerpo_tabla_principal">
            <div class="container col-sm-12"  style="margin-left: 0px; padding-left: 0px; padding-right: 0px; margin-right: 0px;">
                <div id="div_filtros" class="row col-sm-12"  style="margin-left: 0px; padding-left: 1px; padding-right: 1px; margin-right: 0px;">
                    <div class="col-sm-12" style="padding-left: 0px; padding-right:0px">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 25%">Estado Pedido</th>
                                    <th scope="col" style="width: 25%">Mostrar</th>
                                    <th scope="col" colspan="2" style="width: 50%">Búsqueda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_amarillo" style="padding-bottom: 0px; padding-top: 0px; vertical-align: middle">
                                        <div style="height: 100%;">
                                            <p class="rp_div_alinear_izquierda" style="margin-bottom: 0px;">Ingresado</p>
                                            <p class="rp_div_alinear_derecha" style="margin-bottom: 0px;"><label class="rp_label_numero_pedidos" id="lbl_pedidos_ingresados"></label></p>
                                        </div>
                                    </td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="chk_ingresado" checked  type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                    <td style="padding-bottom: 0px; padding-top: 0px; vertical-align:middle;">Nombre Cliente</td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="inp_nombre_cliente" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_naranja" style="padding-bottom: 0px; padding-top: 0px">
                                        <div>
                                            <p class="rp_div_alinear_izquierda" style="margin-bottom: 0px;">Preparando</p>
                                            <p class="rp_div_alinear_derecha" style="margin-bottom: 0px;"><label class="rp_label_numero_pedidos" id="lbl_pedidos_preparando"></label></p>
                                        </div>
                                    </td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="chk_preparando" checked type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                    <td style="padding-bottom: 0px; padding-top: 0px; vertical-align:middle;">Identificación Cliente</td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="inp_identificacion" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_verde" style="padding-bottom: 0px; padding-top: 0px">
                                        <div>
                                            <p class="rp_div_alinear_izquierda" style="margin-bottom: 0px;">Listo</p>
                                            <p class="rp_div_alinear_derecha" style="margin-bottom: 0px;"><label class="rp_label_numero_pedidos" id="lbl_pedidos_listo"></label></p>
                                        </div>
                                    </td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="chk_listo" type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                    <td style="padding-bottom: 0px; padding-top: 0px; vertical-align:middle;">Código app</td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="inp_codigo_app" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_azul" style="padding-bottom: 0px; padding-top: 0px">
                                        <div>
                                            <p class="rp_div_alinear_izquierda" style="margin-bottom: 0px;">Entregado</p>
                                            <p class="rp_div_alinear_derecha" style="margin-bottom: 0px;"><label class="rp_label_numero_pedidos" id="lbl_pedidos_entregado"></label></p>
                                        </div>
                                    </td>
                                    <td style="padding-bottom: 0px; padding-top: 0px"><input inputmode="none"  id="chk_entregado" type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                    <td colspan="2" align="center" style="padding-bottom: 0px; padding-top: 0px"><button id="btn_buscar" type="button" class="btn btn-success rp_boton_busqueda" onclick="buscar()">Buscar</button> </td>
                                </tr>
                                <tr>
                                    <td class="rp_fondo_morado rp_fila_alinear_centro_vertical">
                                        <div>
                                            <p class="rp_div_alinear_izquierda">Transferido</p>
                                            <p class="rp_div_alinear_derecha"><label class="rp_label_numero_pedidos" id="lbl_pedidos_transferido"></label></p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none"  id="chk_transferido" type="checkbox" data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                    <td colspan="2"></td>
                                </tr>
                                <!-- <tr>
                                    <td class="rp_fila_alinear_centro_vertical rp_fondo_rojo">
                                        <div>
                                            <p class="rp_div_alinear_izquierda"> Anulado</p>
                                            <p class="rp_div_alinear_derecha">0</p>
                                        </div>
                                    </td>
                                    <td><input inputmode="none"  type="checkbox" checked data-toggle="toggle" data-on="SI" data-off="NO" data-onstyle="success" data-offstyle="warning"></td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                    <!--                    <div class="col-sm-6" style="padding-left: 0px; padding-right:0px">-->
                    <!--                        <table class="table">-->
                    <!--                            <thead>-->
                    <!--                                <tr>-->
                    <!--                                    <th scope="col" colspan="2">Búsqueda</th>-->
                    <!--                                </tr>-->
                    <!--                            </thead>-->
                    <!--                            <tbody>-->
                    <!--                                <tr>-->
                    <!--                                    <td style="vertical-align: bottom !important;"><div>Nombre Cliente</div></td>-->
                    <!--                                    <td><input inputmode="none"  id="inp_nombre_cliente" type="text" class="form-control"></td>-->
                    <!--                                </tr>-->
                    <!--                                <tr>-->
                    <!--                                    <td style="vertical-align: bottom !important;">Identificación Cliente</td>-->
                    <!--                                    <td><input inputmode="none"  id="inp_identificacion" type="text" class="form-control"></td>-->
                    <!--                                </tr>-->
                    <!--                                <tr>-->
                    <!--                                    <td style="vertical-align: bottom !important;">Código app</td>-->
                    <!--                                    <td><input inputmode="none"  id="inp_codigo_app" type="text" class="form-control"></td>-->
                    <!--                                </tr>-->
                    <!--                                <tr>-->
                    <!--                                    <td style="vertical-align: bottom !important;" colspan="2" align="center"> <button id="btn_buscar" type="button" class="btn btn-success rp_boton_busqueda" onclick="buscar()">Buscar</button> </td>-->
                    <!--                                </tr>-->
                    <!---->
                    <!--                            </tbody>-->
                    <!--                        </table>-->
                    <!---->
                    <!--                    </div>-->
                    <!-- <div class="col-sm-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="4">Filtro de tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4">Rango de fechas</td>
                                </tr>
                                <tr>
                                    <td>Desde</td>
                                    <td><input inputmode="none"  id='cal_desde' type="text" class="form-control"></td>
                                    <td>Hasta</td>
                                    <td><input inputmode="none"  id='cal_hasta' type="text" class="form-control"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
                </div>


                <div class="row col-sm-12" style="margin-left: 0px; padding-left: 1px; padding-right: 1px; margin-right: 0px;">
                    <div class="col-sm-12" style="padding-left: 0px; padding-right:0px">
                        <div class="table-responsive">
                            <div style="text-align: right; padding-bottom: 10px">
                                <button id="btn_anterior" type="button" class="btn btn-success rp_boton_paginador" onclick="anterior()">Anterior</button>
                                <button id="btn_siguiente" type="button" class="btn btn-success rp_boton_paginador" onclick="siguiente()">Siguiente</button>
                            </div>
                            <table id="tbl_pedidos" class=" table table-bordered table-striped table-hover datatable datatable-Conciliation" style="font-size: 11px">
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <div id="pnl_detalle" style="display: block; position: relative; height: 565px; overflow: initial;">
        <div class="card">
            <div class="card-body rp_cuerpo_principal">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <button id="btn_buscar" type="button" class="btn btn-success" onclick="regresarAPanelPrincipal()"><i class="fa fa-arrow-circle-left fa-2x"> </i> Regresar</button>
                        </div>
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-3">
                            <label class="rp_titulo_estado" id="lbl_detalle_cabecera_estado"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        </br>



        <table id="tbl_detalle_principal" style="width:100%; font-size: x-small;">
            <tr>
                <th width="44%" valign="top" style="max-width: 30px; padding: 8px;">
                    <div class="card">
                        <div class="card-header rp_encabezado_detalle">
                            <b>Pedido:</b> <label id="detalle_encabezado_orden_pedido_id"></label>
                        </div>
                        <div class="card-body">
                            <div class="mb-1">
                                <div class="list-group-item">
                                    <h5>Datos de pedido</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td><b>Código App: </b></td>
                                                <td><label id="detalle_codigo_app"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Restaurante: </b></td>
                                                <td>K071</td>
                                            </tr>
                                            <tr>
                                                <td><b>Tipo de servicio: </b></td>
                                                <td><label id="detalle_tipo_servicio"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Tipo pickup: </b></td>
                                                <td><label id="detalle_tipo_pickup"></label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="list-group-item">
                                    <h5>Detalle</h5>
                                    <table id="tbl_detalle_pedido_producto" class="table table-sm" style="max-width: 350px;">
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
                                                <td><label id="detalle_cliente_nombre"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Dirección: </b></td>
                                                <td><label id="detalle_cliente_direccion"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Email: </b></td>
                                                <td><label id="detalle_cliente_email"></label></td>
                                            </tr>
                                            <tr>
                                                <td><b>Teléfono: </b></td>
                                                <td><label id="detalle_cliente_telefono"></label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-1">
                                <li class="list-group-item">
                                    <h5>Pago: </h5>
                                    <table class="table table-sm" id="tbl_detalle_formas_pago">
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
                                        <table class="table table-sm" id="tbl_detalle_autorizaciones_pago">
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
                            <div style="float:left; "><b>Factura: </b> <label id="detalle_encabezado_factura_id"></label></div>
                            <div style="float:right; "><b>Hora Pickup: </b><label id="detalle_encabezado_hora_pickup"></div>
                        </div>
                        <div class="card-body">
                            <div class="mb-1">
                                <div class="list-group" style="margin-bottom: 0px !important;">
                                    <li class="list-group-item">
                                        <h5>Impresiones de factura</h5>
                                        <table id="tbl_detalle_impresion_factura" class="table table-sm font-sm">
                                        </table>
                                    </li>

                                    <li class="list-group-item">
                                        <h5>Impresiones de Orden de pedido</h5>
                                        <table id="tbl_detalle_impresion_orden_pedido" class="table table-sm font-sm">
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
                                                <tr>
                                                    <td><b>ID: </b></td>
                                                    <td><label id="detalle_factura_id"></label></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Fecha inserción: </b></td>
                                                    <td><label id="detalle_factura_fecha"></label></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor Total: </b></td>
                                                    <td><label id="detalle_factura_valor_total"></label></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor Neto: </b></td>
                                                    <td><label id="detalle_factura_valor_neto"></label></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor IVA: </b></td>
                                                    <td><label id="detalle_factura_valor_iva"></label></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor Descuento: </b></td>
                                                    <td><label id="detalle_factura_valor_descuento"></label></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </li>
                                    <li class="list-group-item">
                                        <table id="tbl_detalle_factura_pickup" class="table table-sm">
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


    <div id="dlg_impresion_previa" class="no-titlebar" style="width:350px !important; top: 0px !important; ">
    </div>




</body>

</html>