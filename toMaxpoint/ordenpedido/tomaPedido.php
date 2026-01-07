<?php
session_start();
require_once "../parametros.php";
include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_seguridades.php";
include_once "../clases/clase_ordenPedido.php";
include_once "../seguridades/seguridad_niv2.inc";
include_once "../clases/clase_facturacion.php";
include_once "../../kds/config_kds.php";

use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;

$seguridades = new seguridades();
$tomaPedido = new menuPedido();
$botonesPermitidos = $seguridades->fn_accesoPermisosPerfilBotones($_SESSION['usuarioId'], "Mesas");
$habilitarBotonQR = $tomaPedido->habilitarBotonQR($_SESSION['cadenaId'], $_SESSION['rstId']);

$objFactura = new facturas();
$lc_rst = $_SESSION['rstId'];
$lc_cdnId = $_SESSION['cadenaId'];
$unificacion_transferencia_de_venta = isset($_SESSION['unificacion_transferencia_de_venta']) ? $_SESSION['unificacion_transferencia_de_venta'] : '0';
$politicaEliminarTodo = $seguridades->fn_politicaEliminarTodo($_SESSION['cadenaId'], $_SESSION['rstId'],  "Borrar Productos Todos ?");

$politicaAppedir = $seguridades->fn_politicaAppedir($_SESSION['rstId'], $_SESSION['cadenaId']);
$autorizacionApiMasivo = $seguridades->getAutorizacionApiMasivo();

