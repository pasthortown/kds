<?php

session_start();

include_once"library/models/request/AnnulmentRequestModel.php";
include_once"library/common/Constants.php";
include_once"library/api/Transaction.php";
include_once"library/Configuration.php";

include"../system/conexion/clase_sql.php";
include"../clases/clase_payphone.php";

$configPayPhone = ConfigurationManager::Instance();

$establecimiento = $_SESSION['rstId'];

$transaction = new Transaction();

$payphone = new PayPhone();

//Configuración Claves
$maxpoint = $payphone->configuracionPayPhone($establecimiento);
if($maxpoint->Integracion='PAYPHONE'){
	$configPayPhone->ApiPath = $maxpoint->ApiPath;
	$configPayPhone->Token = $maxpoint->Token;
	$configPayPhone->ApplicationId = $maxpoint->ApplicationId;
	$configPayPhone->ApplicationPublicKey = $maxpoint->PublicKey;
	$configPayPhone->PrivateKey = $maxpoint->PrivateKey;
	$configPayPhone->ClientId = $maxpoint->ClientId;
	$configPayPhone->ClientSecret = $maxpoint->ClientSecret;
}

//Anular Transaccion y Esperar Respuesta
if(isset($_POST["solicitarAnularTransaccion"])){

	$AnnulmentId = 0;

	$datos[0]=$_SESSION['cadenaId'];
	$datos[1]=$_SESSION['rstId'];
	$datos[2]=$_POST['cfac_id'];
	$factura = $payphone->fn_cargarInformacionTransaccion($datos);

	$data = new AnnulmentRequestModel();
	$data->Latitude = $factura->Latitude;
	$data->Longitude = $factura->Longitude;
	$data->TimeZone = $factura->TimeZone;
	$data->TransactionId = $factura->transactionId;

	try {
		//Solicitud Reverso
		$result = $transaction->SendAnnulment($data);
		$AnnulmentId = $result->AnnulmentId;

		//Consultar Estado Anulacion
		sleep(10);
		for ($seg = 0; $seg<=5; $seg++){
			$result = $transaction->GetAnnulmentStatus($AnnulmentId);
			if($result->Success==Constants::STATUS_APPROVED){
				//Anular FormaPago
				//Confirmar Anulacion y continuar con los procesos de generar tipo de facturación
				$result->Confirmar = 0;
			}else if($result->Status==Constants::STATUS_CANCELED){
				//Timeout PayPhone
				//Transaccion Cancelada, cancelar proceso anulación
				$result->Confirmar = 1;
				$seg = 10;
				//$seg = 10;
			}else{
				sleep(5);
			}
		}
	} catch (PayPhoneWebException $e) {
		header('HTTP/1.1 '.$e->StatusCode.' error');
    	foreach ($e->ErrorList as $valor) {
			$message = $valor;
		}
		$result = $message;
	}
	print json_encode($result);
}

//Esperar Respuesta Anulacion
if(isset($_POST["verificarEstadoReverso"])){

	$ReimbursementId = $_POST['ReimbursementId'];

	try {
		//Consultar Estado Anulacion
		sleep(5);
		for ($seg = 0; $seg<=4; $seg++){
			$result = $transaction->GetReimbursementStatus($ReimbursementId);
			if($result->Status==Constants::STATUS_APPROVED){
				//Anular FormaPago
				//Confirmar Anulacion y continuar con los procesos de generar tipo de facturación
				$result->Confirmar = 1;
				$seg = 10;
			}else if($result->Status==Constants::STATUS_CANCELED){
				//Timeout PayPhone
				//Transaccion Cancelada, cancelar proceso anulación
				$result->Confirmar = 0;
				$seg = 10;
			}else{
				sleep(5);
			}
		}
	} catch (PayPhoneWebException $e) {
		header('HTTP/1.1 '.$e->StatusCode.' error');
    	foreach ($e->ErrorList as $valor) {
			$message = $valor;
		}
		$result = $message;
	}
	print json_encode($result);
}


