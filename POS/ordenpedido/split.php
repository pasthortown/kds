<?php
session_start();

include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_seguridades.php";
include_once"../clases/clase_ordenPedido.php";
include_once"../seguridades/seguridad_niv2.inc";

$seguridades = new seguridades();

/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO: Jorge Tinoco ///////////////////////////////////////////
///////FECHA CREACION: 06/02/2016 ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Christian Pinto //////////////////////////////////////
///////DESCRIPCION: Cierre Periodo abierto mas de un dia ////////////////////
///////FECHA MODIFICACION: 07/07/2016 ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Toma de Pedido</title>

        <!-- Librerias CSS -->
        <link rel="StyleSheet" href="../css/tomaPedido_fs.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
        <!-- Scripts para scroll-->
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />

        <!-- Scripts para alertas-->
        <link rel="stylesheet" href="../css/alertify.core.css"/>
        <link rel="stylesheet" href="../css/alertify.default.css"/>
        <!-- Scripts para keyboard -->
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bloquear_acceso.css"/>

        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>


        <!-- Librerias JavaScript -->
        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <!-- Scripts para scroll-->
        <script type="text/javascript" src="../js/scroll/mousewheel.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>

        <!-- Swiper -->
        <script type="text/javascript" src="../js/swiper.js"></script>

        <!-- Scripts para alertas-->
        <script type="text/javascript" src="../js/alertify.js"></script>
        <!-- Scripts para keyboard -->
        <script type="text/javascript" src="../js/teclado.js"></script>
        <!-- Toma Pedido -->
        <script type="text/javascript" src="../js/ajax_ordenPedido.js"></script>
        <script type="text/javascript" src="../js/ajax_bloquear_acceso.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </head>

    <input inputmode="none"  type="hidden" name="bloqueo" id="bloqueo"  value="<?php echo $_SESSION['bloqueoacceso']; ?>"/>

    <?php
    $usr_id = $_SESSION['usuarioId'];
    $cdn_id = $_SESSION['cadenaId'];
    $rst_id = $_SESSION['rstId'];
    $est_id = $_SESSION['estacionId'];
    $est_ip = $_SESSION['direccionIp'];
    $nombre = $_SESSION['nombre'];
    $usuario = $_SESSION['usuario'];
    $num_Pers = 0;
    $bloqueado = $_SESSION['bloqueoacceso'];

    if (isset($_GET["numPers"])) {
        $num_Pers = $_GET["numPers"];
    }
    if (isset($_GET["numMesa"])) {
        $mesa_id = $_GET["numMesa"];
    } else {
        ?>
        <script type="text/javascript"> fn_obtenerMesa();</script>
        <?php
    }
    ?>
    <body>




        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12" style="   overflow-x: auto;  overflow-y: hidden; height: 600px">
                    <div  id="listaPedidoTomaPedido" style="height: 606px; float: left; width: 50px">
                    </div>

                    <div id="container_splits"  style="width: 250%;  margin: 0px ;  height: 100%;">

<!--                        <div  class="content_split" >
                            <div class="header_split" >
                                <label>Mesa #1  Split 1</label>
                            </div>
                            <div id="rdn_pdd_brr_nfmcn" style=" height: 90%;" class="rdn_pdd_brr_nfmcn">
                                <div id="rdn_pdd_sprdr" class="rdn_pdd_sprdr"></div>
                                <div>
                                    <div class="listado" id="listado">
                                        <ul id="listadoPedido" class="listadoPedido"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>-->


<!--                         <div class="header_split" >
                            <label>Mesa 1  Split 1</label>
                        </div>
                        <div id="rdn_pdd_brr_nfmcn" style=" height: 90%;" class="rdn_pdd_brr_nfmcn">
                            <div id="rdn_pdd_sprdr" class="rdn_pdd_sprdr"></div>
                            <div>
                                <div style="width:100%" class="listado" id="listado2">
                                    <ul class="listadoPedido" id="listadoPedido2"></ul> <li id='BA0E09E4-5D24-E711-80DF-00505686417C' onclick='fn_modificarLista("BA0E09E4-5D24-E711-80DF-00505686417C")' codigovalidador=undefined gramos=0 anular=0 ancestro='BA0E09E4-5D24-E711-80DF-00505686417C' tipo='1'><div class='listaproductosDescTomaPedido'>helados8</div><div class='listaproductosValTomaPedido'>$8.00</div><div class='listaproductosCantTomaPedido'>1</div></li></div>
                            </div>
                        </div>
                    </div> -->
