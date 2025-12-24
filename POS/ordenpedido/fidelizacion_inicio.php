<?php
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_seguridades.php";
include_once "../clases/clase_ordenPedido.php";
include_once "../seguridades/seguridad_niv2.inc";
include_once "../clases/clase_fidelizacionCadena.php";
include_once '../seguridades/AesEncryption.php';

$usr_id = $_SESSION['usuarioIdAdmin']; // usuarioId
$cdn_id = $_SESSION['cadenaId'];
$rst_id = $_SESSION['rstId'];
$est_id = $_SESSION['estacionId'];
$est_ip = $_SESSION['direccionIp'];
$nombre = $_SESSION['nombre'];
$usuario = $_SESSION['usuario'];
$seguridades = new seguridades();
$botonesPermitidos = $seguridades->fn_accesoPermisosPerfilBotones($_SESSION['usuarioId'], "Mesas");

if (!isset($_SESSION["bienvenidaPlanFidelizacion"])) {
    $fidelizacionCadena = new Cadena();
    $AesEncrypt = new AESEncriptar();
    $respuesta = $fidelizacionCadena->cargarConfiguracionPoliticas($cdn_id);
    $respuesta = json_decode($respuesta);
    $_SESSION["bienvenidaPlanFidelizacion"] = $respuesta->bienvenida;
    $_SESSION["preguntaPlanFidelizacion"] = $respuesta->preguntaRegistro;
    $_SESSION["nombrePlanFidelizacion"] = $respuesta->nombrePlan;
    $_SESSION["claveConexion"] = $respuesta->seguridad;
    $password = $respuesta->ContrasenaWebServicesFidelizacion;
    $key = 'd480863a4dbd1b245608b9d28c2fc02cdbb2443e1bcb19d0df82d67ab6ba60c9';
    $FidelizacionClaveSeguridadWebS = $AesEncrypt->DesencriptarDatos($password, $key);
    $_SESSION["ContrasenaWebServicesFidelizacion"] = $FidelizacionClaveSeguridadWebS;

}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html" ; charset="utf-8"/>

    <title>Toma de Pedido</title>

    <!-- Librerias CSS -->
    <link rel="StyleSheet" href="../css/tomaPedido.css" type="text/css"/>

    <!-- Scripts para alertas-->
    <link rel="stylesheet" href="../css/alertify.core.css"/>
    <link rel="stylesheet" href="../css/alertify.default.css"/>
    <!-- Scripts para keyboard -->
    <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>
    <link rel="stylesheet" type="text/css" href="../css/bloquear_acceso.css"/>
    <link rel="stylesheet" type="text/css" href="../css/fidelizacion_inicio.css"/>
    <!-- Teclado Facturacion -->
    <link rel="stylesheet" type="text/css" href="../css/teclado_facturacion.css"/>

    <!-- Librerias JavaScript -->
    <script src="../js/jquery.min.js"></script>

    <!-- Scripts para alertas-->
    <script type="text/javascript" src="../js/alertify.js"></script>
    <!-- Scripts para keyboard -->
    <script type="text/javascript" src="../js/teclado.js"></script>
    <!-- Fidelizacion -->
    <script type="text/javascript" src="../js/ajax_fidelizacion_menu.js"></script>
    <!-- Script para validación de código QR -->
    <script type="text/javascript" src="../js/validacionCodigoQR.js"></script>
<!-- Script API Impresion -->
    <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>

</head>

<input inputmode="none"  type="hidden" name="bloqueo" id="bloqueo" value="<?php echo $_SESSION['bloqueoacceso']; ?>"/>

<?php
$usr_id = $_SESSION['usuarioIdAdmin']; // usuarioId
$cdn_id = $_SESSION['cadenaId'];
$rst_id = $_SESSION['rstId'];
$est_id = $_SESSION['estacionId'];
$est_ip = $_SESSION['direccionIp'];
$nombre = $_SESSION['nombre'];
$usuario = $_SESSION['usuario'];

$_SESSION['fdznDocumento'] = null;
$_SESSION['fdznNombres'] = null;
$_SESSION['fdznDireccion'] =   null;


$lc_tipoServicio = $_SESSION['TipoServicio'];
$_SESSION['cargoPisoArea'] = "Si";

$num_Pers = 0;
$bloqueado = $_SESSION['bloqueoacceso'];

if (htmlspecialchars(isset($_GET["numPers"]))) {
    $num_Pers = htmlspecialchars($_GET["numPers"]);
}
if (htmlspecialchars(isset($_GET["numMesa"]))) {
    $mesa_id = htmlspecialchars($_GET["numMesa"]);
} else {
    $mesa_id = "";
}