$politicaConfirmacionEliminarTodo = 0;
if ($politicaEliminarTodo) {
     $politicaConfirmacionEliminarTodo = $seguridades->fn_politicaEliminarTodo($_SESSION['cadenaId'], $_SESSION['rstId'],  "Mostrar Alerta Borrar Productos Todos ?");    
}
$pedidoAplicaPicada = $tomaPedido->pedidoAplicaPicada();
/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO: Jorge Tinoco ///////////////////////////////////////////
///////FECHA CREACION: 06/02/2016 ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Christian Pinto //////////////////////////////////////
///////DESCRIPCION: Cierre Periodo abierto mas de un dia ////////////////////
///////FECHA MODIFICACION: 07/07/2016 ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <title>Toma de Pedido</title>

        <!-- Librerias CSS -->
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery.jb.shortscroll.css"/>
        <link rel="stylesheet" type="text/css" href="../css/tomaPedido.css"/>

        <!-- Scripts para alertas-->
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
        <!-- Scripts para keyboard -->
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bloquear_acceso.css"/>

        <!--<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css"/>-->
        <link rel="stylesheet" type="text/css" href="../bootstrap/v5/css/bootstrap.min.css">
       
        <link rel="stylesheet" href="../bootstrap/bootstrapIcons/bootstrap-icons.css">
        
        <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->


        <!--<link href="../js/asset/fonts/icon.css" rel="stylesheet">-->
        <link rel="stylesheet"
              type="text/css"
              href="../css/cndjs/cloudflare/ajax/libs/font-awesome/5.15.0/css/all.min.css"/>
        <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
        <link href="../css/pagina/smart_wizard_all.min.css"
              rel="stylesheet"
              type="text/css"/>

              <link rel="stylesheet" type="text/css" href="../css/pagina/toma_pedido.css"/>      

        <link rel="stylesheet" type="text/css" href="../css/media_toma_pedido.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style_odp_modales_payphone.css"/>
        <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
        <style>

            div[aria-describedby="modalCuponSistemaGerente"] {
                top: 70px !important;
                left: 19% !important;
                width: 550px !important;
            }
            
            div[aria-describedby="cuadro_buscador"] {
                top: 84px !important;
                left: 13% !important;
            }
            div[aria-describedby="cuadro_buscador"] > #cuadro_buscador  {
                height: 200px !important;
            }

            div[aria-describedby="anulacionesContenedor"] {
                top: 10% !important;
                left: 22% !important;              
            }
            #numPad {                
                left: 31% !important;
                top: 37% !important;
                bottom: auto !important;
            }
            #cuadro_buscador {
                border: 0;
                padding: 0.5em 1em;
                background: none;
            }
        
            #alertify {
                top: 76px !important;
            }

            div[aria-describedby="contenedorComentario"] > #contenedorComentario {
                position: relative;
                border: 0;
                padding: .5em 1em;
                background: none;
                overflow: auto;
            }
        
            div[aria-describedby="cuadro_buscador"] > #cuadro_buscador {
                position: relative;
                border: 0;
                padding: .5em 1em;
                background: none;
            }

            div[aria-describedby="contenedorComentario"] {
                width: auto !important;
                top: 102px !important;
                left: 214px !important;
            }

            div[aria-describedby="contenedorComentario"] > #contenedorComentario > #comentario{
                width: 571px !important;
                height: 111px !important;
                resize: none !important;
                pointer-events: none !important;
            }
        </style>
    </head>
    <body style="overflow-y: auto" onload="fn_ValidarControlEstacionActivo();" >
    <input inputmode="none" id="politicaEliminarTodo" type="hidden" value="<?php echo $politicaEliminarTodo ?>" />
    <input inputmode="none" id="politicaConfirmacionEliminarTodo" type="hidden" value="<?php echo $politicaConfirmacionEliminarTodo ?>" />

    <?php
    $usr_id = $_SESSION['usuarioIdAdmin']; // usuarioId
    $cdn_id = $_SESSION['cadenaId'];
    $rst_id = $_SESSION['rstId'];
    $est_id = $_SESSION['estacionId'];
    $est_ip = $_SESSION['direccionIp'];
    $nombre = $_SESSION['nombre'];
    $usuario = $_SESSION['usuario'];

    $idControlEstacion = $_SESSION['IDControlEstacion'];
    $simboloMoneda = $_SESSION['simboloMoneda'];

    $vitality = (isset($_SESSION['vitality'])) ? $_SESSION['vitality'] : 0;
    if ($vitality == 1) {
        $idClienteVitality = $_SESSION['idClienteVitality'];
        $balanceVitality = $_SESSION['balanceVitality'];
    }

    $fidelizacionActiva = (isset($_SESSION['fidelizacionActiva'])) ? $_SESSION['fidelizacionActiva'] : 0;
    if ($fidelizacionActiva == 1) {
        $PerteneceGrupoAmigos = (isset($_SESSION['fdznNombres'])) ? 1 : 0;
    } else {
        $PerteneceGrupoAmigos = 0;
        $_SESSION['fdznNombres'] = null;
    }

    if ($fidelizacionActiva == 1) {
        if (isset($_SESSION['fb_econtroDatos'])) {
            $fb_econtroDatos = $_SESSION['fb_econtroDatos'];
            $fb_document = $_SESSION['fb_document'];
            $fb_name = $_SESSION['fb_name'];
            $fb_status = $_SESSION['fb_status'];
            $fb_points = $_SESSION['fb_points'];
            $fb_money = $_SESSION['fb_money'];
        } else {
            $fb_document = 0;
            $fb_name = "USUARIO";
            $fb_points = 0;
            $fb_money = 0;
            $fb_econtroDatos = 0;
            $fb_status = '';
        }
    } else {
        $fb_document = 0;
        $fb_name = "USUARIO";
        $fb_points = 0;
        $fb_money = 0;
        $fb_status = '';
        $fb_econtroDatos = 0;
        $fb_status = '';
    }

    $codigo_app;
    $codigoAppActivo = 0;

    if (htmlspecialchars(isset($_GET["codigo_app"]))) {
        $codigo_app = htmlspecialchars($_GET["codigo_app"]);
        if ($codigo_app != '') {
            $codigoAppActivo = 1;
            $respuesta = json_decode($tomaPedido->fn_consultarPedidoApp($codigo_app));
            if ($respuesta->str > 0) {
                $_SESSION['codigoAppActivo'] = 1;
                $_SESSION['fdznDocumento'] = $respuesta->identificacion_cliente;
                $_SESSION['fdznNombres'] = $respuesta->nombres_cliente;
            }
        }
    } else {
        $_SESSION['codigoAppActivo'] = null;
    }


    if (!isset($SESSION["habilitadoPorEstacion"])) {
        try {
            $responseEnable = $objFactura->fn_turneroHabilitadoPorEstacion($_SESSION['estacionId']);
            $_SESSION["habilitadoPorEstacion"] = $responseEnable['Respuesta']['habilitado'];
        } catch (Exception $e) {
            return $e;
        }
    }
    //validar que la estación tiene permitido las notificaciones Pickup
    $resultado_verificar_politica = $tomaPedido->fn_consulta_generica_escalar("EXEC dbo.verificarPoliticaRecibePickupEstacion '%s'", $_SESSION["estacionId"]);
    $estacion_recibe_notificacion_pickup = isset($resultado_verificar_politica["Activo"]) ? $resultado_verificar_politica["Activo"] : 0;
    // variable configuracion pickup activo
    if (!isset($_SESSION["configuracionPickupActivo"])) {
        try {
            $response = $tomaPedido->fn_configuracionPickup($lc_cdnId, $lc_rst);
            if ($response != NULL){
                $_SESSION["configuracionPickupActivo"] = $response["activo"];
            }
        } catch (Exception $e) {
            return $e;
        }
    }
    $configuracionPickupActivo = (isset($_SESSION["configuracionPickupActivo"])) ? $_SESSION["configuracionPickupActivo"] : 0;

    // variables configuracion turnero activo
    if (!isset($_SESSION["turneroActivo"]) || !isset($_SESSION["turneroURl"])) {
        try {
            $response = $objFactura->fn_configuracionTurnero($lc_cdnId, $lc_rst);
            $_SESSION["turneroActivo"] = $response['Respueseta']['activo'];
            $_SESSION["turneroURl"] = $response['Respueseta']['url'];
        } catch (Exception $e) {
            return $e;
        }
    }

    // variables configuracion kiosko activo
    if (!isset($_SESSION["configuracionKioskoActivo"]) || !isset($_SESSION["ordenesKioskoURL"]) || !isset($_SESSION["ordenesKioskoURL_http"])) {
        try {
            $response = $tomaPedido->fn_configuracionKiosko($cdn_id, $rst_id);
            if ($response["respuesta"]["estado"] == 1) {
                $_SESSION["configuracionKioskoActivo"] = $response["respuesta"]["activo"];
                $_SESSION["ordenesKioskoURL"] = $response["respuesta"]["url"];
                $_SESSION["ordenesKioskoURL_http"] = $response["respuesta"]["url_http"];
            }
        } catch (Exception $e) {

        }
    }
    $configuracionKioskoActivo = (isset($_SESSION["configuracionKioskoActivo"])) ? $_SESSION["configuracionKioskoActivo"] : 0;
    $ordenesKioskoURL = (isset($_SESSION["ordenesKioskoURL"])) ? $_SESSION["ordenesKioskoURL"] : "0";
    $ordenesKioskoURL_http = (isset($_SESSION["ordenesKioskoURL_http"])) ? $_SESSION["ordenesKioskoURL_http"] : "0";

     //validar que el restaurant tiene activa la politica para validar email con plugthem
     $resultado_verificar_email_plug = $tomaPedido->fn_consulta_generica_escalar("EXEC dbo.verificarPoliticaValidaEmailPlugthemSimplified '%s'", $lc_rst);
     $restaurant_valida_email = isset($resultado_verificar_email_plug["Activo"]) ? $resultado_verificar_email_plug["Activo"] : 0;
 

    // variable si la orden actual es retomada de kiosko
    $kiosko = (isset($_SESSION["kioskoActivo"])) ? $_SESSION["kioskoActivo"] : 0;
    // variable si la orden actual es retomada de pickup
    $pickup = (isset($_SESSION["pickupActivo"])) ? $_SESSION["pickupActivo"] : 0;

    $lc_tipoServicio = $_SESSION['TipoServicio'];

    $_SESSION['cargoPisoArea'] = "Si";

    $num_Pers = 0;
    $bloqueado = $_SESSION['bloqueoacceso'];

    if (htmlspecialchars(isset($_GET["numPers"]))) {
        $num_Pers = htmlspecialchars($_GET["numPers"]);
    }
    if (htmlspecialchars(isset($_GET["numMesa"]))) {
        $mesa_id = htmlspecialchars($_GET["numMesa"]);
    }

    if (htmlspecialchars(isset($_GET["cat_id"]))) {
        $cat_id = htmlspecialchars($_GET["cat_id"]);
    } else {
        $cat_id = "";
    }
    if (htmlspecialchars(isset($_GET["numSplit"]))) {
        $numSplit = htmlspecialchars($_GET["numSplit"]);


    } else {
        $numSplit = 1;
        ?>
        <!-- Oportunidad, esta funcion está dando error al recuperar una mesa en fullservices --><!-- <script type="text/javascript"> fn_obtenerMesa();</script>-->
    <?php }
    print('<input inputmode="none"  type="hidden" name="hide_numSplit" id="hide_numSplit" value="' . $numSplit . '"/>');
    ?>
    <input inputmode="none"
           type="hidden"
           name="bloqueo"
           id="bloqueo"
           value="<?php echo $_SESSION['bloqueoacceso']; ?>"/>
    <div id="formCobrar">
        <input inputmode="none" type="hidden" name="pantallaAcceso" value="TOMA PEDIDO" id="pantallaAcceso"/>
        <input inputmode="none" type="hidden" name="codigoCategoria" id="codigoCategoria"/>
        <input inputmode="none" type="hidden" name="hide_menu_id" id="hide_menu_id"/>

        <input inputmode="none" type="hidden" name="unificacion_transferencia_de_venta" id="unificacion_transferencia_de_venta" value="<?= $unificacion_transferencia_de_venta ?>" />
            
        <input inputmode="none"
               type="hidden"
               name="hide_numeroCuenta"
               value="<?php echo $numSplit; ?>"
               id="hide_numeroCuenta"/>
        <input inputmode="none" type="hidden" name="hide_tipov_id" id="hide_tipov_id"/>
        <input inputmode="none" type="hidden" name="hide_vae_IDCliente" id="hide_vae_IDCliente"/>

        <input inputmode="none" type="hidden" name="hide_cli_direccion" id="hide_cli_direccion"/>
        <input inputmode="none" type="hidden" name="hide_cli_documento" id="hide_cli_documento"/>
        <input inputmode="none" type="hidden" name="hide_cli_email" id="hide_cli_email"/>
        <input inputmode="none" type="hidden" name="hide_cli_nombres" id="hide_cli_nombres"/>
        <input inputmode="none" type="hidden" name="hide_cli_telefono" id="hide_cli_telefono"/>
        <input inputmode="none" type="hidden" id="idControlEstacion" value="<?php echo $idControlEstacion; ?>"/>

        <input inputmode="none" type="text" name="hide_pluId" id="hide_pluId" style="display: none;"/>
        <input inputmode="none" type="hidden" name="hide_magp_id" id="hide_magp_id"/>
        <input inputmode="none" type="hidden" name="hide_plu_gramo" id="hide_plu_gramo"/>
        <input inputmode="none" type="hidden" name="hide_odp_id" id="hide_odp_id"/>
        <input inputmode="none" type="hidden" name="hide_dop_id" id="hide_dop_id"/>
        <input inputmode="none" type="hidden" name="hide_mesa_id" id="hide_mesa_id" value="<?php echo $mesa_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_num_Pers" id="hide_num_Pers" value="<?php echo $num_Pers; ?>"/>
        <input inputmode="none" type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo $usr_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_cat_id" id="hide_cat_id" value="<?php echo $cat_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $est_id; ?>"/>
        <input inputmode="none" type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
        <input inputmode="none" type="hidden" id="txtTipoServicio" value="<?php echo $lc_tipoServicio; ?>"/>
        <input inputmode="none" type="hidden" name="hide_upselling" id="hide_upselling"/>
        <input inputmode="none" type="hidden" id="hide_fidelizacionActiva" value="<?php echo $fidelizacionActiva; ?>"/>

        <!-- Orden actual es retomada de kiosko -->
        <input inputmode="none" type="hidden" id="hide_ordenKiosko" value="<?php echo $kiosko; ?>"/>
        <!-- Orden actual es retomada de pickup -->
        <input inputmode="none" type="hidden" id="hide_ordenPickup" value="<?php echo $pickup; ?>"/>

        <!-- Configuracion Turnero - Activo -->
        <input inputmode="none" id="hide_turneroActivo" type="hidden" value="<?php echo $_SESSION["turneroActivo"]; ?>"/>
        <input inputmode="none" id="hide_turneroURl" type="hidden" value="<?php echo $_SESSION["turneroURl"]; ?>"/>
        <input inputmode="none" id="hide_turneroHabilitadoPorEstacion"
               type="hidden"
               value="<?php echo $_SESSION["habilitadoPorEstacion"]; ?>"/>
        <!-- Configuracion Kiosko - Activo -->
        <input inputmode="none" type="hidden" id="hide_configuracionKioskoActivo" value="<?php echo $configuracionKioskoActivo; ?>"/>
        <!-- Configuracion Turnero - Servidor Sockets -->
        <input inputmode="none" type="hidden" id="hide_ordenesKioskoURL" value="<?php echo $ordenesKioskoURL; ?>"/>
        <input inputmode="none" type="hidden" id="hide_ordenesKioskoURL_http" value="<?php echo $ordenesKioskoURL_http; ?>"/>
        <!-- Servidor Socket se encuentra levantado -->
        <input inputmode="none" type="hidden" id="hide_socketActivo" value="0"/>
        <!-- Configuracion Pickup - Activo (para ordenes en efectivo) -->
        <input inputmode="none" type="hidden" id="hide_configuracionPickupActivo" value="<?php echo $configuracionPickupActivo; ?>"/>
        <!-- Verificar la politica para cargar Socket de Notificaciones Pickup -->
        <input inputmode="none" type="hidden"
               id="hide_politica_recibe_notificacion_pickup"
               value="<?php echo $estacion_recibe_notificacion_pickup; ?>"/>

        <input inputmode="none" type="hidden" name="hide_cdn_tipoimpuesto" id="hide_cdn_tipoimpuesto"/>
        <input inputmode="none" type="hidden" name="cantidadOK" id="cantidadOK" value="1"/>
        <input inputmode="none" type="hidden" name="pluAgregar" id="pluAgregar"/>
        <input inputmode="none" type="hidden" name="magpAgregar" id="magpAgregar"/>
        <input inputmode="none" type="hidden" name="hid_cla_id" id="hid_cla_id"/>
        <input inputmode="none"
               type="hidden"
               name="banderaCierrePeriodo"
               id="banderaCierrePeriodo"
               value="<?php echo $_SESSION['sesionbandera']; ?>"/>
        <input inputmode="none" type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
        <input inputmode="none" type="hidden" name="cantidadKG" id="cantidadKG" value="0"/>
        <input inputmode="none" type="hidden" name="alPeso" id="alPeso" value="0"/>
        <input inputmode="none" type="hidden" name="ventaAlPeso" id="ventaAlPeso" value="0"/>
        <input inputmode="none" type="hidden" name="aplicaLectorBarras" id="aplicaLectorBarras" value="0"/>
        <input inputmode="none" type="hidden" name="num_puntos" id="num_puntos" value="<?php echo $fb_points; ?>"/>
        <input inputmode="none"
               type="hidden"
               name="num_puntosIniciales"
               id="num_puntosIniciales"
               value="<?php echo $fb_points; ?>"/>
        <input inputmode="none" type="hidden" name="saldo" id="saldo" value="<?php echo $fb_money; ?>"/>

        <input inputmode="none" type="hidden" name="num_puntos_plusActual" id="num_puntos_plusActual" value=""/>
        <input inputmode="none"
               type="hidden"
               name="num_puntos_plusActualiniciales"
               id="num_puntos_plusActualiniciales"
               value=""/>

        <input inputmode="none" type="hidden" name="puntosPorPluNoAgrupado" id="puntosPorPluNoAgrupado" value="0"/>

        <input inputmode="none"
               type="hidden"
               name="hide_PerteneceGrupoAmigos"
               id="hide_PerteneceGrupoAmigos"
               value="<?php echo $PerteneceGrupoAmigos; ?>"/>


        <input inputmode="none"
               type="hidden"
               name="hide_fb_econtroDatos"
               id="hide_fb_econtroDatos"
               value="<?php echo $fb_econtroDatos; ?>"/>
        <input inputmode="none"
               type="hidden"
               name="hide_fb_status"
               id="hide_fb_status"
               value="<?php echo $fb_status; ?>"/>

        <input inputmode="none" type="hidden" name="fdzn_cedula" id="fdzn_cedula" value="<?php echo $fb_document; ?>"/>

        <input inputmode="none"
               type="hidden"
               name="simboloMoneda"
               id="simboloMoneda"
               value="<?php echo $simboloMoneda; ?>"/>
        <input inputmode="none"
               type="hidden"
               name="fdzn_nombre_cliente"
               id="fdzn_nombre_cliente"
               value="<?php echo $_SESSION['fdznNombres']; ?>"/>

        <input inputmode="none" id="hidVitality" type="hidden" value="<?php echo $vitality; ?>"/>
        <!--              <input inputmode="none"  type="hidden" name="puntosQueValeElplu" id="puntosQueValeElplu" value="0" />
        <input inputmode="none"  type="hidden" name="CantidadDePlus" id="CantidadDePlus" value="0" />
        <input inputmode="none"  type="hidden" name="PuntosPluSugerido" id="puntosQueValeElplu" value="0" />-->
    </div>

    <input inputmode="none" id="hidMenuEsAgregador" type="hidden" value="0"/>
    <input inputmode="none" type="hidden" id="hid_bandera_gramo"/>
    <input inputmode="none" type="hidden" id="hid_gramoPlu"/>
    <input inputmode="none" type="hidden" id="txtDocumentoClientePaypone" value=""/>

    <div id="pnt_rdn_pdd_mxpnt" class="pnt_rdn_pdd_mxpnt">

        <!-- Informacion Orden del Pedido -->
        <div id="rdn_pdd_brr_nfmcn" class="rdn_pdd_brr_nfmcn">
            <div id="cntdr_mn_dnmc_stcn" style="width: 260px; height: 65px;">
                <!--                    <input inputmode="none"  name="Que alcanza" type="button"   id='queAlcanza' onclick="fn_abuscarProductosQuemeAlcanza()"    class="boton_Accion_Bloqueado_puntos queAlcanza_activo" alt="Guardar Cuenta"  /> -->
                <input inputmode="none"
                       name="Que alcanza"
                       type="button"
                       id='queAlcanza'
                       onclick="fn_abuscarProductosQuemeAlcanza()"
                       class="boton_Accion_Bloqueado_puntos queAlcanza_activo"
                       alt="Guardar Cuenta"/>
            </div>
            <div id="listaPedidoTomaPedido">
                <div id="listado">
                    <ul id="listadoPedido"></ul>
                </div>
            </div>
            <!-- Pedidos Kiosko y Pickup -->
            <div id="Pedidoskiosko">
                <ul id="listaPedidosKiosko"></ul>
            </div>
            <!-- Buscador de Pedidos Kiosko y Pickup -->
            <div id="pedidosKioskoAccionesBuscador" style="display:flex;justify-content:center;align-items:center;padding: 0px 0px;margin:1px;">
                <button type="button" id="btnAccionPedidosKioskoBuscador" class="boton_Accion" style="margin: 0; width: 100%!important" title="Buscar pedidos" aria-label="Buscar pedidos" onclick="mostrarModalBuscadorCodigoKioskoPickup(true)">
                    <i class="bi bi-search" style="font-size:18px; margin:1px; vertical-align:middle;"></i>
                </button>
            </div>
            <div id="nfrmcn_srs_sstm" class="nfrmcn_srs_sstm" onclick="fn_salirSistema()" style="min-height:100px;height:auto;">
                <div id="informacionFidelizacion">
                    <?php
                    if ($fidelizacionActiva == 1 || $codigoAppActivo == 1) {
                        // var_dump($_SESSION);
                        if (isset($_SESSION['fdznNombres'])) {
                            echo '<div>Hola, <span id="nombreCliente">' . $_SESSION['fdznNombres'] . '</span></div><div id="TotalPuntos" style="width: 120px; display: inline-block;"></div><div id="TotalSaldo" style="display: inline-block;"></div>';
                        } else {
                            echo "<script>document.getElementById('listaPedidoTomaPedido').style.height = \"630px\";    </script>";
                        }
                    }
                    if ($vitality == 1) {
                        //echo '<div>VITALITY</div><div id="CupoVitality" style="width: 120px; display:none;">Cupo: $<span id="spnBalanceVitality" >5</span></div>';
                        echo '<div>VITALITY</div><div id="CupoVitality" style="width: 120px; display:none;">Cupo: $<span id="spnBalanceVitality" >' . $balanceVitality . '</span></div>';
                    }
                    ?>
                </div>
                <div id="nmbr_srs_sstm" class="nmbr_srs_sstm">
                    <?php echo $nombre; ?> <span id="btnDesmesa"></span>
                    <div id="nfrmcn_srs_sstm_periodo" class="nfrmcn_srs_sstm_periodo"></div>
                    <div id="horayminuto" class="horayminuto"></div>
                    <div class="data-client"></div>
                </div>
                <div id="nfrmcn_srs_sstm_hora" class="nfrmcn_srs_sstm_hora"></div>
            </div>
        </div>

        <div id="rdn_pdd_ctgrs_prdcts" class="rdn_pdd_ctgrs_prdcts">
            <!-- Productos -->
            <div id="cnt_rdn_pdd_pls" class="cnt_rdn_pdd_pls">
                <div id="barraProducto"></div>
            </div>
            <!-- Categorias  -->
            <div id="cnt_rdn_pdd_ctgrs" class="cnt_rdn_pdd_ctgrs">
                <div id="barraCategoria"></div>
            </div>
            <!-- Acciones -->
            <div id="cnt_rdn_pdd_btns" class="cnt_rdn_pdd_btns" style="width: 770px; position: initial !important">
                <button name="Menu"
                        type="button"
                        id="boton_sidr"
                        value="Menu"
                        class="boton_Accion position-relative"
                        style="margin-right: 14px; height: 90%">
                    Menu
                    <span id="alertaPickupMenu"
                          class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle d-none">
                                 <span class="visually-hidden">New alerts</span>
                             </span>
                </button>
                <input inputmode="none"
                       name="Actualizar"
                       type="button"
                       id="boton_ref"
                       alt="Actualizar"
                       class="boton_Accion refresh_activo classDisabled"
                       onclick="fn_actualizar()"/>
                <input inputmode="none"
                       name="Guardar Cuenta"
                       type="button"
                       servicio="2"
                       id='Volver'
                       onclick="fn_volver(<?php echo "'$mesa_id'"; ?>)"
                       disabled="disabled"
                       class="boton_Accion_Bloqueado guardar_activo classDisabled"
                       alt="Guardar Cuenta"/>
                <input inputmode="none"
                       name="Comentar"
                       type="button"
                       id='comentar'
                       onclick='fn_comentar()'
                       alt="Comentar"
                       class="boton_Accion_Bloqueado comentar_activo classDisabled"
                       disabled="disabled"/>
                <input inputmode="none"
                       name="Eliminar"
                       type="button"
                       id='btn_eliminarElemnto'
                       onclick='fn_validarAnulacion()'
                       alt="Eliminar"
                       class="boton_Accion_Bloqueado eliminar_bloqueado classDisabled"
                       disabled="disabled"/>
                <input inputmode="none"
                       name="Cantidad"
                       type="button"
                       id='agregarCantidad'
                       class="boton_Accion_Bloqueado cantidad_activo classDisabled"
                       alt="Cantidad"
                       disabled="disabled"/>
                <!-- DESACTIVAR BOTÓN DE CUPON EN LA PANTALLA TOMA DE PEDIDO
                        <input inputmode="none"  name="Cobrar Voucher" type="button"  title=""  id='cobrarVoucher' onclick='abrirModalVoucher(<?php echo $numSplit; ?>)' class="boton_Accion_Bloqueado pagovoucher classDisabled" alt="Pagar" disabled="disabled"/>
                    -->
                <input inputmode="none"
                       name="Cobrar"
                       type="button"
                       id='cobrar'
                       onclick='fn_cobrar(<?php echo $numSplit; ?>)'
                       class="boton_Accion_Bloqueado ok_activo classDisabled"
                       alt="Pagar"
                       disabled="disabled"/>
                <!--<input inputmode="none"  name="Guardar Cuenta" type="button" id='btn_sistema' onclick='fn_guardarCuenta()' class="boton_Accion_Bloqueado guardar_activo classDisabled" alt="Guardar Cuenta" disabled="disabled" />-->

                <?php
                if ($habilitarBotonQR == 1) {
                    echo '<input inputmode="none"  name="btnagregarPromocion" type="button" id="btnagregarPromocion" class="boton_Accion ok_CuponElectronico " alt="Cupon Electronico" data-qr-type="' . $habilitarBotonQR . '" /> ';
                }
                ?>

                <!--<input inputmode="none"  type="button" id='Volver' onclick="fn_volver(<?php echo "'$mesa_id'"; ?>)" class="boton_Accion_Bloqueado ok_atras" alt="Pagar"  />-->

                <button id="btnArmarPicada" 
                        class="boton_Accion position-relative"
                        style="padding: 2px 12px; margin-right: 4px; height: 89%;<?php if(!$pedidoAplicaPicada) echo ' display: none;'; ?>"
                        type="button"
                        onclick="armarPicada()">
                        Armar Picada
                </button>
        
                <input inputmode="none"
                       type="button"
                       id="etiqueta_cantidad"
                       class="boton_Cantidad"
                       value="x1"
                       disabled="disabled"/>
            </div>
        </div>

    </div>

    <!-- SubMenu Opciones -->
    <div id="rdn_pdd_brr_ccns" class="menu_desplegable">
        <!-- Menu Izquierdo -->
        <div id="cnt_mn_dsplgbl_pcns_zqrd" class="modal_opciones_zqd">
            <input inputmode="none"
                   name="Promociones"
                   type="button"
                   id='btnPromocionesCodigo'
                   class="boton_Opcion"
                   value="Promociones"/>
            <!-- <input inputmode="none"  name="Promociones" type="button" id='btnPromocionesCodigo' onclick='' class="boton_Opcion_Bloqueado classDisabled" value="Promociones" disabled="disabled" /> -->
            <input inputmode="none"
                   name="Fidelizacion"
                   type="button"
                   id='btnFidelizacion'
                   class="boton_Opcion"
                   value="Fidelización"
                   onclick="fn_modalFidelizacion(true)"/>
            <!--Boton Vitality-->
            <input inputmode="none"
                   type="button"
                   id="btnIrPntVitality"
                   class="boton_Opcion"
                   value="Vitality"
                   onclick="opcionSetVitality('Vitality');"/>
            <input inputmode="none"
                   type="button"
                   id="btnCampanaSolidaria"
                   class="boton_Opcion"
                   value="Campaña Solidaria"
                   onclick="fn_modalCampanaSolidaria();"/>
                   <?php 
                        if ($politicaEliminarTodo) {
                            ?> 
                            <input inputmode="none"
                            type="button"
                            id="btnDeleteAllProduct"
                            class="boton_Opcion"
                            value="Limpiar Productos"
                            onclick="fnDirectDeleteAllProduct();"/>
                            <?php
                        }
                    ?>

            <button id="btnPedidoPickupPanel"
                    class="btn btn-primary position-relative boton_Opcion btnPedidosPickupNothing"
                    onclick="openPedidosPickup();">
                <div style="position: absolute; width: 30px; top: 25%;">
                    <?php include '../css/images/readypickup.svg' ?>
                </div>
                Pedidos Pick-Up
                <span id="countPedidosPickupBadge"
                      class="position-absolute top-0 start-100 translate-middle badge bg-primary">0</span>
            </button>

            <input inputmode="none"
                   name="LeerPromocion"
                   type="button"
                   id='btnLeerPromocion'
                   class="boton_Opcion"
                   value="Leer Promoción"
                   onclick="fn_modalLeerPromocion(true)"/>

            <!-- BTNAPPEDIR -->
            <?php if($politicaAppedir->active) {?>
                <input type="hidden" id="limiteCaraterAppedir" name="limiteCaraterAppedir" value="<?= $politicaAppedir->limiteCaracter?>">
                <input type="hidden" id="ApiMasivo" name="ApiMasivo" value="<?= htmlspecialchars($autorizacionApiMasivo, ENT_QUOTES, 'UTF-8') ?>">
                <input inputmode="none" name="AppedirQrCodigo" type="button" id='btnAppedirQrCodigo' class="boton_Opcion" value="Lealtad Masivo" onclick="fn_modalQrCodigoAppedir(true)" />
            <?php } ?>

        </div>
        <!-- Menu Derecho -->
        <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
            <input inputmode="none"
                   name="Funciones Gerente"
                   type="button"
                   id='funcionesGerente'
                   onclick='fn_funcionesGerente()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Funciones Gerente"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="Transacciones"
                   type="button"
                   id='btn_transacciones'
                   onclick='fn_irTransacciones()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Transacciones"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="Resumen Ventas"
                   type="button"
                   id='resumenVentas'
                   onclick='fn_resumenVentas()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Resumen Ventas"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="Separar cuentas"
                   servicio="2"
                   type="button"
                   id='separarCuentas'
                   onclick='fn_separar()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Separar Cuentas"/>
            <input inputmode="none"
                   servicio="2"
                   name="Imprimir Pre-Cuenta"
                   type="button"
                   id='precuenta'
                   onclick='fn_CrearEImprimirPreCuenta(<?php echo $numSplit; ?>)'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Pre Cuenta"
                   disabled="disabled"/>
            <?php
            if ($numSplit == 1) {
                echo '<input inputmode="none"   servicio="2" name = "Dividir por personas" type = "button" id = "dividirCuenta" onclick = "fn_dividirCuenta(' . "'$mesa_id'" . ')" class = "boton_Opcion_Bloqueado classDisabled" value = "Dividir Cuenta" />';
            }
            ?>
            <input inputmode="none"
                   name="Imprimir Orden Pedido"
                   type="button"
                   id='imprimir_orden'
                   onclick='fn_imprimirOrdenPedido()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Imprimir Orden"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="Buscar"
                   type="button"
                   id='buscar'
                   onclick='fn_modalBuscador()'
                   class="boton_Opcion_Bloqueado classDisabled"
                   value="Buscador"
                   disabled="disabled"/>
            <!--<input inputmode="none"  name="Salir mesas" type="button" id='regresar' onclick='fn_salirMesas()'
                   class="boton_Opcion_Bloqueado classDisabled" value="Salir Mesas" disabled="disabled"/>-->
            <input inputmode="none"
                   name="Cupones"
                   type="button"
                   id='btn_cuponesSG'
                   class="boton_Opcion_Bloqueado classDisabled"
                   onclick="fn_modalCupones()"
                   value="Cupones"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="btnCancelarOrden"
                   type="button"
                   id="btnCancelarOrdenFidelizacion"
                   class="boton_Opcion_Bloqueado"
                   onclick="fn_cancelarOrdenFidelizacion()"
                   value="Cancelar Orden"
                   disabled="disabled"/>
            <input inputmode="none"
                   name="Salir"
                   type="button"
                   id="salir"
                   class="boton_Opcion_Bloqueado classDisabled"
                   onclick="fn_salirSistema()"
                   value="Salir"/>
        </div>
    </div>

    <!-- Modal Ingreso de Cantidades -->
    <div id="aumentarContador">
        <label>Cantidad </label>
        <input inputmode="none" type="text" name="cantidad" id="cantidad" value="" style="width: 190px; height: 30px;" readonly/>
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
                    <button id="ok_cantidad" class="btnVirtualBorrar" onclick="fn_okCantidad()">OK</button>
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
                    <button class="btnVirtualCancelar"
                            id="btn_punto"
                            onclick="fn_cancelarAgregarCantidad()"
                            style="width: 200px;">Cancelar </button>
                </td>
            </tr>
        </table>
    </div>

    <!-- Lector de Barras -->
    <div id="lectorBarras">
        <input inputmode="none" type="text" name="txt_lectorBarras" id="txt_lectorBarras" value=""/>
    </div>

    <!-- Buscador -->
    <div id="cuadro_buscador" name="cuadro_buscador" class="cuadro_buscador">
        <table>
            <tr>
                <td>
                    <label for="txt_busca">Descripci&oacute;n Producto</label>
                    <input inputmode="none" type="text" name="txt_busca" id="txt_busca" style="width: 456px; height: 36px; pointer-events: none;" readonly/>
                </td>
            </tr>
            <tr>
                <td>
                <div id="buscaProducto" style="height: 160px;overflow-x: hidden;overflow-y: scroll;width: 100%;padding: 10px;">
                        <table>
                            <tr></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Preguntas Sugeridas -->
    <div id="preguntasContenedor"></div>

    <!-- Confirmacion Credenciales -->
    <div id="anulacionesContenedor">
        <div class="preguntasTitulo"><label>Credenciales de Administrador</label></div>
        <div class="anulacionesSeparador">
            <input inputmode="none"
                   type="password"
                   name="usr_clave"
                   id="usr_clave"
                   onchange="fn_validarUsuario()"
                   style="height: 35px; width: 454px;"
                   readonly/>
        </div>
    </div>

    <!-- Codigos GO TRADE -->
    <div id="tecladoCodigos">
        <div class="preguntasTitulo"><label>Ingrese Codigo</label></div>
        <div class="anulacionesSeparador">
            <input inputmode="none" type="text" id="txt_codigos" style="height: 35px; width: 454px;"/>
        </div>
    </div>
    <!-- Modal Pickup Pedidos -->
    <div class="modal fade"
         id="modalPickupPedidos"
         data-bs-backdrop="false"
         data-backdrop="false"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content " style="min-height: 90vh">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="staticBackdropLabel">Listado de pedidos
                        <bold>Pide y Recoge</bold>
                        <button type="button" style="width: auto; height: auto" class="btn btn-primary ms-3 disabled">
                            Pedidos totales <span id="countPedidosPickupBadgeModal" class="badge bg-danger">0</span>
                        </button>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="container-fluid d-flex">
                        <div id="containerPickupEmpty" class="m-auto d-none"><span style="font-weight: bolder">No hay pedidos en curso</span>
                        </div>
                        <div id="tablaContainerPedidosPickup" class="table-responsive custom-table-responsive d-none">
                            <table id="tabla-pedidos-pickup" class="table custom-table-pickup table-borderless">
                                <thead>
                                <tr class="">
                                    <th scope="col">Código App</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Detalles</th>
                                    <th scope="col">Observación</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Hora de retiro</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Minimizar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Lista de tiendas -->
    <div class="modal fade"
         id="modalPickupTiendas"
         data-bs-backdrop="false"
         data-backdrop="false"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content " style="min-height: 90vh; max-height: 90vh">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="staticBackdropLabel">Transferir un pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="container-fluid">

                        <div id="smartwizardTransferirPedido">
                            <ul class="nav">
                                <li class="nav-item"><a href="#step1" class="nav-link">Paso 1<br> <small>Tienda</small></a>
                                </li>
                                <li class="nav-item"><a href="#step2" class="nav-link">Paso 2<br> <small>Motivo</small></a>
                                </li>
                                <li class="nav-item"><a href="#step3" class="nav-link">Paso 3 <br>
                                        <small>Confirmar</small></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="step1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">

                                </div>
                                <div id="step2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">

                                </div>
                                <div id="step3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="transferir-pedido-prev-btn" type="button">Regresar</button>
                    <button class="btn btn-secondary" id="transferir-pedido-next-btn" type="button">Siguiente</button>
                    <button class="btn btn-primary" id="transferir-pedido-finish-btn" type="button">Finalizar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pickup Detalles Pedido -->
    <div class="modal fade"
         id="modalPickupDetallesPedido"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content " style="min-height: 50vh">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="staticBackdropLabel">Detalles de pedido
                        <b id="name-transaction">0001</b>
                    </h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <div class="container-fluid d-flex">
                        <div id="autorizaciones-pago" class="card">
                            <div class="card-header">
                                <b>Autorizaciones de pago:</b>
                            </div>
                            <div id="autorizaciones-pago-body"
                                 class="card-body d-flex justify-content-center flex-wrap">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal confirmar cualquier cosa -->
    <div id="modalConfirmar"
         class="modal"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmarTitle"></h5><button type="button"
                                                                                  class="btn-close"
                                                                                  data-bs-dismiss="modal"
                                                                                  aria-label="Close">

                    </button>
                </div>
                <div id="modalConfirmarBody" class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button id="modalConfirmarOk" type="button" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comentarios -->

    <div id="contenedorComentario">

        <label id="plu_comentar"></label>
        <div onclick="fn_cerrarModalComentar()"
             style="text-align: right;background-color: green;width: 100px;float: right;/*! height: 20px; */padding: 10px;text-align: center;font-weight: bold;color: white;border-radius: 5px;">
            Cerrar
        </div>
        <br/>
        <br/>
        <textarea  id="comentario" inputmode="none"  style="width: 460px; height: 200px; resize:none; pointer-events: none;"></textarea>
    </div>
    <!-- Modal Código de Barras  -->
    <div id="contenedorCodigoBarras">
                <label id="plu_comentar_CodigoBarras"> Código de Barras</label>
                <div onclick="fn_cerrarModalCodigoBarras()"
                    style="text-align: right;background-color: red;width: 100px;float: right;/*! height: 20px; */padding: 10px;text-align: center;font-weight: bold;color: white;border-radius: 5px;">
                    Cerrar
                </div>
                <br/>
                <br/>
                <textarea id="comentariocb" style="width: 460px; height: 100px;float: right;resize:none;" onkeypress="validarEnter();"></textarea>
            </div>
    <!-- Modal Cupones Sistema Gerente-->
    <div id="modalCuponSistemaGerente">
        <div class="preguntasTitulo"><label>Ingreso Cup&oacute;n Sistema
                Gerente</label><img src="../imagenes/admin_resources/btn_eliminar.png"
                                    onclick="fn_cerrarModalCuponesSistemaGerente()"
                                    class="btn_cerrar_modal_cupones"/></div>

        <div id="cnt_tp_nv_cnj_cpn" class="cnt_tp_nv_cnj_cpn">
            <div class="preguntasBotonPlu">
                <input inputmode="none"
                       id="pcn_tp_cnj_cpn_0"
                       class="respuestaPregunta"
                       type="checkbox"
                       value="0"
                       checked="checked"
                       disabled="disabled"/>
                <label onclick="fn_procesoCanjearAutomatico()" for="pcn_tp_cnj_cpn_0">
                    <p>Autom&aacute;tico</p>
                </label>
            </div>
            <div class="preguntasBotonPlu">
                <input inputmode="none"
                       id="pcn_tp_cnj_cpn_1"
                       class="respuestaPregunta"
                       type="checkbox"
                       value="1"
                       disabled="disabled"/>
                <label onclick="fn_procesoCanjearManual()" for="pcn_tp_cnj_cpn_1">
                    <p>Manual</p>
                </label>
            </div>
            <div class="preguntasBotonPlu">
                <input inputmode="none"
                       id="pcn_tp_cnj_cpn_2"
                       class="respuestaPregunta"
                       type="checkbox"
                       value="2"
                       checked="checked"
                       disabled="disabled"/>
                <label onclick="fn_procesoCanjearCuponDigital()" for="pcn_tp_cnj_cpn_2">
                    <p>Digital</p>
                </label>
            </div>
        </div>

        <div id="aut_frm_cnj_cpn">
            <div class="anulacionesSeparador">
                <input inputmode="none"
                       type="password"
                       name="input_cuponSistemaGerenteAut"
                       id="input_cuponSistemaGerenteAut"
                       onchange="fn_canjearCuponAutomatico()"
                       style="height: 35px; width: 454px;"/>
            </div>
        </div>
        <div id="aut_frm_cnj_cpn_digital">
            <div class="anulacionesSeparador">
                <input inputmode="none"
                       type="password"
                       name="input_cuponSistemaGerenteDigital"
                       id="input_cuponSistemaGerenteDigital"
                       onchange="fn_canjearCuponesDigitales()"
                       style="height: 35px; width: 454px;"/>
            </div>
        </div>
        <div id="man_frm_cnj_cpn" style="display:none;">
            <div class="cuponesSeparador">
                <center>
                    <input inputmode="none"
                           type="text"
                           id="input_cuponSistemaGerenteMan1"
                           name="input_cuponSistemaGerenteMan1"
                           onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan1');"
                           style="height: 35px; width: 100px;" readonly/> -
                    <input inputmode="none"
                           type="text"
                           id="input_cuponSistemaGerenteMan2"
                           name="input_cuponSistemaGerenteMan2"
                           onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan2');"
                           style="height: 35px; width: 100px;" readonly/> /
                    <input inputmode="none"
                           type="text"
                           id="input_cuponSistemaGerenteMan3"
                           name="input_cuponSistemaGerenteMan3"
                           onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan3');"
                           style="height: 35px; width: 100px;" readonly/> /
                    <input inputmode="none"
                           type="text"
                           id="input_cuponSistemaGerenteMan4"
                           name="input_cuponSistemaGerenteMan4"
                           onclick="fn_activarCasilla('#input_cuponSistemaGerenteMan4');"
                           style="height: 35px; width: 100px;" readonly/>
                </center>
            </div>
        </div>
    </div>

    <!-- Modal Campaña Solidaria-->
    <div id="modalCamapanaSolidaria">
        <?php include 'campanaSolidaria.php'; ?>
    </div>

    <div id="numPad"></div>
    <div id="txtPad"></div>
    <div id="keyboard" style="top: 294px !important; z-index: 99999 !important;height: fit-content; width: 723px !important" class="keyboard"></div>
    <div id="dominio1" style="z-index: 9999999"></div>
    <div id="dominio2" style="z-index: 9999999"></div>


    <div id="mdl_prgnts_sgrds" class="modal_preguntas_sugeridas">
        <div id="mdl_pcns_prgnts_sgrds" class="modal_preguntas_opciones">
            <div id="cntndr_mdl_prgnts_sgrds" class="cntndr_mdl_prgnts_sgrds">
                <div id="cbcr_prgnts_sgrds_cntdor" class="cbcr_prgnts_sgrds_cntdor"></div>
                <div id="cbcr_prgnts_sgrds" class="cbcr_prgnts_sgrds"></div>
                <div id="body_prgnts_sgrds" style="height: 100%" class="body_prgnts_sgrds">
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

    <div id="modalCuponSistemaGerenteVoucher">
        <div class="preguntasTitulo"><label id="infoMdalf">Ingreso Cup&oacute;n Sistema
                Gerente</label><img src="../imagenes/admin_resources/btn_eliminar.png"
                                    onclick="fn_cerrarModalCuponesSistemaGerenteVoucher()"
                                    class="btn_cerrar_modal_cupones"/></div>
        <div id="voucherAE"></div>
        <div id="select_cliente"></div>
        <div id="select_tipocupo"></div>
        <div id="botonVolver1" style="display: none">
            <img onclick="pantalla1()" style="margin-top: 3%; width: 10%;" src="../imagenes/volverCupon.png"></img>
        </div>
        <div id="botonVolver2" style="display: none">
            <img onclick="pantalla2()"
                 id="imgPantalla2"
                 style="margin-top: 3%; width: 10%;"
                 src="../imagenes/volverCupon.png"></img>
        </div>
        <div id="voucherAEs">
            <center><input inputmode="none"
                           type="password"
                           name="input_cuponSistemaGerenteAutEXT"
                           onchange="fn_canjearCuponAutomaticoVoucher()"
                           id="input_cuponSistemaGerenteAutEXT"
                           style="height: 35px; width: 454px;"/></center>
        </div>
    </div>

    <div id="modalQRPromociones" title="Leer QR Promoción">
        <div style="text-align:center;">
            <div>
                <h3>Coloca el QR en el lector</h3>
            </div>
            <div>
                <img src="../imagenes/qr.jpg" width="250"/>
            </div>
            <div>
                <input inputmode="none" id="valorQRPromociones" type="password" name="valorQRPromociones"/>
            </div>
        </div>
    </div>

    <!-- Contenedor para integraciones, nuevas funcionalidades -->
    <div id="cntIntegraciones">
        <!-- Modal -->
        <div class="modal"
             id="mdlIntegracion"
             style="display: none"
             tabindex="-1"
             role="dialog"
             aria-labelledby="testLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="testLabel">Promociones</h4>
                    </div>
                    <div class="modal-body">
                        <p class="parrafoPromocion">Ingrese código de la promoción</p><br/>
                        <input inputmode="none" type="text" id="inputParametro" class="codigoPromocion" value=""/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="mdlVenta" class="modal_tipopedido">
        <div id="mdlTipoVenta" class="content__modalMPX">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-headertipo" style="background-color: #0a98bb;">
                        <h4 class="modal-title"
                            style="font-size: 21px;color:white;font-family: Arial;text-align: center; ">TIPO DE
                            PEDIDO</h4>
                    </div>
                    <div class="modal-body" align="center">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <table id="mdlTipoVentaCuerpo"></table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </div>

    <!-- Kiosko y Pickup -->
    <div id="mdlKioskoCodigo" class="modal_cargando" style="display: none">
        <div class="modal-dialog" style="margin: 200px auto;width:400px;">
            <div class="modal-content">
                <div class="modal-headertipo" style="background-color: #2e6e9e;">
                    <h3 id="hRetomarOrden" class="modal-title" style="color:#dfeffc; text-align: center;"></h3>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <p class="parrafoPromocion"><b>Leer código QR de la orden de pedido</b></p>
                    <div>
                        <img src="../imagenes/qr.jpg" width="100"/>
                    </div>
                    <input inputmode="none"
                           id="inpCodigoOrdenPedido"
                           type="input"
                           onchange="leerCodigoOrdenPedido(this.value)"
                           class="codigoPromocion"
                           style="margin-top: 10px;"
                           value=""/>
                </div>
                <div id="keyboard_div"></div>
                <div id="cancel_div" class="modal-footer">
                    <button type="button" id="btn_cancelModal" class="btn btn-secondary" onclick="mostrarModalCodigoKiosko(false)">Cancelar
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Buscador atajo de ordenes kiosko y pickup -->
    <div id="mdlKioskoCodigoBuscador" class="modal_cargando" style="display: none">
        <div class="modal-dialog" style="margin: 200px auto;width:400px;">
            <div class="modal-content">
                <div class="modal-headertipo" style="background-color: #2e6e9e;">
                    <h3 id="hRetomarOrdenBuscador" class="modal-title" style="color:#dfeffc; text-align: center;"></h3>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <p class="parrafoPromocion"><b>Leer código QR de la orden</b></p>
                    <div>
                        <img src="../imagenes/qr.jpg" width="100"/>
                    </div>
                    <input inputmode="none"
                           id="inpCodigoOrdenPedidoBuscador"
                           type="input"
                           onchange="leerCodigoOrdenPedidoBuscador(this.value)"
                           class="codigoPromocion"
                           style="margin-top: 10px;"
                           value=""/>
                </div>
                <div id="keyboard_div_buscador"></div>
                <div id="cancel_div_buscador" class="modal-footer">
                    <button type="button" id="btn_cancelModalBuscador" class="btn btn-secondary" onclick="mostrarModalBuscadorCodigoKioskoPickup(false)">Cancelar
                </div>
            </div>
        </div>
    </div>
    <!-- Kiosko y Pickup -->
    <div id="mdlOpciones" class="modal_cargando" style="display: none">
        <div class="modal-dialog" style="margin: 200px auto; width:600px;">
            <div class="modal-content">
                <div class="modal-headertipo" style="background-color: #2e6e9e;">
                    <p id="hTituloModalKiosko" class="tituloModalKiosko"></p>
                    <p id="hSubtituloModalKiosko" class="subtituloModalKiosko"></p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3" style="margin-left: 137px">
                            <input inputmode="none"
                                   type="button"
                                   class="boton_Opcion"
                                   value="Retomar Orden"
                                   onclick="mostrarModalCodigoKiosko(true)"/>
                            <input inputmode="none"
                                   type="button"
                                   class="boton_Opcion"
                                   value="Reimpresión"
                                   onclick="procesoReimpresion()"/>
                            <input inputmode="none"
                                   type="button"
                                   class="boton_Opcion"
                                   value="Anular Orden"
                                   onclick="confirmarAnularOrdenKiosko()"/>
                            <input inputmode="none"
                                   type="button"
                                   class="boton_Opcion"
                                   value="Transferir Orden"
                                   onclick="transferir_pedido_pickup_orden_pedido()"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="deseleccionarOrdenEfectivo()">
                        Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Locales Transferencia Pickup -->
    <div id="modalLocalesTransferenciaPickup" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading" style="background-color:#337ab7; padding: 30px;">
                            <span id="headerLocales">
                                <h3 style="color: white; font-size:24px;">Selecciona un local</h3>
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
                        <div class="panel-heading" style="background-color:#337ab7; padding: 30px;">
                            <span id="headerLocales">
                                <h3 style="color: white; font-size:24px;">Elije un motivo</h3>
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


    <div id="mdlFidelizacion" class="modal_cargando" style="display: none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Código de Seguridad</h3>
                </div>
                <div class="modal-body">
                    <p class="parrafoPromocion">Leer código de seguridad del cliente.</p><br/>
                    <input inputmode="none"
                           id="inpCodigoSeguridad"
                           type="password"
                           onchange="leerCodigoSeguridad(this.value)"
                           class="codigoPromocion"
                           value=""/>
                </div>
                <div style="height: 90px">
                    <button type="button" class="boton_Opcion" onclick="fn_modalFidelizacion(false)">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VITALITY   -->
    <div class="container" id="ingresoVitality" style="display: none">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="marco">
                    <div id="tqtIngresoVT" class="titulo">LEA CODIGO DE SEGURIDAD
                        <p style="font-size: 0.6em;" class="mensajePequeno">Lectura de código de seguridad.</p>
                    </div>
                    <div class="botones">
                        <input inputmode="none"
                               type="password"
                               class="text_codigoVitality"
                               id="txtCodigoVitality"
                               placeholder="Código QR"/>
                    </div>
                    <div class="botones">
                        <input inputmode="none"
                               type="button"
                               class="button_fdzn"
                               onclick="flujo_seguimiento('pre1', 'ingresoVitality', true)"
                               value="Cancelar"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

    <!-- LEER PROMOCION -->

    <div id="mdlLeerPromocion" class="modal_cargando" style="display: none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Lectura de promoción</h3>
                </div>
                <div class="modal-body">
                    <p class="parrafoPromocion">Lea el código QR o escriba el código ubicado debajo del mismo.</p><br/>
                                        <input inputmode="none"
                                    type="text"
                                    name="cc_month"
                                    id="inpCodigoPromocion"
                                    value=""
                                    class="input"
                                    onclick="pay_numericoCliente('#inpCodigoPromocion');"
                                    onkeydown="odp_ValidarCodigoPromocion(event, <?php echo $restaurant_valida_email; ?>)"
                                    placeholder=""
                                    minlength="fn_numerico10"
                                    maxlength="13"
                                    required=""/>
                </div>

                <div style="height: 90px">
                    <button type="button" class="boton_Opcion" onclick="fn_modalLeerPromocion(false)">Cancelar</button>
                    <button type="button" class="boton_Opcion" onclick="pay_numericoCliente('#inpCodigoPromocion')">Teclado</button>
                </div>

                <div class="teclado_clientes">
                    <div class="numPad"></div>
                    <div class="txtPad"></div>
                    <div class="dominio5" style="z-index: 9999999"></div>
                    <div class="keyboard"></div>
                    <div class="dominio6" style="z-index: 9999999"></div>
                </div>

            </div>
        </div>
    </div>

    <div id="tecladoClientes"></div>

    <!-- MODAL QR CODIGO APPEDIR -->
    <div id="mdlQrCodigoAppedir" class="modal_cargando" style="display: none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Código Qr</h3>
                </div>
                <div class="modal-body">
					<p class="parrafoPromocion">Lea el código QR o escriba el código ubicado debajo del mismo.</p><br/>
                    <input inputmode="none"
                            type="text"
                            name="cc_month"
                            id="inpCodigoQrAppedirNew"
                            value=""
                            class="input"
                            placeholder=""
                            onkeydown="odp_okQrAppedir(event)"
                            minlength="1"
                            maxlength="<?= $politicaAppedir->limiteCaracter?>"
                            required=""/>
                    <!--<div class="botones">
                        <p class="parrafoPromocion">Leer código Qr del cliente.</p><br />
                        <input inputmode="none" title="Codigo QR" placeholder="****" style="text-align:center"
                        id="inpCodigoQrAppedir" autofocus type="text" class="codigoPromocion" value="" />
                    </div>-->

                </div>
                <div style="height: 90px">
                    <button type="button" class="btn-danger boton_Opcion" onclick="fn_modalQrCodigoAppedir(false)">Cancelar</button>
                    <button type="button" class="btn-primary boton_Opcion" onclick="pay_numericoCliente('#inpCodigoQrAppedirNew')">Ingreso manual</button>
                </div>

                <div class="teclado_clientes">
                    <div class="numPad"></div>
                </div>
                <!-- TECLADO QR 
                <div class="teclado-appedir" style="display: none">
                    <center>
                        <table style="margin-top: 20px; margin-bottom: 20px;">
                            <tr>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('7')">7</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('8')">8</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('9')">9</button>
                                </td>
                                <td>
                                    <button id="ok_cantidad" class="btnVirtualBorrar" onclick="fn_okQrAppedir()">OK</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('4')">4</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('5')">5</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('6')">6</button>
                                </td>
                                <td>
                                    <button class="btnVirtualBorrar" onclick="fn_eliminarCantidadQrAppedir()">&larr;</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('1')">1</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('2')">2</button>
                                </td>
                                <td>
                                    <button class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('3')">3</button>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button id="btn_cantidad_cero_qr" class="btnVirtual" onclick="fn_agregarNumeroQrAppedir('0')">0</button>
                                </td>
                                <td colspan="3">
                                    <button class="btnVirtualCancelar" id="btn_punto" onclick="fn_cancelarAgregarCantidadQrAppedir()" style="width: 200px;">Cancelar </button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </div>-->
            </div>
        </div>
    </div>

    <div class="modal modal-payphone" id="ModalRegistroDatosDomicilio" style="display: none">
        <div class="modal__container">
            <div class="modal__content">
                <ul class="form-list">
                    <input inputmode="none" type="hidden" value="<?php if (isset($_SESSION['acepta_beneficio'])){ echo $_SESSION['acepta_beneficio']; } ?>" id="acepta_beneficio">
                    <li class="text-center botones" id="botones_cabecera">
                        <input inputmode="none" type="hidden" id="validacion" value="cedula">
                        <button type="button" id="cedula_validar" class="button active">Cedula</button>
                        <button type="button" id="pasaporte_validar" class="button" onclick="fn_solicitaCredencialesAdministrador()">Pasaporte</button>
                    </li>
                    <li id="botones_domicilio" style="justify-content: space-between;">
                        <button id="btn_salirOrden" class="otro_documento" onclick="pedirDatosDocumento()" id="closeModal" title="Retomar Orden"></button>
                        <button type="button" onclick="fn_activarEsDomicilio();" id="es_domicilio" class="button">Domicilio</button>
                    </li>
                    <li id="datosClientePayphone" class="primero">
                        <ul>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline">
                                    <label> Documento:</label>
                                    <input inputmode="none"
                                           type="text"
                                           name="cc_month"
                                           id="pay_txtCedulaCliente"
                                           value=""
                                           class="input"
                                           desc="Cédula/Ruc"
                                           onclick="pay_numericoCliente('#pay_txtCedulaCliente', <?php echo $restaurant_valida_email; ?>);"
                                           onkeydown="odp_buscarCliente(event, <?php echo $restaurant_valida_email; ?>)"
                                           placeholder=""
                                           minlength="fn_numerico10"
                                           maxlength="13"
                                           required=""
                                           readonly/>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li id="datosClientePayphone" class="segundo">
                        <ul>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline">
                                    <label>Nombres <span class="req">(*)</span></label>
                                    <input inputmode="none"
                                           class="input"
                                           id="pay_txtNombres"
                                           onclick="pay_alfaNumericoCliente('#pay_txtNombres');"
                                           desc="Nombres"
                                           type="text"
                                           name=""/>
                                </div>
                            </li>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline">
                                    <label> Teléfono: <span class="req">(*)</span></label>
                                    <input inputmode="none"
                                           type="text"
                                           name="cc_year"
                                           id="pay_txtTelefono"
                                           class="input"
                                           desc="Teléfono"
                                           onclick="pay_numericoCliente('#pay_txtTelefono');"
                                           placeholder=""
                                           minlength="4"
                                           maxlength="12"/>
                                </div>
                            </li>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline">
                                    <label>Email <span class="req">(*)</span></label>
                                    <input inputmode="none"
                                           class="input"
                                           id="pay_txtCorreo"
                                           onclick="pay_alfaNumericoCliente('#pay_txtCorreo');"
                                           onkeydown="odp_validaEmail(event, <?php echo $restaurant_valida_email; ?>)"
                                           desc="Email"
                                           type="email"
                                           name=""/>
                                </div>
                            </li>
                            <input inputmode="none" type="hidden" id="aplica_beneficio" value="0">
                        </ul>   
                    </li>
                    <li class="form-list__row padding_top" id="datosDomicilio">
                        <ul>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline1">
                                    <label class="ajustar" style="max-width: 100px;"> Calle Principal</label>
                                    <input inputmode="none"
                                           type="text"
                                           name="cc_month"
                                           desc="Calle Principal"
                                           id="pay_txtDireccion"
                                           onclick="pay_alfaNumericoCliente('#pay_txtDireccion');"
                                           onchange="fn_quitar_coordendas();"
                                           class="input ajustar"
                                           placeholder=""
                                           minlength="2"
                                           maxlength="400"/>
                                    <label lass="ajustar"> #</label>
                                    <input inputmode="none"
                                           type="text"
                                           name="cc_year"
                                           desc="Número Calle"
                                           id="pay_numeroCallePrincipal"
                                           onclick="pay_alfaNumericoCliente('#pay_numeroCallePrincipal');"
                                           style="width: 100px;"
                                           class="input ajustar"
                                           placeholder=""
                                           minlength="2"
                                           maxlength="100"/>
                                </div>
                            </li>
                            <li class="form-list__row padding_top">
                                <label>Calle secundaria</label>
                                <input inputmode="none"
                                       class="input"
                                       desc="Calle Secundaria"
                                       onclick="pay_alfaNumericoCliente('#pay_calleSecundaria');"
                                       id="pay_calleSecundaria"
                                       type="text"
                                       name=""/>
                            </li>
                            <li class="form-list__row padding_top">
                                <div class="form-list__input-inline1">
                                    <label class="ajustar" style="max-width: 100px;">Tipo Inmueble</label>
                                    <select id="selectTiposInmuebles" name="select" style="width: 200px;"></select>
                                    <label lass="ajustar"> #</label>
                                    <input inputmode="none"
                                           type="text"
                                           id="pay_referenciaTipoInmueble"
                                           onclick="pay_alfaNumericoCliente('#pay_referenciaTipoInmueble');"
                                           desc="Tipo Inmueble"
                                           name="cc_year"
                                           style="width: 100px;"
                                           class="input ajustar"
                                           placeholder=""
                                           minlength="2"
                                           maxlength="10"
                                           required=""/>
                                </div>
                            </li>
                            <li class="form-list__row">
                                <label>Referencia / Observación </label>
                                <textarea id="pay_referencia"
                                          onclick="pay_alfaNumericoCliente('#pay_referencia');"
                                          desc="Referencia"
                                          class="input"
                                          rows="1"
                                          cols="50"></textarea>
                                <!--<input class="input" type="text" name="" required="" />-->
                            </li>
                        </ul>
                    </li>
                    <li class="text-center botones align-center justify-content-center" id="botones_primeros">
                        <button type="submit" onclick="fn_buscarCliente('#pay_txtCedulaCliente', <?php echo $restaurant_valida_email; ?>);"
                                id="btn_consultar"
                                class="button">Consultar</button>
                        <button type="button"
                                id="btn_consumidor"
                                onclick="cancelarPayPhone()"
                                style="background-color: rgb(193, 34, 34);"
                                class="button">Consumidor Final</button>
                    </li>
                    <li class="text-center botones align-center justify-content-center" id="botones_segundos">
                        <button type="submit" onclick="modalAgregaBeneficio(true);"
                                id="btn_beneficio"
                                class="button">Agregar Beneficio</button>
                        <button type="submit" onclick="ejecutarOpcionesGuardar();"
                                id="btn_opcionesGuardar"
                                style="background-color: rgb(193, 34, 34);"
                                class="button">Aceptar</button>
                    </li>
                    <div class="teclado_clientes">
                        <div class="numPad"></div>
                        <div class="txtPad"></div>
                        <div class="dominio5" style="z-index: 9999999"></div>
                        <div class="keyboard"></div>
                        <div class="dominio6" style="z-index: 9999999"></div>
                    </div>
                </ul>

            </div> <!-- END: .modal__content -->
        </div> <!-- END: .modal__container -->
    </div>

    <!-- Modal verificacion agregar beneficio cliente-->
    <div class="modal modal-payphone" id="modalverificacionagregarbeneficio" style="display: none">
        <div class="modal__container">
            <div class="modal__content">
                <div class="contenido">
                    <div class="preguntasTitulo">
                        <label>Escanea el codigo de barras que se encuentra en la c&eacute;dula</label>
                        <img src="../imagenes/admin_resources/btn_eliminar.png" onclick="cerraModalAgregaBeneficio()" class="btn_cerrar_modal_cupones"/>
                    </div>
                    <div id="cnt_tp_nv_cnj_cpn" class="cnt_tp_nv_cnj_cpn">
                        <div class="preguntasBotonPlu">
                            <input inputmode="none" id="btn_cl_ben_1" class="respuestaPregunta" type="checkbox" value="0" checked="checked" disabled="disabled"/>
                            <label onclick="fn_procesoCanjearAutomaticoCedula()" for="btn_cl_ben_1">
                                <p>Autom&aacute;tico</p>
                            </label>
                        </div>
                        <div class="preguntasBotonPlu">
                            <input inputmode="none" id="btn_cl_ben_2" class="respuestaPregunta" type="checkbox" value="1" disabled="disabled"/>
                            <label onclick="fn_procesoCanjearManualCedula()" for="btn_cl_ben_2">
                                <p>Manual</p>
                            </label>
                        </div>
                    </div>
                    <div id="aut_frm_cnj_cpn_lnd">
                        <input inputmode="none" type="tel" name="input_cedulaBeneficio" onkeydown="odp_buscarClienteBeneficio(event)" id="input_cedulaBeneficio"/>
                    </div>
                    <div id="teclado_clientes">
                        <div id="numPad2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--INICIO MODAL DE CREDENCIALES ADMINISTRADOR -->
    <div id="div_adminPasaporte" title="Ingrese las Credenciales del Administrador"  align="center" style="display: none;">
        <div class="anulacionesSeparador">
            <div class="anulacionesInput" align="center">
                <input inputmode="none"  type="password" id="txt_passPasaporte" style="height: 40px; width: 454px; font-size: 16px;"/>
            </div>
        </div>
        <table id="tabla_credencialesAdmin" align="center">
            <tr>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 7)">7</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 8)">8</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 9)">9</button></td>
                <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarNumero(txt_passPasaporte);">&larr;</button></td>
            </tr>
            <tr>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 4)">4</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 5)">5</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 6)">6</button></td>
                <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarTodo(txt_passPasaporte);">&lArr;</button></td>
            </tr>
            <tr>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 1)">1</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 2)">2</button></td>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 3)">3</button></td>
                <td><button style="font-size:45px;" class='btnVirtualOKpq' id="fn_okPasaporte" onclick="fn_okPasaporte();">OK</button></td>
            </tr>
            <tr>
                <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(txt_passPasaporte, 0)">0</button></td>
                <td colspan="4"><button style="font-size:45px;" class='btnVirtualCancelar' onclick="fn_canPasaporte();">Cancelar</button></td>
            </tr>
        </table>
    </div>
    <!--FIN MODAL DE CREDENCIALES ADMINISTRADOR -->

    <!-- Nombrar picada -->
    <div class="modal" id="modalNombrarPicada" tabindex="-1" aria-labelledby="modalNombrarPicadaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-nombrarPicada">
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <h5><label for="nombrePicada" class="col-form-label"><strong>Introduzca el nombre de la picada:</strong></label></h5>
                            <textarea class="form-control" id="nombrePicada"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="nombrarPicada('nombrar')">Confirmar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin nombrar picada -->

    <input inputmode="none" type="hidden" id="Tarjeta" name="Tarjeta" value="0"/>
    <!--
            <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase-app.js"></script>
            <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase-auth.js"></script>
            <script src="https://www.gstatic.com/firebasejs/4.3.0/firebase-database.js"></script>-->
    <!-- Librerias JavaScript -->
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>-->
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../kds/ajax_kds.js"></script>
    <script type="text/javascript" src="../js/cndjs/cloudflare/ajax/libs/dayjs/1.10.7/dayjs.min.js"></script>
    <!-- Scripts para scroll-->
    <script type="text/javascript" src="../js/mousewheel.js"></script>
    <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>

    <!-- Swiper -->
    <script type="text/javascript" src="../js/swiper.js"></script>

    <!-- Scripts para alertas-->
    <script type="text/javascript" src="../js/alertify.js"></script>
    <!-- Script para Notificaciones de tipo TippyJS -->
    <script type="text/javascript" src="../js/unpkg/popper.min.js"></script>
    <script type="text/javascript" src="../js/unpkg/tippy-bundle.umd.min.js"></script>
    <!-- Scripts para keyboard -->
    <script type="text/javascript" src="../js/teclado.js"></script>
    <!-- Toma Pedido -->
    <script type="text/javascript" src="../js/ajax_ordenPedido.js"></script>
    <script type="text/javascript" src="../js/ajax_bloquear_acceso.js"></script>
    <script type="text/javascript" src="../js/cnd/jsdelivr/net/npm/sweetalert2@9.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>-->
    <!--<script type="text/javascript" src="../bootstrap/js/bootstrap.js"></script>-->
    <script type="text/javascript" src="../bootstrap/v5/js/bootstrap.bundle.min.js"></script>
    <?php include 'ordenPedidoProductosUpSelling.php'; ?>
    <!--<script type="text/javascript" src="../js/ajax_ordePedidoProductosUpSelling.js"></script>-->
    <!--<script type="text/javascript" src="../js/ajax_ordePedidoProductosUpSelling.js"></script>-->
    <!--<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>-->   <!-- Ya no existe este javacscript --> 
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRK7EfU4FD92RfejpAbjOondcBCgYwKL4&callback=initAutocomplete&libraries=places&v=weekly" type="text/javascript" defer></script> -->
    <script type="text/javascript" src="../js/jquery.smartWizard.min.js"></script>
    <script type="text/javascript" src="../js/ajax_promociones.js"></script>
    <script src="../js/socket.io.min_4_4_1.js"></script>
    <script type="text/javascript" src="../js/socketClient.js"></script>
    <!-- <script type="text/javascript" src="../js/mapaOperador.js"></script> -->
    <script type="text/javascript" src="../js/tomaPedidoPickup_ext.js"></script>

    <script type="text/javascript" src="../js/ordenPedido/promociones/promociones.js"></script>
    <script type="text/javascript" src="../js/validacionCodigoQR.js"></script>
    <script type="text/javascript" src="../js/ajax_payphoneEventosOrdenPedido.js"></script>
    <script type="text/javascript" src="../js/tecladosDinamicos.js"></script>
    <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
    <script type="text/javascript" src="../js/TecladoCredencialesAdministrador.js"></script>
    <script src="../js/cnd/jsdelivr/net/npm/sweetalert2@11.js"></script>
    <script src="../js/ajax_statusVersion.js"></script>
	<script type="text/javascript" src="../js/kds.js"></script>
    <script type="text/javascript" src="../js/ajax_campanaSolidaria.js"></script>
    <script>
       
        
       $( window ).on( "load", function() {
            var aplicaActualizacion = getActualizacionPendiente();
            if (aplicaActualizacion){
                buildAlertActualizacion();
                return;
            }
            $( "#contenedorComentario" ).parent( ".ui-dialog" ).removeAttr("style");        
            $( "#contenedorComentario" ).parent( ".ui-dialog" ).attr("style", "position: absolute; height: auto;   width: 500px;   top: 0;   left: 303px;   display: block;");
        
            $(window).resize(function() {
            
        });
        
        $( "#contenedorComentario" ).removeClass( "ui-dialog-content" )

        })
        $("#comentar").click(function(){
            $( "#contenedorComentario" ).parent( ".ui-dialog" ).removeAttr("style");        
            $( "#contenedorComentario" ).parent( ".ui-dialog" ).attr("style", "position: absolute; height: auto;   width: 500px;   top: 0;   left: 303px;   display: block;");       
            $( "#contenedorComentario" ).removeClass( "ui-dialog-content" )
        }); 
 
        $(document).on('click','.producto_activo', function(){
            var bodyheight = $(window).height();
            let newHeight =parseInt(bodyheight)-40;       
            if(newHeight < 800) {
                $("#body_prgnts_sgrds").attr("style", "height:"+newHeight+"px !important")  
            } else {
                $("#body_prgnts_sgrds").attr("style", "height: 724px !important") 
            }
        });

        $("#btn_cuponesSG").click(function(){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        });
        var position = $("#numPad").offset();
        $(document).on('click','.btnVirtual', function(e){      
            $('#comentario').trigger('blur');
            $('#txt_busca').trigger('blur');
        });
        $(document).on('click','#buscar', function(e){  
            $('#cuadro_buscador').removeClass('ui-dialog-content ')    
        });

        $(document).ready(function(){
            jQuery(function(){
                $( "#contenedorComentario" ).removeClass( "ui-dialog-content" );                
            });
            
        });
        $(document).on('click','#comentar', function(e){  
            $('#contenedorComentario').removeClass('ui-dialog-content ')    
        });
        $(window).load(function() {
            fn_cerrarModalComentar()
        });
    </script>
    </body>

    </html>
<?php
/*
 * Función definida en el archivo sincronizacionCupones.php, para
 * disparar el proceso de sincronización de los canjes de cupones
 * tabla centralizada en el servidor MasterData
 */
// $conexionTienda = $conexionDinamica->conexionTienda();
// $promocionesControllerObj = new PromocionesController($conexionTienda);
// $promocionesControllerObj->sincronizarCanjesPromociones();
?>