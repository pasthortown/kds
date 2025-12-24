<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";
if (isset($_POST["solicitudValidacionDeUna"]) && isset($_POST["plu_id"]) && isset($_POST["rst_id"])
&& isset($_POST["cdn_id"]) && isset($_POST["odp_id"]) && isset($_POST["est_id"]) && isset($_POST["cli_id"]) 
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $cdn_id= $_POST["cdn_id"];
    $rst_id = $_POST["rst_id"];
    $odp_id = $_POST["odp_id"];
    $plu_id = $_POST["plu_id"];
    //var_dump("plu_id: ".$plu_id);
    $est_id = $_POST["est_id"];
    $cli_id = $_POST["cli_id"];
    $etiqueta_cantidad = $_POST["etiqueta_cantidad"];
    $etiqueta_cantidad = str_replace('x', '', $etiqueta_cantidad);
    $etiqueta_cantidad = intval($etiqueta_cantidad);
    //var_dump("etiqueta_cantidad: ".$etiqueta_cantidad);
    $cantidadTotal = 0;
    $productosSubsiados = json_decode($datosDeUna->verificarProductosMaximosPorCliente($cdn_id),true);
    //var_dump("productos subsidiados");
    //var_dump($productosSubsiados);
    if (count($productosSubsiados) == 0 ) {
        echo json_encode(["puedeAvanzar" => true]);
        return;
    }
    $productosCompradosSubsidiados = json_decode($datosDeUna->obtenerProductosSubsidiadosPorClienteDeUna($cdn_id, $cli_id, $plu_id), true);
    //var_dump("productosCompradosSubsidiados");
    //var_dump($productosCompradosSubsidiados);
    $productosEnODP = json_decode($datosDeUna->obtenerProductosDe_ODP_By_ODPId($odp_id),true);
    //var_dump("productosEnODP");
    //var_dump($productosEnODP);


    foreach ($productosSubsiados as $ps) {
        if ($plu_id == $ps["plu_id"]) {
            if ($etiqueta_cantidad > $ps["variableI"]) {
                echo json_encode(["puedeAvanzar" => false]);
                return;
            }
        }
    }

    foreach($productosSubsiados as $ps) {
        if (count($productosCompradosSubsidiados) > 0) {
            foreach($productosCompradosSubsidiados as $comprado) {
                if (count($productosEnODP) > 0) {
                    foreach($productosEnODP as $pODP) {
                        if ($ps["plu_id"] == $comprado["plu_id"] && $ps["plu_id"] == $pODP["plu_id"]) {
                            if ($ps["plu_id"] == $plu_id) {
                                $cantidadTotal = floatval($comprado["cantidad"]) + floatval($pODP["dop_cantidad"]);
                                if ($cantidadTotal >= floatval($ps["variableI"])) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                            if ($plu_id == 0) {
                                $cantidadTotal = floatval($comprado["cantidad"]) + floatval($pODP["dop_cantidad"]);
                                if ($cantidadTotal > floatval($ps["variableI"])) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                        }
                        if ($ps["plu_id"] == $comprado["plu_id"]) {
                            if ($ps["plu_id"] == $plu_id) {
                                $cantidadTotal = floatval($comprado["cantidad"]);
                                if ($cantidadTotal >= floatval($ps["variableI"])) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                            // if ($plu_id == 0) {
                            //     //var_dump($productosCompradosSubsidiados);
                            //     $cantidadTotal = floatval($comprado["cantidad"]);
                            //     //var_dump($cantidadTotal);
                            //     if ($cantidadTotal > floatval($ps["variableI"])) {
                            //         //echo "aqui2";
                            //         echo json_encode(["puedeAvanzar" => false]);
                            //         return;
                            //     }
                            // }
                        }
                    }   
                }
            }
        }   
    }

    if (count($productosEnODP) == 0) {
        if (count($productosSubsiados) > 0) {
            foreach($productosSubsiados as $ps) {
                if (count($productosCompradosSubsidiados) > 0) {
                    foreach($productosCompradosSubsidiados as $comprado) {
                        if ($ps["plu_id"] == $comprado["plu_id"]) {
                            if ($ps["plu_id"] == $plu_id) {
                                $cantidadTotal = floatval($comprado["cantidad"]);
                                if ($cantidadTotal >= $ps["variableI"]) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                        } 
                    }
                }
            }
        }
    }
    

    if (count($productosCompradosSubsidiados) == 0 ) {
        if (count($productosSubsiados) > 0) {
            foreach($productosSubsiados as $ps) {
                if (count($productosEnODP)> 0) {
                    foreach($productosEnODP as $pODP) {
                        if ($ps["plu_id"] == $pODP["plu_id"]) {
                            if ($ps["plu_id"] == $plu_id) {
                                $cantidadTotal = floatval($pODP["dop_cantidad"]);
                                if ($cantidadTotal >= $ps["variableI"]) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
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
    $cantidadMaximaDeProductosPorCliente = 3;
    if (count($parametrosDeUna) > 0) {
        foreach ($parametrosDeUna as $key => $value) {
            if ($value["Descripcion"] == "INTEGRACION DE UNA") {
                if ($value["parametro"] == "NUMERO MAXIMO DE PRODUCTOS POR FACTURA") {
                    $cantidadoMaximaDeProductosSubsidiados = intval($value["variableI"]);
                }
                if ($value["parametro"] == "NUMERO MAXIMO DE PRODUCTOS POR CLIENTE") {
                    $cantidadMaximaDeProductosPorCliente = intval($value["variableI"]);
                }
            }
        }
    }

    //var_dump("cantidadMaximaDeProductosPorCliente");
    //var_dump($cantidadMaximaDeProductosPorCliente);
    //var_dump("cantidadoMaximaDeProductosSubsidiados");
    //var_dump($cantidadoMaximaDeProductosSubsidiados);

    $cantidadSubsidiados = 0;
    if (count($productosEnODP) > 0) {
        foreach($productosEnODP as $productoOdp) {
            if (floatval($productoOdp["subsidio"]) > 0) {
                $cantidadSubsidiados = floatval($cantidadSubsidiados) + floatval($productoOdp["dop_cantidad"]);
            } 
        }
    }
    if (count($productosSubsiados) > 0) {
        foreach($productosSubsiados as $productoSubsidiado) {
            if ($plu_id == $productoSubsidiado["plu_id"]) {
                if ($cantidadSubsidiados >= $cantidadoMaximaDeProductosSubsidiados) {
                    echo json_encode(["puedeAvanzar" => false]);
                    return;
                } 
            }
        }
    }

    $totalPlusCompradosConSub = 0.0;
    if (count($productosCompradosSubsidiados) > 0) {
        foreach($productosCompradosSubsidiados as $comprado) {
            $totalPlusCompradosConSub =  $totalPlusCompradosConSub + floatval($comprado["cantidad"]);
        }
    }
    if (count($productosSubsiados) > 0) {
        foreach($productosSubsiados as $ps) {
            if ($ps["plu_id"] == $plu_id) {
                if ($totalPlusCompradosConSub >= $cantidadMaximaDeProductosPorCliente) {
                    echo json_encode(["puedeAvanzar" => false]);
                    return;
                }
            }
        }
    }
    if (count($productosCompradosSubsidiados) == 0 && count($productosEnODP) == 0) {
        //var_dump($productosSubsiados);
        if ($etiqueta_cantidad > 1) {
            if (count($productosSubsiados) > 0) {
                foreach($productosSubsiados as $ps) {
                    if ($ps["plu_id"] == $plu_id) {
                        if ($etiqueta_cantidad > $ps["variableI"]) {
                            echo json_encode(["puedeAvanzar" => false]);
                            return;
                        }
                    }
                }
            }
        }
    }

    if (count($productosEnODP) == 0 &&  (count($productosCompradosSubsidiados) > 0)) {
        if ($etiqueta_cantidad > 1) {
            if (count($productosSubsiados) > 0) {
                foreach($productosSubsiados as $ps) {
                    if ($ps["plu_id"] == $plu_id) {
                        foreach($productosCompradosSubsidiados as $comprado) {
                            if ($comprado["plu_id"] == $plu_id) {
                               $cantidadTotal = $etiqueta_cantidad + floatval($comprado["cantidad"]);
                                if($cantidadTotal > $ps["variableI"]) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if (count($productosEnODP) > 0 &&  (count($productosCompradosSubsidiados) > 0)) {
        if ($etiqueta_cantidad > 1) {
            if (count($productosSubsiados) > 0) {
                foreach ($productosSubsiados as $ps) {
                    if ($ps["plu_id"] == $plu_id) {
                        foreach ($productosCompradosSubsidiados as $comprado) {
                            if ($comprado["plu_id"] == $plu_id) {
                                foreach ($productosEnODP as $pODP) {
                                    if ($pODP["plu_id"] == $plu_id) {
                                        $cantidadTotal = $etiqueta_cantidad + floatval($comprado["cantidad"]) + floatval($pODP["dop_cantidad"]);
                                        if ($cantidadTotal > $ps["variableI"]) {
                                            echo json_encode(["puedeAvanzar" => false]);
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if (count($productosEnODP) > 0 &&  (count($productosCompradosSubsidiados) == 0)) {
        if ($etiqueta_cantidad > 1) {
            if (count($productosSubsiados) > 0) {
                foreach ($productosSubsiados as $ps) {
                    if ($ps["plu_id"] == $plu_id) {
                        foreach ($productosEnODP as $pODP) {
                            if ($pODP["plu_id"] == $plu_id) {
                                $cantidadTotal = $etiqueta_cantidad + floatval($pODP["dop_cantidad"]);
                                if ($cantidadTotal > $ps["variableI"]) {
                                    echo json_encode(["puedeAvanzar" => false]);
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
echo json_encode(["puedeAvanzar" => true]);
return;

}
echo json_encode(["puedeAvanzar" => true]);
return;