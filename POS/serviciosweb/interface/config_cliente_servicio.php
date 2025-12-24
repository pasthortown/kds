<?php

ini_set("max_execution_time", 360); //Extiende el tiempo de la ejecucion de la peticion
session_start();
/*
  DESARROLLADO POR: Darwin Mora
  DESCRIPCION: Consume el web service para interface
  TABLAS INVOLUCRADAS:
  FECHA CREACION:19/04/2016
  FECHA ULTIMA MODIFICACION: 18/08/2016
  USUARIO QUE MODIFICO: Christian Pinto
  DECRIPCION ULTIMO CAMBIO: Envio Forma de Pago EmpleadoCxC
 *  HISTORIAL DE CAMBIOS:
  10/01/2017 Francisco Sierra
  Se incorpora el proceso de transferencia de venta
 *  19/04/2017
 *      Se coloca los webservices en políticas
 *  20/09/2017 Hugo Mera
 *      Se aumentó el tiempo de ejecución de la página a 360 segundos
 *  09/05/2019 Daniel Llerena
 *      Se aumentó el campo Agradores
 *      Se aumentó el campo integradores
 *  26/07/2023 Christian Pinto
 *      Consumo por curl ws interface SIR
 *      Se cambia la estructura del envio de la data al ws de SIR
*/


include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_clienteServicio.php";
require_once "../../soap/lib/nusoap.php";
include_once "../../clases/clase_transferenciaventa.php";
include_once "../../clases/clase_loguin.php";
include_once "../../clases/clase_webservice.php";

ini_set('default_socket_timeout', 1);

//Leer archivo de configuración
$array_ini = parse_ini_file("config.ini");
$servicioWebObj = new webservice();
$lc_cliente_interfaceGer = new cliente_servicio();
$lc_cliente_inactivaSesion = new cliente_servicio();
$lc_cliente_transferenciaVenta = new TransferenciaVenta();

//parametros para interface
$numeroIntentos = 0; //variable de inicio número de intentos
$numeroMaximoDeIntentos = ($array_ini['numeroMaximoDeIntentos']);
$tiempoEspera = ($array_ini['tiempoEspera']);

