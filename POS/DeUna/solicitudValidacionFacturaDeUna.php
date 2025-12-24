<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";
if (isset($_POST["solicitudValidacionDeUna"]) && isset($_POST["plu_id"]) && isset($_POST["rst_id"])
&& isset($_POST["cdn_id"]) && isset($_POST["odp_id"]) && isset($_POST["est_id"]) 
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $cdn_id= $_POST["cdn_id"];
    $rst_id = $_POST["rst_id"];
    $odp_id = $_POST["odp_id"];
    $plu_id = $_POST["plu_id"];
    $est_id = $_POST["est_id"];

    $productosSubsiados = json_decode($datosDeUna->verificarProductosMaximosPorFactura($cdn_id),true);
    //var_dump($productosSubsiados);
    if (count($productosSubsiados) == 0) {
        echo json_encode(["puedeAvanzar" => true]);
        return;
    }
    //echo json_encode($productosSubsiados);
    $productosEnODP = [];
    if(isset($odp_id) && $odp_id != null && $odp_id != ""){
        $productosEnODP = json_decode($datosDeUna->obtenerProductosDe_ODP_By_ODPId($odp_id),true);
    }
    //echo json_encode($productosEnODP);
    if (count($productosEnODP) == 0 ) {
        if (count($productosSubsiados) > 0)  {
            foreach ($productosSubsiados as $productoSubsidiado) {
                if ($productoSubsidiado["plu_id"] == $plu_id) {
                    if ($productoSubsidiado["variableI"] == 0) {
                        echo json_encode(["puedeAvanzar" => false]);
                        return;
                    }
                    //separarSubsidio
                    echo json_encode(["puedeAvanzar" => true]);
                    return;
                }
            }
        }
    }

    if (count($productosSubsiados) > 0) {
        foreach ($productosSubsiados as $productoSubsidiado) {
            if ($productoSubsidiado["plu_id"] == $plu_id) {
                if ($productoSubsidiado["variableI"] <= 0) {
                    echo json_encode(["puedeAvanzar" => false]);
                    return;
                }
            }
        }
    }

    //echo count($productosSubsiados);
    //echo count($productosEnODP);
    if (count($productosEnODP) > 0) {
        foreach ($productosEnODP as $productoOdp) {
            //echo json_encode($productoOdp);
            if (count($productosSubsiados) > 0) {
                foreach ($productosSubsiados as $productoSubsidiado) {
                    if ($productoOdp["plu_id"] == $productoSubsidiado["plu_id"]) {
                        if ($productoOdp["plu_id"] == $plu_id) {
                            if (intval($productoOdp["dop_cantidad"]) >= intval($productoSubsidiado["variableI"])) {
                                echo json_encode(["puedeAvanzar" => false]);
                                return;
                            }
                        } else {
                            if (intval($productoOdp["dop_cantidad"]) > intval($productoSubsidiado["variableI"])) {
                                //
                                echo json_encode(["puedeAvanzar" => false]);
                                return;
                            }
                        }
                        
                        
                    }   
                }
            }
        }
    }
        $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id,$est_id),true);
        //var_dump($parametrosDeUna);
        $cantidadoMaximaDeProductosSubsidiados = 3;
        if (count($parametrosDeUna) > 0) {
            foreach ($parametrosDeUna as $key => $value) {
                if (!isset($value) && $value["Descripcion"] == "INTEGRACION DE UNA") {
                    if ($value["parametro"] == "NUMERO MAXIMO DE PRODUCTOS POR FACTURA") {
                        $cantidadoMaximaDeProductosSubsidiados= intval($value["variableI"]);
                    }
                }
            }   
        }
        //var_dump($productosSubsiados);
        $cantidadSubsidiados = 0;
        if (count($productosEnODP) > 0) {
            foreach($productosEnODP as $productoOdp) {
                if (floatval($productoOdp["subsidio"]) > 0) {
                    $cantidadSubsidiados = floatval($cantidadSubsidiados) + floatval($productoOdp["dop_cantidad"]);
                } 
            }
        }
        if (count($productosSubsiados) > 0 ) {
            foreach($productosSubsiados as $productoSubsidiado) {
                if ($plu_id == $productoSubsidiado["plu_id"]) {
                    if ($cantidadSubsidiados >= $cantidadoMaximaDeProductosSubsidiados) {
                    echo json_encode(["puedeAvanzar" => false]);
                    return;
                    } 
                }
            }
        }
    
    // separar Subsidio
    echo json_encode(["puedeAvanzar" => true]);
    return;
}
?>