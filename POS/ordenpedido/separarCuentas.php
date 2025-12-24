<?php
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////    
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 28-02-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+///////
///////////////////////////////////////////////////////////  

session_start();

include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_seguridades.php";
include_once"../clases/clase_separarCuentas.php";


  if (htmlspecialchars(isset($_GET['tipov_id']))) {
     $tipov_id = htmlspecialchars($_GET['tipov_id']);
     $vae_cod = htmlspecialchars($_GET['vae_cod']);
     $vae_IDCliente = htmlspecialchars($_GET['vae_IDCliente']);
     $cli_direccion = htmlspecialchars($_GET['hide_cli_direccion']);
     $cli_documento = htmlspecialchars($_GET['hide_cli_documento']);
     $cli_email = htmlspecialchars($_GET['hide_cli_email']);
     $cli_nombres = htmlspecialchars($_GET['hide_cli_nombres']);
     $cli_telefono = htmlspecialchars($_GET['hide_cli_telefono']);
     $montoCupon = htmlspecialchars($_GET['hide_montoCupon']);
     $cuentaAfectada = htmlspecialchars($_GET['cuenta_afectada']);
     $esVoucher = htmlspecialchars($_GET['esVoucher']);
     
     
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="height: 100%;">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Separar Cuentas</title>
        <link rel="StyleSheet" href="../css/style_separarCuentas.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>

        <!-- Scripts para keyboard -->
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>


        <script src="../js/jquery.min.js"></script>
<!--        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>-->
        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/ajax_separarCuentas.js"></script>    
        <script type="text/javascript" src="../js/ajax.js"></script>

        <!--Scripts para alertas-->
        <link rel="stylesheet" href="../css/alertify.core.css" />
        <link rel="stylesheet" href="../css/alertify.default.css" />
        <script type="text/javascript" src="../js/alertify.js"></script> 
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />


        <!--Para el funcionamiento de la calculadora.-->
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script type="text/javascript" src="../js/ajax_ordenPedido.js"></script>
        <script type="text/javascript" src="../js/teclado.js"></script>
        <!--end funcionamiento de la calculadora.-->
        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>

        <!--<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css"/>-->
        <link rel="stylesheet" type="text/css" href="../bootstrap/v5/css/bootstrap.min.css">
        <!--<script type="text/javascript" src="../bootstrap/js/bootstrap.js"></script>-->
        <script type="text/javascript" src="../bootstrap/v5/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="../js/kds.js"></script>
    </head>
    <?php
    $mesa_id = 0;
    if (htmlspecialchars(htmlspecialchars(isset($_GET["mesa_id"])))) {
        $mesa_id = htmlspecialchars($_GET["mesa_id"]);
    } else {
        $mesa_id = htmlspecialchars($_POST["mesa_id"]);
    }

    if (htmlspecialchars(htmlspecialchars(isset($_GET["odp_id"])))) {
        $odp_id = htmlspecialchars($_GET["odp_id"]);
    } else {
        $odp_id = htmlspecialchars($_POST["odp_id"]);
    }

    if (htmlspecialchars(htmlspecialchars(isset($_GET["cdn_tipoimpuesto"])))) {
        $text_cdn_tipoimpuesto = htmlspecialchars($_GET["cdn_tipoimpuesto"]);
    } else {
        $text_cdn_tipoimpuesto = htmlspecialchars($_POST["cdn_tipoimpuesto"]);
    }

    $est_ip = htmlspecialchars($_GET['est_ip']);

    if ($text_cdn_tipoimpuesto == 'Diferenciado') {
        $cdn_tipoimpuesto = 1;
    } else {
        $cdn_tipoimpuesto = 0;
    }
    if (htmlspecialchars(htmlspecialchars(isset($_GET['cat_id'])))) {
        $codigoCategoria = htmlspecialchars($_GET['cat_id']);
    }
    if (htmlspecialchars(htmlspecialchars(isset($_GET['rst_id'])))) {
        $rst_id = htmlspecialchars($_GET['rst_id']);
    }

    $usr_id = $_SESSION['usuarioIdAdmin']; //usuarioId
    $cdn_id = $_SESSION['cadenaId'];
    $_SESSION['cargoPisoArea'] = "Si";
//    $lc_IDPeriodo = $_SESSION['IDPeriodo'];
//    $lc_ctrc_id=$_SESSION['IDControlEstacion'];
    $lc_est_id=$_SESSION['estacionId'];
    ?>
    <body style="height: 100%;">



        <div id="formCobrar">
            <input inputmode="none"  type="hidden" name="hide_cat_id" id="hide_cat_id" value="<?php echo $codigoCategoria; ?>"/>
            <input inputmode="none"  type="text" name="hide_pluId" id="hide_pluId"/>
            <input inputmode="none"  type="hidden" name="hide_magp_id" id="hide_magp_id"/>
            <input inputmode="none"  type="hidden" name="hide_odp_id" id="hide_odp_id" value="<?php echo $odp_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_dop_id" id="hide_dop_id"/>
            <input inputmode="none"  type="hidden" name="hide_mesa_id" id="hide_mesa_id" value="<?php echo $mesa_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo $usr_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_cdn_tipoimpuesto" id="hide_cdn_tipoimpuesto" value="<?php echo $cdn_tipoimpuesto; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
            <input inputmode="none"  type="hidden"name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $lc_est_id;?>" />
            <input inputmode="none"  type="hidden" name="hid_cla_id" id="hid_cla_id"/>
            <input inputmode="none"  type="hidden" name="hide_menu_id" id="hide_menu_id"/>
            <input inputmode="none"  type="hidden" name="cantidadOK" id="cantidadOK"/>

        </div>

        <div id="contenedorTotal">


            <!--            <div class="contenedorIzquierda">
                            <div class= "content_split">   
                                <div class="header_split"  onclick= "fn_recuperar_cuenta_split(1)" >   
                                    <label>Recuperar cuenta</label> 
                                </div>
                            </div>     
                            <div class="listaPedido">
                                <div class="listado">
                                    <ul id="id1" class="listadoPedido">
            
                                    </ul>
                                </div>
                            </div>
                            <div class="botonesLista" >
                                <button id="btn_facturarCuenta" class="facturarCuenta" onclick="fn_facturarCuenta(1)" title="Facturar Cuenta">Cobrar</button>
                            </div>
                            <div class="calculosLista">
                                <table class="calculo" border="0" cellpadding="1" cellspacing="0">
                                    <tr>
                                        <td width="300" align="right">Subtotal: </td>
                                        <td width="20"></td>
                                        <td align="right" class="subTotal_id1"></td>
                                        <td width="30"></td>
                                    </tr>
                                    <tr class="hide_impuesto">
                                        <td width="300" align="right">Base Iva 12%: </td>
                                        <td width="20"></td>
                                        <td align="right" class="baseDoce_id1"></td>
                                        <td width="30"></td>
                                    </tr>
                                    <tr  class="hide_impuesto">
                                        <td width="300" align="right">Base Iva 0%: </td>
                                        <td width="20"></td>
                                        <td align="right" class="baseCero_id1"></td>
                                        <td width="30"></td>
                                    </tr>                        
                                    <tr>
                                        <td width="300" align="right">Iva 12%: </td>
                                        <td width="20"></td>
                                        <td align="right" class="Iva_id1"></td>
                                        <td width="30"></td>
                                    </tr>
                                    <tr >
                                        <td width="300" align="right"></td>
                                        <td width="20"></td>
                                        <td style="border-top:2px dashed #000; height:5px;"></td>
                                        <td width="30"></td>
                                    </tr>
                                    <tr>
                                        <td width="300" align="right">Total: </td>
                                        <td width="20"></td>
                                        <td align="right" class="Total_id1"></td>
                                        <td width="30"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>-->

            <div id='contenedor_split'>
            </div>
            <div style="clear:both;  height:24px;"> </div>
            <div class="contenedorInferior">



            </div>

        </div>
        <div id="panel_botones_control" >
            <button  name="Panel de mesas"  id="regresar" disabled="disabled" class="facturarCuenta classDisabled" onclick="fn_regresar(<?php echo "'$mesa_id'"; ?>)">Panel de Mesas</button>
            <button  name="Agregar Cuenta" id="split_acumulado"disabled="disabled"  class="right facturarCuenta classDisabled" >Agregar Cuenta</button>
            <button  name="Dividir productos" id="split_dividir" disabled="disabled"  class="rigth facturarCuenta classDisabled" onclick="dividirProductos()">Dividir Productos</button>
            <button id="cancelar_dividir"  style="display: none" class="rigth facturarCuenta" onclick="cancelar_split_productos()">Cancelar Dividir</button>




            <?php
            if (htmlspecialchars(htmlspecialchars(isset($_GET['recuperar'])))) {
                $recuperar = htmlspecialchars($_GET['recuperar']);
                if ($recuperar == 1) {
                    echo '<button id="restablecerCuenta" class="rigth facturarCuenta" onclick="fn_recuperar_cuenta_dividida(' . "'$odp_id'" . ')">Restablecer Cuenta</button>';
                    // echo '<button id="restablecerCuenta" class="rigth facturarCuenta" onclick="fn_validarUsuarioAdministrador()">Restablecer Cuenta</button>';
                }
            }
            ?>
            <button name="Imprimir todas las Pre-Cuenta" disabled="disabled" id="imprimirCuentas" class="rigth facturarCuenta classDisabled" onclick="fn_ImprimirTodasPrecuenta()">Imprimir todas</button>
            <img   style="float: right;cursor: pointer; display: none; margin-right: 5%;"id="imgBack" onclick="clickIzquierda()"  src="../imagenes/botones/btnSiguiente.png">
                <img   style="float: right; cursor: pointer; display: none;" id="imgNext"  onclick="clickDerecha()"  src="../imagenes/botones/btnAtras.png">
                    </div>
                    <div id="anulacionesContenedor">
                        <div class="preguntasTitulo"><label>Credenciales de Administrador</label></div>
                        <div class="anulacionesSeparador">
                            <input inputmode="none"  type="password" name="usr_clave" id="usr_clave" onchange="fn_validarUsuario()" style="height: 35px; width: 454px;"/>
                        </div>
                    </div>
                    <div id="numPad"></div>

                    <div id="aumentarContador">
                        <label>Cantidad </label>
                        <input inputmode="none"  type="text" name="cantidad" id="cantidad" value="" style="width: 190px; height: 30px;"/>
                        <table style="margin-top: 20px">
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
                                <td>
                                    <button class="btnVirtualBorrar" onclick="fn_okCantidad1()">OK</button>
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
                                <td>
                                    <button class="btnVirtualBorrar" onclick="fn_eliminarCantidad()">&larr;</button>
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
                                <td>
                                    <button style="display: none" class="btnVirtualBorrar" id="btn_punto" onclick="fn_agregarNumero('.')">.</button>
                                </td>
                            </tr> 
                            <tr>
                                <td>
                                    <button id="btn_cantidad_cero" class="btnVirtual" onclick="fn_agregarNumero('0')">0</button>
                                </td>
                                <td colspan="3">
                                    <button class="btnVirtualCancelar" id="btn_punto" onclick="fn_cancelarAgregarCantidad1()" style="width: 200px;">Cancelar</button>
                                </td>
                            </tr>
                        </table>
                    </div>





                    <div  id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
                        <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                            <img src="../imagenes/loading.gif" />
                        </div>
                    </div> 

                    
         <?php 
                if (htmlspecialchars(isset($_GET['tipov_id']))) {
            ?>
            <input inputmode="none"  type="hidden" id="txt_tipov_id" value="<?php echo htmlspecialchars($tipov_id); ?>" />
            <input inputmode="none"  type="hidden" id="txt_vae_cod" value="<?php echo htmlspecialchars($vae_cod); ?>" />
            <input inputmode="none"  type="hidden" id="txt_vae_IDCliente" value="<?php echo htmlspecialchars($vae_IDCliente); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cli_direccion" value="<?php echo htmlspecialchars($cli_direccion); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cli_documento" value="<?php echo htmlspecialchars($cli_documento); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cli_email"     value="<?php echo htmlspecialchars($cli_email); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cli_nombres"   value="<?php echo htmlspecialchars($cli_nombres); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cli_telefono"  value="<?php echo htmlspecialchars($cli_telefono); ?>" />
            <input inputmode="none"  type="hidden" id="txt_montoCupon"  value="<?php echo htmlspecialchars($montoCupon); ?>" />
            <input inputmode="none"  type="hidden" id="txt_cuentaAfectada"  value="<?php echo htmlspecialchars($cuentaAfectada); ?>" />
            <input inputmode="none"  type="hidden" id="txt_esVoucher" value="<?php echo htmlspecialchars($esVoucher); ?>" />  
           <?php }?>           
                    
                    
    </body>
  </html>