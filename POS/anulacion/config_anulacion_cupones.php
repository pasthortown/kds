<?php
/**
 * User: Francisco Sierra
 * Date: 10/19/2018
 * Time: 1:21 PM
 */
require_once '../parametros.php';
use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;
if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

if(!isset($_REQUEST["accion"])){
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "No se envió ninguna acción."
    ]));
}

if($_REQUEST["accion"]=="anularCanjesFactura"){

    $resultadoSincronizacion = false;
    $conexionTienda= $conexionDinamica->conexionTienda();
    $promocionesController = new PromocionesController($conexionTienda);

    $parametros=[
        "cfac_id"=>$_REQUEST["cfac_id"],
        "ncre_id"=>$_REQUEST["ncre_id"],
        "rst_id"=>$_SESSION['rstId'],
        "usr_id"=>$_SESSION['usuarioId'],
    ];
    $resultadoAnulacion = $promocionesController->anularCanjes($parametros);

    $nuevosParametros=[
        "cfac_id"=>$_REQUEST["cfac_id"],
        "ncre_id"=>$_REQUEST["ncre_id"],
        "rst_id"=>$_SESSION['rstId'],
        "usr_id"=>$_SESSION['usuarioId'],
        "IDCanjeMasterData"=>$resultadoAnulacion['datos'][0]['IDCanjeMasterData'],
    ];

    if($resultadoAnulacion["estado"]!==1) return;
    if(count($resultadoAnulacion["datos"]) > 0){
        $resultadoSincronizacion = $promocionesController->sincronizarAnulacionCanjesFactura($nuevosParametros);
    }

    print(json_encode($resultadoSincronizacion));
    die();
}





