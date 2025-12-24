<?php
session_start();
if (!isset($_SESSION['validado'])) {
    // en caso de no existir sesión iniciada, se destruye toda información
    include_once '../seguridades/seguridad.inc';
} else {
    include_once "../system/conexion/clase_sql.php";
    include_once "../clases/clase_seguridades.php";
    include_once "../clases/clase_facturacion.php";
    include_once "../clases/clase_progresoNiv.php";
    include_once "../clases/clase_ordenPedido.php";

    $lc_ip = $_SESSION["direccionIp"];
    $lc_UsuarioId = $_SESSION['usuarioId'];
    $lc_perfilUsuario = $_SESSION['perfil'];
    $lc_rst = $_SESSION['rstId'];
    $lc_codigoRst = $_SESSION['rstCodigoTienda'];
    $lc_nombreRst = $_SESSION['rstNombre'];
    $lc_cdnId = $_SESSION['cadenaId'];
    $lc_logo = $_SESSION['logo'];
    //$lc_ordenPedidoId = $_POST['odp_id'];
    //$lc_numCuenta = $_POST['dop_cuenta'];
    //$lc_numMesa = $_POST['mesa_id'];
    $lc_ordenPedidoId = (htmlspecialchars(isset($_POST['odp_id'])) && !empty($_POST['odp_id'])) ? htmlspecialchars($_POST['odp_id']) : null;
    $lc_numCuenta = (htmlspecialchars(isset($_POST['dop_cuenta'])) && !empty($_POST['dop_cuenta'])) ? htmlspecialchars($_POST['dop_cuenta']) : null;
    $lc_numMesa = (htmlspecialchars(isset($_POST['mesa_id'])) && !empty($_POST['mesa_id'])) ? htmlspecialchars($_POST['mesa_id']) : null;
    $lc_tipoServicio = $_SESSION['TipoServicio'];
    $lc_simbolomodeda = $_SESSION['simboloMoneda'];
    $nombre = $_SESSION['nombre'];
    $ValidacionRucCodigo = trim($_SESSION['ValidacionRucCodigo']);
    $ValidacionErrorRUC = trim($_SESSION['ValidacionErrorRUC']);
    $HabilitarValidacionRUC = trim($_SESSION['HabilitarValidacionRUC']);
    $ValidacionRUCintento = trim($_SESSION['ValidacionRUCintento']);
    $ValidacionRUCdirecto = trim($_SESSION['ValidacionRUCdirecto']);
    $ValidacionRUCdirectoN = trim($_SESSION['ValidacionRUCdirectoN']);
    $seguridades = new seguridades();
    $tomaPedido = new menuPedido();
    $_SESSION['cargoPisoArea'] = "Si";
	$ordenKiosko = (isset($_SESSION['kioskoActivo'])) ? $_SESSION['kioskoActivo'] : 0;
    // TODO: Seteo de reimpresión de orden de pedido en caja por política
	$reimpresionKiosko = (isset($_SESSION['reimpresionKiosko'])) ? $_SESSION['reimpresionKiosko'] : 0;
	
	$pickupActivo = (isset($_SESSION["pickupActivo"])) ? $_SESSION["pickupActivo"] : 0;

    $objFactura = new facturas();
    //Dependiendo del valor configurado en la política Restaurante - DESCUENTOS:SOLICITAR CLAVE (Booleano en 0 o 1)
    //se modifica el comportamiento de los botones para ingreso de descuentos
    $resultadoValidacionSeguridadBotonDescuentosFactura = $objFactura->fn_validarPoliticaDescuentosSeguridad(array("Factura", $lc_cdnId, $lc_rst));
    $requiereSeguridadBotonDescuentosFactura = $resultadoValidacionSeguridadBotonDescuentosFactura["str"];
    $funcionJSBotonDescuentosFactura = (0 === $requiereSeguridadBotonDescuentosFactura) ? "fn_listaDescuentos();" : "fn_dialogCredenciales1(1)";

    $resultadoValidacionSeguridadBotonDescuentosProducto = $objFactura->fn_validarPoliticaDescuentosSeguridad(array("Producto", $lc_cdnId, $lc_rst));
    $requiereSeguridadBotonDescuentosProducto = $resultadoValidacionSeguridadBotonDescuentosProducto["str"];
    $funcionJSBotonDescuentosProducto = (0 === $requiereSeguridadBotonDescuentosProducto) ? "fn_listaDescuentosDiscrecionales();" : "fn_dialogCredenciales1(3);";
    
    //validar que el restaurant tiene activa la politica para validar email con plugthem
    $resultado_verificar_email_plug = $tomaPedido->fn_consulta_generica_escalar("EXEC dbo.verificarPoliticaValidaEmailPlugthemSimplified '%s'", $lc_rst);
    $restaurant_valida_email = isset($resultado_verificar_email_plug["Activo"]) ? $resultado_verificar_email_plug["Activo"] : 0;

    if (htmlspecialchars(isset($_POST['vae_IDCliente']))) {
        $tipov_id = $_POST['tipov_id'];
        $vae_cod = $_POST['vae_cod'];
        $vae_IDCliente = $_POST['vae_IDCliente'];

        $cli_direccion = $_POST['hide_cli_direccion'];
        $cli_documento = $_POST['hide_cli_documento'];
        $cli_email = $_POST['hide_cli_email'];
        $cli_nombres = $_POST['hide_cli_nombres'];
        $cli_telefono = $_POST['hide_cli_telefono'];
        $montoCupon = $_POST['hide_montoCupon'];
        $esVoucher = $_POST['esVoucher'];
    }

    $fidelizacionActiva = (htmlspecialchars(isset($_SESSION['fidelizacionActiva']))) ? htmlspecialchars($_SESSION['fidelizacionActiva']) : 0;
    $vitality = (isset($_SESSION['vitality'])) ? $_SESSION['vitality'] : 0;

    $codigo_app;
    $documento_app = '';
    $codigoAppActivo = 0;

    if ( htmlspecialchars(isset($_POST["codigo_app"])) ) {
        $codigo_app = $_POST["codigo_app"];
        if( $codigo_app!='' ){
            $codigoAppActivo = 1;
            $respuesta = json_decode($objFactura->fn_consultarPedidoApp($codigo_app));
            $_SESSION['codigoAppActivo'] = 1;
            $_SESSION['fdznDocumento'] = $respuesta->identificacion_cliente;
            $_SESSION['fdznNombres'] = $respuesta->nombres_cliente;
            $fdznNombre = $_SESSION['fdznNombres'];
        }
    }

    $codigoAppActivo = (isset($_SESSION['codigoAppActivo'])) ? $_SESSION['codigoAppActivo'] : 0;
    $documento_app = (isset($_SESSION['codigoAppActivo'])) ? $_SESSION['fdznDocumento'] : 0;

    if ($fidelizacionActiva == 1) {
        $saldo = (isset($_SESSION['fb_money'])) ? $_SESSION['fb_money'] : 0;
        if (isset($_SESSION['fdznDocumento'])) {
            $fdznDocumento = $_SESSION['fdznDocumento'];
            $fdznNombre = $_SESSION['fb_name'];
        } else {
            $fdznDocumento = "0";
            $fdznNombre = "";
        }
    } else {
        $_SESSION['fdznDocumento'] = null;
        $fdznDocumento = "0";
        $fdznNombre = "";
        $saldo = 0;
    }

    if (!isset($_SESSION["turneroActivo"]) || !isset($_SESSION["turneroURl"])) {
        try {
            $response = $objFactura->fn_configuracionTurnero($lc_cdnId, $lc_rst);
            $_SESSION["turneroActivo"] = $response['Respueseta']['activo'];
            $_SESSION["turneroURl"] = $response['Respueseta']['url'];
        } catch (Exception $e) {
            return $e;
        }
    }


    if(!isset($SESSION["habilitadoPorEstacion"])){
        try{
            $responseEnable =  $objFactura->fn_turneroHabilitadoPorEstacion($_SESSION['estacionId']);
            $_SESSION["habilitadoPorEstacion"] = $responseEnable['Respuesta']['habilitado'];
        }catch (Exception $e){
            return $e;
        }
    }

    $vitality = (isset($_SESSION['vitality'])) ? $_SESSION['vitality'] : 0;
    $idClienteVitality = ($vitality == 1) ? $_SESSION['idClienteVitality'] : "";
    $balanceVitality = ($vitality == 1) ? $_SESSION['balanceVitality'] : 0;
    $numeroDocumentoC = ($vitality == 1) ? $_SESSION['documentNumber'] : "";
    $codigoQRVitality = ($vitality == 1) ? $_SESSION['codigoQRVitality'] : "";
    $tokenSeguridadVitality = ($vitality == 1) ? $_SESSION['tokenSeguridadVitality'] : "";
    $NombreClienteVitality = ($vitality == 1) ? $_SESSION['legalNameV'] : "";
    $addressVitality = ($vitality == 1) ? $_SESSION['addressV'] : "";
    $phoneNumberVitality = ($vitality == 1) ? $_SESSION['phoneNumberV'] : "";

    ?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Facturaci&oacute;n</title>
            <link rel="stylesheet" type="text/css" href="../css/style_clientes.css"/>
            <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
            <link rel="stylesheet" type="text/css" href="../css/teclado_facturacion.css"/>      
            <link rel="stylesheet" type="text/css" href="../css/style_factura.css" />
            <link rel="stylesheet" type="text/css" href="../css/est_botones.css" />    
            <link rel="stylesheet" type="text/css" href="../css/est_pantallas.css" />
            <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
            <link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />
            <link rel="StyleSheet" type="text/css" href="../css/style_tomaPedido.css"/>
            <link rel="StyleSheet" href="../css/tomaPedido.css" type="text/css"/>
            <link rel="StyleSheet" type="text/css" href="../css/est_botonesbarra.css"/>  
            <!-- Scripts para scroll-->
            <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
            <!--<link rel="stylesheet" type="text/css" href="../css/jquery.jb.shortscroll.css" />-->
            <link rel="stylesheet" type="text/css" href="../css/style_ver_factura.css"/>
            <link rel="stylesheet" type="text/css" href="../css/interfaceger.css"/>
            <link rel="stylesheet" type="text/css" href="../css/modalLecturaCuponMultimarca.css"/>

            <!-- Estilos para keyboard credenciales -->
            <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>       
            <link rel="stylesheet" type="text/css" href="../js/asset/sweetalert2-9/sweetalert2.css" />

            <!-- Estilos para modal campaña solidaria -->
            <link rel="StyleSheet" type="text/css" href="../css/style_campanaSolidaria.css"/>

            <style type="text/css">                 

                .alertify-cover{opacity: 0;}
                #btnAplicarPago:disabled {
                    background-color: #7bba8b !important;
                }
                .productoDiscrecional:nth-child(odd) {
                    background: #000000;
                }

                #descuentosDiscrecionalesContenedor {
                    height: 440px !important;
                }

                #descuentosContenedor {
                    height: 440px !important;
                }
                 .modal-Deuna {
                    display: none;
                    z-index: 99999;
                    position: fixed;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                }

                .modal-content-Deuna {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: #fff;
                    padding: 20px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }

                .close-button-Deuna {
                    cursor: pointer;
                }

                .boton-cerrar-deuna {
                    height: 50px;
                    width: 250px;
                    background-color: #F66; /* Color de fondo */
                    color: #FFF; /* Texto blanco para mejor contraste */
                    display: block; /* Hace que el botón se comporte como un bloque */
                    margin: 0 auto; /* Centra el botón horizontalmente */
                    border-radius: 25px; /* Hace los bordes completamente redondeados */
                    border: none; /* Elimina bordes por defecto */
                    font-size: 16px; /* Tamaño de texto */
                    cursor: pointer; /* Cambia el cursor a pointer al pasar el mouse */
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* Agrega sombra */
                    transition: all 0.3s ease; /* Transición suave para los efectos */
                    text-align: center; /* Alinea el texto al centro */
                    line-height: 50px; /* Centra verticalmente el texto */
                }

                .boton-esperar-pago-deuna {
                    height: 50px;
                    width: fit-content;
                    background-color:rgb(40, 147, 40); /* Color de fondo */
                    color: #FFF; /* Texto blanco para mejor contraste */
                    display: block; /* Hace que el botón se comporte como un bloque */
                    margin: 0 auto; /* Centra el botón horizontalmente */
                    border-radius: 25px; /* Hace los bordes completamente redondeados */
                    border: none; /* Elimina bordes por defecto */
                    font-size: 16px; /* Tamaño de texto */
                    cursor: pointer; /* Cambia el cursor a pointer al pasar el mouse */
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* Agrega sombra */
                    transition: all 0.3s ease; /* Transición suave para los efectos */
                    text-align: center; /* Alinea el texto al centro */
                    line-height: 50px; /* Centra verticalmente el texto */
                    padding: 0 20px; /* Agrega espacio interno al botón */
                }

                .boton-cancelar-deuna {
                    height: 50px;
                    width: fit-content;
                    background-color: #F66; /* Color de fondo */
                    color: #FFF; /* Texto blanco para mejor contraste */
                    display: block; /* Hace que el botón se comporte como un bloque */
                    margin: 0 auto; /* Centra el botón horizontalmente */
                    border-radius: 25px; /* Hace los bordes completamente redondeados */
                    border: none; /* Elimina bordes por defecto */
                    font-size: 16px; /* Tamaño de texto */
                    cursor: pointer; /* Cambia el cursor a pointer al pasar el mouse */
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* Agrega sombra */
                    transition: all 0.3s ease; /* Transición suave para los efectos */
                    text-align: center; /* Alinea el texto al centro */
                    line-height: 50px; /* Centra verticalmente el texto */
                    padding: 0 20px; /* Agrega espacio interno al botón */
                }

                .boton-esperar-pago-deuna:disabled, .boton-cancelar-deuna:disabled {
                    opacity: 0.39; /* Reduce la opacidad para dar apariencia de deshabilitado */
                    cursor: not-allowed; /* Cambia el cursor para indicar que no es clickeable */
                    box-shadow: none; /* Elimina la sombra */
                }

                .grillaDeUna {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr); /* Define 2 columnas iguales */
                    gap: 10px; /* Espacio entre los elementos */
                    justify-items: end; /* Centra los elementos hacia la derecha */
                }

                .grillaDeUnaModalConfirmar {
                    display: grid;
                    grid-template-columns: repeat(2, auto); /* 2 columnas con ancho según su contenido */
                    gap: 20px; /* Espacio entre los elementos */
                    justify-items: start; /* Alinea los elementos al inicio de la celda horizontalmente */
                    align-items: center; /* Centra los elementos verticalmente */
                }

                .letrasGrisesDeUna {
                    color: #666; /* Color de texto gris */
                }

                .textoJustificadoDeUna {
                    text-align: justify; /* Justifica el texto */
                }

                .progress-circle {
                    position: relative;
                    width: 275px;
                    height: 275px;
                }

                .background-circle {
                    fill: none;
                    stroke: #555;
                    stroke-width: 8;
                }

                .progress-ring-deuna {
                    fill: none;
                    stroke: #00ffcc;
                    stroke-width: 8;
                    stroke-dasharray: 283;
                    stroke-dashoffset: 283;
                    transition: stroke-dashoffset 0.2s linear;
                    stroke-linecap: round;
                }


                #svg_deuna {
                    transform: rotate(-90deg);
                    width: 275px;
                    height: 275px;
                }

                #progressDeUna {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 42px;
                    font-weight: bold;
                    text-align: center;
                }      

            </style>

            <link rel="stylesheet" type="text/css" href="../css/payphoneT.css" />  
            
            <link rel="stylesheet" type="text/css" href="../css/poppins/fonts.poppins.css" />  
        </head>

        <body style="overflow-y: auto;">
           
            <!-- Almacenará la condición en la que se encuentra la facturación de la orden de pedido  -->
            <input inputmode="none" type="hidden" id="condicionFacOrdenPedido"  value=""/>

            <!-- Almacenarán datos de la Forma de Pago Pretendida por una Promesa de Forma de pago: Tarjetas, en estado pendiente.  -->
            <input inputmode="none" type="hidden" id="IDFormapagoPromesaPendiente"  value=""/>
            <input inputmode="none" type="hidden" id="montoPagadoPromesaPendiente"  value=""/>
                    
            <input inputmode="none" type="hidden" id="fdznDocumento"  value="<?php echo htmlspecialchars($fdznDocumento); ?>"/>
            <input inputmode="none" type="hidden" id="fdznNombre"  value="<?php echo htmlspecialchars($fdznNombre); ?>"/>
            
            <input inputmode="none" type="hidden" id="hide_fidelizacionActiva" value="<?php echo htmlspecialchars($fidelizacionActiva); ?>" />
            <input inputmode="none" type="hidden" id="hide_saldo" value="<?php echo htmlspecialchars($saldo); ?>" />
            <input inputmode="none" type="hidden" id="txtDocumentoClientePaypone"  value=""/>
            <input inputmode="none" type="hidden" id="txtclientIdPaypone"  value=""/>
            <input inputmode="none" type="hidden" id="cardNumberSinEnmascarar"  value=""/>
            <input inputmode="none" type="hidden" id="cambio_estados_automatico" value=""/>
            <input inputmode="none" type="hidden" id="url_bringg_crear" value=""/>
            <input inputmode="none" type="hidden" id="nombre_proveedor_por_medio" value=""/>
            <input inputmode="none" type="hidden" id="ValidacionRucCodigo" value="<?php echo htmlspecialchars($ValidacionRucCodigo); ?>"/>
            <input inputmode="none" type="hidden" id="ValidacionErrorRUC" value="<?php echo htmlspecialchars($ValidacionErrorRUC); ?>"/>
            <input inputmode="none" type="hidden" id="HabilitarValidacionRUC" value="<?php echo htmlspecialchars($HabilitarValidacionRUC); ?>"/>
            
            <input inputmode="none" type="hidden" id="ValidacionRUCintento" value="<?php echo htmlspecialchars($ValidacionRUCintento); ?>"/>
            <input inputmode="none" type="hidden" id="ValidacionRUCdirecto" value="<?php echo htmlspecialchars($ValidacionRUCdirecto); ?>"/>
            <input inputmode="none" type="hidden" id="ValidacionRUCNIntentos" value="0"/>
            <input inputmode="none" type="hidden" id="ValidacionRUCdirectoN" value="<?php echo $ValidacionRUCdirectoN; ?>"/>
            <input inputmode="none" type="hidden" id="IDEstacionDeUna" value="<?php echo $_SESSION['estacionId']; ?>"/>
            <input inputmode="none" type="hidden" id="IntervaloConsultaEstadoPagoDeUna" value="3000"/>   
            <input inputmode="none" type="hidden" id="tiempoEsperaDePagoEnSegundos" value="60000"/>
            <input inputmode="none" type="hidden" id="RequestIdDeUna" value="0"/>   
            <input inputmode="none" type="hidden" id="PinCodeDeUnaValue" value=""/>
            <!-- Hiddens para recuperar cedula de cliente app -->
            <input type="hidden" value="<?php if (isset($_SESSION['acepta_beneficio'])){ echo $_SESSION['acepta_beneficio']; } ?>" id="acepta_beneficio">
            <input type="hidden" id="appDocumento"  value="<?php echo htmlspecialchars($documento_app); ?>"/>
            <input type="hidden" id="hide_appDocumentoActivo" value="<?php echo htmlspecialchars($codigoAppActivo); ?>" />

            <!-- Modal Credenciales de Administrador -->
            <div id="credencialesAdmin" title="Ingrese las Credenciales del Administrador" style="display:none;" align="center">
                <div class="anulacionesSeparador">
                    <div class="anulacionesInput">
                        <input inputmode="none"  type="password" id="usr_claveAdmin" style="height: 35px; width: 454px; font-size: 16px;"/>
                    </div>
                </div>
                <table id="tabla_credencialesAdmin" align="center">
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 7)">7</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 8)">8</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 9)">9</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarNumero(usr_claveAdmin);">&larr;</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 4)">4</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 5)">5</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 6)">6</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarTodo(usr_claveAdmin);">&lArr;</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 1)">1</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 2)">2</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 3)">3</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_validaAdmin();">OK</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 0)">0</button></td>
                        <td colspan="4"><button style="font-size:45px;" class='btnVirtualCancelar' onclick="fn_cerrarValidaAdmin();">Cancelar</button></td>           
                    </tr>
                </table>
            </div>

            <div id="numPadAdmin"></div>

            <div align="center" id="nfrmcn_srs_sstm" class="nfrmcn_srs_sstm"  style="height: 38px; width: 350px;" onclick="fn_salirSistema();">
                <img src="../imagenes/admin_resources/icon-user.png"/>
                <div id="nmbr_srs_sstm" style="margin-top: 13px;" class="nmbr_srs_sstm"><?php echo htmlspecialchars($nombre); ?></div>
            </div>

            <div id="rdn_pdd_brr_ccns" class="menu_desplegable" style="display: none;">
                <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                    <input inputmode="none"  type="button" id='btn_descuentos' onclick='<?php echo(htmlspecialchars($funcionJSBotonDescuentosFactura)) ?>'
                           title='Dscto. Factura' class="boton_Opcion_Bloqueado" value="Dscto. Factura"
                           disabled="disabled"/>
                    <input inputmode="none"  type="button" id='btnDescuentoDiscrecional' onclick='<?php echo(htmlspecialchars($funcionJSBotonDescuentosProducto)) ?>'
                           title='Dscto. Productos' class="boton_Opcion_Bloqueado" value="Dscto. Productos"
                           disabled="disabled"/>
                    <input inputmode="none"  type="button" id='btn_eliminar_descuentos' onclick='fn_dialogCredenciales1(2)' title='Eliminar Descuentos' class="boton_Opcion_Bloqueado" value="Eliminar Descuentos" disabled="disabled" />
                    <input inputmode="none"  type="button"  id='btn_propinas'  onclick='fn_abrirModalPropina()' title="Ingresar Propina" class="boton_Opcion_Bloqueado" value="Propina" disabled="disabled" />
                    <input inputmode="none"  type="button" id='btn_sistema'  onclick='fn_salirSistema()' title="Salir del Sistema" class="boton_Opcion"value="Salir Sistema"/>
                </div>
            </div>

            <!--Modal Datos Cliente-->
            <div id="datosFactura" align="center">
                <table align="center" width="900px" border="0">
                    <tr>
                        <td align="left" class="tituloCabecera" style="padding-bottom:10px;">
                            <label id="documento_obligatorios" style="color:#F00" >(*)</label> N&uacute;mero Documento:<br />
                            <input inputmode="none"  type="text" id="txtClienteCI" maxlength="13" onclick="fn_validaTeclado(this);" onchange="fn_clienteBuscar(true, <?php echo htmlspecialchars($restaurant_valida_email); ?>);" style="border-radius:8px; font-size:24px; height:40px;"/>
                        </td>
                        <td>
                            <input inputmode="none"  type="button" class="btnRucCiActivo" id="rdo_ruc" onclick="fn_validaTecladoCedula();" value="CI / RUC" style="margin-left:50px"/>
                            <input inputmode="none"  type="button" class="btnRucCiInactivo" id="rdo_pasaporte" onclick="fn_solicitaCredencialesAdministrador();" value="PASAPORTE"/>
                        </td>
                        <td align="right">
                            <button id="btnClienteGuardar" class="botonesbarra" onclick="fn_clienteGuardar(<?php echo htmlspecialchars($restaurant_valida_email); ?>);">Facturar</button>
                            <button  id="btnClienteGuardarActualiza" class="botonesbarra" onclick="fn_clienteGuardarActualiza();">Facturar</button>
                            <button  id="btnClienteConfirmarDatos" class="botonesbarra" onclick="fn_confirmarDatos(<?php echo htmlspecialchars($restaurant_valida_email); ?>);">Confirmar</button>
                            <button  id="btnClienteConfirmarDatosFacturar" class="botonesbarra" onclick="fn_actualizarRegistroCliente();">Facturar</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" width="50%" class="tituloCabecera" style="padding-bottom:10px;">
                            <label id="nombres_obligatorios" style="color:#F00" >(*)</label> Nombres:<br />
                            <input inputmode="none"  onclick="fn_alfaNumerico_letrass(this); fn_btnOk(numPad);" onchange="fn_focoinput_direccion();" size="30" type="text" id="txtClienteNombre" name="txtClienteNombre" style="border-radius:8px; text-transform:uppercase; width:586px; height:40px;"/>
                        </td>
                        <td align="right"> 
                            <div id="espacioCambio" style="text-padding:10px;margin:5px;font-size:12px;text-align:center;">
                                Su cambio es: 
                                <span style="font-size:32px">$0.00</span>
                            </div>
                            <button class="botonesbarra"  id="btnConsumidorFinal" onclick="fn_consumidorFinal()">Consumidor Final</button>        
                        </td>
                    </tr>
                    <tr>
                        <td align="left" class="tituloCabecera" >Tel&eacute;fono<br/>
                            <input inputmode="none"  onclick="fn_alfaNumerico_numeross(this); fn_btnOk(numPad);" onchange="fn_focoinput_email();" type="text" id="txtClienteFono" name="txtClienteFono" size="20" maxlength="10" style="border-radius:8px; width:250px; height:40px;"/>
                        </td>
                        <td align="left" class="tituloCabecera" >Correo Electr&oacute;nico:<br/>
                            <input inputmode="none" onclick="fn_alfaNumericoo(this); fn_btnOk(numPad);" onchange="fn_validaEmailAPI(<?php echo htmlspecialchars($restaurant_valida_email); ?>);" type="text" id="txtCorreo" name="txtCorreo" size="30" maxlength="50" style="border-radius:8px; width:470px; height:40px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label id="campos_obligatorios" style="color:#F00"> (* Campos Obligatorios) </label>
                        </td>
                    </tr>
                </table>

                <!-- Teclado para digitar numero de documento -->
                <div id="numPad"></div>
                <div id="txtPad"></div>

                <div id="numPadCliente"></div>
                <div id="txtPadCliente"></div>

                <div id="keyboardCliente"></div>
                <div id="dominio1"></div>  
                <div id="dominio2"></div>

                <!--VISUALIZAR FACTURA-->
                <div id="visorFacturas" class="overlay" >
                    <div id="detalleFactura" class="modalfactura">
                        <div id="cabecerafactura" style=" margin: 0; padding: 0; overflow-y: auto;"></div>   		
                        <div>
                            <center>
                                <input inputmode="none"  class="botonesbarra" type="button" id="salirVisor" value="OK" onclick="fn_cerrarVisorFacturas()" style="height: 60px; width: 120px; margin: 5px 0 0 0;" />
                            </center>
                        </div>
                    </div>
                </div><!-- FIN VISUALIZAR FACTURA -->
            </div><!--Modal Datos Cliente-->

            <!--Teclado para ingresar los datos del cliente-->
            <div id="keyboard"></div>

            <div id="contenedors">
                <div id="facturaItems">
                    <div id="listaFactura"></div>
                    <div id="totalesFactura" align="right" style="line-height:0.9">
                        <div id="valoresTotalesFactura" style="margin-top:10px;">        
                            <table id="tblValoresTotales" width='350px' align='left' cellspacing='0'></table>
                        </div>
                        <br/><br/><br/>
                        <div id="formasPagoFactura" align="right" style="line-height:0.9; margin-top:80px;">
                            <table id="formasPago2" width='350px' align='left' cellpading='0' ></table>
                        </div>
                    </div>
                </div> 
                <div id="izquierdo" align="center">
                    <div id="titulo">
                        <b>Billetes</b>
                    </div>
                    <br/>
                    <button class="btnDinero" style="background: no-repeat center center url(../imagenes/signo_mas.png)" id="sumador" title="Sumador de Billetes" onclick="fn_sumarBillete()"></button>
                    <br/>
                    <br/>
                    <div id="lista_billetes"></div>
                    <div id="titulo"></div>
                </div>
                <div id="centro">                    
                    <table class="tbl_total_factura" align="center" border="0" width="100%" cellspacing="0">
                        <tr>
                            <td width="35%"><b>TOTAL:</b></td>
                            <td width="68%">
                                <input inputmode="none"  type="text" id="pagoGranTotal" class="valores" readonly="readonly" size="7"/>
                            <input inputmode="none"  type="hidden" id="valor_total_factura"/>
                            </td>
                        </tr>
                        <tr id="td_falta">
                            <td width="35%"><b>FALTA:</b></td><td width="68%"><input inputmode="none"  type="text" id="pagoTotal" class="valores" readonly="readonly" size="7"/></td>
                        </tr>
                        <tr id="td_cambio">
                            <td>
                                <b>CAMBIO:<b>
                            </td>
                            <td>
                                <input inputmode="none"  type="text" id="valorCambio" class="valores" readonly="readonly" size="7"/>
                            </td>
                        </tr>
                        <tr id="td_pagado">
                            <td>
                                <b>PAGADO:<b>
                            </td>
                            <td>
                                <input inputmode="none"  type="text" id="pagado" class="valores" readonly="readonly" size="7"/>
                            </td>
                                                        </tr>
                                                        </table>
                                                        <table width="90%" border="0" align="center">
                                                            <tr>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '7')">7</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '8')">8</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '9')">9</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '4')">4</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '5')">5</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '6')">6</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '1')">1</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '2')">2</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '3')">3</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '0')">0</button></td>
                                                                <td align="center"><button class='btnVirtualCalculadora' onclick="fn_agregarCaracter(pagado, '.')">.</button></td>
                                                                <td align="center"><button class='btnVirtualBorrarCalculadora' onclick="fn_eliminarNumero(pagado)">&larr;</button></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" align="center"><button class='btnVirtualBorrarCalculadora2' onclick="fn_limpiarCalculadora()">Borrar</button></td>
                                                            </tr>
                                                        </table>
                                                        </div>
                                                        <div id="derecho" align="center">
                                                            <div id="formasPago"></div>
                                                            <br/>
                                                            <button id="btnAplicarPago" onclick="this.disabled=true; fn_diferencia(event);" style="width:170px; height: 100px; position:absolute; top:495px; left:20px; background-color:  #009926; font-size: 22px; border-radius: 13px; color: #FFF;" ></button>
                                                            <!-- <button id="btnCancelarPago" onclick="fn_cancelarPago(event)" style="width:170px; position:absolute; top:695px; left:20px;" disabled="disabled" ></button> -->
                                                            <button id="btnCancelarPago" onclick="fn_cancelarPago(event)" style="width:170px; height: 100px; position:absolute; top:665px; background-color: #FFB1B1; font-size: 15px; left: 20px;font-size: 22px; border-radius: 13px; " disabled="disabled" ></button>
                                                            <br/>
                                                        </div>
                                                        </div>

                                                        <div id="ticketPromedio"></div>

                                                        <div id="barraP" class="cnt_rdn_pdd_btns">
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <button id='btn_salirOrden' class='boton_Accion  regresar_activo' onclick='validaExisteFormaPagoSalir()' title='Retomar Orden'></button>
                                                                    </td>
                                                                    <td>
                                                                        <button id='btn_menuU'  class="boton_Accion" onclick='fn_desplegarMenu()' title="Opciones de men&uacute;">Menu</button>
                                                                    </td>
                                                                    <td>
                                                                        <button id='btn_imprimir' name='Imprimir Pre-Cuenta' servicio="2" disabled="disabled"  class="boton_Accion_Bloqueado" onclick="<?php echo htmlspecialchars("fn_imprimirPreCuenta('$lc_numCuenta')"); ?>" title="Opciones de men&uacute;">Pre Cuenta</button>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                        <div id="pay_TeladocedulaCliente" style="z-index: 999999;"></div>
                                                        <div id="pay_TeladoNombres" style="z-index: 999999;"></div>

                                                        <div id="dominio3" style="z-index: 999999;"></div>
                                                        <div id="dominio4" style="z-index: 999999;"></div>


                                                        <div id="modalDescuentos" title="Descuentos">
                                                            <table align="center"></table>
                                                        </div>

                                                        <div id="aumentarContador">
                                                            <label> Ingrese Cantidad </label>
                                                            <input inputmode="none"  type="text" id="cantidad"/>
                                                            <table>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroD('7')" value=" 7 " >7</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('8')" value=" 8 "  >8</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('9')" value=" 9 " >9</button></td>
                                                                    <td><button id="btn_ok" onclick="fn_ok()">OK</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroD('4')" value=" 4 "  >4</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('5')" value=" 5 "  >5</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('6')" value=" 6 "  >6</button></td>
                                                                    <td><button id="btn_borrar" onclick="fn_eliminarCantidad()" value=" <- " >Borrar</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroD('1')" value=" 1 "  >1</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('2')" value=" 2 "  >2</button></td>
                                                                    <td><button onclick="fn_agregarNumeroD('3')" value=" 3 "  >3</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroD('0')" >0</button></td>
                                                                    <td><button id="btn_punto" onclick="fn_agregarNumeroD('.')">.</button></td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                        <div id="anulacionesContenedor">
                                                            <div class="preguntasTitulo">Ingrese las credenciales del Administrador</div>
                                                            <div class="anulacionesSeparador">
                                                                <div class="anulacionesLabel">Clave:</div>
                                                                <div class="anulacionesInput"><input inputmode="none"  type="password" id="usr_clave" /></div>
                                                            </div>
                                                            <div class="anulacionesSeparadorFin">


                                                                <button id="btn_anulaok" onclick="fn_validar_usuario()">OK</button>


                                                                <button id="btn_anulacancela" onclick="fn_cerrarDialogoUsuarioAdmin()">Cancelar</button>
                                                            </div>
                                                            <table align="left" border="0" align="center">
                                                                <tr>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '7')">7</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '8')">8</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '9')">9</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '4')">4</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '5')">5</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '6')">6</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '1')">1</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '2')">2</button></td>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '3')">3</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center"><button class='btnVirtual' onclick="fn_agregarCaracter2(usr_clave, '0')">0</button></td>
                                                                    <td align="center"><button class='btnVirtualBorrar' onclick="fn_eliminarNumero(usr_clave)">&larr;</button></td>
                                                                </tr>
                                                            </table>
                                                        </div>

            <!-- CREA MODAL DE LISTA DESCUENTOS -->
            <div id="descuentosContenedor" style="overflow-x: hidden; overflow-y: hidden; max-height: 405px;">
                <div></div>
                <div class="descuentoSeparador" style="height: 350px;">
                    <div class="descuentosLabel" style="height: 340px; width: 450px;"><ul id="listadescuento"></ul></div>
                </div>
                <div class="descuentoSeparadorFin" style="width:440px;">
                    <div class="anulacionesSubmit" style="text-align: center;">
                        <button id="btn_anulaok1" onclick="fn_agregarDescuento()">OK</button>
                        <button id="btn_anulacancela" onclick="fn_cerrarDialogoDescuentosContenedor()">Cancelar</button>
                    </div>
                </div>
            </div>
            <!--CREA MODAL PARA DESCUENTOS DISCRECIONALES-->
            <div id="descuentosDiscrecionalesContenedor" style="overflow-x: hidden; overflow-y: hidden; max-height: 405px;">
                <div></div>
                <div class="descuentoSeparador" style="height: 350px;">
                    <div class="descuentosLabel" style="height: 340px; width: 600px;">
    <!--                                <table id="desctDiscrecional" cellspacing="8" style="font-size: 21px; margin-left: 35px;">
                            <thead id="detallesDescuentos">
                                <tr>
                                    <th>Descripción Producto</th>
                                    <th>Descuento</th>
                                </tr>
                            </thead>
                            <tbody id="plusDiscrecional"></tbody>
                        </table>-->
                                                                    <div id="listaDiscrecionales" style="width: 100%; font-size: 21px;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="descuentoSeparadorFin" style="width:590px; height: 60px;">
                                                                <div class="anulacionesSubmit" style="text-align: center;">
                                                                    <button id="btn_anulaok1" onclick="guardarDescuentosDiscrecionales()">OK</button>
                                                                    <button id="btn_anulacancela" onclick="fn_cerrarDialogoDescuentosContenedor()">Cancelar</button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--     CREA MODAL DE CRDENCIALES-->
                                                        <div id="credencialesContenedor" style="max-height: 381px;">
                                                            <div class="credencialesTitulo" align="center">Ingrese Credenciales de Administrador</div>
                                                            <div class="credencialesSeparador">
                                                                <div class="credencialesInput">
                                                                    <input inputmode="none"  type="password" name="usr_clave1" id="usr_clave1" style="height: 35px; width: 454px; font-size: 20px;"/>
                                                                </div>
                                                            </div>
                                                            <div id="numPadCredenciales" align="center" style="left: 17%; font-size: 34px;"></div>
                                                        </div>

                                                        <div id="numPad2"></div>
                                                        <?php fn_espera(1); ?>

                                                        <!-- Lector de Codigo de Barras -->
                                                        <div id="lectorTrama">
                                                            <input inputmode="none"  type="text" name="txt_trama" id="txt_trama" />
                                                        </div>

                                                        <!-- PAD PARA EL INGRESO DE NUMEROS DE SEGURIDAD DE TARJETA - CVV -->
                                                        <div id="div_cvv">
                                                            <label style="font-size:24px;"> Ingrese CVV </label>
                                                            <input inputmode="none"  style="font-size:18px;" type="text" name="txt_cvv" id="txt_cvv" value="" maxlength="4"/>
                                                            <table>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroCVV('7')" value=" 7 " >7</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('8')" value=" 8 "  >8</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('9')" value=" 9 " >9</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroCVV('4')" value=" 4 "  >4</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('5')" value=" 5 "  >5</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('6')" value=" 6 "  >6</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroCVV('1')" value=" 1 "  >1</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('2')" value=" 2 "  >2</button></td>
                                                                    <td><button onclick="fn_agregarNumeroCVV('3')" value=" 3 "  >3</button></td>
                                                                </tr>
                                                                <tr>
                        <td><button style="background-color:#81F781;" id="fn_okCVV" onclick="fn_okCVV(event)">OK</button></td>
                                                                    <td align="center"><button onclick="fn_agregarNumeroCVV('0')" >0</button></td>
                                                                    <td><button id="btn_borrarCVV" onclick="fn_eliminarCantidadCVV()" value=" <- " >Borrar</button></td>
                                                                </tr>
                                                                <tr align="center">
                                                                    <td colspan="3">
                                                                        <button style="background-color:#F66;" id="fn_canCVV" onclick="fn_canCVV()">Cancelar</button>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                        <!-- Contenedor Tipo Cuenta [Ahorro, Credito] -->
                                                        <div id="div_tipoCuentaTarjeta" title="Seleccione el tipo de cuenta..."></div>

                                                        <!-- End: Contenedor Tipo Cuenta [Ahorro, Credito] -->
                                                        <div id="adminCreditoSinCupon">
                                                            <div class="preguntasTitulo">Ingrese las credenciales del Administrador</div>
                                                            <div class="anulacionesSeparador">
                                                                <div class="anulacionesLabel">Clave:</div>
                                                                <div class="anulacionesInput"><input inputmode="none"  type="password" id="usr_claveSinCupon" /></div>
                                                            </div>
                                                            <table>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('7')" value=" 7 " >7</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('8')" value=" 8 "  >8</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('9')" value=" 9 " >9</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('4')" value=" 4 "  >4</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('5')" value=" 5 "  >5</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('6')" value=" 6 "  >6</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('1')" value=" 1 "  >1</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('2')" value=" 2 "  >2</button></td>
                                                                    <td><button onclick="fn_agregarNumeroSinCupon('3')" value=" 3 "  >3</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button style="background-color:#81F781;" id="fn_okSinCupon" onclick="fn_okSinCupon();">OK</button></td>
                                                                    <td align="center"><button onclick="fn_agregarNumeroCVV('0')" >0</button></td>
                                                                    <td><button id="btn_borrarCVV" onclick="fn_eliminarCantidadSinCupon()" value=" <- " >Borrar</button></td>
                                                                </tr>
                                                                <tr align="center">
                                                                    <td colspan="3">
                                                                        <button style="background-color:#F66;" id="fn_canSinCupon" onclick="fn_canSinCupon()">Cancelar</button>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                        <div id="div_adminPasaporte" title="Ingrese las Credenciales del Administrador"  align="center">
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
 
                                                        <!-- Contenedor pago  botones payphone -->
                                                        <div id="modalBotonesPayphone">
                                                            <div id="botonesPayphone">

                                                                <div class="modal tamanio" id="modalbotonesPayphone" style=" display:none; height: 500px;  width: 780px;; top: 267px;left: 523px;"   >
                                                                    <div class="modal__container"  style="margin-bottom: 15px;" id="metodoDirecto">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circleMedios"></div>
                                                                            <img src="../imagenes/facturacion/payphoneLOGO.PNG" class="modal__product" />

                                                                        </div>
                                                                        <div class="modal__contentMedios">
                                                                            <h2>Seleccione Medio de cobro</h2>  <br/>

                                                                            <div id="contenedorBotonesDinamico">
                                                                            </div>

                                                                        </div>

                                                                    </div> <!-- END: .modal__content -->
                                                                </div> <!-- END: .modal__container -->

                                                            </div> <!-- END: .modalPay -->

                                                        </div>





                                                        <div id="modalLink_payphone">
                                                            <div id="botonesPayphone">

                                                                <div class="modal tamanio" id="modalTransaccionLink" style=" display:none; height: 500px;  width: 780px;; top: 267px;left: 523px;"   >
                                                                    <div class="modal__containerAppLink" id="metodoDirecto" style="min-width:  700px;width: 700px ;">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circleMedios"></div>
                                                                            <img src="../imagenes/facturacion/payphoneLOGO.PNG" class="modal__product" />

                                                                        </div>
                                                                        <div class="modal__contentMedios">
                                                                            <h2>Ingrese el correo del cliente.</h2>  <br/> <br/> <br/> 
                                                                            <ul class="form-list">
                                                                                <li class="form-list__row">
                                                                                    <label>Correo</label>
                                                                                    <input inputmode="none"  id="correoLink"  desc="Nombre en la tarjeta" onclick="fn_alfaNumericoCorreo(this);" type="text" name="" required=""  value=""/>
                                                                                </li>   
                                                                                

                                                                                <button type="button"    style="margin-top: 25px;  font-size: 18px;  " class="button" id="btnPagarLinks" style="" onclick="AplicarFormaPagoLinkPayphone()" >Pagar</button>
                                                                                <button type="button"    class="button" style=" font-size: 18px;" id="cancelarPagoLink" onclick="fn_CancelarpayphoneTransaccionLinkPagos()">Cancelar</button>
                                                                                <button type="button"    class="button" style=" font-size: 18px; display: none  " id="btncancelarEsperaCliente" onclick="pararProcesoCorreo()">Cancelar</button>
                                                                                
                                                                                <div class="spinner" id="iconoCargandoLink">
                                                                                    <div class="rect1"></div>
                                                                                    <div class="rect2"></div>
                                                                                    <div class="rect3"></div>
                                                                                    <div class="rect4"></div>
                                                                                    <div class="rect5"></div>
                                                                                </div> 

                                                                                <div id="seccionInformacionTransaccion">
                                                                                    <h3 id="TextoinfoAccion"></h3>
                                                                                    
                                                                                    
                                                                                    
                                                                                    <div id="contenedorCirculo">
                                                                                        <div   class="circulo">
                                                                                            <h2 id="conteo">0000</h2>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>



                                                                            </ul>

                                                                        </div>

                                                                    </div> <!-- END: .modal__content -->
                                                                </div> <!-- END: .modal__container -->

                                                            </div> <!-- END: .modalPay -->

                                                        </div>





                                                        <div id="modalApp_payphone">
                                                            <div id="botonesPayphone">

                                                                <div class="modal tamanio" id="modalTransaccionApp" style=" display:none; height: 500px;  width: 780px;; top: 267px;left: 523px;"   >
                                                                    <div class="modal__containerAppLink" id="metodoDirecto" style="min-width:  700px;width: 700px ;">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circleMedios"></div>
                                                                            <img src="../imagenes/facturacion/payphoneLOGO.PNG" class="modal__product" />

                                                                        </div>
                                                                        <div class="modal__contentMedios">
                                                                            <h2>Codigo país y número de teléfono</h2>  <br/> <br/> <br/> 
                                                                            <ul class="form-list">
                                                                                <li class="form-list__row form-list__row--inline">
                                                                                    <div>
                                                                                        <label>Coc. País</label>

                                                                                        <div class="form-list__input-inline" style="min-width: 100px; width: 110px">
                                                                                            <input inputmode="none"  id="codeCountri" style="width:100px" desc="Fecha expiracion"   onclick="fn_tecladoNumTelefono('#codeCountri');"  value="" type="text" name="cc_month" placeholder=""     required="" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div>
                                                                                        <label>Teléfono</label>
                                                                                        <div class="form-list__input-inline" style="min-width: 100px;   width: 350px">
                                                                                            <input inputmode="none"  id="payTelefonoApp" style="width:250px" desc="Fecha expiracion"   onclick="fn_tecladoNumTelefono('#payTelefonoApp');"  value="" type="text" name="cc_month" placeholder=""     required="" />
                                                                                        </div>
                                                                                    </div>
                                                                                </li>     

                                                                                <button type="button"    style="margin-top: 25px;  font-size: 18px;  " class="button" id="btnPagarApp" style="" onclick="cobrarPagoAppMovilPayphone()" >Pagar</button>
                                                                                <button type="button" class="button" style=" font-size: 18px;" id="btnCancelarApp" onclick="fn_CancelarpayphoneTransaccionAppMovil()">Cancelar</button>
                                                                                <button type="button" class="button" style=" font-size: 18px; display: none;" id="btnfinalizarTransaccion" onclick="fn_finalizarTransaccion()">Cancelar</button>
                                                        

                                                                                <div class="spinner" id="procesandoPago">
                                                                                    <div class="rect1"></div>
                                                                                    <div class="rect2"></div>
                                                                                    <div class="rect3"></div>
                                                                                    <div class="rect4"></div>
                                                                                    <div class="rect5"></div>
                                                                                </div> 

                                                                                
                                                                                
                                                                                <div id="">
                                                                                    <h3 id="lblInfoTransaccion"></h3>
                                                                                </div>
                                                                                
                                                                                
                                                                            </ul>

                                                                        </div>

                                                                    </div> <!-- END: .modal__content -->
                                                                </div> <!-- END: .modal__container -->

                                                            </div> <!-- END: .modalPay -->

                                                        </div>
                                                        <!-- End: Contenedor   botones payphone-->



                                                        <!-- Contenedor pago Paypone -->
                                                        <div id="modalPayphone" title="Payphone::. Complete los campos..">

                                                            <div id="RegistroCliente">

                                                                <div class="modal tamanio modal1" id="modalRegistroCliente" style=" display:none; height: 585px;  width: 677px ; top: 270px;left: 480px;"   >
                                                                    <div class="modal__container_directa" style="width: 100%">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circle registro"></div>
                                                                            <img src="../imagenes/facturacion/mxpBuscarUsuario.PNG" class="modal__product" />

                                                                        </div>
                                                                        <div class="modal__content modal__contentRegistro">
                                                                            <h1>Datos del cliente</h1> <br/>


                                                                            <ul class="form-list">

                                                                                <li class="form-list__row">
                                                                                    <label>Cécula/Ruc(*)</label>
                                                                                    <div  class="creditcard-icon">
                                                                                        <input inputmode="none"  id="pay_txtCedulaCliente" desc="Cédula/Ruc"   type="text" value="" name="cc_number" required=""
                                                                                               onclick="fn_numericoFDZN('#pay_txtCedulaCliente');" 
                                                                                               />
                                                                                    </div>
                                                                                </li>
                                                                                <!-- onclick="fn_tecladoNumTelefono('#pay_txtCedulaCliente');"  -->


                                                                                <div id="pay_datosCliente" style="display:none">

                                                                                    <li class="form-list__row">
                                                                                        <label>Nombres</label>
                                                                                        <div  class="creditcard-icon">
                                                                                            <input inputmode="none"  type="text" desc="Nombres" onclick="fn_alfaNumerico_EscribirNombre('#pay_txtNombres');" id="pay_txtNombres" name="cc_number" value=""    />
                                                                                        </div>
                                                                                    </li>


                                                                                    <li class="form-list__row">
                                                                                        <label>Direccion</label>
                                                                                        <div  class="creditcard-icon">
                                                                                            <input inputmode="none"  type="text" desc="Dirección" onclick="fn_alfaNumerico_EscribirDireccion('#pay_txtDireccion');"  id="pay_txtDireccion" name="cc_number"  />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="form-list__row">
                                                                                        <label>Teléfono(*)</label>
                                                                                        <div  class="creditcard-icon">
                                                                                            <input inputmode="none"  type="text"  desc="Número teléfono"  onclick="fn_tecladoNumTelefono('#pay_txtTelefono');" id="pay_txtTelefono" name="cc_number" value="" required="" />
                                                                                        </div>
                                                                                    </li>

                                                                                    <li class="form-list__row">
                                                                                        <label>Correo Electrónico(*)</label>
                                                                                        <div  class="creditcard-icon">
                                                                                            <input inputmode="none"   style="font-size: 19px;"desc="Correo"  type="text" id="pay_txtCorreo"  onclick="fn_alfaNumericoCorreo(this)" name="cc_number" value="" required="" />
                                                                                        </div>
                                                                                    </li>
                                                                                </div>


                                                                                <button id="paybtn1" type="button" class="button button1" style=" font-size: 18px; display:none" onclick="pay_buscarCliente()">Buscar</button>
                                                                                <button type="button" class="button button1" style=" font-size: 18px; margin-left: 25%;" onclick="pay_cancelarBusquedaRegistroCliente()">Cancelar</button>



                                                                                </li>
                                                                            </ul>

                                                                        </div> <!-- END: .modal__content -->
                                                                    </div> <!-- END: .modal__container -->

                                                                </div> <!-- END: .modalPay -->


                                                            </div>


                                                            <div id="modalPayphone">

                                                                <div class="modal tamanio" id="modalPay" style=" display:none; height: 500px;  width: 690px;; top: 267px;left: 523px;"   >
                                                                    <div class="modal__container" id="metodoDirecto">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circle"></div>
                                                                            <img src="../imagenes/facturacion/payphoneLOGO.PNG" class="modal__product" />

                                                                        </div>
                                                                        <div class="modal__content">
                                                                            <h1>Detalles de su tarjeta</h1> <br/>


                                                                            <ul class="form-list">

                                                                                <li class="form-list__row">
                                                                                    <label>Número de tarjeta</label>
                                                                                    <div class="creditcard-icon">
                                                                                        <input inputmode="none"   id="cardNumber"  onkeypress='return false;' desc="Número Tarjeta"  type="text" name="cc_number" required="" 
                                                                                                value=""
                                                                                                onclick="fn_tecladoNumTelefono('#cardNumber');" 
                                                                                                />
                                                                                    </div>
                                                                                </li>


                                                                                <li class="form-list__row form-list__row--inline">
                                                                                    <div>
                                                                                        <label>Fecha Expiracion</label>
                                                                                        <div class="form-list__input-inline">
                                                                                            <input inputmode="none"  id="expirationMonth" desc="Fecha expiracion"   onclick="fn_tecladoNumTelefono('#expirationMonth');"  value="" type="text" name="cc_month" placeholder="MM"     required="" />
                                                                                            <input inputmode="none"  id="expirationYear"  desc="Año Expiración"   onclick="fn_tecladoNumTelefono('#expirationYear');"   value="" type="text" name="cc_year" placeholder="AA"     required="" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div>
                                                                                        <label>
                                                                                            CVC


                                                                                        </label>
                                                                                        <input inputmode="none"  id="securityCode" desc="Código de seguridad" onclick="fn_tecladoNumTelefono('#securityCode');" type="password" value="" name="cc_cvc" placeholder="###"   minlength="3" maxlength="4" required="" />
                                                                                    </div>
                                                                                </li>
                                                                                <li class="form-list__row">
                                                                                    <label>Nombre en la tarjeta</label>
                                                                                    <input inputmode="none"  id="holderName"  desc="Nombre en la tarjeta" onclick="fn_alfaNumerico_EscribirNombre('#holderName');" type="text" name="" required=""  value=""/>
                                                                                </li>

                                                                                <button type="button"    style="margin-top: 25px;  font-size: 18px;  " class="button" id="btnPagar" style="" onclick="enviarPago()" >Pagar</button>
                                                                                <button type="button" class="button" style=" font-size: 18px;" id="btnCancelarPay" onclick="pay_cancelarBusquedaRegistroCliente()">Cancelar</button>

                                                                                <div class="spinner" id="procesandoPagoDirecto">
                                                                                    <div class="rect1"></div>
                                                                                    <div class="rect2"></div>
                                                                                    <div class="rect3"></div>
                                                                                    <div class="rect4"></div>
                                                                                    <div class="rect5"></div>
                                                                                </div>

                                                                                </li>
                                                                            </ul>

                                                                        </div> <!-- END: .modal__content -->
                                                                    </div> <!-- END: .modal__container -->

                                                                </div> <!-- END: .modalPay -->

                                                            </div>

                                                        </div><!-- End: Contenedor   Paypone-->


                                                        <!-- Inicio Contenedor  PAYVALIDA -->
                                                        <div id="modalPayvalida" title="Payvalida: Seleccione una opción">
                                                            <div id="seleccionOpcionPayvalida">

                                                            <div class="modal tamanio modal1" id="modalSeleccionOpcionPayvalida" style="display:none; height: 585px; width: 677px; top: 270px; left: 480px;">
                                                                    <div class="modal__container_directa" style="width: 100%">
                                                                        <div class="modal__featured">

                                                                            <div class="modal__circle registro"></div>
                                                                            <img src="../imagenes/facturacion/mxpBuscarUsuario.PNG" class="modal__product">

                                                                        </div>
                                                                        <div class="modal__content modal__contentRegistro">
                                                                            <div id="opcionesPayvalida"></div>
                                                                            <div id="resultadoPayvalida"></div>
                                                                        </div> 
                                                                    </div> 

                                                                </div>

                                                           
                                                            </div>
                                                        </div>
                                                        <!-- Fin Contenedor  PAYVALIDA -->


                                                        <!-- Contenedor Medio Autorizador -->
                                                        <div id="modalSWT" title="Seleccione Medio Autorizador..">
                                                            <table align="center"><tr id="tblSWT"></tr></table>
                                                        </div><!-- End: Contenedor Medio Autorizador -->

                                                        <!-- Contedor Cupones -->
                                                        <div id="modalCupon" title="Seleccione forma de pago del Cliente Externo ..." style="display:none">
                                                            <table align="center"><tr id="tblCupon"></tr></table>
                                                        </div><!-- End: Contenedor Medio Autorizador -->

                                                        <!--Contenedor-->
                                                        <div id="modalCuponSistemaGerenteVoucher" style="display:none">
                                                            <div class="preguntasTitulo"><label id="infoMdalf">Ingreso Cup&oacute;n Sistema Gerente</label><img src="../imagenes/admin_resources/btn_eliminar.png" onclick="fn_cerrarModalCuponesSistemaGerenteVoucher()" class="btn_cerrar_modal_cupones"/></div>
                <div  id="voucherAE"></div>
                <div  id="select_cliente"></div>
                <div  id="select_tipocupo"></div>
                                                            <div  id="select_cliente">

                                                            </div>

                                                            <div  id="select_tipocupo">
                                                            </div>
                                                            <div id="botonVolver1" style="display: none">
                                                                <!--<img onclick="pantalla1()"  style="margin-top: 3%; width: 10%;" src="../imagenes/volverCupon.png"></img>-->
                                                                <input inputmode="none"  type="text" id="buscarClienteExt" name="buscarClienteExt" style="width: 97%;height: 40%; padding:15px;margin: 15px;"/>
                                                            </div>
                                                            <div id="botonVolver2" style="display: none">
                                                                <img onclick="pantalla2()" id="imgPantalla2"  style="margin-top: 3%; width: 10%;" src="../imagenes/volverCupon.png"></img>
                                                            </div>
                                                            <div  id="voucherAEs">
                                                                <center>  <input inputmode="none"   type="password" name="input_cuponSistemaGerenteAutEXT" onchange="fn_canjearCuponAutomaticoVoucher()" id="input_cuponSistemaGerenteAutEXT" style="height: 35px; width: 454px;"/> </center>
                                                            </div>
                                                        </div>

                                                        <!-- Contenedor Dispositovo Tarjeta Credito -->
                                                        <div id="modalsubSWT" title="Desea cobrar por...">
                                                            <table align="center"><tr id="tblsubSWT"></tr></table>
                                                        </div><!-- End: Contenedor Dispositovo Tarjeta Credito -->

                                                        <!-- Contendor Clientne Axapta -->
                                                        <div id="modalclienteAx" title="Seleccione cliente..">
                                                            <div style="height:20px;">
                                                                <table align="center" id="tblclienteAx">
                        <tr>
                            <td><input inputmode="none"  style="width:480px; font-size:20px;" type="text" id="txt_cliAx"/></td>
                        </tr>
                                                                </table>
                                                            </div>
                                                            <br/>
                                                            <div style="height:200px;" id="divContenedor">
                                                                <table align="center" id="detalleAx"></table>
                                                                <div id="divCadenasC" style="height: 110px; width: 620px;">
                                                                    <div id="divCadenas"></div>
                                                                </div>
                                                                <br/>
                                                                <div id="divRsts" style="padding-top: 0px;" class="cntSelectMovimiento">
                                                                    <label>Tienda: </label>
                                                                    <select id="selRsts"></select>
                                                                </div>
                                                                <div id="divObservacion" style="padding-top: 0px;" class="cntObservacion">
                                                                <label style="color:#000000;">Es obligatorio registrar el nombre de quién autoriza el crédito y su centro de costo </label><br><br>
                                                              
                                                                    <label>Observaci&oacute;n: </label>
                                                                    <textarea id="txtObservacion" style="width: 600px; height: 98px;"></textarea>
                                                                    <div id="div_conceptos" style="width: 600px; height: 118px;" ></div>
                                                                </div>
                                                            </div>
                                                        </div><!-- End: Contendor Clientne Axapta -->

                                                        <!-- Contenedor Teclado Credito Sin Cupon -->
                                                        <div id="ingresoValorCredito">
                                                            <div class="preguntasTitulo">Ingrese el valor..</div>
                                                            <div class="anulacionesSeparador" >
                                                                <div class="anulacionesInput">
                                                                    <input inputmode="none"  type="text" id="txt_valorDescuento" style="width: 380px; font-size: 20px;" readonly="readonly" />
                                                                </div>
                                                            </div>
                                                        </div><!-- End: Contenedor Teclado Credito Sin Cupon -->

                                                        <!-- Contenedor Credito Valor Fijo-->
                                                        <div id="ingresoValorCreditoTeclado" style="left:35%; top:450px; position:absolute; display:block; z-index:99999;">
                                                            <table id="tabla_ingresoValorCredito" align="center">
                                                                <tr>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 7)">7</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 8)">8</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 9)">9</button></td>
                                                                    <td><button class='btnVirtualOKpq' onClick="fn_eliminarNumero(txt_valorDescuento);">&larr;</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 4)">4</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 5)">5</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 6)">6</button></td>
                                                                    <td><button class='btnVirtualOKpq' onClick="fn_eliminarTodo(txt_valorDescuento);">&lArr;</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 1)">1</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 2)">2</button></td>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 3)">3</button></td>
                                                                    <td><button class='btnVirtualOKpq' onClick="fn_IngresoFondo();">OK</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button class='btnVirtual' onClick="fn_agregarCaracter(txt_valorDescuento, 0)">0</button></td>
                                                                    <td><button class='btnVirtual' id='btn_punto' onClick="fn_agregarCaracter(txt_valorDescuento, '.')">.</button></td>
                                                                    <td colspan="4"><button style="width:195px" class='btnVirtualOKpq' onClick="fn_cerrarValorCredito();">Cancelar</button></td>
                                                                </tr>
                                                            </table>
                                                        </div><!-- End: Contenedor Credito Valor Fijo-->

                                                        <!-- Contenedor Anulacion Formas Pago -->
                                                        <div id="modalSWTCancelacion" title="Seleccione Tarjeta a Cancelar...">
                                                            <table align="left">
                                                                <tr id="tblSWTCancelacion"></tr>
                                                            </table>
                                                        </div><!-- End: Contenedor Anulacion Formas Pago -->

                                                        <!-- Contenedor MODAL PARA INGRESI DE BIN PARA PAGOS CON DATAFAST -->
                                                        <div id="modal_binDatafast" title="INGRESE 6 PRIMEROS DIGITOS DE LA TARJETA...">
                                                            <table align="center">
                                                                <tr>
                                                                    <td align="center" colspan="3"><input inputmode="none"  style="font-size:30px;" type="text" id="txt_bin" value="" maxlength="6"/></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarBinTarjeta('7')" value=" 7 " >7</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('8')" value=" 8 "  >8</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('9')" value=" 9 " >9</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarBinTarjeta('4')" value=" 4 "  >4</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('5')" value=" 5 "  >5</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('6')" value=" 6 "  >6</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_agregarBinTarjeta('1')" value=" 1 "  >1</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('2')" value=" 2 "  >2</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('3')" value=" 3 "  >3</button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><button onclick="fn_eliminarDigitoBin()">&larr;</button></td>
                                                                    <td><button onclick="fn_agregarBinTarjeta('0')" >0</button></td>
                                                                    <td><button id="btn_borrarBin" onclick="fn_eliminarBin()" value=" <- " >Borrar</button></td>
                                                                </tr>
                                                            </table>
                                                        </div><!-- End: Contenedor MODAL PARA INGRESI DE BIN PARA PAGOS CON DATAFAST -->

                                                        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display: none">
                                                            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor" style="height: 220px; width: 220px; z-index:99999999999999">
                                                                <img src="../imagenes/loading.gif"/>
                                                            </div>
                                                        </div>
                                                        <div id="mdl_rdn_pdd_crgnd1" class="modal_cargando" style="display: none">
                                                            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor" style="height: 220px; width: 220px; z-index:99999999999999">
                                                                <img src="../imagenes/loading.gif"/>
                                                            </div>
                                                        </div>
                                                        <div id="tecladoCodigoFacturacion" style="display: none">
                                                            <div class="OpcionCodigo"><label>Ingrese Codigo </label></div>
                                                            <div class="opcionCampoCodigo">
                                                                <input inputmode="none"  type="text" id="txtcodigoFacturacion" style="height: 35px; width: 454px;"/>
                                                            </div>
                                                        </div>

                                                        <!-- Modal Cupones Sistema Gerente-->
                                                        <div id="modalCuponMultimarca">
                                                            <div id="cntBotonesLecturaAutomaticaCuponMultimarca">
                                                                <button id="btnLecturaAutomaticaCuponMultimarca">Ingreso Autom&aacute;tico</button>
                                                                <button id="btnLecturaManualCuponMultimarca">Ingreso Manual</button>
                                                            </div>
                                                            <div id="cntInputCodigoCuponMultimarcaAutomatico" class="cntInputCodigoCuponMultimarca">
                                                                <input inputmode="none"  type="text" id="inputCodigoCuponMultimarcaAutomatico" />
                                                            </div>
                                                            <div id="cntInputCodigoCuponMultimarcaManual" class="cntInputCodigoCuponMultimarca">
                                                                <input inputmode="none"  type="text" id="inputCodigoCuponMultimarcaManual1" maxlength="3"  />/
                                                                <input inputmode="none"  type="text" id="inputCodigoCuponMultimarcaManual2" maxlength="8"  />/
                                                                <input inputmode="none"  type="text" id="inputCodigoCuponMultimarcaManual3" maxlength="9"  />/
                                                                <input inputmode="none"  type="text" id="inputCodigoCuponMultimarcaManual4" maxlength="2"  />
                                                            </div>
                                                        </div>


                                                        <!--Contenedor Agregadores-->         
                                                        <?php include 'agregadores.php'; ?>


                                                        <!-- FORMULARIO PARA RECARGAR PAGINA AL ELIMINAR DESCUENTOS -->
                                                        <div id="formCobrar">
                                                            <input inputmode="none"  type="hidden" name="pantallaAcceso" value="TOMA PEDIDO" id="pantallaAcceso"/>
                                                            <input inputmode="none"  type="hidden" name="codigoCategoria" id="codigoCategoria"/>
                                                            <input inputmode="none"  type="hidden" name="hide_menu_id" id="hide_menu_id"/>
                                                            <input inputmode="none"  type="text" name="hide_pluId" id="hide_pluId" style="display: none;"/>
                                                            <input inputmode="none"  type="hidden" name="hide_magp_id" id="hide_magp_id"/>
                                                            <input inputmode="none"  type="hidden" name="hide_plu_gramo" id="hide_plu_gramo"/>
                                                            <input inputmode="none"  type="hidden" name="hide_odp_id" id="hide_odp_id"/>
                                                            <input inputmode="none"  type="hidden" name="hide_dop_id" id="hide_dop_id"/>
                                                            <input inputmode="none"  type="hidden" name="hide_mesa_id" id="hide_mesa_id" value="<?php echo htmlspecialchars($mesa_id); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_num_Pers" id="hide_num_Pers" value="<?php echo htmlspecialchars($num_Pers); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo htmlspecialchars($cdn_id); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo htmlspecialchars($usr_id); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo htmlspecialchars($lc_rst); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_cat_id" id="hide_cat_id" value="<?php echo htmlspecialchars($cat_id); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo htmlspecialchars($est_id); ?>"/>
                                                            <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo htmlspecialchars($est_ip); ?>"/>
                                                            <input inputmode="none"  type="hidden" id="txtTipoServicio" value="<?php echo $lc_tipoServicio; ?>" />
                                                            <input inputmode="none"  type="hidden" name="hide_cdn_tipoimpuesto" id="hide_cdn_tipoimpuesto"/>
                                                            <input inputmode="none"  type="hidden" name="cantidadOK" id="cantidadOK" value="1" />
                                                            <input inputmode="none"  type="hidden" name="pluAgregar" id="pluAgregar"/>
                                                            <input inputmode="none"  type="hidden" name="magpAgregar" id="magpAgregar"/>
                                                            <input inputmode="none"  type="hidden" name="hid_cla_id" id="hid_cla_id"/>
                                                            <input inputmode="none"  type="hidden" name="banderaCierrePeriodo" id="banderaCierrePeriodo" value="<?php echo $_SESSION['sesionbandera']; ?>"/>
                                                            <input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $_SESSION['bloqueoacceso'] ?>"/>

                                                        </div>

                                                        <div id="cntFormularioPayPhone"></div>

                                                        <!------------------------- Informacion cupon cliente externo ------------------------->
                                                        <?php
                                                        if (htmlspecialchars(isset($_POST['txt_vae_IDCliente']))) {
                                                            $tipov_id = htmlspecialchars(isset($_POST['txt_tipov_id'])) ? htmlspecialchars($_POST['txt_tipov_id']) : '';
                                                            $vae_cod = htmlspecialchars(isset($_POST['txt_vae_cod'])) ? htmlspecialchars($_POST['txt_vae_cod']) : '';
                                                            $vae_IDCliente = htmlspecialchars(isset($_POST['txt_vae_IDCliente'])) ? htmlspecialchars($_POST['txt_vae_IDCliente']) : '';
                                                            $cli_direccion = htmlspecialchars(isset($_POST['txt_cli_direccion'])) ? htmlspecialchars($_POST['txt_cli_direccion']) : '';
                                                            $cli_documento = htmlspecialchars(isset($_POST['txt_cli_documento'])) ? htmlspecialchars($_POST['txt_cli_documento']) : '';
                                                            $cli_email = htmlspecialchars(isset($_POST['txt_cli_email'])) ? htmlspecialchars($_POST['txt_cli_email']) : '';
                                                            $cli_nombres = htmlspecialchars(isset($_POST['txt_cli_nombres'])) ? htmlspecialchars($_POST['txt_cli_nombres']) : '';
                                                            $cli_telefono = htmlspecialchars(isset($_POST['txt_cli_telefono'])) ? htmlspecialchars($_POST['txt_cli_telefono']) : '';
                                                            $montoCupon = htmlspecialchars(isset($_POST['txt_montoCupon'])) ? htmlspecialchars($_POST['txt_montoCupon']) : '';
                                                            $esVoucher = htmlspecialchars(isset($_POST['txt_esVoucher'])) ? htmlspecialchars($_POST['txt_esVoucher']) : 0;
                                                        } else {
                                                            $tipov_id = '';
                                                            $vae_cod = '';
                                                            $vae_IDCliente = '';
                                                            $cli_direccion = '';
                                                            $cli_documento = '';
                                                            $cli_email = '';
                                                            $cli_nombres = '';
                                                            $cli_telefono = '';
                                                            $montoCupon = '';
                                                            $esVoucher = 0;
                                                        }
                                                        ?>

			<input type="hidden" id="txt_tipov_id" value="<?php echo htmlspecialchars($tipov_id); ?>" />
			<input type="hidden" id="txt_vae_cod" value="<?php echo htmlspecialchars($vae_cod); ?>" />
			<input type="hidden" id="txt_vae_IDCliente" value="<?php echo htmlspecialchars($vae_IDCliente); ?>" />

            <input type="hidden" id="Tarjeta" name="Tarjeta" value="0"/>
            <input type="hidden" id="TarjetaDescripcion" name="TarjetaDescripcion" value=""/>
            <input type="hidden" id="TarjetaId" name="TarjetaId" value=""/>
            <input type="hidden" id="TarjetaId_tfp" name="TarjetaId_tfp" value=""/>
            <input type="hidden" id="TarjetaRequiereAutorizacion" name="TarjetaRequiereAutorizacion" value=""/>
            <input type="hidden" id="TarjetaDescripcion_tfp" name="TarjetaDescripcion_tfp" value=""/>


                                                        <input inputmode="none"  type="hidden" id="txt_tipov_id" value="<?php echo htmlspecialchars($tipov_id); ?>" />
                                                        <input inputmode="none"  type="hidden" id="txt_vae_cod" value="<?php echo htmlspecialchars($vae_cod); ?>" />
                                                        <input inputmode="none"  type="hidden" id="txt_vae_IDCliente" value="<?php echo htmlspecialchars($vae_IDCliente); ?>" />


            <input inputmode="none"  id="txt_cli_direccion" type="hidden" value="<?php echo htmlspecialchars($cli_direccion); ?>" />
            <input inputmode="none"  id="txt_cli_documento" type="hidden" value="<?php echo htmlspecialchars($cli_documento); ?>" />
            <input inputmode="none"  id="txt_cli_email" type="hidden" value="<?php echo htmlspecialchars($cli_email); ?>" />
            <input inputmode="none"  id="txt_cli_nombres" type="hidden" value="<?php echo htmlspecialchars($cli_nombres); ?>" />
            <input inputmode="none"  id="txt_cli_telefono" type="hidden" value="<?php echo htmlspecialchars($cli_telefono); ?>" />
            <input inputmode="none"  id="txt_montoCupon" type="hidden" value="<?php echo htmlspecialchars($montoCupon); ?>" />
            <input inputmode="none"  id="txt_esVoucher" type="hidden" value="<?php echo htmlspecialchars($esVoucher); ?>" />
            <input inputmode="none"  id="txt_est_ip" type="hidden" value="<?php echo htmlspecialchars($lc_ip); ?>" />
            <input inputmode="none"  id="txtCadenaId" type="hidden" value="<?php echo htmlspecialchars($lc_cdnId); ?>" />
            <input inputmode="none"  id="txtRestaurante" type="hidden" value="<?php echo htmlspecialchars($lc_rst); ?>" />
            <input inputmode="none"  id="txtOrdenPedidoId" type="hidden" value="<?php echo htmlspecialchars($lc_ordenPedidoId); ?>" />
            <input inputmode="none"  id="txtNumCuenta" type="hidden" value="<?php echo htmlspecialchars($lc_numCuenta); ?>" />
            <input inputmode="none"  id="txtNumMesa" type="hidden" value="<?php echo htmlspecialchars($lc_numMesa); ?>" />
            <input inputmode="none"  id="cdn_tipoImpuesto" type="hidden" />
            <input inputmode="none"  id="txtUserId" type="hidden" value="<?php echo htmlspecialchars($lc_UsuarioId); ?>" />
            <input inputmode="none"  id="txtTipoServicio" type="hidden" value="<?php echo htmlspecialchars($lc_tipoServicio); ?>" />
            <input inputmode="none"  id="txtClienteId" type="hidden" name="txtClienteId" />
            <input inputmode="none"  id="btnFormaPagoId" type="hidden" name="btnFormaPagoId" />
            <input inputmode="none"  id="btnBaseFactura" type="hidden" />
            <input inputmode="none"  id="txt_tfpId" type="hidden" />
            <input inputmode="none"  id="valorSubTotal" type="hidden" name="valorSubTotal" />
            <input inputmode="none"  id="valorIva" type="hidden" name="valorIva" />
            <input inputmode="none"  id="valorTotal" type="hidden" name="valorTotal" />
            <input inputmode="none"  id="txtNumFactura" class="numFactura" type="hidden" />
            <input inputmode="none"  id="usr_perfil" class="numFactura" type="hidden" value="<?php echo htmlspecialchars($lc_perfilUsuario); ?>" />
            <input inputmode="none"  id="id_desc" class="numFactura" type="hidden" />
            <input inputmode="none"  id="maximo" class="numFactura" type="hidden" />
            <input inputmode="none"  id="valor" class="numFactura" type="hidden" />
            <input inputmode="none"  id="tipo_des" class="numFactura" type="hidden" />
            <input inputmode="none"  id="precio_min" class="numFactura" type="hidden" />
            <input inputmode="none"  id="canti_min" class="numFactura" type="hidden" />
            <input inputmode="none"  id="aplica" class="numFactura" type="hidden" />
            <input inputmode="none"  id="porfactura" class="numFactura" type="hidden" />
            <input inputmode="none"  id="fecha_ini" class="numFactura" type="hidden" />
            <input inputmode="none"  id="fecha_fin" class="numFactura" type="hidden" />
            <input inputmode="none"  id="hid_cambio" class="numFactura" type="hidden" />
            <input inputmode="none"  id="hid_pasaporte" class="numFactura" type="hidden" />
            <input inputmode="none"  id="idClienteAX" type="hidden" />
            <input inputmode="none"  id="hid_btn_cancelaPago" type="hidden" />
            <input inputmode="none"  id="hid_bandera_cvv" type="hidden" />
            <input inputmode="none"  id="hid_bandera_propina" type="hidden"/>
            <input inputmode="none"  id="hid_bandera_teclado" type="hidden"/>
            <input inputmode="none"  id="idUser" type="hidden" value="<?php echo htmlspecialchars($_SESSION['usuarioId']); ?>"/>
            <input inputmode="none"  id="idRest" type="hidden" value="<?php echo htmlspecialchars($_SESSION['rstId']); ?>"/>
            <input inputmode="none"  id="tiempoEspera" type="hidden" value="<?php echo htmlspecialchars($_SESSION['tiempoEsperaTarjetas']); ?>"/>
            <input inputmode="none"  id="simMoneda" type="hidden" value="<?php echo htmlspecialchars($lc_simbolomodeda); ?>"/>
            <input inputmode="none"  id="fmp_id" type="hidden" value=""/>
            <input inputmode="none"  id="tfp_id" type="hidden" value=""/>
            <input inputmode="none"  id="can_sumar" type="hidden" value=""/>
            <input inputmode="none"  id="hid_valorPagado" type="hidden" value=""/>
            <input inputmode="none"  id="hid_cliAx" type="hidden" />
            <input inputmode="none"  id="hid_nombreAx" type="hidden" />
            <input inputmode="none"  id="hid_descTipoFp" type="hidden" />
            <input inputmode="none"  id="hid_descFp" type="hidden" />
                                                        <input inputmode="none"  type="hidden" id="hid_bloqueo"  value="<?php $_SESSION['bloqueoacceso']; ?>"/>
                                                        <input inputmode="none"  id="hid_conDatos" type="hidden" />
                                                        <input inputmode="none"  id="hide_ordenKiosko" type="hidden" value="<?php echo htmlspecialchars($ordenKiosko); ?>"/>
                                                        <input inputmode="none"  id="hide_reimpresionKiosko" type="hidden" value="<?php echo htmlspecialchars($reimpresionKiosko); ?>"/>
                                                        <input inputmode="none"  id="hide_turneroActivo"  type="hidden"  value="<?php echo htmlspecialchars($_SESSION["turneroActivo"]); ?>"/>
                                                        <input inputmode="none"  id="hide_turneroURl"     type="hidden"  value="<?php echo htmlspecialchars($_SESSION["turneroURl"]); ?>"/>
            <input inputmode="none"  id="hide_turneroHabilitadoPorEstacion"  type="hidden"  value="<?php echo $_SESSION["habilitadoPorEstacion"]; ?>"/>
            <input inputmode="none"  id="clienteAutorizacion" type="hidden" value="" />
            <input inputmode="none"  id="clienteEstado" type="hidden" value="" />
            <input inputmode="none"  id="estadoWS" type="hidden" value="" />
            <input type="hidden" id="hide_pickupActivo" value="<?php echo htmlspecialchars($pickupActivo); ?>"/>

            <input inputmode="none"  id="hidVitality" type="hidden" value="<?php echo htmlspecialchars($vitality); ?>" />
            <input inputmode="none"  id="hidClienteVitality" type="hidden" value="<?php echo htmlspecialchars($idClienteVitality); ?>" />
            <input inputmode="none"  id="hidBalanceVitality" type="hidden" value="<?php echo htmlspecialchars($balanceVitality); ?>" />
            <input inputmode="none"  id="hidNumeroDocumentoVitality" type="hidden" value="<?php echo htmlspecialchars($numeroDocumentoC); ?>"/>
            <input inputmode="none"  id="hidCodigoQRVitality" type="hidden" value="<?php echo htmlspecialchars($codigoQRVitality); ?>"/>
            <input inputmode="none"  id="hidTokenSeguridadVitality" type="hidden" value="<?php echo htmlspecialchars($tokenSeguridadVitality); ?>"/>
            <input inputmode="none"  id="hidIdCreditoExterno" type="hidden" value=""/>
            <input inputmode="none"  id="hidNombreClienteVitality" type="hidden" value="<?php echo htmlspecialchars($NombreClienteVitality); ?>"/>
            <input inputmode="none"  id="hidAddressVitality" type="hidden" value="<?php echo htmlspecialchars($addressVitality); ?>"/>
            <input inputmode="none"  id="hidPhoneNumberVitality" type="hidden" value="<?php echo htmlspecialchars($phoneNumberVitality); ?>"/>
                                                        <input inputmode="none"  id="clienteAutorizacion" type="hidden" value=""></input>
                                                        <input inputmode="none"  id="clienteEstado" type="hidden" value=""></input>
                                                        <input inputmode="none"  id="estadoWS" type="hidden" value=""></input>
            <!-- Contenedor para integraciones, nuevas funcionalidades -->
            <!-- Modal -->
            <div id="cntSeguridadCliente" class="modal_cargando" style="display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="cntHeader" class="modal-header">
                            <h3 class="modal-title" id="testLabel">Código de Seguridad</h3>
                        </div>
                        <div id="cntBody" class="modal-body">
                            <p class="parrafoPromocion">Leer código de seguridad del cliente.</p>
                            <br/>
                            <input inputmode="none"  type="password" id="inputCodigoSeguridad" onchange="cambiarCodigoSeguridad()" class="codigoPromocion" value="" />
                        </div>
                        <div id="mdlFooter" style="height: 90px">
                            <button type="button" class="boton_Opcion" onclick="cerrarModalCodigoSeguridad()">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