if (htmlspecialchars(isset($_GET["generarInterface"]))) {
    $tipo_interface = htmlspecialchars($_GET["tipo_interface"]);
    $interface_eventos = htmlspecialchars($_GET["interface_eventos"]);
    $TipoTransferencia = htmlspecialchars($_GET["TipoTransferencia"]);
    $ventaPluHeladeria = '0x00000000';
    $IDPeriodo = htmlspecialchars($_GET["IDPeriodo"]);
    $valorBrutoTransferencia = '0';
    $valorNetoTransferencia = '0';
    $ivaTransferencia = '0';

    if ($TipoTransferencia == 'DESTINO') {
        $lc_condiciones[0] = $_SESSION['rstId'];
        $lc_condiciones[1] = $_SESSION['cadenaId'];

        $datosWebservice = $servicioWebObj->retorna_WS_Trans_Venta_CalculoInterfaceDestino($_SESSION['rstId']);
        $urlWSCalculoInterfaceDestino = $datosWebservice["urlwebservice"];
        $datosOrigen = $lc_cliente_transferenciaVenta->fn_encuentra_bdd($lc_condiciones);
        $basedatos = $datosOrigen["NombreBdd"];
        $restauranteDestino = $datosOrigen["Restaurante"];
        $cadenaDestino = $datosOrigen["Cadena"];

        $lc_condicion[0] = $IDPeriodo;
        $datosPeriodoRelacionado = $lc_cliente_transferenciaVenta->fn_encuentra_periodo_relacionado($lc_condicion);
        $periodoRelacionado = $datosPeriodoRelacionado["PeriodoRelacionado"];

        $url = $urlWSCalculoInterfaceDestino . "?rst_id=" . $restauranteDestino . "&cdn_id=" . $cadenaDestino . "&bdd=" . $basedatos . "&IDPeriodo=$periodoRelacionado";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $result = curl_exec($ch);

        curl_close($ch);

        $respuestaSolicitud = json_decode($result);

        $codRestaurante = $respuestaSolicitud[0];
        $lc_fecha = $respuestaSolicitud[1];
        $ventaPluHeladeria = $respuestaSolicitud[2];
        $valorBrutoTransferencia = $respuestaSolicitud[3];
        $valorNetoTransferencia = $respuestaSolicitud[4];
        $ivaTransferencia = $respuestaSolicitud[5];

        $lc_condicion[1] = $valorBrutoTransferencia;
        $lc_condicion[2] = $valorNetoTransferencia;
        $lc_condicion[3] = $ivaTransferencia;

        //se guarda la informacion de los valores de venta de la transferencia en la base de heladeria
        $lc_cliente_transferenciaVenta->fn_inyecta_valoresVentaTransferencia($lc_condicion);
    }

    // Interface de venta
    $accion = 1;
    $id_cadena =  $_SESSION['cadenaId'];

    $parametro_metodo = $lc_cliente_interfaceGer->metodoInterface($accion, $id_cadena);
    $metodo_interface = $parametro_metodo["metodo_interface"];

    if ($metodo_interface == '' || $metodo_interface == 'null') {
        $datos[] = array(
            "0" => '{"Respuesta":"0","Mensaje":"La Política INTERFACE DE VENTA, no se encuentra configurado el parámetro METODO..."}'
        );

        print json_encode($datos);
        die();
    }

    $lc_datos = [$_SESSION["rstId"], 'GERENTE', 'INTERFACE', 0];
    $datosWebservice = $servicioWebObj->retorna_Direccion_Webservice($lc_datos);
    $wsdl = $datosWebservice["urlwebservice"];

    $datos_interface = $lc_cliente_interfaceGer->interfaceVenta($IDPeriodo);
    $datos_interface_array = $datos_interface[0];

    $eventos                    = $interface_eventos;
    $id_restaurante             = $datos_interface_array['id_restaurante'];
    $fecha_periodo              = $datos_interface_array['fecha_periodo'];
    $json_cierre_cajas          = $datos_interface_array['json_cierre_cajas'];
    $json_formas_pago           = $datos_interface_array['json_formas_pago'];
    $json_venta_por_hora        = $datos_interface_array['json_venta_por_hora'];
    $json_plus                  = $datos_interface_array['json_plus'];
    $json_depositos             = $datos_interface_array['json_depositos'];
    $json_caja_chica            = $datos_interface_array['json_caja_chica'];
    $json_cxc_empleado          = $datos_interface_array['json_cxc_empleado'];
    $json_credito_autoconsumo   = $datos_interface_array['json_credito_autoconsumo'];
    $json_switch_transaccional  = $datos_interface_array['json_switch_transaccional'];
    $json_recargas_consumos     = $datos_interface_array['json_recargas_consumos'];
    $json_medio_formas_pago     = $datos_interface_array['json_medio_formas_pago'];

    // Datos que quieres enviar
    $data_back = array(
        'interface_eventos'             => $eventos, 'id_restaurante'              => $id_restaurante, 'fecha_periodo'               => $fecha_periodo, 'json_cierre_cajas'           => $json_cierre_cajas, 'json_formas_pago'            => $json_formas_pago, 'json_venta_por_hora'         => $json_venta_por_hora, 'json_plus'                   => $json_plus, 'json_depositos'              => $json_depositos, 'json_caja_chica'             => $json_caja_chica, 'json_cxc_empleado'           => $json_cxc_empleado, 'json_credito_autoconsumo'    => $json_credito_autoconsumo, 'json_switch_transaccional'   => $json_switch_transaccional, 'json_recargas_consumos'      => $json_recargas_consumos, 'json_medio_formas_pago'      => $json_medio_formas_pago
    );

	//se cambia la estructura del envio de la data al ws de SIR // CP
    $data = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <interfaceVentaPorMedio xmlns="">
                    <interface_eventos>' . $eventos . '</interface_eventos>
                    <id_restaurante>' . $id_restaurante . '</id_restaurante>
                    <fecha_periodo>' . $fecha_periodo . '</fecha_periodo>
                    <json_cierre_cajas>' . $json_cierre_cajas . '</json_cierre_cajas>
                    <json_formas_pago>' . $json_formas_pago . '</json_formas_pago>
                    <json_venta_por_hora>' . $json_venta_por_hora . '</json_venta_por_hora>
                    <json_plus>' . $json_plus . '</json_plus>
                    <json_depositos>' . $json_depositos . '</json_depositos>
                    <json_caja_chica>' . $json_caja_chica . '</json_caja_chica>
                    <json_cxc_empleado>' . $json_cxc_empleado . '</json_cxc_empleado>
                    <json_credito_autoconsumo>' . $json_credito_autoconsumo . '</json_credito_autoconsumo>
                    <json_switch_transaccional>' . $json_switch_transaccional . '</json_switch_transaccional>
                    <json_recargas_consumos>' . $json_recargas_consumos . '</json_recargas_consumos>
                    <json_medio_formas_pago>' . $json_medio_formas_pago . '</json_medio_formas_pago>
                </interfaceVentaPorMedio>
            </Body>
        </Envelope>';
	
    // Inicializar la sesión cURL
    $ch = curl_init();

    // Configurar las opciones de la solicitud cURL
    curl_setopt($ch, CURLOPT_URL, $wsdl); // URL de destino
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Indicar que se usará el método POST
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Datos a enviar en el cuerpo de la solicitud
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);

    // Ejecutar la solicitud cURL y almacenar la respuesta en $result	
	$result = curl_exec($ch);
    $doc = new DOMDocument();
    $doc->loadXML($result);
    // Encontrar el elemento <return> y obtener su contenido
    $returnElement = $doc->getElementsByTagName("return")->item(0);
    $jsonString = $returnElement->nodeValue;
    //$jsonArray = json_decode(html_entity_decode($jsonString), true);
    // Ahora $jsonArray contiene los datos en formato JSON como un arreglo asociativo
    echo $jsonString;
    //return false;


    if (curl_errno($ch)) {
        curl_error($ch);

        $datos[] = array(
            "0" => '{"Respuesta":"-1","Mensaje":"Error al consumir el servicio inteface de venta, ' . $ch . '"}'
        );

        print json_encode($datos);

        die();
    } else {

        $response = $result;
		return false;
        if ($response != '') {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//return');
            $responseArray = json_encode($body);

            print($responseArray);
        } else {
            $datos[] = array(
                "0" => '{"Respuesta":"0","Mensaje":"Existe problemas al realizar interface de venta, intenteló más tarde!"}'
            );

            print json_encode($datos);
        }

        curl_close($ch);
    }
}

