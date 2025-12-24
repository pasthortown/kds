<?php
/**
 * Created by PhpStorm.
 * User: nathaly.sanchez
 * Date: 25/9/2020
 * Time: 12:31
 */
session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminCambioEstadosBring.php';

$obj = new clase_adminCambioEstadosBring();
$request = (object)$_POST;


if (empty($_SESSION['rstId']) || empty($_SESSION['usuarioId']) || empty($_SESSION['cadenaId'])) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$idRestaurante     = $_SESSION['rstId'];
$idCadena          = $_SESSION['cadenaId'];
$idUsuario         = $_SESSION['usuarioId'];



if($request->metodo === 'cargaPeriodoAbierto'){

    print json_encode($obj->cargarPeriodoAbierto($idCadena,$idRestaurante));

}elseif ($request->metodo === 'cargaTablaCambioEstadosBringg'){

    print $obj->cargaTablaCambioEstadosBringg();

}elseif ($request->metodo === 'cargaTablaMotivosCambioEstadosBringg'){

    print $obj->cargaTablaMotivosCambioEstadosBringg();
}elseif ($request->metodo === 'cargaMotorolos'){

    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] = $idRestaurante;
    $lc_condiciones[2] = $request->idPeriodo;
    $lc_condiciones[3] = $request->medio;

    print $obj->cargaMotorolos($lc_condiciones);
}elseif ($request->metodo === 'fn_asignarMotorizado'){
    $lc_condiciones[0] = $request->idMotorolo;
    $lc_condiciones[1] = $request->codigoApp;
    $lc_condiciones[2] = $idUsuario;
    $lc_condiciones[3] = $request->banderaMotorolo;
    $lc_condiciones[4] = $request->estado;

    if($lc_condiciones[3] == 1){
        print $obj->fn_asignarMotorizadoPedido($lc_condiciones);
    }else{
        print $obj->fn_asignarMotorizado($lc_condiciones);
    }


}elseif ($request->metodo === 'guardaAuditoriaCambioEstado'){
    $lc_condiciones[0] = $request->codigoApp;
    $lc_condiciones[1] = $request->estado;
    $lc_condiciones[2] = $request->idMotivo;
    $lc_condiciones[3] = $idUsuario;
    $lc_condiciones[4] = $idRestaurante;
    $lc_condiciones[5] = $request->accion;

    print $obj->guardaAuditoriaCambioEstado($lc_condiciones);
}elseif ($request->metodo === 'cambioEstadoEnCamino'){
    $lc_condiciones[0] = $request->idPeriodo;
    $lc_condiciones[1] = $request->idMotorolo;
    $lc_condiciones[2] = $idUsuario;
    $lc_condiciones[3] = $request->codigoApp;

    print $obj->cambioEstadoEnCamino($lc_condiciones);
}elseif ($request->metodo === 'cambioEstadoEntregado'){
    $lc_condiciones[0] = $request->idPeriodo;
    $lc_condiciones[1] = $request->idMotorolo;
    $lc_condiciones[2] = $idUsuario;
    $lc_condiciones[3] = $request->codigoApp;

    print $obj->cambioEstadoEntregado($lc_condiciones);
}elseif ($request->metodo === 'cambioTipoAsignacion'){
    $lc_condiciones[0] = $request->codigoApp;
    $lc_condiciones[1] = $request->tipoAsignacion;

    print $obj->cambioTipoAsignacion($lc_condiciones);
}