<!-- modal masivo -->
            <div class="modal_cargando" id="reintentosMasivoApi" style="display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="cntHeader" class="modal-header">
                            <h3 class="modal-title" id="testLabel">Fallo en la acumulacion de masivo</h3>
                        </div>
                        <div id="cntBody" class="modal-body">
                            <p class="parrafoPromocion">Porfavor vuelve a reintentar</p>
                            <br/>
                            <input inputmode="none" type="button" class="button_fdzn" onclick="fn_reintentar_masivo()" value="Reintentar" />
                            <input inputmode="none" type="button" class="button_fdzn" onclick="fn_cancelar_masivo()" value="Cancelar" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal_cargando" id="continuarMasivoApi" style="display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="cntHeader" class="modal-header">
                            <h3 class="modal-title" id="testLabel">Fallo en la acumulacion de masivo</h3>
                        </div>
                        <div id="cntBody" class="modal-body">
                            <p class="parrafoPromocion">Para proceder con el proceso da click en continuar</p>
                            <br/>
                            <input inputmode="none" type="button" class="button_fdzn" onclick="fn_continuar_masivo()" value="Continuar" />
                        </div>
                    </div>
                </div>
            </div>
                                                    
            <div class="campana_modal_overlay" id="campana_modal" style="display: none;">
            <div class="campana_modal_dialog">
                <div class="campana_modal_content">

                <!-- Columna izquierda con imagen -->
                <div class="campana_columna_izquierda">
                    <img src="../imagenes/campanaSolidaria/image_uno.png" alt="Campaña" class="campana_imagen" />
                </div>

                <!-- Columna derecha con contenido -->
                <div class="campana_columna_derecha">

                    <div class="campana_contenido_texto">
                    <div class="texto_con_icono">
                        <img src="../imagenes/campanaSolidaria/ico_exclamacion.png" alt="!" class="icono_exclamacion" />
                        <div class="texto_principal">
                        Hola, has seleccionado en esta orden de kiosko incluir una propina o donación voluntaria por un monto de:
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <strong id="campana_monto_texto" class="monto_destacado">$0.00</strong>
                    </div>

                    <p>
                        Esta contribución apoya directamente a la compañía Sonrisas
                        ¿Deseas continuar?
                    </p>
                    </div>

                    <div class="campana_botones_derecha">
                    <button class="btn-rojo" onclick="fn_cancelar_campana()">&#x1F61E; Ya no quiero donar</button>
                    <button class="btn-azul" onclick="fn_generarCampanaSolidariaFactura()">&#x1F600; Sí, quiero donar</button>
                    </div>
                </div>

                </div>
            </div>
            </div>

                                                        <input type="hidden" id="txt_tipov_id" value="<?php echo htmlspecialchars($tipov_id); ?>" />
                                                        <input type="hidden" id="txt_vae_cod" value="<?php echo htmlspecialchars($vae_cod); ?>" />
                                                        <input type="hidden" id="txt_vae_IDCliente" value="<?php echo htmlspecialchars($vae_IDCliente); ?>" />

                                                        <input type="hidden" id="txt_cli_direccion" value="<?php echo htmlspecialchars($cli_direccion); ?>" />
                                                        <input type="hidden" id="txt_cli_documento" value="<?php echo htmlspecialchars($cli_documento); ?>" />
                                                        <input type="hidden" id="txt_cli_email"     value="<?php echo htmlspecialchars($cli_email); ?>" />
                                                        <input type="hidden" id="txt_cli_nombres"   value="<?php echo htmlspecialchars($cli_nombres); ?>" />
                                                        <input type="hidden" id="txt_cli_telefono"  value="<?php echo htmlspecialchars($cli_telefono); ?>" />
                                                        <input type="hidden" id="txt_montoCupon"  value="<?php echo htmlspecialchars($montoCupon); ?>" />
                                                        <input type="hidden" id="txt_esVoucher" value="<?php echo htmlspecialchars($esVoucher); ?>" />

                                                        <input type="hidden" id="txt_est_ip" value="<?php echo htmlspecialchars($lc_ip); ?>" />
                                                        <input type="hidden" id="txtCadenaId" value="<?php echo htmlspecialchars($lc_cdnId); ?>" />
                                                        <input type="hidden" id="txtRestaurante" value="<?php echo htmlspecialchars($lc_rst); ?>" />
                                                        <input type="hidden" id="txtOrdenPedidoId" value="<?php echo htmlspecialchars($lc_ordenPedidoId); ?>" />
                                                        <input type="hidden" id="txtNumCuenta" value="<?php echo htmlspecialchars($lc_numCuenta); ?>" />
                                                        <input type="hidden" id="txtNumMesa" value="<?php echo htmlspecialchars($lc_numMesa); ?>" />
                                                        <input type="hidden" id="cdn_tipoImpuesto" />
                                                        <input type="hidden" id="txtUserId" value="<?php echo htmlspecialchars($lc_UsuarioId); ?>" />
                                                        <input type="hidden" id="txtTipoServicio" value="<?php echo htmlspecialchars($lc_tipoServicio); ?>" />
                                                        <input type="hidden" id="txtClienteId" name="txtClienteId"/>
                                                        <input type="hidden" id="btnFormaPagoId" name="btnFormaPagoId"/>
                                                        <input type="hidden" id="btnBaseFactura"/>
                                                        <input type="hidden" id="txt_tfpId"/>
                                                        <input type="hidden" id="valorSubTotal" name="valorSubTotal"/>
                                                        <input type="hidden" id="valorIva" name="valorIva"/>
                                                        <input type="hidden" id="valorTotal" name="valorTotal"/>
                                                        <input class="numFactura" type="hidden" id="txtNumFactura" />
                                                        <input class="numFactura" type="hidden" id="usr_perfil" value="<?php echo htmlspecialchars($lc_perfilUsuario); ?>" />
                                                        <input class="numFactura" type="hidden" id="id_desc" />
                                                        <input class="numFactura" type="hidden" id="maximo" />
                                                        <input class="numFactura" type="hidden" id="valor" />
                                                        <input class="numFactura" type="hidden" id="tipo_des" />
                                                        <input class="numFactura" type="hidden" id="precio_min" />
                                                        <input class="numFactura" type="hidden" id="canti_min" />
                                                        <input class="numFactura" type="hidden" id="aplica" />
                                                        <input class="numFactura" type="hidden" id="porfactura" />
                                                        <input class="numFactura" type="hidden" id="fecha_ini" />
                                                        <input class="numFactura" type="hidden" id="fecha_fin" />
                                                        <input class="numFactura" type="hidden" id="hid_cambio" />
                                                        <input class="numFactura" type="hidden" id="hid_pasaporte" />
                                                        <input  type="hidden" id="idClienteAX" />
                                                        <input  type="hidden" id="hid_btn_cancelaPago" />
                                                        <input  type="hidden" id="hid_bandera_cvv" />
                                                        <input type="hidden"id="hid_bandera_propina"/>
                                                        <input type="hidden"id="hid_bandera_teclado"/>
                                                        <input id="idUser" type="hidden" value="<?php echo htmlspecialchars($_SESSION['usuarioId']); ?>"/>
                                                        <input id="idRest" type="hidden" value="<?php echo htmlspecialchars($_SESSION['rstId']); ?>"/>
                                                        <input id="tiempoEspera" type="hidden" value="<?php echo htmlspecialchars($_SESSION['tiempoEsperaTarjetas']); ?>"/>
                                                        <input id="simMoneda" type="hidden" value="<?php echo htmlspecialchars($lc_simbolomodeda); ?>"/>
                                                        <input id="fmp_id" type="hidden" value=""/>
                                                        <input id="tfp_id" type="hidden" value=""/>
                                                        <input id="can_sumar" type="hidden" value=""/>
                                                        <input id="hid_valorPagado" type="hidden" value=""/>
                                                        <input id="hid_cliAx" type="hidden" />
                                                        <input id="hid_nombreAx" type="hidden" />
                                                        <input id="hid_descTipoFp" type="hidden" />
                                                        <input id="hid_descFp" type="hidden" />
                                                        <input type="hidden" id="hid_bloqueo"  value="<?php $_SESSION['bloqueoacceso']; ?>"/>
                                                        <input id="hid_conDatos" type="hidden" />
                                                        <input id="hide_ordenKiosko" type="hidden" value="<?php echo htmlspecialchars($ordenKiosko); ?>"/>
                                                        <input id="hide_reimpresionKiosko" type="hidden" value="<?php echo htmlspecialchars($reimpresionKiosko); ?>"/>
                                                        <input id="hide_turneroActivo"  type="hidden"  value="<?php echo htmlspecialchars($_SESSION["turneroActivo"]); ?>"/>
                                                        <input id="hide_turneroURl"     type="hidden"  value="<?php echo htmlspecialchars($_SESSION["turneroURl"]); ?>"/>
                                                        <input id="hide_turneroHabilitadoPorEstacion"  type="hidden"  value="<?php echo htmlspecialchars($_SESSION["habilitadoPorEstacion"]); ?>"/>
                                                        <input id="clienteAutorizacion" type="hidden" value=""></input>
                                                        <input id="clienteEstado" type="hidden" value=""></input>
                                                        <input id="estadoWS" type="hidden" value=""></input>

