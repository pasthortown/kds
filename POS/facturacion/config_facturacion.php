<?php

session_start();
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Archivo de Configuración para////////////
/////////////////////Facturación//////////////////////////////
///////TABLAS INVOLUCRADAS:///////////////////////////////////
///////FECHA CREACION: 10-02-2014/////////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: funcionalidad de la pantalla/
///////FECHA ULTIMA MODIFICACION:12/05/2015///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: Anulacion de formas de pago//
//////////////////////////////////////////////////////////////
include("../system/conexion/clase_sql.php");
include("../clases/clase_facturacion.php");
include('../clases/clase_fidelizacionAuditoria.php');
include("../soap/lib/nusoap.php");

//Clases para consumo de url configuradas en base de datos
include_once "../clases/clase_webservice.php";
include_once "../clases/clase_execWebService.php";
//Canje de puntos
include_once "../resources/module/fidelizacion/CanjePuntos.php";
//OAuth token
require_once('../resources/module/fidelizacion/Token.php');
require_once('../resources/module/fidelizacion/TokenManager.php');
$lc_facturas = new facturas();
$ip = $_SESSION['direccionIp'];
$usuario = $_SESSION['usuarioId'];
$restaurante = $_SESSION['rstId'];
$perfil = $_SESSION['perfil'];
$cadena = $_SESSION['cadenaId'];
$idEstacion = $_SESSION['estacionId'];
$periodo = $_SESSION['IDPeriodo'];
$controlEstacion = $_SESSION['IDControlEstacion'];
$tipoServicio = $_SESSION['TipoServicio'];
$objExecuteWs = new ExecWebService();
$servicioWebObj = new webservice();