if (htmlspecialchars(isset($_GET["cat_id"]))) {
    $cat_id = htmlspecialchars($_GET["cat_id"]);
} else {
    $cat_id = "";
}
if (htmlspecialchars(isset($_GET["numSplit"]))) {
    $numSplit = htmlspecialchars($_GET["numSplit"]);
    print('<input inputmode="none"  type="hidden" name="hide_numSplit" id="hide_numSplit" value="' . $numSplit . '"/>');
} else {
    $numSplit = 1;
    ?>
<?php } ?>
<body style="overflow-y: hidden">
<div id="formCobrar">
    <input inputmode="none"  type="hidden" name="pantallaAcceso" value="TOMA PEDIDO" id="pantallaAcceso"/>
    <input inputmode="none"  type="hidden" name="codigoCategoria" id="codigoCategoria"/>
    <input inputmode="none"  type="hidden" name="hide_menu_id" id="hide_menu_id"/>

    <input inputmode="none"  type="hidden" name="hide_numeroCuenta" value="<?php echo $numSplit; ?>" id="hide_numeroCuenta"/>
    <input inputmode="none"  type="hidden" name="hide_tipov_id" id="hide_tipov_id"/>
    <input inputmode="none"  type="hidden" name="hide_vae_IDCliente" id="hide_vae_IDCliente"/>

    <input inputmode="none"  type="hidden" name="hide_cli_direccion" id="hide_cli_direccion"/>
    <input inputmode="none"  type="hidden" name="hide_cli_documento" id="hide_cli_documento"/>
    <input inputmode="none"  type="hidden" name="hide_cli_email" id="hide_cli_email"/>
    <input inputmode="none"  type="hidden" name="hide_cli_nombres" id="hide_cli_nombres"/>
    <input inputmode="none"  type="hidden" name="hide_cli_telefono" id="hide_cli_telefono"/>

    <input inputmode="none"  type="text" name="hide_pluId" id="hide_pluId" style="display: none;"/>
    <input inputmode="none"  type="hidden" name="hide_magp_id" id="hide_magp_id"/>
    <input inputmode="none"  type="hidden" name="hide_plu_gramo" id="hide_plu_gramo"/>
    <input inputmode="none"  type="hidden" name="hide_odp_id" id="hide_odp_id"/>
    <input inputmode="none"  type="hidden" name="hide_dop_id" id="hide_dop_id"/>
    <input inputmode="none"  type="hidden" name="hide_mesa_id" id="hide_mesa_id" value="<?php echo $mesa_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_num_Pers" id="hide_num_Pers" value="<?php echo $num_Pers; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo $usr_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_cat_id" id="hide_cat_id" value="<?php echo $cat_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $est_id; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
    <input inputmode="none"  type="hidden" id="txtTipoServicio" value="<?php echo $lc_tipoServicio; ?>"/>
    <input inputmode="none"  type="hidden" name="hide_cdn_tipoimpuesto" id="hide_cdn_tipoimpuesto"/>
    <input inputmode="none"  type="hidden" name="cantidadOK" id="cantidadOK" value="1"/>
    <input inputmode="none"  type="hidden" name="pluAgregar" id="pluAgregar"/>
    <input inputmode="none"  type="hidden" name="magpAgregar" id="magpAgregar"/>
    <input inputmode="none"  type="hidden" name="hid_cla_id" id="hid_cla_id"/>
    <input inputmode="none"  type="hidden" name="banderaCierrePeriodo" id="banderaCierrePeriodo"
           value="<?php echo $_SESSION['sesionbandera']; ?>"/>
    <input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
    <input inputmode="none"  type="hidden" name="cantidadKG" id="cantidadKG" value="0"/>
    <input inputmode="none"  type="hidden" name="alPeso" id="alPeso" value="0"/>

    <input inputmode="none"  type="hidden" name="fidelizacionActiva" id="fidelizacionActiva" value=""/>
    <input inputmode="none"  type="hidden" name="clienteAutorizacion" id="clienteAutorizacion" value=""/>
</div>
<input inputmode="none"  type="hidden" id="hid_bandera_gramo"/>
<input inputmode="none"  type="hidden" id="hid_gramoPlu"/>