<div id="loadingDeUna">
        <div id="myModalDeuna" class="modal-Deuna">
        <div class="modal-content-Deuna">
            <!--<span class="close-button-Deuna">&times;</span>-->
            <hr>
                <h3 style="margin-top: 2px; margin-bottom: 2px;"><b>Esperando el pago del cliente...</b></h1>
            <hr>
            <br>
                <div id="msgPagoDeUnaConQR">
                    <h4>El cliente ya puede escanear el código QR y realizar el pago.</h4>
                    <br>
                    <p class="letrasGrisesDeUna">Si cierras esta ventana el pago no se completará</p>
                </div>
                <div id="msgPagoDeUnaConPinCode">
                    <div style="justify-content: center; display: flex">
                        <bold><p style="font-size: 32px; font-weight: bold">Código único de pago:</p></bold>
                    </div>
                    <p style="font-size: 24px; font-weight: normal">Este es un número que debes entregar al cliente para realizar el pago</p>

                    <br>
                    <div style="text-align: center;">
                        <div id="pinCodeDeUna" style=" display: inline-flex; align-items: center; gap: 15px;">
                        </div>
                    </div>
                    <br>
                </div>
                    <div id="loaderDeUnaPin" style="justify-content: center; display: flex; margin-top: 10px;">
                        <div class="progress-circle">
                            <svg id="svg_deuna" width="100" height="100" viewBox="0 0 100 100">
                                <circle class="background-circle" cx="50" cy="50" r="45"></circle>
                                <circle class="progress-ring-deuna" cx="50" cy="50" r="45"></circle>
                            </svg>
                            <div id="progressDeUna">
                                <span id="cuenta_regresiva_deuna">60</span>
                                <span style="font-size: 20px; font-weight: normal; margin: 0 5px;">de</span>
                                <span id="tiempoTotalDeUna" style="font-size: 18px;font-weight: bold;">70</span>
                                <br>
                                <span style="font-size: 20px; color: #777; display: block; margin-top: -5px;">Segundos</span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div style="justify-content: center; display: flex">
                        <p id="mensajeExpDeUna" class="letrasGrisesDeUna"></p>
                    </div>
            <br>
            <div>