/*
//Crear Transaccion PayPhone
if(isset($_POST["crearTransaccion"])){
	$documento = $_POST["typeCharge"];
	$ClientTransactionId = $_POST["factura"];
	$Region = $_POST["regionCode"];
	$Parametro = $_POST["parametro"];

	$factura = $payphone->configuracionTransaccion($ClientTransactionId);

	$data = new TransactionRequestModel();
	$data->Amount = $factura->Amount;
	$data->AmountWithTax = $factura->AmountWithTax;
	$data->AmountWithOutTax = $factura->AmountWithOutTax;
	$data->Tax = $factura->Tax;
	$data->TimeZone = $factura->TimeZone;
	$data->Latitud = $factura->Latitud;
	$data->Longitud = $factura->Longitud;
	$data->Token = $configPayPhone->Token;
	$data->ClientTransactionId = $ClientTransactionId;

	try {
		if($documento==0){
	    	$result = $transaction->sendTransaction($data, $Parametro, $Region);
		}else{
			$result = $transaction->sendTransaction($data, $Parametro, 'false');
		}
		$result->Total = $factura->Total;
	} catch (PayPhoneWebException $e) {
		header('HTTP/1.1 '.$e->StatusCode.' error');
    	foreach ($e->ErrorList as $valor) {
			$message = $valor;
		}
		$result = $message;
	}
	print json_encode($result);
}

//Confirmar Transaccion y Consultar Estado Transaccion
if(isset($_POST["consultarEstadoTransaccion"])){
	$transactionId = $_POST['TransactionId'];
	try {
    	$result = $transaction->setTransaction($transactionId);
		sleep(5);
		for ($seg = 0; $seg<=2; $seg++){
			try {
				$result = $transaction->getStatus($transactionId);
				if($result->Status==Constants::STATUS_APPROVED){
					$seg = 10;
					$confirm = $result->Message;
					$datos[0]=1;
					$datos[1]=$_SESSION['usuarioId'];
					$datos[2]=$_SESSION['rstId'];
					$datos[3]=$_SESSION['cadenaId'];
					$datos[4]=$confirm->ClientTransactionId;
					$datos[5]=$confirm->Bin;
					$datos[6]=$confirm->AuthorizationCode;
					$datos[7]=$confirm->TransactionId;
					$datos[8]=$confirm->TransactionStatus;
					$maxpoint = $payphone->agregarFormaPagoFactura($datos);
				}else if($result->Status==Constants::STATUS_CANCELED){
					//Timeout PayPhone
					$seg = 10;
				}else{
					sleep(5);
				}
			} catch (PayPhoneWebException $e) {
				header('HTTP/1.1 '.$e->StatusCode.' error');
				foreach ($e->ErrorList as $valor) {
					$message = $valor;
				}
				$result = $message;
			}
		}
   	} catch (PayPhoneWebException $e) {
   		header('HTTP/1.1 '.$e->StatusCode.' error');
    	foreach ($e->ErrorList as $valor) {
			$message = $valor;
		}
		$result = $message;
	}
	print json_encode($result);
}

//Consultar Estado Transaccion
if(isset($_POST["verificarTransaccion"])){
	$transactionId = $_POST['TransactionId'];
	for ($seg = 0; $seg<=4; $seg++){
		try {
			$result = $transaction->getStatus($transactionId);
			if($result->Status==Constants::STATUS_APPROVED){
				$seg = 10;
				$confirm = $result->Message;
				$datos[0]=1;
				$datos[1]=$_SESSION['usuarioId'];
				$datos[2]=$_SESSION['rstId'];
				$datos[3]=$_SESSION['cadenaId'];
				$datos[4]=$confirm->ClientTransactionId;
				$datos[5]=$confirm->Bin;
				$datos[6]=$confirm->AuthorizationCode;
				$datos[7]=$confirm->TransactionId;
				$datos[8]=$confirm->TransactionStatus;
				$maxpoint = $payphone->agregarFormaPagoFactura($datos);
			}else if($result->Status==Constants::STATUS_CANCELED){
				//Timeout PayPhone
				$seg = 10;
			}else{
				sleep(4);
			}
		} catch (PayPhoneWebException $e) {
   			header('HTTP/1.1 '.$e->StatusCode.' error');
    		foreach ($e->ErrorList as $valor) {
				$message = $valor;
			}
			$result = $message;
		}
	}
	print json_encode($result);
}

//Cancelar Transaccion Pendiente
if(isset($_POST["cancelarTransaccion"])){
	$transactionId = $_POST['TransactionId'];
	try {
		$result = $transaction->Cancel($transactionId);
	} catch (PayPhoneWebException $e) {
		header('HTTP/1.1 '.$e->StatusCode.' error');
		foreach ($e->ErrorList as $valor) {
			$message = $valor;
		}
		$result = $message;
	}
	print json_encode($result);
}

if(isset($_POST["cargarDatosFactura"])){
	$ClientTransactionId = $_POST["factura"];
	$factura = $payphone->configuracionTransaccion($ClientTransactionId);
	print json_encode($factura);
}
*/

?>