<!-- Contenedor Pregunta de Inicio -->
<div class="container" id="pre1">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="marco">
                <div class="titulo"><?php echo $_SESSION["bienvenidaPlanFidelizacion"]; ?></div>
                <div class="botones">
                    <input inputmode="none"  type="button" class="button_fdzn" onclick="flujo_seguimiento('ingresoCedula', 'pre1', true)"
                           value="Si">
                    <!--<input inputmode="none"  type="button" class="button_fdzn" onclick="flujo_seguimiento('pre2', 'pre1', true)"
                           value="No"></input> -->

                           <input inputmode="none"  type="button" class="button_fdzn" onclick="ir_noEnrrolar()"
                           value="No">
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<!-- Contenedor pregunta para registrar -->
<div class="container" id="pre2">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="marco">
                <div class="titulo"><?php echo $_SESSION["preguntaPlanFidelizacion"]; ?></div>
                <div class="botones">
                    <input inputmode="none"  type="button" class="button_fdzn" 
                    onclick="flujo_seguimiento('registroCliente', 'pre2', true)" value="Si" />
                    <input inputmode="none"  type="button" class="button_fdzn" value="No" onclick="ir_noEnrrolar()" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Contenedor formuario registro -->
<div class="container" id="registroCliente">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="marco">
                <div class="cuadro_registro">
                    <table>
                        <tr>
                            <td colspan="2" class="labelTitle">FORMULARIO DE REGISTRO DE CLIENTES<br/></td>
                        </tr>
                        <tr style="display: none">
                            <td class="textTabla">Tipo Documento *</td>
                            <td><input inputmode="none"  class="txt" type="text" value="CEDULA" readonly="true" id="txtTipoDocumento" required="true" /></td>
                        </tr>
                        <tr>
                            <td class="textTabla">Número de Documento *</td>
                            <td>
                                <input inputmode="none"  class="txt" type="text" value="" id="txtNumeroDocumento" maxlength="13"
                                       onclick="fn_numericoFDZN('#txtNumeroDocumento');" required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="textTabla">Nombre y Apellido *</td>
                            <td>
                                <input inputmode="none"  class="txt" type="text" value="" id="txtNombresApellidos"
                                       onclick="fn_alfaNumerico_EscribirNombre('#txtNombresApellidos');"
                                       required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="textTabla">Teléfono *</td>
                            <td>
                                <input inputmode="none"  class="txt" type="text" value="" id="txtTelefono" maxlength="10"
                                       onclick="fn_tecladoNumTelefono('#txtTelefono');" required="true"></input>
                            </td>
                        </tr>
                        <tr>
                            <td class="textTabla">Dirección *</td>
                            <td>
                                <input inputmode="none"  class="txt" type="text" value="" id="txtDireccion"
                                       onclick="fn_activarCasillaFDZN('#txtDireccion');" required="true"></input>
                            </td>
                        </tr>
                        <tr>
                            <td class="textTabla">Email</td>
                            <td>
                                <input inputmode="none"  class="txt" type="text" value="" id="txtCorreo"
                                       onclick="fn_alfaNumericoCorreo(this)" />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <p style="color: #FF0000; text-align: right">Campos Obligatorios *</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input inputmode="none"  type="hidden" id="hid_IDCliente" value=""/>
                            </td>
                            <td style="text-align: right">
                                <input inputmode="none"  style=" border-radius: 3px; font-size: 1.5em;width: 45%;height: 80px;background-color: #dfeffc;border: none;"
                                       type="button" class="button_fdzn"
                                       onclick="flujo_seguimiento('pre1', 'registroCliente', true)" id="btnVolver"
                                       value="Volver" />
                                <input inputmode="none"  style=" border-radius: 3px;font-size: 1.5em;width: 45%;height: 80px;background-color: #dfeffc;border: none;"
                                       type="button" class="button_fdzn" onclick="fn_CRUD_Cliente()" id="btnCRUD"
                                       value="Registrar" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Contenedor lectura de cedula o ruc -->