<!--            <button class="boton-cerrar-deuna" id="cerrarModalDeUna" onclick="confirmacionParaCerrarElModal(0)" >Cancelar Transacción</button>-->
            <!--<button id="seguirModelDeUna" onclick="" >Dejar Pasar</button>-->
        </div>
    </div>
</div>

<div id="confirmarCancelacion">
        <div id="myModalDeunaConfirmarCancelacion" class="modal-Deuna">
        <div class="modal-content-Deuna">
            <!--<span class="close-button-Deuna">&times;</span>-->
            <hr>
                <h3 style="margin-top: 2px; margin-bottom: 2px;">¿ Estás seguro de cancelar esta transacción en Curso ?</h3>
            <hr>
            <br>
            <div class="grillaDeUnaModalConfirmar">
                <div>
                    <svg 
                        fill="#ffa200" 
                        width="80px" 
                        height="80px" 
                        viewBox="0 0 1920 1920" 
                        xmlns="http://www.w3.org/2000/svg" 
                        stroke="#ffa200"
                    >
                    <!-- Elementos internos del SVG -->
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path 
                                d="M960 0c530.193 0 960 429.807 960 960s-429.807 960-960 960S0 1490.193 0 960 429.807 0 960 0Zm0 101.053c-474.384 0-858.947 384.563-858.947 858.947S485.616 1818.947 960 1818.947 1818.947 1434.384 1818.947 960 1434.384 101.053 960 101.053Zm-9.32 1221.49c-80.024 0-145.128 65.105-145.128 145.129 0 80.024 65.104 145.128 145.128 145.128 80.024 0 145.128-65.104 145.128-145.128 0-80.024-65.104-145.128-145.128-145.128Zm192.785-968.859h-385.57l93.901 851.327h197.768l93.901-851.327Z" 
                                fill-rule="evenodd"
                            ></path>
                        </g>
                    </svg>
                </div>
                <div>
                    <p class="letrasGrisesDeUna textoJustificadoDeUna"> Al cancelar la transacción en CURSO el dinero será REEMBOLSADO al CLIENTE y no se finalizará el proceso de FACTURACIÓN</p>
                    <br>
                    <p class="letrasGrisesDeUna"> Los tiempo de reembolso son los siguientes: </p>
                    <p><b> Banca Móvil: 3 días laborales</b></p>
                    <p><b> DeUna 2.0: 2 minutos</b></p>
                </div>
            </div>
            <br>
            <div class="grillaDeUna">
                <button class="boton-cancelar-deuna" disabled id="cancelarModalDeUna" onclick="cerrarModalDeUna(0)" >Sí, Cancelar Transacción </button>
                <button class="boton-esperar-pago-deuna" disabled id="esperarModalDeUna" onclick="reanudarTransaccionDeUna()" >Seguir esperando el pago</button>
            </div>
            <!--<button id="seguirModelDeUna" onclick="" >Dejar Pasar</button>-->
        </div>
    </div>