if (htmlspecialchars(isset($_POST["validaTransferencia"]))) {
    $cedulaCajero = htmlspecialchars($_POST["cedulaCajero"]);
    $cod_restaurante = htmlspecialchars($_POST["rst_id"]);
    $lc_fecha = htmlspecialchars($_POST["fecha"]);
    $param = array(
        'cod_restaurante' => $cod_restaurante,
        'lc_fecha' => $lc_fecha,
        'Personal' => $cedulaCajero
    );

    $lc_datos = [
        $cod_restaurante,
        'GERENTE',
        'INTERFACE',
        0
    ];
    $datosWebservice = $servicioWebObj->retorna_Direccion_Webservice($lc_datos);

    if ($datosWebservice["estado"] == 1) {
        $wsdl = $datosWebservice["urlwebservice"];
        $loginGerentePrimario = intentarLoginGerente($wsdl, $param, $numeroMaximoDeIntentos, $tiempoEspera);

        if (true === $loginGerentePrimario["estado"]) {
            echo ($loginGerentePrimario["mensaje"]);
            die();
        }
    }

    $lc_datos[3] = 1;

    $wsdlsecundario = $servicioWebObj->retorna_Direccion_Webservice($lc_datos);

    if ($wsdlsecundario["estado"] == 1) {
        $loginGerenteSecundario = intentarLoginGerente($wsdlsecundario["urlwebservice"], $param, $numeroMaximoDeIntentos, $tiempoEspera);
        if (true === $loginGerenteSecundario["estado"]) {
            echo ($loginGerenteSecundario["mensaje"]);
            die();
        }
    }

    $puedeLoguearse = validarLoginLocalmente($cedulaCajero, $cod_restaurante);
    
    echo json_encode($puedeLoguearse,JSON_INVALID_UTF8_IGNORE);
}

function intentarLoginGerente($wsdl, $param, $numeroMaximoDeIntentos, $tiempoEspera)
{
    /*$numIntentos = 0;
    $client = new nusoap_client($wsdl, 'wsdl');
    
    while ($numIntentos < $numeroMaximoDeIntentos) {
        $Confirmacion = $client->call('ValidarTransferenciaPersonal', $param);
        
        if ($Confirmacion) {
            return ["estado" => true, "mensaje" => $Confirmacion];
        } else {
            $numIntentos++;
        }
        
        if ($numIntentos < $numeroMaximoDeIntentos)
            sleep($tiempoEspera);
    }*/

    return ["estado" => false, "mensaje" => "No se pudo validar login en gerente"];
}

function validarLoginLocalmente($cedulaCajero, $cod_restaurante)
{
    $lc_loguin = new loguin();
    $lc_condiciones = [$cod_restaurante, $cedulaCajero];
    $resultado = $lc_loguin->fn_consultar("ValidarLoginLocalmente", $lc_condiciones);
    return $resultado;
}
