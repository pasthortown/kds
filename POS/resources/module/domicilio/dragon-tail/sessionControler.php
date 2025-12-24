<?php
@session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['cadenaId'], $_SESSION['rstId'], $_SESSION['IDPeriodo'])) {
        print json_encode(array("idCadena"      => $_SESSION['cadenaId'],
                                "idRestaurante" => $_SESSION['rstId'],
                                "idPeriodo"     => $_SESSION['IDPeriodo']));
    } elseif (isset($_SESSION['cadenaId'], $_SESSION['rstId'])) {
        print json_encode(array("idCadena"      => $_SESSION['cadenaId'],
                                "idRestaurante" => $_SESSION['rstId']));
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(array("error"=>"must be logged"));//html for 500 page
    }
}
?>