<div class="container" id="ingresoCedula">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="marco">
                <div id="tqtIngresoCR" class="titulo">LEA CODIGO DE SEGURIDAD
                    <p style="font-size: 0.6em;" class="mensajePequeno">Para acumular puntos es necesario lectura de
                        código de seguridad.</p>
                </div>
                <div class="botones">
                    <input inputmode="none"  type="password" class="text_cedula" id="txtNumeroCedulaBusqueda"
                    onkeydown="ejecutarFuncion(event)" autofocus placeholder="" />
                </div>
                <div class="botones">
                    <input inputmode="none"  type="button" class="button_fdzn" onclick="buscarExistente('txtNumeroCedulaBusqueda')"
                           value="Continuar" />
                    <input inputmode="none"  type="button" class="button_fdzn" onclick="flujo_seguimiento('pre1', 'ingresoCedula', true)"
                           value="Cancelar" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<div class="container" id="ingresoVitality" style="display: none">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="marco">
                <div id="tqtIngresoVT" class="titulo">LEA CODIGO DE SEGURIDAD
                    <p style="font-size: 0.6em;" class="mensajePequeno">Lectura de código de seguridad.</p>
                </div>
                <div class="botones">
                    <input inputmode="none"  type="password" class="text_codigoVitality" id="txtCodigoVitality"
                           placeholder="Codigo QR" />
                </div>
                <div class="botones">
                    <input inputmode="none"  type="button" class="button_fdzn"
                           onclick="flujo_seguimiento('pre1', 'ingresoVitality', true)" value="Cancelar" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Contenedor Recargas -->
<div class="container" id="cntRecargas" style="display: none">
    <div class="marco">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-5">
                <div class="titulo">RECARGAS DE EFECTIVO</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="contenedorRecarga">
                    <div>
                        <p id="outCliente"></p>
                    </div>
                    <div style="margin: 5px 0px 25px 0px">
                        <div style="width: 200px; display: inline-block;">
                            <p id="outClientePuntos"></p>
                        </div>
                        <div style="width: 250px; display: inline-block;">
                            <p id="outClienteSaldo"></p>
                        </div>
                    </div>
                    <div>
                        <p>Valor a recargar: $<input inputmode="none"  id="inValorRecarga" type="text" value="" style="font-size: 1.5em;"
                                                     onclick="fn_numericoFDZN('#inValorRecarga');"/></p>
                        <p class="mensajePequeno">* Las recargas solo pueden realizarse en EFECTIVO, con un valor mínimo
                            de $<span id="spnMinimo"></span>.</p>
                        <p style="display: none;" class="mensajePequeno">El valor máximo es de $<span
                                    id="spnMaximo"></span>.</p>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="botones">
                    <input inputmode="none"  type="button" id="btnConfirmar" onclick="recargarEfectivoCliente();" class="button_fdzn"
                           value="Recargar"/>
                    <input inputmode="none"  type="button" id="btnCancelar" onclick="cancelarRecarga();" class="button_fdzn"
                           value="Cancelar"/>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teclados -->
<div id="numPadCliente" style="display: block; position: absolute; top: 45%; left: 45%;"></div>
<div id="txtPadCliente"></div>

<div id="numPad"></div>
<div id="txtPad"></div>
<div id="keyboard" style="left: 100px; text-align: center;top: 54%; width: 850px;"></div>
<div id="keyboardCliente"
     style="left: 195px; text-align: center; top: 54%; display: block; position: absolute; width: 715px;"></div>

<div id="dominio1" style="left:65px; text-align: center; top: 54%; display: block; position: absolute;"></div>
<div id="dominio2" style="left: 910px; text-align: center; top: 54%; display: block; position: absolute;"></div>

<!-- Menú despegable -->
<div id="rdn_pdd_brr_ccns" class="menu_desplegable" style="display: none">
    <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
        <input inputmode="none"  type="button" id="btnIrPntRecarga" class="boton_Opcion" onclick="opcionSetRecarga('Recargas');"
               style="font-size: 22px" value="Recargas"/>
        <input inputmode="none"  type="button" id="btnIrPntVitality" class="boton_Opcion" onclick="opcionSetVitality('Vitality');"
               style="font-size: 22px" value="Vitality"/>
        <input inputmode="none"  name="Salir" type="button" id="salir" class="boton_Opcion" onclick="fn_salirSistema()"
               style="font-size: 22px" value="Salir"/>
    </div>
</div>

<!-- Boton Menu -->
<div style="width: 92%; height: 100px;  margin: auto">
    <input inputmode="none"  name="Menu" id="boton_sidr" value="Menu" class="boton_Accion" onclick="mostrarPanel()"
           style="margin-right: 14px;height: 80px; width: 100px ;font-size: 25px " type="button" />
</div>

<!-- Modal Cargando -->
<div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../imagenes/loading.gif"/>
    </div>
</div>

<!-- Lector de Barras -->
<div id="lectorBarras">>
    <input inputmode="none"  type="text" id="inLectorCodigos" />
</div>

<!-- Librerias JS -->
<script type="text/javascript" src="../js/tecladoCliente.js"></script>

</body>
</html>