</div>

<div id="loadingDeUnaIntentos">
        <div id="myModalDeunaIntentos" class="modal-Deuna">
        <div class="modal-content-Deuna">
            <!--<span class="close-button-Deuna">&times;</span>-->
            <hr>
                <h3 style="margin-top: 2px; margin-bottom: 2px;">Espere por favor...</h3>
            <hr>
            <hr>
            <br>
            <br>
            <h4>Se está realizando la solicitud <br>de Pago al Servicio de DeUna.</h4>
            <br>
            <br>
            <br>
            <!--<button id="seguirModelDeUna" onclick="" >Dejar Pasar</button>-->
        </div>
    </div>
</div>



            <div id="numeroLocalizador" title="Ingrese el número de localizador" style="display:none;" align="center">
                <div class="preguntasTitulo">Ingrese el número de localizador</div>

                <div class="anulacionesSeparador">

                    <div>
                        <input  type="text" id="numero_localizador" style="height: 35px; width: 454px; font-size: 16px;"/>
                    </div>

                </div>
                <table id="tabla_credencialesAdmin" align="center">
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 7)">7</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 8)">8</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 9)">9</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarNumero(numero_localizador);">&larr;</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 4)">4</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 5)">5</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 6)">6</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarTodo(numero_localizador);">&lArr;</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 1)">1</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 2)">2</button></td>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 3)">3</button></td>
                        <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_validaNumeroLocalizador();">OK</button></td>
                    </tr>
                    <tr>
                        <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(numero_localizador, 0)">0</button></td>
                        <td colspan="4"><button style="font-size:45px;" class='btnVirtualCancelar' onclick="fn_cerrarNumeroLocalizador();">Cancelar</button></td>           
                    </tr>
                </table>
            </div>





                                                        <script type="text/javascript" src="../js/asset/jquery/jquery-3.6.0.min.js"></script>
                                                        <script type="text/javascript" src="../js/asset/iziModal/iziModal.js"></script>    
                                                        <link rel="stylesheet" type="text/css" href="../js/asset/iziModal/iziModal.min.css"/>
                                                        
                                                        <script type="text/javascript" src="../js/jquery.min.js"></script>	

                                                        <!--
                                                        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/js/iziModal.min.js"></script>
                                                        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/js/iziModal.js"></script> -->
                                                        <script type="text/javascript" src="../js/calendario.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_propina.js"></script>
                                                        <script type="text/javascript" src="../js/alertify.js"></script>
                                                        <script type="text/javascript" src="../js/jquery.countdown360.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_ValidaDocumento.js"></script>
                                                        <script type="text/javascript" src="../js/teclado.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_pagoServicioTarjeta.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_facturacion.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_clientes.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_credito_sin_cupon.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_pagoBanda.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_pagoTarjetaDinamico.js"></script>									  
                                                        <script type="text/javascript" src="../js/ajax_pagoMultiredes.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_descuentos.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_promociones.js"></script>
                                                        <script type="text/javascript" src="../js/teclado_facturacion.js"></script>
                                                            <!--<script type="text/javascript" src="../ordenpedido/scroll/mousewheel.js"></script>-->
                                                        <script type="text/javascript" src="../js/mousewheel.js"></script>
                                                        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
                                                        <script type="text/javascript" src="../js/tecladoCliente.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_agregadores.js"></script>

                                                        <script type="text/javascript" src="../js/CryptoJS.js"></script>

                                                        <script type="text/javascript" src="../js/ajax_payphoneEventos.js"></script>
                                                        <script type="text/javascript" src="../js/js_payphoneApp.js"></script>
                                                        <script type="text/javascript" src="../js/js_payphoneLink.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
                                                        <script type="text/javascript" src="../js/kds.js"></script>
                                                        <script type="text/javascript" src="../js/ajax_api_masterdataclientes.js"></script>
