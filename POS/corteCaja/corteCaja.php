<?php
session_start();

/////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ//////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE FIN DE DIA///////////////////////////////
////////////////TABLAS: ARQUEO_CAJA,BILLETE_ESTACION,////////////////////////
////////////////////////CONTROL_ESTACION,ESTACION////////////////////////////
////////////////////////BILLETE_DENOMINACION/////////////////////////////////
////////FECHA CREACION: 20/12/2013///////////////////////////////////////////
/////////////////FECHA:14/01/2014////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/03/2015 ////////////////////////////////
///////USUARIO QUE MODIFICO: Jorge Tinoco ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Ajustes para visualizar mesas abiertas /////
/////////////////////////////////////////////////////////////////////////////

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");


if (!isset($_SESSION['validado'])) {          // en caso de no existir sesión iniciada, se destruye toda información
    include_once('../seguridades/seguridad.inc');
} else {

    //Estudia la condición en la que se encuentran los cajeros del restaurante, en caso de no estar activos, lleva a efecto el control de cuentas y fondo relativo a fin de día.
    include_once("../system/conexion/clase_sql.php");
    include_once("../clases/clase_corteCaja.php");

    $ins_corteCaja  = new corteCaja();
    $IDUsersPos     = ( isset($_SESSION['usuarioId']) && $_SESSION['usuarioId'] != '' ) ? $_SESSION['usuarioId'] : '';
    $salida         = $ins_corteCaja->finDiaControlCuentas($IDUsersPos);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Corte de Caja</title>

        <link rel="stylesheet" type="text/css" href="../css/modal.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_pantallas.css" />
        <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css" />
        <link rel="StyleSheet" type="text/css" href="../css/retiro_fondo.css" />
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../css/teclado_admincortecaja.css" />
        <link rel="stylesheet" type="text/css" href="../css/barra_progreso.css" />

        <style type="text/css">
            button {
                        height:60px;
                        width: 120px;
                        cursor:pointer;
                   }
        </style>
    </head>

    <body>

        <input inputmode="none"  type="hidden" name="cdn_id" id="cdn_id"  value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $_SESSION['rstId']; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_tipo_servicio" id="hide_tipo_servicio" value="<?php echo $_SESSION['TipoServicio']; ?>"/>
        <input inputmode="none"  type="hidden" id="est_id"  value="<?php echo $_SESSION['estacionId']; ?>"/>
        <input inputmode="none"  type="hidden" id="usrrid"  value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  type="hidden" id="dirIp"  value="<?php echo $_SESSION['direccionIp']; ?>"/>
        <input inputmode="none"  type="hidden" id="perfil"  value="<?php echo $_SESSION['perfil']; ?>"/>
        <input inputmode="none"  type="hidden" id="IDPeriodo"  value="<?php echo $_SESSION['IDPeriodo']; ?>"/>
        <input inputmode="none"  type="hidden" id="hid_existeCajeroDomicilio"/>
        <input inputmode="none"  type="hidden" id="hid_aplicaDomicilio"/>
<input inputmode="none"  type="hidden" id="hid_idOdp"/>


        <table width="99%" height="100%">
            <td>
                <div>
                    <table border="1px" width="100%" height="100%" class="table table-bordered">
                        <tr>
                            <th class="tituloCeldas" width="34%">Cuentas Abiertas</th>
                            <th class="tituloCeldas" width="35%">Cuentas por Facturar</th>
                            <th class="tituloCeldas" width="35%">Empleado Asignado</th>
                        </tr>
                        <tr>
                            <td align="center">
                                <div id="detalle_plu" style="height:500px;" >
                                </div><br />
                                <div>
                                    <button id="btn_limpiar" type="button" class="btn btn-primary" style='height:60px;width:100px;' onclick="fn_LimpiarcuentaAbierta();">Limpiar</button>
                                </div>
                            </td>
                            <td align="center">
                                <div id="cuenta" style="height:290px;">
                                </div>
                                <div class="tituloCeldas">
                                    Pedidos App
                                </div>
                                <div id="pedidosApp" style="height:290px;">
                                </div>
                            </td>
                            <td align="center">
                                <div id="empleado" style="height:290px;">
                                </div>
                                <div class="tituloCeldas">
                                    Motorizado Asignado
                                </div>

                                <div id="motorizados" style="height:290px;">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <div>
                <table>
                    <tr>
                        <td>
                            <button id="btn_refresh" type="button" class="boton_Accion" style='height:70px;width:70px; background-image:url(../imagenes/refresh.png); background-repeat:no-repeat; background-position:center; margin-left:20px' onclick="fn_refresh();" ></button>
                        </td>
                        <td>
                            <button id="btn_aceptar" type="button" class="btn btn-primary" style='height:70px;width:200px; margin-left:320px' onclick="fn_validaFindeDia();">Finalizar el Día</button>
                        </td>
                    </tr>
                    <!-- Menu Opciones -->
                    <div class="cnt_menu">
                        <input inputmode="none"  type="button" id="boton_sidr" value="Menu" class="boton_Accion" onclick="" style="margin-right: 14px; right:10px;"/>
                    </div>
                    <!-- SubMenu Opciones -->
                    <div id="id_menu_desplegable" class="menu_desplegable">
                        <div id="id_modal_opciones_drc" class="modal_opciones_drc">
                            <button class="boton_Opcion" id='funcionesGerente' onclick='fn_funcionesGerente()'>Funciones Gerente</button>
                            <button class="boton_Opcion" onclick="fn_salirSistema()">Salir Sistema</button>
                        </div>
                    </div>
                </table>
            </div>
        </table>

        <!-- CONTENEDOR CREDENCIALES -->
        <div id="Contenedorcuentas">
            <div class="preguntasTitulo">Ingrese Credenciales de Administrador</div>
            <div class="anulacionesSeparador">
                <div class="anulacionesInput"><input inputmode="none"  type="password" name="usr_clave" id="usr_clave" style="height: 35px; width: 454px; font-size: 16px;"/></div>
            </div>
        </div>

        <div id="Contenedorfacturas">
            <div class="preguntasTitulo">Ingrese Credenciales de Administrador</div>
            <div class="anulacionesSeparador">
                <div class="anulacionesInput"><input inputmode="none"  type="password" name="usr_claves" id="usr_claves" style="height: 35px; width: 454px; font-size: 16px;"/></div>
            </div>
        </div>

        <div id="numPad"></div>
        <div id="txtPad"></div>
        <div id="keyboard"></div>

        <div id="content" >
            <input inputmode="none"  type="hidden" id="validacuenta" value="1"/>
            <table class="table table-bordered">
                <tr>
                    <td>
                        <div id="listamesasabiertas" style="height:400px; width:450px">
                            <table id="detalleMesa"  class="table table-bordered table-hover"></table>
                        </div>
                    </td>
                    <td style="width:400px">
                        <table align="center">
                            <br /><br />
                            <tr align="right">
                                <th>Mesa: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_mesa_descripcion" readonly="readonly" type="text"></td>

                            </tr>
                            <tr align="right">
                                <th>Total Neto: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_txt_totalNeto" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th>IVA: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_iva" readonly="readonly" type="text" /></td>
                            </tr>
                            <tr align="right">
                                <th></th>
                                <td align="left" style="font-size:18px">-----------------</td>
                            </tr>
                            <tr align="right">
                                <th>Total: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_totalDetalle" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th style="display:none">Cod. Mesa: &nbsp;</th>
                                <td style="display:none" align="left"><input inputmode="none"  style="font-size:16px" id="txt_codigoMesa" readonly="readonly" type="text"></td>
                            </tr>
                        </table>

                        <table height="125px">
                            <tr>
                                <td rowspan="2" colspan="2" align="center" style="width:400px; position:inherit; margin-top:10px">
                                    <button id="btn_okDetalle" type="button" class='btn btn-default' style='height:60px;width:100px;' title="OK">Cancelar</button>
                                    <button id="btn_tomar_mesa" type="button" class='btn btn-primary' style='height:60px;width:100px;'onclick="fn_validarUsuarioAdministrador(1);" title="Retomar Mesa">Retomar</button>
<button id="btn_anular_mesa" type="button" class='btn btn-danger' style='height:60px;width:100px;'onclick="fn_validarUsuarioAdministrador(0);" title="Anular Mesa">Anular</button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!--MODAL CUENTAS ABIERTAS DE FUNCIONES GERENTE-->
        <div id="content2" title="Cuentas por Facturar">
            <input inputmode="none"  type="hidden" id="validacuentas" value="2"/>
            <table class="table table-bordered">
                <tr>
                    <td>
                        <div id="listacuentasabiertas" style="height:400px; width:450px">
                            <table id="detalleCuentass" class="table table-bordered table-hover"></table>
                        </div>
                    </td>

                    <td style="width:400px">
                        <table align="center">
                            <br /><br />
                            <tr align="right">
                                <th>Factura: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_factura" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th>Total Neto: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_totalNetoCuenta" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th>IVA: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_ivaCuenta" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th></th>
                                <td align="left" style="font-size:18px">-----------------</td>
                            </tr>
                            <tr align="right">
                                <th>Total: &nbsp;</th>
                                <td align="left"><input inputmode="none"  class='form-control' style="font-size:16px" id="txt_totalDetalleCuenta" readonly="readonly" type="text"></td>
                            </tr>
                            <tr align="right">
                                <th style="display:none">Cod. Factura:</th>
                                <td style="display:none" align="left"><input inputmode="none"  style="font-size:16px" id="txt_codigoFactura" readonly="readonly" type="text"></td>
                            </tr>
                        </table>

                        <table height="125px">
                            <tr>
                                <td rowspan="2" colspan="2" align="center" style="width:400px; position:inherit; margin-top:10px">
                                    <button id="btn_okDetalleCuenta" type="button" class='btn btn-default' style='height:60px;width:100px;' title="OK">Cancelar</button>
                                    <button id="btn_tomar_cuenta" type="button" class='btn btn-primary' style='height:60px;width:100px;' onclick="fn_validarUsuarioAdministradorfacturas(1);" title="Retomar Cuenta">Retomar</button>
<button id="btn_anular_cuenta" type="button" class='btn btn-danger' style='height:60px;width:100px;' onclick="fn_validarUsuarioAdministradorfacturas(0);" title="Anular Cuenta">Anular</button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../imagenes/cargando.gif" />
            </div>
        </div>
        <input id="url_api_motorizados_gerente" type="hidden" />
        <!--MODAL FIN DEL DIA-->
        <div id="ModalTemporizador" title="Finalizando el D&iacute;a, espere por favor..." align="center">
            <div id="countdown"></div>
        </div>

        <div id="contenedorRetomarOrden"></div>

        <!-- Barra progreso -->
        <div id="modalBarraProgreso" class="modal fade modalBarraProgreso" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalBarraProgresoTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modaldialogBarraProgreso" role="document">
                <div class="modal-content modalcontentBarraProgreso">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="progress blue">
                                        <span class="progress-left">
                                            <span class="progress-bar"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar"></span>
                                        </span>
                                        <div id="progress-value" class="progress-value">0%</div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modalBarraProgresofooter">
                        <h5 id="modalBarraProgresoTituloLargo" class="modal-title modalBarraProgresoTituloLargo"></h5>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin barra progreso -->

        <input inputmode="none"  type="hidden" id="hid_controlEfectivo"/>
        <input inputmode="none"  type="hidden" id="hid_estacion"/>
        <input inputmode="none"  type="hidden" id="hid_usuario"/>
        <input inputmode="none"  type="hidden" id="hid_controlMesa"/>
        <input inputmode="none"  type="hidden" id="hid_controlMotorizado"/>
        <input inputmode="none"  type="hidden" id="hid_controlPendientesApp"/>
        <input inputmode="none"  type="hidden" id="hid_controlCuenta"/>
        <input inputmode="none"  type="hidden" id="hid_controlEstacion"/>
        <input inputmode="none"  type="hidden" id="hid_usuarioDescripcion"/>
        <input inputmode="none"  type="hidden" id="hid_controlDiferencia"/>
        <input inputmode="none"  type="hidden" id="hid_usuarioDesmontado"/>
        <input inputmode="none"  type="hidden" id="hid_codigoorden"/>
        <input inputmode="none" type="hidden" id="hid_codigofactura"/>
<input inputmode="none" type="hidden" id="hid_codigoEstacion"/>
        <input inputmode="none" type="hidden" id="hid_cuentaOdp"/>
        <input inputmode="none" type="hidden" id="hid_bandera_op"/>
        <input inputmode="none" type="hidden" id="hid_nombreEstacion"/>
        <input inputmode="none" type="hidden" id="hid_nombreUsuario"/>
        <input inputmode="none" type="hidden" id="hid_nombreMesa"/>

        <script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script src="../bootstrap/js/bootstrap.js"></script>
        <script language="javascript1.1"  src="../js/alertify.js"></script>
        <script type="text/javascript" src="../js/teclado_billetes.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script type="text/javascript" src="../js/teclado_admincortecaja.js"></script>
        <script type="text/javascript" src="../js/jquery.countdown360.js"></script>
        <script type="text/javascript" language="javascript" src="../js/ajax_corteCaja.js"></script>
        <script type="text/javascript" language="javascript" src="../js/ajax_cliente_interface.js"></script>
        <script type="text/javascript" language="javascript" src="../js/ajax_telegram.js"></script>
        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
        <script type="text/javascript" src="../js/barra_progreso.js"></script>

    </body>
</html>
<?php
}