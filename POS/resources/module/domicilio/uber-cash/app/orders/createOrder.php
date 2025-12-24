<?php
require_once "../../../../exceptions/GeneralException.php";
require_once "../../../../clases/clase_uberDirect_cash.php";
require_once "../../../../resources/models/webservices/CallREST.php";
require_once "../../../../resources/models/webservices/Request.php";
require_once "../../../../system/conexion/clase_sql.php";


function validarUberDirectCash($idCadena, $idRestaurante, $codigo_app, $medio) {
    $uberDirectCash = new uberDirectCash();
    date_default_timezone_set("America/Guayaquil");

    return $uberDirectCash::validarUberDirectCash($idCadena, $idRestaurante, $codigo_app, $medio);
}

function validarUberDirectCashMedio($idCadena, $idRestaurante, $codigo_app, $medio) {
    $uberDirectCash = new uberDirectCash();
    date_default_timezone_set("America/Guayaquil");

    return $uberDirectCash::validarUberDirectCashMedio($idCadena, $idRestaurante, $codigo_app, $medio);
}
