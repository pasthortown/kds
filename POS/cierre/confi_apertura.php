<?php

session_start();
/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jose Fernandez//////////////////////////////////////
///////DESCRIPCION: Archivo de configuracion/////////////////////////////////
////////////////////de la pantalla Apertura//////////////////////////////////
///////TABLAS INVOLUCRADAS:Periodo,Estacion//////////////////////////////////
////////FECHA CREACION: 20/12/2013///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include_once"../system/conexion/clase_sql.php";
include_once "../clases/clase_apertura.php";
include_once "../clases/clase_webservice.php";
//require '../serviciosweb/interface/config.ini';
$lc_apertura = new apertura();
$servicioWebObj=new webservice();
//$lc_rest = $_SESSION['rstId']; 
//$lc_ip =$_SESSION['direccionIp'];
//$lc_usuarioId=$_SESSION['usuarioId']; 

if (htmlspecialchars(isset($_GET["grabaperiodo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['rst_id']);
    $lc_condiciones[2] = preg_replace('[\s+]', '', htmlspecialchars($_GET['usr_usuario']));
    $lc_condiciones[3] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[4] = htmlspecialchars($_GET['opcion_apertura']);
    print $lc_apertura->fn_ejecutar("grabaperiodo", $lc_condiciones);
}

/* if(htmlspecialchars(isset($_GET["validaip"])))
  {
  $lc_condiciones[0]=$_GET['rst_id'];
  $lc_condiciones[1]=$_GET['est_ip'];
  print $lc_apertura->fn_consultar("validaip",$lc_condiciones);
  } */
if (htmlspecialchars(isset($_GET["validaperiodoAbierto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['rst_id']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    print $lc_apertura->fn_consultar("validaperiodoAbierto", $lc_condiciones);
}
if (htmlspecialchars(isset($_GET["traerLogoCadena"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    print $lc_apertura->fn_consultar("traerLogoCadena", $lc_condiciones);
}


if (htmlspecialchars(isset($_GET["validaAccesoPerfil"]))) {



    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = preg_replace('[\s+]', '', htmlspecialchars($_GET['usr_usuario'])); //$_GET['usr_usuario'];
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);

    $tieneTransferencia = 0;
    if (htmlspecialchars(isset($_GET["transf"]))) {
        $tieneTransferencia = htmlspecialchars($_GET["transf"]);
    }

    $estado = true;
    $origenDestino = "";
    if ($tieneTransferencia == 1) {
        $lc_rest=$_GET["rst_id"];
        $configuraciones = parse_ini_file('../serviciosweb/interface/config.ini', true);

        $lc_condiciones[3] = $tieneTransferencia;
        $lc_condiciones[4] = htmlspecialchars($_GET['cadena_des']);
        $lc_condiciones[5] = htmlspecialchars($_GET['rst_des']);
        $lc_condiciones[6] = htmlspecialchars($_GET['bd_dest']);
        $datosWebservice=$servicioWebObj->retorna_WS_Trans_Venta_ActualizacionPrecios($lc_rest);
        $urlWSActualizacionPrecios=$datosWebservice["urlwebservice"];
    
        try {
            
            $url = $urlWSActualizacionPrecios. "?bdd=" . $lc_condiciones[6];
            //$url = $configuraciones['TransferenciaVenta']["urlWSActualizacionPrecios"] . "?bdd=" . $lc_condiciones[6];
       
 
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $result = curl_exec($ch);
            // Saber el estado de la ejecucion del Ws  con 400(valida problema en parametro el curl valida problema de url)
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            
            if (curl_errno($ch) || $http_status === 400 || $http_status === 404 || $http_status === 0) {
                 
                $estado = false;
                $origenDestino= ($http_status===0)?"El WebService no responde.": (($http_status===400)?"Errores dentro de los parametros del WebService [Destino]":(($http_status===404)?"WebService no encontrado [Destino]":"Error de Ws en Destino."));
               // $origenDestino = " Destino";
            } else {
               
                $respuesta = json_decode($result, true);

                if (empty($respuesta)) {
                    $estado = false;
                    $origenDestino = " Ning�n registro actualizado (El Ws en el destino no devolvi�  valores)";
                }
            }
            
        } catch (Exception $e) {
            $mensaje["mensaje"] = "Servicio no Disponible";
            $mensaje["estado"] = 0;
        } finally {
            
        }

        if ($estado) { //$estado
                $datosWebservice=$servicioWebObj->retorna_WS_Trans_Venta_RetornaPrecios($lc_rest);
                $urlWSRetornaPrecios=$datosWebservice["urlwebservice"];
            try {
                $url_getPrecio = $urlWSRetornaPrecios. "?cdn_id=" . $lc_condiciones[4] . "&rst_id=" . $lc_condiciones[5] . "&bdd=" . $lc_condiciones[6];
                //$url_getPrecio = $configuraciones['TransferenciaVenta']["urlWSRetornaPrecios"] . "?cdn_id=" . $lc_condiciones[4] . "&rst_id=" . $lc_condiciones[5] . "&bdd=" . $lc_condiciones[6];
                $ch1 = curl_init($url_getPrecio);
                curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                curl_setopt($ch1, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 5);
                
                
                $result_price = curl_exec($ch1);
                $http_status1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                
                if (curl_errno($ch1)  || $http_status1 === 400|| $http_status1 === 404|| $http_status1 === 0) {

                    $estado = false;
                     $origenDestino= ($http_status1===0)?"El WebService no responde.": (($http_status1===400)?"Errores dentro de los parametros del WebService [Origen]":(($http_status1===404)?"WebService no encontrado [Origen]":"Error de Ws en Origen."));
                   // $origenDestino = " Origen";
                } else {

            
                    curl_close($ch1);
                    $respuesta1 = json_decode($result_price, true);
                    $cadena = "";

                    if (empty($respuesta1)) {
                         $origenDestino = " Ning�n registro actualizado (El Ws en el origen no devolvi�  valores)";
                        $estado = false;
                    } else {
                        $products = json_decode($result_price, true);
                        $con = 0;

                        foreach ($products as $product) {
                            if (is_null($product['plu_id'])) {
                                $cadena = "";
                                break;
                            }
                            $cadena .= "(" . ($product['plu_id'] . "," . $product['pr_valor_neto'] . "," . $product['pr_valor_iva'] . "," . $product['pr_pvp'] . ")," );
                            $con ++;
                            if ($con == 60) {
                                $cadena = substr($cadena, 0, -1);
                                $lc_condicionesUPD [0] = $cadena;
                                $lc_condicionesUPD [1] = $lc_condiciones[2];
                                $lc_apertura->fn_consultar("ActualizarPrecio", $lc_condicionesUPD);
                                $con = 0;
                                $cadena = "";
                            }
                        }

                        if ($cadena != "") {
                            $cadena = substr($cadena, 0, -1);
                            $lc_condicionesUPD [0] = $cadena;
                            $lc_condicionesUPD [1] = $lc_condiciones[2];
                            $lc_apertura->fn_consultar("ActualizarPrecio", $lc_condicionesUPD);
                        }
                    } // Fin Valida estado.
                }
            } catch (Exception $e) {
                $mensaje["mensaje"] = "Servicio no Disponible";
                $mensaje["estado"] = 0;
            }
        }


        if ($estado) {
            print $lc_apertura->fn_consultar("validaAccesoPerfil", $lc_condiciones);
        } else {
            print ('{"accesoperfil":0,"tipo_problema":5,"str":"' . $origenDestino . ' "}');
        }
    } else {
        print $lc_apertura->fn_consultar("validaAccesoPerfil", $lc_condiciones);
    }
}