<!--                                             <div  class="content_split" >
                                                <div class="header_split" >
                                                    <label>Mesa #1  Split 2</label>
                                                </div>
                                                <div id="rdn_pdd_brr_nfmcn" style=" height: 90%;" class="rdn_pdd_brr_nfmcn">
                                                    <div id="rdn_pdd_sprdr" class="rdn_pdd_sprdr"></div>
                                                    <div>
                                                        <div class="listado" id="listado2">
                                                            <ul  id="listadoPedido2"  class="listadoPedido">
                                                                <li id="CB1A1CEA-BC23-E711-80DF-00505686417C" onclick="fn_modificarLista(&quot;CB1A1CEA-BC23-E711-80DF-00505686417C&quot;)" codigovalidador="undefined" gramos="0" anular="0" ancestro="CB1A1CEA-BC23-E711-80DF-00505686417C" tipo="1"><div class="listaproductosDescTomaPedido">helados10</div><div class="listaproductosValTomaPedido">$10.00</div><div class="listaproductosCantTomaPedido">2</div></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  -->
                </div>

                <!--                   
                                        
                                        
                                        <div  class="content_split" >
                                            <div class="header_split" >
                                                <label>Mesa #1  Split 1</label>
                                            </div>
                                            <div class="body_split">
                                          
                                                
                               
                                          <ul id="listadoPedido"> </ul>
                                 
                                                <div>1 :Deli Sanduche</div>
                                            </div>
                                        </div>
                                        
                                              
                                    </div>-->


            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >

                    <!--<center> <button value="add" name="">Add</button></center>-->

            </div>
        </div>








        <div id="formCobrar">
            <input inputmode="none"  type="hidden" name="codigoCategoria" id="codigoCategoria"/>
            <input inputmode="none"  type="hidden" name="hide_menu_id" id="hide_menu_id"/>
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
            <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $est_id; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
            <input inputmode="none"  type="hidden" name="hide_cdn_tipoimpuesto" id="hide_cdn_tipoimpuesto"/>
            <input inputmode="none"  type="hidden" name="cantidadOK" id="cantidadOK" value="1" />
            <input inputmode="none"  type="hidden" name="pluAgregar" id="pluAgregar"/>
            <input inputmode="none"  type="hidden" name="magpAgregar" id="magpAgregar"/>
            <input inputmode="none"  type="hidden" name="hid_cla_id" id="hid_cla_id"/>
            <input inputmode="none"  type="hidden" name="banderaCierrePeriodo" id="banderaCierrePeriodo" value="<?php echo $_SESSION['sesionbandera']; ?>"/>
            <input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
        </div>
        <input inputmode="none"  type="hidden" id="hid_bandera_gramo" /> 
        <input inputmode="none"  type="hidden" id="hid_gramoPlu" /> 


        <!-- SubMenu Opciones -->
        <div id="rdn_pdd_brr_ccns" class="menu_desplegable">
            <!--Lado Izquierdo <div id="cnt_mn_dsplgbl_pcns_zqrd" class="modal_opciones_zqd"></div> -->
            <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                <input inputmode="none"  type="button" id='funcionesGerente' onclick='fn_funcionesGerente()' class="boton_Opcion_Bloqueado" value="Funciones Gerente" disabled="disabled" />
                <input inputmode="none"  type="button" id='btn_transacciones' onclick='fn_irTransacciones()' class="boton_Opcion_Bloqueado" value="Transacciones" disabled="disabled"/>
                <input inputmode="none"  type="button" id='resumenVentas' onclick='fn_resumenVentas()' class="boton_Opcion_Bloqueado" value="Resumen Ventas" disabled="disabled" />
                <input inputmode="none"  type="button" id='separarCuentas' onclick='fn_separar()' class="boton_Opcion_Bloqueado" value="Separar Cuentas" disabled="disabled" />
                <input inputmode="none"  type="button" id='precuenta' onclick='fn_imprimirPreCuenta()' class="boton_Opcion_Bloqueado" value="Pre Cuenta" disabled="disabled" />
                <input inputmode="none"  type="button" id='imprimir_orden' onclick='fn_imprimirOrdenPedido()' class="boton_Opcion_Bloqueado" value="Imprimir Orden" disabled="disabled" />
                <input inputmode="none"  type="button" id='buscar' onclick='fn_modalBuscador()' class="boton_Opcion_Bloqueado" value="Buscador" disabled="disabled" />
                <input inputmode="none"  type="button" id='regresar' onclick='fn_salirMesas()' class="boton_Opcion_Bloqueado" value="Salir Mesas" disabled="disabled" />
                <input inputmode="none"  type="button" id='btn_cuponesSG' class="boton_Opcion" onclick="fn_modalCupones()" value="Cupones"/>
                <input inputmode="none"  type="button" class="boton_Opcion" onclick="fn_salirSistema()" value="Salir"/>
            </div>
        </div>

        <!-- Modal Ingreso de Cantidades -->
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
                        <button class="btnVirtualBorrar" onclick="fn_okCantidad()">OK</button>
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
                        <button class="btnVirtualBorrar" id="btn_punto" onclick="fn_agregarNumero('.')">.</button>
                    </td>
                </tr> 
                <tr>
                    <td>
                        <button id="btn_cantidad_cero" class="btnVirtual" onclick="fn_agregarNumero('0')">0</button>
                    </td>
                    <td colspan="3">
                        <button class="btnVirtualCancelar" id="btn_punto" onclick="fn_cancelarAgregarCantidad()" style="width: 200px;">Cancelar</button>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Lector de Barras -->
        <div id="lectorBarras">
            <input inputmode="none"  type="text" name="txt_lectorBarras" id="txt_lectorBarras" value=""/>
        </div>

        <!-- Buscador -->
        <div id="cuadro_buscador">
            <table>
                <tr>
                    <td>
                        <label for="txt_busca">Descripci&oacute;n Producto</label>
                        <input inputmode="none"  type="text" name="txt_busca" id="txt_busca" style="width: 456px; height: 36px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="buscaProducto">
                            <table><tr></tr></table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Preguntas Sugeridas -->
        <div id="preguntasContenedor">
        </div>

        <!-- Confirmacion Credenciales -->
        <div id="anulacionesContenedor">
            <div class="preguntasTitulo"><label>Credenciales de Administrador</label></div>
            <div class="anulacionesSeparador">
                <input inputmode="none"  type="password" name="usr_clave" id="usr_clave" onchange="fn_validarUsuario()" style="height: 35px; width: 454px;"/>
            </div>
        </div>

        <!-- Codigos GO TRADE -->
        <div id="tecladoCodigos">
            <div class="preguntasTitulo"><label>Ingrese Codigo</label></div>
            <div class="anulacionesSeparador">
                <input inputmode="none"  type="text" id="txt_codigos" style="height: 35px; width: 454px;"/>
            </div>
        </div>




        <!-- Modal Cupones Sistema Gerente-->
        <div id="modalCuponSistemaGerente">
            <div class="preguntasTitulo"><label>Ingreso Cup&oacute;n Sistema Gerente</label><img src="../imagenes/admin_resources/btn_eliminar.png" onclick="fn_cerrarModalCuponesSistemaGerente()" class="btn_cerrar_modal_cupones"/></div>

            <div id="cnt_tp_nv_cnj_cpn" class="cnt_tp_nv_cnj_cpn">
                <div class="preguntasBotonPlu">
                    <input inputmode="none"  id="pcn_tp_cnj_cpn_0" class="respuestaPregunta" type="checkbox" value="0" checked="checked" disabled="disabled"/>
                    <label onclick="fn_procesoCanjearAutomatico()" for="pcn_tp_cnj_cpn_0">
                        <p>Autom&aacute;tico</p>
                    </label>
                </div>
                <div class="preguntasBotonPlu">
                    <input inputmode="none"  id="pcn_tp_cnj_cpn_1" class="respuestaPregunta" type="checkbox" value="1" disabled="disabled"/>
                    <label onclick="fn_procesoCanjearManual()" for="pcn_tp_cnj_cpn_1">
                        <p>Manual</p>
                    </label>
                </div>
            </div>

            <div id="aut_frm_cnj_cpn">
                <div class="anulacionesSeparador">
                    <input inputmode="none"  type="password" name="input_cuponSistemaGerenteAut" id="input_cuponSistemaGerenteAut" onchange="fn_canjearCuponAutomatico()" style="height: 35px; width: 454px;"/>
                </div>
            </div>
            <div id="man_frm_cnj_cpn" style="display:none;">
                <div class="cuponesSeparador">
                    <center>
                        <input inputmode="none"  type="text" id="input_cuponSistemaGerenteMan1" name="input_cuponSistemaGerenteMan1" onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan1');" style="height: 35px; width: 100px;"/> -
                        <input inputmode="none"  type="text" id="input_cuponSistemaGerenteMan2" name="input_cuponSistemaGerenteMan2" onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan2');" style="height: 35px; width: 100px;"/> /
                        <input inputmode="none"  type="text" id="input_cuponSistemaGerenteMan3" name="input_cuponSistemaGerenteMan3" onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan3');" style="height: 35px; width: 100px;"/> /
                        <input inputmode="none"  type="text" id="input_cuponSistemaGerenteMan4" name="input_cuponSistemaGerenteMan4" onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan4');" style="height: 35px; width: 100px;"/>
                    </center>
                </div>
            </div>
        </div>

        <div id="numPad"></div>
        <div id="txtPad"></div>


        <div id="mdl_prgnts_sgrds" class="modal_preguntas_sugeridas">
            <div id="mdl_pcns_prgnts_sgrds" class="modal_preguntas_opciones">
                <div id="cntndr_mdl_prgnts_sgrds" class="cntndr_mdl_prgnts_sgrds">
                    <div id="cbcr_prgnts_sgrds_cntdor" class="cbcr_prgnts_sgrds_cntdor"></div>
                    <div id="cbcr_prgnts_sgrds" class="cbcr_prgnts_sgrds"></div>
                    <div id="body_prgnts_sgrds" style="height: 700px" class="body_prgnts_sgrds">
                        <div id="cntndr_body_prgnts_sgrds" style="width: 720px;"></div>
                    </div>
                </div>
                <div id="cntndr_mdl_prgnts_sgrds_btns" class="cntndr_mdl_prgnts_sgrds_btns"></div>
            </div>
        </div>

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../imagenes/loading.gif"/>
            </div>
        </div>

    </body>
</html>