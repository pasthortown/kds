<?php 
//////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////////////
////////DESCRIPCION: Carga de Combos//////////////////////////////////
/////////////////////En las Mesas/////////////////////////////////////
///////TABLAS INVOLUCRADAS: //////////////////////////////////////////
///////FECHA CREACION: 16-Enero-2014//////////////////////////////////
//////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Jorge Tinoco //////////////////////////////////
///////FECHA: 18/07/2014 /////////////////////////////////////////////
///////CAMBIO REALIZADOS: Validación de permisos de perfil y /////////
///////Modificación de botones de piso y area ////////////////////////
//////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Jorge Tinoco //////////////////////////////////
///////FECHA: 02/03/2015 /////////////////////////////////////////////
///////CAMBIO REALIZADOS: Solicitud numero de personas por mesa //////
//////////////////////////////////////////////////////////////////////
//verificación de sesión iniciada
session_start();
if (!isset($_SESSION['validado'])) {
    // en caso de no existir sesión iniciada, se destruye toda información
    include_once '../seguridades/seguridad.inc';
} else {
    include_once '../system/conexion/clase_sql.php';
    include_once '../clases/clase_seguridades.php';
    include_once "../clases/clase_ordenPedido.php";
    $permisos = new seguridades();
    $tomaPedido = new menuPedido();
    $resId = $_SESSION['rstId'];
    $nomRestaurante = $_SESSION['rstNombre'];
    $codRestaurante = $_SESSION['rstCodigoTienda'];
    $cadId = $_SESSION['cadenaId'];
    $cadena = $_SESSION['cadenaNombre'];
    $usr_id = $_SESSION['usuarioId'];

    $cargoPisoArea = $_SESSION['cargoPisoArea'];
    $ValidacionRucCodigoMensaje = $_SESSION['ValidacionRucCodigoMensaje'];
    $_SESSION['ValidacionRucCodigoMensaje']='';
    $botones = $permisos->fn_accesoPermisosPerfilBotones($usr_id, "USERMESAS");
      //validar que el restaurant tiene activa la politica para validar email con plugthem
    $resultado_verificar_email_plug = $tomaPedido->fn_consulta_generica_escalar("EXEC dbo.verificarPoliticaValidaEmailPlugthemSimplified '%s'", $resId);
    $restaurant_valida_email = isset($resultado_verificar_email_plug["Activo"]) ? $resultado_verificar_email_plug["Activo"] : 0;
  
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
            

        <script type="text/javascript" src="../js/configuracion.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" />
        <link rel="StyleSheet" href="../css/tomaPedido.css" type="text/css" />
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="../css/bloquear_acceso.css" />

        <!-- Estilos para keyboard buscador -->
        <link rel="stylesheet" type="text/css" href="../css/est_teclado.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
        <link rel="stylesheet" type="text/css" href="../css/visor_factura.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_botonesbarra.css" />

        <!-- Estilos para keyboard credenciales -->
        <link rel="stylesheet" type="text/css" href="../css/teclado.css" />

        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
        <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js" type="text/javascript"></script> -->
        
        <script src="../js/asset/moment/moment.min.js" type="text/javascript"></script>

        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/ajax_trade.js"></script>
        <script src="../js/cnd/jsdelivr/net/npm/sweetalert2@11.js"></script>
        <script src="../js/ajax_statusVersion.js"></script>
        <script type="text/javascript" src="../js/ajax_userMesas.js"></script>
        <script type="text/javascript" src="../js/ajax_dragontail.js"></script>
        <script type="text/javascript" src="../js/ajax_reporte_pickup.js" ></script>
        <script type="text/javascript" src="../js/ajax_monitor.js"></script>
        <script type="text/javascript" src="../js/ajax_reporte_pickup_central.js" ></script>

        <!--Scripts para alertas-->
        <link rel="stylesheet" href="../css/alertify.core.css" />
        <link rel="stylesheet" href="../css/alertify.default.css" />
        <script type="text/javascript" src="../js/alertify.js"></script>

        <!-- Scripts para keyboard buscador -->
        <script type="text/javascript" src="../js/jquery.keypad.js"></script>
        <link type="text/css" href="../css/jquery.keypad.css" rel="stylesheet">
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script type="text/javascript" src="../js/teclado.js"></script>
        <!-- JavaScript Cambio datos cliente  -->
        <script type="text/javascript" src="../js/ajax_cambioDatosCliente.js"></script>

        <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
        <link href="../js/asset/fonts/icon.css" rel="stylesheet">
        <link type="text/css" href="../css/cndjs/cloudflare/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="../css/media_orden_pedido.css" />
<script>
  $( function() {
    $( "#tabs" ).tabs();
  } );
  </script>
            

        <script type="text/javascript">

        </script>
        <style>
            div[aria-describedby="anulacionesContenedor"] > #anulacionesContenedor {
                height: 349px !important;
            }
        </style>

        <title>Distribuci&oacute;n de Mesas</title>
    </head>
            

    <body onload="ini()" onkeypress="parar()" onclick="parar()" style="overflow-x: hidden; overflow-y: auto">

    <input type="hidden" id="hid_notaCredito" />
            <input type="hidden" name="hide_tipo_servicio" id="hide_tipo_servicio"/>
            <input type="hidden" name="config_servicio_domicilio" id="config_servicio_domicilio"/>
            <input type="hidden" name="config_servicio_pickup" id="config_servicio_pickup"/>
            <input id="aplica_nc_sinconsumidor"     type="hidden"/>
            <input id="proveedor_tracking"          type="hidden"/>
            <input id="cambio_estados_automatico"   type="hidden"/>
            <input id="url_bringg_crear"            type="hidden"/>
            <input id="url_bringg_anular"           type="hidden"/>
            <input id="url_api_motorizados"         type="hidden"/>

        <input id="semaforoConfig" type="hidden" />
        <input id="ValidacionRucCodigoMensaje" name="ValidacionRucCodigoMensaje"  type="hidden" value="<?php echo $ValidacionRucCodigoMensaje ?>"/>
        <input id="ValidacionSesionApi" name="ValidacionSesionApi"  type="hidden" value="<?php echo $_SESSION['cambio_caje_v1_v2'] ?>"/>
        <?php $_SESSION['cambio_caje_v1_v2']=''; ?>
        <!--<body   style="overflow-x: hidden; overflow-y: hidden">-->
        <div id="formsEnvio"></div>
        <div class="container-fluid" style="background-color: #e6e9ef">

            <div class="col-xs-2" style=" background-color: #e6e9ef ;  height: 660px; border-radius: 0px 30px 0px 0px">

                <table align="left" border="0" width="100%">
                    <tr>
                        <td colspan="2" align="left"><label><?php echo $codRestaurante . '  -  ' . htmlentities($nomRestaurante); ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="2" id="perfil_usuario"></td>
                    </tr>
                    <tr>
                        <td colspan="2" id="nombre_usuario"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="width: 100%;  height: 2px; background-color: #c6d0dc;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Pisos:</b></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="piso"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="width: 100%;  height: 2px; background-color: #c6d0dc;" />
                        </td>
                    </tr>
                    <tr>
                        <td> <label><b>&Aacute;rea :</b></label></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="area"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                    <tr id="codigo-confirmacion-agregadores">
                        <td colspan="2">
                            <div id="generar_codigo">
                                <input type="text" class="form-control input_code" id="numero_rest" value="0000" disabled="disabled">
                                <button id="generar_code" class="area_button boton_Accion_EE1" onclick="obtenerActualizarCodigo(2)"><i class="fa-solid fa-arrows-rotate"></i></button>
                                <button id="imprimir_code" class="area_button boton_Accion_EE1" onclick="imprimirCodigo()"><i class="fa-solid fa-print"></i></button>

                            </div>
                        </td>
                    </tr>
                    
                    <!--
                     <tr>
                        <td align="center"><a href="reservas/reservas.php"><input inputmode="none"  type="button" value="Reservas" name="reservas" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../corteCaja/corteCaja.php"><input inputmode="none"  type="button" value="Corte Caja" name="Corte Caja" /></a></td>
                    </tr> -->
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>



                    </table>
                </table>
            </div>



            <div class="container">
                <div class="row" style="background: url(../imagenes/ordenpedido/bg_tomaPedido.png)">
                    <!--Panel izquierdo, pisos y areas.-->


                    <!--Panel central plano y mesas-->
                    <div class="col-xs-9 center-block" style="  width: 73%; margin-top: 2% ; margin-left: 2%" id="cuado">
                        <!--             style=" width: 65%; " -->

                        <!-- <div id="contenedorTotal" style="  margin: auto">-->
                        <!--                            <div class="separador" style="margin-top:0px;"></div>-->

                            <div id="mesas" style="position: absolute;  top: 0px; "></div>
                            <input inputmode="none"  type="hidden" name="hide_tipo_servicio" id="hide_tipo_servicio" value="<?php echo $_SESSION['TipoServicio']; ?>"/>
                            <input inputmode="none"  type="hidden" id="est_id"  value="<?php echo $_SESSION['estacionId']; ?>"/>
                            <input inputmode="none"  type="hidden" id="idPeriodo"  value="<?php echo $_SESSION['IDPeriodo']; ?>"/>
                            <input inputmode="none"  type="hidden" id="txtUsuarioLogin"  value="<?php echo $_SESSION['usuarioIdAdmin']; ?>"/>
                            <input inputmode="none"  type="hidden" id="cargoPisoArea"  value="<?php echo $cargoPisoArea; ?>"/>

                        <div id="detalle_plu" style="display: none;">
                            <div class="container-fluid table-responsive">
                                <table class="table table-hover" style="background-color: #e6e9ef;border-radius: 5px;">
                                    <thead>
                                        <tr>
                                            <th>Mesa</th>
                                            <th>Cajero</th>
                                            <th>Total</th>
                                            <th>Tiempo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listado_mesas">

                                    </tbody>
                                </table>
                            </div>
                            <!--                                <div class="container-fluid">
                                                                    <div class="row">
                                                                        <div class="col-xs-6" id="detalle_plu">
                                
                                                                        </div>
                                                                        <div class="col-xs-6">
                                
                                                                        </div>
                                                                    </div>
                                                                </div>-->

                        </div>

                        <div id="transferencia_cuenta" style="display: none;">
                            <div class="container-fluid table-responsive">
                                <table class="table table-hover" style="background-color: #e6e9ef;border-radius: 5px;">
                                    <thead>
                                        <tr>
                                            <th>Mesa</th>
                                            <th>Usuario</th>
                                            <th>a</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listado_cuentas">

                                    </tbody>
                                </table>


                            </div>
                        </div>


                        <div id="contenedorRetomarOrden"></div>

                            <input inputmode="none"  type="hidden" value="<?php echo htmlentities($cadId); ?>" id="txtCadena" />
                            <input inputmode="none"  type="hidden" value="<?php echo htmlentities($resId); ?>" id="txtRest" />
                            <input inputmode="none"  type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>" id="txtUsr_Id" />

                            <input inputmode="none"  type="hidden" value="userMesas" id="txtPantalla" />
                            <input inputmode="none"  type="hidden" value="0" id="switch" />
                        <input  type="hidden" value="" id="txtClienteCedula" />
                        <input  type="hidden" value="" id="txtClienteNombre" />
                        <input  type="hidden" value="" id="txtClienteTelefono" />
                        <input  type="hidden" value="" id="txtClienteEmail" />


                        <div style="position:fixed;clear:both;width:1024px;top: 650px; display: none" class="contenedorInferior">
                            <div id="barraCajero">
                                <h4><b>Usuario:</b> <?php echo $usr_nombre; ?></h4>
                            </div>
                            <div id="barraPrincipal">
                                <?php
                                if ($botones != NULL) {
                                    for ($i = 0; $i < count($botones); $i++) {
                                        switch ($botones[$i]) {
                                            case "Corte Caja":
                                                print "<button id='cortecaja' onclick='fn_irCorteCaja()'>Corte Caja</button>";
                                                break;
                                            case "Reservar":
                                                print "<button id='reservas' onclick='fn_irReservas()'>Reservas</button>";
                                                break;
                                            case "Funciones Gerente":
                                                print "<button id='funcionesGerente' onclick='fn_irFuncionesGerente()'>Funciones Gerente</button>";
                                                break;
                                            case "Abrir":
                                                print "	<script type='text/javascript'>
								$(document).ready(function(){ $('#switch').val(1); })
							</script>";
                                                break;
                                        }
                                    }
                                } else {
                                ?>
                                    <script type="text/javascript">
                                        //fn_alertaPermisosPerfil("Por favor, Comuníquese con soporte para que le asignen los permisos necesarios.");
                                    </script>
                                <?php
                                }
                                ?> <tr>
                                    <!--<td>Tú perfil no tiene habilitado botones de acción para esta pantalla</td>-->
                                    <td><button id='btn_sistema' onclick='fn_salirSistema()' title="Salir del Sistema">Salir Sistema</button></td>
                                </tr>
                            </div>
                        </div>
                        <!--</div>-->
                    </div>

                    <!-- INICIO Contenedor Impresiones Error -->
                    <div id="cnt_pedidos_error" class="col-xs-9 center-block" style="display: none; width: 73%; margin-top: 2%; margin-left: 2%">
                        <!-- INICIO Lista Impresiones Error -->
                        <div id="lst_imp_error">
                            <div id="lst_error_imp">
                                <div class="cabecera_lst">
                                    <div class='codigo_transaccion'>&nbsp;<b>Código</b></div>
                                    <div class='total_transaccion'><b>Total</b></div>
                                    <div class='error_transaccion'><b>Error</b></div>
                                    <div class='tipo_transaccion'><b>Tipo</b></div>
                                </div>
                                <ul id="lst_error"></ul>
                            </div>
                        </div>
                        <!-- FIN LISTA DE PEDIDOS -->
                    </div>
                    <!-- FIN Contenedor Impresiones Error -->
                        <!-- Fin Panel Central para Mesas y Pedidos -->

                        <!--Inicio Panel central para reporte Pickup-->
                        <div id="pnl_pickup" class="col-xs-9 center-block"  style="width: 82%; margin-top: 1% ; margin-left: 1% ">
                            <div id="tabs">
                                <ul>
                                  <li><a href="#tabs-1">Pedidos</a></li>
                                  <li><a href="#tabs-2">Pedidos en Error <label class="rp_label_error_pedidos" id="lbl_cantidad_error"></label> </a></li>
                                </ul>
                                <div id="tabs-1" style="padding: 0px;background: white;">
                                </div>
                                <div id="tabs-2" style="padding: 0px;background: white;">
                            </div>
                        </div>

                        </div>
                        <!--Inicio Panel central para reporte Pickup-->



                    <!-- Pedidos App y Web -->
                    <div id="cnt_pedidos" class="col-xs-9 center-block" style="display: none; width: 73%; margin-top: 2%; margin-left: 2%">

                        <div class="col-md-12" id="div_busqueda">
                            <div class="col-md-3" style="margin-left: 0">
                                <span id="sltd_codigo_app"></span><br/>
                                <span id="sltd_estado_app"></span>
                            </div>
                            <div class="col-md-4"><select id="cboEstadoPedido" name="cboEstadoPedido" class="cboEstadoPedido"></select></div>
                            <div class="col-md-4" id="busqueda">
                                <input inputmode="none"  type="text" id="parBusqueda" value="" style="margin: 6px 0 6px 25px; width: 225px; height: 40px; font-size: 30px; color: #444;" />
                            </div>
                        </div>
                        <!-- LISTA DE PEDIDOS -->
                        <div id="listaPedido">
                            <div id="listado_app">
                                <div class="cabeceraOrden">
                                    <div class='lista_medios'><b>Medio</b></div>  
                                    <div class='codigo_app'>&nbsp;<b>Transacci&oacute;n</b></div>
                                    <div class='cliente_app'><b>Cliente</b></div>
                                    <div class='lista_motorizado'><b>Motorizado</b></div>
                                    <div class='lista_estado'><b>Estado</b></div>
                                    <div class='lista_semaforo'><span class="material-icons">traffic</span></div>
                                    <div class='lista_tiempo'><b>T. Espera</b></div>
                                    <div class='lista_tiempo'><b>T. Desp.</b></div>
                                    <div class='lista_tiempo'><b>T. Entr</b></div>
                                    <div class='lista_tiempo'><b>T. Tot</b></div>
                                    
                                </div>
                                <ul id="listado_pedido_app"></ul>
                                <ul id="listado_pendientes" style="display:none"></ul>
                            </div>
                        </div>

                        <!-- DETALLE PEDIDO -->
                        <div id="detalle_pedido">
                            <table style="background-color:#333;height:40px;line-height:40px;width:700px;color:#fff;font-size:12px">
                                <tr>
                                    <th style="padding-left: 10px;">Detalle Pedido</th>
                                </tr>
                            </table>
                            <table id="tbl_cabecera_pedido" class="cabecera_pedido">
                                <tr id="codigo" class="secundario">
                                    <th class="separacion-superior">CODIGO APP:</th>
                                    <th id="codigo_app" class="separacion-superior" style="color: #3B7808; font-size: 18px"></th>
                                    <th class="separacion-superior">FECHA:</th>
                                    <td id="fecha" class="separacion-superior"></td>
                                </tr>
                                <tr class="fila secundario">
                                    <th>ESTADO:</th>
                                    <td id="estado"></td>
                                    <th>MEDIO:</th>
                                    <td id="medio"></td>
                                </tr>
                                <tr class="fila secundario">
                                    <th>FECHA ESTADO:</th>
                                    <td id="fecha_estado"></td>
                                    <th>USUARIO ESTADO:</th>
                                    <td id="user_estado"></td>
                                </tr>
                                <tr class="fila secundario">
                                    <th>FORMA DE PAGO:</th>
                                    <td id="formapago" ></td>
                                    <th>MEDIO DE PAGO:</th>
                                    <td id="mediopago" ></td>
                                </tr>
                                <tr class="fila secundario" id="motivo_anulacion_contenedor">
                                    <th>MOTIVO ANULACIÓN:</th>
                                    <td id="motivo_anulacion" colspan="3" ></td>
                                   
                                </tr>
                                <!-- Datos Transferencia -->
                                <tr id="sprdr_trans" class="separador separacion-superior">
                                    <td colspan="4">Información Transferencia</td>
                                </tr>
                                <tr id="cnt_transferencia" class="secundario">
                                    <th class="separacion-superior">TRANSFERENCIA:</th>
                                    <th id="tipo_transferencia" class="separacion-superior" style="color: #3B7808; font-size: 18px"></th>
                                </tr>
                                <tr id="cnt_detalle_transferencia" class="secundario">
                                    <th class="separacion-superior">LOCAL:</th>
                                    <th id="local_trans" class="separacion-superior" style="color: #3B7808; font-size: 18px"></th>
                                    <th class="separacion-superior">USUARIO:</th>
                                    <td id="usuario_trans" class="separacion-superior"></td>
                                </tr>
                                <!-- Motorizado Asignado -->
                                <tr class="separador">
                                    <td colspan="4" class="separacion-superior">Información Motorizado</td>
                                </tr>
                                <tr id="motoriado" class="fila secundario">
                                    <th class=" separacion-superior separacion-inferior">MOTORIZADO:</th>
                                    <td id="motorizado_nombres" class="separacion-superior separacion-inferior"></td>
                                    <th class="separacion-superior separacion-inferior">M. TELÉFONO:</th>
                                    <td id="motorizado_telefono" class="separacion-superior separacion-inferior"></td>
                                </tr>
                                <!-- Datos del Cliente -->
                                <tr class="separador separacion-superior">
                                    <td colspan="4">Información Cliente</td>
                                </tr>
                                <tr class="fila secundario">
                                    <th class="separacion-superior">CLIENTE:</th>
                                    <td id="cliente" class="separacion-superior"></td>
                                    <th class="separacion-superior">DOCUMENTO:</th>
                                    <td id="documento" class="separacion-superior"></td>
                                </tr>
                                <tr id="telefono" class="fila secundario">
                                    <th>TELEFONO:</th>
                                    <td colspan="3"></td>
                                </tr>
                                <!-- Información Entrega -->
                                <tr class="separador separacion-superior">
                                    <td colspan="4">Información de Entrega</td>
                                </tr>
                                <tr id="direccion" class="fila secundario">
                                    <th class="separacion-superior">DIRECCION:</th>
                                    <td colspan="3" class="separacion-superior"></td>
                                </tr>
                                <tr id="adicional" class="fila secundario separacion-superior">
                                    <th>DATOS ADICIONALES:</th>
                                    <td colspan="3"></td>
                                </tr>
                                <tr id="observacion" class="fila secundario">
                                    <th>OBSERVACIONES:</th>
                                    <td colspan="3"></td>
                                </tr>
                            </table>
                            <table id="tbl_detalle_pedido" class="detalle_pedido">
                            </table>
                        </div>
                        <!-- Fin: Pedidos App y Web -->
                    </div>
                    <!-- Pedidos App y Web -->





                    <!--Panel derecho, opciones para el menu de planos y mesas.-->
                    <div id="cntMesas" class="col-xs-1 pull-right" style=" background-color: #e6e9ef ; height: 660px; border-radius: 30px 0px 0px 0px; width: 12.33333333%;
                             margin-right: -40px; font-size: 12px;">
                        <div class="mesas_info">
                            <br />
                            <div> <img src="../imagenes/mesa/guia_Disponible.png" width="30" height="30" alt="Disponible" /> Disponible </div>
                            <div> <img src="../imagenes/mesa/guia_En Uso.png" width="30" height="30" alt="En Uso" /> En uso </div>
                            <div> <img src="../imagenes/mesa/guia_Cuenta.png" width="30" height="30" alt="Cuenta" /> Precuenta</div>
                            <!--<div> <img src="../imagenes/mesa/guia_Reservado.png" width="40" height="40" alt="Reservado" /> Reservado</div>-->
                            <div> <img src="../imagenes/mesa/guia_Mis Mesas.png" width="30" height="30" alt="Mis Mesas" /> Mis mesas </div>
                        </div>
                        <div class="medios_info">
                           
                        </div>

                        <div id="transacciones_view">

                            <hr style="width: 100%;  height: 2px; background-color: #c6d0dc;" />
                            <button class="boton_Accion_Disable1 view_btn" disabled="disabled" name="Informacion total de mesas" title="Información total de mesas" id="informacionTotalMesas" onclick="fn_transaction_view()"></button>
                            <button class="boton_Accion_Disable1 info_mesa_E_btn" name="Informacion por mesa" id="informacionPorMesa" title="Información de mesa" disabled="disabled" id="buttonInfo" onclick="fn_opcion_informacion()"></button>
                            <button class="boton_Accion_Disable1 cuentaRapida" name="Pedido rapido" disabled="disabled" id="PedidoRapido" title="Pedido rápido" onclick="pedidoRapido(<?php echo $restaurant_valida_email; ?>)"></button>
                            <button class="boton_Accion_Disable1 funcionGerente" title="Funciones gerente" name="Funciones Gerente" id="funciones_gerente" disabled="disabled" onclick="fn_irFuncionesGerente()"></button>
                            <button class="boton_Accion_Disable1 transferirCuenta" disabled="disabled" title="Transferir Cuenta" name="Transferir Cuenta" id="transferir_cuenta" onclick="fn_botonlistarUsuario()"></button>
                            <button class="boton_Accion_Disable1 BtnSalir" title="Salir" name="Salir" id="salirSistema" disabled="disabled" onclick="fn_salir()"></button>

                            <!--<button class="boton_Accion info_unionMesa" id="buttonUnion"    onclick="fn_unionMesa()" title="Unión de mesas" onclick=""></button>-->
                            <!--<button    id="btn_modal"  onclick=""  data-toggle="modal"  data-target=".bs-example-modal-lg">Modal Info</button>-->



                            <!--                        <button class="boton_Accion_Disable "           disabled="disabled"  onclick="fn_irFuncionesGerente()">FG</button>
                                                            <button class="boton_Accion_Disable "           disabled="disabled" onclick="fn_irCorteCaja()">CJ</button>-->

                            <button class="boton_Accion_Disable" id="btn_modal" style="display: none" onclick="" data-toggle="modal" data-target=".bs-example-modal-lg">Modal Info</button>
                        </div>

                        <div id="filtro_app_fin">

                            <hr style="width: 100%;  height: 2px; background-color: #c6d0dc; margin: 4px"/>

                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_anulado" onclick="$('#cboEstadoPedido').val('ENTREGADO').change();">
                                <i class="material-icons">done</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">ENTREGADOS</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_anulado" onclick="$('#cboEstadoPedido').val('ANULADO').change();">
                                <i class="material-icons">cancel</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">ANULADOS</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_anulado" onclick="cargar_transferencia_salida()">
                                <i class="material-icons">north_east</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">T. ENVIADAS</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_anulado" onclick="cargar_transferencia_entrada()">
                                <i class="material-icons">south_west</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">T. RECIBIDAS</h6>
                            </button>

                        </div>

                        <div id="filtro_app">

                            <hr style="width: 100%;  height: 2px; background-color: #c6d0dc; margin: 4px" />

                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_principal" onclick="$('#cboEstadoPedido').val('PRINCIPAL').change();">
                                <i class="material-icons">inbox</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">TODOS</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_pendiente" onclick="$('#cboEstadoPedido').val('PENDIENTE').change();">
                                <i class="material-icons">pending_actions</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">PENDIENTE</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_recibido" onclick="$('#cboEstadoPedido').val('RECIBIDO').change();">
                                <i class="material-icons">compare_arrows</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">RECIBIDO</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_porasignar" onclick="$('#cboEstadoPedido').val('POR ASIGNAR').change();">
                                <i class="material-icons">two_wheeler</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">POR ASIGNAR</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_asignado" onclick="$('#cboEstadoPedido').val('ASIGNADO').change();">
                                <i class="material-icons">assignment_ind</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">ASIGNADO</h6>
                            </button>
                            <button class="btn_filtro_app btn btn-info" id="btn_filtro_app_encamino" onclick="$('#cboEstadoPedido').val('EN CAMINO').change();">
                                <i class="material-icons">commute</i>
                                <span badge=""></span>
                                <h6 style="font-size:10px;display:block">EN CAMINO</h6>
                            </button>
                            <br/>
                            <br/>
                            <!-- <button class="btn_filtro_app btn btn-warning" title="Salir" name="Salir" id="salirSistema" onclick="location.reload();"><i class="material-icons">arrow_back</i></button> -->
                        </div>
                    </div>

                </div>
            <div class="row">
            <div id="barraInferior" class="col-xs-12" style="background-color:#e6e9ef; height: 100px; border: 2px #e6e9ef  solid">
                    <button id="btn_notificar" class="pedidos_recibidos" style="width: 150px; display: none; background-color: #006400;" title="Notificar" name="Notificar" onclick="notificarPedido()">El pedido está listo</button>
                    <button id="btn_ver" class="pedidos_recibidos" style="width: 100px; display: none" title="Ver" name="Ver" onclick="accion()">Ver</button>
                    <button id="btn_transferir" class="pedidos_recibidos" style="width: 100px; display: none" title="Transferir" name="Transferir" onclick="transferir_pedido()">Transferir</button>
                    <button id="btn_transferir_pickup" class="pedidos_recibidos" style="width: 150px; display: none" title="Transferir Pickup" name="TransferirPickup" onclick="transferir_pedido_pickup()">Transferir Pickup</button>
                    <button id="btn_facturar" class="pedidos_recibidos" style="width: 100px; display: none" title="Facturar" name="Facturar" onclick="facturar()">Facturar</button>
                    <button id="btn_cancelar" class="pedidos_recibidos" style="width: 100px; display: none" title="Atrás" name="Cancelar" onclick="irAtras()">Atrás</button>
                    <button id="btn_en_camino" class="pedidos_recibidos" style="width: 100px; display: none" title="En Camino" name="EnCamino" onclick="fn_accionMotorizado()">En Camino</button>
                    <button id="btn_entregado" class="pedidos_recibidos" style="width: 100px; display: none" title="Entregado" name="Entregado" onclick="fn_accionMotorizado()">Entregado</button>
                    <button id="btn_asignar" class="pedidos_recibidos" style="width: 100px; display: none" title="Asignar" name="Asignar" onclick="fn_accionMotorizado()">Asignar</button>
                    <button id="btn_confirmar" class="pedidos_recibidos" style="width: 100px; display: none" title="Confirmar" name="Confirmar" onclick="fn_accionMotorizado()">Confirmar</button>
                    <button id="btn_desasignar" class="pedidos_recibidos" style="width: 100px; display: none;" title="Desasignar" name="Desasignar" onclick="confirmarDesasignarMotorizado()">Desasignar</button>
                    <button id="btn_reenviar_bringg" class="pedidos_recibidos" style="width: 160px; display: none;" title="Bringg" name="Bringg" onclick="reenviarDelivery('DRAGONTAIL')">Enviar Delivery</button>
                    <button id="btn_reenviar_moto_duna" class="pedidos_recibidos" style="width: 130px; display: none;" title="Duna" name="Duna" onclick="crearOrdenDunaMotorizado()">Asignar Duna</button>
                    <!-- <button id="btn_anular" class="anular_pedido" style="width: 150px; display: none;" title="Anular" name="Anular" onclick="anular()">Anular</button> -->
                    <button id="btn_imprimir_error" class="pedidos_recibidos" style="width: 100px; display: none" title="Imprimir" name="Cancelar" onclick="imprimir_transaccionError()">Imprimir</button>
                    <button id="btn_pedidos_error" class="pedidos_recibidos" style="float: right;" title="Error de Impresión" name="Errores de impresion" onclick="cargar_impresionesError()">Error Impresión<span badge=""></span></button>
                    <button id="btn_pedidos_entregados" class="pedidos_recibidos" style="float: right; display: none" title="Pedidos Entregados" name="Pedidos Entregados" onclick="habilitarContenedorPedidosEntregados()">Pedidos Entregados</button>
                    <button id="btn_pedidos_app" class="pedidos_recibidos" style="float: right; display: none" title="Pedidos Recibido" name="PedidoS Pendientes" onclick="habilitarContenedorPedidos()">Pedidos Pendientes<span badge=""></span></button>
                    <button id="btn_pedidos_pickup_app" class="pedidos_recibidos" style="float: right; display: none" title="Pedidos Pickup" name="Pedidos Pickup" onclick="mostrarPedidosPickup()">Pedidos Pickup<span badge=""></span></button>
                    <button id="btn_pedidos_ocultar_app" class="pedidos_recibidos" style="float: right; display: none" 
                        title="Ocultar Pedidos Pickup" name="Ocultar Pedidos Pickup" onclick="ocultarPedidosPickup()">Ocultar Pickup<span badge=""></span></button>

                </div>
            </div>
        </div>


        <div id="content" style="display:none;">
            <input type="hidden" id="validacuenta" value="1" />
            <table class="table table-bordered">
                <tr>
                    <td>
                        <div id="listamesasabiertas">
                            <table id="detalleMesa" class="table table-bordered table-hover" style="width: 550px;"></table>
                        </div>
                    </td>
                    <td>
                        <table align="center">
                            <tr align="right">
                                <th>Mesa: &nbsp;</th>
                                <td align="left"><input class='form-control' style="font-size:16px" id="txt_mesa_descripcion" readonly="readonly" type="text"></td>

                            </tr>
                            <tr align="right">
                                <th>Total Neto: &nbsp;</th>
                                <td align="left"><input class='form-control' style="font-size:16px" id="txt_txt_totalNeto" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th>IVA: &nbsp;</th>
                                <td align="left"><input class='form-control' style="font-size:16px" id="txt_iva" readonly="readonly" type="text" /></td>
                            </tr>
                            <tr align="right">
                                <th></th>
                                <td align="left" style="font-size:18px">----------------------------------------</td>
                            </tr>
                            <tr align="right">
                                <th>Total: &nbsp;</th>
                                <td align="left"><input class='form-control' style="font-size:16px" id="txt_totalDetalle" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th colspan="2">Observaciones: &nbsp;</th>
                            </tr>
                            <tr align="right">
                                <td align="left" colspan="2"><textarea class='form-control' style="font-size:16px" id="txt_observacion" readonly="readonly" rows="10" cols="30"></textarea></td>

                            </tr>
                            <tr align="right">
                                <th style="display:none">Cod. Mesa: &nbsp;</th>
                                <td style="display:none" align="left"><input style="font-size:16px" id="txt_codigoMesa" readonly="readonly" type="text"></td>
                            </tr>
                        </table>

                        <table height="125px">
                            <tr>
                                <td rowspan="2" colspan="2" align="center" style="width:400px; position:inherit; margin-top:10px">
                                    <button id="btn_okDetalle" type="button" class=-danger-lg' style='height:60px;width:120px;' title="OK"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"> </span> Cancelar</button>
                                    <button id="btn_tomar_mesa" type="button" class=-primary-lg' style='height:60px;width:120px;' title="Retomar Mesa"><span class="glyphicon glyphicon-ok" aria-hidden="true"> </span> Retomar</button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>


        <div id="aumentarContador" >
            <label style="margin-top: 20px;margin-left:35px" >Cantidad de personas </label> <br>
            <input type="text" inputmode="none" name="cantidad" id="cantidad" value="" style="width: 188px;margin-top: 20px;margin-left:20px;" />


            <table style="margin-top: 20px;margin-left:22px;">
                <tr>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('7')">7</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('8')">8</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('9')">9</button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('4')">4</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('5')">5</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('6')">6</button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('1')">1</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('2')">2</button>
                    </td>
                    <td>
                        <button class="btnVirtual" onclick="fn_agregarNumero('3')">3</button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <button id="btn_cantidad_cero" class="btnVirtual" onclick="fn_agregarNumero('0')">0</button>
                    </td>
                    <td colspan="2">
                        <button style="width: 100%;" class="btnVirtualBorrar" onclick="fn_eliminarCantidad()">&larr;</button>
                    </td>
                </tr>
            </table>
        </div>

        <div id="modalInfoMesas" class="modal fade bs-example-modal-lg" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <h3>Informaci&oacute;n: Mesa <span id="nombre_mesa"></span></h3>
                        </div>
                        <div class="panel-body">
                            <!--inicio panel body-->
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-6" style="border: 1px  #9595a1  solid; height: 500px">
                                        <div class="row">
                                            <div class="  col-md-offset-1 col-md-10  col-md-offset-1 text-center" style="border: 1px #9595a1  solid; height: 360px">
                                                <img id="img_fondo" style="width: 90% ;      margin:  auto auto auto auto" src="../imagenes/ejmImg.PNG"></img>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="   col-md-offset-1 col-md-10   col-md-offset-1 " style="border: 1px  #9595a1  solid; height: 135px">
                                                <section>
                                                    <article id="textos">
                                                        <p><strong>N&uacute;mero de cuentas: </strong> <span id="num_splits"></span></p>
                                                        <p><strong>N&uacute;mero de clientes: </strong> <span id="num_cliente"></span></p>
                                                        <p><strong>Capacidad de la mesa: </strong> <span id="capacidad_mesa"></span></p>
                                                        <p><strong>Es mi mesa: </strong> <span id="mi_mesa"></span></p>
                                                        <p><strong>Estado: </strong> <span id="estado"></span></p>
                                                    </article>
                                                </section>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="" class="col-md-6" style="border: 1px  #9595a1  solid; height: 500px; overflow-y: hidden">
                                        <br />
                                        <center style="background-color: #e5e5f2;"><strong>Informaci&oacute;n de transacciones </strong> </center>
                                        <div style="width: 95%; height: 560px;  " id="mccs">
                                            <div id="contenedor_info_mesa" style="height:560px; width: 95%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="border: 1px  #9595a1  solid; height: 100px">
                                        <br />
                                        <center id="footerButton">

                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div> <!--    FIn panel body-->
                    </div>
                </div>
            </div>
        </div>

        <div id="modalEnviarMotorizado" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading"> <span id="enviarHeader"></span></div>
                        <div class="panel-body" style="padding: 0">
                            <!--inicio panel body-->
                            <div class="container-fluid">
                                <div class="row" id="enviarMotorizadosLst"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="enviarFooter" style="padding: 0"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalAsignarMotorizado" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading"> <span id="asignarHeader"></span></div>
                        <div class="panel-body" style="padding: 0">
                            <!--inicio panel body-->
                            <div class="container-fluid">
                                <div class="row" id="asignarMotorizadosLst"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="asignarFooter" style="padding: 0"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Locales Transferencia -->
        <div id="modalLocalesTransferencia" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <span id="headerLocales">
                                <h3>Seleccione un local</h3>
                            </span>
                        </div>
                        <div class="panel-body" style="padding: 0; width: 100%;">
                            <!--inicio panel body-->
                            <div id="cntLocalesTransferencia" style="float: left; width:100%; height:400px;">
                                <div class="row" id="lstLocalesTransferencia"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="footerLocales" style="padding: 0">
                            <button class="btn_cerrar" onClick="$('#modalLocalesTransferencia').modal('toggle')">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Locales Transferencia Pickup -->
        <div id="modalLocalesTransferenciaPickup" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <span id="headerLocales">
                                <h3>Seleccione un local</h3>
                            </span>
                        </div>
                        <div class="panel-body" style="padding: 0; width: 100%;">
                            <!--inicio panel body-->
                            <div id="cntLocalesTransferenciaPickup" style="float: left; width:100%; height:400px;">
                                <div class="row" id="lstLocalesTransferenciaPickup"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="footerLocales" style="padding: 0">
                            <button class="btn_cerrar" onClick="$('#modalLocalesTransferenciaPickup').modal('toggle')">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Motivo Transferencia Pickup -->
        <div id="modalMotivoTransferenciaPickup" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <span id="headerMotivos">
                                <h3>Elije un motivo</h3>
                            </span>
                        </div>
                        <div class="panel-body" style="padding: 0">
                            <!--inicio panel body-->
                            <div id="cntMotivosTransferenciaPickup" style="float: left; width:100%; height:400px; ">
                                <div class="row" id="lstMotivosTransferenciaPickup"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="footerMotivos" style="padding: 0; width: 100%;">
                            <button class="btn_cerrar" onClick="$('#modalMotivoTransferenciaPickup').modal('toggle')">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Motivo Transferencia -->
        <div id="modalMotivoTransferencia" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <span id="headerMotivos">
                                <h3>Elije un motivo</h3>
                            </span>
                        </div>
                        <div class="panel-body" style="padding: 0">
                            <!--inicio panel body-->
                            <div id="cntMotivosTransferencia" style="float: left; width:100%; height:400px; ">
                                <div class="row" id="lstMotivosTransferencia"></div>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-body" id="footerMotivos" style="padding: 0; width: 100%;">
                            <button class="btn_cerrar" onClick="$('#modalMotivoTransferencias').modal('toggle')">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalMotorizadosFormulario" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading">
                            <h3>Asignar Motorizado <span id="nombre_mesa"></span></h3>
                        </div>
                        <div class="panel-body">
                            <form>
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Email address</label>
                                    <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Example select</label>
                                    <select class="form-control" id="exampleFormControlSelect1">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect2">Example multiple select</label>
                                    <select multiple class="form-control" id="exampleFormControlSelect2">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Example textarea</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                            </form>


                        </div> <!--    FIn panel body-->
                    </div>



                </div>
            </div>
        </div>

        <div id="anulacionesContenedor" align="center">
            <div class="preguntasTitulo"><label>Credenciales de Administrador</label></div>
            <div class="anulacionesInput">
                <input inputmode="none" type="password" name="usr_clave" id="usr_clave" onchange="fn_validarUsuario()" style="height: 35px; width: 454px; font-size: 20px;" />
            </div>
            <div id="numPad" align="center" style="left: 17%; font-size: 34px;"></div>
        </div>

        <div id="anulacionesMotivo" style="width: 470px">
            <div class="preguntasTitulo">Ingrese el motivo</div>
            <div class="anulacionesSeparador">
                <select id="motivosAnulacion" style="width: 460px;"></select>
                <div class="anulacionesLabel" style="font-size:20px;"><br />Observaci&oacute;n:</div>
                <textarea name="motivoObservacion" id="motivoObservacion" style="width: 460px;"></textarea>
            </div>
            <div class="anulacionesSeparadorFin">
                <div class="anulacionesSubmit"></div>
            </div>
        </div>

        <!--<div id="numPad"></div>-->
        <div id="txtPad"></div>
        <div id="keyboard" style="left: 20%;"></div>



        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../imagenes/loading.gif" />
            </div>
        </div>


        <div id="modal_cargando_pedido" class="modal_cargando" ng-show="cargando" style="display: none;">  
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor_pantalla_pedido" style="text-align: center;">
                <img src="../imagenes/loading.gif" style="display: block; margin: auto;"/>
                <span id="texto_cargando_pedido" style="display: block;">Cargando...</span>
            </div>
        </div>

        <div id="modalTransferirMesas">
            <div class="preguntasTitulo" style="height: 60px"><label id="tituloModalTransferir" style="padding: 0;margin: 2px;"></label><img src="../imagenes/admin_resources/btn_eliminar.png" onclick="fn_modalTransferirMesas()" class="btn_cerrar_modal_cupones" /></div>
            <div id="aut_frm_cnj_cpn">
                <div class="anulacionesSeparador" style="margin: 2px;padding: 1px">
                    <input inputmode="none" type="text" name="input_transferirMesa" id="input_transferirMesa" style="height: 35px; width: 454px;" />
                </div>
            </div>
        </div>

        <div id="keyboard" style="left: 20%;"></div>

        <div class="modal fade" id="mdl_diferenciaPrecios" tabindex="-1" role="dialog" aria-labelledby="mdl_diferenciaPrecios" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document" style="width: 650px;">
                <div class="modal-content">
                    <div class="modal-header" id="mdl_diferenciaPreciosLabel" style="text-align: center; font-size: 20px;"></div>
                    <div class="modal-body" id="mdl_diferenciaPreciosBody" style="height: 200px; text-align: center; vertical-align: middle; line-height: 35px; font-size: 20px;"></div>
                    <div class="modal-footer" id="btn_diferenciaPrecios"></div>
                </div>
            </div>
        </div>
        
        <script src="../js/html2canvas.min.js"></script>
        <script src="../js/socket.io.min_4_4_1.js"></script>
        <script src="../js/socketNotify.js"></script>
        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
		<script type="text/javascript" src="../js/kds.js"></script>
        <script>document.getElementById("cantidad").addEventListener("input", validarInput);
        </script>
        

    </body>

    </html>
<?php } ?>