<script>
    // Obtiene el modal
    var modalDeuna = document.getElementById("myModal-Deuna");

    // Obtiene el botón que abre el modal
    var btnDeuna = document.getElementById("openModalButton");

    // Obtiene el elemento <span> que cierra el modal
    var spanDeuna = document.getElementsByClassName("close-button")[0];

    // Cuando el usuario haga clic en el botón, abre el modal 
    // btnDeuna.onclick = function() {
    // modalDeuna.style.display = "block";
    // }

    // Cuando el usuario haga clic en <span> (x), cierra el modal
    // spanDeuna.onclick = function() {
    // modal.style.display = "none";
    // }

    // Cuando el usuario haga clic en cualquier lugar fuera del modal, cierra el modal
    window.onclick = function(event) {
    if (event.target == modalDeuna) {
        modalDeuna.style.display = "none";
    }
    }
</script>
<!--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>-->
<!-- <link rel="stylesheet" type="text/css" href="../css/sweetAleter1.css" />
<link rel="stylesheet" type="text/css" href="../css/sweetAleter2.css" /> -->
<script type="text/javascript" src="../js/asset/sweetalert2-9/sweetalert2.js"></script>

<script type="text/javascript" src="../js/toastify-js.js"></script>

<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>-->
</body>


                                                        </html>
                                                    <?php } ?>
