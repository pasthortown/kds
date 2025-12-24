<?php

@session_start();

$cadena = $_SESSION['cadenaId'];
$restaurante = $_SESSION['rstId'];
$tipo_servicio = $_SESSION['TipoServicio'];
$usuario = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];
$estacion_id = $_SESSION['estacionId'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ///////////////////////////////////////////////////////////////
///////DESCRIPCION: /////////////////////////////////////////////////////////////////////////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////////////////////////////////////////////////////////
////////////////Menu_Agrupacionproducto//////////////////////////////////////////////////////////////////
////////////////Detalle_Orden_Pedido/////////////////////////////////////////////////////////////////////
///////////////////Plus, Precio_Plu, Mesas///////////////////////////////////////////////////////////////
///////FECHA CREACION: 24-03-2014////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+//////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-07-2014/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez//////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: se agrego el if "obtienencreid" Y facturacion electronica///////////////
///////FECHA ULTIMA MODIFICACION: 16/01/2015/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez//////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO:facturacion tipo Plan Market/////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 05/05/2015/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Jorge Tinoco ///////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Muestra ordenes de pedido en pantalla de cuentas abiertas //////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_anularOrden.php';
include_once '../clases/clase_webservice.php';
include_once '../xml/generar_xml.php';

require_once('../resources/module/fidelizacion/Token.php');
require_once('../resources/module/fidelizacion/TokenManager.php');

include_once ('../resources/module/fidelizacion/Credito.php');
include('../clases/clase_fidelizacionAuditoria.php');

include('../clases/clase_facturacion.php');

$lc_config = new menuPedido();
$servicioWebObj = new webservice();
$loyaltieTokenManager = new TokenManager();

// ******* V2 FIDELIZACION *******
//$tokenLoyalty = new TokenLoyalty();
// *******

if (htmlspecialchars(isset($_GET["impresionFacturaError"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["idFactura"]);
    print $lc_config->fn_consultar("impresionFacturaError", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["impresion_factura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("impresion_factura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["impresion_voucher"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rqaut"]);
    print $lc_config->fn_consultar("impresion_voucher", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["txTarjetas"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_SESSION['IDPeriodo'];
    $lc_condiciones[2] = $estacion_id;
    print $lc_config->fn_consultar("txTarjetas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabaCanalImpresionAnulacionPreimpresa"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["idfacturaPre"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["charNota"]);
    $lc_condiciones[2] = $ip;
    $lc_condiciones[3] = $usuario;
    print $lc_config->fn_consultar("grabaCanalImpresionAnulacionPreimpresa", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["reimpresion_factura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["canal"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usuarioAdminR"]);
    //$lc_condiciones[2] =htmlspecialchars($_GET["usr_id"]);
    print $lc_config->fn_consultar("reimpresion_factura", $lc_condiciones);

} else if (isset($_POST["validaCajeroActivoParaAnulacion"])) {
    $lc_condiciones[0] = $_POST["facturaId"];
    print $lc_config->fn_validaCajeroActivoParaAnulacion($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarConfiguracionRestaurante"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $tipo_servicio;
    $lc_condiciones[2] = $_SESSION['estacionId'];
    $lc_condiciones[3] = $_SESSION['cadenaId'];
    print $lc_config->fn_consultar("cargarConfiguracionRestaurante", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarAccesosPerfil"]))) {
    $lc_condiciones[0] = $usuario;
    print $lc_config->fn_consultar("cargarAccesosPerfil", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["generarNotaCreditoOtros"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $ip;
    $lc_condiciones[4] = htmlspecialchars($_GET["motivo"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["mtv_id"]);
    $lc_condiciones[6] = $_SESSION['estacionId'];
    print $lc_config->fn_consultar("generarNotaCreditoOtros", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["actualizarMotivoAnulacion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["motivo"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["mtv_id"]);
    print $lc_config->fn_consultar("actualizarMotivoAnulacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarTipoEnvioFacturaFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("cargarTipoEnvioFacturaFormaPago", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["impresionFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["est_ip"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_id"]);
    print $lc_config->fn_consultar("impresionFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["impresionVaucher"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rsaut_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["est_ip"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["rst_id"]);
    print $lc_config->fn_consultar("impresionVaucher", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultar_transaccionformaPagoFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rsaut_movimiento"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["fpf_id"]);
    print $lc_config->fn_consultar("consultar_transaccionformaPagoFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultar_formasPagoFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("consultar_formasPagoFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["impresionNotaCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ncre_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["est_ip"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_id"]);
    print $lc_config->fn_consultar("impresionNotaCredito", $lc_condiciones);

} else if (isset($_GET["consultarMesaOrden"])) {
    $lc_condiciones[0] = $_GET["odp_id"];
    $lc_condiciones[1] = $usuario;
    print $lc_config->fn_consultar("consultarMesaOrden", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["visorCabeceraFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("visorCabeceraFactura", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["visorDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("visorDetalleFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["visorDetalleNotaCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ncre_id"]);
    print $lc_config->fn_consultar("visorDetalleNotaCredito", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["totalDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("totalDetalleFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["totalDetalleNotaCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ncre_id"]);
    print $lc_config->fn_consultar("totalDetalleNotaCredito", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["formasPagoDetalleFactura"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("formasPagoDetalleFactura", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["formasPagoDetalleNotaCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ncre_id"]);
    print $lc_config->fn_consultar("formasPagoDetalleNotaCredito", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["muestraTipoCuenta"]))) {
    print $lc_config->fn_consultar("muestraTipoCuenta", '');

} else if (htmlspecialchars(isset($_GET["grabacanalmovimientoVoucher"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["respuesta"]);
    $lc_condiciones[3] = $restaurante;
    $lc_condiciones[4] = $estacion_id;
    print $lc_config->fn_consultar("grabacanalmovimientoVoucher", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cancelaTarjetaForma"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cancela_codFact"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cancela_idPago"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["can_respuesta"]);
    $lc_condiciones[3] = $ip;
    $lc_condiciones[4] = $usuario;
    print $lc_config->fn_consultar("cancelaTarjetaForma", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["armaTramaSWTbanda"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['tipoTransaccion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['numMovimiento']);
    $lc_condiciones[2] = htmlspecialchars($_GET['formaIdPagoFact']);
    $lc_condiciones[3] = $_SESSION['estacionId'];
    $lc_condiciones[4] = $_SESSION['usuarioId'];
    $lc_condiciones[5] = $_SESSION['rstId'];
    $lc_condiciones[6] = htmlspecialchars($_GET['tipoTarjeta']);
    $lc_condiciones[7] = htmlspecialchars($_GET['trackTarjeta']);
    $lc_condiciones[8] = htmlspecialchars($_GET['cvvtarjeta']);
    $lc_condiciones[9] = htmlspecialchars($_GET['anuFormaPagoId']);
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = 0;
    print $lc_config->fn_consultar("armaTramaSWTbanda", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["esperaRespuestaRequerimientoAutorizacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
    print $lc_config->fn_consultar("esperaRespuestaRequerimientoAutorizacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["claveAcceso"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['factt']);
    $lc_condiciones[1] = htmlspecialchars($_GET['char']);
    $lc_condiciones[2] = $ip;
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = $restaurante;
    print $lc_config->fn_consultar("claveAcceso", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["anu_consultaTipoEnvio"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['cfac_idTipoEnvio']);
    print $lc_config->fn_consultar("anu_consultaTipoEnvio", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["anu_insertarRequerimientoAutorizacion"]))) {
    $lc_condiciones[0] = '03'; //$_GET["tipoTransac"];
    $lc_condiciones[1] = htmlspecialchars($_GET["anucfac"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["anuformaPagoID"]);
    $lc_condiciones[3] = $estacion_id;
    $lc_condiciones[4] = $usuario;
    $lc_condiciones[5] = $restaurante;
    $lc_condiciones[6] = htmlspecialchars($_GET["envio"]);
    print $lc_config->fn_consultar("anu_insertarRequerimientoAutorizacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["anu_insertarRequerimientoAutorizacionUnired"]))) {
    $lc_condiciones[0] = '03'; //$_GET["tipoTransac"];
    $lc_condiciones[1] = htmlspecialchars($_POST["anucfacP"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["anuformaPagoIDP"]);
    $lc_condiciones[3] = $estacion_id;
    $lc_condiciones[4] = $usuario;
    $lc_condiciones[5] = $restaurante;
    print $lc_config->fn_insertarRequerimientoAutorizacionUnired($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cuentasAbiertas"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $estacion_id;
    print $lc_config->fn_consultar("cuentasAbiertas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["busquedaCuentasAbiertas"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $estacion_id;
    print $lc_config->fn_consultar("busquedaCuentasAbiertas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cuentasCerradas"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $estacion_id;
    print $lc_config->fn_consultar("cuentasCerradas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["busquedaCuentasCerradas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $estacion_id;
    print $lc_config->fn_consultar("busquedaCuentasCerradas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["insertaNotaDeCredito"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["cfac_idA"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["userAdmin"]); //$usuario;
    $lc_condiciones[3] = htmlspecialchars($_GET["mtv_idA"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["motivoA"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["cliente"]);
    print $lc_config->fn_consultar("insertaNotaDeCredito", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["motivoAnulacion"]))) {
    $lc_condiciones[0] = $cadena;
    print $lc_config->fn_consultar("motivoAnulacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["anularOrden"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mtv_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cfac_observacion"]);
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] =htmlspecialchars($_GET["idFormaP"]);
    print $lc_config->fn_consultar('anularOrden', $lc_condiciones);
    
} else if (isset($_GET['AnulacionFidelizacionEfectivo'])) {
    $lc_condiciones[0] = $_GET['cfac_id'];
    $lc_condiciones[1] = $_GET['mtv_id'];
    $lc_condiciones[2] = $_GET['cfac_observacion'];
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = $_GET['idFormaP'];
    $result = new stdClass();
    $app = 'jvz';
    if (!empty($_SESSION['appid'])) {
        $app = $_SESSION['appid'];
    }

    $revertir = new Credito($restaurante, $app);
    $auditoria = new Auditoria();
    $respuesta = new stdClass();
    $lc_facturas = new facturas();
    // Datos Cliente
    $clienteDocumento = !empty($_SESSION['fb_document']) ? $_SESSION['fb_document'] : '';
    $cliente    =   !empty($_SESSION['fb_name']) ? $_SESSION['fb_name'] : '';
    $accion     =   'NOTA DE CREDITO PUNTOS';
    $proceso    =   'Fidelizacion';
    $mensaje    =   '';
    //OPCIÓN SOLO VALIDA PARA CLIENTES REGISTRADOS
            $transaccion = (object)$auditoria->solicitarSecuencialProceso($restaurante, $proceso, $usuario);
                try {                    
                    $SecuenciasOrigen=$lc_config->fn_CajeSecuenciaOrigen($lc_condiciones[0],$restaurante);                    
                    $data['balanceRedemptionCode']=$lc_condiciones[0];
                    $JSON = json_encode($data);
                    $lc_facturas->logProcesosFidelizacion('INICIO DE NOTA DE CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, $JSON);
                    //$respuesta = $revertir->revertir_puntos($lc_condiciones[0]);
                    $respuesta=(object) array(
                        'numberError'   => 0,
                        'httpStatus'    => 200,
                        'data'          => null
                    );
                    if(isset($respuesta->exception)){
                        $lc_facturas->logProcesosFidelizacion('EXCEPCION EN CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, $respuesta->exceptionMessage);
                        $result->message = 'Ha ocurrido un error mientras se procesaba el credito canje.';
                        $result->code = -1;
                    }else{
                        if ($respuesta->numberError == 0) {
                            $vectorDatos = @json_decode($respuesta->data);
                            if ($respuesta->httpStatus == 200) {
                                $respuesta->request = $JSON;
                                //Finaliza Transaccion
                                $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                //Log Auditoria
                                $lc_facturas->logProcesosFidelizacion('Credito exitoso.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                print $lc_config->fn_consultar('anularOrden', $lc_condiciones);
                                exit(0);
                            } else {
                                // Obtener Errores de respuesta
                                if (isset($vectorDatos->errors)) {
                                    $result->message = 'Alerta, por favor comunicarse con el soporte para validar este error.';
                                    $result->code = -1;
                                    //Finaliza Transaccion
                                    $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                    //Log Auditoria
                                    $lc_facturas->logProcesosFidelizacion('Error Canje de puntos.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                } else {
                                    $lc_facturas->logProcesosFidelizacion('REQUEST ERROR CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                    $result->message = $vectorDatos->error;
                                    $result->code = -1;
                                }
                            }
                        } else {
                            //TIMEOUT: no cambio el estado de la transaccion para que el cliente pueda volver a intentar
                            $lc_facturas->logProcesosFidelizacion('ERROR EN PETICION CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            $result->message = 'Se ha encontrado un error en la solicitud al procesar el canje.';
                            $result->code = -1;
                        }
                    }
                } catch (\Exception $error) {
                    $lc_facturas->logProcesosFidelizacion('GLOBAL ERROR CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, $error->getMessage());
                    $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                    $result->code = -1;
                }
                print json_encode($result);
}else if (isset($_GET['AnulacionFidelizacion'])) {
    $lc_condiciones[0] = $_GET['cfac_id'];
    $lc_condiciones[1] = $_GET['mtv_id'];
    $lc_condiciones[2] = $_GET['cfac_observacion'];
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = $_GET['idFormaP'];

    $result = new stdClass();
    $app = 'jvz';
    if (!empty($_SESSION['appid'])) {
        $app = $_SESSION['appid'];
    }

    $revertir = new Credito($restaurante, $app);
    $auditoria = new Auditoria();
    $respuesta = new stdClass();
    $lc_facturas = new facturas();

    // Datos Cliente
    $clienteDocumento = !empty($_SESSION['fb_document']) ? $_SESSION['fb_document'] : '';
    $cliente    =   !empty($_SESSION['fb_name']) ? $_SESSION['fb_name'] : '';
    $accion     =   'NOTA DE CREDITO PUNTOS';
    $proceso    =   'Fidelizacion';
    $mensaje    =   '';
    //OPCIÓN SOLO VALIDA PARA CLIENTES REGISTRADOS
            $transaccion = (object)$auditoria->solicitarSecuencialProceso($restaurante, $proceso, $usuario);
                try {                    
                    $SecuenciasOrigen=$lc_config->fn_CajeSecuenciaOrigen($lc_condiciones[0],$restaurante);                    
                    $data['balanceRedemptionCode']=$lc_condiciones[0];
                    $JSON = json_encode($data);
                    $lc_facturas->logProcesosFidelizacion('INICIO DE NOTA DE CREDITO CANJE', $accion, $restaurante,  $cadena, $usuario, $JSON);
                    if($SecuenciasOrigen['origen']==1){
                        //$urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOPUNTOS'); //jk Cuando se tenga activa el web service para generar nota de credito por compras de puntos.
                        echo json_encode(array('str'=>0,'mensaje'=>'No se puede generar un nota crédito en transacción con canje de puntos.'));
                        die; 
                    }
                    //$respuesta = $revertir->revertir($lc_condiciones[0],$SecuenciasOrigen['origen']);
                    //$respuesta = $revertir->revertir_puntos($lc_condiciones[0]);
                    if($SecuenciasOrigen['origen']==1){
                        //$urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOPUNTOS'); //jk Cuando se tenga activa el web service para generar nota de credito por compras de puntos.
                        echo json_encode(array('str'=>0,'mensaje'=>'No se puede generar un nota crédito en transacción con canje de puntos.'));
                        die; 
                    }
                    $respuesta=(object) array(
                        'numberError'   => 0,
                        'httpStatus'    => 200,
                        'data'          => null
                    );
                    if(isset($respuesta->exception)){
                        $lc_facturas->logProcesosFidelizacion('EXCEPCION EN CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, $respuesta->exceptionMessage);
                        $result->message = 'Ha ocurrido un error mientras se procesaba el credito canje.';
                        $result->code = -1;
                    }else{
                        if ($respuesta->numberError == 0) {
                            $vectorDatos = @json_decode($respuesta->data);
                            if ($respuesta->httpStatus == 200) {
                                $respuesta->request = $JSON;
                                //Finaliza Transaccion
                                $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                //Log Auditoria
                                $lc_facturas->logProcesosFidelizacion('Credito exitoso.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                print $lc_config->fn_consultar('anularOrden', $lc_condiciones);
                                exit(0);
                            } else {
                                // Obtener Errores de respuesta
                                if (isset($vectorDatos->errors)) {
                                    $result->message = 'Alerta, por favor comunicarse con el soporte para validar este error.';
                                    $result->code = -1;
                                    //Finaliza Transaccion
                                    $auditoria->finalizarSecuencialProceso($restaurante, $proceso, $transaccion->secuencia);
                                    //Log Auditoria
                                    $lc_facturas->logProcesosFidelizacion('Error Canje de puntos.', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                } else {
                                    $lc_facturas->logProcesosFidelizacion('REQUEST ERROR CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                                    $result->message = $vectorDatos->error;
                                    $result->code = -1;
                                }
                            }
                        } else {
                            //TIMEOUT: no cambio el estado de la transaccion para que el cliente pueda volver a intentar
                            $lc_facturas->logProcesosFidelizacion('ERROR EN PETICION CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, json_encode($respuesta));
                            $result->message = 'Se ha encontrado un error en la solicitud al procesar el canje.';
                            $result->code = -1;
                        }
                    }
                } catch (\Exception $error) {
                    $lc_facturas->logProcesosFidelizacion('GLOBAL ERROR CREDITO CANJE', $accion, $restaurante, $cadena, $usuario, $error->getMessage());
                    $result->message = 'Ha ocurrido un error mientras se procesaba el canje.';
                    $result->code = -1;
                }
                print json_encode($result);            
} else if (htmlspecialchars(isset($_GET["validarUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_clave"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_tarjeta"]);
    print $lc_config->fn_consultar('validarUsuario', $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validarCreencialesUsuario"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_clave"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_tarjeta"]);
    $lc_condiciones[3] = $usuario;
    $lc_condiciones[4] = htmlspecialchars($_GET["movimiento"]);
    $lc_condiciones[5] = $estacion_id;
    print $lc_config->fn_consultar("validarCreencialesUsuario", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["generarNotaCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["std_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["est_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cli_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["mtv_id"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["ncre_numero_factura"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["ncre_subtotal"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["ncre_base_iva"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["ncre_base_cero"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["ncre_iva"]);
    $lc_condiciones[11] = htmlspecialchars($_GET["ncre_total"]);
    $lc_condiciones[12] = htmlspecialchars($_GET["tcp_id"]);
    $lc_condiciones[13] = htmlspecialchars($_GET["ncre_claveAcceso"]);
    $lc_condiciones[14] = htmlspecialchars($_GET["prd_id"]);
    $lc_condiciones[15] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[16] = htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[17] = htmlspecialchars($_GET["fpf_id"]);
    $lc_condiciones[18] = htmlspecialchars($_GET["cfac_observacion"]);
    print $lc_config->fn_consultar("generarNotaCredito", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["formasPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("formasPago", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabacanalmovimientoImpresionAnulacionElectronica"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["idfactura"]);
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $restaurante;
    print $lc_config->fn_consultar("grabacanalmovimientoImpresionAnulacionElectronica", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["enviarSWT"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["tipo_trans"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cfac_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["est_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["fmp_id"]);
    print $lc_config->fn_consultar("enviarSWT", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["recibirSWT"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("recibirSWT", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["actualizarEstadoFormaEliminada"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_idEliminada"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["formaEliminada"]);
    print $lc_config->fn_consultar("actualizarEstadoFormaEliminada", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["obtienencreid"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfactura_id"]);
    print $lc_config->fn_consultar("obtienencreid", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["retomarCuentaAbierta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfac_id"]);
    print $lc_config->fn_consultar("retomarCuentaAbierta", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["esMiMesaEnFacturacion"]))) {
    $lc_condiciones[0] = $_POST["est_id"];
    $lc_condiciones[1] = $_POST["odp_id"];
    print $lc_config->fn_consultar("esMiMesaEnFacturacion", $lc_condiciones);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
//*************************FACTURACION ELECTRONICA**************************************************////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
} else if (htmlspecialchars(isset($_GET["anulaFacturaSinFacturacionElectronica"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfactura_idD"]);
    print $lc_config->fn_consultar("anulaFacturaSinFacturacionElectronica", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["lee_canalXMLfirmado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfacturas_id"]);
    print $lc_config->fn_consultar("lee_canalXMLfirmado", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["logErrores"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    print $lc_config->fn_consultar("logErrores", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["obtenerMesa"]))) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_SESSION['estacionId'];
    $lc_condiciones[2] = $_SESSION['usuarioId'];
    if (isset($_GET["odp_id"])){
        $lc_condiciones[3] = $_GET["odp_id"];
    }
    print $lc_config->fn_consultar("obtenerMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["esFacturaPlanAmigos"]))) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    print $lc_config->fn_consultar("esFacturaPlanAmigos", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["recargas"]))) {
    $idPeriodo = $_SESSION["IDPeriodo"];
    print $lc_config->fn_consultarRecargas($idPeriodo);
}
if (htmlspecialchars(isset($_POST["payphoneObtieneTransaccionID"]))) {
    $lc_condiciones[0] = $_POST["cfac_id"];
    print $lc_config->fn_consultar("payphoneObtieneTransaccionID", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["realizarReverso"]))) {
    $postBody = $_POST["postBody"];
    $obj = json_decode($postBody);
    $newTokenGenerate = $loyaltieTokenManager->generateNewAccessTokenApp($restaurante, $_SESSION["claveConexion"]);
    $urlWS = $servicioWebObj->retorna_rutaWS($restaurante, "RECARGAS", "REVERSO");
    $urlWS = $urlWS["urlwebservice"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWS,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $postBody,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $newTokenGenerate,
            "content-type: application/json"
        )
    ));

    // Respuesta
    $response = curl_exec($curl);
    // print "Response: " . $response . "<br/>";
    $err = curl_error($curl);
    // print "Error: " . $err . "<br/>";
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // print "Status: " . $http_status . "<br/>";
    $timeoutError = curl_errno($curl);
    // print "Timeout Error: " . curl_errno($curl);
    curl_close($curl);

    if ($timeoutError == 0) {
        if ($http_status == 200) {
            $vectorDatos = json_decode($response);
            $lc_facturas->logProcesosFidelizacion("Reverso de recarga exitoso.", $accion, $restaurante, $cadena, $usuario, $response);
            print ($response);
        } else {

        }
    } else {
        $trama = "{ \"balanceRedemptionCode\": \"$obj->balanceRedemptionCode\" }";//{ "cliente":  "' . $cliente . '", "clienteDocumento": "' . $clienteDocumento . '"}';
        $accion = "REVERSO DE RECARGAS";
        $mensajeredemptionCode = "TIMEOUT";
        $lc_facturas->logProcesosFidelizacion($mensajeredemptionCode, $accion, $restaurante, $cadena, $usuario, $trama);
        print('{ "message": "TIMEOUT", "codigo": "28", "balanceRedemptionCode": "' . $obj->balanceRedemptionCode . '"}');
    }
} else if (htmlspecialchars(isset($_POST["valida_transferencia"]))) {
    $transaccion = $_POST['cfac_id'];
    $id_restaurante = $restaurante;
    $id_cadena = $cadena;
    print $lc_config->validarTransaccionConTransferencia($transaccion, $id_restaurante, $id_cadena);
} else if (isset($_POST['valida_tiempo'])) {
    $transaccion = $_POST['cfac_id'];
    $ValidacionAnulacionFacturaTiempoApp = isset($_POST['ValidacionAnulacionFacturaTiempoApp'])? $_POST['ValidacionAnulacionFacturaTiempoApp'] : 0;
    $ValidacionAnulacionFacturaTiempoFast = isset($_POST['ValidacionAnulacionFacturaTiempoFast'])? $_POST['ValidacionAnulacionFacturaTiempoFast'] : 0;
    $id_restaurante = $restaurante;
    print $lc_config->validarTransaccionConTiempo($transaccion, $id_restaurante,$ValidacionAnulacionFacturaTiempoApp,$ValidacionAnulacionFacturaTiempoFast);
    unset($transaccion, $id_restaurante,$ValidacionAnulacionFacturaTiempoApp,$ValidacionAnulacionFacturaTiempoFast);
} else if (htmlspecialchars(isset($_POST["validarServicioTercero"]))) {

    $medio = $_POST["medio"];
    $servicio = $_POST["servicio"];
    $respuesta = $lc_config->validacionConsumoServicio($medio, $servicio);
    print json_encode($respuesta);

}

else if (htmlspecialchars(isset($_GET["restauranteCashless"]))) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = htmlspecialchars($_GET["rst_id"]);
    print $lc_config->fn_consultar("restauranteCashless", $lc_condiciones);
}else if (htmlspecialchars(isset($_GET["facturaCashless"]))) {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["codigoFactura"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["rst_id"]);
    $lc_condiciones[3] = $usuario;
    print $lc_config->fn_consultar("facturaCashless", $lc_condiciones);
}else if (htmlspecialchars(isset($_GET["activarCashless"]))) {
    $lc_condiciones[0] = $usuario;
    $lc_condiciones[1] = "";
    
    $codigoBarras = htmlspecialchars($_GET["codigoBarras"]);
    $restaurante = htmlspecialchars($_GET["rst_id"]);
    $codigofactura = htmlspecialchars($_GET["codigoFactura"]);
    $lc_condicionesWS[0] = $cadena;
    $lc_condicionesWS[1] = $usuario;;
    $lc_condicionesWS[2] = htmlspecialchars($_GET["rst_id"]);
    
    $datoUsuario=$lc_config->fn_consultar("retorna_cedula_user",$lc_condicionesWS);
    $cedula_user=$datoUsuario["cedula_user"];
        
    $datosWebservice=$lc_config->fn_consultar("retorna_WS_URL_Cashless",$lc_condicionesWS);
    $url=$datosWebservice["urlwebservice"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode(array(
            "accion" => 0,
            "restaurante"=> $restaurante,
            "codigo_barra"=> $codigoBarras,
            "nombre_user"=> $_SESSION['nombre'],
            "cedula_user"=> $cedula_user
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = ["status"=>"error comm", "message"=>curl_error($curl)];
        $lc_condiciones[1] = "Nota de Credito cashless:".curl_error($curl)." cfac_id:$codigofactura";
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }else{
        $lc_condiciones[1] = "Nota de Credito cashless:".$response ."cfac_id:$codigofactura";
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }
    curl_close($curl);
    print $response;
} else if (htmlspecialchars(isset($_GET["busquedaCuentasPeriodosAnteriores"]))) {
    $parametros = $_GET;
    $parametros["rstId"] = $restaurante;
    print $lc_config->buscarCuentasPeriodosAnteriores($parametros);
} else if(isset($_POST['valorarCambioSobreFactura'])) {
    print $lc_config->valorarCambioSobreFactura($_POST['cfac_id']);
}
?>