// Inicio la clase token OAuth
$tokenLoyalty = new TokenLoyalty(); // ******* V2 FIDELIZACION *******
$loyaltyTokensManager = new TokenManager();
if (htmlspecialchars(isset($_GET["fac_listaTotales"]))) {
    $lc_condiciones[1] = htmlspecialchars($_GET["codigodelaFactura"]);
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = $cadena;
    print $lc_facturas->fn_consultar("fac_listaTotales", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["insertarRequerimientoPinpadProduccion"]))) {
    $lc_condiciones[1] = htmlspecialchars($_POST["cfacPinpadP"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["formaPagoIDPinpadP"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["valorTransaccionPinPadP"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["prop_valorPinpadP"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["tipoEnvioPinPadP"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["tipoTransaccionPinpadP"]);
    $lc_condiciones[7] = $restaurante;
    $lc_condiciones[8] = $idEstacion;
    $lc_condiciones[9] = $usuario;
    print $lc_facturas->fn_insertarRequerimientoPinpadUnired($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaFormaBinDatafast"]))) {
    $lc_condiciones[1] = htmlspecialchars($_GET["binTarjetaDatafast"]);
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = $ip;
    print $lc_facturas->fn_consultar("validaFormaBinDatafast", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ticketPromedio"]))) {
    $lc_condiciones[1] = $idEstacion;
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = $cadena;
    print $lc_facturas->fn_consultar("ticketPromedio", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["aplicaPagoPredeterminado"]))) {
    $lc_condiciones[1] = $idEstacion;
    $lc_condiciones[3] = $cadena;
    $lc_condiciones[4] = htmlspecialchars($_GET["es_menu_agregador"]);
    print $lc_facturas->fn_consultar("aplicaPagoPredeterminado", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["PromocionesMovistar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
    print $lc_facturas->fn_consultar("PromocionesMovistar", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["checkTransferencia"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["codRestaurante"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["codCadena"]);
    print $lc_facturas->fn_consultar("checkTransferencia", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["SetQRPromocionesMovistar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["QRData"]);
    print $lc_facturas->fn_consultar("SetQRPromocionesMovistar", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["auditoria_cupones_movistar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["atran_modulo"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["atran_descripcion"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["atran_accion"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["atran_varchar1"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["atran_varchar2"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["IDUsersPos"]);
    print $lc_facturas->fn_consultar("auditoria_cupones_movistar", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["consumir_ws"]))) {
    $url = htmlspecialchars($_GET['url_data']);
    $codFactura = htmlspecialchars($_GET['codFactura']);
    $codRestaurante = htmlspecialchars($_GET['codRestaurante']);
    $codCadena = htmlspecialchars($_GET['codCadena']);
    $address = $url . '?codFactura=' . $codFactura . '&codCadena=' . $codCadena . '&codRestaurante=' . $codRestaurante;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $address,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json",
            "Content-Length: 0"
        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = ["status" => "error comm", "message" => curl_error($curl)];
    }
    curl_close($curl);
    print $response;
} else if (htmlspecialchars(isset($_GET["consultaIdSWtimeoutBanda"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["movtimeOutBanda"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["accionSwtTimeouBanda"]);
    $lc_condiciones[2] = $ip;
    print $lc_facturas->fn_consultar("consultaIdSWtimeoutBanda", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["verificaBinExistenteSwt"]))) {
    $lc_condiciones[1] = htmlspecialchars($_GET["caracteresTarjeta"]);
    print $lc_facturas->fn_consultar("verificaBinExistente", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaColeccionEstacionTipoEnvio"]))) {
    $lc_condiciones[1] = $idEstacion;
    $lc_condiciones[2] = $cadena;
    print $lc_facturas->fn_consultar("validaColeccionEstacionTipoEnvio", $lc_condiciones);
} else if (isset($_POST["cargaCadenasEmpresa"])) {
    $lc_condiciones[0] = htmlspecialchars($_POST["documentoEmpresa"]);
    $lc_condiciones[1] = $usuario;
    print $lc_facturas->fn_cargaCadenaEmpresa($lc_condiciones);
} else if (isset($_POST["cargaRstCdn"])) {
    $lc_condiciones[0] = htmlspecialchars($_POST["rstI"]);
    $lc_condiciones[1] = $usuario;
    print $lc_facturas->fn_cargaRestauranteCadena($lc_condiciones);

    /* se consume WS de clientes */
} else if (htmlspecialchars(isset($_GET["buscaClienteAx"]))) {
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["desCliAx"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["banderaC"]);
    print $lc_facturas->fn_consultar("buscaClienteAx", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["activaOpcionCobroTarjeta"]))) {
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["opcionSwt"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[4] = $idEstacion;
    print $lc_facturas->fn_consultar("activaOpcionCobroTarjeta", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["verificaConfiguracionSWT"]))) {
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["SWTaccionconfiguracion"]);
    $lc_condiciones[3] = $idEstacion;
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoConfiguracionSWT"]);
    print $lc_facturas->fn_consultar("verificaConfiguracionSWT", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["consultaAgrupacionSWT"]))) {
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["SWTaccion"]);
    $lc_condiciones[3] = $idEstacion;
    print $lc_facturas->fn_consultar("consultaAgrupacionSWT", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["imprimirOrden"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odpOrden"]);
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[4] = 0;
    print $lc_facturas->fn_consultar("imprimirOrden", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["imprimirPromociones"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odpOrden"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dop_id"]);
    print $lc_facturas->fn_consultar("imprimirPromociones", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargaTeclasEmail"]))) {
    $lc_condiciones[0] = $cadena;
    print $lc_facturas->fn_consultar("cargaTeclasEmail", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaDetalleEnFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigodefactura"]);
    print $lc_facturas->fn_consultar("validaDetalleEnFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargaBilletes"]))) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["es_menu_agregador"]);
    print $lc_facturas->fn_consultar("cargaBilletes", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validarUsuarioAdministrador"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_Admin"]);
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = 'B';
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = htmlspecialchars($_GET["facturaAuditoria"]);
    print $lc_facturas->fn_consultar("validarUsuarioAdministrador", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["formaPago"]))) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = htmlspecialchars($_GET["es_menu_agregador"]);
    print $lc_facturas->fn_consultar("formaPago", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["consulta_cancelacionTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["can_codFact"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["can_idPago"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["banderaBuscaCancelacion"]);
    print $lc_facturas->fn_consultar("consulta_cancelacionTarjeta", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["anula_formaPagoEfectivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["anu_codFact"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["anu_idPago"]);
    $lc_condiciones[2] = $usuario;
    print $lc_facturas->fn_consultar("anula_formaPagoEfectivo", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["anula_formaPagoCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["anu_codFactCredito"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["anu_idPagoCredito"]);
    $lc_condiciones[2] = $usuario;
    print $lc_facturas->fn_consultar("anula_formaPagoCredito", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["obtieneTotalApagar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ob_codFact"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ob_idPago"]);
    print $lc_facturas->fn_consultar("obtieneTotalApagar", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validarUsuarioCreditoSinCupon"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_claveSinCupon"]);
    $lc_condiciones[2] = 'A';
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = '0';
    print $lc_facturas->fn_consultar("validarUsuarioCreditoSinCupon", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ingresaFormaPagoCreditoSinCupon"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codFactCredito"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["fmpIdTarjetaCredito"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["fmpNumSegtarCredito"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["frmPagoTotalCredito"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fctTotalCredito"]);
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $ip;
    $lc_condiciones[7] = $usuario;
    $lc_condiciones[8] = htmlspecialchars($_GET["SwtTipoCredito"]);
    $lc_condiciones[9] = 0;
    print $lc_facturas->fn_consultar("insertarFormaPago", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ingresaCanalMovimientoCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["FactCreditoCanal"]);
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $usuario;
    print $lc_facturas->fn_consultar("ingresaCanalMovimientoCredito", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaExisteFormaPagoSalir"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["factAevaluar"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dop_cuenta"]);
    print $lc_facturas->fn_consultar("validaExisteFormaPagoSalir", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaSalirOrden"]))) {
    $lc_condiciones[0] = $restaurante;
    print $lc_facturas->fn_consultar("validaSalirOrden", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["actualiza_estados_OrdenYfactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["mesaF"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odpId"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["dop_cuenta"]);
    print $lc_facturas->fn_consultar("actualiza_estados_OrdenYfactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["listaFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_facturas->fn_consultar("listaFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["actualizaFacturacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['nuFactu']);
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $usuario;
    print $lc_facturas->fn_consultar("actualizaFacturacion", $lc_condiciones);
} else if (isset($_POST["autoConsumoCupon"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_POST['IDCabeceraOrdenPedido'];
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $_POST['dop_cuenta'];
    $lc_condiciones[4] = $idEstacion;
    $lc_condiciones[5] = $periodo;
    $lc_condiciones[6] = $controlEstacion;
    $lc_condiciones[7] = $_POST['status_cupon'];
    $lc_condiciones[8] = $_POST['status_pago'];
    print $lc_facturas->fn_consultar("autoConsumoCupon", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["armaTramaSWTbanda"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['tipoTransaccion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['numMovimiento']);
    $lc_condiciones[2] = htmlspecialchars($_GET['formaIdPagoFact']);
    $lc_condiciones[3] = $idEstacion; //$_SESSION['estacionId'];
    $lc_condiciones[4] = $usuario; //$_SESSION['usuarioId'];
    $lc_condiciones[5] = $restaurante; //$_SESSION['rstId'];
    $lc_condiciones[6] = htmlspecialchars($_GET['tipoTarjeta']);
    $lc_condiciones[7] = htmlspecialchars($_GET['trackTarjeta']);
    $lc_condiciones[8] = htmlspecialchars($_GET['cvvtarjeta']);
    $lc_condiciones[9] = htmlspecialchars($_GET['prop_valor']);
    $lc_condiciones[10] = htmlspecialchars($_GET['valorTransaccionBanda']);
    $lc_condiciones[11] = htmlspecialchars($_GET['tipoEnvioBanda']);
    print $lc_facturas->fn_consultar("armaTramaSWTbanda", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["claveAcceso"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['factt']);
    $lc_condiciones[1] = htmlspecialchars($_GET['char']);
    print $lc_facturas->fn_consultar("claveAcceso", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validaItemPagado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $_SESSION['kioskoActivo'] = null;
    $_SESSION['reimpresionKiosko'] = null;
    $_SESSION['pickupActivo'] = null;
    print $lc_facturas->fn_consultar("validaItemPagado", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["obtenerurlsplit"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    print $lc_facturas->fn_consultar("obtenerurlsplit", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["autoCompletar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["term"]);
    print $lc_facturas->fn_consultarCliente($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["resumenFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfactura"]);
    print $lc_facturas->fn_consultar("resumenFormaPago", $lc_condiciones);
} else if (isset($_POST["TieneDivisionCuenta"])) {
    $lc_condiciones[0] = $_POST["odp_id"];
    print $lc_facturas->fn_consultar("TieneDivisionCuenta", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertarFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["numCuenta"]);
    $lc_condiciones[4] = $idEstacion;
    $lc_condiciones[5] = $periodo;
    $lc_condiciones[6] = $controlEstacion;
    $lc_condiciones[7] = isset($_GET["divisionCuenta"]) ? htmlspecialchars($_GET["divisionCuenta"]) : 0;
    $lc_condiciones[8] = isset($_GET["recargaPantalla"]) ? htmlspecialchars($_GET["recargaPantalla"]) : 0;
    /*
      if ($tipoServicio == 1) {
      if ($lc_condiciones[7] == 1) {
      print $lc_facturas->fn_consultar("insertarFactura_FS", $lc_condiciones);
      } else {
      print $lc_facturas->fn_consultar("insertarFactura", $lc_condiciones);
      }
      } if ($tipoServicio == 2) {
      print $lc_facturas->fn_consultar("insertarFactura_FS", $lc_condiciones);
      }
     */
    print $lc_facturas->fn_consultar("insertarFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET['RegistroCanjePuntosMasivo']))) {
    $lc_condiciones[0] = $restaurante;
    if (isset($_GET["uid"], $_GET["appedir"], $_GET["factura"], $_GET['IDUsersPos'])) {
        $headers = apache_request_headers();
		if (isset($headers['Authorization'])) {
			$barer = explode(' ', $headers['Authorization']);
			$authorization = $barer[1];
		}else{
			$authorization=$_GET['authorization'];
		}
        $lc_condiciones[1] = $_GET["uid"];
        $lc_condiciones[2] = $_GET["appedir"];
        $lc_condiciones[3] = $_GET["factura"];
        $lc_condiciones[4] = $authorization;
        $lc_condiciones[5] = 'CASH';
        $lc_condiciones[6] = $_GET['IDUsersPos'];
        $lc_condiciones[7] = true; //$_GET['redimirPuntos']

        //print $lc_facturas->fn_consultar("valida_tipo_facturacion_puntos", $lc_condiciones);
        $auditoria = new Auditoria();
        $proceso = 'Fidelizacion';
        if(isset($_SESSION['estadoMasivo']) && ($_SESSION["estadoMasivo"]==="true" || $_SESSION["estadoMasivo"]===true)){
            $transaccion = (object)$auditoria->solicitarSecuencialProceso($restaurante, $proceso, $usuario);
        }else{
            $transaccion=(object)array(
                'estadoProceso' => 'Iniciado',
                'secuencia' => 'sn'
            );
        }
        
        $lc_condiciones[3] = isset($_GET["facturaCanje"]) && $_GET["facturaCanje"]!==""?$_GET["facturaCanje"]:$_GET["factura"];
        $response=$lc_facturas->fn_consultar("valida_tipo_facturacion_puntos", $lc_condiciones);
        $response=json_decode($response);
        $response->redemptionCode = $transaccion->secuencia;
        $response->quantityPoints = $_SESSION['cantidadPuntosCanjeados'];
        $response->totalBillPoints = $_SESSION['totalFacturaPuntosCanjeados'];
        $response->tipoCanjeFinal = $_SESSION['tipoCanjeFinal'];
        //Finaliza Transaccion
        if(isset($_SESSION['estadoMasivo']) && ($_SESSION["estadoMasivo"]==="true" || $_SESSION["estadoMasivo"]===true)){
            $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
        }
        print(json_encode($response));
    }else{
        $response=$lc_facturas->fn_consultar("valida_tipo_facturacion_puntos", $lc_condiciones);
    }

} else if (htmlspecialchars(isset($_GET["valida_tipo_facturacion"]))) {
    $lc_condiciones[0] = $restaurante;
    if (isset($_GET["uid"], $_GET["appedir"], $_GET["factura"], $_GET['IDUsersPos'])) {
        $headers = apache_request_headers();
		if (isset($headers['Authorization'])) {
			$barer = explode(' ', $headers['Authorization']);
			$authorization = $barer[1];
		}else{
			$authorization=$_GET['authorization'];
		}
        $lc_condiciones[1] = $_GET["uid"];
        $lc_condiciones[2] = $_GET["appedir"];
        $lc_condiciones[3] = $_GET["factura"];
        $lc_condiciones[4] = $authorization;
        $lc_condiciones[5] = 'CASH';
        $lc_condiciones[6] = $_GET['IDUsersPos'];
        $lc_condiciones[7] = true; //$_GET['redimirPuntos']

        //Estado Transaccionss
        $auditoria = new Auditoria();
        $proceso = 'Fidelizacion';
        
        if(isset($_SESSION['estadoMasivo']) && ($_SESSION["estadoMasivo"]==="true" || $_SESSION["estadoMasivo"]===true)){
            $transaccion = (object)$auditoria->solicitarSecuencialProceso($restaurante, $proceso, $usuario);
        }else{
            $transaccion=(object)array(
                'estadoProceso' => 'Iniciado',
                'secuencia' => 'sn'
            );
        }
        
        $lc_condiciones[3] = isset($_GET["facturaCanje"]) && $_GET["facturaCanje"]!=""?$_GET["facturaCanje"]:$_GET["factura"];
        $response=$lc_facturas->fn_consultar("valida_tipo_facturacion", $lc_condiciones);
        $response=json_decode($response);
        $response->redemptionCode = $transaccion->secuencia;
        $response->quantityPoints = $_SESSION['cantidadPuntosCanjeados'];
        $response->totalBillPoints = $_SESSION['totalFacturaPuntosCanjeados'];
        $response->tipoCanjeFinal = $_SESSION['tipoCanjeFinal'];
        //Finaliza Transaccion
        if(isset($_SESSION['estadoMasivo']) && ($_SESSION["estadoMasivo"]==="true" || $_SESSION["estadoMasivo"]===true)){
            $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
        }
    }else{
        $response=$lc_facturas->fn_consultar("valida_tipo_facturacion", $lc_condiciones);
    }

    print(json_encode($response));
} else if (htmlspecialchars(isset($_GET["actualizaTipoFacturacion"]))) {
    $lc_condiciones[0] = $_GET["idDfactura"];
    print $lc_facturas->fn_consultar("actualizaTipoFacturacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertarFormaPagoCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["fctCredito_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["frmPagoCredito_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["frmPagoCredito_numSeg"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["frmPagoBilleteCredito"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fctTotalCredito"]);
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $ip;
    $lc_condiciones[7] = $usuario;
    $lc_condiciones[8] = htmlspecialchars($_GET["tfpSwtransaccionalCredito"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["cliCredito"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["banderaCredito"]);
    $lc_condiciones[11] = htmlspecialchars($_GET["opcionFp"]);
    $lc_condiciones[12] = htmlspecialchars($_GET["observacion"]);
    $lc_condiciones[13] = htmlspecialchars($_GET["documentoClienteAX"]);
    $lc_condiciones[14] = htmlspecialchars($_GET["telefonoClienteAx"]);
    $lc_condiciones[15] = htmlspecialchars($_GET["direccionClienteAx"]);
    $lc_condiciones[16] = htmlspecialchars($_GET["correoClienteAx"]);
    $lc_condiciones[17] = isset($_GET["tipoIdentificacionCLienteExt"]) ? htmlspecialchars($_GET["tipoIdentificacionCLienteExt"]) : '';
    $lc_condiciones[18] = htmlspecialchars($_GET["nombreCLienteCredito"]);
    $lc_condiciones[19] = htmlspecialchars($_GET["tipoCliCredito"]);
    $lc_condiciones[20] = htmlspecialchars($_GET["banderaVitality"]);
    $lc_condiciones[21] = isset($_GET["valorCampoCodigo"]) ? htmlspecialchars($_GET["valorCampoCodigo"]) : '';
    print $lc_facturas->fn_consultar("insertarFormaPagoCredito", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertarFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["fct_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["frmPago_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["frmPago_numSeg"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["frmPagoBillete"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fctTotal"]);
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $ip;
    $lc_condiciones[7] = $usuario;
    $lc_condiciones[8] = htmlspecialchars($_GET["tfpSwtransaccional"]);
    $lc_condiciones[9] = 0;
    print $lc_facturas->fn_consultar("insertarFormaPago", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ingresaFormaPagoTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codFactTarjeta"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["fmpIdTarjeta"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["fmpNumSegtar"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["frmPagoTotalTarjeta"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fctTotalTarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["propina_valor"]);
    $lc_condiciones[6] = $ip;
    $lc_condiciones[7] = $usuario;
    $lc_condiciones[8] = htmlspecialchars($_GET["SwtTipo"]);
    $lc_condiciones[9] = isset($_GET["aplicaServicioV2"]) ? $_GET["aplicaServicioV2"] : 0;
    print $lc_facturas->fn_consultar("insertarFormaPago", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["actualizarFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cliente_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["codFactura"]);
    $lc_condiciones[2] = $usuario;
    // Finalizar Variables sesion Cliente
    if (isset($_SESSION["fdznDocumento"])) {
        unset($_SESSION['codigoAppActivo']);
        unset($_SESSION['fdznDocumento']);
        unset($_SESSION['fdznNombres']);
    }
    print $lc_facturas->fn_consultar("actualizarFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cancelaTarjetaForma"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cancela_codFact"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cancela_idPago"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["can_respuesta"]);
    $lc_condiciones[3] = $ip;
    $lc_condiciones[4] = $usuario;
    print $lc_facturas->fn_consultar("cancelaTarjetaForma", $lc_condiciones);
} else if (isset($_POST["cargaConceptosAyuda"])) {
    $lc_condiciones[0] = $cadena;
    print $lc_facturas->fn_cargaConceptosAyuda($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["consultaSwtTransaccional"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $usuario;
    print $lc_facturas->fn_consultar("consultaSwtTransaccional", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["consultaSwtTransaccionalCancelacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cancelacion_codFact"]);
    $lc_condiciones[1] = $usuario;
    print $lc_facturas->fn_consultar("consultaSwtTransaccionalCancelacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["anu_insertarRequerimientoAutorizacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["anulacion_cfac"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["anulacion_formaPagoID"]);
    $lc_condiciones[2] = $_SESSION['estacionId'];
    $lc_condiciones[3] = $_SESSION['usuarioId'];
    $lc_condiciones[4] = $_SESSION['rstId'];
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '03';
    print $lc_facturas->fn_consultar("insertarRequerimientoAutorizacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["TransaccionalSWT"]))) {
    $lc_condiciones[0] = $ip;
    print $lc_facturas->fn_consultar("TransaccionalSWT", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["clienteInfo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["clienteCedula"]);
    print $lc_facturas->fn_consultar("clienteInfo", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertarRequerimientoAutorizacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["formaPagoID"]);
    $lc_condiciones[2] = $_SESSION['estacionId'];
    $lc_condiciones[3] = $_SESSION['usuarioId'];
    $lc_condiciones[4] = $_SESSION['rstId'];
    $lc_condiciones[5] = htmlspecialchars($_GET["prop_valor"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["valorTransaccionPinPad"]);
    $lc_condiciones[7] = 1; //$_GET["tipoEnvioPinPad"];
    $lc_condiciones[8] = htmlspecialchars($_GET["tipoTransaccionPinpad"]);
    print $lc_facturas->fn_consultar("insertarRequerimientoAutorizacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["valida_abreFinTransaccion"]))) {
    $lc_condiciones[0] = $_SESSION['rstId'];
    print $lc_facturas->fn_consultar("valida_abreFinTransaccion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["esperaRespuestaRequerimientoAutorizacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
    print $lc_facturas->fn_consultar("esperaRespuestaRequerimientoAutorizacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["esperaRespuestaRequerimientoAutorizacionPinpadMultired"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfacMultired"]);
    print $lc_facturas->fn_esperaRespuestaPinpadMultired($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertaCanalAperturaCajon"]))) {
    $lc_condiciones[0] = $usuario;
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["banderaCajon"]);
    $lc_condiciones[3] = $usuario;
    print $lc_facturas->fn_consultar("insertaCanalAperturaCajon", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["EstadoAbrirCajon"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["op_id"]);
    print $lc_facturas->fn_consultar("EstadoAbrirCajon", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["grabacanalmovimientoVoucher"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["respuesta"]);
    $lc_condiciones[3] = $restaurante;
    $lc_condiciones[4] = $idEstacion;
    print $lc_facturas->fn_consultar("grabacanalmovimientoVoucher", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["eliminaformadepago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfacIDee"]);
    print $lc_facturas->fn_consultar("eliminaformadepago", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["inserta_canalComprobante"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigoFactura"]);
    print $lc_facturas->fn_consultar("inserta_canalComprobante", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["grabacanalmovimientoImpresionFactura"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["idfactura"]);
    print $lc_facturas->fn_consultar("grabacanalmovimientoImpresionFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["grabacanalmovimientoImpresionFacturaElectronica"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["idfactura"]);
    print $lc_facturas->fn_consultar("grabacanalmovimientoImpresionFacturaElectronica", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["lee_canalXMLfirmado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigoFacturaxml"]);
    print $lc_facturas->fn_consultar("lee_canalXMLfirmado", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["visorCabeceraFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_facturas->fn_consultar("visorCabeceraFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["visorDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_facturas->fn_consultar("visorDetalleFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["totalDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_facturas->fn_consultar("totalDetalleFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["formasPagoDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_facturas->fn_consultar("formasPagoDetalleFactura", $lc_condiciones);
} else if (isset($_POST["biometrika"])) {
    $wsdl = "http://200.124.230.154:8200/ABIS12QRF.Integracion/RCService.asmx?WSDL";
    $client = new nusoap_client($wsdl, 'wsdl');
    $param = array('trama' => $_POST["hid_bio"], 'identificacion' => $_POST["cedula_bio"], 'operacion' => 2);
    $Confirmacion = $client->call('ProcesarWU2', $param);
    $respuesta = $Confirmacion['ProcesarWU2Result'];
    $respuesta = explode("|", $respuesta);
    $respuesta = $respuesta[0];
    $res2[] = array('respuesta' => $respuesta);
    print json_encode($res2);
} else if (htmlspecialchars(isset($_GET["obtenerMesa"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['rst_id']);
    $lc_condiciones[1] = $idEstacion;
    if (htmlspecialchars(isset($_GET['odp_id']))) {
        $lc_condiciones[2] = htmlspecialchars($_GET['odp_id']);
    } else {
        $lc_condiciones[2] = '';
    }
    print $lc_facturas->fn_consultar("obtenerMesa", $lc_condiciones);
} else if (isset($_POST["verificarFormasPagoAplicadasFacturaPayPhone"])) {
    $lc_condiciones[0] = $_POST["factura"];
    print $lc_facturas->fn_consultar("verificarFormasPagoAplicadasFacturaPayPhone", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["validaCuadreFormasPago"]))) {
    $_SESSION['kioskoActivo'] = null;
    $_SESSION['reimpresionKiosko'] = null;
    $_SESSION['pickupActivo'] = null;
    $lc_condiciones[1] = htmlspecialchars($_POST["facturaAvalidar"]);
    print $lc_facturas->fn_validaCuadreFormasPago($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["url_cuentas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp"]);
    print $lc_facturas->fn_url_cuentas($lc_condiciones);

//CODIGO PARA LISTAR LOS DESCUENTOS
} else if (htmlspecialchars(isset($_GET["consultaDescuentos"]))) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = ($_GET["cfac_id"]);
    print $lc_facturas->fn_lista_descuentos($lc_condiciones);

//CODIGO PARA AGREGAR DESCUENTOS
} else if (htmlspecialchars(isset($_GET["agregarDescuento"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = ($_GET["cfac_id"]);
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = ($_GET["desc_id"]);
    print $lc_facturas->fn_agrega_descuentos($lc_condiciones);

//VALIDA LAS CREDENCIALES DEL ADMINISTRADOR
} else if (htmlspecialchars(isset($_GET["validarUsuarioDescuentos"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_clave"]);
    print $lc_facturas->fn_validarUsuarioDescuentos($lc_condiciones);

//HABILITA BOTONES (DESCUENTOS - ELIMINAR DESCUENTOS)SEGUN PERFIL
} else if (htmlspecialchars(isset($_GET["cargarAccesosPerfil"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $perfil;
    print $lc_facturas->fn_cargarAccesosPerfil($lc_condiciones);

//CODIGO PARA ELIMINAR DESCUENTOS
} else if (htmlspecialchars(isset($_GET["eliminarDescuento"]))) {
    $lc_condiciones[0] = ($_GET["cfac_id"]);
    $lc_condiciones[1] = ($_GET["idCabeceraOrdenPedido"]);
    $lc_condiciones[2] = $cadena;
    $lc_condiciones[3] = $restaurante;
    print $lc_facturas->fn_eliminar_descuentos($lc_condiciones);

//CODIGO PARA LISTAR PRODUCTOS DISCRECIONALES
} else if (htmlspecialchars(isset($_GET["descuentosDiscrecionales"]))) {
    $lc_condiciones[0] = ($_GET["accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = ($_GET["cfac_id"]);
    print $lc_facturas->fn_descuentosDiscrecionales($lc_condiciones);

//CODIGO PARA AGREGAR DESCUENTOS DISCRECIONALES
} else if (isset($_POST["guardarDescuentosDiscrecionales"])) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = ($_POST["cfac_id"]);
    $lc_condiciones[3] = ($_POST["cadenaProductosDiscrecionales"]);
    print $lc_facturas->fn_guardarDescuentosDiscrecionales($lc_condiciones);

//CODIGO PARA LISTAR VALOR DISCRECIONAL
} else if (htmlspecialchars(isset($_GET["listaPorcentajesDiscrecionales"]))) {
    $lc_condiciones[0] = ($_GET["accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = $restaurante;
    $lc_condiciones[3] = ($_GET["cfac_id"]);
    print $lc_facturas->fn_listaPorcentajesDiscrecionales($lc_condiciones);

//CODIGO PARA LISTAR LOS DETALLES DEL DESCUENTO DISCRECIONAL
} else if (htmlspecialchars(isset($_GET["detallesDescuentosDiscrecionales"]))) {
    $lc_condiciones[0] = ($_GET["cfac_id"]);
    print $lc_facturas->fn_detallesDescuentosDiscrecionales($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["buscaPagoCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["factutraBuscaCredito"]);
    $lc_condiciones[1] = 1;
    print $lc_facturas->fn_buscaPagoCredito($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cierraFacturaConCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codFacturaTipoCredito"]);
    $lc_condiciones[1] = 1;
    $lc_condiciones[2] = htmlspecialchars($_GET["numeroDocumentoAx"]);
    $lc_condiciones[3] = $usuario;
    print $lc_facturas->fn_cierraFacturaConCredito($lc_condiciones);
} else if (isset($_POST["validaCuponAX"])) {
    $lc_condiciones[0] = $_POST["rs_id"];
    $lc_condiciones[1] = $_POST["idclienteax"];
    print $lc_facturas->fn_validaCuponAX($lc_condiciones);
} else if (isset($_POST["SepararCuentasAlExecerVoucher"])) {
    $lc_condiciones[0] = $_POST["idRest"];
    print $lc_facturas->fn_consultar("SepararCuentasAlExecerVoucher", $lc_condiciones);
} else if (isset($_POST["dividir"])) {
    $lc_condiciones[0] = $_POST["dop_id"];
    $lc_condiciones[1] = $_POST["cuenta1"];
    $lc_condiciones[2] = $_POST["cuenta2"];
    $lc_condiciones[3] = $_POST["limiteVoucher"];
    $lc_condiciones[4] = $_POST["parametrosVoucher"];
    print $lc_facturas->fn_consultar("dividir", $lc_condiciones);
} else if (isset($_POST["obtenerDatosVitalityFac"])) {
    $lc_condiciones[0] = $_POST["accion"];
    $lc_condiciones[1] = $_POST["cfac_id"];
    $lc_condiciones[2] = $_POST["cedulaCliente"];
    $lc_condiciones[3] = $_POST["codigoQRVitality"];
    print $lc_facturas->fn_consultar("obtenerDatosVitalityFac", $lc_condiciones);
} else if (isset($_POST["obtenerDatosEnvioPuntos"])) {
    $lc_condiciones[0] = $_POST["accion"];
    $lc_condiciones[1] = $_POST["cfac_id"];
    $lc_condiciones[2] = $_POST["cedulaCliente"];
    print $lc_facturas->fn_consultar("obtenerDatosEnvioPuntos", $lc_condiciones);
} else if (isset($_POST["Autoconsumo"])) {
    $cfac_id = $_POST["cfac_id"];
    $rst_id = $_POST["rst_id"];
    $secuencial = $_POST["secuencial"];
    $documentoCliente = $_POST["documentoCliente"];
    $nombreCliente = $_POST["nombreCliente"];
    $puntosCanjeados = $_POST["puntosCanjeados"];
    $marketingCost = isset($_POST["marketingCost"]) ? $_POST["marketingCost"] : 0;
    $storeCost = isset($_POST["storeCost"]) ? $_POST["storeCost"] : 0;
    print $lc_facturas->autoconsumo($cfac_id, $rst_id, $secuencial, $documentoCliente, $nombreCliente, $puntosCanjeados, $marketingCost, $storeCost);
} else if (isset($_POST["aplicaAcumulacionPuntos"])) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    $lc_condiciones[1] = $cadena;
    print $lc_facturas->fn_consultar("aplicaAcumulacionPuntos", $lc_condiciones);
} else if (isset($_POST['insertaDatosWS'])) {
    $lc_condiciones[0] = $_POST['txt_tipov_id']; //
    $lc_condiciones[1] = $_POST['txt_vae_cod'];
    $lc_condiciones[2] = $_POST['vae_cfac_id'];
    $lc_condiciones[3] = $_POST['vae_monto'];
    $lc_condiciones[4] = $_POST['vae_IDCliente']; //
    $lc_condiciones[5] = $_POST['vae_cdn_id'];
    $lc_condiciones[6] = $_POST['vae_rst_id'];

    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($lc_condiciones[6], 'VOUCHER', 'GUARDAR VOUCHER');
    $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSRetornaPrecios, //"http://192.168.100.186:8090/GuardarVoucher",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\"tipov_id\":\"" . $lc_condiciones[0] . "\",\n\"vae_cod\":\"" . $lc_condiciones[1] . "\",\n\"vae_cfac_id\":\"" . $lc_condiciones[2] . "\",\n\"vae_monto\":" . $lc_condiciones[3] . ",\n\"vae_IDCliente\":\"" . $lc_condiciones[4] . "\",\n\"vae_cdn_id\":" . $lc_condiciones[5] . ",\n\"vae_rst_id\":" . $lc_condiciones[6] . "\n}",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json" //,
            //  "postman-token: a64fa457-2870-9b1e-4a96-3c3b0388d84b"
        )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if (!$response) {
        print '{"estado":"0","mensaje":"Error"}';
    } else {
        print ($response);
    }
    /*     * *********************************************************** */
    /* CANJE DE PUNTOS COMUNICACIÓN CON WS BILLETERA DE PUNTOS      */
    /*     * *********************************************************** */
} else if (isset($_POST['RegistroCanjePuntos'])) {
    $result = new stdClass();
    $app = 'jvz';
    if (!empty($_SESSION['appid'])) {
        $app = $_SESSION['appid'];
    }

    $canjearPuntos = new CanjePuntos($restaurante, $app);
    $auditoria = new Auditoria();
    $respuesta = new stdClass();
    // Datos Cliente
    $clienteDocumento = $_SESSION['fb_document'];
    $cliente = $_SESSION['fb_name'];
    $accion = 'REDENCION DE PUNTOS';
    $proceso = 'Fidelizacion';
    //OPCIÓN SOLO VALIDA PARA CLIENTES REGISTRADOS
    if ($_SESSION['fb_status'] == 'REGISTERED') {

        //validar que la lectura del cliente haya sido con el QR
        if ($_SESSION['fb_security'] != null || $_SESSION['fb_security'] != "") {
            //Estado Transaccion
            $transaccion = (object)$auditoria->solicitarSecuencialProceso($restaurante, $proceso, $usuario);
            if ($transaccion->estadoProceso == 'Iniciado') {
                try {
                    $JSON = ($_POST['json']);
                    $obj = json_decode($JSON);
                    //Agregar código de seguridad para redención de puntos
                    $codigoSeguridad = $_SESSION['fb_security'];
                    $obj->token = $_SESSION['fb_security'];
                    $obj->redemptionCode = $transaccion->secuencia;
                    $obj->cashier = array('document' => $transaccion->cashierDocument, 'name' => $transaccion->cashierName);
                    //Convert to JSON
                    $JSON = json_encode($obj);
                    //Funciones para canje de puntos
                    $canjearPuntos = new CanjePuntos($restaurante, $app);
                    $lc_facturas->logProcesosFidelizacion('INICIO DE REQUEST CANJE', $accion, $restaurante, $cadena, $usuario, $JSON);
                    $respuesta = $canjearPuntos->canjear($JSON);
                    if(isset($respuesta->exception)){
                        $lc_facturas->logProcesosFidelizacion('EXCEPCION EN REQUEST CANJE', $accion, $restaurante, $cadena, $usuario, $respuesta->exceptionMessage);
                        $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                        $result->code = -1;
                    }else{
                        if ($respuesta->numberError == 0) {
                            $vectorDatos = @json_decode($respuesta->data);
                            if ($respuesta->httpStatus == 200) {
                                $_SESSION['fb_points'] = $vectorDatos->data->pointsByCustomer;
                                $_SESSION['fb_econtroDatos'] = 1;
                                $respuesta->request = $JSON;
                                //Finaliza Transaccion
                                $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                //Log Auditoria
                                $lc_facturas->logProcesosFidelizacion('Canje exitoso.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                //Devolver código de canje
                                $result = $vectorDatos;
                                $result->redemptionCode = $transaccion->secuencia;
                            } else {
                                // Obtener Errores de respuesta
                                if (isset($vectorDatos->errors)) {
                                    if (isset($vectorDatos->errors->token)) {
                                        $result->message = 'Código de seguridad no válido. Solicite al cliente que cierre la aplicación de Juan Valdez y vuelva a abrirla para obtener un nuevo código.';
                                        $result->code = 10001212;
                                    } else if (isset($vectorDatos->warning)) {
                                        $result->message = 'Alerta, por favor comunicarse con el soporte para validar este error.';
                                        $result->code = -1;
                                    } else {
                                        //Obtener error de forma dinámica
                                        $result->message = 'Error, servicio no disponible'; //Mensaje opcional
                                        $result->code = -1;
                                    }
                                    //Finaliza Transaccion
                                    $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                    //Log Auditoria
                                    $lc_facturas->logProcesosFidelizacion('Error Canje de puntos.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                } else {
                                    $lc_facturas->logProcesosFidelizacion('REQUEST ERROR CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                    $result->message = $vectorDatos->error;
                                    $result->code = -1;
                                }
                            }
                        } else {
                            //TIMEOUT: no cambio el estado de la transaccion para que el cliente pueda volver a intentar
                            $lc_facturas->logProcesosFidelizacion('ERROR EN PETICION CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            $result->message = 'Se ha encontrado un error en la solicitud al procesar el canje.';
                            $result->code = -1;
                        }
                    }


                } catch (\Exception $error) {
                    $lc_facturas->logProcesosFidelizacion('GLOBAL ERROR CANJE', $accion, $restaurante, $cadena, $usuario, $error->getMessage());
                    $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                    $result->code = -1;
                }
                //Validar solo el estado de la transacción
            } else {
                try {
                    $respuesta = $canjearPuntos->estadoCanje($transaccion->secuencia);
                    if(isset($respuesta->exception)){
                        $lc_facturas->logProcesosFidelizacion('EXCEPCION EN CONSULTAR ESTADO CANJE', $accion, $restaurante, $cadena, $usuario, $respuesta->exceptionMessage);
                        $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                        $result->code = -1;
                    }else{
                        if ($respuesta->numberError == 0) {
                            $vectorDatos = @json_decode($respuesta->data);
                            if ($respuesta->httpStatus == 200) {
                                $_SESSION['fb_points'] = $vectorDatos->data->pointsByCustomer; // ******* V1 FIDELIZACION *******
                                //$_SESSION['fb_points'] = $vectorDatos->data->pointsByCustomer; // ******* V2 FIDELIZACION *******

                                $_SESSION['fb_econtroDatos'] = 1;
                                //Guardar en los codigo de la transaccion
                                $respuesta->request = json_encode($transaccion->secuencia);
                                //LogAuditoria
                                $lc_facturas->logProcesosFidelizacion('Consultar estado.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                $result = $vectorDatos;
                                $result->redemptionCode = $transaccion->secuencia;
                            } else {
                                //Guardar en los codigo de la transaccion
                                $respuesta->request = json_encode($transaccion->secuencia);
                                //LogAuditoria
                                $lc_facturas->logProcesosFidelizacion('Error consultar estado', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                $result->message = 'Transacción no exitosa, por favor intente nuevamente.';
                                $result->code = -1;
                            }
                            //Finaliza Transaccion
                            $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                        }else{
                            $lc_facturas->logProcesosFidelizacion('Error consultar estado', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            $result->message = 'Transacción no exitosa, por favor intente nuevamente.';
                            $result->code = -1;
                        }
                    }
                } catch (\Exception $error) {
                    $lc_facturas->logProcesosFidelizacion('GLOBAL ERROR CONSULTACANJE', $accion, $restaurante, $cadena, $usuario, $error->getMessage());
                    $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                    $result->code = -1;
                }
            }
        } else {
            $result->message = 'CODIGO DE SEGURIDAD OBLIGATORIO.';
            $result->code = 10001212;
        }
    } else {
        $result->message = 'USUARIO BLOQUEADO.';
        $result->code = -1;
    }
    print json_encode($result);
    
} else if (isset($_POST["actualizarCodigoSeguridadCliente"])) {
    $_SESSION['fb_security'] = $_POST["codigo"];
    $respuesta["codigo"] = $_SESSION['fb_security'];
    print json_encode($respuesta);
} else if (isset($_POST["obtenerDatosTotalesClienteResturante"])) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    $lc_condiciones[1] = $_POST["rst_id"];
    $lc_condiciones[2] = $_POST["cdn_id"];
    print $lc_facturas->fn_consultar("obtenerDatosTotalesClienteResturante", $lc_condiciones);
} else if (isset($_POST["obtenerDatosFacturaProductos"])) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    print $lc_facturas->fn_consultar("obtenerDatosFacturaProductos", $lc_condiciones);
} else if (isset($_POST['ValidarFacturaTarjeta'])) {
    $cabecera_odp = $_POST["odp_id"];
    print $lc_facturas->fn_ValidarFacturaTarjeta($cabecera_odp);
} else if (isset($_POST["obtenerDatosFacturaFormaPago"])) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    print $lc_facturas->fn_consultar("obtenerDatosFacturaFormaPago", $lc_condiciones);
} else if (isset($_POST["PayPhoneObtenerClaves"])) {
    $lc_condiciones[0] = $_POST["restaurante"];

    print $lc_facturas->fn_consultar("PayPhoneObtenerClaves", $lc_condiciones);
} else if (isset($_POST["PayPhoneEnviarTransaccion"])) {

    $JSON = json_encode($_POST['json']);
    $TOKEN = ($_POST['token']);

    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'CREAR TRANSACCION');
    $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];


    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSRetornaPrecios,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 40,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $JSON,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $TOKEN"
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $array = [
            "message" => curl_error($curl),
            "statusCode" => "500",
        ];

        print (json_encode($array));
        curl_close($curl);
    } else {
        print ($response);
        curl_close($curl);
    }
} else if (isset($_POST["PayPhoneObtenerDatosTransaccion"])) {

    $transactionId = ($_POST['transactionId']);
    $TOKEN = ($_POST['token']);

    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'OBTENER TRANSACCION');
    $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];
    $curl = curl_init();

    // if ($curl === false) {
    //      throw new Exception('failed to initialize');
    // }

    curl_setopt_array($curl, array(
        CURLOPT_URL => "$urlWSRetornaPrecios" . $transactionId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 40,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $TOKEN",
            "Cookie: ARRAffinity=46b00f471cd74e0d2ed86afd100b04df09570f8099612a7c7a6d4e550fa14999"
        ),
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        throw new Exception(curl_error($curl), curl_errno($curl));
    }
    curl_close($curl);
    print ($response);
} else
    if (isset($_POST["payphoneReverse"])) {

        $transaccionID = $_POST["transaccionId"];
        $token = $_POST["token"];

        $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'REVERSAR TRANSACCION');
        $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlWSRetornaPrecios,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n  \"id\": $transaccionID\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/json",
                "Cookie: ARRAffinity=46b00f471cd74e0d2ed86afd100b04df09570f8099612a7c7a6d4e550fa14999"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        print ($response);
    } else
        if (isset($_POST["payphoneReverseClientID"])) {

            $clientIdTransaccion = $_POST["clientIdTransaccion"];
            $token = $_POST["token"];

            $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'REVERSAR CLIENTE ID TRANSACCION');
            $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $urlWSRetornaPrecios,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\r\n  \"clientId\": \"$clientIdTransaccion\"\r\n}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    "Content-Type: application/json",
                    "Cookie: ARRAffinity=46b00f471cd74e0d2ed86afd100b04df09570f8099612a7c7a6d4e550fa14999"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            print ($response);
        } else if (isset($_POST["PayPhoneGuardarRespuestaAutorizacion"])) {

            $lc_condiciones[0] = $_POST["rsaut_trama"];
            $lc_condiciones[1] = $_POST["ttra_codigo"];
            $lc_condiciones[2] = $_POST["cres_codigo"];
            $lc_condiciones[3] = $_POST["rsaut_respuesta"];
            $lc_condiciones[4] = $_POST["rsaut_secuencial_transaccion"];
            $lc_condiciones[5] = $_POST["rsaut_hora_autorizacion"];
            $lc_condiciones[6] = $_POST["rsaut_fecha_autorizacion"];
            $lc_condiciones[7] = $_POST["rsaut_numero_autorizacion"];
            $lc_condiciones[8] = $_POST["rsaut_terminal_id"];
            $lc_condiciones[9] = $_POST["rsaut_grupo_tarjeta"];
            $lc_condiciones[10] = $_POST["rsaut_red_adquiriente"];
            $lc_condiciones[11] = $_POST["rsaut_merchant_id"];
            $lc_condiciones[12] = $_POST["rsaut_numero_tarjeta"];
            $lc_condiciones[13] = $_POST["rstaut_tarjetahabiente"];
            $lc_condiciones[14] = $_POST["mlec_codigo"];
            $lc_condiciones[15] = $_POST["rsaut_identificacion_aplicacionemp"];
            $lc_condiciones[16] = $_POST["rsaut_movimiento"];
            $lc_condiciones[17] = utf8_decode($_POST["raut_observacion"]);
            $lc_condiciones[18] = $_POST["IDStatus"];
            $lc_condiciones[19] = $_POST["replica"];
            $lc_condiciones[20] = $_POST["nivel"];
            $lc_condiciones[21] = $_POST["SWT_Respuesta_AutorizacionVarchar1"];

            // REquerimiento
            $lc_condiciones[22] = $_POST["rqaut_ip"];
            $lc_condiciones[23] = $_POST["tpenv_id"];
            $lc_condiciones[24] = $_POST["idUser"];

            $lc_condiciones[25] = $_POST["SWT_Respuesta_AutorizacionVarchar2"];

            print $lc_facturas->fn_consultar("PayPhoneGuardarRespuestaAutorizacion", $lc_condiciones);
        } else if (isset($_POST['RegistroTransaccionFB'])) {
            $respuesta = new stdClass();
            $JSON = json_decode($_POST['json']);
            $JSON->token = $_SESSION['fb_security'];
            $JSON->cashier->uid=$_SESSION['fb_security'];;
            $JSON = json_encode($JSON);
            $app = 'jvz';
            if (!empty($_SESSION['appid'])) {
                $app = $_SESSION['appid'];
            }
            if ($_SESSION['fb_status'] == 'REGISTERED') {
                // ******* V2 FIDELIZACION *******
                // Verificar Token
                // *******
                if ($app === 'jvz') {
                    if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                        $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'FIREBASE', 'REGISTRO TRANSACCIONV2');
                    }
                    else{
                        $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'FIREBASE', 'REGISTRO TRANSACCION');
                    } 
                } else {
                    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'FIDELIZACION ' . $app, 'REGISTRO TRANSACCION');
                }
                $urlWSRetornaPrecios = $urlWSRetornaPrecios['urlwebservice'];
                $newTokenGenerate = $loyaltyTokensManager->generateNewAccessTokenApp($restaurante, $_SESSION['claveConexion']);
                try{
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $urlWSRetornaPrecios,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 60,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_AUTOREFERER => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_POSTFIELDS => $JSON,
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $newTokenGenerate,
                            'Content-Type: application/json'
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    if (!$response || strlen(trim($response)) == 0) {
                        $accion = "ACUMULACION PUNTOS";
                        $lc_facturas->logProcesosFidelizacion("Error al acumular puntos", $accion, $restaurante, $cadena, $usuario, 'Respuesta CURL vacia');
                        print ('{ "message": "Error al acumular puntos, intenta más tarde", "codigo": "-1"}');
                    } else {
                        if ($http_status == 200) {
                            $accion = 'ACUMULACION PUNTOS';
                            $vectorDatos = @json_decode($response);
                            //$vectorDatos = $vectorDatos->data // ******* V2 FIDELIZACION *******
                            $_SESSION['fb_points'] = $vectorDatos->data->pointsByCustomer; // ******* V2 FIDELIZACION *******  data->
                            $_SESSION['fb_econtroDatos'] = 1;
                            $vectorJSON = @json_decode($JSON);
                            $cfac_id = $vectorJSON->invoiceCode;
                            $lc_condiciones[0] = $cfac_id;
                            $lc_condiciones[1] = 3;
                            $lc_condiciones[2] = $vectorDatos->data->pointsByTransaction; // Puntos  ganados por transaccion // ******* V2 FIDELIZACION *******  data->
                            $lc_condiciones[3] = 'correcto';
                            $lc_condiciones[4] = $vectorDatos->data->pointsByCustomer; // Puntos totales del cliente. // ******* V2 FIDELIZACION *******  data->
                            $lc_facturas->fn_consultar('estadoErrorFactura', $lc_condiciones);
                            $accion = 'ACUMULACION PUNTOS';
                            $respuesta->request = $JSON;
                            $respuesta->response = $response;
                            $respuesta->data = json_encode($lc_condiciones);
                            $respuesta->url =$urlWSRetornaPrecios;
                            $lc_facturas->logProcesosFidelizacion("Acumulacion de puntos exitoso", $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            $_SESSION['fb_document'] = null;
                            $_SESSION['fdznDocumento'] = null; // para cuando se cierre erroneamente en la pantalla facturacin.
                            $_SESSION['fb_name'] = null;
                            $_SESSION['fb_status'] = null;
                            $_SESSION['fb_points'] = null;
                            $_SESSION['fdznNombres'] = null;
                            $_SESSION['fdznDireccion'] = null;
                            $_SESSION['fb_econtroDatos'] = 0;
                            print($response);
                        } else if ($http_status != 0) {
                            $vectorDatos = @json_decode($response);
                            if ($http_status == 422) {
                                $mensajeredemptionCode = $vectorDatos->message;
                            } else {
                                $mensajeredemptionCode = 'Error: ' . $vectorDatos->message;
                            }
                            $vectorJSON = @json_decode($JSON);
                            $cfac_id = $vectorJSON->invoiceCode;
                            $lc_condiciones[0] = $cfac_id;
                            $lc_condiciones[1] = 2;
                            $lc_condiciones[2] = -1;
                            $lc_condiciones[3] = utf8_decode($mensajeredemptionCode);
                            $lc_facturas->fn_consultar('estadoErrorFactura', $lc_condiciones);
                            $accion = 'ACUMULACION PUNTOS';
                            $respuesta->request = $JSON;
                            $respuesta->response = $response;
                            $respuesta->data = json_encode($lc_condiciones);
                            $lc_facturas->logProcesosFidelizacion("Error al acumular puntos", $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            //Borrar variables de sesión
                            $_SESSION['fb_document'] = null;
                            $_SESSION['fdznDocumento'] = null; // para cuando se cierre erroneamente en la pantalla facturacion.
                            $_SESSION['fb_name'] = null;
                            $_SESSION['fb_status'] = null;
                            $_SESSION['fb_points'] = null;
                            $_SESSION['fdznNombres'] = null;
                            $_SESSION['fdznDireccion'] = null;
                            $_SESSION['fb_econtroDatos'] = 0;
                            if (isset($response)) {
                                print ($response);
                            } else {
                                print ('{ "message": "error", "codigo": "-1"}');
                            }
                        } else {
                            $accion = 'ACUMULACION PUNTOS';
                            $respuesta->request = $JSON;
                            $respuesta->response = null;
                            $respuesta->data = null;
                            $lc_facturas->logProcesosFidelizacion("Error al acumular puntos, servicio no disponible", $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            print ('{ "message": "Estado: Servicio no disponible...", "codigo": "-1", "http_status": "' . $http_status . '", "response": "' . $response . '", "response": "' . $err . '"}');
                        }
                    }
                }catch (\Exception $error){
                    $accion = 'ACUMULACION PUNTOS';
                    $lc_facturas->logProcesosFidelizacion('Excepcion global de error al acumular', $accion, $restaurante, $cadena, $usuario, $error->getMessage());
                    print ('{ "message": "Error al acumular puntos, intenta más tarde", "codigo": "-1"}');
                }
            } else {
                $accion = 'ACUMULACION PUNTOS';
                $respuesta->request = $JSON;
                $respuesta->response = null;
                $respuesta->data = null;
                $lc_facturas->logProcesosFidelizacion("Usuario " . $_SESSION['fb_status'] . " no acumula puntos", $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                print ('{ "message": "Estado: ' . $_SESSION['fb_status'] . '", "codigo": "-1"}');
            }
        } else if (isset($_POST['obtener_agregadores'])) {
            $id_cadena = $cadena;

            print $lc_facturas->obtener_agregadores($id_cadena);
        } else if (isset($_POST['configuracionTurnero'])) {
            print $lc_config->fn_configuracionTurnero($cadena, $restaurante);
        } else if (htmlspecialchars(isset($_GET["reimprimirOrden"]))) {
            $lc_condiciones[0] = htmlspecialchars($_GET["odpOrden"]);
            $lc_condiciones[1] = $cadena;
            $lc_condiciones[2] = $restaurante;
            $lc_condiciones[3] = 1;
            $lc_condiciones[4] = 0;
            $lc_condiciones[5] = $idEstacion;
            print $lc_facturas->fn_consultar("reimprimirOrden", $lc_condiciones);
        } else if (htmlspecialchars(isset($_GET["validacionOrdenPedidoKiosko"]))) {
            $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
            print $lc_facturas->fn_consultar("validacionOrdenPedidoKiosko", $lc_condiciones);
        } else if (isset($_POST["consultarMediosPagoPayphoneDisponible"])) {
            $lc_condiciones[0] = $_POST["rst_id"];
            print $lc_facturas->fn_consultar("consultarMediosPagoPayphoneDisponible", $lc_condiciones);
        } else if (isset($_POST["PayPhoneEnviarTransaccionApp"])) {

            $JSON = json_encode($_POST['json']);
            $TOKEN = ($_POST['token']);

            $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'APP CREAR TRANSACCION');
            $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];


            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => $urlWSRetornaPrecios,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $JSON,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer $TOKEN"
                ),
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $array = [
                    "message" => curl_error($curl),
                    "statusCode" => "500",
                ];

                print (json_encode($array));
                curl_close($curl);
            } else {
                print ($response);
                curl_close($curl);
            }
        } else if (isset($_POST["obtenerConfiguracionSMTPyCorreo"])) {
            $lc_condiciones[0] = $_POST["rst_id"];
            print $lc_facturas->fn_consultar("obtenerConfiguracionSMTPyCorreo", $lc_condiciones);

        } else if (isset($_POST["obtenerHTMLCorreroPayphone"])) {
            $lc_condiciones[0] = $_POST["rst_id"];
            $lc_condiciones[1] = $_POST["cfac_id"];
            $lc_condiciones[2] = $_POST["link"];
            print $lc_facturas->fn_consultar("obtenerHTMLCorreroPayphone", $lc_condiciones);

        } else if (isset($_POST["PayPhoneObtenerLinkDePagosPayphone"])) {

            $JSON = json_encode($_POST['json']);
            $TOKEN = ($_POST['token']);

            $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'LINK CREAR TRANSACCION');
            $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];


            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => $urlWSRetornaPrecios,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $JSON,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer $TOKEN"
                ),
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $array = [
                    "message" => curl_error($curl),
                    "statusCode" => "500",
                ];

                print (json_encode($array));
                curl_close($curl);
            } else {

                print ($response);
                curl_close($curl);
            }
        } else if (isset($_POST["PayPhoneObtenerDatosTransaccionClientID"])) {

            $clientID = ($_POST['clientID']);
            $TOKEN = ($_POST['token']);

            $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($restaurante, 'PAYPHONE', 'OBTENER TRANSACCION CLIENT ID');
            $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"];

            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => "$urlWSRetornaPrecios" . $clientID,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $TOKEN",
                    "Cookie: ARRAffinity=46b00f471cd74e0d2ed86afd100b04df09570f8099612a7c7a6d4e550fa14999"
                ),
            ));

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
            curl_close($curl);
            print ($response);

        } else if (htmlspecialchars(isset($_GET['obtieneNumeroOrden']))) {
            $txtOrdenPedidoId = htmlspecialchars($_GET["IDCabeceraOrdenPedido"]);
            print  $lc_facturas->fn_obtieneNumeroOrden($txtOrdenPedidoId);

        } else if (isset($_POST['agregadorOrdenPedido'])) {
            $id_cabecera_pedido = $_POST['id_cabecera_pedido'];
            $id_agregador = $_POST['id_agregador'];

            print $lc_facturas->ordenPedidOAgregador($id_cabecera_pedido, $id_agregador);
        } else if (htmlspecialchars(isset($_GET["anula_formaPagoPayvalida"]))) {
            $lc_condiciones[0] = htmlspecialchars($_GET["anu_codFact"]);
            $lc_condiciones[1] = htmlspecialchars($_GET["anu_idPago"]);
            $lc_condiciones[2] = $usuario;
            print $lc_facturas->fn_consultar("anula_formaPagoPayvalida", $lc_condiciones);
}
else if( isset( $_POST["IDCabeceraOrdenPedidoCFOP"] ) ) 
{
    $IDCabeceraOrdenPedido = $_POST["IDCabeceraOrdenPedidoCFOP"];

    print $lc_facturas->condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido );

}
else if( isset( $_POST["aceptaBeneficioClienteApi"] ) ) 
{
    $dop_id = $_POST["dop_id"];
    $uid = $_POST["uid"];
    print $lc_facturas->aceptaBeneficioClienteApi($cadena, $restaurante, $dop_id, $uid);

} else if (isset($_GET['grabaVoucherNoCancelar'])) {
    $transaccion = $_GET['respuesta'];
    $idEstacion = $_SESSION['estacionId'];
    $idRestaurante = $_SESSION['rstId'];
    $idUsuario= $_SESSION['usuarioId'];
    print $lc_facturas->VoucherCanceladoNoAprobado($transaccion,$idEstacion,$idRestaurante,$idUsuario);

}else if (isset($_GET['api_qualtrics'])) {
    try {
        $fact = $_GET['fact'];
        $idEstacion = $_SESSION['estacionId'];
        $idRestaurante = $_SESSION['rstId'];
        $idUsuario = $_SESSION['usuarioId'];

        $data = $lc_facturas->getPayloadQualtrics($fact, $idRestaurante, $idUsuario);

        if(isset($data['payload']) and $data['payload'] != ''){
            $urlQualtrics = $servicioWebObj->retorna_WS_Qualtrics($idRestaurante);
            $urlQualtrics = $urlQualtrics["urlwebservice"];
            
            $ch = curl_init($urlQualtrics);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data['payload']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            curl_close($ch);
    
            $lc_facturas->auditoriadQualtrics($fact, $idRestaurante, $idUsuario, $response);
    
            if ($http_status !== 200 and $http_status !== 202) {
                throw new Exception("HTTP request failed with status code $http_status");
            }
            echo "✅ La API fue consumida satisfactoriamente. Se recibió una respuesta válida del servidor.";
        }else{
            echo "⚠️ No aplica para enviar datos a qualtrics, posiblemente es 'CONSUMIDOR FINAL'.";
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } 
}else if (isset($_GET['validaFactura'])) {
        $lc_condiciones[0] = $_GET["cfac_id"];
        $lc_condiciones[1] = $_GET["tipoValidacion"];
    print $lc_facturas->fn_consultar("validaFactura", $lc_condiciones);

}else if (isset($_GET['condicionConfiguracionLocalizador'])) {
        $lc_condiciones[0] = $cadena;
        $lc_condiciones[1] = $restaurante;
        $lc_condiciones[2] = $_GET["idCabeceraOrdenPedido"];
    print $lc_facturas->fn_consultar("condicionConfiguracionLocalizador", $lc_condiciones);

}else if (isset($_GET['guardarConfiguracionLocalizador'])) {
        $lc_condiciones[0] = $_GET["numeroLocalizador"];
        $lc_condiciones[1] = $_GET["IDCabeceraOrdenPedido"];
    print $lc_facturas->fn_consultar("guardarConfiguracionLocalizador", $lc_condiciones);

}